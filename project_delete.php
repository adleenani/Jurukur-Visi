<!-- ADMIN PROJECT DELETE -->

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';
require_once 'functions.php';
requireAdmin();

// Check if required parameters exist
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    redirect('project_list.php');
    exit;
}

if (!isset($_POST['project_id'])) {
    $_SESSION['error'] = "Project ID not provided";
    redirect('project_list.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token'])) {
    $_SESSION['error'] = "Security token missing";
    redirect('project_list.php');
    exit;
}

// Then verify it
if (!verify_csrf_token($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid security token";
    redirect('project_list.php');
    exit;
}

$project_id = sanitizeInput($_POST['project_id']);

try {
    // First check if project exists
    $checkStmt = $pdo->prepare("SELECT project_id FROM projects WHERE project_id = ?");
    $checkStmt->execute([$project_id]);

    if ($checkStmt->rowCount() === 0) {
        $_SESSION['error'] = "Project not found";
        redirect('project_list.php');
        exit;
    }

    // Delete the project
    $stmt = $pdo->prepare("DELETE FROM projects WHERE project_id = ?");
    $stmt->execute([$project_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Project deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete project";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting project: " . $e->getMessage();
    error_log("Delete error: " . $e->getMessage());
}

redirect('project_list.php');
?>