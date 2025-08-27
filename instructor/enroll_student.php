<?php
session_start();
require_once '../functions/student_function.php';
require_once '../functions/module.php';
require_once '../functions/session.php';
requireLogin();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';
switch ($action) {
    case 'search_student':
        echo json_encode(searchStudentById());
        break;
    case 'enroll_student':
        echo json_encode(enrollStudentInModule());
        break;
    case 'unenroll_student':
        echo json_encode(unenrollByInstructor($_POST['student_id'], $_POST['module_id'], $_POST['force_unenroll']));
        break;
    default:
        echo json_encode(['status' => false, 'message' => 'Invalid action']);
        break;
}
exit;

function searchStudentById() {
    $studentId = trim($_POST['student_id'] ?? '');
    if (empty($studentId)) {
        return ['status' => false, 'message' => 'Student ID is required'];
    }
    try {
        $student = getStudentById($studentId);
        if (!empty($student)) {
            return ['status' => true, 'message' => 'Student found','student' => $student];
        } else {
            return ['status' => false, 'message' => 'Student not found with ID: ' . htmlspecialchars($studentId)];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error searching for student: ' . $e->getMessage()];
    }
}

function enrollStudentInModule() {
    $studentId = trim($_POST['student_id'] ?? '');
    $moduleId = trim($_POST['module_id'] ?? '');
    if (empty($studentId) || empty($moduleId)) {
        return ['status' => false, 'message' => 'Student ID and Module ID are required'];
    }
    try {
        $student = getStudentById($studentId);
        if (empty($student)) {
            return ['status' => false, 'message' => 'Student not found'];
        }
        $module = getCourseById($moduleId);
        if (empty($module)) {
            return ['status' => false, 'message' => 'Module not found'];
        }
        $existingEnrollment = checkStudentEnrollment($studentId, $moduleId);
        if ($existingEnrollment) {
            return ['status' => false, 'message' => 'Student is already enrolled in this module'];
        }
        $result = enrollStudent($studentId, $moduleId);
        if ($result['status']) {
            return ['status' => true, 'message' => 'Student successfully enrolled in the module'];
        } else {
            return ['status' => false, 'message' => $result['message']];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error enrolling student: ' . $e->getMessage()];
    }
}

function checkStudentEnrollment($studentId, $moduleId) {
    require '../functions/db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM module_enrollments WHERE studentId = ? AND moduleId = ?");
    $stmt->bind_param("ss", $studentId, $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function enrollStudent($studentId, $moduleId) {
    require '../functions/db_connect.php';
    try {
        $stmt = $conn->prepare("INSERT INTO module_enrollments (studentId, moduleId) VALUES (?, ?)");
        $stmt->bind_param("ss", $studentId, $moduleId);
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Student enrolled successfully'];
        } else {
            return ['status' => false, 'message' => 'Database error: ' . $conn->error];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function unenrollByInstructor($studentId, $moduleId, $forceUnenroll = 0){
    require '../functions/db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module_enrollments` WHERE studentId = ? AND moduleId = ?");
    $stmt->bind_param("ss", $studentId, $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows < 1) {
        return ["status" => false, "message" => "You are not enrolled in this course."];
    }
    $res = $result->fetch_assoc();
    if ($res['status'] == 'completed' or $forceUnenroll == 1) {
        $deleteStmt = $conn->prepare("DELETE FROM `module_enrollments` WHERE studentId = ? AND moduleId = ?");
        $deleteStmt->bind_param("ss", $studentId, $moduleId);  
        if ($deleteStmt->execute()) {
            return ["status" => true, "message" => "You are now unenrolled from this course."];
        } else {
            return ["status" => false, "message" => "Database error while unenrolling."];
        }
    } else {
        return ["status" => false, "message" => "Student have not complete the module yet. Force Unenroll?"];
    }
}
?>