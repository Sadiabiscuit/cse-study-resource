<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ------------ APPROVE PENDING REVIEW ------------ */
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];

    // 1) Fetch pending row
    $stmt = $conn->prepare("SELECT * FROM pending_reviews WHERE PendingID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $pendingRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($pendingRow) {
        // 2) Insert into main review table, including review_type + course info
        $insert = $conn->prepare("
            INSERT INTO review (
                review_type,
                target_name,
                course_code,
                Review_Text,
                Rating_Score,
                Review_Date,
                Reviewer_ID
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->bind_param(
            "ssssisi",
            $pendingRow['review_type'],
            $pendingRow['target_name'],
            $pendingRow['course_code'],
            $pendingRow['review_text'],
            $pendingRow['rating_score'],
            $pendingRow['submitted_at'],   // or date('Y-m-d H:i:s')
            $pendingRow['submitted_by']
        );
        $insert->execute();
        $newReviewId = $insert->insert_id;
        $insert->close();

        // 2b) Update faculty_course_evaluation mapping if you use it
        $update = $conn->prepare("
            UPDATE faculty_course_evaluation
            SET Review_ID = ?
            WHERE Review_ID = ?
        ");
        $update->bind_param("ii", $newReviewId, $pendingRow['PendingID']);
        $update->execute();
        $update->close();

        // 3) Delete from pending_reviews
        $del = $conn->prepare("DELETE FROM pending_reviews WHERE PendingID = ?");
        $del->bind_param("i", $id);
        $del->execute();
        $del->close();
    }

    header("Location: admin_approval.php");
    exit;
}

/* ------------ REJECT PENDING REVIEW ------------ */
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $del = $conn->prepare("DELETE FROM pending_reviews WHERE PendingID = ?");
    $del->bind_param("i", $id);
    $del->execute();
    $del->close();

    header("Location: admin_approval.php");
    exit;
}

/* ------------ LOAD PENDING LIST ------------ */
$pending = $conn->query("SELECT * FROM pending_reviews ORDER BY submitted_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSEHub | Admin Approval</title>
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

        .reviews-card{
            background:#ffffff;
            border-radius:22px;
            padding:22px 22px 26px;
            box-shadow:0 18px 60px rgba(15,23,42,0.08);
            border:1px solid rgba(148,163,184,0.18);
            backdrop-filter: blur(8px);
            transition:transform 0.25s ease, box-shadow 0.25s ease, border-color 0.2s ease, background 0.2s ease;
            position:relative;
            overflow:hidden;
        }
        .reviews-card::before{
            content:"";
            position:absolute;
            inset:0;
            opacity:0;
            background:radial-gradient(circle at top left, rgba(129,140,248,0.18), transparent 60%);
            transition:opacity 0.25s ease;
            pointer-events:none;
        }
        .reviews-card:hover{
            transform:translateY(-2px);
            box-shadow:0 22px 70px rgba(15,23,42,0.12);
            border-color:#a855f7;
            background:linear-gradient(135deg,#ffffff 0,#eef2ff 100%);
        }
        .reviews-card:hover::before{
            opacity:1;
        }

        h4{
            font-weight:600;
            letter-spacing:.01em;
        }

        .badge{
            border-radius:999px;
            font-size:11px;
            padding-inline:12px;
            background:#eef2ff;
            color:#4f46e5;
        }

        .pending-item{
            border-radius:14px;
            background:linear-gradient(135deg,#f9fafb 0,#eef2ff 100%);
            border:1px solid rgba(148,163,184,0.3);
            padding:12px 16px;
            margin-bottom:10px;
            box-shadow:0 8px 30px rgba(15,23,42,0.08);
            display:flex;
            flex-direction:column;
            gap:6px;
            transition:transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
            position:relative;
            overflow:hidden;
            animation: reviewIn 0.35s ease-out;
        }
        .pending-item:hover{
            transform:translateY(-2px);
            box-shadow:0 14px 40px rgba(15,23,42,0.14);
            border-color:#a855f7;
            background:linear-gradient(135deg,#ffffff 0,#eef2ff 100%);
        }

        .pending-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:8px;
        }

        .pending-meta{
            font-size:11px;
            color:#6b7280;
        }

        .rating-chip{
            font-size:12px;
            color:#4b5563;
            font-weight:600;
            padding:4px 10px;
            border-radius:999px;
            background:rgba(251, 191, 36, 0.12);
            border:1px solid rgba(251, 191, 36, 0.5);
        }

        .btn-success{
            border-radius:999px;
            padding-inline:14px;
            font-size:12px;
        }
        .btn-outline-danger{
            border-radius:999px;
            padding-inline:14px;
            font-size:12px;
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
        @keyframes reviewIn{
            from{
                opacity:0;
                transform:translateY(6px);
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
            .reviews-card{
                border-radius:18px;
                padding:18px 16px 22px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main class="page-wrapper">
    <div class="reviews-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Pending Reviews</h4>
            <span class="badge">
                Admin panel · Approval queue
            </span>
        </div>
        <p class="text-muted small">
            Approve only clear, respectful and useful reviews. Rejected reviews are removed from the queue.
        </p>

        <?php if ($pending && $pending->num_rows > 0): ?>
            <?php while ($row = $pending->fetch_assoc()): ?>
                <div class="pending-item">
                    <div class="pending-header">
                        <div>
                            <strong><?php echo htmlspecialchars(ucfirst($row['review_type'])); ?></strong>
                            <span class="text-muted">
                                · <?php echo htmlspecialchars($row['target_name']); ?>
                                <?php if (!empty($row['course_code'])): ?>
                                    (<?php echo htmlspecialchars($row['course_code']); ?>)
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="rating-chip">
                            Rating: <?php echo (int)$row['rating_score']; ?>/5
                        </div>
                    </div>

                    <div>
                        <?php echo nl2br(htmlspecialchars($row['review_text'])); ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <div class="pending-meta">
                            Submitted on <?php echo htmlspecialchars($row['submitted_at']); ?>
                            · User ID <?php echo htmlspecialchars($row['submitted_by']); ?>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="admin_approval.php?approve=<?php echo (int)$row['PendingID']; ?>"
                               class="btn btn-sm btn-success">Approve</a>
                            <a href="admin_approval.php?reject=<?php echo (int)$row['PendingID']; ?>"
                               class="btn btn-sm btn-outline-danger">Reject</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                There are no pending reviews right now. New submissions will appear here for your approval.
            </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>