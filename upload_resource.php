<?php
session_start();
require_once "config.php";


/* force login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if (isset($_POST['upload'])) {

    $title       = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $topic       = $conn->real_escape_string($_POST['topic']);
    $course      = $conn->real_escape_string($_POST['course_code']);
    $type        = $conn->real_escape_string($_POST['resource_type']);
    $uploader_id = intval($_SESSION['user_id']);
    $file_path   = "";

    /* for YouTube link */
    if ($type === "video" && !empty($_POST['youtube_link'])) {
        $file_path = $conn->real_escape_string($_POST['youtube_link']);
    }

    /* for File upload */
    if (!empty($_FILES['resource_file']['name'])) {

        $ext = strtolower(pathinfo($_FILES['resource_file']['name'], PATHINFO_EXTENSION));

        $allowed = [
            'video' => ['mp4'],
            'note'  => ['pdf','doc','docx'],
            'file'  => ['ppt','pptx','pdf']
        ];

        if (!in_array($ext, $allowed[$type])) {
            die("Invalid file type for selected resource type");
        }

        $folder = "uploads/$type/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES['resource_file']['name']);
        $target   = $folder . $filename;

        move_uploaded_file($_FILES['resource_file']['tmp_name'], $target);
        $file_path = $target;
    }

    /* ----------where I Insert ---------- */
    $conn->query("INSERT INTO resources 
    (Title, Description, Topic, Course_Code, Resource_Type, File_Path, Uploader_ID)
    VALUES ('$title','$description','$topic','$course','$type','$file_path',$uploader_id)");


    $message = "✅ Resource uploaded successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Resource</title>

<style>
body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    background: #f2f0ea;
}
header {
    background: #dcd6f7;
    padding: 20px 50px;
    display: flex;
    justify-content: space-between;
}
header h1 { color: #4b3f72; }
nav a {
    margin-left: 20px;
    text-decoration: none;
    color: #4b3f72;
    font-weight: 600;
}

nav a:hover {
    text-decoration: underline;
}


.container {
    max-width: 700px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
label {
    font-weight: 600;
    color: #4b3f72;
}
input, textarea, select {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ccc;
}
button {
    background: #4b3f72;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
}
.success {
    background: #e6ffe6;
    color: #2d7a2d;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>
</head>

<body>

<header>
   <h1>CSEHub💻</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="resource.php">Resources</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="review.php">Reviews</a>
    </nav>
</header>


<div class="container">

<h2 style="color:#4b3f72;">Upload your study materials here! 🤓</h2>

<?php if ($message): ?>
<div class="success"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<label>Title</label>
<input type="text" name="title" required>

<label>Description</label>
<textarea name="description" rows="4" required></textarea>

<label>Topic</label>
<input type="text" name="topic" required>

<label>Course Code</label>
<input type="text" name="course_code" required>

<label>Resource Type</label>
<select name="resource_type" required>
    <option value="">Select</option>
    <option value="video">Video</option>
    <option value="note">Note</option>
    <option value="file">Slides</option>
</select>

<label>YouTube Link (only for video)</label>
<input type="text" name="youtube_link" placeholder="https://youtube.com/...">

<label>Upload File (mp4 / pdf / doc / ppt)</label>
<input type="file" name="resource_file">

<button type="submit" name="upload">Upload Resource</button>

</form>
</div>

</body>
</html>
