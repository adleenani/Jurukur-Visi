<!DOCTYPE html>
<html>

<head>
    <title>Project List | Jurukur Visi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Raleway", sans-serif
        }

        body,
        html {
            height: 100%;
            line-height: 1.8;
        }

        .logo {
            width: 26px;
            height: 26px;
            margin-left: 10px;
        }

        .w3-bar .w3-button {
            padding: 16px;
        }

        .w3-top .w3-card1 {
            border-radius: 0 !important;
        }

        /* Main content padding to account for fixed navbar */
        .main-content {
            padding-top: 100px;
        }

        .card1 {
            background-color: #ffffff;
            padding: 40px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <!-- Navbar (same as your other pages) -->
    <div class="w3-top">
        <div class="w3-card1 w3-bar w3-white w3-card" id="myNavbar">
            <a class="w3-bar-item w3-button w3-wide">JURUKUR VISI
                <img src="images/jvisi_logo.png" class="logo"></a>
            <div class="w3-right w3-hide-small">
                <a href="admin_dashboard.php" class="w3-bar-item w3-button"><i class="fa fa-tachometer"></i>
                    DASHBOARD</a>
                <a href="project_add.php" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i> ADD
                    PROJECT</a>
                <a href="?logout" class="w3-bar-item w3-button"><i class="fa fa-user"></i> LOG OUT
                    (<?php echo sanitizeInput($_SESSION['username']); ?>)</a>
            </div>
            <a href="javascript:void(0)" class="w3-bar-item w3-button w3-right w3-hide-large w3-hide-medium"
                onclick="w3_open()">
                <i class="fa fa-bars"></i>
            </a>
        </div>
    </div>
</body>