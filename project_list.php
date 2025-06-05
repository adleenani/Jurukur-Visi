<!-- ADMIN PROJECT LIST PAGE -->

<?php
require_once 'config.php';
require_once 'functions.php';
requireAdmin();

// Handle logout
if (isset($_GET['logout'])) {
    $_SESSION['message'] = "You have successfully logged out.";
    setcookie(session_name(), '', time() - 42000);
    session_destroy();
    redirect('home_public.php');
}

// Fetch all projects
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY project_start DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching projects: " . $e->getMessage();
    error_log("Project list error: " . $e->getMessage());
    $projects = [];
}

include 'templates/header_project_list.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Project List (Admin) | Jurukur Visi </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/project_list.css">
</head>

<body>

    <!-- Main content -->
    <div class="w3-main" style="margin-top:80px; padding:20px;">
        <div class="container">
            <h2 class="mb-4">Project List</h2>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['message'];
                unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="row">
                <?php if (empty($projects)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">No projects found. <a href="project_add.php">Add a project</a></div>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card project-card h-100">
                                <div class="card-body d-flex flex-column">
                                    <span
                                        class="badge bg-<?php echo $project['project_status'] === 'completed' ? 'secondary' : 'success'; ?> status-badge">
                                        <?php echo ucfirst($project['project_status']); ?>
                                    </span>
                                    <h5 class="card-title"><?php echo htmlspecialchars($project['project_name']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <?php echo htmlspecialchars($project['project_id']); ?>
                                    </h6>
                                    <div class="card-text mb-2">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($project['project_location']); ?>
                                    </div>
                                    <div class="card-text mb-2">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo date('M j, Y', strtotime($project['project_start'])); ?> to
                                        <?php echo date('M j, Y', strtotime($project['project_end'])); ?>
                                    </div>
                                    <div class="card-text mb-2">
                                        <i class="fas fa-clock"></i>
                                        <?php echo htmlspecialchars($project['project_duration']); ?>
                                    </div>
                                    <div class="card-text mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-tools"></i>
                                            <?php echo htmlspecialchars($project['project_services']); ?>
                                        </small>
                                    </div>
                                    <div class="mt-auto d-grid gap-2">
                                        <a href="project_edit.php?project_id=<?php echo urlencode($project['project_id']); ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> View/Edit
                                        </a>
                                        <form method="post" action="project_delete.php" class="d-inline">
                                            <input type="hidden" name="project_id"
                                                value="<?php echo htmlspecialchars($project['project_id']); ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger w-100"
                                                onclick="return confirm('Are you sure you want to delete this project?')">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle
        function w3_open() {
            document.getElementById("mySidebar").style.display = "block";
        }
        function w3_close() {
            document.getElementById("mySidebar").style.display = "none";
        }
    </script>
</body>

</html>

<!-- Footer -->
<?php include 'templates/footer.php'; ?>