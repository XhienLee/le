<?php
session_start();
require_once '../functions/student_function.php';
require_once '../functions/module.php';
require_once '../functions/session.php';
requireLogin();
header('Content-Type: application/json');

$response = ['status' => false, 'message' => 'An error occurred while processing your request.'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['moduleId'], $_POST['studentId'])) {
        $moduleId = $_POST['moduleId'];
        $studentId = $_POST['studentId'];
        if ($studentId == $_SESSION['user_id']) {
            $result = unenroll($moduleId, $studentId);
            $response = $result;
        } else {
            $response['message'] = 'Unauthorized action. User ID does not match.';
        }
    } else {
        $response['message'] = 'Missing required parameters.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;