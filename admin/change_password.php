<?php
// change_password.php
require_once 'session.php';
require_once 'config.php';
require_login();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } else {
        // Verify old password
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($old_password, $admin['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE admins SET password = ? WHERE id = ?');
            $stmt->execute([$new_hash, $_SESSION['admin_id']]);
            $message = 'Password changed successfully!';
        } else {
            $error = 'Old password is incorrect.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        .password-container {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Change Password</h1>
            <p>Update your admin account password for security</p>
            <div class="breadcrumb">
                <a href="dashboard.php">Dashboard</a>
                <span>→</span>
                <span>Change Password</span>
            </div>
        </div>
        
        <div class="password-container">
            <div class="admin-card">
                <div class="card-header">
                    <h2>🔒 Update Password</h2>
                </div>
                
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="old_password" class="form-input" placeholder="Enter your current password" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-input" placeholder="Enter your new password" required>
                        <div class="form-hint">Password must be at least 6 characters long</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-input" placeholder="Confirm your new password" required>
                    </div>
                    
                    <div style="display: flex; gap: 15px; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">
                            🔐 Change Password
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            ❌ Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
