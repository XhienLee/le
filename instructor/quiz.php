<?php
session_start();
require_once '../functions/module.php';
require_once '../functions/session.php';
requireLogin();
$action = isset($_GET['action']) ? $_GET['action'] : 'create';
$quizId = isset($_GET['id']) ? $_GET['id'] : null;
$moduleId = isset($_GET['module']) ? $_GET['module'] : null;
if (!$moduleId) {
    header('Location: index.php');
    exit();
}
$courseDetails = getCourseById($moduleId);
$title = isset($courseDetails['title']) ? $courseDetails['title'] : 'Course Not Found';
$success_message = '';
$error_message = '';
$quiz_data = [];
$quiz_questions = [];
if ($_POST) {
    if (isset($_POST['quiz_action'])) {
        switch ($_POST['quiz_action']) {
            case 'create':
                $result = createQuiz($_POST, $moduleId);
                if ($result['status']) {
                    $success_message = $result['message'];
                    $quizId = $result['quiz_id'];
                    $action = 'edit';
                    $quiz_data = getQuizById($quizId);
                    header("Location: details.php?id={$moduleId}&success_message={$success_message}&tab=quizzes");
                    exit;
                } else {
                    $error_message = $result['message'];
                    header("Location: details.php?id={$moduleId}&error_message={$error_message}&tab=quizzes");
                    exit;
                }
                break;
            case 'update':
                $result = updateQuiz($_POST, $quizId);
                if ($result['status']) {
                    $success_message = $result['message'];
                    $quiz_data = getQuizById($quizId);
                    header("Location: details.php?id={$moduleId}&success_message={$success_message}&tab=quizzes");
                } else {
                    $error_message = $result['message'];
                    header("Location: details.php?id={$moduleId}&error_message={$error_message}&tab=quizzes");
                    exit;
                }
                break;
        }
    }
    if (isset($_POST['question_action']) && $quizId) {
        switch ($_POST['question_action']) {
            case 'add':
                $result = addQuizQuestion($_POST, $quizId);
                if ($result['status']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
                break;
            case 'update':
                $result = updateQuizQuestion($_POST);
                if ($result['status']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
                break;
            case 'delete':
                $result = deleteQuizQuestion($_POST['question_id']);
                if ($result['status']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
                break;
        }
    }
}

if ($action === 'edit' && $quizId) {
    $quiz_data = getQuizById($quizId);
    if (empty($quiz_data)) {
        $error_message = 'Quiz not found.';
        $action = 'create';
    } else {
        $quiz_questions = getQuizQuestions($quizId);
    }
}
if ($action === 'delete' && $quizId) {
    $result = deleteQuiz($quizId);
    print_r($result);
    if ($result['status']) {
        header('Location: details.php?id=' . $moduleId . '&tab=quizzes&message=Quiz deleted successfully');
        exit();
    } else {
        $error_message = $result['message'];
    }
}
function createQuiz($data, $moduleId) {
    require '../functions/db_connect.php';
    try {
        $quizId = generateQuizId();
        $stmt = $conn->prepare("INSERT INTO quizzes (quizId, moduleId, title, description, total_questions, passing_score, duration_minutes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiis", 
            $quizId, 
            $moduleId, 
            $data['title'], 
            $data['description'], 
            $data['total_questions'], 
            $data['passing_score'], 
            $data['duration_minutes']
        );
        
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Quiz created successfully! You can now add questions.', 'quiz_id' => $quizId];
        } else {
            return ['status' => false, 'message' => 'Database error: ' . $conn->error];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error creating quiz: ' . $e->getMessage()];
    }
}

function updateQuiz($data, $quizId) {
    require '../functions/db_connect.php';
    
    try {
        $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ?, total_questions = ?, passing_score = ?, duration_minutes = ? WHERE quizId = ?");
        $stmt->bind_param("ssiiss", 
            $data['title'], 
            $data['description'], 
            $data['total_questions'], 
            $data['passing_score'], 
            $data['duration_minutes'], 
            $quizId
        );
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return ['status' => true, 'message' => 'Quiz updated successfully!'];
            } else {
                return ['status' => false, 'message' => 'No changes were made to the quiz.'];
            }
        } else {
            return ['status' => false, 'message' => 'Database error: ' . $conn->error];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error updating quiz: ' . $e->getMessage()];
    }
}

function deleteQuiz($quizId) {
    require '../functions/db_connect.php';
    try {
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM quiz_attempt WHERE quizId = ?");
        $checkStmt->bind_param("s", $quizId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            return ['status' => false, 'message' => 'Cannot delete quiz with existing student attempts.'];
        }
        
        $deleteQuestionsStmt = $conn->prepare("DELETE FROM questions WHERE quizId = ?");
        $deleteQuestionsStmt->bind_param("s", $quizId);
        $deleteQuestionsStmt->execute();
    
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE quizId = ?");
        $stmt->bind_param("s", $quizId);
        
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Quiz deleted successfully!'];
        } else {
            return ['status' => false, 'message' => 'Database error: ' . $conn->error];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error deleting quiz: ' . $e->getMessage()];
    }
}

function addQuizQuestion($data, $quizId) {
    require '../functions/db_connect.php';
    
    try {
        $questionId = generateQuestionId();
        $answerOptions = json_encode([
            'A' => $data['option_a'],
            'B' => $data['option_b'],
            'C' => $data['option_c'],
            'D' => $data['option_d']
        ]);
        
        $stmt = $conn->prepare("INSERT INTO questions (questionId, quizId, question_text, answer_option, correct_answer, question_type) VALUES (?, ?, ?, ?, ?, 'multiple_choice')");
        $stmt->bind_param("sssss", 
            $questionId,
            $quizId,
            $data['question_text'],
            $answerOptions,
            $data['correct_answer'],
        );
        
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Question added successfully!'];
        } else {
            return ['status' => false, 'message' => 'Database error: ' . $conn->error];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error adding question: ' . $e->getMessage()];
    }
}

function updateQuizQuestion($data) {
    require '../functions/db_connect.php';
    
    try {
        $answerOptions = json_encode([
            'A' => $data['option_a'],
            'B' => $data['option_b'],
            'C' => $data['option_c'],
            'D' => $data['option_d']
        ]);
        
        $stmt = $conn->prepare("UPDATE questions SET question_text = ?, answer_option = ?, correct_answer = ? WHERE questionId = ?");
        $stmt->bind_param("ssss", 
            $data['question_text'],
            $answerOptions,
            $data['correct_answer'],
            $data['question_id']
        );
        
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Question updated successfully!'];
        } else {
            return ['status' => false, 'message' => 'Database error: ' . $conn->error];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error updating question: ' . $e->getMessage()];
    }
}

function deleteQuizQuestion($questionId) {
    require '../functions/db_connect.php';
    
    try {
        $stmt = $conn->prepare("DELETE FROM questions WHERE questionId = ?");
        $stmt->bind_param("s", $questionId);
        
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Question deleted successfully!'];
        } else {
            return ['status' => false, 'message' => 'Database error: ' . $conn->error];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error deleting question: ' . $e->getMessage()];
    }
}

function getQuizById($quizId) {
    require '../functions/db_connect.php';
    
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE quizId = ?");
    $stmt->bind_param("s", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return [];
}

function getQuizQuestions($quizId) {
    require '../functions/db_connect.php';
    
    $stmt = $conn->prepare("SELECT * FROM questions WHERE quizId = ? ORDER BY questionId");
    $stmt->bind_param("s", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

function generateQuizId() {
    return 'QZ' . time() . rand(100, 999);
}

function generateQuestionId() {
    return 'QN' . time() . rand(100, 999);
}

$page_title = '';
switch ($action) {
    case 'create':
        $page_title = 'Create New Quiz';
        break;
    case 'edit':
        $page_title = 'Edit Quiz';
        break;
    case 'delete':
        $page_title = 'Delete Quiz';
        break;
    default:
        $page_title = 'Manage Quiz';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="quiz.css">
    <link rel="stylesheet" href="details.css">
    <style>
        .question-section {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .question-item {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            border: 1px solid #e1e5e9;
        }
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .question-actions {
            display: flex;
            gap: 10px;
        }
        .option-list {
            margin: 10px 0;
            padding-left: 20px;
        }
        .correct-answer {
            color: #28a745;
            font-weight: bold;
        }
        .add-question-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .edit-question-form {
            display: none;
            background: #f8f9fa;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <main class="content">
            <div class="course-header">
                <h1><?php echo htmlspecialchars($page_title); ?></h1>
                <p>Module: <?php echo htmlspecialchars($title); ?></p>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($action === 'delete' && !empty($quiz_data)): ?>
                <div class="delete-confirmation">
                    <h3><i class="fas fa-exclamation-triangle"></i> Confirm Quiz Deletion</h3>
                    <p><strong>Warning:</strong> This action cannot be undone!</p>
                    
                    <div class="quiz-info-box">
                        <h4>Quiz to be deleted:</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Title:</strong>
                                <?php echo htmlspecialchars($quiz_data['title']); ?>
                            </div>
                            <div class="info-item">
                                <strong>Questions:</strong>
                                <?php echo htmlspecialchars($quiz_data['total_questions']); ?>
                            </div>
                            <div class="info-item">
                                <strong>Duration:</strong>
                                <?php echo htmlspecialchars($quiz_data['duration_minutes']); ?> minutes
                            </div>
                            <div class="info-item">
                                <strong>Passing Score:</strong>
                                <?php echo htmlspecialchars($quiz_data['passing_score']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="quiz_action" value="delete">
                        <div class="quiz-actions">
                            <button type="submit" class="confirm-btn">
                                <i class="fas fa-trash"></i> Yes, Delete Quiz
                            </button>
                            <a href="details.php?id=<?php echo htmlspecialchars($moduleId); ?>&tab=quizzes" class="cancel-btn">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <form method="POST" class="quiz-form">
                    <input type="hidden" name="quiz_action" value="<?php echo $action === 'edit' ? 'update' : 'create'; ?>">
                    
                    <div class="form-group">
                        <label for="title"><strong>Quiz Title:</strong></label>
                        <input type="text" id="title" name="title" 
                               value="<?php echo htmlspecialchars($quiz_data['title'] ?? ''); ?>" 
                               class="form-input" required 
                               placeholder="Enter quiz title">
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><strong>Description:</strong></label>
                        <textarea id="description" name="description" rows="4" 
                                  class="form-textarea" 
                                  placeholder="Enter quiz description"><?php echo htmlspecialchars($quiz_data['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="total_questions"><strong>Total Questions:</strong></label>
                            <input type="number" id="total_questions" name="total_questions" 
                                   value="<?php echo htmlspecialchars($quiz_data['total_questions'] ?? ''); ?>" 
                                   class="form-input" required min="1" max="100"
                                   placeholder="e.g., 10">
                        </div>
                        
                        <div class="form-group">
                            <label for="duration_minutes"><strong>Duration (Minutes):</strong></label>
                            <input type="number" id="duration_minutes" name="duration_minutes" 
                                   value="<?php echo htmlspecialchars($quiz_data['duration_minutes'] ?? ''); ?>" 
                                   class="form-input" required min="1" max="300"
                                   placeholder="e.g., 30">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="passing_score"><strong>Passing Score:</strong></label>
                        <input type="number" id="passing_score" name="passing_score" 
                               value="<?php echo htmlspecialchars($quiz_data['passing_score'] ?? ''); ?>" 
                               class="form-input" required min="1" max="100"
                               placeholder="e.g., 70">
                        <small class="file-help">Students need to score at least this percentage to pass</small>
                    </div>
                    
                    <div class="quiz-actions">
                        <button type="submit" class="save-btn">
                            <i class="fas fa-save"></i> 
                            <?php echo $action === 'edit' ? 'Update Quiz' : 'Create Quiz'; ?>
                        </button>
                        <a href="details.php?id=<?php echo htmlspecialchars($moduleId); ?>&tab=quizzes" class="cancel-btn">
                            <i class="fas fa-arrow-left"></i> Back to Module
                        </a>
                    </div>
                </form>

                <!-- Quiz Questions Section (only show if editing existing quiz) -->
                <?php if ($action === 'edit' && !empty($quiz_data)): ?>
                <div class="question-section">
                    <h3><i class="fas fa-question-circle"></i> Quiz Questions</h3>
                    
                    <!-- Existing Questions -->
                    <?php if (!empty($quiz_questions)): ?>
                        <div class="questions-list">
                            <?php foreach ($quiz_questions as $index => $question): ?>
                                <div class="question-item">
                                    <div class="question-header">
                                        <h4>Question <?php echo $index + 1; ?> (1 points)</h4>
                                        <div class="question-actions">
                                            <button type="button" class="edit-btn btn-small" onclick="toggleEditQuestion('<?php echo $question['questionId']; ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this question?')">
                                                <input type="hidden" name="question_action" value="delete">
                                                <input type="hidden" name="question_id" value="<?php echo $question['questionId']; ?>">
                                                <button type="submit" class="delete-btn btn-small">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="question-display" id="display_<?php echo $question['questionId']; ?>">
                                        <p><strong><?php echo htmlspecialchars($question['question_text']); ?></strong></p>
                                        <div class="option-list">
                                            <?php
                                            $answers = json_decode($question['answer_option'], true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($answers)) {
                                                foreach($answers as $key => $answer) {
                                                    $isCorrect = ($question['correct_answer'] === $key);
                                                    $cssClass = $isCorrect ? 'correct-answer' : '';
                                                    
                                                    echo '<div class="' . $cssClass . '">';
                                                    echo htmlspecialchars($key) . ') ' . htmlspecialchars($answer);
                                                    if ($isCorrect) echo ' ✓';
                                                    echo '</div>';
                                                }
                                            } else {
                                                echo '<div class="error">Error: Invalid answer format</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Edit Form -->
                                    <div class="edit-question-form" id="edit_<?php echo $question['questionId']; ?>">
                                        <form method="POST">
                                            <input type="hidden" name="question_action" value="update">
                                            <input type="hidden" name="question_id" value="<?php echo $question['questionId']; ?>">
                                            
                                            <div class="form-group">
                                                <label><strong>Question:</strong></label>
                                                <textarea name="question_text" class="form-textarea" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                                            </div>
                                            
                                            <?php
                                            // Parse existing answers for editing
                                            $existingAnswers = json_decode($question['answer_option'], true);
                                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($existingAnswers)) {
                                                $existingAnswers = ['A' => '', 'B' => '', 'C' => '', 'D' => ''];
                                            }
                                            ?>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Option A:</label>
                                                    <input type="text" name="option_a" class="form-input" value="<?php echo htmlspecialchars($existingAnswers['A'] ?? ''); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Option B:</label>
                                                    <input type="text" name="option_b" class="form-input" value="<?php echo htmlspecialchars($existingAnswers['B'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Option C:</label>
                                                    <input type="text" name="option_c" class="form-input" value="<?php echo htmlspecialchars($existingAnswers['C'] ?? ''); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Option D:</label>
                                                    <input type="text" name="option_d" class="form-input" value="<?php echo htmlspecialchars($existingAnswers['D'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Correct Answer:</label>
                                                    <select name="correct_answer" class="form-input" required>
                                                        <option value="A" <?php echo ($question['correct_answer'] === 'A') ? 'selected' : ''; ?>>A</option>
                                                        <option value="B" <?php echo ($question['correct_answer'] === 'B') ? 'selected' : ''; ?>>B</option>
                                                        <option value="C" <?php echo ($question['correct_answer'] === 'C') ? 'selected' : ''; ?>>C</option>
                                                        <option value="D" <?php echo ($question['correct_answer'] === 'D') ? 'selected' : ''; ?>>D</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="quiz-actions">
                                                <button type="submit" class="save-btn">
                                                    <i class="fas fa-save"></i> Update Question
                                                </button>
                                                <button type="button" class="cancel-btn" onclick="toggleEditQuestion('<?php echo $question['questionId']; ?>')">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No questions added yet. Add your first question below.</p>
                    <?php endif; ?>
                    
                    <!-- Add New Question Form -->
                    <div class="add-question-form">
                        <h4><i class="fas fa-plus"></i> Add New Question</h4>
                        <form method="POST">
                            <input type="hidden" name="question_action" value="add">
                            
                            <div class="form-group">
                                <label for="question_text"><strong>Question:</strong></label>
                                <textarea id="question_text" name="question_text" rows="3" class="form-textarea" required placeholder="Enter your question here..."></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="option_a">Option A:</label>
                                    <input type="text" id="option_a" name="option_a" class="form-input" required placeholder="Enter option A">
                                </div>
                                <div class="form-group">
                                    <label for="option_b">Option B:</label>
                                    <input type="text" id="option_b" name="option_b" class="form-input" required placeholder="Enter option B">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="option_c">Option C:</label>
                                    <input type="text" id="option_c" name="option_c" class="form-input" required placeholder="Enter option C">
                                </div>
                                <div class="form-group">
                                    <label for="option_d">Option D:</label>
                                    <input type="text" id="option_d" name="option_d" class="form-input" required placeholder="Enter option D">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="correct_answer">Correct Answer:</label>
                                    <select id="correct_answer" name="correct_answer" class="form-input" required>
                                        <option value="">Select correct answer</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="quiz-actions">
                                <button type="submit" class="save-btn">
                                    <i class="fas fa-plus"></i> Add Question
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
<script>
    function toggleEditQuestion(questionId) {
        const displayDiv = document.getElementById('display_' + questionId);
        const editDiv = document.getElementById('edit_' + questionId);
        if (displayDiv && editDiv) {
            if (editDiv.style.display === 'none' || editDiv.style.display === '') {
                displayDiv.style.display = 'none';
                editDiv.style.display = 'block';
                const firstInput = editDiv.querySelector('textarea, input');
                if (firstInput) {
                    firstInput.focus();
                }
            } else {
                displayDiv.style.display = 'block';
                editDiv.style.display = 'none';
            }
        }
    }

    function validateQuizForm(form) {
        const title = form.querySelector('[name="title"]').value.trim();
        const totalQuestions = parseInt(form.querySelector('[name="total_questions"]').value);
        const duration = parseInt(form.querySelector('[name="duration_minutes"]').value);
        const passingScore = parseInt(form.querySelector('[name="passing_score"]').value);
        
        let errors = [];
        
        if (title.length < 3) {
            errors.push('Quiz title must be at least 3 characters long');
        }
        
        if (totalQuestions < 1 || totalQuestions > 100) {
            errors.push('Total questions must be between 1 and 100');
        }
        
        if (duration < 1 || duration > 300) {
            errors.push('Duration must be between 1 and 300 minutes');
        }
        
        if (passingScore < 0 || passingScore > 100) {
            errors.push('Passing score must be between 0 and 100');
        }
        
        if (errors.length > 0) {
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            return false;
        }
        
        return true;
    }
    function validateQuestionForm(form) {
        const questionText = form.querySelector('[name="question_text"]').value.trim();
        const optionA = form.querySelector('[name="option_a"]').value.trim();
        const optionB = form.querySelector('[name="option_b"]').value.trim();
        const optionC = form.querySelector('[name="option_c"]').value.trim();
        const optionD = form.querySelector('[name="option_d"]').value.trim();
        const correctAnswer = form.querySelector('[name="correct_answer"]').value;
        
        let errors = [];
        
        if (questionText.length < 10) {
            errors.push('Question text must be at least 10 characters long');
        }
        
        if (!optionA || !optionB || !optionC || !optionD) {
            errors.push('All answer options must be filled');
        }
        
        if (!correctAnswer) {
            errors.push('Please select the correct answer');
        }
        const options = [optionA, optionB, optionC, optionD];
        const uniqueOptions = [...new Set(options)];
        if (uniqueOptions.length !== options.length) {
            errors.push('Answer options must be unique');
        }
        
        if (errors.length > 0) {
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            return false;
        }
        
        return true;
    }

    function confirmDelete(itemType, itemName) {
        return confirm(`Are you sure you want to delete this ${itemType}?\n\n"${itemName}"\n\nThis action cannot be undone.`);
    }

    let draftData = {};
    function saveDraft() {
        const forms = document.querySelectorAll('form');
        forms.forEach((form, index) => {
            const formData = new FormData(form);
            const draftKey = `draft_form_${index}`;
            draftData[draftKey] = {};
            
            for (let [key, value] of formData.entries()) {
                draftData[draftKey][key] = value;
            }
        });
    }

    function loadDraft() {
        const forms = document.querySelectorAll('form');
        forms.forEach((form, index) => {
            const draftKey = `draft_form_${index}`;
            if (draftData[draftKey]) {
                Object.keys(draftData[draftKey]).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input && input.value === '') {
                        input.value = draftData[draftKey][key];
                    }
                });
            }
        });
    }

    function addCharacterCounter(textareaSelector, maxLength = 500) {
        const textareas = document.querySelectorAll(textareaSelector);
        
        textareas.forEach(textarea => {
            const counter = document.createElement('small');
            counter.className = 'character-counter';
            counter.style.color = '#666';
            counter.style.fontSize = '12px';
            counter.style.display = 'block';
            counter.style.textAlign = 'right';
            counter.style.marginTop = '5px';
            
            textarea.parentNode.appendChild(counter);
            
            function updateCounter() {
                const currentLength = textarea.value.length;
                counter.textContent = `${currentLength}/${maxLength} characters`;
                
                if (currentLength > maxLength * 0.9) {
                    counter.style.color = '#dc3545';
                } else if (currentLength > maxLength * 0.7) {
                    counter.style.color = '#ffc107';
                } else {
                    counter.style.color = '#666';
                }
            }
            
            textarea.addEventListener('input', updateCounter);
            updateCounter();
        });
    }

    function enhanceFormSubmission() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                
                if (submitButton) {
                    let isValid = true;
                    
                    if (form.querySelector('[name="quiz_action"]')) {
                        isValid = validateQuizForm(form);
                    } else if (form.querySelector('[name="question_action"]')) {
                        isValid = validateQuestionForm(form);
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        return false;
                    }
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    submitButton.disabled = true;
                    setTimeout(() => {
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    }, 5000);
                }
            });
        });
    }

    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const submitButton = document.querySelector('button[type="submit"]');
                if (submitButton && !submitButton.disabled) {
                    submitButton.click();
                }
            }
            if (e.key === 'Escape') {
                const editForms = document.querySelectorAll('.edit-question-form[style*="block"]');
                editForms.forEach(form => {
                    const questionId = form.id.replace('edit_', '');
                    toggleEditQuestion(questionId);
                });
            }
        });
    }

    function updateQuestionNumbers() {
        const questionItems = document.querySelectorAll('.question-item');
        questionItems.forEach((item, index) => {
            const header = item.querySelector('.question-header h4');
            if (header) {
                const points = header.textContent.match(/\((\d+) points?\)/);
                const pointsText = points ? ` (${points[1]} points)` : '';
                header.textContent = `Question ${index + 1}${pointsText}`;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadDraft();
        setInterval(saveDraft, 30000);
        addCharacterCounter('textarea[name="description"]', 1000);
        addCharacterCounter('textarea[name="question_text"]', 500);
        enhanceFormSubmission();
        setupKeyboardShortcuts();
        updateQuestionNumbers();
        const firstInput = document.querySelector('input[type="text"], textarea');
        if (firstInput) {
            firstInput.focus();
        }
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        document.querySelectorAll('button[type="submit"]').forEach(button => {
            const form = button.closest('form');
            const actionInput = form?.querySelector('[name="question_action"], [name="quiz_action"]');
            
            if (actionInput && actionInput.value === 'delete') {
                button.addEventListener('click', function(e) {
                    const questionText = form.closest('.question-item')?.querySelector('.question-display p')?.textContent;
                    const confirmMessage = questionText ? 
                        `Are you sure you want to delete this question?\n\n"${questionText.substring(0, 100)}..."\n\nThis action cannot be undone.` :
                        'Are you sure you want to delete this item? This action cannot be undone.';
                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                    }
                });
            }
        });
    });

    function showTemporaryMessage(message, type = 'success', duration = 3000) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        alertDiv.style.position = 'fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '1000';
        alertDiv.style.minWidth = '300px';
        document.body.appendChild(alertDiv);
        setTimeout(() => {
            alertDiv.style.opacity = '0';
            alertDiv.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 300);
        }, duration);
    }
</script>
</body>
</html>