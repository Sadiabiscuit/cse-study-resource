<?php
require_once 'config.php';   // session + DB connection

$error = '';

// Get redirect URL if passed
$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("
        SELECT User_ID, Name, Email, Password, Role
        FROM user
        WHERE Email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['Password'])) {

            // set basic session info
            $_SESSION['user_id']    = $row['User_ID'];
            $_SESSION['user_email'] = $row['Email'];
            $_SESSION['Name']       = $row['Name'];          // keep if you use it elsewhere
            $_SESSION['username']   = $row['Name'];          // NEW: used on dashboard
            $_SESSION['role']       = $row['Role'];


            // fixed admin check: only this account is admin
            if ($row['Email'] === 'nusaop@gmail.com') {
                $_SESSION['is_admin'] = 1;
            } else {
                $_SESSION['is_admin'] = 0;
            }

            // Redirect to original page
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – CSE_Study_Resource</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: #f2f0ea;
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-wrapper {
            width: 100%;
            max-width: 960px;
            padding: 20px;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 40px;
            background: #f9fafb;
            border-radius: 24px;
            box-shadow: 0 18px 45px rgba(15,23,42,0.12);
        }
        .login-hero {
            padding: 24px;
            border-radius: 20px;
            background: linear-gradient(145deg, #e8e4ff, #ffe6f3);
            position: relative;
            overflow: hidden;
        }
        .login-hero h1 {
            margin: 0 0 10px;
            font-size: 28px;
            color: #312e81;
        }
        .login-hero p {
            margin: 0;
            color: #4b5563;
            line-height: 1.6;
        }
        .login-tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .hero-robot {
            position: absolute;
            right: 12px;
            bottom: -8px;
            width: 150px;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
        .login-card {
            padding: 28px 26px 26px;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(15,23,42,0.10);
        }
        .login-card h2 {
            margin: 0 0 6px;
            font-size: 22px;
            color: #111827;
        }
        .login-sub {
            margin: 0 0 18px;
            font-size: 14px;
            color: #6b7280;
        }
        .field {
            margin-bottom: 14px;
        }
        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 4px;
        }
        .field input {
            width: 100%;
            padding: 10px 11px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
            outline: none;
            background: #f9fafb;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .field input:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(99,102,241,0.20);
        }
        .login-btn {
            width: 100%;
            margin-top: 4px;
            padding: 11px 16px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, #4b3f72, #7c5cff);
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 12px 30px rgba(31,41,55,0.35);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 36px rgba(31,41,55,0.40);
        }
        .login-footer {
            margin-top: 14px;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
        }
        .login-footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 900px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 520px;
            }
            .login-hero {
                order: -1;
                padding-bottom: 110px;
            }
            .hero-robot {
                width: 130px;
                right: 10px;
                bottom: -18px;
            }
        }
        @media (max-width: 600px) {
            body { padding: 16px; }
            .login-wrapper { padding: 16px; }
            .login-card { padding: 22px 18px 20px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-hero">
        <div class="login-tag">Welcome back</div>
        <h1>Log in to CSE_Study_Resource</h1>
        <p>
            Continue where you left off, access saved resources and track your
            progress across all your CSE courses.
        </p>
        <img src="images/dog-study-3.png" alt="Study robot" class="hero-robot">
    </div>

    <div class="login-card">
        <h2>Login</h2>
        <p class="login-sub">Use your email and password to access your dashboard.</p>

        <?php if (!empty($error)): ?>
            <p style="color:#dc2626; font-size:13px; margin-bottom:10px;">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php endif; ?>

        <form method="post" action="login.php?redirect=<?php echo urlencode($redirect); ?>">
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="login-footer">
            Don’t have an account?
            <a href="signup.php">Sign up</a>
        </div>
    </div>
</div>

</body>
</html>