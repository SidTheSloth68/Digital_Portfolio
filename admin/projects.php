<?php
// projects.php
require_once 'session.php';
require_once 'config.php';
require_login();

$message = '';
$edit_project = null;

// Check if we're editing a project
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$edit_id]);
    $edit_project = $stmt->fetch();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $project_url = $_POST['project_url'] ?? '';
                $image_url = '';
                
                // Handle file upload
                if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../assets/uploads/';
                    $file_name = $_FILES['project_image']['name'];
                    $file_tmp = $_FILES['project_image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Allowed file extensions
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($file_ext, $allowed_extensions)) {
                        // Generate unique filename
                        $new_filename = uniqid('project_') . '.' . $file_ext;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            $image_url = 'assets/uploads/' . $new_filename;
                        } else {
                            $message = 'Error uploading image.';
                            break;
                        }
                    } else {
                        $message = 'Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.';
                        break;
                    }
                }
                
                $stmt = $pdo->prepare('INSERT INTO projects (title, description, image_url, project_url) VALUES (?, ?, ?, ?)');
                $stmt->execute([$title, $description, $image_url, $project_url]);
                $message = 'Project added successfully!';
                break;
                
            case 'edit':
                $id = $_POST['id'] ?? 0;
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $project_url = $_POST['project_url'] ?? '';
                
                // Get current project data
                $stmt = $pdo->prepare('SELECT image_url FROM projects WHERE id = ?');
                $stmt->execute([$id]);
                $current_project = $stmt->fetch();
                $image_url = $current_project['image_url'] ?? '';
                
                // Handle new image upload
                if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../assets/uploads/';
                    $file_name = $_FILES['project_image']['name'];
                    $file_tmp = $_FILES['project_image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Allowed file extensions
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($file_ext, $allowed_extensions)) {
                        // Generate unique filename
                        $new_filename = uniqid('project_') . '.' . $file_ext;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            // Delete old image if it exists
                            if ($image_url && file_exists('../' . $image_url)) {
                                unlink('../' . $image_url);
                            }
                            $image_url = 'assets/uploads/' . $new_filename;
                        } else {
                            $message = 'Error uploading new image.';
                            break;
                        }
                    } else {
                        $message = 'Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.';
                        break;
                    }
                }
                
                $stmt = $pdo->prepare('UPDATE projects SET title = ?, description = ?, image_url = ?, project_url = ? WHERE id = ?');
                $stmt->execute([$title, $description, $image_url, $project_url, $id]);
                $message = 'Project updated successfully!';
                
                // Redirect to clear the edit parameter
                header('Location: projects.php');
                exit;
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                
                // Get project details to delete associated image
                $stmt = $pdo->prepare('SELECT image_url FROM projects WHERE id = ?');
                $stmt->execute([$id]);
                $project = $stmt->fetch();
                
                if ($project && $project['image_url'] && file_exists('../' . $project['image_url'])) {
                    unlink('../' . $project['image_url']); // Delete the image file
                }
                
                $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
                $stmt->execute([$id]);
                $message = 'Project deleted successfully!';
                break;
        }
    }
}

// Fetch all projects
$projects = $pdo->query('SELECT * FROM projects ORDER BY id DESC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Projects</h1>
            <p>Add, edit, and organize your portfolio projects</p>
            <div class="breadcrumb">
                <a href="dashboard.php">Dashboard</a>
                <span>→</span>
                <span>Projects</span>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <div class="admin-card">
            <div class="card-header">
                <h2><?= $edit_project ? 'Edit Project' : 'Add New Project' ?></h2>
            </div>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_project ? 'edit' : 'add' ?>">
                <?php if ($edit_project): ?>
                    <input type="hidden" name="id" value="<?= $edit_project['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label class="form-label">Project Title</label>
                    <input type="text" name="title" class="form-input" value="<?= $edit_project ? htmlspecialchars($edit_project['title']) : '' ?>" placeholder="Enter project title" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Describe your project..." required><?= $edit_project ? htmlspecialchars($edit_project['description']) : '' ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Project Image</label>
                    <?php if ($edit_project && $edit_project['image_url']): ?>
                        <div class="item-image">
                            <strong>Current Image:</strong>
                            <img src="../<?= htmlspecialchars($edit_project['image_url']) ?>" alt="Current Project Image">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="project_image" class="form-file" accept="image/*">
                    <div class="form-hint">
                        <?= $edit_project ? 'Leave empty to keep current image. ' : '' ?>Supported formats: JPG, JPEG, PNG, GIF, WEBP
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Project URL</label>
                    <input type="url" name="project_url" class="form-input" value="<?= $edit_project ? htmlspecialchars($edit_project['project_url']) : '' ?>" placeholder="https://example.com">
                </div>
                
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <?= $edit_project ? '✏️ Update Project' : '➕ Add Project' ?>
                    </button>
                    <?php if ($edit_project): ?>
                        <a href="projects.php" class="btn btn-secondary">❌ Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="admin-card">
            <div class="card-header">
                <h2>Existing Projects</h2>
            </div>
            
            <?php if (empty($projects)): ?>
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3>No projects found</h3>
                    <p>Start by adding your first project above.</p>
                </div>
            <?php else: ?>
                <div class="item-list">
                    <?php foreach ($projects as $project): ?>
                        <div class="item">
                            <div class="item-header">
                                <div>
                                    <h3 class="item-title"><?= htmlspecialchars($project['title']) ?></h3>
                                    <div class="item-meta">
                                        Created: <?= date('M j, Y', strtotime($project['created_at'] ?? 'now')) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="item-description"><?= htmlspecialchars($project['description']) ?></p>
                            
                            <?php if ($project['image_url']): ?>
                                <div class="item-image">
                                    <img src="../<?= htmlspecialchars($project['image_url']) ?>" alt="Project Image">
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($project['project_url']): ?>
                                <div class="item-meta">
                                    <strong>URL:</strong> <a href="<?= htmlspecialchars($project['project_url']) ?>" target="_blank"><?= htmlspecialchars($project['project_url']) ?></a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="item-actions">
                                <a href="projects.php?edit=<?= $project['id'] ?>" class="btn btn-success">✏️ Edit</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $project['id'] ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure? This will also delete the associated image.')">🗑️ Delete</button>
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
