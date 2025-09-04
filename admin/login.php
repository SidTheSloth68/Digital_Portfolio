<?php
// login.php
require_once 'config.php';
require_once 'session.php';

$error = '';
$remembered_username = '';

// Check for remember me cookie
if (isset($_COOKIE['remember_username'])) {
    $remembered_username = $_COOKIE['remember_username'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        
        // Handle remember me functionality
        if ($remember_me) {
            // Set cookie for 30 days
            setcookie('remember_username', $username, time() + (30 * 24 * 60 * 60), '/');
        } else {
            // Clear the cookie if remember me is not checked
            setcookie('remember_username', '', time() - 3600, '/');
        }
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Digital Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }
        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--success-color));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        .login-subtitle {
            color: var(--secondary-color);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                📊
            </div>
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to your admin dashboard</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Enter your username" value="<?= htmlspecialchars($remembered_username) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <div class="form-group" style="text-align: left; margin-bottom: 20px;">
                    <label class="checkbox-label" style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="remember_me" style="margin-right: 8px;" <?= $remembered_username ? 'checked' : '' ?>>
                        <span style="color: var(--secondary-color); font-size: 14px;">Remember me for 30 days</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>
</html>
