<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  if(isset($_POST['add_feature']))
  {
    if(!verify_csrf_token($_POST['csrf_token'])){
      echo 'csrf_failed';
      exit;
    }
    $frm_data = filteration($_POST);

    $q = "INSERT INTO `features`(`name`) VALUES (?)";
    $values = [$frm_data['name']];
    $res = insert($q,$values,'s');
    echo $res;
  }

  if(isset($_POST['get_features']))
  {
    $res = selectAll('features');
    $i=1;

    while($row = mysqli_fetch_assoc($res))
    {
      $safe_name = htmlspecialchars($row['name']);
      $safe_id = htmlspecialchars($row['id']);
      echo <<<data
        <tr>
          <td>$i</td>
          <td>$safe_name</td>
          <td>
            <button type="button" onclick="rem_feature($safe_id)" class="btn btn-danger btn-sm shadow-none">
              <i class="bi bi-trash"></i> Xoá
            </button>
          </td>
        </tr>
      data;
      $i++;
    }
  }

  if(isset($_POST['rem_feature']))
  {
    if(!verify_csrf_token($_POST['csrf_token'])){
      echo 'csrf_failed';
      exit;
    }
    $frm_data = filteration($_POST);
    $values = [$frm_data['rem_feature']];

    $check_q = select('SELECT * FROM `room_features` WHERE `features_id`=?',[$frm_data['rem_feature']],'i');

    if(mysqli_num_rows($check_q)==0){
      $q = "DELETE FROM `features` WHERE `id`=?";
      $res = delete($q,$values,'i');
      echo $res;
    }
    else{
      echo 'room_added';
    }

  }


  if(isset($_POST['add_facility']))
  {
    if(!verify_csrf_token($_POST['csrf_token'])){
      echo 'csrf_failed';
      exit;
    }
    $frm_data = filteration($_POST);

    $img_r = uploadSVGImage($_FILES['icon'],FACILITIES_FOLDER);

    if($img_r == 'inv_img'){
      echo $img_r;
    }
    else if($img_r == 'inv_size'){
      echo $img_r;
    }
    else if($img_r == 'upd_failed'){
      echo $img_r;
    }
    else{
      $q = "INSERT INTO `facilities`(`icon`,`name`, `description`) VALUES (?,?,?)";
      $values = [$img_r,$frm_data['name'],$frm_data['desc']];
      $res = insert($q,$values,'sss');
      echo $res;
    }
  }

  if(isset($_POST['get_facilities']))
  {
    $res = selectAll('facilities');
    $i=1;
    $path = FACILITIES_IMG_PATH;

    while($row = mysqli_fetch_assoc($res))
    {
      $safe_icon = htmlspecialchars($path.$row['icon']);
      $safe_name = htmlspecialchars($row['name']);
      $safe_desc = htmlspecialchars($row['description']);
      $safe_id = htmlspecialchars($row['id']);
      echo <<<data
        <tr class='align-middle'>
          <td>$i</td>
          <td><img src="$safe_icon" width="100px"></td>
          <td>$safe_name</td>
          <td>$safe_desc</td>
          <td>
            <button type="button" onclick="rem_facility($safe_id)" class="btn btn-danger btn-sm shadow-none">
              <i class="bi bi-trash"></i> Xoá
            </button>
          </td>
        </tr>
      data;
      $i++;
    }
  }

  if(isset($_POST['rem_facility']))
  {
    if(!verify_csrf_token($_POST['csrf_token'])){
      echo 'csrf_failed';
      exit;
    }
    $frm_data = filteration($_POST);
    $values = [$frm_data['rem_facility']];

    $check_q = select('SELECT * FROM `room_facilities` WHERE `facilities_id`=?',[$frm_data['rem_facility']],'i');

    if(mysqli_num_rows($check_q)==0)
    {
      $pre_q = "SELECT * FROM `facilities` WHERE `id`=?";
      $res = select($pre_q,$values,'i');
      $img = mysqli_fetch_assoc($res);
  
      if(deleteImage($img['icon'],FACILITIES_FOLDER)){
        $q = "DELETE FROM `facilities` WHERE `id`=?";
        $res = delete($q,$values,'i');
        echo $res;      
      }
      else{
        echo 0;
      }
    }
    else{
      echo 'room_added';
    }

  }

?>