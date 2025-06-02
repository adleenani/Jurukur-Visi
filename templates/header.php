<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($page_title ?? 'JURUKUR VISI'); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/home.css">
</head>

<body>

    <!-- Navbar (sit on top) -->
    <div class="w3-top">
        <div class="w3-card w3-bar w3-white w3-bar" id="myNavbar">
            <a href="home.php" class="w3-bar-item w3-button w3-wide">JURUKUR VISI<img src="images/jvisi_logo.png"
                    class="logo"></a>
            <!-- Right-sided navbar links -->
            <div class="w3-right w3-hide-small">
                <a href="#about" class="w3-bar-item w3-button">ABOUT</a>
                <a class="w3-bar-item w3-button" href="display_project.php"><i class="fa fa-wrench"></i> PROJECT</a>
                <a class="w3-bar-item w3-button" href="faq.php"><i class="fa fa-question-circle"></i> FAQ</a>
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
        <a href="#about" onclick="w3_close()" class="w3-bar-item w3-button">ABOUT</a>
        <a href="project_list.php" onclick="w3_close()" class="w3-bar-item w3-button">PROJECT</a>
        <a href="faq.php" onclick="w3_close()" class="w3-bar-item w3-button">FAQ</a>
        <a href="#" class="w3-bar-item w3-button">LOG IN</a>
    </nav>

    <!-- Header with full-height image -->
    <header class="bgimg-1 w3-display-container w3-grayscale-min" id="home" style="margin-top: 1px">
        <div class="w3-display-left w3-text-white" style="padding:48px">
            <span class="w3-jumbo w3-hide-small">Welcome to Jurukur Visi</span><br>
            <span class="w3-xxlarge w3-hide-large w3-hide-medium">Welcome to Jurukur Visi</span><br>
            <span class="w3-large">Jurukur Visi Sdn Bhd is a certified surveying and mapping</span><br>
            <span class="w3-large">consulting firm that offers land surveying </span><br>
            <span class="w3-large">and development planning services.</span><br>
            <span class="w3-large">Contact us at 03-6038 8523.</span>
            <p><a href="#about"
                    class="w3-button w3-white w3-padding-large w3-large w3-margin-top w3-opacity w3-hover-opacity-off">Learn
                    more
                    and start today</a></p>
        </div>
        <div class="w3-display-bottomleft w3-text-grey w3-large" style="padding:24px 48px">
            <a href="https://www.facebook.com/JurukurTanahBerlesen/photos" target="_blank"
                style="text-decoration: none; color: inherit;">
                <i class="fa fa-facebook-official w3-hover-opacity"></i>
        </div>
    </header>