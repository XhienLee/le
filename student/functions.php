<?php
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

function getQuizResult($quizId, $studentId) {
    require '../functions/db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM grades WHERE quizId = ? AND studentId = ? ORDER BY updated_at DESC LIMIT 1");
    $stmt->bind_param("ss", $quizId, $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}
function saveQuizAttempt($recordId, $quizId, $studentId, $attempt_score, $studentAnswer, $status) {
    require '../functions/db_connect.php';
    $encodedAnswer = json_encode($studentAnswer);
    $stmt = $conn->prepare("INSERT INTO quiz_attempt (recordId, quizId, studentId, student_answer, attempt_score, completion_status, status) 
                            VALUES (?, ?, ?, ?, ?, 'completed', ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssss", $recordId, $quizId, $studentId, $encodedAnswer, $attempt_score, $status);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
    $conn->close();
}
function saveQuizResult($quizId, $studentId, $moduleId, $score, $totalQuestions, $feedback, $recordId) {
    require '../functions/db_connect.php';
    $gradeId = uniqid('grade_', true);
    $checkStmt = $conn->prepare("SELECT * FROM grades WHERE quizId = ? AND studentId = ?");
    $checkStmt->bind_param("ss", $quizId, $studentId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE grades SET grades = ?, feedback = ?, recordId = ?, updated_at = NOW() WHERE quizId = ? AND studentId = ?");
        $updateStmt->bind_param("dssss", $score, $feedback, $recordId, $quizId, $studentId);
        $updateStmt->execute();
    } else {
        $insertStmt = $conn->prepare("INSERT INTO grades (gradeId, quizId, studentId, moduleId, recordId, grades, feedback, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $insertStmt->bind_param("sssssds", $gradeId, $quizId, $studentId, $moduleId, $recordId, $score, $feedback);
        $insertStmt->execute();
    }
    $activityLogId = uniqid('act_', true);
    $activityType = 'quiz_completion';
    $activityDetails = json_encode([
        'quizId' => $quizId,
        'score' => $score,
        'totalQuestions' => $totalQuestions,
        'passed' => ($score / $totalQuestions) * 100 >= getQuizInfo($quizId)['passing_score']]);
    $logStmt = $conn->prepare("INSERT INTO user_activity_logs (activityLogId, user_type, user_id, activity_type, activity_details) VALUES (?, 'student', ?, ?, ?)");
    $logStmt->bind_param("ssss", $activityLogId, $studentId, $activityType, $activityDetails);
    $logStmt->execute();
}

function checkAndEnrollStudent($moduleId, $studentId) {
    include "../functions/db_connect.php";
    try {
        $checkQuery = "SELECT enrollmentId, status FROM module_enrollments WHERE moduleId = ? AND studentId = ?";
        $checkStmt = $conn->prepare($checkQuery);
        if ($checkStmt === false) {
            return [
                'success' => false,
                'message' => 'Failed to prepare check statement: ' . $conn->error
            ];
        }
        $checkStmt->bind_param("ss", $moduleId, $studentId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $existingEnrollment = $checkResult->fetch_assoc();
        $checkStmt->close();
        if ($existingEnrollment) {
            return [
                'success' => false,
                'message' => 'Student is already enrolled in this module',
                'enrollment_status' => $existingEnrollment['status'],
                'enrollment_id' => $existingEnrollment['enrollmentId']
            ];
        }
        $enrollQuery = "INSERT INTO module_enrollments (moduleId, studentId, status) VALUES (?, ?, 'in-progress')";
        $enrollStmt = $conn->prepare($enrollQuery);
        if ($enrollStmt === false) {
            return ['success' => false,'message' => 'Failed to prepare enroll statement: ' . $conn->error];
        }
        $enrollStmt->bind_param("ss", $moduleId, $studentId);

        if ($enrollStmt->execute()) {
            $newEnrollmentId = $conn->insert_id;
            $enrollStmt->close();
            return ['success' => true,'message' => 'Student successfully enrolled in module','enrollment_id' => $newEnrollmentId];
        } else {
            $enrollStmt->close();
            return ['success' => false, 'message' => 'Failed to enroll student in module: ' . $enrollStmt->error];
        }

    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Application error: ' . $e->getMessage()];
    }
}