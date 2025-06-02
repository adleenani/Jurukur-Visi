<!-- USER HOMEPAGE -->

<?php
require_once 'config.php';

// If user is logged in, redirect to admin_dashboard
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

// Set template variables
$page_title = "JURUKUR VISI";
$show_login_modal = true;

// Include header
include 'templates/header.php';
?>

<!DOCTYPE html>
<html>

<body>
    <!-- About Section -->
    <div class="w3-container" style="padding:128px 22px" id="about">
        <h2 class="w3-center"><b>ABOUT THE COMPANY</b></h2>
        <div class="w3-row-padding w3-center" style="margin-top:40px; display: flex; justify-content: center;">
            <div class="w3-quarter w3-card w3-round" style="padding:50px 20px; margin: 3px; font-size: 18px;">
                <i class="fa fa-building w3-margin-bottom w3-jumbo w3-center"></i>
                <p style="padding:10px 20px; font-size: 20px;"><b>Bumiputera-owned surveying and mapping consulting
                        firm.</b></p>
                <p>Jurukur Visi is a Bumiputera-owned firm specializing in surveying and mapping services.</p>
            </div>
            <div class="w3-quarter w3-card w3-round" style="padding:50px 20px; margin: 3px; font-size: 18px;">
                <i class="fa fa-cogs w3-margin-bottom w3-jumbo"></i>
                <p style="padding:10px 20px; font-size: 20px;"><b>Utilization of advanced surveying technology.</b>
                </p>
                <p>They utilize the latest technology for precise and accurate measurements.</p>
            </div>
            <div class="w3-quarter w3-card w3-round" style="padding:50px 20px; margin: 3px; font-size: 18px;">
                <i class="fa fa-desktop w3-margin-bottom w3-jumbo"></i>
                <p style="padding:10px 20px; font-size: 20px;"><b>IT integration for accuracy and efficiency.</b>
                </p>
                <p>Information technology is integrated throughout the surveying process for 100% accuracy and
                    efficient
                    data processing.</p>
            </div>
            <div class="w3-quarter w3-card w3-round" style="padding:50px 20px; margin: 3px; font-size: 18px;">
                <i class="fa fa-users w3-margin-bottom w3-jumbo"></i>
                <p style="padding:10px 20px; font-size: 20px;"><b>Expertise in government and private sector
                        projects.</b></p>
                <p>They have extensive experience and seek collaboration with government and private sector
                    entities.
                </p>
            </div>
        </div>
    </div>

    <!-- Promo Section - "We know design" -->
    <div class="w3-container w3-light-green" style="padding:100px 16px">
        <div class="w3-row-padding">
            <div class="w3-col m7" style="padding-right: 100px;">
                <h3><b>We specialize in surveying and mapping services.</b></h3>
                <br>
                <h4>Jurukur Visi is a Bumiputera-owned consulting firm that utilizes advanced technology for precise
                    and
                    accurate
                    measurements.</h4>
                <h4>We integrate information technology throughout the surveying process to ensure 100% accuracy and
                    efficient
                    data processing.
                    With our expertise in government and private sector projects, we actively collaborate with
                    various
                    entities to
                    deliver exceptional results.</h4>
                <br>
                <h5><a href="display_project.php" class="w3-button w3-black"><i class="fa fa-th"></i> View Our
                        Projects</a>
                </h5>
            </div>
            <div class="w3-col m5">
                <img class="w3-image w3-round" src="images/surveyor.jpg" alt="Surveying" width="700" height="394">
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="w3-container w3-center" style="padding:100px 16px" id="team">
        <h2 class="w3-center"><b>THE TEAM</b></h2>
        <p class="w3-center w3-xlarge">The ones who runs this company</p>
        <div class="w3-row-padding w3-grayscale" style="margin-top:50px">
            <div class="w3-col l3 m6 w3-margin-bottom">
                <div class="w3-card">
                    <img src="images/download.jpg" alt="John" style="width:50%">
                    <div class="w3-container" style="padding: 30px;">
                        <h4><b>Sr. Zainal Abidin Bin Kamaruddin</b></h4>
                        <p class="w3-opacity w3-large">Director</p>
                        <ul style="list-style-type: disc;" class="w3-large">
                            <li>Manages overall company operations.</li>
                            <li>Utilizes 25 years of experience in private sector surveying.</li>
                            <li>Ensures client satisfaction and meets company goals.</li>
                        </ul>
                        <p><button class="w3-button w3-light-grey w3-block"><i class="fa fa-envelope"></i>
                                Contact</button></p>
                    </div>
                </div>
            </div>
            <div class="w3-col l3 m6 w3-margin-bottom">
                <div class="w3-card">
                    <img src="images/download.jpg" alt="Jane" style="width:50%">
                    <div class="w3-container" style="padding: 30px;">
                        <h4><b>Faizah Binti Abdul Wahab</b></h4>
                        <p class="w3-opacity w3-large">Account and Admin</p>
                        <ul style="list-style-type: disc;" class="w3-large">
                            <li>Manages financial records, budgets, and compliance.</li>
                            <li>Handles transactions, payroll, and taxation.</li>
                            <li>Provides financial reports and analysis.</li>
                        </ul>
                        <p><button class="w3-button w3-light-grey w3-block"><i class="fa fa-envelope"></i>
                                Contact</button></p>
                    </div>
                </div>
            </div>
            <div class="w3-col l3 m6 w3-margin-bottom">
                <div class="w3-card">
                    <img src="images/download.jpg" alt="Mike" style="width:50%">
                    <div class="w3-container" style="padding: 30px;">
                        <h4><b>Fatin Binti Nor Anuar</b></h4>
                        <p class="w3-opacity w3-large">Account and Admin</p>
                        <ul style="list-style-type: disc;" class="w3-large">
                            <li>Manages administrative tasks and office operations.</li>
                            <li>Handles documentation and communication.</li>
                            <li>Supports daily operations and scheduling.</li>
                        </ul>
                        <p><button class="w3-button w3-light-grey w3-block"><i class="fa fa-envelope"></i>
                                Contact</button></p>
                    </div>
                </div>
            </div>
            <div class="w3-col l3 m6 w3-margin-bottom">
                <div class="w3-card">
                    <img src="images/download.jpg" alt="Jane" style="width:50%">
                    <div class="w3-container" style="padding: 30px;">
                        <h4><b>Azharudin bin Abu Hassan</b></h4>
                        <p class="w3-opacity w3-large">Project Manager</p>
                        <ul style="list-style-type: disc;" class="w3-large">
                            <li>Manages specific projects in Jurukur Visi.</li>
                            <li>Plans, coordinates, and monitors project progress.</li>
                            <li>Ensures timely and successful project delivery.</li>
                        </ul>
                        <p><button class="w3-button w3-light-grey w3-block"><i class="fa fa-envelope"></i>
                                Contact</button></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo Section "Statistics" -->
    <div class="w3-container w3-row w3-center w3-light-green w3-padding-64">
        <div class="w3-quarter">
            <span class="w3-xxlarge"><b>14+</b></span>
            <br>
            <p class="w3-xlarge">Partners</p>
        </div>
        <div class="w3-quarter">
            <span class="w3-xxlarge"><b>55+</b></span>
            <br>
            <p class="w3-xlarge">Projects Done</p>
        </div>
        <div class="w3-quarter">
            <span class="w3-xxlarge"><b>89+</b></span>
            <br>
            <p class="w3-xlarge">Happy Clients</p>
        </div>
        <div class="w3-quarter">
            <span class="w3-xxlarge"><b>150+</b></span>
            <br>
            <p class="w3-xlarge">Meetings</p>
        </div>
    </div>

    <!-- Work Section -->
    <div class="w3-container" style="padding:100px 16px" id="work">
        <h2 class="w3-center"><b>OUR WORK</b></h2>
        <p class="w3-center w3-xlarge">We are capable of striking each achivements.</p>

        <div class="w3-row-padding" style="margin-top:64px">
            <div class="w3-col l3 m6">
                <img src="images/visi1.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity">
            </div>
            <div class="w3-col l3 m6">
                <img src="images/visi2.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity">
            </div>
            <div class="w3-col l3 m6">
                <img src="images/visi9.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity">
            </div>
            <div class="w3-col l3 m6">
                <img src="images/visi4.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity">
            </div>
        </div>

        <div class="w3-row-padding w3-section">
            <div class="w3-col l3 m6">
                <img src="images/visi5.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity">
            </div>
            <div class="w3-col l3 m6">
                <img src="images/visi6.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity">
            </div>
            <div class="w3-col l3 m6">
                <img src="images/visi7.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity">
            </div>
            <div class="w3-col l3 m6">
                <img src="images/visi8.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity">
            </div>
        </div>
    </div>

    <!-- Modal for full size images on click-->
    <div id="modal01" class="w3-modal w3-black" onclick="this.style.display='none'">
        <span class="w3-button w3-xxlarge w3-black w3-padding-large w3-display-topright"
            title="Close Modal Image">×</span>
        <div class="w3-modal-content w3-animate-zoom w3-center w3-transparent w3-padding-64">
            <img id="img01" class="w3-image">
            <p id="caption" class="w3-opacity w3-large"></p>
        </div>
    </div>

    <!-- Skills Section -->
    <div class="w3-container w3-light-green w3-padding-64">
        <div class="w3-row-padding">
            <div class="w3-col m6">
                <h2><b>Our Skills</b></h2>
                <ul style="list-style-type: disc; padding: 20px; padding-right: 40px; font-size: 20px;">
                    <li>Committed to 100% accuracy and quality improvement.</li>
                    <li>Delivering high-capacity surveying and mapping services efficiently.</li>
                    <li>Using advanced hardware and software for data collection.</li>
                    <li>Applying modern tech for accurate field-to-finish results.</li>
                    <li>Offering expert surveying and consulting for development projects.</li>
                </ul>
            </div>

            <div class="w3-col m6">
                <p class="w3-wide"><i class="fa fa-check-circle w3-margin-right"></i>Land Surveying</p>
                <div class="w3-grey w3-container w3-dark-grey w3-center" style="width:100%">100%</div>
                <br>
                <p class="w3-wide"><i class="fa fa-cogs w3-margin-right"></i>Development Planning</p>
                <div class="w3-grey w3-container w3-dark-grey w3-center" style="width:100%">100%</div>
                <br>
                <p class="w3-wide"><i class="fa fa-laptop w3-margin-right"></i>Information Technology</p>
                <div class="w3-grey w3-container w3-dark-grey w3-center" style="width:100%">100%</div>
            </div>
        </div>
    </div>

    <!-- Image of location/map -->
    <div class="w3-row-padding" style="padding: 40px 20px; display: flex; flex-wrap: wrap; gap: 20px;">

        <!-- LEFT COLUMN: OUR LOCATION -->
        <div class="w3-col l6 m12"
            style="flex: 1; min-width: 300px; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
            <h2 style="font-weight: bold; margin-bottom: 20px; text-align: center;">OUR LOCATION</h2>
            <p style="font-size: 18px; max-width: 600px; margin: 0 auto; text-align: center;">
                If you require our services, feel free to visit us at our office located at
                <strong>Bandar Saujana Utama, 47000 Sungai Buloh, Selangor</strong>.
            </p>

            <div id="map-container"
                style="width: 100%; height: 400px; margin-top: 30px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            </div>

            <script src="https://maps.googleapis.com/maps/api/js"></script>
            <script>
                function initMap() {
                    var map_parameters = {
                        center: { lat: 3.2009471175255366, lng: 101.48582939131748 },
                        zoom: 18
                    };
                    var map = new google.maps.Map(document.getElementById('map-container'), map_parameters);
                    var marker = new google.maps.Marker({
                        position: map_parameters.center,
                        map: map,
                        title: "Our Office Location"
                    });
                }
                initMap();
            </script>
        </div>

        <!-- RIGHT COLUMN: CONTACT INFO -->
        <div class="w3-col l6 m12"
            style="flex: 1; min-width: 300px; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
            <h2 style="font-weight: bold; margin-bottom: 20px; text-align: center;">CONTACT</h2>
            <p style="font-size: 18px; margin-bottom: 30px; text-align: center;">Let's get in touch. Send us a message:
            </p>

            <div style="text-align: left;  margin: 0 auto; font-size: 16px; line-height: 1.6;">
                <p>
                    <i class="fa fa-map-marker fa-fw" style="font-size: 22px; margin-right: 10px;"></i>
                    NO. 39-1, JALAN BIDARA 10, SAUJANA UTAMA, 47000 SUNGAI BULOH, SELANGOR
                </p>
                <p>
                    <i class="fa fa-phone fa-fw" style="font-size: 22px; margin-right: 10px;"></i>
                    03-6038 8523
                </p>
                <p>
                    <i class="fa fa-envelope fa-fw" style="font-size: 22px; margin-right: 10px;"></i>
                    jvisi95@gmail.com
                </p>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'templates/footer_home.php'; ?>

    <!-- Include login form if needed -->
    <?php if ($show_login_modal && !empty($errors)): ?>
        <?php include 'templates/login_form.php'; ?>
    <?php endif; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal Image Gallery
        function onClick(element) {
            document.getElementById("img01").src = element.src;
            document.getElementById("modal01").style.display = "block";
            var captionText = document.getElementById("caption");
            captionText.innerHTML = element.alt;
        }

        // Toggle between showing and hiding the sidebar when clicking the menu icon
        var mySidebar = document.getElementById("mySidebar");

        function w3_open() {
            if (mySidebar.style.display === 'block') {
                mySidebar.style.display = 'none';
            } else {
                mySidebar.style.display = 'block';
            }
        }

        // Close the sidebar with the close button
        function w3_close() {
            mySidebar.style.display = "none";
        }

        // Close the login modal when clicking outside of it
        window.onclick = function (event) {
            var modal = document.getElementById('loginModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>