<?php
session_start();
require_once '../functions/session.php';
require_once '../functions/db_connect.php';
requireLogin();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->autocommit(false);
    try {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']) ?: null;
        $content_text = trim($_POST['content_text']) ?: null;
        $content_video_url = trim($_POST['content_video_url']) ?: null;
        $instructorId = $_SESSION['user_id'];
        
        if (empty($title)) {
            throw new Exception('Module title is required.');
        }
        
        $moduleId = generateModuleId();
        $content_pdf_path = null;
        $module_image_path = null;
        
        if (isset($_FILES['content_pdf']) && $_FILES['content_pdf']['error'] === UPLOAD_ERR_OK) {
            $content_pdf_path = uploadPdf($_FILES['content_pdf'], $moduleId);
        }
        
        if (isset($_FILES['module_image']) && $_FILES['module_image']['error'] === UPLOAD_ERR_OK) {
            $module_image_path = uploadModuleImage($_FILES['module_image'], $moduleId);
        }
        
        $sql = "INSERT INTO module (moduleId, title, description, content_text, content_video_url, content_pdf_path, module_image_path, instructorId) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Failed to prepare module statement: ' . $conn->error);
        }
        
        $stmt->bind_param("ssssssss", $moduleId, $title, $description, $content_text, $content_video_url, $content_pdf_path, $module_image_path, $instructorId);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create module: ' . $stmt->error);
        }
        createLog($conn, $instructorId, $title, $moduleId);
        $conn->commit();
        $stmt->close();
        $_SESSION['clear_form_data'] = true;
        $_SESSION['success_message'] = "Module '{$title}' created successfully with ID: {$moduleId}";
        
        header('Location: index.php');
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        if (isset($content_pdf_path) && file_exists($content_pdf_path)) {
            unlink($content_pdf_path);
        }
        if (isset($module_image_path) && file_exists($module_image_path)) {
            unlink($module_image_path);
        }
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: index.php');
        exit();
    } finally {
        $conn->autocommit(true);
    }
} else {
    header('Location: index.php');
    exit();
}
function createLog($conn, $instructorId, $title, $moduleId) {
    try {
        $activityLogId = uniqid('log_');
        $activity_details = "Instructor {$instructorId} created Module '{$title}' with ID: {$moduleId} at " . date('Y-m-d H:i:s');
        $logSql = "INSERT INTO user_activity_logs (activityLogId, user_type, user_id, activity_type, activity_details) 
                   VALUES (?, ?, ?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        if (!$logStmt) {
            throw new Exception('Failed to prepare log statement: ' . $conn->error);
        }
        $user_type = 'instructor';
        $activity_type = 'create_module';
        $logStmt->bind_param("sssss", $activityLogId, $user_type, $instructorId, $activity_type, $activity_details);
        if (!$logStmt->execute()) {
            throw new Exception('Failed to create activity log: ' . $logStmt->error);
        }
        $logStmt->close();
    } catch (Exception $e) {
        throw new Exception('Activity log creation failed: ' . $e->getMessage());
    }
}

function generateModuleId() {
    return 'MOD' . strtoupper(bin2hex(random_bytes(4)));
}

function uploadPdf($file, $moduleId) {
    $uploadDir = '../assets/pdf/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create PDF upload directory.');
        }
    }
    $allowedTypes = ['application/pdf'];
    $maxSize = 10 * 1024 * 1024;
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Only PDF files are allowed.');
    }
    if ($file['size'] > $maxSize) {
        throw new Exception('PDF file size must be less than 10MB.');
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if ($mimeType !== 'application/pdf') {
        throw new Exception('Invalid PDF file format.');
    }
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $moduleId . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to upload PDF file.');
    }
    return $filePath;
}

function uploadModuleImage($file, $moduleId) {
    $uploadDir = '../assets/images/module/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create image upload directory.');
        }
    }
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 10 * 1024 * 1024;
    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.');
    }
    if ($file['size'] > $maxSize) {
        throw new Exception('Image file size must be less than 5MB.');
    }
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('Invalid image file.');
    }
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($imageInfo['mime'], $allowedMimeTypes)) {
        throw new Exception('Invalid image MIME type.');
    }
    $imageName = $moduleId . '_' . time() . '.' . $fileExtension;
    $targetPath = $uploadDir . $imageName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to upload image.');
    }
    return $targetPath;
}
?>