<?php
session_start();
require_once 'session.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$action = $_POST['action'] ?? '';

if ($action !== 'update_password') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
    exit;
}

if (strlen($newPassword) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit;
}
include "db_connect.php";
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'];
    $table = '';
    $idColumn = '';
    
    switch ($userType) {
        case 'student':
            $table = 'students';
            $idColumn = 'studentId';
            break;
        case 'instructor':
            $table = 'instructors';
            $idColumn = 'instructorId';
            break;
        case 'admin':
            $table = 'admins';
            $idColumn = 'adminId';
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid user type']);
            $conn->close();
            exit;
    }
    $stmt = $conn->prepare("SELECT password FROM {$table} WHERE {$idColumn} = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        $stmt->close();
        $conn->close();
        exit;
    }
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE {$table} SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE {$idColumn} = ?");
    $updateStmt->bind_param("ss", $hashedNewPassword, $userId);
    $updateResult = $updateStmt->execute();
    
    if ($updateResult && $updateStmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
    }
    $updateStmt->close();
} catch (Exception $e) {
    error_log("Password update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>