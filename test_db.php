<?php
// Test database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

$con = mysqli_connect('localhost', 'g2travel', 'g2travel123', 'g2travel');

if(!$con) {
    echo "<p style='color:red'>Connection failed: " . mysqli_connect_error() . "</p>";
}

if($con) {
    echo "<p style='color:green'>✓ Database connected successfully!</p>";
    
    $result = mysqli_query($con, "SHOW TABLES");
    echo "<h2>Tables in g2travel database:</h2><ul>";
    while($row = mysqli_fetch_array($result)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    mysqli_close($con);
} else {
    echo "<p style='color:red'>✗ Connection failed: " . mysqli_connect_error() . "</p>";
}
?>
