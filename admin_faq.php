<!-- ADMIN FAQ -->

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

// Handle FAQ operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid security token";
        redirect('admin_faq.php');
    }

    // Add new FAQ
    if (isset($_POST['add_faq'])) {
        $category_id = (int) $_POST['category_id'];
        $question = sanitizeInput($_POST['question']);
        $answer = sanitizeInput($_POST['answer'], true); // Allow HTML for answers
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        try {
            $stmt = $pdo->prepare("INSERT INTO faq_questions (category_id, question, answer, is_featured, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$category_id, $question, $answer, $is_featured]);
            $_SESSION['message'] = "FAQ added successfully!";
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error adding FAQ: " . $e->getMessage();
            $_SESSION['error_type'] = 'danger';
        }
        redirect('admin_faq.php');
    }
    // Delete FAQ
    elseif (isset($_POST['delete_faq'])) {
        $id = (int) $_POST['id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM faq_questions WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "FAQ deleted successfully!";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['error'] = "FAQ not found or already deleted";
                $_SESSION['error_type'] = 'warning';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error deleting FAQ: " . $e->getMessage();
            $_SESSION['error_type'] = 'danger';
        }
        redirect('admin_faq.php');
    }
}

// Get all FAQs and categories with pagination
try {
    // Get categories
    $categories = $pdo->query("SELECT * FROM faq_categories ORDER BY name")->fetchAll();

    // Get FAQs with pagination
    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    // Count total FAQs for pagination
    $total_faqs = $pdo->query("SELECT COUNT(*) FROM faq_questions")->fetchColumn();
    $total_pages = ceil($total_faqs / $per_page);

    // Get paginated FAQs
    $faqs = $pdo->query("
        SELECT fq.*, fc.name as category_name 
        FROM faq_questions fq
        LEFT JOIN faq_categories fc ON fq.category_id = fc.id
        ORDER BY fc.name, fq.question
        LIMIT $per_page OFFSET $offset
    ")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $_SESSION['error_type'] = 'danger';
    $categories = [];
    $faqs = [];
    $total_pages = 1;
}

$page_title = "FAQ Management";
include 'templates/header_admin_faq.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="admin_faq.css"> -->
    <style>
        /* ===== BASE STYLES ===== */
        :root {
            --primary-color: #3a7d44;
            --secondary-color: #bfe3b4;
            --text-dark: #1a1a1a;
            --border-color: #dee2e6;
        }

        .admin-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            padding-top: 50px;
        }

        /* ===== HEADER STYLES ===== */
        .admin-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-header h1 {
            font-weight: 600;
            color: var(--primary-color);
        }

        /* ===== CARD STYLES ===== */
        .card {
            border: none;
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            padding: 1rem 1.5rem;
            font-weight: 600;
            background-color: var(--primary-color);
            color: white;
        }

        /* ===== FORM STYLES ===== */
        .needs-validation .form-control:invalid,
        .needs-validation .form-select:invalid {
            border-color: #dc3545;
        }

        .needs-validation .form-control:valid,
        .needs-validation .form-select:valid {
            border-color: #198754;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* ===== TABLE STYLES ===== */
        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            background-color: var(--secondary-color) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(58, 125, 68, 0.05);
        }

        /* ===== BUTTON STYLES ===== */
        .btn {
            padding: 0.375rem 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: #2e6635;
            border-color: #2e6635;
        }

        /* ===== BADGE STYLES ===== */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        .badge.bg-warning {
            font-size: 0.75rem;
            padding: 0.2em 0.4em;
        }

        /* ===== ALERT STYLES ===== */
        .alert {
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        /* ===== PAGINATION ===== */
        .pagination {
            margin-top: 1.5rem;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .page-link {
            color: var(--primary-color);
        }

        /* ===== RESPONSIVE STYLES ===== */
        @media (max-width: 768px) {
            .table-responsive {
                border: 0;
            }

            .table thead {
                display: none;
            }

            .table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 0.25rem;
            }

            .table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                width: 45%;
                padding-right: 1rem;
                font-weight: 600;
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'success'; ?> alert-dismissible fade show"
                role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-<?php echo $_SESSION['error_type'] ?? 'danger'; ?> alert-dismissible fade show"
                role="alert">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error'], $_SESSION['error_type']); ?>
        <?php endif; ?>

        <div class="admin-header">
            <h1><i class="fas fa-question-circle me-2"></i>FAQ Management</h1>

        </div>

        <div class="row g-4">
            <!-- Add FAQ Form -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New FAQ</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a category</div>
                            </div>

                            <div class="mb-3">
                                <label for="question" class="form-label">Question</label>
                                <input type="text" class="form-control" id="question" name="question" required>
                                <div class="invalid-feedback">Please enter a question</div>
                            </div>

                            <div class="mb-3">
                                <label for="answer" class="form-label">Answer</label>
                                <textarea class="form-control" id="answer" name="answer" rows="4" required></textarea>
                                <div class="invalid-feedback">Please provide an answer</div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
                                <label class="form-check-label" for="is_featured">Featured FAQ</label>
                            </div>

                            <button type="submit" name="add_faq" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Add FAQ
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FAQ List -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="fas fa-list me-2"></i>Current FAQs</h4>
                            <span class="badge bg-light text-dark"><?php echo $total_faqs; ?> FAQs</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($faqs)): ?>
                            <div class="alert alert-info">No FAQs found. Add one to get started!</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Category</th>
                                            <th>Question</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($faqs as $faq): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($faq['category_name']); ?></td>
                                                <td><?php echo htmlspecialchars($faq['question']); ?></td>
                                                <td class="text-nowrap">
                                                    <!-- <a href="admin_faq_edit.php?id=<?php echo $faq['id']; ?>"
                                                        class="btn btn-sm btn-primary me-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a> -->
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="csrf_token"
                                                            value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo $faq['id']; ?>">
                                                        <button type="submit" name="delete_faq" class="btn btn-sm btn-danger"
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this FAQ?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                    <?php if ($faq['is_featured']): ?>
                                                        <span class="badge bg-warning text-dark ms-1" title="Featured">
                                                            <i class="fas fa-star"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="FAQ pagination">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>

</html>

<?php include 'templates/footer.php'; ?>