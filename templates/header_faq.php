<!DOCTYPE html>
<html>

<head>
    <title>FAQ | Jurukur Visi</title>
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

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .logo {
            width: 26px;
            height: 26px;
            margin-left: 10px;
        }

        .w3-bar .w3-button {
            padding: 16px;
        }

        .card {
            background-color: #ffffff;
            padding: 40px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .w3-top .w3-card {
            border-radius: 0 !important;
        }

   </style>
</head>

<body>
    <!-- Navbar (sit on top) -->
    <div class="w3-top">
        <div class="w3-card w3-bar w3-white w3-bar" id="myNavbar" style="padding: 8px">
            <a href="home.php" class="w3-bar-item w3-button w3-wide">JURUKUR VISI<img src="images/jvisi_logo.png"
                    class="logo"></a>
            <!-- Right-sided navbar links -->
            <div class="w3-right w3-hide-small">
                <a href="home.php" class="w3-bar-item w3-button"><i class="fa fa-home"></i> HOME</a>
                <a class="w3-bar-item w3-button" href="display_project.php"><i class="fa fa-wrench"></i> PROJECT</a>
                <a class="w3-bar-item w3-button" href="login.php"><i class="fa fa-user"></i> LOG IN</a>
            </div>
            <!-- Hamburger menu for mobile -->
            <a href="javascript:void(0)" class="w3-bar-item w3-button w3-right w3-hide-large w3-hide-medium"
                onclick="w3_open()">
                <i class="fa fa-bars"></i>
            </a>
        </div>
    </div>

    <!-- Sidebar on small screens when clicking the menu icon -->
    <nav class="w3-sidebar w3-bar-block w3-black w3-card w3-animate-left w3-hide-medium w3-hide-large"
        style="display:none" id="mySidebar">
        <a href="javascript:void(0)" onclick="w3_close()" class="w3-bar-item w3-button w3-large w3-padding-16">Close
            ×</a>
        <a href="home.php" onclick="w3_close()" class="w3-bar-item w3-button">HOME</a>
        <a href="display_project.php" onclick="w3_close()" class="w3-bar-item w3-button">PROJECT</a>
        <a href="#" class="w3-bar-item w3-button">LOG IN</a>
    </nav>