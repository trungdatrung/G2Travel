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
  <title>Admin Panel - Test Layout</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h1>TEST PAGE</h1>
        <p>If you see the sidebar on the left, the layout is working!</p>
        <p>If you only see this text, the sidebar is not showing.</p>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php') ?>

</body>
</html>
