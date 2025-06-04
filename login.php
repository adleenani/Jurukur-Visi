<!-- USER LOGIN PAGE -->

<?php
require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    redirect('admin_dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'])) {
    $username = sanitizeInput($_POST['username'], 'sql');
    $password = $_POST['password']; // Don't sanitize passwords

    try {
        $stmt = dbQuery("SELECT id, username, password FROM users WHERE username = ?", [$username]);

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch();
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID on login
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                // Redirect to intended page or admin_dashboard
                $redirect = $_SESSION['redirect_url'] ?? 'admin_dashboard.php';
                unset($_SESSION['redirect_url']);
                redirect($redirect);
            }
        }

        // Generic error message to prevent user enumeration
        $errors[] = "Invalid username or password";

    } catch (Exception $e) {
        $errors[] = "System error. Please try again later.";
        error_log("Login error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Jurukur Visi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/login.css">
</head>

<body>
    <div class="decorative-circle circle-1"></div>
    <div class="decorative-circle circle-2"></div>

    <header class="luxury-header">
        <div class="container">
            <h1>JURUKUR VISI</h1>
        </div>
    </header>

    <div class="access-container">
        <div class="access-portal">
            <div class="portal-header">
                <h2>LOG <span>IN</span></h2>
            </div>
            <div class="portal-body">
                <?php if (!empty($errors)): ?>
                    <div class="status-message">
                        <?php foreach ($errors as $error): ?>
                            <p><i class="fas fa-exclamation-circle"></i> <?php echo sanitizeInput($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo sanitizeInput(csrf_token()); ?>">

                    <div class="luxury-input">
                        <label for="username">USERNAME</label>
                        <input type="text" id="username" name="username" required
                            value="<?php echo isset($_POST['username']) ? sanitizeInput($_POST['username']) : ''; ?>"
                            placeholder="Enter your username">
                        <i class="fas fa-user" style="padding-right: 10px;"></i>
                    </div>

                    <div class="luxury-input">
                        <label for="password">PASSWORD</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                        <i class="fas fa-lock" style="padding-right: 10px;"></i>
                    </div>

                    <button type="submit" class="access-btn">
                        LOG IN <i class="fas fa-arrow-right"></i>
                    </button>

                    <div class="portal-links">
                        <a href="signup.php" class="portal-link"><i class="fas fa-user-plus"></i> SIGN UP</a>
                        <a href="home.php" class="portal-link"><i class="fas fa-arrow-left"></i> RETURN TO HOME</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="luxury-footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> JURUKUR VISI SDN BHD</p>
        </div>
    </footer>
</body>

</html>