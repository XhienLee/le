<?php
    session_start();
    require_once '../functions/staff_function.php';
    require_once '../functions/module.php';
    require_once '../functions/session.php';
    requireLogin();
    $moduleId = isset($_GET['id']) ? $_GET['id'] : null;
    if (!$moduleId) {
        header('Location: index.php');
        exit();
    }
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'course';
    $studentId = $_SESSION['user_id'];
    $courseDetails = getCourseById($moduleId); 
    $enrolledStudent = getStudentByModuleID($moduleId);
    foreach($enrolledStudent as $student){
        $students[] = getStudentById($student['studentId']);
    }
    $grade = getStudentGradeByStudentId($moduleId, $studentId);
    $title = isset($courseDetails['title']) ? $courseDetails['title'] : 'Course Not Found';
    $description = isset($courseDetails['description']) ? $courseDetails['description'] : '';
    $content_text = isset($courseDetails['content_text']) ? $courseDetails['content_text'] : '';
    $content_video_url = isset($courseDetails['content_video_url']) ? $courseDetails['content_video_url'] : '';
    $content_pdf_path = isset($courseDetails['content_pdf_path']) ? $courseDetails['content_pdf_path'] : '';
    $success_message = isset($_GET['success_message']) ? htmlspecialchars($_GET['success_message']) : '';
    $error_message = isset($_GET['error_message']) ? htmlspecialchars($_GET['error_message']) : '';
     if ($_POST) {
        if (isset($_POST['action']) && $_POST['action'] === 'update_module') {
            $pdfPath = $content_pdf_path;
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handlePdfUpload($moduleId);
                if ($uploadResult['status']) {
                    if (!empty($content_pdf_path) && file_exists($content_pdf_path)) {
                        unlink($content_pdf_path);
                    }
                    $pdfPath = $uploadResult['path'];
                } else {
                    $error_message = $uploadResult['message'];
                }
            }
            
            if (empty($error_message)) {
                $result = updateModule($moduleId, $_POST['title'], $_POST['description'], $_POST['content_text'], $_POST['video_url'], $pdfPath);
                if ($result['status']) {
                    $courseDetails = getCourseById($moduleId);
                    $title = $courseDetails['title'];
                    $description = $courseDetails['description'];
                    $content_text = $courseDetails['content_text'];
                    $content_video_url = $courseDetails['content_video_url'];
                    $content_pdf_path = $courseDetails['content_pdf_path']; 
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
            }
        }
    }
    function getFileName($path) {
        if (empty($path)) return '';
        return basename($path);
    }
    function fileExists($path) {
        return !empty($path) && file_exists($path);
    }
    function handlePdfUpload($moduleId) {
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/pdf/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = $_FILES['pdf_file']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if ($fileExtension !== 'pdf') {
                return ['status' => false, 'message' => 'Only PDF files are allowed.'];
            }
            $newFileName = $moduleId . '_' . time() . '.pdf';
            $uploadPath = $uploadDir . $newFileName;
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $uploadPath)) {
                return ['status' => true, 'path' => $uploadPath];
            } else {
                return ['status' => false, 'message' => 'Failed to upload file.'];
            }
        }
        return ['status' => false, 'message' => 'No file uploaded or upload error.'];
    }
    $studentGrades = getStudentGradeByModuleId($moduleId);
    $quizzes = getQuizzesByModuleId($moduleId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details - <?php echo htmlspecialchars($title); ?></title>
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
            <div class="course-header">
                <h1><?php echo htmlspecialchars($title); ?></h1>
            </div>

            <div class="alert alert-success" style="<?php echo $success_message ? 'display: block;' : 'display: none;'; ?>" id="successAlert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
            <div class="alert alert-error" style="<?php echo $error_message ? 'display: block;' : 'display: none;'; ?>" id="errorAlert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            <div id="toast" class="toast hidden"></div>
            <div class="tabs">
                <div class="tabs-left">
                    <button class="tab-btn <?php echo ($tab === 'course') ? 'active' : ''; ?>" data-tab="course">Course</button>
                    <button class="tab-btn <?php echo ($tab === 'quizzes') ? 'active' : ''; ?>" data-tab="quizzes">Quizzes</button>
                    <button class="tab-btn <?php echo ($tab === 'participants') ? 'active' : ''; ?>" data-tab="participants">Participants</button>
                    <button class="tab-btn <?php echo ($tab === 'grade') ? 'active' : ''; ?>" data-tab="grade">Grade</button>
                    <button class="tab-btn <?php echo ($tab === 'enroll') ? 'active' : ''; ?>" data-tab="enroll">Enroll Student</button>
                </div>
                <div class="tabs-right">
                    <button class="add-quiz-btn" id="addQuizBtn" onclick="createNewQuiz('<?php echo htmlspecialchars($moduleId); ?>')"> 
                        <i class="fas fa-add"></i> Create Quiz
                    </button>
                    <button class="edit-module-btn" id="editModuleBtn" onclick="toggleEdit()">
                        <i class="fas fa-edit"></i> Edit Module Info
                    </button>
                    <button class="delete-module-btn" id="deleteModuleBtn" onclick="confirmDeleteModule()">
                        <i class="fas fa-trash"></i> Delete Module
                    </button>
                </div>
            </div>
            <div class="tab-content <?php echo ($tab === 'course') ? 'active' : ''; ?>" id="course">
                <h2>Module Details</h2>
                <form method="POST" class="module-form" id="moduleForm">
                    <input type="hidden" name="action" value="update_module">
                    
                    <div class="form-group">
                        <label for="title"><strong>Title:</strong></label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" class="form-input" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><strong>Description:</strong></label>
                        <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($description); ?>" class="form-input" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="content_text"><strong>Content:</strong></label>
                        <textarea id="content_text" name="content_text" rows="4" class="form-textarea" readonly><?php echo htmlspecialchars($content_text); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="video_url"><strong>Video URL:</strong></label>
                        <input type="url" id="video_url" name="video_url" value="<?php echo htmlspecialchars($content_video_url); ?>" class="form-input" readonly>
                    </div>
                    
                    <div class="form-group">
                    <label for="pdf_display"><strong>PDF File:</strong></label>
                    <div id="pdf_display_section">
                        <?php if (!empty($content_pdf_path)): ?>
                            <?php if (fileExists($content_pdf_path)): ?>
                                <div class="pdf-display">
                                    <a href="<?php echo htmlspecialchars($content_pdf_path); ?>" target="_blank" class="pdf-link">
                                        <i class="fas fa-file-pdf"></i> <?php echo htmlspecialchars(getFileName($content_pdf_path)); ?>
                                    </a>
                                    <span class="file-info">(Click to view PDF)</span>
                                </div>
                            <?php else: ?>
                                <div class="pdf-display error">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    File not found: <?php echo htmlspecialchars(getFileName($content_pdf_path)); ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="pdf-display">
                                <span class="no-file">No PDF file uploaded</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div id="pdf_upload_section" style="display: none;">
                        <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" class="form-input file-input">
                        <small class="file-help">Choose a PDF file to replace the current file</small>
                    </div>
                    <input type="hidden" name="current_pdf_path" value="<?php echo htmlspecialchars($content_pdf_path); ?>">
                </div>
                    
                    <div class="form-actions" id="formActions">
                        <button type="submit" class="save-btn">Save Changes</button>
                        <button type="button" class="cancel-btn" onclick="cancelEdit()">Cancel</button>
                    </div>
                </form>
            </div>
            <div class="tab-content <?php echo ($tab === 'quizzes') ? 'active' : ''; ?>" id="quizzes">
                <h2>Quizzes</h2>
                <?php
                if (!empty($quizzes)){
                    echo generetaDropDownForQuizzes($quizzes, $moduleId);
                } else{
                    echo "<p>No Quizz for this module yet.</p>";
                }
                ?>  
            </div>
            <div class="tab-content <?php echo ($tab === 'participants') ? 'active' : ''; ?>" id="participants">
                <h2>Participants</h2>
                <?php
                if (!empty($students)){
                    echo generateStudentTable($students);
                } else{
                    echo "<p>No participants enrolled in this module yet.</p>";
                }
                ?>  
            </div>
            
            <div class="tab-content <?php echo ($tab === 'grade') ? 'active' : ''; ?>" id="grade">
                <h2>Grade</h2>
                <?php echo generateGradesTable($studentGrades); ?>
            </div>
            
            <div class="tab-content <?php echo ($tab === 'enroll') ? 'active' : ''; ?>" id="enroll">
                <h2>Enroll Student</h2>
                <div class="enroll-section">
                    <div class="search-section">
                        <div class="form-group">
                            <label for="student_id">Student ID:</label>
                            <div class="search-input-group">
                                <input type="text" id="student_id" name="student_id" placeholder="Enter student ID" class="form-input">
                                <button type="button" onclick="searchStudent()" class="search-btn">Search</button>
                                <button type="button" onclick="clearSearch()" class="clear-btn">Clear</button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="studentResult" style="display: none;">
                        <h3>Student Information</h3>
                        <div id="studentDetails"></div>
                        <button type="button" onclick="enrollStudent()" class="enroll-btn" id="enrollBtn">Enroll Student</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- delete module Popup -->
    <div id="deleteConfirmModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-title">Confirm Module Deletion</div>
            <div class="modal-message">
                <p><strong>Warning:</strong> This action cannot be undone!</p>
                <p>Deleting this module will:</p>
                <ul style="text-align: left; margin: 15px 0; padding-left: 20px;">
                    <li>Permanently remove all module content</li>
                    <li>Unenroll all students from this module</li>
                    <li>Delete all associated grades and progress</li>
                    <li>Remove all related assignments and materials</li>
                </ul>
                <p>Are you absolutely sure you want to delete "<strong id="moduleTitle">Advanced Web Development</strong>"?</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="confirm-btn" onclick="deleteModule()">
                    <i class="fas fa-trash"></i> Yes, Delete Module
                </button>
                <button type="button" class="cancel-btn" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>
    <!-- Enrollment Popup -->
    <div id="enrollPopup" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-title">Enrolling Student...</div>
            <p>Please wait while we process the enrollment.</p>
        </div>
    </div>
    
    <!-- UnEnrollment Popup -->
    <div id="unenrollPopup" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-title">Confirm Unenrollment</div>
            <p>Are you sure you want to unenroll this student from the module?</p>
            <div class="modal-actions">
                <button type="button" class="confirm-btn">Yes, Unenroll</button>
                <button type="button" class="cancel-btn" onclick="closeUnenrollPopup()">Cancel</button>
            </div>
        </div>
    </div>
    <script src="details.js"></script>
    <script>
        <?php if ($success_message): ?>
            setTimeout(() => {
                hideAlerts();
            }, 5000);
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            setTimeout(() => {
                hideAlerts();
            }, 8000);
        <?php endif; ?>
    window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');

    if (message) {
        const toast = document.getElementById('toast');
        toast.textContent = decodeURIComponent(message);
        toast.classList.remove('hidden');
        setTimeout(() => {
        toast.classList.add('hidden');
        }, 3000);
    }
    };
</script>

</body>
</html>