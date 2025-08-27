<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../functions/session.php';
requireLogin();
function generateActivityLogId() {
    return 'act_' . uniqid() . '_' . time() . '_' . mt_rand(1000, 9999);
}

function validateActivityData($user_type, $user_id, $activity_type, $activity_details) {
    $errors = [];
    if (empty($user_type) || !in_array($user_type, ['student', 'instructor', 'admin', 'system'])) {
        $errors[] = 'Invalid user type';
    }
    if (empty($user_id)) {
        $errors[] = 'User ID is required';
    }
    if (empty($activity_type)) {
        $errors[] = 'Activity type is required';
    }
    if (strlen($activity_details) > 5000) {
        $errors[] = 'Activity details too long (max 5000 characters)';
    }
    return $errors;
}
function getAllActivities($conn) {
    try {
        $sql = "SELECT * FROM user_activity_logs ORDER BY created_at";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Activities prepare failed: " . $conn->error);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $activities = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['activity_details']) {
                $decoded_details = json_decode($row['activity_details'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row['activity_details_parsed'] = $decoded_details;
                }
                $activities[] = $row;
            }
        }
        $stmt->close();
        return [
            'success' => true,
            'data' => $activities,
            'total' => count($activities)
        ];  
    } catch (Exception $e) {
        error_log("Get all activities error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error fetching activities: ' . $e->getMessage()
        ];
    }
}

$action = $_POST['action'] ?? null;
switch ($action) {
    case 'get_activities':
        include "../functions/db_connect.php";
        echo json_encode(getAllActivities($conn));
        break;
    default:
        echo json_encode(['success' == false, "message" => "Invalid action."]);
        break;
}
exit();
?>