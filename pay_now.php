<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');



if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
}

if (isset($_POST['pay_now'])) {
  if (!verify_csrf_token($_POST['csrf_token'])) {
    redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&error=csrf_failed');
  }
  $frm_data = filteration($_POST);

  // Validate dates
  $today_date = new DateTime(date("Y-m-d"));
  $checkin_date = new DateTime($frm_data['checkin']);
  $checkout_date = new DateTime($frm_data['checkout']);

  if ($checkin_date == $checkout_date) {
    redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&error=check_in_out_equal');
  } else if ($checkout_date < $checkin_date) {
    redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&error=check_out_earlier');
  }

  // Check availability
  $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
      WHERE booking_status=? AND room_id=?
      AND check_out > ? AND check_in < ?";
  $values = ['booked', $_SESSION['room']['id'], $frm_data['checkin'], $frm_data['checkout']];
  $tb_fetch = mysqli_fetch_assoc(select($tb_query, $values, 'siss'));

  $rq_result = select("SELECT `quantity`, `price` FROM `rooms` WHERE `id`=?", [$_SESSION['room']['id']], 'i');
  $rq_fetch = mysqli_fetch_assoc($rq_result);

  if (($rq_fetch['quantity'] - $tb_fetch['total_bookings']) == 0) {
    redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&error=unavailable');
  }

  // Check price
  $count_days = date_diff($checkin_date, $checkout_date)->days;
  $payment = $rq_fetch['price'] * $count_days;

  if ($payment != $_SESSION['room']['payment']) {
    redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&error=price_mismatch');
  }

  $ORDER_ID = 'ORD_' . $_SESSION['uId'] . random_int(11111, 9999999);
  $CUST_ID = $_SESSION['uId'];
  $TXN_AMOUNT = $_SESSION['room']['payment'];

  // Insert payment data into database
  $query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`,`order_id`) VALUES (?,?,?,?,?)";
  insert($query1, [
    $CUST_ID,
    $_SESSION['room']['id'],
    $frm_data['checkin'],
    $frm_data['checkout'],
    $ORDER_ID
  ], 'issss');

  $booking_id = mysqli_insert_id($con);

  $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`,
      `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
  insert($query2, [
    $booking_id,
    $_SESSION['room']['name'],
    $_SESSION['room']['price'],
    $TXN_AMOUNT,
    $frm_data['name'],
    $frm_data['phonenum'],
    $frm_data['address']
  ], 'isiisss');
}

redirect('bookings.php?booking_status=success');
?>