<!-- Top Navigation -->
<div class="w3-bar w3-white w3-card" style="padding: 8px">
    <a href="home.php" class="w3-bar-item w3-button w3-wide">JURUKUR VISI
        <img src="images/jvisi_logo.png" class="logo">
    </a>
    <div class="w3-right">
        <a href="admin_dashboard.php" class="w3-bar-item w3-button"><i class="fa fa-tachometer"></i>  DASHBOARD</a>
        <a href="project_list.php" class="w3-bar-item w3-button"><i class="fa fa-wrench"></i>  PROJECT LIST</a>
        <a href="?logout" class="w3-bar-item w3-button"><i class="fa fa-user"></i>  LOG OUT
            (<?php echo sanitizeInput($_SESSION['username']); ?>)</a>
    </div>
</div>