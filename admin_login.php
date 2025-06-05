<!-- USER LOGIN PAGE -->

<?php
require_once 'config.php';
require_once 'functions.php'; // Make sure this contains isLoggedIn() and other functions

// Redirect if already logged in
if (isLoggedIn()) {
    // If already logged in as admin, go to dashboard
    if ($_SESSION['user_role'] === 'admin') {
        redirect('admin_dashboard.php');
    }
    // If logged in as regular user, show the login page with message
    $_SESSION['error'] = "You don't have admin privileges";
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'])) {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password']; // Don't sanitize passwords

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            session_regenerate_id(true);

            $_SESSION = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'user_role' => $user['role'] ?? 'user',
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                redirect('admin_dashboard.php');
            } else {
                $_SESSION['error'] = "You don't have admin privileges";
                redirect('home_public.php');
            }
        } else {
            $errors[] = "Invalid username or password";
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $errors[] = "System error. Please try again.";
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
    <!-- <link rel="stylesheet" href="styles/admin_login.css"> -->
    <style>
        :root {
            --ivory: #f8f9f5;
            --emerald: #2e8b57;
            --forest: #355e3b;
            --sage: #9caf88;
            --mint: #c1e1c1;
            --moss: #6b8e23;
            --charcoal: #1a1a1a;
            --error: #dc3545;
        }

        html,
        body {
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: "Raleway", sans-serif;
            color: var(--charcoal);
            position: relative;
        }

        body {
            background: radial-gradient(circle at 10% 20%,
                    rgba(210, 230, 210, 0.1) 0%,
                    transparent 20%),
                linear-gradient(135deg, #f0f5f0 0%, #e1e8e1 100%);
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg,
                    var(--emerald),
                    var(--forest),
                    var(--emerald));
            z-index: 10;
        }

        .luxury-header {
            padding: 2rem 0;
            text-align: center;
        }

        .luxury-header h1 {
            font-weight: 700;
            letter-spacing: 2px;
            margin: 0;
            font-size: 2.5rem;
            color: var(--forest);
            position: relative;
            display: inline-block;
        }

        .luxury-header h1::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 25%;
            width: 50%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--emerald), transparent);
        }

        .access-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            padding: 2rem;
        }

        .access-portal {
            width: 100%;
            max-width: 480px;
            background: rgba(248, 249, 245, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(46, 139, 87, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05),
                inset 0 0 0 1px rgba(255, 255, 255, 0.8);
            position: relative;
        }

        .access-portal::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--emerald), var(--moss));
        }

        .portal-header {
            padding: 2.5rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(46, 139, 87, 0.1);
        }

        .portal-header h2 {
            margin: 0;
            font-weight: 400;
            font-size: 1.8rem;
            letter-spacing: 1px;
            color: var(--forest);
        }

        .portal-header h2 span {
            font-weight: 700;
            color: var(--emerald);
        }

        .portal-body {
            padding: 2.5rem;
        }

        .luxury-input {
            position: relative;
            margin-bottom: 2rem;
        }

        .luxury-input label {
            display: block;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            color: var(--moss);
            font-weight: 500;
        }

        .luxury-input input {
            width: 100%;
            padding: 1rem 1rem 1rem 0;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(53, 94, 59, 0.3);
            font-size: 1rem;
            color: var(--charcoal);
            transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .luxury-input input:focus {
            outline: none;
            border-bottom-color: var(--emerald);
            padding-left: 1rem;
        }

        .luxury-input i {
            position: absolute;
            right: 0;
            bottom: 1rem;
            color: var(--sage);
            transition: all 0.4s;
        }

        .luxury-input input:focus+i {
            color: var(--emerald);
            transform: translateX(5px);
        }

        .access-btn {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(to right, var(--emerald), var(--forest));
            border: none;
            color: white;
            font-size: 1rem;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
            margin-top: 1rem;
        }

        .access-btn:hover {
            background: linear-gradient(to right, var(--forest), var(--emerald));
            letter-spacing: 2px;
        }

        .portal-links {
            display: flex;
            justify-content: space-between;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(46, 139, 87, 0.1);
        }

        .portal-link {
            color: var(--moss);
            text-decoration: none;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .portal-link:hover {
            color: var(--emerald);
        }

        .luxury-footer {
            padding: 2rem 0;
            text-align: center;
        }

        .luxury-footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 25%;
            width: 50%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--emerald), transparent);
        }

        .luxury-footer p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--moss);
            letter-spacing: 0.5px;
        }

        /* Decorative circles - fixed to prevent scrolling */
        .decorative-circle {
            position: fixed;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle,
                    rgba(46, 139, 87, 0.05) 0%,
                    transparent 70%);
            z-index: -1;
            pointer-events: none;
        }

        .circle-1 {
            top: -50px;
            right: -50px;
        }

        .circle-2 {
            bottom: -50px;
            left: -50px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .decorative-circle {
                display: none;
            }

            .access-portal {
                max-width: 95%;
            }

            .portal-body {
                padding: 1.5rem;
            }
        }
    </style>

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
                        <a href="admin_signup.php" class="portal-link"><i class="fas fa-user-plus"></i> SIGN UP</a>
                        <a href="home_public.php" class="portal-link"><i class="fas fa-arrow-left"></i> RETURN TO
                            MENU</a>
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