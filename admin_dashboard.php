<!-- ADMIN DASHBOARD -->

<?php
require_once 'config.php';
require_once 'functions.php';
requireAdmin();

// Handle logout
if (isset($_GET['logout'])) {
    $_SESSION['success'] = "You have been successfully logged out";
    $_SESSION = [];
    setcookie(session_name(), '', time() - 42000);
    session_destroy();
    redirect('home_public.php');
}

try {
    // Get all projects with creator info
    $projects = dbQuery(
        "SELECT * FROM projects ORDER BY project_start DESC"
    )->fetchAll(PDO::FETCH_ASSOC);

    // Count projects by status
    $stats = dbQuery(
        "SELECT 
            SUM(CASE WHEN project_status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN project_status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN project_status = 'on_hold' THEN 1 ELSE 0 END) as on_hold,
            COUNT(*) as total
         FROM projects"
    )->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $_SESSION['error'] = "Error loading data: " . $e->getMessage();
    error_log("Dashboard error: " . $e->getMessage());
    $projects = [];
    $stats = ['active' => 0, 'completed' => 0, 'on_hold' => 0, 'total' => 0];
}

$page_title = "Admin Dashboard";
include 'templates/header_admin_dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/admin_dashboard.css">
</head>

<body>
    <!-- Main Content -->
    <div class="main-content" id="main-content" style="margin-bottom: 87px; margin-top: 50px">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
                <div>
                    <a href="project_add.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> New Project
                    </a>
                </div>
            </div>
            <div>
                <h4>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></C>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4 mt-4">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Projects</h5>
                            <h2 class="text-primary"><?php echo $stats['total']; ?></h2>
                            <p class="card-text">All projects in the system</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h5 class="card-title">Active Projects</h5>
                            <h2 class="text-success"><?php echo $stats['active']; ?></h2>
                            <p class="card-text">Currently ongoing projects</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h5 class="card-title">Completed</h5>
                            <h2 class="text-secondary"><?php echo $stats['completed']; ?></h2>
                            <p class="card-text">Finished projects</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5><i class="fas fa-clock"></i> Recently Added</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($projects)): ?>
                                <div class="alert alert-info">No projects found. Add your first project!</div>
                            <?php else: ?>
                                <?php foreach (array_slice($projects, 0, 5) as $project): ?>
                                    <div class="mb-3 pb-2 border-bottom">
                                        <h6><?php echo htmlspecialchars($project['project_name']); ?></h6>
                                        <small class="text-muted">
                                            Created:
                                            <?php echo htmlspecialchars(date('M j, Y', strtotime($project['created_at']))); ?> |
                                            Last Updated:
                                            <?php echo htmlspecialchars(date('M j, Y', strtotime($project['updated_at']))); ?> |
                                            <br>
                                            Created by: <?php echo 'Admin ' . htmlspecialchars($project['created_by']); ?>
                                        </small>

                                    </div>
                                <?php endforeach; ?>
                                <a href="project_list.php" class="btn btn-sm btn-outline-primary mt-2">View All</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-exclamation-circle"></i> Active Projects</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $active_projects = array_filter($projects, function ($p) {
                                return $p['project_status'] === 'active';
                            });
                            $active_projects = array_slice($active_projects, 0, 5);
                            ?>

                            <?php if (empty($active_projects)): ?>
                                <div class="alert alert-info">No active projects</div>
                            <?php else: ?>
                                <?php foreach ($active_projects as $project): ?>
                                    <div class="mb-3 pb-2 border-bottom">
                                        <h6><?php echo htmlspecialchars($project['project_name']); ?></h6>
                                        <div class="progress mt-2">
                                            <?php
                                            $start = strtotime($project['project_start']);
                                            $end = strtotime($project['project_end']);
                                            $now = time();
                                            $progress = ($now - $start) / ($end - $start) * 100;
                                            $progress = min(max($progress, 0), 100);
                                            ?>
                                            <div class="progress-bar bg-success" style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                        <small class="text-muted">
                                            Ends: <?php echo date('M j, Y', $end); ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <a href="project_list.php?status=active" class="btn btn-sm btn-outline-warning mt-2">View
                                All</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-check-circle"></i> Recently Completed</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $completed_projects = array_filter($projects, function ($p) {
                                return strtotime($p['project_end']) < time();
                            });
                            $completed_projects = array_slice($completed_projects, 0, 5);
                            ?>

                            <?php if (empty($completed_projects)): ?>
                                <div class="alert alert-info">No recently completed projects</div>
                            <?php else: ?>
                                <?php foreach ($completed_projects as $project): ?>
                                    <div class="mb-3 pb-2 border-bottom">
                                        <h6><?php echo htmlspecialchars($project['project_name']); ?></h6>
                                        <small class="text-muted">
                                            Completed: <?php echo date('M j, Y', strtotime($project['project_end'])); ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <a href="project_list.php?status=completed" class="btn btn-sm btn-outline-success mt-2">View
                                All</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'templates/footer.php'; ?>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const hamburger = document.querySelector('.hamburger');

            if (window.innerWidth <= 992 &&
                !sidebar.contains(event.target) &&
                !hamburger.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                mainContent.classList.remove('active');
            }
        });
    </script>
</body>

</html>