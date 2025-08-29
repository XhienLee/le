<?php
    session_start();
    require_once '../functions/session.php';
    require_once '../functions/staff_function.php';
    require_once '../functions/module.php';
    requireLogin();
    $user = isset($_SESSION['full_name']) ? strtok($_SESSION['full_name'], " ") : "Instructor";
    $courses = getManageModule($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="instructor.css">
    <link rel="stylesheet" href="../assets/global/global.css">
</head>
<body>
    <div class="container">
        <header class="top-nav">
            <div class="profile-container">
                <img src="../assets/images/icons/profile.png" alt="User Profile" class="profile-image" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"/>
            </div>
            <nav class="main-menu">
                <a href="index.php" class="<?php echo isActive("dashboard.php") ?>">Home</a>
                <a href="index.php" class="<?php echo isActive("index.php") ?>">My courses</a>
            </nav>
            <span class="logout-container">
                <a href="#" class="change-password-link" onclick="showPasswordModal()">Change Password</a>
                <a href="#" class="logout-link" onclick="showLogoutModal()">Logout</a>
            </span>
        </header>
        <main class="content">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="notification success">
                    <?php 
                        echo $_SESSION['success_message']; 
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="notification error">
                    <?php 
                        echo $_SESSION['error_message']; 
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>
            <h1 class="welcome-text">Hi, <?php echo $user; ?>!</h1>
            <h2 class="subtitle">Module overview</h2>
            <hr class="separator" style="margin: 20px 0;">

            <div class="filter-controls">
                <input type="text" class="search-input" placeholder="Search Module">
                <button class="sort-btn">Sort by module name</button>
                <button class="create-module-btn" onclick="showCreateModuleModal()">+ Create Module</button>
            </div>
            <?php generateCards($courses); ?>
        </main>
    </div>
    <div class="circle-menu">
        <button class="circle-btn" id="openOverlay" onclick="openOverlay()">☰</button>
    </div>
    <div class="side-overlay" id="sideOverlay">
        <button class="close-btn" onclick="closeOverlay()">×</button>
        <div class="overlay-content">
           <a href="../index.php" class="<?php echo isActive("home.php") ?>">Home</a>
            <a href="index.php" class="<?php echo isActive("index.php") ?>">My courses</a>
        </div>
    </div>
    
    <!-- Logout Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>Are you sure you want to log out?</h2>
            <p>We hope to see you again soon!</p>
            <div class="modal-actions">
                <button class="cancel-btn" onclick="closeLogoutModal()">Cancel</button>
                <button class="confirm-btn" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <!-- Create Module Modal -->
    <div id="createModuleModal" class="modal">
        <div class="modal-content create-module-modal">
            <h2>Create New Module</h2>
            <form id="createModuleForm" action="create_module.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="moduleTitle">Module Title *</label>
                    <input type="text" id="moduleTitle" name="title" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <label for="moduleDescription">Description</label>
                    <textarea id="moduleDescription" name="description" rows="3" placeholder="Brief description of the module"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="moduleImage">Module Image</label>
                    <input type="file" id="moduleImage" name="module_image" accept="image/*">
                    <small>Upload an image for this module (JPG, PNG, GIF). If none selected, default image will be used.</small>
                    <div class="image-preview" id="imagePreview" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 150px; margin-top: 10px; border-radius: 4px;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="contentText">Content Text</label>
                    <textarea id="contentText" name="content_text" rows="5" placeholder="Module content text"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="contentVideo">Video URL</label>
                    <input type="url" id="contentVideo" name="content_video_url" placeholder="https://example.com/video.mp4">
                </div>
                
                <div class="form-group">
                    <label for="contentPdf">PDF File</label>
                    <input type="file" id="contentPdf" name="content_pdf" accept=".pdf">
                    <small>Upload a PDF file for this module (optional)</small>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeCreateModuleModal()">Cancel</button>
                    <button type="submit" class="confirm-btn">Create Module</button>
                </div>
            </form>
        </div>
    </div>
    <div id="passwordModal" class="password-modal">
        <div class="password-modal-content">
            <button class="password-close-btn" onclick="closePasswordModal()">&times;</button>
            <h2>Change Password</h2>
            
            <form id="passwordForm">
                <div class="password-form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="password-form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                
                <div class="password-form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="password-modal-actions">
                    <button type="button" class="password-btn password-cancel-btn" onclick="closePasswordModal()">Cancel</button>
                    <button type="submit" class="password-btn password-update-btn">Update Password</button>
                </div>
                
                <div id="passwordLoading" class="password-loading">
                    <div class="password-spinner"></div>
                    Updating password...
                </div>
            </form>
            
            <div id="passwordMessage"></div>
        </div>
    </div>
    <script src="instructor.js"></script>
    <script src="../assets/global/global.js"></script>
</body>
</html>