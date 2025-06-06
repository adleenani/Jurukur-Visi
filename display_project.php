<!-- USER DISPLAY PROJECT PAGE -->

<?php
require_once 'config.php';

// Fetch all projects
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY project_start DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching projects: " . $e->getMessage();
    error_log("Project list error: " . $e->getMessage());
    $projects = [];
}

include 'templates/header_display_project.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Project List | Jurukur Visi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/display_project.css">
</head>

<body>

    <!-- Main content -->
    <div class="w3-main" style="margin-top: 10px; padding: 20px;">
        <div class="container">
            <h2 class="mb-4" style="font-weight: bold;">Project List</h2>

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

                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card project-card h-100 text-dark"
                                style="background-color: #e8f5e9; border: none; ">
                                <div class="card-body d-flex flex-column position-relative">
                                    <span
                                        class="badge status-badge bg-<?php echo $project['project_status'] === 'completed' ? 'secondary' : 'success'; ?>">
                                        <i class="fas fa-circle me-1"></i>
                                        <?php echo ucfirst($project['project_status']); ?>
                                    </span>

                                    <h5 class="card-title mt-4 fw-bold text-success" style="padding-top: 20px">
                                        <i class="fas fa-folder-open me-2 text-muted"></i>
                                        <?php echo htmlspecialchars($project['project_name']); ?>
                                    </h5>

                                    <h6 class="card-subtitle mb-3 text-muted">
                                        <i class="fas fa-hashtag me-2"></i>
                                        <?php echo htmlspecialchars($project['project_id']); ?>
                                    </h6>

                                    <p class="mb-2">
                                        <i class="fas fa-map-marker-alt me-2 text-success"></i>
                                        <?php echo htmlspecialchars($project['project_location']); ?>
                                    </p>

                                    <p class="mb-2">
                                        <i class="fas fa-calendar-alt me-2 text-success"></i>
                                        <?php echo date('M j, Y', strtotime($project['project_start'])); ?> —
                                        <?php echo date('M j, Y', strtotime($project['project_end'])); ?>
                                    </p>

                                    <p class="mb-2">
                                        <i class="fas fa-clock me-2 text-success"></i>
                                        <?php echo htmlspecialchars($project['project_duration']); ?>
                                    </p>

                                    <p class="mb-0">
                                        <i class="fas fa-tools me-2 text-success"></i>
                                        <?php echo htmlspecialchars($project['project_services']); ?>
                                    </p>
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