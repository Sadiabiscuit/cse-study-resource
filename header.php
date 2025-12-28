<?php
require_once 'config.php'; // make sure the session is started
?>
<header class="site-header">
    <div class="header-inner">
        <div class="site-brand">
            <a href="index.php" class="brand-link">CSEHub💻</a>
        </div>

        <nav class="main-nav">
            <a href="resource.php">Resources</a>
            <a href="review.php">Reviews</a>
            <a href="dashboard.php">Dashboard</a>

            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <a href="admin_approval.php">Admin Approval</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>

    </div>
</header>


<style>
    .site-header {
        background-color: #dad3f5;
        padding: 8px 0;
    }

    /* full‑width bar */
    .header-inner {
        width: 100%;
        margin: 0;
        padding: 0 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-sizing: border-box;
    }

    .site-brand .brand-link {
        font-family: "Poppins", sans-serif;
        font-size: 30px;
        font-weight: 700;
        color: #251c5a;
        text-decoration: none;
        margin-left: 20px; /* move logo a little right */
    }

    /* animated tab‑style nav links */
    .main-nav a {
        position: relative;
        margin-left: 22px;
        font-family: "Poppins", sans-serif;
        font-size: 15px;
        color: #251c5a;
        text-decoration: none;
        padding: 6px 10px;
        border-radius: 999px;
        transition:
            background-color 0.25s ease,
            color 0.25s ease,
            transform 0.2s ease;
    }

    .main-nav a::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 0;
        height: 2px;
        background-color: #4b3aa8;
        border-radius: 999px;
        transform: translateX(-50%);
        transition: width 0.25s ease;
    }

    .main-nav a:hover {
        background-color: #eee5ff;
        color: #4b3aa8;
        transform: translateY(-1px);
    }

    .main-nav a:hover::after {
        width: 70%;
    }

    /* active tab (current page) */
    .main-nav a.active-tab {
        background-color: #4b3aa8;
        color: #ffffff;
    }

    .main-nav a.active-tab::after {
        width: 70%;
    }

    @media (max-width: 640px) {
        .header-inner {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .main-nav a {
            margin-left: 0;
            margin-right: 12px;
        }
    }
</style>
