<?php
session_start(); 
require_once "config.php";

if (!isset($_GET['id'])) {
    die("Invalid resource");
}

$resource_id = intval($_GET['id']);

/* ---------- This handles Comment Submission ---------- */
if (isset($_POST['add_comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $redirect_url = "resource_details.php?id=$resource_id";
        header("Location: login.php?redirect=" . urlencode($redirect_url));
        exit;
    }

    $comment = $conn->real_escape_string($_POST['comment']);
    if ($comment != '') {
        $user_id = $_SESSION['user_id'];
        $conn->query("INSERT INTO comment (ResourceID, Comment_Text, Commenter_ID)
                      VALUES ($resource_id, '$comment', $user_id)");
    }
}

/* ---------- This part handles Comment Deletion ---------- */
if (isset($_GET['delete_comment']) && isset($_SESSION['user_id'])) {
    $delete_id = intval($_GET['delete_comment']);
    $user_id = intval($_SESSION['user_id']); 

    $check = $conn->query("SELECT * FROM comment WHERE Comment_ID = $delete_id AND Commenter_ID = $user_id");

    if ($check && $check->num_rows > 0) {
        $conn->query("DELETE FROM comment WHERE Comment_ID = $delete_id AND Commenter_ID = $user_id");
        header("Location: resource_details.php?id=$resource_id");
        exit;
    } else {
        echo "You cannot delete this comment.";
    }
}

/* ---------- This handles Rating Submission ---------- */
if (isset($_POST['add_rating'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?redirect=resource_details.php?id=$resource_id");
        exit;
    }

    $score   = intval($_POST['rating']);
    $user_id = intval($_SESSION['user_id']);

    if ($score >= 1 && $score <= 5) {
        $check = $conn->query("
            SELECT r.RatingID
            FROM user_rating ur
            JOIN resource_rating rr ON ur.RatingID = rr.RatingID
            JOIN rating r ON r.RatingID = ur.RatingID
            WHERE ur.User_ID = $user_id AND rr.ResourceID = $resource_id
        ");

        if ($check->num_rows > 0) {
            echo "<script>alert('You already rated this resource');</script>";
        } else {
            $conn->query("INSERT INTO rating (Score, Rating_Date) VALUES ($score, NOW())");
            $rating_id = $conn->insert_id;

            $conn->query("INSERT INTO user_rating (User_ID, RatingID) VALUES ($user_id, $rating_id)");
            $conn->query("INSERT INTO resource_rating (ResourceID, RatingID) VALUES ($resource_id, $rating_id)");

            $avg = $conn->query("
                SELECT ROUND(AVG(r.Score),1) AS avg_rating
                FROM rating r
                JOIN resource_rating rr ON r.RatingID = rr.RatingID
                WHERE rr.ResourceID = $resource_id
            ")->fetch_assoc()['avg_rating'];

            $conn->query("UPDATE resources SET Average_Rating = $avg WHERE ResourceID = $resource_id");
        }
    }
}

/* ---------- Fetch Resource ---------- */
$resource = $conn->query("SELECT * FROM resources WHERE ResourceID = $resource_id")->fetch_assoc();

/* ---------- Fetch Comments ---------- */
$comments = $conn->query("SELECT * FROM comment
                          WHERE ResourceID = $resource_id
                          ORDER BY Comment_Date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resource Details</title>
<style>
body { margin:0; font-family:"Poppins",sans-serif; background:#f2f0ea; color:#333; }
header { background:#dcd6f7; padding:20px 50px; display:flex; justify-content:space-between; }
header h1 { color:#4b3f72; }
nav a { margin-left:20px; text-decoration:none; color:#4b3f72; font-weight:600; }
.container { padding:40px 50px; }
.card { background:white; border-radius:15px; padding:25px; box-shadow:0 4px 10px rgba(0,0,0,0.08); margin-bottom:30px; }
button { background:#4b3f72; color:white; padding:8px 14px; border:none; border-radius:8px; cursor:pointer; }
textarea { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; }
.video-thumbnail { width:100%; aspect-ratio:16/9; border-radius:10px; object-fit:cover; margin:15px 0; }
.delete-comment { color:red; text-decoration:none; margin-left:10px; }
.download-btn { display:inline-block; margin-top:10px; padding:8px 16px; background:#4b3f72; color:white; border-radius:8px; text-decoration:none; }
</style>
</head>
<body>

<header>
<h1>CSEHub💻</h1>
<nav>
<a href="resource.php">Back to Resources</a>
</nav>
</header>

<div class="container">

<div class="card">
<h2 style="color:#4b3f72;"><?php echo htmlspecialchars($resource['Title']); ?></h2>
<p><?php echo htmlspecialchars($resource['Description']); ?></p>
<p><b>Topic:</b> <?php echo htmlspecialchars($resource['Topic']); ?></p>
<p><b>Course Code:</b> <?php echo htmlspecialchars($resource['Course_Code']); ?></p>
<p><b>Average Rating:</b> <?php echo htmlspecialchars($resource['Average_Rating']); ?>/5</p>

<!-- Display Resource -->
<div>
<?php
if (!empty($resource['File_Path'])) {
    $file_path = $resource['File_Path'];

    // YouTube videos
    if (strpos($file_path, 'youtube.com') !== false || strpos($file_path, 'youtu.be') !== false) {
        if (preg_match("/v=([a-zA-Z0-9_-]+)/", $file_path, $matches)) {
            $video_id = $matches[1];
        } elseif (preg_match("/youtu\.be\/([a-zA-Z0-9_-]+)/", $file_path, $matches)) {
            $video_id = $matches[1];
        } else { $video_id = ''; }

        if ($video_id) {
            echo '<iframe class="video-thumbnail" src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';
        } else { echo "<p>No resource available.</p>"; }
    } else {
        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        if ($ext === 'mp4') {
            echo '<video class="video-thumbnail" controls>
                    <source src="'.htmlspecialchars($file_path).'" type="video/mp4">
                  Your browser does not support the video tag.
                  </video>';
        }

        // Download button for files and MP4
        if (isset($_SESSION['user_id'])) {
            $user_id = intval($_SESSION['user_id']);

            // Record download if not already recorded
            $check = $conn->query("SELECT * FROM resource_download WHERE UserID = $user_id AND ResourceID = $resource_id");
            if ($check && $check->num_rows == 0) {
                $conn->query("INSERT INTO resource_download (UserID, ResourceID, Download_Date) VALUES ($user_id, $resource_id, NOW())");
            }

            echo '<a class="download-btn" href="'.htmlspecialchars($file_path).'" download>Download File</a>';
        } else {
            echo '<p><a href="login.php" style="color:red;">Login to download this file</a></p>';
        }
    }
} else {
    echo "<p>No resource available.</p>";
}
?>
</div>
</div>

<!-- Rating -->
<div class="card">
<h3 style="color:#4b3f72;">Rate this Resource</h3>
<form method="POST">
<select name="rating" required>
<option value="">Select</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
</select>
<button type="submit" name="add_rating">Submit Rating</button>
</form>
</div>

<!-- Comments -->
<div class="card">
<h3 style="color:#4b3f72;">Comments</h3>

<?php 
if ($comments->num_rows > 0) {
    while ($c = $comments->fetch_assoc()) { 
        $comment_id = isset($c['Comment_ID']) ? intval($c['Comment_ID']) : 0;
        $comment_text = htmlspecialchars($c['Comment_Text']);
        $comment_date = htmlspecialchars($c['Comment_Date']);
        $commenter_id = $c['Commenter_ID']; // ID of commenter
        $can_delete = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $commenter_id;

        $commenter_name = $commenter_id; 

        if (!empty($commenter_id)) {
            $commenter_id_int = intval($commenter_id); 
            $user_query = $conn->query("SELECT Name FROM `user` WHERE User_ID = $commenter_id_int");
            if ($user_query && $user_query->num_rows > 0) {
                $commenter_name = $user_query->fetch_assoc()['Name'];
            }
            }

        
?>
    <p><b><?php echo htmlspecialchars($commenter_name); ?></b> 💬 <?php echo $comment_text; ?></p>
    <small><?php echo $comment_date; ?></small>

<?php if ($can_delete): ?>
    <a href="resource_details.php?id=<?php echo $resource_id; ?>&delete_comment=<?php echo $comment_id; ?>" 
       onclick="return confirm('Are you sure you want to delete this comment?');"
       class="delete-comment">Delete</a>
<?php endif; ?>
<hr>
<?php 
    }
} else { echo "<p>No comments yet. Be the first to comment!</p>"; }
?>

<!-- This adds Comment Form -->
<form method="POST">
<textarea name="comment" rows="4" placeholder="Write your comment..." required></textarea>
<br><br>
<button type="submit" name="add_comment">Post Comment</button>
</form>
</div>

</div>
</body>
</html>
