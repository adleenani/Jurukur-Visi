<!-- ADMIN SIGN UP -->

<?php
require_once 'config.php';

// Initialize variables
$username = $email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'])) {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // Don't sanitize passwords

    // Validation
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 14) {
        $errors[] = "Password must be at least 14 characters";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter and one number";
    }

    if (empty($errors)) {
        try {
            // Check if username or email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->rowCount() > 0) {
                $errors[] = "Username or email already exists";
            } else {
                // Insert new user
                $hashedPassword = hashPassword($password);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$username, $email, $hashedPassword]);

                $_SESSION['message'] = "Registration successful! Please login.";
                redirect('login.php');
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "Registration failed. Please try again later.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Jurukur Visi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --ivory: #f8f9f5;
            /* Soft green-tinged ivory */
            --emerald: #2e8b57;
            /* Classic rich emerald */
            --forest: #355e3b;
            /* Deep forest green */
            --sage: #9caf88;
            /* Muted sage green */
            --mint: #c1e1c1;
            /* Soft pastel mint */
            --moss: #6b8e23;
            /* Earthy moss green */
            --charcoal: #1a1a1a;
            /* Darker charcoal for contrast */
            --error: #dc3545;
        }

        body {
            background: radial-gradient(circle at 10% 20%,
                    rgba(210, 230, 210, 0.1) 0%,
                    transparent 20%),
                linear-gradient(135deg, #f0f5f0 0%, #e1e8e1 100%);
            min-height: 100vh;
            font-family: "Raleway", sans-serif;
            color: var(--charcoal);
            padding-top: 10px;
        }

        .brand-header {
            text-align: center;
            padding: 2rem 0;
        }

        .brand-header h1 {
            font-family: "Raleway", sans-serif;
            font-weight: 600;
            letter-spacing: 2px;
            color: var(--forest);
            position: relative;
            display: inline-block;
        }

        .brand-header h1::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 25%;
            width: 50%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--emerald), transparent);
        }

        .auth-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 1rem;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border-top: 4px solid var(--emerald);
        }

        .auth-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(46, 139, 87, 0.1);
        }

        .auth-header h2 {
            font-family: "Raleway", sans-serif;
            color: var(--forest);
            margin: 0;
        }

        .auth-header p {
            color: var(--sage);
            margin-top: 0.5rem;
        }

        .auth-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--moss);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--emerald);
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.2);
            outline: none;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 38px;
            cursor: pointer;
            color: var(--sage);
        }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(to right, var(--emerald), var(--forest));
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, var(--forest), var(--emerald));
            transform: translateY(-2px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(46, 139, 87, 0.1);
        }

        .auth-link {
            color: var(--emerald);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid var(--error);
            color: var(--error);
        }

        .password-strength {
            margin-top: 0.5rem;
            height: 5px;
            background: #eee;
            border-radius: 3px;
            overflow: hidden;
        }

        .strength-meter {
            height: 100%;
            width: 0;
            background: #ddd;
            transition: width 0.3s;
        }

        @media (max-width: 576px) {
            .auth-container {
                padding: 1rem;
            }

            .auth-body {
                padding: 1.5rem;
            }
        }

        .luxury-footer {
            background: transparent;
            padding: 2rem 0;
            text-align: center;
            margin-top: auto;
            position: relative;
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

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 40px;
            /* space for the eye icon */
        }

        .password-toggle {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            /* optional */
        }
    </style>
</head>

<body>
    <header class="brand-header">
        <div class="container">
            <h1>JURUKUR VISI</h1>
        </div>
    </header>

    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Create Your Account</h2>
            </div>

            <div class="auth-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post" id="signupForm">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="<?php echo htmlspecialchars($username); ?>" required minlength="4" maxlength="30">
                        <small class="text-muted">4-30 characters, letters and numbers only</small>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="password" name="password" required
                                minlength="14">
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                        <div class="password-strength">
                            <div class="strength-meter" id="strengthMeter"></div>
                        </div>
                        <small class="text-muted">Minimum 14 characters with at least one uppercase letter and
                            number</small>
                    </div>


                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php" class="auth-link">Sign in here</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer class="luxury-footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> JURUKUR VISI SDN BHD</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        // Password strength indicator
        password.addEventListener('input', function () {
            const strengthMeter = document.getElementById('strengthMeter');
            const strength = calculatePasswordStrength(this.value);

            // Update meter width and color
            strengthMeter.style.width = strength.percentage + '%';
            strengthMeter.style.backgroundColor = strength.color;
        });

        function calculatePasswordStrength(password) {
            let strength = 0;

            // Length contributes up to 50%
            strength += Math.min(password.length / 16 * 50, 50);

            // Character variety contributes up to 30%
            if (/[A-Z]/.test(password)) strength += 10;
            if (/[0-9]/.test(password)) strength += 10;
            if (/[^A-Za-z0-9]/.test(password)) strength += 10;

            // Common patterns reduce strength
            if (password.length < 8) strength = Math.min(strength, 30);
            if (password === password.toLowerCase()) strength = Math.min(strength, 60);

            // Determine color
            let color;
            if (strength < 30) color = '#dc3545'; // Red
            else if (strength < 60) color = '#fd7e14'; // Orange
            else if (strength < 80) color = '#ffc107'; // Yellow
            else color = '#28a745'; // Green

            return {
                percentage: strength,
                color: color
            };
        }

        // Form validation
        document.getElementById('signupForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;

            if (password.length < 14) {
                e.preventDefault();
                alert('Password must be at least 14 characters');
                return false;
            }

            if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one uppercase letter and one number');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>