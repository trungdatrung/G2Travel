<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Dashboard</title>
  <?php require('inc/links.php'); ?>
</head>

<body class="bg-light">

  <?php

  require('inc/header.php');

  $is_shutdown = mysqli_fetch_assoc(select("SELECT `shutdown` FROM `settings` WHERE `sr_no`=?", [1], 'i'));

  $current_bookings = mysqli_fetch_assoc(selectAll('booking_order'));
  // Wait, the original query had COUNT with logic. selectAll only does SELECT * FROM table.
  // The query is: 
  // SELECT 
  //   COUNT(CASE WHEN booking_status='booked' AND arrival=0 THEN 1 END) AS `new_bookings`,
  //   COUNT(CASE WHEN booking_status='cancelled' AND refund=0 THEN 1 END) AS `refund_bookings`
  //   FROM `booking_order`
  //
  // This cannot be replaced by selectAll().
  // It must use select($sql, $values, $types). 
  // But since it has no variables, we can pass empty arrays.
  // select($sql, [], "")
  
  // However, looking at db_config.php:
  // function select($sql, $values, $datatypes) {
  //   if($stmt = mysqli_prepare($con, $sql)){
  //     mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
  
  // If usage is with 0 params, we can't easily use this function as is because it calls bind_param which expects args if datatypes is not empty (or maybe it errors if empty?).
  // Actually, bind_param causes issues if called with empty args on some setups or if the function doesn't handle it.
  
  // Let's look at db_config.php again (I have it in history).
  /*
    function select($sql, $values, $datatypes) {
      $con = $GLOBALS['con'];
      if($stmt = mysqli_prepare($con, $sql)){
        mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
        ...
  */

  // If I pass [], "", it will call bind_param($stmt, "", ...[]).
  // This is likely to fail or be awkward.
  
  // BUT, the error is "Too few arguments". That means the caller passed 1 arg.
  // The code I saw in dashboard.php is:
  // select("SELECT COUNT(CASE WHEN booking_status='booked' AND arrival=0 THEN 1 END) AS `new_bookings`, ...")
  
  // So I need to use a raw query or fix it to pass dummy args?
  // Or adds a new function `selectRaw`?
  // Or just use mysqli_query directly since there are no user inputs.
  
  // Let's just use mysqli_query directly for these static queries.
  
  $result_bookings = mysqli_query($con, "SELECT 
      COUNT(CASE WHEN booking_status='booked' AND arrival=0 THEN 1 END) AS `new_bookings`,
      COUNT(CASE WHEN booking_status='cancelled' AND refund=0 THEN 1 END) AS `refund_bookings`
      FROM `booking_order`");
  $current_bookings = mysqli_fetch_assoc($result_bookings);

  $unread_queries = mysqli_fetch_assoc(select("SELECT COUNT(sr_no) AS `count`
      FROM `user_queries` WHERE `seen`=?", [0], 'i'));

  $unread_reviews = mysqli_fetch_assoc(select("SELECT COUNT(sr_no) AS `count`
      FROM `rating_review` WHERE `seen`=?", [0], 'i'));

  $result_users = mysqli_query($con, "SELECT 
      COUNT(id) AS `total`,
      COUNT(CASE WHEN `status`=1 THEN 1 END) AS `active`,
      COUNT(CASE WHEN `status`=0 THEN 1 END) AS `inactive`,
      COUNT(CASE WHEN `is_verified`=0 THEN 1 END) AS `unverified`
      FROM `user_cred`");
  $current_users = mysqli_fetch_assoc($result_users);

  ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">

        <div class="d-flex align-items-center justify-content-between mb-4">
          <h3>DASHBOARD</h3>
          <?php
          if ($is_shutdown['shutdown']) {
            echo <<<data
                <h6 class="badge bg-danger py-2 px-3 rounded">Shutdown Mode is Active!</h6>
              data;
          }
          ?>
        </div>

        <div class="row mb-4">
          <div class="col-md-3 mb-4">
            <a href="new_bookings.php" class="text-decoration-none">
              <div class="card border-0 border-start border-4 border-success shadow-sm h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">New Bookings</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo htmlspecialchars($current_bookings['new_bookings']); ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-3 mb-4">
            <a href="refund_bookings.php" class="text-decoration-none">
              <div class="card border-0 border-start border-4 border-warning shadow-sm h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Refund Requests</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo htmlspecialchars($current_bookings['refund_bookings']); ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-3 mb-4">
            <a href="user_queries.php" class="text-decoration-none">
              <div class="card border-0 border-start border-4 border-info shadow-sm h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">User Messages</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo htmlspecialchars($unread_queries['count']); ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-3 mb-4">
            <a href="rate_review.php" class="text-decoration-none">
              <div class="card border-0 border-start border-4 border-primary shadow-sm h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Reviews</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo htmlspecialchars($unread_reviews['count']); ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5>Booking Analytics</h5>
          <select class="form-select shadow-none bg-light w-auto" onchange="booking_analytics(this.value)">
            <option value="1">Past 30 Days</option>
            <option value="2">Past 90 Days</option>
            <option value="3">Past 1 Year</option>
            <option value="4">All time</option>
          </select>
        </div>

        <div class="row mb-3">
          <div class="col-md-3 mb-4">
            <div class="card text-center text-primary p-3">
              <h6>Total Bookings</h6>
              <h1 class="mt-2 mb-0" id="total_bookings">0</h1>
              <h4 class="mt-2 mb-0" id="total_amt">0 VND</h4>
            </div>
          </div>
          <div class="col-md-3 mb-4">
            <div class="card text-center text-success p-3">
              <h6>Active Bookings</h6>
              <h1 class="mt-2 mb-0" id="active_bookings">0</h1>
              <h4 class="mt-2 mb-0" id="active_amt">0 VND</h4>
            </div>
          </div>
          <div class="col-md-3 mb-4">
            <div class="card text-center text-danger p-3">
              <h6>Cancelled Bookings</h6>
              <h1 class="mt-2 mb-0" id="cancelled_bookings">0</h1>
              <h4 class="mt-2 mb-0" id="cancelled_amt">0 VND</h4>
            </div>
          </div>
        </div>


        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5>User, Queries, Reviews Analytics</h5>
          <select class="form-select shadow-none bg-light w-auto" onchange="user_analytics(this.value)">
            <option value="1">Past 30 Days</option>
            <option value="2">Past 90 Days</option>
            <option value="3">Past 1 Year</option>
            <option value="4">All time</option>
          </select>
        </div>

        <div class="row mb-3">
          <div class="col-md-3 mb-4">
            <div class="card text-center text-success p-3">
              <h6>New Registration</h6>
              <h1 class="mt-2 mb-0" id="total_new_reg">0</h1>
            </div>
          </div>
          <div class="col-md-3 mb-4">
            <div class="card text-center text-primary p-3">
              <h6>Queries</h6>
              <h1 class="mt-2 mb-0" id="total_queries">0</h1>
            </div>
          </div>
          <div class="col-md-3 mb-4">
            <div class="card text-center text-primary p-3">
              <h6>Reviews</h6>
              <h1 class="mt-2 mb-0" id="total_reviews">0</h1>
            </div>
          </div>
        </div>

        <h5>Users</h5>
        <div class="row mb-3">
          <div class="col-md-3 mb-4">
            <div class="card text-center text-info p-3">
              <h6>Total</h6>
              <h1 class="mt-2 mb-0"><?php echo htmlspecialchars($current_users['total']); ?></h1>
            </div>
          </div>
          <div class="col-md-3 mb-4">
            <div class="card text-center text-success p-3">
              <h6>Active</h6>
              <h1 class="mt-2 mb-0"><?php echo htmlspecialchars($current_users['active']); ?></h1>
            </div>
          </div>
          <div class="col-md-3 mb-4">
            <div class="card text-center text-warning p-3">
              <h6>Inactive</h6>
              <h1 class="mt-2 mb-0"><?php echo htmlspecialchars($current_users['inactive']); ?></h1>
            </div>
          </div>
          <div class="col-md-3 mb-4">
            <div class="card text-center text-danger p-3">
              <h6>Unverified</h6>
              <h1 class="mt-2 mb-0"><?php echo htmlspecialchars($current_users['unverified']); ?></h1>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>


  <?php require('inc/scripts.php'); ?>
  <script src="scripts/dashboard.js"></script>
</body>

</html>