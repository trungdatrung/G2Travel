<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

//frontend purpose data
define('SITE_URL', 'http://localhost:8000/');
define('ABOUT_IMG_PATH', SITE_URL . 'images/about/');
define('CAROUSEL_IMG_PATH', SITE_URL . 'images/carousel/');
define('FACILITIES_IMG_PATH', SITE_URL . 'images/facilities/');
define('ROOMS_IMG_PATH', SITE_URL . 'images/rooms/');
define('USERS_IMG_PATH', SITE_URL . 'images/users/');

//backend upload process needs this data

define('UPLOAD_IMAGE_PATH', dirname(__DIR__, 2) . '/images/');
define('ABOUT_FOLDER', 'about/');
define('CAROUSEL_FOLDER', 'carousel/');
define('FACILITIES_FOLDER', 'facilities/');
define('ROOMS_FOLDER', 'rooms/');
define('USERS_FOLDER', 'users/');

function adminLogin()
{
  if (!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)) {
    echo "<script>window.location.href='/admin/index.php'</script>";
    exit;
  }
}

function redirect($url)
{
  echo "<script>window.location.href='$url'</script>";
  exit;
}

function alert($type, $msg)
{

  $bs_class = ($type == 'success') ? 'alert-success' : 'alert-danger';

  echo <<<alert
      <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert">
        <strong class="me-3">'".htmlspecialchars($msg)."'</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    alert;
}

function uploadImage($image, $folder)
{
  $valid_mime = ['image/jpeg', 'image/png', 'image/webp'];
  $img_mime = $image['type'];

  if (!in_array($img_mime, $valid_mime)) {
    return 'Format not supported!';
  } else if (($image['size'] / (1024 * 1024)) > 10) {
    return 'Please choose an image smaller than 10MB!';
  } else {
    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
    $rname = 'IMG_' . random_int(11111, 99999) . ".$ext";
    $img_path = UPLOAD_IMAGE_PATH . $folder . $rname;
    if (move_uploaded_file($image['tmp_name'], $img_path)) {
      return $rname;
    } else {
      return 'Image upload failed!';
    }
  }
}

function deleteImage($image, $folder)
{
  if (file_exists(UPLOAD_IMAGE_PATH . $folder . $image)) {
    if (unlink(UPLOAD_IMAGE_PATH . $folder . $image)) {
      return true;
    } else {
      return false;
    }
  } else {
    return true; // Return true if file doesn't exist so DB entry can still be deleted
  }
}

function uploadSVGImage($image, $folder)
{
  $valid_mime = ['image/svg+xml'];
  $img_mime = $image['type'];

  if (!in_array($img_mime, $valid_mime)) {
    return 'Format not supported!'; //invalid image mime or format
  } else if (($image['size'] / (1024 * 1024)) > 10) {
    return 'Please choose an image smaller than 10MB!'; //invalid size greater than 1mb
  } else {
    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
    $rname = 'IMG_' . random_int(11111, 99999) . ".$ext";

    $img_path = UPLOAD_IMAGE_PATH . $folder . $rname;
    if (move_uploaded_file($image['tmp_name'], $img_path)) {
      return $rname;
    } else {
      return 'Image upload failed!';
    }
  }
}

function uploadUserImage($image)
{
  $valid_mime = ['image/jpeg', 'image/png', 'image/webp'];
  $img_mime = $image['type'];

  if (!in_array($img_mime, $valid_mime)) {
    return 'inv_img'; //invalid image mime or format
  } else {
    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
    $rname = 'IMG_' . random_int(11111, 99999) . ".jpeg";

    $img_path = UPLOAD_IMAGE_PATH . USERS_FOLDER . $rname;

    if ($ext == 'png' || $ext == 'PNG') {
      $img = imagecreatefrompng($image['tmp_name']);
    } else if ($ext == 'webp' || $ext == 'WEBP') {
      $img = imagecreatefromwebp($image['tmp_name']);
    } else {
      $img = imagecreatefromjpeg($image['tmp_name']);
    }


    if (imagejpeg($img, $img_path, 75)) {
      return $rname;
    } else {
      return 'upd_failed';
    }
  }
}

function generate_csrf_token()
{
  $token = bin2hex(random_bytes(32));
  $_SESSION['csrf_token'] = $token;
  return $token;
}

function verify_csrf_token($token)
{
  if (!isset($_SESSION['csrf_token'])) {
    return false;
  }
  if (empty($token)) {
    return false;
  }
  if (!hash_equals($_SESSION['csrf_token'], $token)) {
    return false;
  }
  return true;
}
