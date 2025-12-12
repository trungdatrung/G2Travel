<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['get_bookings'])) {
  $frm_data = filteration($_POST);

  $query = "SELECT bo.*, bd.* FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?) 
      AND (bo.booking_status=? AND bo.arrival=?) ORDER BY bo.booking_id ASC";

  $res = select($query, ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "booked", 0], 'sssss');

  $i = 1;
  $table_data = "";

  if (mysqli_num_rows($res) == 0) {
    echo "<b>No Data Found!</b>";
    exit;
  }

  while ($data = mysqli_fetch_assoc($res)) {
    $date = date("d-m-Y", strtotime($data['datentime']));
    $checkin = date("d-m-Y", strtotime($data['check_in']));
    $checkout = date("d-m-Y", strtotime($data['check_out']));

    $table_data .= "
        <tr>
          <td>$i</td>
          <td>
            <span class='badge bg-primary'>
              Order ID: " . htmlspecialchars($data['order_id']) . "
            </span>
            <br>
            <b>Name:</b> " . htmlspecialchars($data['user_name']) . "
            <br>
            <b>Phone No:</b> " . htmlspecialchars($data['phonenum']) . "
          </td>
          <td>
            <b>Room:</b> " . htmlspecialchars($data['room_name']) . "
            <br>
            <b>Price:</b> " . htmlspecialchars($data['price']) . " VND
          </td>
          <td>
            <b>Check-in:</b> $checkin
            <br>
            <b>Check-out:</b> $checkout
            <br>
            <b>Paid:</b> " . htmlspecialchars($data['trans_amt']) . " VND
            <br>
            <b>Date:</b> $date
          </td>
          <td>
            <button type='button' onclick='assign_room(" . htmlspecialchars($data['booking_id']) . ")' class='btn btn-success btn-sm shadow-none' data-bs-toggle='modal' data-bs-target='#assign-room'>
              Assign Room
            </button>
            <br>
            <button type='button' onclick='cancel_booking(" . htmlspecialchars($data['booking_id']) . ")' class='mt-2 btn btn-danger btn-sm shadow-none'>
              Cancel Booking
            </button>
          </td>
        </tr>
      ";

    $i++;
  }

  echo $table_data;
}

if (isset($_POST['assign_room'])) {
  if (!verify_csrf_token($_POST['csrf_token'])) {
    echo 'csrf_failed';
    exit;
  }
  $frm_data = filteration($_POST);

  $query = "UPDATE `booking_order` bo INNER JOIN `booking_details` bd
      ON bo.booking_id = bd.booking_id
      SET bo.arrival = ?, bo.rate_review = ?, bd.room_no = ? 
      WHERE bo.booking_id = ?";

  $values = [1, 0, $frm_data['room_no'], $frm_data['booking_id']];

  $res = update($query, $values, 'iisi'); // it will update 2 rows so it will return 2

  echo ($res == 2) ? 1 : 0;
}

if (isset($_POST['cancel_booking'])) {
  if (!verify_csrf_token($_POST['csrf_token'])) {
    echo 'csrf_failed';
    exit;
  }
  $frm_data = filteration($_POST);

  $query = "UPDATE `booking_order` SET `booking_status`=?, `refund`=? WHERE `booking_id`=?";
  $values = ['cancelled', 0, $frm_data['booking_id']];
  $res = update($query, $values, 'sii');

  echo $res;
}
