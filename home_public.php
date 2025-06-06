<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>JURUKUR VISI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/home_public.css">
    <style>
        .error-popup {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 20px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: flex;
            align-items: center;
            max-width: 90%;
            width: fit-content;
            min-width: 250px;
            animation: slideIn 0.5s, fadeOut 0.5s 3.5s forwards;
            font-size: 1rem;
        }

        .error-popup i {
            margin-right: 10px;
            font-size: 1.3em;
        }


        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
        }
    </style>
</head>

<body>
    <!-- Full-Height Background Section -->
    <header class="bgimg-1 w3-display-container">
        <div class="w3-display-middle w3-text-white w3-center">
            <h1 class="welcome-title">Welcome to Jurukur Visi</h1>
            <span class="tagline">Surveying Excellence Since 2005</span>

            <div class="w3-row" style="max-width: 800px; margin: 0 auto;">
                <div class="w3-half w3-center" style="padding: 15px;">
                    <a href="homepage.php" class="w3-button w3-round-xxlarge w3-padding-large portal-btn">
                        <i class="fas fa-user fa-2x"></i><br>
                        Public User Portal
                    </a>
                </div>
                <div class="w3-half w3-center" style="padding: 15px;">
                    <a href="admin_login.php" class="w3-button w3-round-xxlarge w3-padding-large login-btn">
                        <i class="fas fa-lock fa-2x"></i><br>
                        Staff Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Fixed Footer -->
        <div class="footer-fixed">
            <div>© <?php echo date('Y'); ?> Jurukur Visi Sdn Bhd</div>
            <div>
                <a href="tel:+60312345678"><i class="fas fa-phone"></i> +603 1234 5678</a>
                <a href="mailto:info@jurukurvisi.com"><i class="fas fa-envelope"></i> info@jurukurvisi.com</a>
            </div>
        </div>
    </header>

    <!-- Error User try to log in -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-popup">
            <i class="fas fa-exclamation-circle"></i>
            <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(() => {
                    const popup = document.querySelector('.error-popup');
                    if (popup) popup.remove();
                }, 4000); // 4 seconds display
            });
        </script>
    <?php endif; ?>

</body>

</html>