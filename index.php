<?php
include_once "functions/session.php";
include_once "functions/login.php";
include_once "functions/db_connect.php";
if(isset($_POST['accept_privacy'])) {
    $_SESSION['privacy_accepted'] = true;
}

if(isset($_POST['decline_privacy'])) {
    $_SESSION['privacy_accepted'] = false;
    header("location: https://google.com");
    exit;
}

$isLoggedIn = isLoggedIn();
$privacyAccepted = $_SESSION['privacy_accepted'] ?? false;

if(isset($_GET['redirect']) && $_GET['redirect'] == 1){
    if($isLoggedIn){
        requireLogin();
        exit();
    }
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['accept_privacy']) && !isset($_POST['decline_privacy'])){
    $user_type = $_GET['type'] ?? null;
    $userId = $_POST['userid'] ?? null;
    $password = $_POST['password'] ?? null;
    $error = login($userId, $password, $user_type);
}

$user_type = $_GET['type'] ?? 'student';
if (!in_array($user_type, ['student', 'instructor', 'admin'])) {
    $user_type = 'student';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberSense - Your Gateway to Cybersecurity Awareness</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="index.css">

</head>
<body>
    <?php if(!$privacyAccepted): ?>
        <div class="privacy-overlay active" id="privacyOverlay">
            <div class="privacy-modal">
                <div class="privacy-header">
                    <h2><i class="fas fa-shield-alt" style="margin-right: 10px;"></i>Privacy Policy & Data Protection</h2>
                    <p>Please review our privacy policy before using CyberSense</p>
                </div>
                
                <div class="privacy-content">
                    <div class="privacy-notice">
                        <i class="fas fa-info-circle" style="margin-right: 8px; color: #2196F3;"></i>
                        <strong>Your privacy matters to us.</strong> Please take a moment to review how we collect, use, and protect your information.
                    </div>

                    <h3>Information We Collect</h3>
                    <p>When you use CyberSense, we collect the following types of information:</p>
                    <ul>
                        <li><strong>Account Information:</strong> Username, password (encrypted), user type (student/instructor/admin), and contact details provided during registration</li>
                        <li><strong>Academic Data:</strong> Quiz scores, learning progress, module completion status, and performance analytics</li>
                        <li><strong>Usage Information:</strong> Login timestamps, session duration, pages accessed, and system interaction logs</li>
                        <li><strong>Technical Data:</strong> IP address, browser type, device information, and cookies for session management</li>
                    </ul>
                    <h3>Data Storage and Security</h3>
                    <p>We implement robust security measures to protect your information:</p>
                    <ul>
                        <li>All passwords are encrypted using password hashing algorithms</li>
                        <li>Data is stored on secure servers within the a secured server</li>
                        <li>Access to personal data is restricted to authorized personnel only</li>
                        <li>Session data is automatically cleared when you log out or after inactivity</li>
                    </ul>

                    <h3>Information Sharing</h3>
                    <p>Your personal information is never sold, rented, or shared with third parties. Data sharing is limited to:</p>
                    <ul>
                        <li>Your assigned instructors for academic monitoring and support</li>
                        <li>University administrators for educational oversight and system management</li>
                        <li>Technical support staff for troubleshooting and maintenance purposes</li>
                        <li>Anonymized, aggregated data may be used for academic research and platform improvement</li>
                    </ul>
                    <h3>Cookies and Tracking</h3>
                    <p>CyberSense uses essential cookies to:</p>
                    <ul>
                        <li>Maintain your login session and remember your preferences</li>
                        <li>Ensure proper functionality of interactive learning modules</li>
                        <li>Provide security features and prevent unauthorized access</li>
                    </ul>
                    <h3>Updates to This Policy</h3>
                    <p>This privacy policy may be updated periodically to reflect changes in our practices or legal requirements. Users will be notified of significant changes through the platform or email. Continued use of CyberSense after policy updates constitutes acceptance of the revised terms.</p>

                    <h3>Contact Information</h3>
                    <p>For privacy-related questions, concerns, or requests, please contact:</p>
                    <ul>
                        <li><strong>Email:</strong> cybersense@usep.edu.ph</li>
                        <li><strong>Subject Line:</strong> Privacy Policy Inquiry</li>
                        <li><strong>Response Time:</strong> Within 5 business days</li>
                    </ul>

                    <div class="privacy-notice" style="margin-top: 30px;">
                        <i class="fas fa-graduation-cap" style="margin-right: 8px; color: #2196F3;"></i>
                        <strong>Educational Use:</strong> CyberSense is designed exclusively for educational purposes within USeP's cybersecurity awareness program. Your participation helps create a safer digital environment for our entire university community.
                    </div>
                </div>
                
                <div class="privacy-actions">
                    <form method="POST" style="display: contents;">
                        <button type="submit" name="accept_privacy" class="privacy-btn accept-btn">
                            <i class="fas fa-check"></i> Accept & Continue
                        </button>
                        <button type="submit" name="decline_privacy" class="privacy-btn decline-btn">
                            <i class="fas fa-times"></i> Decline
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="background-animation"></div>
    
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="assets/images/icons/usep.png" alt="USeP Logo">
                    <img src="assets/images/icons/cic.png" alt="CIC Logo">
                    <img src="assets/images/icons/cybersense.png" alt="CyberSense Logo">
                    <h1>CyberSense</h1>
                </div>
                <div class="nav-actions">
                    <?php if($isLoggedIn): ?>
                        <button class="login-btn" onclick="showAlreadyLoggedInModal()">
                            <i class="fas fa-sign-in-alt"></i>Login</button>
                    <?php else: ?>
                        <button class="login-btn" onclick="openLoginModal()">
                            <i class="fas fa-sign-in-alt"></i>Login</button>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to CyberSense!</h1>
                <p>Your Gateway to Cybersecurity Awareness and Digital Safety Education</p>
                <a href="#features" class="cta-button">Explore Our Mission</a>
            </div>
        </div>
    </section>

    <section class="main-content" id="features">
        <div class="container">
            <div class="features">
                <div class="feature-box">
                    <h2><i class="fas fa-shield-alt" style="margin-right: 10px; color: var(--accent);"></i>Our Purpose</h2>
                    <p>CyberSense is an interactive learning platform developed for the University of Southeastern Philippines. Its primary purpose is to educate students about data privacy, digital security, and safe online practices through interactive quizzes, engaging learning modules, and performance tracking.</p>
                    <p>In today's digital age, protecting your information is as vital as using it. CyberSense equips you with the tools and knowledge to stay secure, aware, and responsible online.</p>
                </div>
                
                <div class="feature-box key-points">
                    <h2><i class="fas fa-list-check" style="margin-right: 10px; color: var(--accent);"></i>Key Features</h2>
                    <ul>
                        <li>Educate students about cybersecurity fundamentals, threats, and protection techniques</li>
                        <li>Engage learners through quiz-based assessments tailored to reinforce learning</li>
                        <li>Track Progress and provide real-time feedback to enhance learning outcomes</li>
                        <li>Ensure Security with encrypted accounts, data privacy safeguards, and role-based access</li>
                        <li>Support Learning with structured, instructor-created modules and resources</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <?php if(!$isLoggedIn): ?>
        <div class="login-overlay" id="loginOverlay">
            <div class="login-modal">
                <button class="close-btn" onclick="closeLoginModal()">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="login-header">
                    <h2 id="loginTitle">Login to CyberSense</h2>
                    <p>Choose your role and enter your credentials</p>
                </div>
                
                <div class="user-type-selector">
                    <div class="user-type-btn active" onclick="selectUserType('student', this)">
                        <i class="fas fa-user-graduate"></i><br>Student
                    </div>
                    <div class="user-type-btn" onclick="selectUserType('instructor', this)">
                        <i class="fas fa-chalkboard-teacher"></i><br>Instructor
                    </div>
                    <div class="user-type-btn" onclick="selectUserType('admin', this)">
                        <i class="fas fa-user-shield"></i><br>Admin
                    </div>
                </div>
                
                    <form id="loginForm" method="POST" action="index.php?type=<?php echo htmlspecialchars($user_type); ?>">
                    <input type="hidden" id="userType" name="type" value="student">
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="userid" required placeholder="Enter your username">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" required placeholder="Enter your password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="error-message" id="errorMessage">
                        <?php echo isset($error) ? htmlspecialchars($error) : '' ?>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
                        Forgot Password? <a href="#" style="color: var(--primary);" onclick="contactSupport()">Contact Support</a>
                    </p>
                </form>
            </div>
        </div>
    <?php endif; ?>
    <div class="login-overlay" id="alreadyLoggedInOverlay">
        <div class="login-modal">
            <button class="close-btn" onclick="closeAlreadyLoggedInModal()"><i class="fas fa-times"></i></button>
            <div class="login-header">
                <h2><i class="fas fa-check-circle" style="color: #4CAF50; margin-right: 10px;"></i>Already Logged In</h2>
                <p>You are already logged in to CyberSense</p>
            </div>
            
            <div style="text-align: center; padding: 20px;">
                <p style="margin-bottom: 10px; font-size: 1.1rem;">Would you like to go to your dashboard?</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button onclick="redirectToDashboard()" class="submit-btn" style="background: #4CAF50; width: 150px; height: 50px;">
                        <i class="fas fa-tachometer-alt"></i> Dashboard</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-users" style="margin-right: 10px;"></i>CyberSense Team</h3>
                    <p>University of Southeastern Philippines</p>
                    <p>BSIT - IS 2A Students Project</p>
                </div>
                <div class="footer-section">
                    <h3><i class="fas fa-envelope" style="margin-right: 10px;"></i>Contact Information</h3>
                    <p>Email: cybersense@usep.edu.ph</p>
                </div>
                <div class="footer-section">
                    <h3><i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i>Address</h3>
                    <p>Iñigo St., Bo. Obrero</p>
                    <p>Davao City, 8000</p>
                    <p>Philippines</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 CyberSense. All Rights Reserved. University of Southeastern Philippines.</p>
            </div>
        </div>
    </footer>
    <script src="index.js"></script>
</body>
</html>