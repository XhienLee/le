<?php
    session_start();
    require_once '../functions/session.php';
    require_once '../functions/student_function.php';
    require_once '../functions/module.php';
    requireLogin();
    $user = isset($_SESSION['full_name']) ? strtok($_SESSION['full_name'], " ") : "Admin";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="../assets/global/global.css">
    <title>Admin Dashboard</title>
</head>

<body data-admin-id="<?php echo $_SESSION['user_id'] ?? ''; ?>">
    <div class="container">
        <header class="top-nav">
            <img src="../assets/images/icons/profile.png" alt="User Profile" class="profile-image" style="width: 40px; height: 40px;"/>
            <nav class="main-menu">
                <a href="../index.php">Home</a>
                <a href="index.php" class="<?php echo isActive("index.php") ?>">Dashboard</a>
                <a href="report.php" class="<?php echo isActive("report.php") ?>">Users Logs</a>
            </nav>
            <span class="logout-container">
                <a href="#" class="change-password-link" onclick="showPasswordModal()">Change Password</a>
                <a href="#" class="logout-link" onclick="showLogoutModal()">Logout</a>
            </span>
        </header>

        <main class="content">
            <h1 class="welcome-text">Welcome, <?php echo $user; ?>!</h1>
            <h2 class="subtitle">Management overview</h2>
            <hr class="separator">

            <div id="contentContainer">
                <div class="button-container">
                    <button id="addStudent">Add Users</button>
                    <button id="manageStudent" class="active">Manage Users</button>
                </div>

                <!-- Add Users -->
                <div class="box-container" id="addUsersSection">
                    <div class="user-type-selector">
                        <button class="add-type-btn active" data-type="students">Add Student</button>
                        <button class="add-type-btn" data-type="instructors">Add Instructor</button>
                        <button class="add-type-btn" data-type="admins">Add Admin</button>
                    </div>

                    <div class="user-section active" id="student-section">
                        <div class="import-section">
                            <div class="import-header">
                                <h2 class="import-title">Student Data</h2>
                            </div>
                            <div class="drop-area" id="student-drop-area">
                                <div class="download-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12.0 15 17.0 10"></polyline>
                                        <line x1="12.0" y1="15" x2="12.0" y2="3"></line>
                                    </svg>
                                </div>
                                <p class="drop-text">Drag & drop your CSV file here or click to browse</p>
                                <div class="file-info" style="display: none;">
                                    <p>Selected file: <span class="filename">No file selected</span></p>
                                </div>
                                <input type="file" class="file-input" accept=".csv">
                            </div>

                            <div class="buttons">
                                <button type="button" class="btn btn-primary import-btn">Import</button>
                                <button type="button" class="btn btn-secondary manual-btn">Manual</button>
                            </div>

                            <div class="results-area" id="student-results-area"></div>
                        </div>
                    </div>

                    <div class="user-section" id="instructor-section">
                        <div class="import-section">
                            <div class="import-header">
                                <h2 class="import-title">Instructor Data</h2>
                            </div>

                            <div class="drop-area" id="instructor-drop-area">
                                <div class="download-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12.0 15 17.0 10"></polyline>
                                        <line x1="12.0" y1="15" x2="12.0" y2="3"></line>
                                    </svg>
                                </div>
                                <p class="drop-text">Drag & drop your CSV file here or click to browse</p>
                                <div class="file-info" style="display: none;">
                                    <p>Selected file: <span class="filename">No file selected</span></p>
                                </div>
                                <input type="file" class="file-input" accept=".csv">
                            </div>

                            <div class="buttons">
                                <button type="button" class="btn btn-primary import-btn">Import</button>
                                <button type="button" class="btn btn-secondary manual-btn">Manual</button>
                            </div>

                            <div class="results-area" id="instructor-results-area"></div>
                        </div>
                    </div>

                    <div class="user-section" id="admin-section">
                        <div class="import-section">
                            <div class="import-header">
                                <h2 class="import-title">Admin Data</h2>
                            </div>

                            <div class="drop-area" id="admin-drop-area">
                                <div class="download-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12.0 15 17.0 10"></polyline>
                                        <line x1="12.0" y1="15" x2="12.0" y2="3"></line>
                                    </svg>
                                </div>
                                <p class="drop-text">Drag & drop your CSV file here or click to browse</p>
                                <div class="file-info" style="display: none;">
                                    <p>Selected file: <span class="filename">No file selected</span></p>
                                </div>
                                <input type="file" class="file-input" accept=".csv">
                            </div>

                            <div class="buttons">
                                <button type="button" class="btn btn-primary import-btn">Import</button>
                                <button type="button" class="btn btn-secondary manual-btn">Manual</button>
                            </div>

                            <div class="results-area" id="admin-results-area"></div>
                        </div>
                    </div>
                </div>

                <!-- Manage Users -->
                <div class="box-container" id="manageUsersSection" style="display:none;">
                    <div class="user-type-selector">
                        <button class="manage-type-btn active" data-type="students">Manage Student</button>
                        <button class="manage-type-btn" data-type="instructors">Manage Instructor</button>
                        <button class="manage-type-btn" data-type="admins">Manage admin</button>
                    </div>
                    <div class="tab-content">
                        <div id="students" class="tab-pane active">
                            <div class="search-container">
                                <input type="text" id="studentSearch" placeholder="Search students...">
                                <button id="studentSearchBtn">Search</button>
                            </div>
                            <div class="data-container" id="studentsData">
                            </div>
                            <div class="pagination" id="studentsPagination"></div>
                        </div>

                        <div id="instructors" class="tab-pane">
                            <div class="search-container">
                                <input type="text" id="instructorSearch" placeholder="Search instructors...">
                                <button id="instructorSearchBtn">Search</button>
                            </div>
                            <div class="data-container" id="instructorsData">
                            </div>
                            <div class="pagination" id="instructorsPagination"></div>
                        </div>

                        <div id="admins" class="tab-pane">
                            <div class="search-container">
                                <input type="text" id="adminSearch" placeholder="Search admin...">
                                <button id="adminSearchBtn">Search</button>
                            </div>
                            <div class="data-container" id="adminsData">
                            </div>
                            <div class="pagination" id="adminsPagination"></div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </main>
    </div>

    <div id="loader" class="loader">Loading...</div>
    <div id="toast" class="toast"></div>
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 class="modal-title">Edit Details</h2>
            <div class="modal-body" id="editModalBody">
            </div>
            <div class="modal-footer">
                <button class="cancel-btn" id="closeEditModal">Cancel</button>
                <button class="save-btn" id="saveEditModal">Save Changes</button>
            </div>
        </div>
    </div>
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
    <script>
        window.sessionData = {
            adminId: <?php echo json_encode($_SESSION['user_id']); ?>
        };
        function showLogoutModal() {
            const modal = document.getElementById("logoutModal");
            modal.style.display = "flex";
            modal.style.animation = "fadeIn 0.3s ease-in-out";
        }

        function closeLogoutModal() {
            const modal = document.getElementById("logoutModal");
            modal.style.display = "none";
        }

        function confirmLogout() {
            window.location.href = "../logout.php";
        }
    </script>
    <script src="add_user.js"></script>
    <script src="manage_user.js"></script>
    <script src="../assets/global/global.js"></script>
</body>
</html>