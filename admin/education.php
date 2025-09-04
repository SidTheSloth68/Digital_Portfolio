<?php
// education.php
require_once 'session.php';
require_once 'config.php';
require_login();

$message = '';
$edit_education = null;

// Check if we're editing an education entry
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM education WHERE id = ?');
    $stmt->execute([$edit_id]);
    $edit_education = $stmt->fetch();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $institution = $_POST['institution'] ?? '';
                $degree = $_POST['degree'] ?? '';
                $year = $_POST['year'] ?? '';
                $description = $_POST['description'] ?? '';
                
                $stmt = $pdo->prepare('INSERT INTO education (institution, degree, year, description) VALUES (?, ?, ?, ?)');
                $stmt->execute([$institution, $degree, $year, $description]);
                $message = 'Education entry added successfully!';
                break;
                
            case 'edit':
                $id = $_POST['id'] ?? 0;
                $institution = $_POST['institution'] ?? '';
                $degree = $_POST['degree'] ?? '';
                $year = $_POST['year'] ?? '';
                $description = $_POST['description'] ?? '';
                
                $stmt = $pdo->prepare('UPDATE education SET institution = ?, degree = ?, year = ?, description = ? WHERE id = ?');
                $stmt->execute([$institution, $degree, $year, $description, $id]);
                $message = 'Education entry updated successfully!';
                
                // Redirect to clear the edit parameter
                header('Location: education.php');
                exit;
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                $stmt = $pdo->prepare('DELETE FROM education WHERE id = ?');
                $stmt->execute([$id]);
                $message = 'Education entry deleted successfully!';
                break;
        }
    }
}

// Fetch all education entries
$education = $pdo->query('SELECT * FROM education ORDER BY year DESC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Education - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Education</h1>
            <p>Update your educational background and professional experience</p>
            <div class="breadcrumb">
                <a href="dashboard.php">Dashboard</a>
                <span>→</span>
                <span>Education</span>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <div class="admin-card">
            <div class="card-header">
                <h2><?= $edit_education ? 'Edit Education Entry' : 'Add New Education Entry' ?></h2>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="<?= $edit_education ? 'edit' : 'add' ?>">
                <?php if ($edit_education): ?>
                    <input type="hidden" name="id" value="<?= $edit_education['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label class="form-label">Institution</label>
                    <input type="text" name="institution" class="form-input" value="<?= $edit_education ? htmlspecialchars($edit_education['institution']) : '' ?>" placeholder="University/School name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Degree/Course</label>
                    <input type="text" name="degree" class="form-input" value="<?= $edit_education ? htmlspecialchars($edit_education['degree']) : '' ?>" placeholder="Bachelor of Science, Master of Arts, etc." required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Year</label>
                    <input type="text" name="year" class="form-input" value="<?= $edit_education ? htmlspecialchars($edit_education['year']) : '' ?>" placeholder="2020-2024 or 2022" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Additional details, achievements, or coursework..."><?= $edit_education ? htmlspecialchars($edit_education['description']) : '' ?></textarea>
                </div>
                
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <?= $edit_education ? '✏️ Update Education' : '➕ Add Education Entry' ?>
                    </button>
                    <?php if ($edit_education): ?>
                        <a href="education.php" class="btn btn-secondary">❌ Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="admin-card">
            <div class="card-header">
                <h2>Education History</h2>
            </div>
            
            <?php if (empty($education)): ?>
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3>No education entries found</h3>
                    <p>Add your educational background above to get started.</p>
                </div>
            <?php else: ?>
                <div class="item-list">
                    <?php foreach ($education as $edu): ?>
                        <div class="item">
                            <div class="item-header">
                                <div>
                                    <h3 class="item-title"><?= htmlspecialchars($edu['degree']) ?></h3>
                                    <div class="item-meta">
                                        <?= htmlspecialchars($edu['institution']) ?> • <?= htmlspecialchars($edu['year']) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($edu['description']): ?>
                                <p class="item-description"><?= htmlspecialchars($edu['description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="item-actions">
                                <a href="education.php?edit=<?= $edu['id'] ?>" class="btn btn-success">✏️ Edit</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this education entry?')">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
