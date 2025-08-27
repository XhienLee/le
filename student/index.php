<?php
    session_start();
    require_once '../functions/session.php';
    require_once '../functions/student_function.php';
    require_once '../functions/module.php';
    require_once 'functions.php';
    requireLogin();
    $user = isset($_SESSION['full_name']) ? strtok($_SESSION['full_name'], " ") : "Student";
    $moduleInfos = getModule();;
    $studentId = $_SESSION['user_id'];
    foreach($moduleInfos as $moduleInfo){
        $result = checkAndEnrollStudent($moduleInfo['moduleId'], $studentId,);
    }
    $allCourse = getEnrolledCourse($_SESSION['user_id']);
    $courses = [];
    foreach($allCourse as $filtered){
        $courseInfo = getCourseById($filtered['moduleId']);
        if(@$courseInfo['status'] == 'active'){
            $courses[] = $filtered;
        }
    }
    $quizId = "popup";
    $showResultModal = false;
    if (isset($_GET['take_later'])) {
        $_SESSION['quiz_take_later'] = true;
        header("Location: index.php");
        exit;
    }
    if (isset($_GET['start_quiz'])) {
        unset($_SESSION['quiz_take_later']);
        header("Location: index.php");
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        unset($_SESSION['quiz_take_later']);
        $answers = $_POST['answers'] ?? [];
        $score = 0;
        $recordId = "rec_" . md5(uniqid(mt_rand(), true));
        $studentId = $_SESSION['user_id'];
        $quizInfo = getQuizInfo("popup");
        $allQuestion = $_SESSION['questions'];
        $totalQuestions = $quizInfo['total_questions'];
        foreach ($allQuestion as $question) {
            $questionId = $question['questionId'];
            $studentAnswerKey = $answers[$questionId] ?? '';
            $correctAnswerKey = $question['correct_answer'];
            $studentAnswerText = '';
            if (isset($question['answer_option']) && !empty($question['answer_option'])) {
                $options = json_decode($question['answer_option'], true);
                if (is_array($options) && isset($options[$studentAnswerKey])) {
                    $studentAnswerText = $options[$studentAnswerKey];
                } else {
                    $studentAnswerText = $studentAnswerKey;
                }
            } else {
                $studentAnswerText = $studentAnswerKey;
            }
            $isCorrect = (trim(strtolower($studentAnswerKey)) === trim(strtolower($correctAnswerKey)));
            if ($isCorrect) {
                $score++;
            }
            $answerJson[] = json_encode([$quizId => $studentAnswerText]);
        }
        $passed = $score >= $quizInfo['passing_score'];
        $feedback = $passed ? 'Congratulations! You passed the quiz.' : 'You did not meet the passing score. Please try again.';
        $status = $passed ? 'passed' : 'failed';
        saveQuizAttempt($recordId, $quizId, $studentId, $score, $answerJson, $status);
        saveQuizResult($quizId, $studentId, $quizId, $score, $totalQuestions, $feedback, $recordId);
        $showResultModal = true;
    }
    
    $defaultQuiz = getStudentGradeByQuizID($_SESSION['user_id'], $quizId);
    $shouldShowQuiz = false;
    if (!isset($defaultQuiz['status'])) {
        if (!isset($_SESSION['quiz_take_later'])) {
            $shouldShowQuiz = true;
        }
    } elseif ($defaultQuiz['status'] == 'failed') {
        if (!isset($_SESSION['quiz_take_later'])) {
            $shouldShowQuiz = true;
        }
    }
    if ($shouldShowQuiz) {
        $startQuiz = true;
        $quizInfo = getQuizInfo("popup");
        $totalQuestion = $quizInfo['total_questions'];
        $allQuestion = getQuizQuestions($quizId);
        shuffle($allQuestion);
        $questions = array_slice($allQuestion, 0, $totalQuestion);
        $description = $quizInfo['description'];
        $_SESSION['questions'] = $questions;
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student.css">
    <link rel="stylesheet" href="../assets/global/global.css">
</head>
<body>
    <div class="container">
        <header class="top-nav">
            <img src="../assets/images/icons/profile.png" alt="User Profile" class="profile-image" style="width: 40px; height: 40px;"/>
            <nav class="main-menu">
                <a href="../index.php">Home</a>
                <a href="index.php" class="<?php echo isActive("index.php") ?>">My courses</a>
            </nav>
            
            <span class="logout-container">
                <a href="#" class="change-password-link" onclick="showPasswordModal()">Change Password</a>
                <a href="#" class="logout-link" onclick="showLogoutModal()">Logout</a>
            </span>
        </header>
        <main class="content">
            <h1 class="welcome-text">Hi, <?php echo $user; ?>!</h1>
            <h2 class="subtitle">Module overview</h2>
            <hr class="separator">
            
            <?php if(isset($_SESSION['quiz_take_later'])): ?>
                <div class="quiz-reminder" style="background: #f0f8ff; border: 1px solid #0066cc; padding: 15px; margin: 20px 0; border-radius: 5px;">
                    <h3 style="color: #0066cc; margin: 0 0 10px 0;">Quiz Reminder</h3>
                    <p style="margin: 0 0 15px 0;">You have a pending quiz that you chose to take later.</p>
                    <a href="index.php?start_quiz=1" style="background: #0066cc; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Start Quiz Now</a>
                </div>
            <?php endif; ?>
            
            <div class="filter-controls">
            <div class="dropdown">
                <button class="dropdown-btn">All</button>
                <div class="dropdown-menu">
                <div class="dropdown-item" data-filter="all">All</div>
                <div class="dropdown-item" data-filter="in-progress">In progress</div>
                <div class="dropdown-item" data-filter="completed">Completed</div>
                </div>
            </div>
            <input type="text" class="search-input" placeholder="Search Module">
            <button class="sort-btn">Sort by module name</button>
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
           <a href="../index.php">Home</a>
            <a href="index.php" class="<?php echo isActive("index.php") ?>">My courses</a>
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
    <?php if(isset($startQuiz) && $startQuiz && !$showResultModal): ?>
    <div id="quizModal" class="modal" style="display: block;">
        <div class="modal-content quiz-modal-content">
            <div class="quiz-header">
                <h2 id="quizTitle"><?php echo $description; ?></h2>
                <div class="quiz-progress">
                    <span class="progress-text">Question <span id="currentQuestionNum">1</span> of <span id="totalQuestions"><?php echo count($questions); ?></span></span>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <span class="progress-percent" id="progressPercent">0%</span>
                </div>
                <div class="question-indicator" id="questionIndicator"></div>
            </div>
            <form method="post" id="quizForm">
                <div id="questionsContainer">
                    <?php if (empty($questions)): ?>
                        <div class="no-questions">
                            <p>No questions available for this quiz.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="question-container" data-question="<?php echo $index; ?>" data-question-id="<?php echo $question['questionId']; ?>" style="<?php echo $index === 0 ? 'display: block;' : 'display: none;'; ?>">
                                <div class="question-header">
                                    <div class="question-number">Question <?php echo $index + 1; ?> of <?php echo count($questions); ?></div>
                                    <h3 class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></h3>
                                </div>
                                <div class="quiz-options">
                                    <?php
                                        $options = [];
                                        if (isset($question['answer_option']) && !empty($question['answer_option'])) {
                                            $options = json_decode($question['answer_option'], true);
                                            if (!is_array($options)) {
                                                $options = [];
                                            }
                                        }
                                        if (empty($options) && $question['question_type'] === 'true_false') {
                                            $options = ['a' => 'True', 'b' => 'False'];
                                        } elseif (empty($options) && isset($question['correct_answer'])) {
                                            $options = ['a' => $question['correct_answer']];
                                        }
                                        $shuffledOptions = [];
                                        if (!empty($options)) {
                                            $keys = array_keys($options);
                                            shuffle($keys);
                                            foreach ($keys as $key) {
                                                $shuffledOptions[$key] = $options[$key];
                                            }
                                            $options = $shuffledOptions;
                                        }
                                    ?>
                                    <?php if (!empty($options)): ?>
                                        <?php foreach ($options as $key => $optionText): ?>
                                            <label class="quiz-option">
                                                <input type="radio" name="answers[<?php echo $question['questionId']; ?>]" 
                                                       value="<?php echo htmlspecialchars($key); ?>" required>
                                                <span class="quiz-option-text"><?php echo htmlspecialchars($optionText); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="no-options">No answer options available for this question.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="quiz-navigation">
                    <div class="nav-center">
                        <button type="button" class="later-btn" onclick="laterQuiz()">Take Later</button>
                    </div>
                    <button type="button" class="nav-btn prev-btn" id="prevBtn" disabled>Previous</button>
                    <button type="button" class="nav-btn next-btn" id="nextBtn">Next</button>
                    <button type="submit" name="submit_quiz" class="nav-btn submit-btn" id="submitBtn" style="display: none;">Submit Quiz</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
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
    <script src="student.js"></script>
    <script src="../assets/global/global.js"></script>
    <script>
        <?php if($showResultModal): ?>
            const quizData = {
                title: '<?php echo isset($quizInfo["title"]) ? addslashes($quizInfo["title"]) : "Quiz"; ?>',
                description: '<?php echo isset($quizInfo["description"]) ? addslashes($quizInfo["description"]) : ""; ?>',
                totalQuestions: <?php echo isset($quizInfo["total_questions"]) ? $quizInfo["total_questions"] : 0; ?>,
                passingScore: <?php echo isset($quizInfo["passing_score"]) ? $quizInfo["passing_score"] : 70; ?>,
                userScore: <?php echo isset($score) ? $score : 0; ?>,
                feedback: '<?php echo isset($feedback) ? addslashes($feedback) : ""; ?>',
                passed: <?php echo isset($passed) ? ($passed ? 'true' : 'false') : 'false'; ?>,
                moduleId: '<?php echo isset($quizInfo["moduleId"]) ? $quizInfo["moduleId"] : ""; ?>'
            };
            document.addEventListener('DOMContentLoaded', function() {
                showResultModal(quizData);
            });
        <?php endif; ?>
    </script>
</body>
</html>