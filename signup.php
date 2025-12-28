<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // 1) basic validation
    if ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // 2) check if email already exists
        $check = $conn->prepare("SELECT 1 FROM user WHERE Email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'An account with this email already exists.';
        } else {
            // 3) insert new user (using password_hash)
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO user (Name, Email, Password, join_date, Role)
                VALUES (?, ?, ?, NOW(), 'student')
            ");
            $stmt->bind_param("sss", $name, $email, $hash);

            if ($stmt->execute()) {
                $success = 'Account created. You can now log in.';
                // optional: redirect straight to login:
                // header('Location: login.php'); exit;
            } else {
                $error = 'Something went wrong while creating your account.';
            }
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up – CSE_Study_Resource</title>
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
        .signup-wrapper {
            width: 100%;
            max-width: 980px;
            padding: 22px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.05fr);
            gap: 40px;
            background: #f9fafb;
            border-radius: 26px;
            box-shadow: 0 20px 50px rgba(15,23,42,0.14);
        }

        /* Left side: friendly copy */
        .signup-intro {
            padding: 26px 22px;
            border-radius: 20px;
            background: radial-gradient(circle at top left, #e0f2fe, #ede9fe 40%, #fee2e2 80%);
            position: relative;
            overflow: hidden;
        }
        .signup-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 999px;
            background: rgba(15,23,42,0.06);
            color: #111827;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 10px;
        }
        .signup-intro h1 {
            margin: 0 0 10px;
            font-size: 27px;
            color: #111827;
        }
        .signup-intro p {
            margin: 0;
            color: #374151;
            line-height: 1.7;
            font-size: 14px;
            max-width: 360px;
        }
        .signup-stats {
            margin-top: 22px;
            padding: 10px 12px;
            border-radius: 16px;
            background: rgba(249,250,251,0.82);
            font-size: 12px;
            color: #4b5563;
        }
        .signup-stats strong {
            color: #4f46e5;
        }

        /* Right side: form card */
        .signup-card {
            padding: 30px 28px 26px;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 14px 36px rgba(15,23,42,0.12);
        }
        .signup-card h2 {
            margin: 0 0 4px;
            font-size: 23px;
            color: #111827;
        }
        .signup-sub {
            margin: 0 0 18px;
            font-size: 14px;
            color: #6b7280;
        }
        .field-group {
            margin-bottom: 14px;
        }
        .field-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 4px;
        }
        .field-group input {
            width: 100%;
            padding: 10px 11px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
            outline: none;
            background: #f9fafb;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .field-group input:focus {
            border-color: #8b5cf6;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(139,92,246,0.25);
        }

        .signup-row {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 10px;
        }

        .signup-btn {
            width: 100%;
            margin-top: 6px;
            padding: 11px 16px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 14px 34px rgba(79,70,229,0.45);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .signup-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 40px rgba(79,70,229,0.55);
        }

        .signup-footer {
            margin-top: 14px;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
        }
        .signup-footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }
        .signup-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .signup-wrapper {
                grid-template-columns: 1fr;
                max-width: 540px;
            }
            .signup-intro {
                order: -1;
                margin-bottom: 4px;
            }
        }
        @media (max-width: 600px) {
            body { padding: 16px; }
            .signup-wrapper { padding: 16px; }
            .signup-card { padding: 24px 18px 20px; }
        }
    </style>
</head>
<body>

<div class="signup-wrapper">
    <div class="signup-intro">
        <div class="signup-pill">
            <span>New here?</span>
        </div>
        <h1>Create your CSE study space</h1>
        <p>
            Save notes, upload resources and track your progress across all of your CSE courses
            with one simple dashboard.
        </p>

        <div class="signup-stats">
            <strong>Tip:</strong> Use your university email so classmates and faculty can easily
            share materials with you.
        </div>
    </div>

    <div class="signup-card">
        <h2>Sign up</h2>
        <p class="signup-sub">Start organizing your CSE study resources in seconds.</p>

        <?php if (!empty($error)): ?>
            <p style="color:#dc2626; font-size:13px; margin-bottom:10px;">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php elseif (!empty($success)): ?>
            <p style="color:#059669; font-size:13px; margin-bottom:10px;">
                <?php echo htmlspecialchars($success); ?>
            </p>
        <?php endif; ?>
        

        <form method="post" action="signup.php">
            <div class="field-group">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="field-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="signup-row">
                <div class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="field-group">
                    <label for="confirm">Confirm</label>
                    <input type="password" id="confirm" name="confirm_password" required>
                </div>
            </div>

            <button type="submit" class="signup-btn">Create account</button>
        </form>

        <div class="signup-footer">
            Already have an account?
            <a href="login.php">Log in</a>
        </div>
    </div>
</div>

</body>
</html>
