<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

// uploaded resources by this user
$stmt = $conn->prepare("
    SELECT ResourceID, Title, Topic, Upload_Date, Average_Rating, Course_Code
    FROM resources
    WHERE Uploader_ID = ?
    ORDER BY Upload_Date DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$myUploads = $stmt->get_result();

// resources downloaded by this user
$stmt = $conn->prepare("
    SELECT r.ResourceID, r.Title, r.Topic, r.Course_Code, d.Download_Date
    FROM resource_download d
    JOIN resources r ON d.ResourceID = r.ResourceID
    WHERE d.UserID = ?
    ORDER BY d.Download_Date DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$myDownloads = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSEHub | Dashboard</title>
    <link rel="icon" type="image/png" href="csehub-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: radial-gradient(circle at top left, #f2eaff 0, #f8f7fb 40%, #f8f7fb 100%);
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            scroll-behavior:smooth;
        }

        .page-wrapper{
            max-width:1200px;
            margin:32px auto 48px;
            padding:0 24px;
            animation: fadeUp 0.45s ease-out;
        }

        .dashboard-card{
            background:#ffffff;
            border-radius:22px;
            padding:24px 26px 28px;
            box-shadow:0 18px 60px rgba(15,23,42,0.08);
            border:1px solid rgba(148,163,184,0.18);
            backdrop-filter: blur(8px);
            transition:transform 0.25s ease, box-shadow 0.25s ease, border-color 0.2s ease, background 0.2s ease;
            position:relative;
            overflow:hidden;
        }
        .dashboard-card::before{
            content:"";
            position:absolute;
            inset:0;
            opacity:0;
            background:radial-gradient(circle at top left, rgba(129,140,248,0.18), transparent 60%);
            transition:opacity 0.25s ease;
            pointer-events:none;
        }
        .dashboard-card:hover{
            transform:translateY(-2px);
            box-shadow:0 22px 70px rgba(15,23,42,0.12);
            border-color:#a855f7;
            background:linear-gradient(135deg,#ffffff 0,#eef2ff 100%);
        }
        .dashboard-card:hover::before{
            opacity:1;
        }

        .top-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            margin-bottom:22px;
        }

        .back-pill{
            flex:0 0 auto;
            border-radius:999px;
            border:1px solid #e2e8f0;
            padding:8px 20px;
            display:inline-flex;
            align-items:center;
            gap:8px;
            background:#ffffff;
            box-shadow:0 1px 2px rgba(15,23,42,0.05);
            transition:transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
            text-decoration:none;
            color:#111827;
            font-size:14px;
        }
        .back-pill:hover{
            transform:translateY(-1px);
            box-shadow:0 6px 18px rgba(15,23,42,0.12);
            border-color:#a855f7;
        }

        .search-input{
            flex:1 1 auto;
            border-radius:999px;
            border:1px solid #e2e8f0;
            padding:11px 18px;
            background:#ffffff;
            box-shadow:0 1px 2px rgba(15,23,42,0.03);
            transition:border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        }
        .search-input:focus{
            outline:none;
            border-color:#8b5cf6;
            box-shadow:0 0 0 3px rgba(139,92,246,0.2);
            transform:translateY(-1px);
        }

        .heading-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            margin-bottom:22px;
        }
        .heading-row h1{
            font-size:24px;
            margin:0;
        }
        .subtext{
            color:#6b7280;
            font-size:13px;
            margin-top:4px;
        }

        .actions{
            display:flex;
            gap:12px;
        }
        .btn-primary-soft{
            background:linear-gradient(135deg,#6366f1,#a855f7);
            border-radius:999px;
            border:none;
            color:#ffffff;
            padding:8px 18px;
            font-size:14px;
            box-shadow:0 10px 30px rgba(88,80,236,0.35);
            transition:transform 0.2s ease, box-shadow 0.2s ease, filter 0.15s ease;
            text-decoration:none;
        }
        .btn-primary-soft:hover{
            transform:translateY(-1px);
            box-shadow:0 16px 40px rgba(88,80,236,0.45);
            filter:brightness(1.04);
        }
        .btn-secondary-soft{
            background:#ffffff;
            border-radius:999px;
            border:1px solid #e2e8f0;
            color:#111827;
            padding:8px 18px;
            font-size:14px;
            box-shadow:0 1px 2px rgba(15,23,42,0.05);
            transition:transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
            text-decoration:none;
        }
        .btn-secondary-soft:hover{
            transform:translateY(-1px);
            box-shadow:0 6px 18px rgba(15,23,42,0.12);
            border-color:#a855f7;
            background-color:#f9fafb;
        }

        .cards-row{
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:24px;
            margin-top:10px;
        }
        @media (max-width:900px){
            .cards-row{
                grid-template-columns:1fr;
            }
        }

        .card-dashboard{
            background:#ffffff;
            border-radius:18px;
            padding:18px 20px;
            box-shadow:0 10px 35px rgba(15,23,42,0.08);
            border:1px solid rgba(226,232,240,0.9);
            transition:transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
            position:relative;
            overflow:hidden;
        }
        .card-dashboard:hover{
            transform:translateY(-2px);
            box-shadow:0 16px 45px rgba(15,23,42,0.12);
            border-color:#a855f7;
            background:linear-gradient(135deg,#ffffff 0,#eff6ff 100%);
        }
        .card-dashboard h5{
            font-size:16px;
            margin-bottom:4px;
        }
        .card-dashboard small{
            color:#6b7280;
        }

        .dashboard-table{
            margin-top:14px;
            font-size:13px;
        }
        .dashboard-table th{
            border-bottom:1px solid #e5e7eb;
            color:#6b7280;
            font-weight:500;
        }
        .dashboard-table td{
            vertical-align:middle;
            border-color:#f3f4f6;
        }

        .link-view{
            font-size:13px;
            color:#6366f1;
            text-decoration:none;
            font-weight:500;
            transition:color 0.15s ease;
        }
        .link-view:hover{
            color:#4f46e5;
            text-decoration:underline;
        }

        .empty-state{
            padding:16px 0;
            color:#9ca3af;
            font-size:13px;
        }

        @keyframes fadeUp{
            from{
                opacity:0;
                transform:translateY(10px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        @media (max-width:768px){
            .page-wrapper{
                padding:0 16px;
            }
            .dashboard-card{
                border-radius:18px;
                padding:18px 18px 22px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main class="page-wrapper">
    <div class="dashboard-card">

        <div class="top-row">
            <a href="index.php" class="back-pill">← Back</a>
            <input class="search-input" type="text" placeholder="Search resources">
        </div>

        <div class="heading-row">
            <div>
                    <h1>
                        Welcome, 
                        <?php 
                            $displayName = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
                            echo htmlspecialchars($displayName);
                        ?>
                    </h1>

                <p class="subtext">
                    See everything you’ve uploaded and downloaded across CSE_Study_Resource.
                </p>
            </div>
            <div class="actions">
                <a href="upload_resource.php" class="btn-primary-soft">Upload resource</a>
                <a href="resource.php" class="btn-secondary-soft">Browse all resources</a>
            </div>
        </div>

        <div class="cards-row">
            <!-- Uploaded resources -->
            <section class="card-dashboard">
                <h5>My uploaded resources</h5>
                <small>Resources you have contributed for your classmates.</small>

                <table class="table dashboard-table">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Course</th>
                        <th>Topic</th>
                        <th>Date</th>
                        <th>Rating</th>
                        <th>View</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($myUploads->num_rows > 0): ?>
                        <?php while ($row = $myUploads->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                                <td><?php echo htmlspecialchars($row['Course_Code']); ?></td>
                                <td><?php echo htmlspecialchars($row['Topic']); ?></td>
                                <td><?php echo htmlspecialchars($row['Upload_Date']); ?></td>
                                <td><?php echo htmlspecialchars($row['Average_Rating'] ?? '-'); ?></td>
                                <td>
                                    <a class="link-view" href="resource.php?id=<?php echo $row['ResourceID']; ?>">Open</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">No uploaded resources yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- Downloaded resources -->
            <section class="card-dashboard">
                <h5>My downloaded resources</h5>
                <small>Recently viewed or downloaded study materials.</small>

                <table class="table dashboard-table">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Course</th>
                        <th>Topic</th>
                        <th>Downloaded on</th>
                        <th>View</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($myDownloads->num_rows > 0): ?>
                        <?php while ($row = $myDownloads->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                                <td><?php echo htmlspecialchars($row['Course_Code']); ?></td>
                                <td><?php echo htmlspecialchars($row['Topic']); ?></td>
                                <td><?php echo htmlspecialchars($row['Download_Date']); ?></td>
                                <td>
                                    <a class="link-view" href="resource.php?id=<?php echo $row['ResourceID']; ?>">Open</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">No downloaded resources yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

    </div>
</main>

</body>
</html>
