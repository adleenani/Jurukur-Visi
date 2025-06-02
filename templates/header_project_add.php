<!-- Navbar (same as before) -->
<div class="w3-top">
    <div class="w3-bar w3-white" id="myNavbar" style="padding: 8px">
        <a href="home.php" class="w3-bar-item w3-button w3-wide">JURUKUR VISI<img src="images/jvisi_logo.png"
                class="logo"></a>
        <div class="w3-right w3-hide-small">
            <a href="admin_dashboard.php" class="w3-bar-item w3-button"><i class="fa fa-tachometer"></i> DASHBOARD</a>
            <a href="project_list.php" class="w3-bar-item w3-button"><i class="fa fa-wrench"></i> PROJECT LIST</a>
            <a href="?logout" class="w3-bar-item w3-button"><i class="fa fa-user"></i> LOG OUT
                (<?php echo sanitizeInput($_SESSION['username']); ?>)</a>
        </div>
        <a href="javascript:void(0)" class="w3-bar-item w3-button w3-right w3-hide-large w3-hide-medium"
            onclick="w3_open()">
            <i class="fa fa-bars"></i>
        </a>
    </div>
</div>

<!-- Sidebar (same as before) -->
<nav class="w3-sidebar w3-bar-block w3-black w3-card w3-animate-left w3-hide-medium w3-hide-large" style="display:none"
    id="mySidebar">
    <a href="javascript:void(0)" onclick="w3_close()" class="w3-bar-item w3-button w3-large w3-padding-16">Close</a>
    <a href="admin_dashboard.php" onclick="w3_close()" class="w3-bar-item w3-button">DASHBOARD</a>
    <a href="display_project.php" onclick="w3_close()" class="w3-bar-item w3-button">BACK</a>
    <a href="?logout" onclick="w3_close()" class="w3-bar-item w3-button">LOG OUT
        (<?php echo sanitizeInput($_SESSION['username']); ?>)</a>
</nav>