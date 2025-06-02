<!-- ADMIN EDIT PROJECT PAGE -->

<?php
require_once 'config.php';
requireLogin();

// Handle logout
if (isset($_GET['logout'])) {
    $_SESSION['message'] = "You have successfully logged out.";
    setcookie(session_name(), '', time() - 42000);
    session_destroy();
    redirect('home.php');
}

// Check if project_id is provided
if (!isset($_GET['project_id'])) {
    $_SESSION['error'] = "No project ID provided.";
    redirect('project_list.php');
}

$project_id = $_GET['project_id']; // Get raw value
$project_id = preg_replace('/[^a-zA-Z0-9\-_]/', '', $project_id); // Sanitize (allow dashes and underscores)

// Fetch the project
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        $_SESSION['error'] = "Project not found.";
        redirect('project_list.php');
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching project: " . $e->getMessage();
    error_log("Fetch error: " . $e->getMessage());
    redirect('project_list.php');
}

// Handle update form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token exists first
    if (!isset($_POST['csrf_token'])) {
        $_SESSION['error'] = "Security token missing";
        redirect('project_edit.php?project_id=' . urlencode($project_id));
        exit;
    }

    // Then verify it
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid security token";
        redirect('project_edit.php?project_id=' . urlencode($project_id));
        exit;
    }

    $project_name = sanitizeInput($_POST['project_name']);
    $project_start = sanitizeInput($_POST['project_start']);
    $project_end = sanitizeInput($_POST['project_end']);
    $project_location = sanitizeInput($_POST['project_location']);
    $project_services = sanitizeInput($_POST['project_services']);
    $project_status = sanitizeInput($_POST['project_status'] ?? 'active');
    $errors = [];

    // Basic validations
    if (empty($project_name))
        $errors[] = "Project name is required.";
    if (empty($project_start))
        $errors[] = "Start date is required.";
    if (empty($project_end))
        $errors[] = "End date is required.";
    if (empty($project_location))
        $errors[] = "Location is required.";
    if (empty($project_services))
        $errors[] = "Services are required.";

    // Date validation
    if (empty($errors)) {
        try {
            $start = new DateTime($project_start);
            $end = new DateTime($project_end);

            if ($end < $start) {
                $errors[] = "End date cannot be before start date";
            } else {
                $interval = $start->diff($end);
                $project_duration = $interval->format('%y Year(s), %m Month(s), %d Day(s)');
            }
        } catch (Exception $e) {
            $errors[] = "Invalid date format: " . $e->getMessage();
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE projects 
    SET project_name = ?, 
        project_start = ?, 
        project_end = ?, 
        project_location = ?, 
        project_duration = ?, 
        project_services = ?,
        project_status = ?,
        updated_at = NOW()
    WHERE project_id = ?");

            $stmt->execute([
                $project_name,
                $project_start,
                $project_end,
                $project_location,
                $project_duration,
                $project_services,
                $project_status,
                $project_id
            ]);

            $_SESSION['message'] = "Project updated successfully!";
            redirect('project_list.php');
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating project: " . $e->getMessage();
            error_log("Update error: " . $e->getMessage());
            redirect('project_edit.php?project_id=' . urlencode($project_id));
        }
    } else {
        // Store errors in session to display them after redirect
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        redirect('project_edit.php?project_id=' . urlencode($project_id));
    }
}

// Use the standard header
$page_title = "Edit Project - " . htmlspecialchars($project['project_name']);

include 'templates/header_project_edit.php';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary-color: #1b3f1c;
            --secondary-color: #bfe3b4;
            --accent-color: #3a7d44;
            --light-grey: #f8f9fa;
            --dark-grey: #6c757d;
        }

        .project-form-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .project-form-header {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 15px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: var(--primary-color);
        }

        .status-toggle {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            border-radius: 6px;
            overflow: hidden;
        }

        .status-option {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: all 0.3s;
            background-color: var(--light-grey);
        }

        .status-option.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .required-field::after {
            content: " *";
            color: red;
        }

        .duration-hint {
            color: var(--dark-grey);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .project-form-container {
                padding: 20px;
                margin: 15px;
            }

            .status-toggle {
                flex-direction: column;
            }

            .status-option {
                border-radius: 6px !important;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
    <!-- Main Form Content -->
    <div class="main-content">
        <div class="project-form-container">
            <div class="project-form-header">
                <h2><i class="fas fa-project-diagram"></i>
                    <?php echo isset($project) ? 'Edit Project' : 'Project Registration'; ?></h2>
                <p class="text-muted">
                    <?php echo isset($project) ? 'Update the project details' : 'Fill in the project details below'; ?>
                </p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo sanitizeInput($_SESSION['error']);
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="post"
                action="project_edit.php?project_id=<?php echo htmlspecialchars($project['project_id']); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo sanitizeInput(csrf_token()); ?>">
                <!-- Project Status Toggle -->
                <div class="form-group">
                    <label class="form-label required-field">Project Status</label>
                    <div class="status-toggle">
                        <div class="status-option <?php echo (!isset($project) || $project['project_status'] === 'active') ? 'active' : ''; ?>"
                            onclick="toggleStatus(this, 'active')">
                            <i class="fas fa-spinner"></i> Active
                        </div>
                        <div class="status-option <?php echo (isset($project) && $project['project_status'] === 'completed') ? 'active' : ''; ?>"
                            onclick="toggleStatus(this, 'completed')">
                            <i class="fas fa-check-circle"></i> Completed
                        </div>
                    </div>
                    <input type="hidden" name="project_status"
                        value="<?php echo isset($project) ? htmlspecialchars($project['project_status']) : 'active'; ?>"
                        id="projectStatus">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required-field">Project ID</label>
                            <input type="text" name="project_id" class="form-control" placeholder="PRJ-001" required
                                value="<?php echo isset($project) ? htmlspecialchars($project['project_id']) : (isset($_POST['project_id']) ? sanitizeInput($_POST['project_id']) : ''); ?>"
                                <?php echo isset($project) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required-field">Project Name</label>
                            <input type="text" name="project_name" class="form-control" placeholder="Project Name"
                                required
                                value="<?php echo isset($project) ? htmlspecialchars($project['project_name']) : (isset($_POST['project_name']) ? sanitizeInput($_POST['project_name']) : ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required-field">Start Date</label>
                            <input type="date" name="project_start" class="form-control" required
                                value="<?php echo isset($project) ? htmlspecialchars($project['project_start']) : (isset($_POST['project_start']) ? sanitizeInput($_POST['project_start']) : ''); ?>"
                                onchange="calculateDuration()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required-field">End Date</label>
                            <input type="date" name="project_end" class="form-control" required
                                value="<?php echo isset($project) ? htmlspecialchars($project['project_end']) : (isset($_POST['project_end']) ? sanitizeInput($_POST['project_end']) : ''); ?>"
                                onchange="calculateDuration()">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required-field">Project Location</label>
                    <input type="text" name="project_location" class="form-control" placeholder="Project Location"
                        required
                        value="<?php echo isset($project) ? htmlspecialchars($project['project_location']) : (isset($_POST['project_location']) ? sanitizeInput($_POST['project_location']) : ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Project Duration</label>
                    <input type="text" name="project_duration" id="project_duration" class="form-control"
                        value="<?php echo isset($project) ? htmlspecialchars($project['project_duration']) : ''; ?>"
                        placeholder="Will be calculated automatically" readonly>
                    <p class="duration-hint">Duration will be calculated based on start and end dates</p>
                </div>

                <div class="form-group">
                    <label class="form-label required-field">Project Services</label>
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
                            "Underground Utilities Detection & Mapping"
                        ];
                        foreach ($options as $option) {
                            $selected = '';
                            if (isset($project) && $project['project_services'] === $option) {
                                $selected = 'selected';
                            } elseif (isset($_POST['project_services']) && $_POST['project_services'] === $option) {
                                $selected = 'selected';
                            }
                            echo "<option value=\"" . htmlspecialchars($option) . "\" $selected>" . htmlspecialchars($option) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-submit"
                        style="background-color: #046307; color: white; border: none; padding: 10px 20px; border-radius: 5px;">
                        <i class="fas fa-save"></i>
                        <?php echo isset($project) ? 'Update Project' : 'Register Project'; ?>
                    </button>

                    <a href="project_list.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
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