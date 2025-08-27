<?php
session_start();
require_once '../functions/db_connect.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'instructor') {
    echo json_encode(['status' => false, 'message' => 'Access denied. Instructor privileges required.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
    exit;
}
if (!isset($_POST['action']) || $_POST['action'] !== 'delete_module' || !isset($_POST['module_id'])) {
    echo json_encode(['status' => false, 'message' => 'Missing required parameters.']);
    exit;
}

$moduleId = trim($_POST['module_id']);
$instructorId = $_SESSION['user_id'];

if (empty($moduleId)) {
    echo json_encode(['status' => false, 'message' => 'Invalid module ID.']);
    exit;
}
try {
    $checkStmt = $conn->prepare("SELECT title, instructorId FROM `module` WHERE moduleId = ?");
    $checkStmt->bind_param("s", $moduleId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => false, 'message' => 'Module not found.']);
        exit;
    }
    $module = $result->fetch_assoc();
    if ($module['instructorId'] !== $instructorId) {
        echo json_encode(['status' => false, 'message' => 'You do not have permission to delete this module.']);
        exit;
    }
    $conn->begin_transaction();
    try {
        $activityLogId = 'LOG_' . date('Ymd') . '_' . substr(uniqid(), -8);
        $softDeleteStmt = $conn->prepare("UPDATE `module` SET status = 'deleted', deleted_at = CURRENT_TIMESTAMP WHERE moduleId = ?");
        $softDeleteStmt->bind_param("s", $moduleId);
        $softDeleteStmt->execute();
        if ($softDeleteStmt->affected_rows > 0) {
            $logStmt = $conn->prepare("INSERT INTO `user_activity_logs` (activityLogId, user_type, user_id, activity_type, activity_details, created_at) VALUES (?, 'instructor', ?, 'module_delete', ?, CURRENT_TIMESTAMP)");
            $activityDetails = "Deleted module: '{$module['title']}' (ID: {$moduleId})";
            $logStmt->bind_param("sss", $activityLogId, $instructorId, $activityDetails);
            $logStmt->execute();
            $conn->commit();
            echo json_encode(['status' => true, 'message' => "Module '{$module['title']}' has been successfully deleted."]);
        } else {
            $conn->rollback();
            echo json_encode(['status' => false, 'message' => 'Failed to delete the module.']);
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Module deletion error: " . $e->getMessage());
    echo json_encode(['status' => false, 'message' => 'An error occurred while deleting the module. Please try again.']);
}

$conn->close();
?>