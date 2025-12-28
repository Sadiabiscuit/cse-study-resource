<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// search term
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchLike = '%' . $search . '%';

// faculty reviews
$stmt = $conn->prepare("
    SELECT ReviewID, review_type, target_name, course_code, Review_Text,
           Rating_Score, Review_Date, Reviewer_ID
    FROM review
    WHERE review_type = 'faculty'
      AND (
          target_name   LIKE ?
          OR course_code LIKE ?
          OR Review_Text LIKE ?
      )
    ORDER BY Review_Date DESC
    LIMIT 20
");
$stmt->bind_param('sss', $searchLike, $searchLike, $searchLike);
$stmt->execute();
$facultyReviews = $stmt->get_result();

// course reviews
$stmt = $conn->prepare("
    SELECT ReviewID, review_type, target_name, course_code, Review_Text,
           Rating_Score, Review_Date, Reviewer_ID
    FROM review
    WHERE review_type = 'course'
      AND (
          target_name   LIKE ?
          OR course_code LIKE ?
          OR Review_Text LIKE ?
      )
    ORDER BY Review_Date DESC
    LIMIT 20
");
$stmt->bind_param('sss', $searchLike, $searchLike, $searchLike);
$stmt->execute();
$courseReviews = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSEHub | Reviews</title>

    <!-- Favicon: put your logo file (ico or png) in the same folder -->
    <link rel="icon" type="image/png" href="csehub-logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body{
        background: radial-gradient(circle at top left, #f2eaff 0, #f8f7fb 40%, #f8f7fb 100%);
        font-family: system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
        scroll-behavior:smooth;
    }

    .page-wrapper{
        max-width:1200px;
        margin:32px auto 48px;
        padding:0 24px;
        animation: fadeUp 0.5s ease-out;
    }

    .reviews-card{
        background:#ffffff;
        border-radius:22px;
        padding:22px 22px 26px;
        box-shadow:0 18px 60px rgba(15, 23, 42, 0.08);
        border:1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(8px);
        transition:transform 0.25s ease, box-shadow 0.25s ease;
    }
    .reviews-card:hover{
        transform:translateY(-2px);
        box-shadow:0 22px 70px rgba(15, 23, 42, 0.12);
    }

    .section-title{
        font-size:15px;
        font-weight:600;
        margin-top:22px;
        margin-bottom:10px;
        color:#475569;
        letter-spacing:0.02em;
        text-transform:uppercase;
    }

    .search-input{
        border-radius:999px;
        border:1px solid #e2e8f0;
        padding:11px 18px;
        margin-bottom:16px;
        box-shadow:0 1px 2px rgba(15,23,42,0.03);
        transition:border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
    }
    .search-input:focus{
        outline:none;
        border-color:#8b5cf6;
        box-shadow:0 0 0 3px rgba(139,92,246,0.2);
        transform:translateY(-1px);
    }

    .review-item{
        padding:12px 16px;
        border-radius:14px;
        background:linear-gradient(135deg, #f9fafb 0, #eef2ff 100%);
        margin-bottom:10px;
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        border:1px solid rgba(148,163,184,0.3);
        transition:transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        position:relative;
        overflow:hidden;
    }
    .review-item::before{
        content:"";
        position:absolute;
        inset:0;
        opacity:0;
        background:radial-gradient(circle at top left, rgba(139,92,246,0.14), transparent 60%);
        transition:opacity 0.25s ease;
        pointer-events:none;
    }
    .review-item:hover{
        transform:translateY(-2px);
        box-shadow:0 14px 40px rgba(15,23,42,0.18);
        border-color:#a855f7;
        background:linear-gradient(135deg, #fdfdfd 0, #eef2ff 100%);
    }
    .review-item:hover::before{
        opacity:1;
    }

    .review-meta{
        font-size:11px;
        color:#6b7280;
        margin-top:4px;
    }

    .rating-text{
        font-size:12px;
        color:#4b5563;
        font-weight:600;
        padding:4px 10px;
        border-radius:999px;
        background:rgba(251, 191, 36, 0.12);
        border:1px solid rgba(251, 191, 36, 0.5);
        align-self:flex-start;
        transform:translateY(2px);
    }

    .btn.btn-primary{
        background:linear-gradient(135deg,#6366f1,#a855f7);
        border:none;
        border-radius:999px;
        padding-inline:18px;
        box-shadow:0 10px 30px rgba(88, 80, 236, 0.3);
        transition:transform 0.2s ease, box-shadow 0.2s ease, filter 0.15s ease;
    }
    .btn.btn-primary:hover{
        transform:translateY(-1px);
        box-shadow:0 14px 40px rgba(88, 80, 236, 0.4);
        filter:brightness(1.03);
    }
    .btn.btn-primary:active{
        transform:translateY(0);
        box-shadow:0 6px 20px rgba(88, 80, 236, 0.3);
    }

    /* subtle fade-in animation for list items */
    .review-item{
        animation: reviewIn 0.35s ease-out;
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

    @media (max-width: 768px){
        .page-wrapper{
            padding:0 16px;
        }
        .reviews-card{
            border-radius:16px;
            padding:18px 16px 22px;
        }
        .review-item{
            flex-direction:column;
            gap:6px;
        }
        .rating-text{
            align-self:flex-end;
        }
    }
</style>

</head>
<body>


<?php include 'header.php'; ?>

<main class="page-wrapper">
    <div class="reviews-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Available Reviews</h4>
            <a href="upload_review.php" class="btn btn-sm btn-primary" style="border-radius:999px;">Upload a Review</a>
        </div>

        <!-- search -->
        <form method="get" action="review.php">
            <input
                type="text"
                name="q"
                class="form-control search-input"
                placeholder="Search by faculty, course or keyword"
                value="<?php echo htmlspecialchars($search); ?>"
            >
        </form>

        <!-- Faculty reviews -->
        <div class="section-title">Faculty reviews</div>

        <?php if ($facultyReviews->num_rows > 0): ?>
            <?php while ($row = $facultyReviews->fetch_assoc()): ?>
                <div class="review-item">
                    <div>
                        <div><?php echo htmlspecialchars($row['Review_Text']); ?></div>
                        <div class="review-meta">
                            Posted on <?php echo htmlspecialchars($row['Review_Date']); ?>
                            · User ID <?php echo htmlspecialchars($row['Reviewer_ID']); ?>
                        </div>
                    </div>
                    <div class="rating-text">
                        Rating: <?php echo (int)$row['Rating_Score']; ?>/5
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-muted small">No faculty reviews yet.</div>
        <?php endif; ?>

        <!-- Course reviews -->
        <div class="section-title">Course reviews</div>

        <?php if ($courseReviews->num_rows > 0): ?>
            <?php while ($row = $courseReviews->fetch_assoc()): ?>
                <div class="review-item">
                    <div>
                        <div><?php echo htmlspecialchars($row['target_name']); ?> · <?php echo htmlspecialchars($row['course_code']); ?></div>
                        <div><?php echo htmlspecialchars($row['Review_Text']); ?></div>
                        <div class="review-meta">
                            Posted on <?php echo htmlspecialchars($row['Review_Date']); ?>
                            · User ID <?php echo htmlspecialchars($row['Reviewer_ID']); ?>
                        </div>
                    </div>
                    <div class="rating-text">
                        Rating: <?php echo (int)$row['Rating_Score']; ?>/5
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-muted small">No course reviews yet.</div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>