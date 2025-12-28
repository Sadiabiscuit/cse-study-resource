<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_id'])) {
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'resource.php';
    header("Location: login.php?redirect=" . urlencode($redirect_url));
    exit;
}

if (!isset($_GET['resource_id'])) {
    die("Invalid resource");
}

$resource_id = intval($_GET['resource_id']);
$user_id     = intval($_SESSION['user_id']);

$resource = $conn->query("SELECT * FROM resources WHERE ResourceID = $resource_id")->fetch_assoc();
if (!$resource) die("Resource not found");

$file_path = $resource['File_Path'];

// Insert download record
$check = $conn->query("SELECT * FROM resource_download WHERE UserID = $user_id AND ResourceID = $resource_id");
if ($check && $check->num_rows == 0) {
    $sql = "INSERT INTO resource_download (UserID, ResourceID, Download_Date) VALUES ($user_id, $resource_id, NOW())";
    if (!$conn->query($sql)) {
        die("Error inserting download record: " . $conn->error);
    }
}

// Serve file
if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    die("File does not exist: $file_path");
}
?>
