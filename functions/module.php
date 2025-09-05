<?php
function getModule($status = 'active'){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module` where status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
    return [];
}

function getCourseById($moduleId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module` where moduleId = ?");
    $stmt->bind_param("s", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return [];
}

function getSelectedEnrolledCourseInfo($moduleId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module` where moduleId = ?");
    $stmt->bind_param("s", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return [];
}

function getEnrolledCourse($userid){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module_enrollments` where studentId = ?");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
    return [];
}

function getInstructorById($instructorId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `instructors` where instructorId  = ?");
    $stmt->bind_param("s", $instructorId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_assoc();
        return $res;
    }
    return [];
}

function getParticipant($moduleId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module_enrollments` where moduleId = ?");
    $stmt->bind_param("s", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
    return [];
}

function getStudentById($studentId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `students` where studentId   = ?");
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_assoc();
        return $res;
    }
    return [];  
}

function getStudentGradeByQuizID($studentId, $quizId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `quiz_attempt` where studentId = ? and quizId = ?");
    $stmt->bind_param("ss", $studentId, $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_assoc();
        return $res;
    }
    return [];  
}
function getStudentGrade($studentId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `student_grades` where studentId  = ?");
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_assoc();
        return $res;
    }
    return [];  
}


function getStudentByModuleID($moduleId, $status = 'in-progress'){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module_enrollments` WHERE moduleId = ? AND `status` = ?");
    $stmt->bind_param("ss", $moduleId, $status);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
    return []; 
}

function getStudentGradeByStudentId($moduleId, $studentId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `grades` WHERE moduleId = ? AND `studentId` = ?");
    $stmt->bind_param("ss", $moduleId, $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
    return [];
}
function getStudentGradeByModuleId($moduleId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `grades` WHERE moduleId = ?");
    $stmt->bind_param("s", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
    return [];
}
function unenroll($moduleId, $studentId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module_enrollments` WHERE studentId = ? AND moduleId = ?");
    $stmt->bind_param("ss", $studentId, $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows < 1) {
        return ["status" => false, "message" => "You are not enrolled in this course."];
    }
    $res = $result->fetch_assoc();
    if ($res['status'] == 'completed') {
        $deleteStmt = $conn->prepare("DELETE FROM `module_enrollments` WHERE studentId = ? AND moduleId = ?");
        $deleteStmt->bind_param("ss", $studentId, $moduleId);
        
        if ($deleteStmt->execute()) {
            return ["status" => true, "message" => "You are now unenrolled from this course."];
        } else {
            return ["status" => false, "message" => "Database error while unenrolling."];
        }
    } else {
        return ["status" => false, "message" => "You need to complete this course before you can unenroll yourself."];
    }
}

function getQuizzesByModuleId($moduleId){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `quizzes` WHERE moduleId = ?");
    $stmt->bind_param("s", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
    return []; 
}

function getQuizInfo($quizId) {
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
function getManageModule($userid, $status = 'active'){
    require 'db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM `module` where instructorId = ? and status = ?");
    $stmt->bind_param("ss", $userid, $status);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $res = $result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }
    return [];
}
function updateModule($moduleId, $title, $description, $contentText, $videoUrl, $pdfPath) {
    require 'db_connect.php';
    
    try {
        $stmt = $conn->prepare("UPDATE `module` SET title = ?, description = ?, content_text = ?, content_video_url = ?, content_pdf_path = ? WHERE moduleId = ?");
        $stmt->bind_param("ssssss", $title, $description, $contentText, $videoUrl, $pdfPath, $moduleId);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return ['status' => true, 'message' => 'Module updated successfully'];
            } else {
                return ['status' => false, 'message' => 'No changes were made to the module'];
            }
        } else {
            return ['status' => false, 'message' => 'Database error: ' . $conn->error];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error updating module: ' . $e->getMessage()];
    }
}
function getQuizAttempt($recordId) {
    require 'db_connect.php';
    try {
        $stmt = $conn->prepare("SELECT * FROM quiz_attempt WHERE recordId = ?");
        $stmt->bind_param("s", $recordId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $res = $result->fetch_all(MYSQLI_ASSOC);
            return $res;
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Error updating module: ' . $e->getMessage()];
    }
}