<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('admin/inc/essentials.php');
require('admin/inc/db_config.php');

session_start();

echo "Testing admin login...<br>";
echo "Database connection: " . ($con ? "OK" : "Failed") . "<br><br>";

$admin_name = 'holden';
$admin_pass = '12345';

$query = "SELECT * FROM `admin_cred` WHERE `admin_name`=? AND `admin_pass`=?";
$values = [$admin_name, $admin_pass];

try {
    $res = select($query, $values, "ss");
    echo "Query executed<br>";
    echo "Number of rows: " . $res->num_rows . "<br>";
    
    if($res->num_rows == 1){
        $row = mysqli_fetch_assoc($res);
        echo "Login successful!<br>";
        echo "Admin ID: " . $row['sr_no'] . "<br>";
        echo "Admin Name: " . $row['admin_name'] . "<br>";
    } else {
        echo "Login failed - no matching credentials<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
