<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Confirm Booking</title>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php

  /*
    Check room id from url is present or not
    Shutdown mode is active or not
    User is logged in or not
  */

  if (!isset($_GET['id']) || $settings_r['shutdown'] == true) {
    redirect('rooms.php');
  } else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('rooms.php');
  }

  // filter and get room and user data
  
  $data = filteration($_GET);

  $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

  if (mysqli_num_rows($room_res) == 0) {
    redirect('rooms.php');
  }

  $room_data = mysqli_fetch_assoc($room_res);

  $_SESSION['room'] = [
    "id" => $room_data['id'],
    "name" => $room_data['name'],
    "price" => $room_data['price'],
    "payment" => null,
    "available" => false,
  ];


  $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
  $user_data = mysqli_fetch_assoc($user_res);

  ?>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 mb-4 px-4">
        <h4 class="mt-4 fw-bold h-font">CONFIRM BOOKING</h4>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">Home</a>
          <span class="text-secondary"> > </span>
          <a href="rooms.php" class="text-secondary text-decoration-none">Tour Packages</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">Confirm Booking</a>
        </div>
      </div>

      <?php
      if (isset($_GET['error'])) {
        $error_msg = '';
        switch ($_GET['error']) {
          case 'check_in_out_equal':
            $error_msg = 'You cannot check-out on the same day!';
            break;
          case 'check_out_earlier':
            $error_msg = 'Check-out date is earlier than check-in date!';
            break;
          case 'check_in_earlier':
            $error_msg = "Check-in date is earlier than today's date!";
            break;
          case 'unavailable':
            $error_msg = 'Room not available for this check-in date!';
            break;
          case 'price_mismatch':
            $error_msg = 'The price has been changed. Please check the new price.';
            break;
          default:
            $error_msg = 'An unknown error occurred.';
        }
        echo "<div class='col-12 px-4'><div class='alert alert-danger'>$error_msg</div></div>";
      }
      ?>

      <div class="col-lg-7 col-md-12 px-4">
        <?php

        $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
        $thumb_q = select("SELECT * FROM `room_images` WHERE `room_id`=? AND `thumb`=?", [$room_data['id'], 1], 'is');

        if (mysqli_num_rows($thumb_q) > 0) {
          $thumb_res = mysqli_fetch_assoc($thumb_q);
          $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
        }

        $safe_room_name = htmlspecialchars($room_data['name']);
        $safe_room_price = htmlspecialchars($room_data['price']);

        echo <<<data
            <div class="card p-3 shadow-sm rounded">
              <img src="$room_thumb" class="img-fluid rounded mb-3">
              <h5>$safe_room_name</h5>
              <h6>$safe_room_price VND / person</h6>
            </div>
          data;

        ?>
      </div>

      <div class="col-lg-5 col-md-12 px-4">
        <div class="card mb-4 border-0 shadow-sm rounded-3">
          <div class="card-body">
            <form action="pay_now.php" method="POST" id="booking_form">
              <h6 class="mb-3">Booking Details</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Name</label>
                  <input name="name" type="text" value="<?php echo htmlspecialchars($user_data['name']); ?>"
                    class="form-control shadow-none" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Phone Number</label>
                  <input name="phonenum" type="number" value="<?php echo htmlspecialchars($user_data['phonenum']); ?>"
                    class="form-control shadow-none" required>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Address</label>
                  <textarea name="address" class="form-control shadow-none" rows="1"
                    required><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Check-in</label>
                  <input name="checkin" onchange="check_availability()" type="date" class="form-control shadow-none"
                    required>
                </div>
                <div class="col-md-6 mb-4">
                  <label class="form-label">Check-out</label>
                  <input name="checkout" onchange="check_availability()" type="date" class="form-control shadow-none"
                    required>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="col-12">
                  <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                    <span class="visually-hidden">Please wait...</span>
                  </div>

                  <h6 class="mb-3 text-danger" id="pay_info">Provide check-in & check-out date!</h6>

                  <button name="pay_now" class="btn w-100 text-white custom-bg shadow-none mb-1" disabled>Pay
                    Now</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>


  <?php require('inc/footer.php'); ?>
  <script>

    let booking_form = document.getElementById('booking_form');
    let info_loader = document.getElementById('info_loader');
    let pay_info = document.getElementById('pay_info');

    function check_availability() {
      let checkin_val = booking_form.elements['checkin'].value;
      let checkout_val = booking_form.elements['checkout'].value;

      booking_form.elements['pay_now'].setAttribute('disabled', true);

      if (checkin_val != '' && checkout_val != '') {
        pay_info.classList.add('d-none');
        pay_info.classList.replace('text-dark', 'text-danger');
        info_loader.classList.remove('d-none');

        let data = new FormData();

        data.append('check_availability', '');
        data.append('check_in', checkin_val);
        data.append('check_out', checkout_val);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/confirm_booking.php", true);

        xhr.onload = function () {
          let data = JSON.parse(this.responseText);

          if (data.status == 'check_in_out_equal') {
            pay_info.innerText = "You cannot check-out on the same day!";
          }
          else if (data.status == 'check_out_earlier') {
            pay_info.innerText = "Check-out date is earlier than check-in date!";
          }
          else if (data.status == 'check_in_earlier') {
            pay_info.innerText = "Check-in date is earlier than today's date!";
          }
          else if (data.status == 'unavailable') {
            pay_info.innerText = "Room not available for this check-in date!";
          }
          else {
            pay_info.innerHTML = "No. of Days: " + data.days + "<br>Total Amount to Pay: " + data.payment + " VND";
            pay_info.classList.replace('text-danger', 'text-dark');
            booking_form.elements['pay_now'].removeAttribute('disabled');
          }

          pay_info.classList.remove('d-none');
          info_loader.classList.add('d-none');
        }

        xhr.send(data);
      }

    }

  </script>

</body>

</html>