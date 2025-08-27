<?php
    session_start();
    require_once '../functions/student_function.php';
    require_once '../functions/module.php';
    require_once '../functions/session.php';
    requireLogin();
    $moduleId = $_GET['id'];
    $studentId =  $_SESSION['user_id'];
    $courseDetails = getCourseById($moduleId);
    $courses = getEnrolledCourse($_SESSION['user_id']);
    $students = getStudentByModuleID($moduleId);
    $grade = getStudentGradeByStudentId($moduleId, $studentId);
    $title = @$courseDetails['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($courseDetails['title'] ?? 'Course Details'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="details.css">
</head>
<body>
    <div class="container">
    <header class="top-nav">
    <img src="../assets/images/icons/profile.png" alt="User Profile" class="profile-image" style="width: 40px; height: 40px;"/>
      <nav class="main-menu">
        <a href="../index.php" class="<?php echo isActive("index.php") ?>">Home</a>
        <a href="index.php" class="<?php echo isActive("index.php") ?>">My courses</a>
      </nav>
      <span class="logout-container"><a href="#" class="logout-link" onclick="showLogoutModal()">Logout</a></span>
    </header>
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
        <main class="content">
            <?php if ($courseDetails): ?>
                <div class="course-header">
                    <h1><?php echo htmlspecialchars($courseDetails['title']); ?></h1>
                </div>

                <div class="tabs">
                    <button class="tab-btn active" data-tab="course">Course</button>
                    <button class="tab-btn" data-tab="participants">Participants</button>
                    <button class="tab-btn" data-tab="grade">Grade</button>
                    <button class="tab-btn" data-tab="more">Unenroll</button>
                </div>
                <div class="tab-content active" id="course">
                    <h2>Module Details</h2>
                    <div class="module-info">
                        <p><strong>Title:</strong> <span class="module-data"><?php echo htmlspecialchars($courseDetails['description']); ?></span></p>
                        <p><strong>Description:</strong> <span class="module-data"><?php echo htmlspecialchars($courseDetails['content_text']); ?></span></p>
                        <?php if (!empty($courseDetails['content_video_url'])): ?>
                        <p><strong>Video Link:</strong> <a href="<?php echo htmlspecialchars($courseDetails['content_video_url']); ?>" target="_blank" class="resource-link">Video Resource</a></p>
                        <?php endif; ?>
                        <?php if (!empty($courseDetails['content_pdf_path'])): ?>
                        <p><strong>PDF Link:</strong> <a href="<?php echo htmlspecialchars($courseDetails['content_pdf_path']); ?>" target="_blank" class="resource-link">PDF Document</a></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="tab-content" id="participants">
                    <h2>Participants</h2>
                    <?php echo generateStudentTable($students)?>
                </div>
                <div class="tab-content" id="grade">
                    <h2>Grade</h2>
                    <?php echo generateGradesTable($grade)?>
                </div>
                <div class="tab-content" id="more">
                    <h2>Unenroll from module</h2>
                    <div class="module-info">
                        <p>Are you sure you want to unenroll from <strong><?php echo htmlspecialchars($courseDetails['title']); ?></strong>?</p>
                        <p>Unenrolling will remove you from this module and you may lose access to any module materials.</p>
                        <div class="modal-actions" style="margin-top: 20px;">
                            <button type="button" class="confirm-btn" onclick="processUnenroll('<?php echo $moduleId; ?>', '<?php echo $_SESSION['user_id']; ?>')">Yes, Unenroll Me</button>
                            <button type="button" class="cancel-btn" onclick="resetTabs()">Cancel</button>
                        </div>
                    </div>
                </div>
                
                <div id="unenrollPopup" class="modal">
                    <div class="modal-content">
                        <p>Processing your request...</p>
                    </div>
                </div>
                
                <div class="collapsible">
                    <button class="collapsible-btn">Quizzes For <?php echo $title; ?></button>
                    <div class="collapsible-content" style="display: block;">
                        <?php $quizzes = getQuizzesByModuleId($moduleId); ?>
                        <?php echo generetaDropDownForQuizzes($quizzes, $studentId); ?>
                    </div>
                </div>
            <?php else: ?>
                <p>Course details not found.</p>
            <?php endif; ?>
            <a href="index.php" class="back-btn">Back to Courses</a>
        </main>
    </div>

<div class="circle-menu">
    <button class="circle-btn" id="openOverlay">☰</button>
</div>

<div class="side-overlay" id="sideOverlay">
    <button class="close-btn" onclick="closeOverlay()">×</button>
    <div class="overlay-content">
        <a href="../index.php">Home</a>
        <a href="index.php">My Courses</a>
    </div>
</div>
<script src="details.js"></script>  
</body>
</html>