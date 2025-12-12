<?php

require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

if (isset($_GET['gen_pdf']) && isset($_GET['id'])) {
    $frm_data = filteration($_GET);

    $query = "SELECT bo.*, bd.*,uc.email FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      INNER JOIN `user_cred` uc ON bo.user_id = uc.id
      WHERE ((bo.booking_status='booked' AND bo.arrival=1) 
      OR (bo.booking_status='cancelled' AND bo.refund=1)
      OR (bo.booking_status='payment failed')) 
      AND bo.booking_id = '$frm_data[id]'";

    $res = mysqli_query($con, $query);
    $total_rows = mysqli_num_rows($res);

    if ($total_rows == 0) {
        header('location: dashboard.php');
        exit;
    }

    $data = mysqli_fetch_assoc($res);

    $date = date("d-m-Y", strtotime($data['datentime']));
    $checkin = date("d-m-Y", strtotime($data['check_in']));
    $checkout = date("d-m-Y", strtotime($data['check_out']));

    if ($data['booking_status'] == 'cancelled') {
        $refund = ($data['refund']) ? "Amount Refunded" : "Not Yet Refunded";

        $status_row = "<tr>
        <td>Amount Paid: $data[trans_amt]</td>
        <td>Refund: $refund</td>
      </tr>";
    } else if ($data['booking_status'] == 'payment failed') {
        $status_row = "<tr>
        <td>Transaction Amount: $data[trans_amt]</td>
        <td>Failure Response: $data[trans_resp_msg]</td>
      </tr>";
    } else {
        $status_row = "<tr>
        <td>Room Number: $data[room_no]</td>
        <td>Amount Paid: $data[trans_amt]</td>
      </tr>";
    }

    echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Booking Receipt - $data[order_id]</title>
      <style>
        body{ font-family: sans-serif; padding: 50px; }
        table{ width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th{ border: 1px solid #ccc; padding: 10px; text-align: left; }
        .header { text-align: center; margin-bottom: 30px; }
        .no-print { margin-bottom: 20px; }
        @media print { .no-print { display: none; } }
      </style>
    </head>
    <body>
      <div class="no-print">
        <button onclick="window.print()">Print Receipt</button>
      </div>

      <div class="header">
        <h2>BOOKING RECEIPT</h2>
        <h4>Order ID: $data[order_id]</h4>
      </div>

      <table>
        <tr>
          <td>Ordered Date: $date</td>
          <td>Status: <b>$data[booking_status]</b></td>
        </tr>
        <tr>
          <td>Name: $data[user_name]</td>
          <td>Email: $data[email]</td>
        </tr>
        <tr>
          <td>Phone Number: $data[phonenum]</td>
          <td>Address: $data[address]</td>
        </tr>
        <tr>
          <td>Room Name: $data[room_name]</td>
          <td>Cost: $data[price] VND</td>
        </tr>
        <tr>
          <td>Check-in: $checkin</td>
          <td>Check-out: $checkout</td>
        </tr>
        $status_row
      </table>
    </body>
    </html>
    HTML;

} else {
    header('location: dashboard.php');
}
?>