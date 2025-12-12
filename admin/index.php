<?php
require('inc/essentials.php');
require('inc/db_config.php');

if ((isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)) {
  echo "<script>window.location.href='/admin/dashboard.php'</script>";
  exit;
}

// Handle Login Check BEFORE ensuring a new token for the render
if (isset($_POST['login'])) {
  $frm_data = filteration($_POST);

  // Check if csrf_token exists in POST data
  if (!isset($_POST['csrf_token']) || empty($_POST['csrf_token'])) {
    alert('error', 'CSRF token missing from form submission!');
  } else if (!verify_csrf_token($_POST['csrf_token'])) {
    alert('error', 'CSRF token validation failed! Session token does not match.');
  } else {
    // CSRF OK, proceed to checking credentials
    $query = "SELECT * FROM  `admin_cred` WHERE `admin_name`=?";
    $values = [$frm_data['admin_name']];

    $res = select($query, $values, "s");
    if ($res->num_rows == 1) {
      $row = mysqli_fetch_assoc($res);
      if (password_verify($frm_data['admin_pass'], $row['admin_pass'])) {
        session_regenerate_id(true);
        $_SESSION['adminLogin'] = true;
        $_SESSION['adminId'] = $row['sr_no'];
        echo "<script>window.location.href='/admin/dashboard.php'</script>";
        exit;
      } else {
        alert('error', 'Login failed - Invalid Password!');
      }
    } else {
      alert('error', 'Login failed - Invalid Username!');
    }
  }
}

// Generate CSRF token for the HTML form
$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login Panel</title>
  <?php require('inc/links.php'); ?>
  <style>
    div.login-form {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 400px;
    }
  </style>
</head>

<body class="bg-light">

  <div class="login-form text-center rounded bg-white shadow overflow-hidden">
    <form method="POST">
      <h4 class="bg-dark text-white py-3">Admin Panel</h4>
      <div class="p-4">
        <div class="mb-3">
          <input name="admin_name" required type="text" class="form-control shadow-none text-center"
            placeholder="Admin Username">
        </div>
        <div class="mb-4">
          <input name="admin_pass" required type="password" class="form-control shadow-none text-center"
            placeholder="Password">
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <button name="login" type="submit" class="btn text-white custom-bg shadow-none">Login</button>
      </div>
    </form>
  </div>


  <?php require('inc/scripts.php') ?>
</body>

</html>