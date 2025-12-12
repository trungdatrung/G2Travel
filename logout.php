<?php 
  session_start();
  require('admin/inc/essentials.php');
  session_destroy();
  redirect('index.php');
?>