<?php
/**
 * ZAMAHI Admin - Login
 */
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid session. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users_admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — ZAMAHI</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #080808;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Subtle background pattern */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(212,175,55,0.06) 0%, transparent 60%);
            pointer-events: none;
        }
        .login-container { width: 100%; max-width: 440px; padding: 24px; position: relative; z-index: 1; }
        .login-card {
            background: #1a1a1a;
            border: 1px solid rgba(212,175,55,0.12);
            border-radius: 20px;
            padding: 52px 44px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #D4AF37, #E8D48B, #D4AF37, transparent);
        }
        .login-logo { margin-bottom: 36px; }
        .login-logo .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem; font-weight: 700;
            color: #D4AF37; letter-spacing: 7px; display: block;
        }
        .login-logo .logo-sub {
            font-size: 0.58rem; letter-spacing: 4px; color: #E8D48B;
            text-transform: uppercase; opacity: 0.6; margin-top: 4px;
        }
        h2 {
            font-size: 1rem; font-weight: 400; color: rgba(255,255,255,0.5);
            margin-bottom: 36px; line-height: 1.5;
        }
        .form-group { margin-bottom: 22px; text-align: left; }
        .form-group label {
            display: block; font-size: 0.8rem; font-weight: 500;
            color: rgba(255,255,255,0.6); margin-bottom: 8px; letter-spacing: 0.5px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: rgba(255,255,255,0.25); font-size: 0.9rem; transition: color 0.3s;
        }
        .form-control {
            width: 100%; padding: 14px 16px 14px 44px;
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.08); border-radius: 10px;
            color: #fff; font-family: 'Poppins', sans-serif; font-size: 0.95rem;
            outline: none; transition: all 0.35s;
        }
        .form-control:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 0 4px rgba(212,175,55,0.1);
        }
        .form-control:focus + i, .input-wrap:focus-within i { color: #D4AF37; }
        .btn-login {
            width: 100%; padding: 14px;
            background: #D4AF37; color: #000;
            border: 2px solid #D4AF37;
            border-radius: 100px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.88rem; font-weight: 600; letter-spacing: 1.5px;
            text-transform: uppercase; cursor: pointer;
            transition: all 0.35s; margin-top: 12px;
        }
        .btn-login:hover {
            background: transparent; color: #D4AF37;
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(212,175,55,0.25);
        }
        .error-msg {
            background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.2);
            color: #e74c3c; padding: 12px 16px; border-radius: 10px;
            font-size: 0.85rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
        }
        .back-link {
            display: inline-block; margin-top: 28px; color: rgba(255,255,255,0.3);
            font-size: 0.82rem; text-decoration: none; transition: color 0.3s;
        }
        .back-link:hover { color: #D4AF37; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <span class="logo-text">ZAMAHI</span>
                <span class="logo-sub">ADMIN PANEL</span>
            </div>
            <h2>Sign in to manage your catering platform</h2>

            <?php if ($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrap">
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <button type="submit" class="btn-login"><i class="fas fa-arrow-right" style="margin-right:8px;"></i>Sign In</button>
            </form>
            <a href="<?= SITE_URL ?>" class="back-link"><i class="fas fa-arrow-left" style="margin-right:6px;"></i>Back to website</a>
        </div>
    </div>
</body>
</html>
