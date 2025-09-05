<?php
session_start();
require_once '../functions/functions.php';
require_once '../functions/module.php';
require_once '../functions/session.php';

requireLogin();

$recordId = $_GET['recordId'] ?? null;
$studentId = $_GET['studentId'] ?? null;
if (!$recordId) {
    header('Location: index.php');
    exit;
}

$detailedAnswers = [];
$quizQuestions = [];
$showDetails = true;

$quizAttempt = getQuizAttempt($recordId);
if (empty($quizAttempt) || !isset($quizAttempt[0]['student_answer'])) {
    header('Location: index.php');
    exit;
}

$quizId = $quizAttempt[0]['quizId'];
$quizInfo = getQuizInfo($quizId);
if (empty($quizInfo)) {
    header('Location: index.php');
    exit;
}

$moduleId = $quizInfo['moduleId'];
$quizResult = getQuizResult($quizId, $studentId);

if (empty($quizResult)) {
    header('Location: index.php');
    exit;
}

$attemptData = $quizAttempt[0];
$studentAnswers = json_decode($attemptData['student_answer'], true);

if (is_array($studentAnswers)) {
    foreach ($studentAnswers as $answerJsonString) {
        if (is_string($answerJsonString)) {
            $answer = json_decode($answerJsonString, true);
        } else {
            $answer = $answerJsonString;
        }
        if ($answer && isset($answer['questionId'])) {
            $detailedAnswers[$answer['questionId']] = $answer;
        }
    }
}

$quizQuestions = getQuizQuestions($quizId);

$scorePercentage = ($quizResult['grades'] / $quizInfo['total_questions']) * 100;
$passed = $scorePercentage >= $quizInfo['passing_score'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quizInfo['title']); ?> - Results</title>
    <style>
        .answer-details {
            margin-top: 20px;
        }
        .question-detail {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .question-detail.correct {
            border-left: 4px solid #28a745;
        }
        .question-detail.incorrect {
            border-left: 4px solid #dc3545;
        }
        .question-text {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .answer-choice {
            margin: 5px 0;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .answer-choice.student-answer {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
        }
        .answer-choice.correct-answer {
            background-color: #e8f5e8;
            border: 1px solid #4caf50;
        }
        .answer-choice.student-answer.incorrect {
            background-color: #ffebee;
            border: 1px solid #f44336;
        }
        .answer-status {
            font-weight: bold;
            margin-top: 10px;
        }
        .answer-status.correct {
            color: #28a745;
        }
        .answer-status.incorrect {
            color: #dc3545;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .result-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .result-score {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .result-passed {
            color: #28a745;
        }
        .result-failed {
            color: #dc3545;
        }
        .result-feedback {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .result-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button-group {
            text-align: center;
            margin: 30px 0;
        }
        .back-btn, .retry-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .back-btn {
            background-color: #6c757d;
            color: white;
        }
        .retry-btn {
            background-color: #007bff;
            color: white;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
        .retry-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <main class="content">
            <div class="result-header">
                <h1><?php echo htmlspecialchars($quizInfo['title']); ?> - Results</h1>
                <p><?php echo htmlspecialchars($quizInfo['description'] ?? ''); ?></p>
            </div>

            <div class="result-content">
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

                <?php if ($showDetails && !empty($detailedAnswers) && !empty($quizQuestions)): ?>
                    <div class="answer-details">
                        <h3>Answer Details:</h3>
                        <?php foreach ($quizQuestions as $index => $question): ?>
                            <?php 
                                $questionId = $question['questionId'];
                                $answer = $detailedAnswers[$questionId] ?? null;
                                if (!$answer) continue;
                                
                                $isCorrect = $answer['isCorrect'];
                                $studentAnswerKey = '';
                                $correctAnswerKey = $question['correct_answer'];
                                
                                // Get options
                                $options = [];
                                if (isset($question['answer_option']) && !empty($question['answer_option'])) {
                                    $options = json_decode($question['answer_option'], true);
                                    if (!is_array($options)) {
                                        $options = [];
                                    }
                                }
                                if (empty($options) && $question['question_type'] === 'true_false') {
                                    $options = ['a' => 'True', 'b' => 'False'];
                                }
                                
                                // Find student answer key
                                foreach ($options as $key => $text) {
                                    if ($text === $answer['studentAnswer']) {
                                        $studentAnswerKey = $key;
                                        break;
                                    }
                                }
                            ?>
                            <div class="question-detail <?php echo $isCorrect ? 'correct' : 'incorrect'; ?>">
                                <div class="question-text">
                                    Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question_text']); ?>
                                </div>
                                
                                <?php foreach ($options as $key => $optionText): ?>
                                    <?php 
                                        $classes = ['answer-choice'];
                                        if ($key === $studentAnswerKey) {
                                            $classes[] = 'student-answer';
                                            if (!$isCorrect) {
                                                $classes[] = 'incorrect';
                                            }
                                        }
                                        if ($key === $correctAnswerKey) {
                                            $classes[] = 'correct-answer';
                                        }
                                    ?>
                                    <div class="<?php echo implode(' ', $classes); ?>">
                                        <?php echo strtoupper($key); ?>. <?php echo htmlspecialchars($optionText); ?>
                                        <?php if ($key === $studentAnswerKey): ?>
                                            <strong> (Your Answer)</strong>
                                        <?php endif; ?>
                                        <?php if ($key === $correctAnswerKey): ?>
                                            <strong> (Correct Answer)</strong>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="answer-status <?php echo $isCorrect ? 'correct' : 'incorrect'; ?>">
                                    <?php echo $isCorrect ? '✓ Correct' : '✗ Incorrect'; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="button-group">
                    <a href="details.php?id=<?php echo $moduleId; ?>" class="back-btn">Back to Course</a>
                    <?php if (!$passed): ?>
                        <a href="quiz.php?id=<?php echo $quizId; ?>" class="retry-btn">Retake Quiz</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>