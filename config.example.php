<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* database connection */
$host = "YOUR_DB_HOST";         // e.g., localhost
$user = "YOUR_DB_USERNAME";     // e.g., root
$pass = "YOUR_DB_PASSWORD";     // e.g., empty string for XAMPP
$db   = "YOUR_DB_NAME";         // e.g., study_resources_db

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
/*
  Rename this file to config.php
  Fill in your database credentials before running the project.
*/
