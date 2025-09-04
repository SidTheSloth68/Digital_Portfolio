<?php
// dashboard.php
require_once 'session.php';
require_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Digital Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <p>Manage your portfolio content and settings</p>
            <div class="breadcrumb">
                <span>Dashboard</span>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <a href="projects.php" class="dashboard-card">
                <div class="dashboard-icon">🚀</div>
                <h3>Manage Projects</h3>
                <p>Add, edit, and organize your portfolio projects with images and descriptions</p>
            </a>
            
            <a href="education.php" class="dashboard-card">
                <div class="dashboard-icon">🎓</div>
                <h3>Manage Education</h3>
                <p>Update your educational background and professional experience</p>
            </a>
            
            <a href="change_password.php" class="dashboard-card">
                <div class="dashboard-icon">🔒</div>
                <h3>Change Password</h3>
                <p>Update your admin account password for security</p>
            </a>
            
            <a href="../index.php" target="_blank" class="dashboard-card">
                <div class="dashboard-icon">🌐</div>
                <h3>View Website</h3>
                <p>Preview your portfolio website as visitors see it</p>
            </a>
            
            <a href="logout.php" class="dashboard-card" style="border-color: var(--danger-color);">
                <div class="dashboard-icon" style="background: var(--danger-color);">🚪</div>
                <h3>Logout</h3>
                <p>Sign out of the admin panel securely</p>
            </a>
        </div>
    </div>
</body>
</html>
