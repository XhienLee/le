<?php
session_start();
require_once 'functions.php';
require_once '../functions/module.php';
require_once '../functions/session.php';
requireLogin();
$quizId = $_GET['id'] ?? null;
$resultId = $_GET['result'] ?? null;
$studentId = $_SESSION['user_id'];
if (!$quizId) {
    header('Location: dashboard.php');
    exit;
}
$quizInfo = getQuizInfo($quizId);
if (empty($quizInfo)) {
    header('Location: dashboard.php');
    exit;
}
$moduleId = $quizInfo['moduleId'];
$totalQuestion = $quizInfo['total_questions'];
$enrolledCourses = getEnrolledCourse($studentId);
$isEnrolled = false;
foreach ($enrolledCourses as $course) {
    if ($course['moduleId'] == $moduleId) {
        $isEnrolled = true;
        break;
    }
}
if (!$isEnrolled) {
    header('Location: dashboard.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $questions = $_SESSION['questions'] ?? [];
    $answers = $_POST['answers'] ?? [];
    $score = 0;
    $totalQuestions = count($questions);
    $recordId = "rec_" . md5(uniqid(mt_rand(), true));
    $answerJson = [];
    foreach ($questions as $question) {
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
        $answerJson[] = json_encode([
            'questionId' => $questionId,
            'studentAnswer' => $studentAnswerText,
            'correctAnswer' => $correctAnswerKey,
            'isCorrect' => $isCorrect
        ]);
    }
    
    $passed = $score >= $quizInfo['passing_score'];
    $feedback = $passed ? 'Congratulations! You passed the quiz.' : 'You did not meet the passing score. Please try again.';
    $status = $passed ? 'passed' : 'failed';
    saveQuizAttempt($recordId, $quizId, $studentId, $score, $answerJson, $status);
    saveQuizResult($quizId, $studentId, $moduleId, $score, $totalQuestions, $feedback, $recordId);
    unset($_SESSION['questions']);
    header("Location: quiz.php?id=$quizId&result=1");
    exit;
}

if ($resultId) {
    $quizResult = getQuizResult($quizId, $studentId);
    $showResults = true;
} else {
    $allQuestion = getQuizQuestions($quizId);
    if (!isset($_SESSION['questions']) || empty($_SESSION['questions'])) {
        shuffle($allQuestion);
        $_SESSION['questions'] = array_slice($allQuestion, 0, $totalQuestion);
    }
    $questions = $_SESSION['questions'];
    $showResults = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quizInfo['title']); ?> - <?php echo $showResults ? 'Results' : 'Quiz'; ?></title>
    <link rel="stylesheet" href="quiz.css">
</head>
<body>
    <div class="container">
        <main class="content">
            
            <?php if ($showResults): ?>
                <div class="result-header">
                    <h1><?php echo htmlspecialchars($quizInfo['title']); ?> - Results</h1>
                    <p><?php echo htmlspecialchars($quizInfo['description'] ?? ''); ?></p>
                </div>

                <div class="result-content">
                    <?php if (!empty($quizResult)): ?>
                        <?php 
                            $scorePercentage = ($quizResult['grades'] / $quizInfo['total_questions']) * 100;
                            $passed = $scorePercentage >= $quizInfo['passing_score'];
                        ?>
                        <div class="result-score">
                            Your Score: <span class="<?php echo $passed ? 'result-passed' : 'result-failed'; ?>">
                                <?php echo htmlspecialchars($quizResult['grades']); ?> / <?php echo htmlspecialchars($quizInfo['total_questions']); ?>
                                (<?php echo number_format($scorePercentage, 1); ?>%)
                            </span>
                        </div>
                        
                        <div class="result-feedback">
                            <h3>Feedback:</h3>
                            <p><?php echo htmlspecialchars($quizResult['feedback']); ?></p>
                        </div>
                        
                        <?php if (!$passed): ?>
                            <div class="result-info">
                                <p>The passing score is <?php echo htmlspecialchars($quizInfo['passing_score']); ?>%. You may retake the quiz to improve your score.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>No quiz results found. Please complete the quiz first.</p>
                    <?php endif; ?>
                    
                    <div class="button-group">
                        <a href="details.php?id=<?php echo $moduleId; ?>" class="back-btn">Back to Course</a>
                        <?php if (!empty($quizResult) && !$passed): ?>
                            <a href="quiz.php?id=<?php echo $quizId; ?>" class="retry-btn">Retake Quiz</a>
                        <?php endif; ?>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="quiz-header">
                    <h1><?php echo htmlspecialchars($quizInfo['title']); ?></h1>
                    <p><?php echo htmlspecialchars($quizInfo['description'] ?? ''); ?></p>
                    <div class="quiz-info">
                        <p><strong>Total Questions:</strong> <?php echo htmlspecialchars($quizInfo['total_questions']); ?></p>
                        <p><strong>Passing Score:</strong> <?php echo htmlspecialchars($quizInfo['passing_score']); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($quizInfo['duration_minutes']); ?> minutes</p>
                    </div>
                    <div class="timer" id="timer">Time Remaining: <?php echo htmlspecialchars($quizInfo['duration_minutes']); ?>:00</div>
                </div>

                <?php if (empty($questions)): ?>
                    <p>No questions available for this quiz.</p>
                <?php else: ?>
                    <form method="post" id="quizForm">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="quiz-question">
                                <h3>Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question_text']); ?></h3>
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
                                                <?php echo htmlspecialchars($optionText); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No answer options available for this question.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="quiz-submit">
                            <button type="submit" name="submit_quiz" class="submit-btn">Submit Quiz</button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <a href="details.php?id=<?php echo $moduleId; ?>" class="back-btn">Back to Course</a>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const durationMinutes = <?php echo $quizInfo['duration_minutes']; ?>;
                        const quizId = <?php echo (int)$quizId; ?>;
                        const timerKey = `quizRemainingTime_${quizId}`;
                        let totalSeconds = durationMinutes * 60;
                        const timerElement = document.getElementById('timer');
                        const quizForm = document.getElementById('quizForm');
                        
                        if (!quizForm) return;
                        
                        const savedTime = sessionStorage.getItem(timerKey);
                        if (savedTime && parseInt(savedTime) > 0) {
                            totalSeconds = parseInt(savedTime);
                        } else {
                            sessionStorage.removeItem(timerKey);
                        }
                        updateTimerDisplay();
                        const timerInterval = setInterval(function() {
                            totalSeconds--;
                            if (totalSeconds <= 0) {
                                clearInterval(timerInterval);
                                sessionStorage.removeItem(timerKey);
                                alert('Time is up! Your quiz will be submitted automatically.');
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'submit_quiz';
                                hiddenInput.value = '1';
                                quizForm.appendChild(hiddenInput);
                                quizForm.submit();
                                return;
                            }
                            updateTimerDisplay();
                            sessionStorage.setItem(timerKey, totalSeconds);
                            if (totalSeconds === 60) {
                                timerElement.style.color = '#ff0000';
                                alert('1 minute remaining!');
                            }
                        }, 1000);
                        
                        function updateTimerDisplay() {
                            const minutes = Math.floor(totalSeconds / 60);
                            const seconds = totalSeconds % 60;
                            timerElement.textContent = `Time Remaining: ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
                        }
                        
                        let isSubmitting = false;
                        quizForm.addEventListener('submit', function() {
                            isSubmitting = true;
                            clearInterval(timerInterval);
                            sessionStorage.removeItem(timerKey);
                        });
                        
                        window.addEventListener('beforeunload', function() {
                            if (!isSubmitting) {
                                sessionStorage.setItem(timerKey, totalSeconds);
                            }
                        });
                        
                        window.addEventListener('pagehide', function() {
                            if (isSubmitting) {
                                sessionStorage.removeItem(timerKey);
                            }
                        });
                    });
                </script>
                
            <?php endif; ?>
        </main>
    </div>
</body>
</html>