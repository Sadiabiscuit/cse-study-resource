<?php
require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Study Resources</title>
    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: #f2f0ea;
            color: #333;
        }
        header {
            background: #dcd6f7;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            color: #4b3f72;
            margin: 0;
        }
        nav a {
            margin-left: 20px;
            text-decoration: none;
            color: #4b3f72;
            font-weight: 600;
        }
        .container {
            padding: 40px 50px;
        }
        .resource-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .resource-card h3 {
            color: #4b3f72;
        }
        .resource-card p {
            color: #6a5a87;
        }
        footer {
            background: #dcd6f7;
            text-align: center;
            padding: 20px;
            color: #4b3f72;
        }
        .search-bar input, .search-bar button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-bar button {
            background-color: #4b3f72;
            color: white;
            border: none;
            margin-left: 5px;
            cursor: pointer;
        }
        .video-thumbnail {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 10px;
            object-fit: cover;
            margin-top: 15px;
        }
        .resources-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .resource-card {
            width: 100%;
            box-sizing: border-box;
        }

        /* View Details button */
        .view-details-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #6c5ce7, #a29bfe); /* soft gradient matching theme */
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .view-details-btn:hover {
            background: linear-gradient(135deg, #5a4dd9, #8f82fd);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

    </style>
</head>
<body>

<header>
    <h1>CSEHub💻</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="resource.php">Resources</a>
        <a href="upload_resource.php">Upload</a>
        <a href="review.php">Reviews</a>
    </nav>
</header>

<div class="container">
    <div class="search-bar" style="margin-bottom: 30px;">
        <form method="GET" action="resource.php">
            <input type="text" name="search" placeholder="Search by Topic/Course Code"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" name="type" value="all">All</button>
            <button type="submit" name="type" value="video">Videos</button>
            <button type="submit" name="type" value="note">Notes</button>
            <button type="submit" name="type" value="file">Slides</button>
        </form>
    </div>

    <h2 style="color:#4b3f72;">Available Study Resources</h2>
    <div class="resources-container">
    <?php
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $type   = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';

    $where = [];
    if ($search !== '') {
        $where[] = "(Topic LIKE '%$search%' 
                OR Course_Code LIKE '%$search%' 
                OR Title LIKE '%$search%' 
                OR Description LIKE '%$search%')";
    }
    if ($type !== '' && $type !== 'all') {
        $where[] = "Resource_Type = '$type'";
    }
    $sql = "SELECT * FROM Resources";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="resource-card">
                <h3><?php echo $row['Title']; ?></h3>
                <p><b>Topic:</b> <?php echo $row['Topic']; ?></p>
                <p><?php echo $row['Description']; ?></p>
                <p><b>Course Code:</b> <?php echo $row['Course_Code']; ?></p>
                <p><b>Rating:</b> <?php echo $row['Average_Rating']; ?>/5</p>

                <?php
                if (!empty($row['File_Path'])) {
                    // YouTube
                    if (strpos($row['File_Path'], 'youtube.com') !== false || strpos($row['File_Path'], 'youtu.be') !== false) {
                        if (preg_match("/v=([a-zA-Z0-9_-]+)/", $row['File_Path'], $matches)) {
                            $video_id = $matches[1];
                        } elseif (preg_match("/youtu\.be\/([a-zA-Z0-9_-]+)/", $row['File_Path'], $matches)) {
                            $video_id = $matches[1];
                        } else {
                            $video_id = '';
                        }
                        if ($video_id) {
                            echo '<iframe class="video-thumbnail" src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';
                        } else {
                            echo "<p>No resource available.</p>";
                        }
                    } else {
                            $ext = strtolower(pathinfo($row['File_Path'], PATHINFO_EXTENSION));

                            if ($ext === 'mp4') {
                                echo '<video class="video-thumbnail" controls>
                                        <source src="'.$row['File_Path'].'" type="video/mp4">
                                    </video>';
                            } else {
                                echo '<p>
                                        📄 <a href="'.$row['File_Path'].'" target="_blank">
                                        View / Download File
                                        </a>
                                    </p>';
                            }
                        }

                } else {
                    echo "<p>No resource available.</p>";
                }
                ?>

                <!-- View Details Button -->
                <a href="resource_details.php?id=<?php echo $row['ResourceID']; ?>" class="view-details-btn">
                    View Details / Comments
                </a>
            </div>

        <?php }
    } else {
        echo "<p>No resources found.</p>";
    }
    ?>
    </div>

    <div class="recent-resources" style="margin-bottom: 40px;">
        <h2 style="color:#4b3f72;">Recently Uploaded Resources</h2>
        <div class="resources-container">
        <?php
        $recent_sql = "SELECT * FROM Resources ORDER BY Upload_Date DESC LIMIT 4";
        $recent_result = $conn->query($recent_sql);

        if ($recent_result->num_rows > 0) {
            while ($row = $recent_result->fetch_assoc()) {
                ?>
                <div class="resource-card">
                    <h3><?php echo $row['Title']; ?></h3>
                    <p><b>Topic:</b> <?php echo $row['Topic']; ?></p>
                    <p><?php echo $row['Description']; ?></p>
                    <p><b>Course Code:</b> <?php echo $row['Course_Code']; ?></p>
                    <p><b>Rating:</b> <?php echo $row['Average_Rating']; ?>/5</p>

                    <?php
                    if (!empty($row['File_Path'])) {
                        // YouTube
                        if (strpos($row['File_Path'], 'youtube.com') !== false || strpos($row['File_Path'], 'youtu.be') !== false) {
                            if (preg_match("/v=([a-zA-Z0-9_-]+)/", $row['File_Path'], $matches)) {
                                $video_id = $matches[1];
                            } elseif (preg_match("/youtu\.be\/([a-zA-Z0-9_-]+)/", $row['File_Path'], $matches)) {
                                $video_id = $matches[1];
                            } else {
                                $video_id = '';
                            }
                            if ($video_id) {
                                echo '<iframe class="video-thumbnail" src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';
                            } else {
                                echo "<p>No resource available.</p>";
                            }
                        } else {
                                $ext = strtolower(pathinfo($row['File_Path'], PATHINFO_EXTENSION));

                                if ($ext === 'mp4') {
                                    echo '<video class="video-thumbnail" controls>
                                            <source src="'.$row['File_Path'].'" type="video/mp4">
                                        </video>';
                                } else {
                                    echo '<p>
                                            📄 <a href="'.$row['File_Path'].'" target="_blank">
                                            View / Download File
                                            </a>
                                        </p>';
                                }
                            }

                    } else {
                        echo "<p>No resource available.</p>";
                    }
                    ?>
                    <!-- View Details Button -->
                    <a href="resource_details.php?id=<?php echo $row['ResourceID']; ?>" class="view-details-btn">
                        View Details / Comments
                    </a>
                </div>

            <?php }
        } else {
            echo "<p>No recent resources found.</p>";
        }
        ?>
        </div>
    </div>

</div>

<footer>
    © 2025 CSE_Study_Resource
</footer>

</body>
</html>

