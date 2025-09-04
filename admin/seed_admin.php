<?php
// seed_admin.php
require_once 'config.php';

try {
    // Create database if it doesn't exist
    $temp_pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
    $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS digital_portfolio");
    
    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image_url VARCHAR(255),
            project_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS education (
            id INT AUTO_INCREMENT PRIMARY KEY,
            institution VARCHAR(255) NOT NULL,
            degree VARCHAR(255) NOT NULL,
            year VARCHAR(20) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create default admin user
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Check if admin already exists
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
        $stmt->execute([$username, $password]);
        echo "Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Admin user already exists!<br>";
    }
    
    echo "<a href='login.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
