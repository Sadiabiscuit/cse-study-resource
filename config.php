
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* database connection */
$host = "localhost";
$user = "root";
$pass = "";                  // default XAMPP password is empty
$db   = "study_resources_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>