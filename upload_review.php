<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // FIXED: get review_type from form instead of using a constant
    $typeRaw = $_POST['review_type'] ?? 'faculty';
    $type = ($typeRaw === 'course') ? 'course' : 'faculty';

    $name   = trim($_POST['target_name'] ?? '');
    $code   = trim($_POST['course_code'] ?? '');
    $rating = (int)($_POST['rating_score'] ?? 0);
    $text   = trim($_POST['review_text'] ?? '');

    $stmt = $conn->prepare("
        INSERT INTO pending_reviews
            (review_type, target_name, course_code, rating_score, review_text, submitted_by)
        VALUES (?,?,?,?,?,?)
    ");
    $stmt->bind_param('sssisi', $type, $name, $code, $rating, $text, $userId);
    $stmt->execute();
    $stmt->close();

    header('Location: review.php?submitted=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSEHub | Upload Review</title>
    <link rel="icon" type="image/png" href="csehub-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body{
        background: radial-gradient(circle at top left, #f2eaff 0, #f8f7fb 40%, #f8f7fb 100%);
        font-family: system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
        scroll-behavior:smooth;
    }

    .page-wrapper{
        max-width:900px;
        margin:32px auto 48px;
        padding:0 24px;
        animation: fadeUp 0.45s ease-out;
    }

    .upload-card{
        background:#ffffff;
        border-radius:22px;
        padding:24px 24px 28px;
        box-shadow:0 18px 60px rgba(15,23,42,0.08);
        border:1px solid rgba(148,163,184,0.18);
        backdrop-filter: blur(8px);
        transition:transform 0.25s ease, box-shadow 0.25s ease, border-color 0.2s ease;
    }
    .upload-card:hover{
        transform:translateY(-2px);
        box-shadow:0 22px 70px rgba(15,23,42,0.12);
        border-color:#a855f7;
    }

    h4{
        font-weight:600;
        letter-spacing:.01em;
    }

    .form-label{
        font-size:13px;
        font-weight:600;
        color:#4b5563;
    }

    .form-pill{
        border-radius:999px;
    }

    select.form-select,
    input.form-control,
    textarea.form-control{
        border:1px solid #e2e8f0;
        box-shadow:0 1px 2px rgba(15,23,42,0.03);
        transition:border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease, background-color 0.15s ease;
    }
    select.form-select:focus,
    input.form-control:focus,
    textarea.form-control:focus{
        outline:none;
        border-color:#8b5cf6;
        box-shadow:0 0 0 3px rgba(139,92,246,0.2);
        transform:translateY(-1px);
        background-color:#f9fafb;
    }

    textarea.form-control{
        border-radius:18px;
        resize:vertical;
        min-height:140px;
    }

    .form-text{
        font-size:11px;
        color:#6b7280;
    }

    .btn-primary{
        background:linear-gradient(135deg,#6366f1,#a855f7);
        border:none;
        border-radius:999px;
        padding-inline:22px;
        box-shadow:0 10px 30px rgba(88,80,236,0.35);
        transition:transform 0.2s ease, box-shadow 0.2s ease, filter 0.15s ease;
    }
    .btn-primary:hover{
        transform:translateY(-1px);
        box-shadow:0 16px 40px rgba(88,80,236,0.45);
        filter:brightness(1.04);
    }
    .btn-primary:active{
        transform:translateY(0);
        box-shadow:0 6px 20px rgba(88,80,236,0.35);
    }

    .btn-link{
        color:#6b7280;
        font-size:13px;
        text-decoration:none;
        transition:color 0.15s ease;
    }
    .btn-link:hover{
        color:#4f46e5;
        text-decoration:underline;
    }

    /* subtle top badge animation */
    .upload-card > .d-flex{
        animation: fadeUp 0.5s ease-out;
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

    @media (max-width: 768px){
        .page-wrapper{
            padding:0 16px;
        }
        .upload-card{
            padding:20px 18px 24px;
            border-radius:18px;
        }
    }
</style>

</head>
<body>

<?php include 'header.php'; ?>

<main class="page-wrapper">
    <div class="upload-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Upload a Review</h4>
            <a href="review.php" class="btn btn-link small">Back to reviews</a>
        </div>
        <p class="text-muted small mb-4">
            Submit a faculty or course review for <strong>admin</strong> approval. Your review will appear on the reviews page after it is approved.
        </p>

        <!-- your original form, only requirement: the select has name="review_type" -->
        <form method="post" action="upload_review.php">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Review type</label>
                    <select name="review_type" class="form-select form-pill" required>
                        <option value="faculty">Faculty</option>
                        <option value="course">Course</option>
                    </select>
                    <div class="form-text">
                        Choose whether this review is for a specific faculty or a course.
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rating (1–5)</label>
                    <input type="number" name="rating_score" class="form-control form-pill" min="1" max="5" required>
                    <div class="form-text">5 is excellent, 1 is very poor.</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Faculty / Course name</label>
                    <input type="text" name="target_name" class="form-control form-pill" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Course code (for course reviews)</label>
                    <input type="text" name="course_code" class="form-control form-pill" placeholder="e.g. CSE2201">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Review text</label>
                <textarea name="review_text" rows="5" class="form-control"
                          placeholder="Share your experience, teaching style, difficulty level, etc."
                          required></textarea>
            </div>

            <button type="submit" class="btn btn-primary px-4" style="border-radius:999px;">
                Submit for admin approval
            </button>
        </form>
    </div>
</main>

</body>
</html>
