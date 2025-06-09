<!-- ADMIN ADD PROJECT PAGE -->

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid security token";
        redirect('project_add.php');
    }

    // Validate and sanitize inputs
    $project_id = sanitizeInput($_POST['project_id']);
    $project_name = sanitizeInput($_POST['project_name']);
    $project_start = sanitizeInput($_POST['project_start']);
    $project_end = sanitizeInput($_POST['project_end']);
    $project_location = sanitizeInput($_POST['project_location']);
    $project_services = sanitizeInput($_POST['project_services']);
    $project_status = sanitizeInput($_POST['project_status']);

    // Calculate duration based on dates
    if (!empty($project_start) && !empty($project_end)) {
        $start = new DateTime($project_start);
        $end = new DateTime($project_end);

        if ($end < $start) {
            $errors[] = "End date cannot be before start date";
        } else {
            $interval = $start->diff($end);
            $project_duration = $interval->format('%y Year(s), %m Month(s), %d Day(s)');
        }
    } else {
        $errors[] = "Both start and end dates are required";
    }

    if (empty($errors)) {
        try {
            // Convert dates to proper format
            $project_start = date('Y-m-d', strtotime($project_start));
            $project_end = date('Y-m-d', strtotime($project_end));

            // Get current user ID
            $created_by = $_SESSION['user_id'] ?? 1; // Fallback to 1 if not set

            $stmt = $pdo->prepare("INSERT INTO projects 
                (project_id, project_name, project_start, project_end, 
                 project_location, project_duration, project_services, 
                 project_status, created_by) 
                VALUES (:id, :name, :start, :end, :location, :duration, 
                        :services, :status, :creator)");

            $stmt->execute([
                ':id' => $project_id,
                ':name' => $project_name,
                ':start' => $project_start,
                ':end' => $project_end,
                ':location' => $project_location,
                ':duration' => $project_duration,
                ':services' => $project_services,
                ':status' => $project_status,
                ':creator' => $created_by
            ]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "Project successfully added!";
                redirect('project_list.php');
            } else {
                throw new Exception("No rows were inserted. Check table structure.");
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $_SESSION['error'] = "Failed to add project. Error: " . $e->getMessage();
            redirect('project_add.php');
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
        redirect('project_add.php');
    }
}

include 'templates/header_project_add.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>JURUKUR VISI - Add Project</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/projectadd.css">
</head>

<body>

    <!-- Main Form Content -->
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-project-diagram"></i> Project Registration</h2>
            <p class="text-muted">Fill in the project details below</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo sanitizeInput($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="project_add.php">
            <input type="hidden" name="csrf_token" value="<?php echo sanitizeInput(csrf_token()); ?>">

            <!-- Project Status -->
            <div class="form-group">
                <label class="form-label">Project Status</label>
                <div class="status-toggle">
                    <div class="status-option active" onclick="toggleStatus(this, 'active')">
                        <i class="fas fa-spinner"></i> Active
                    </div>
                    <div class="status-option" onclick="toggleStatus(this, 'completed')">
                        <i class="fas fa-check-circle"></i> Completed
                    </div>
                </div>
                <input type="hidden" name="project_status" value="active" id="projectStatus">
            </div>

            <!-- Project ID -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Project ID</label>
                        <input type="text" name="project_id" class="form-control" placeholder="PRJ-001" required
                            value="<?php echo isset($_POST['project_id']) ? sanitizeInput($_POST['project_id']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="project_name" class="form-control" placeholder="Project Name" required
                            value="<?php echo isset($_POST['project_name']) ? sanitizeInput($_POST['project_name']) : ''; ?>">
                    </div>
                </div>
            </div>

             <!-- Start Date -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="project_start" class="form-control" required
                            value="<?php echo isset($_POST['project_start']) ? sanitizeInput($_POST['project_start']) : ''; ?>"
                            onchange="calculateDuration()">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" name="project_end" class="form-control" required
                            value="<?php echo isset($_POST['project_end']) ? sanitizeInput($_POST['project_end']) : ''; ?>"
                            onchange="calculateDuration()">
                    </div>
                </div>
            </div>

             <!-- Project Location -->
            <div class="form-group">
                <label class="form-label">Project Location</label>
                <input type="text" name="project_location" class="form-control" placeholder="Project Location" required
                    value="<?php echo isset($_POST['project_location']) ? sanitizeInput($_POST['project_location']) : ''; ?>">
            </div>

             <!-- Project Duration -->
            <div class="form-group">
                <label class="form-label">Project Duration</label>
                <input type="text" name="project_duration" id="project_duration" class="form-control"
                    placeholder="Will be calculated automatically" readonly>
                <p class="duration-hint">Duration will be calculated based on start and end dates</p>
            </div>

             <!-- Project Services -->
            <div class="form-group">
                <label class="form-label">Project Services</label>
                <select name="project_services" class="form-control" required>
                    <option value="">-- Select a service --</option>
                    <?php
                    $options = [
                        "Consultant and Survey Services in Cadastral",
                        "Strata Title",
                        "Topographic Hydrographic",
                        "Engineering and Mapping",
                        "Mining",
                        "Aerial",
                        "M.Tech",
                        "GPS",
                        "Land & Housing Development",
                        "Underground Utilities Detection and Mapping"
                    ];
                    foreach ($options as $option) {
                        $selected = (isset($_POST['project_services']) && $_POST['project_services'] === $option) ? 'selected' : '';
                        echo "<option value=\"$option\" $selected>$option</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Register Project
            </button>

            <button type="reset" class="btn-reset" onclick="resetForm()">
                <i class="fas fa-undo"></i> Reset Form
            </button>
        </form>
    </div>

    <script>
        // Toggle between showing and hiding the sidebar when clicking the menu icon
        function w3_open() {
            var mySidebar = document.getElementById("mySidebar");
            if (mySidebar.style.display === 'block') {
                mySidebar.style.display = 'none';
            } else {
                mySidebar.style.display = 'block';
            }
        }

        // Close the sidebar with the close button
        function w3_close() {
            document.getElementById("mySidebar").style.display = "none";
        }

        // Toggle project status
        function toggleStatus(element, status) {
            document.querySelectorAll('.status-option').forEach(opt => {
                opt.classList.remove('active');
            });
            element.classList.add('active');
            document.getElementById('projectStatus').value = status;
        }

        // Calculate project duration
        function calculateDuration() {
            const startInput = document.querySelector("input[name='project_start']");
            const endInput = document.querySelector("input[name='project_end']");
            const durationInput = document.getElementById("project_duration");

            if (!startInput.value || !endInput.value) return;

            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);

            if (isNaN(startDate.getTime())) return;
            if (isNaN(endDate.getTime())) return;

            if (endDate < startDate) {
                durationInput.value = "Invalid (end before start)";
                return;
            }

            // Calculate difference
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            // Calculate years, months, days
            let years = endDate.getFullYear() - startDate.getFullYear();
            let months = endDate.getMonth() - startDate.getMonth();
            let days = endDate.getDate() - startDate.getDate();

            if (days < 0) {
                months--;
                const tempDate = new Date(endDate.getFullYear(), endDate.getMonth(), 0);
                days += tempDate.getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            // Format the duration string
            let durationStr = '';
            if (years > 0) durationStr += `${years} Year${years > 1 ? 's' : ''}, `;
            if (months > 0) durationStr += `${months} Month${months > 1 ? 's' : ''}, `;
            durationStr += `${days} Day${days > 1 ? 's' : ''}`;

            durationInput.value = durationStr;
        }

        // Reset form completely
        function resetForm() {
            document.querySelector('form').reset();
            document.getElementById('project_duration').value = '';
            toggleStatus(document.querySelector('.status-option'), 'active');
        }

        // Calculate duration on page load if dates exist
        document.addEventListener("DOMContentLoaded", function () {
            if (document.querySelector("input[name='project_start']").value &&
                document.querySelector("input[name='project_end']").value) {
                calculateDuration();
            }
        });
    </script>
</body>

</html>

<!-- Include footer -->
<?php include 'templates/footer.php'; ?>