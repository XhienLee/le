<?php
function generateId($fullName, $birthdate) {
    preg_match_all('/\b\w+\b/', $fullName, $matches);
    $lastname = strtolower(end($matches[0]));
    $date = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$date) {
        return "Invalid date format. Expected: YYYY-MM-DD.";
    }
    return $lastname.$date->format('Y-m-d');
}
function generateEmail($fullName, $birthdate) {
    $fullName = trim(preg_replace('/\s+/', ' ', $fullName));
    preg_match_all('/\b\w/', $fullName, $initialMatches);
    preg_match_all('/\b\w+\b/', $fullName, $wordMatches); 
    $nameParts = $wordMatches[0];
    $initials = '';
    if (count($nameParts) < 2) {
        return "Name must include at least a first name and surname.";
    }
    for ($i = 0; $i < count($nameParts) - 1; $i++) {
        $initials .= strtolower($nameParts[$i][0]);
    }
    $surname = strtolower(end($nameParts));
    $date = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$date) {
        return "Invalid date format. Expected: YYYY-MM-DD.";
    }
    $dateFormatted = $date->format('mdy');
    $email = $initials . $surname . $dateFormatted . '@cybersense.com';
    return $email;
}
function generatePassword($fullName, $birthdate) {
    preg_match_all('/\b\w+\b/', $fullName, $matches);
    $lastname = strtolower(end($matches[0]));
    $date = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$date) {
        return "Invalid date format. Expected: YYYY-MM-DD.";
    }
    return $lastname.'@'.$date->format('Y-m-d');
}
function insertUser($users, $type, $createdBy) {
    include "../functions/db_connect.php";  
    $conn->begin_transaction();
    try {
        if ($type === 'student') {
            $sql = "INSERT INTO students (studentId, full_name, email, password, date_of_birth, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        } elseif ($type === 'instructor') {
            $sql = "INSERT INTO instructors (instructorId, full_name, email, password, date_of_birth, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        } elseif ($type === 'admin') {
            $sql = "INSERT INTO admins (adminId, full_name, email, password, date_of_birth, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        }
        $stmt = $conn->prepare($sql);
        foreach ($users as $user) {
            $passwordHash = password_hash($user['password'], PASSWORD_BCRYPT);
            if ($type === 'student' || $type === 'instructor' || $type === 'admin') {
                $stmt->bind_param('ssssss', $user['id'], $user['name'], $user['email'], $passwordHash, $user['date_of_birth'], $createdBy);
            }
            $stmt->execute();
        }
        $conn->commit();
        return json_encode([
            'success' => true,
            'message' => count($users) . ' ' . ucfirst($type) . (count($users) !== 1 ? 's' : '') . ' saved successfully.',
            'count' => count($users)
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        return json_encode(['success' => false,'message' => 'Database error: ' . $e->getMessage()]);
    }
}


function getUserData($user_type) {
    global $conn;
    switch ($user_type) {
        case 'students':
        case 'instructors':
        case 'admins':
            $query = "SELECT * FROM $user_type";
            break;
        default:
            return json_encode(['error' => 'Invalid user type']);
            
    }
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return json_encode(['error' => 'Failed to prepare statement']);
        
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($users as &$user) {
            unset($user['password']);
        }
        return $users;
    }
}
function getIdFieldName($user_type) {
        if ($user_type === 'students') {
            return 'studentId';
        } else if ($user_type === 'instructors') {
            return 'instructorId';
        } else if ($user_type === 'admins') {
            return 'adminId';
        }
        return 'id';
    }
    
function saveUserData() {
    global $conn;
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
    $idField = getIdFieldName($user_type);
    $id = isset($_POST[$idField]) ? $_POST[$idField] : '';
    if (empty($id)) {
        return json_encode(['success' => false, 'message' => 'User ID is required']);
    }
    try {
        if ($user_type === 'students') {
            $stmt = $conn->prepare("UPDATE `students` SET `full_name` = ?, `date_of_birth` = ?, `email` = ?, `enrollment_status` = ? WHERE studentId = ?");
            $stmt->bind_param("sssss", $_POST['full_name'], $_POST['date_of_birth'], $_POST['email'], $_POST['enrollment_status'], $id);
        } else if ($user_type === 'instructors') {
            $stmt = $conn->prepare("UPDATE `instructors` SET `full_name` = ?, `date_of_birth` = ? WHERE instructorId = ?");
            $stmt->bind_param("sss", $_POST['full_name'], $_POST['date_of_birth'], $id);
        } else if ($user_type === 'admins') {
            $stmt = $conn->prepare("UPDATE `admins` SET `full_name` = ?, `date_of_birth` = ? WHERE adminId = ?");
            $stmt->bind_param("sss", $_POST['full_name'], $_POST['date_of_birth'], $id);
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute statement error: " . $stmt->error);
        }
        return json_encode(['success' => true,  'message' => 'User data updated successfully','affected_rows' => $stmt->affected_rows]); 
    } catch (Exception $e) {
        return json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
function deleteUser() {
    global $conn;
    $user_type = isset($_POST['user_type']) ? trim($_POST['user_type']) : '';
    $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    $admin_id = isset($_POST['admin_id']) ? trim($_POST['admin_id']) : '';
    if (empty($user_id)) {
        return json_encode(['success' => false,'message' => 'User ID is required']);
    }

    if (empty($user_type) || !in_array($user_type, ['students', 'instructors', 'admins'])) {
        return json_encode(['success' => false,'message' => 'Invalid user type']);
    }
    if (empty($admin_id)) {
        return json_encode(['success' => false,'message' => 'Admin ID is required for deletion tracking']);
    }
    
    if (empty($reason)) {
        return json_encode(['success' => false,'message' => 'Deletion reason is required']);
    }
    try {
        $idField = getIdFieldName($user_type);
        $check_sql = "SELECT $idField FROM $user_type WHERE $idField = ?";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception("Prepare check statement error: " . $conn->error);
        }
        $check_stmt->bind_param('s', $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();  
        if ($check_result->num_rows === 0) {
            return json_encode(['success' => false,'message' => 'User not found']);
        }
        $conn->begin_transaction();
        $user_info_sql = "SELECT full_name, email FROM $user_type WHERE $idField = ?";
        $user_info_stmt = $conn->prepare($user_info_sql);
        if (!$user_info_stmt) {
            throw new Exception("Prepare user info statement error: " . $conn->error);
        }
        $user_info_stmt->bind_param('s', $user_id);
        if (!$user_info_stmt->execute()) {
            throw new Exception("Execute user info statement error: " . $user_info_stmt->error);
        }
        $user_info_result = $user_info_stmt->get_result();
        $user_info = $user_info_result->fetch_assoc();
        $user_name = $user_info['full_name'] ?? 'Unknown';
        $user_email = $user_info['email'] ?? 'Unknown';
        $log_sql = "INSERT INTO user_activity_logs (activityLogId, user_type, user_id, activity_type, activity_details) VALUES (?, ?, ?, ?, ?)";
        $activityLogId = uniqid('act_', true);
        $activity_type = 'delete_user';
        $activity_details = json_encode([
            'reason' => $reason,
            'deleted_user' => ['id' => $user_id, 'type' => $user_type, 'name' => $user_name, 'email' => $user_email],
            'performed_by' => $admin_id
        ]);
        
        $log_stmt = $conn->prepare($log_sql);
        if (!$log_stmt) {
            throw new Exception("Prepare log statement error: " . $conn->error);
        }
        $log_stmt->bind_param('sssss', $activityLogId, $user_type, $admin_id, $activity_type, $activity_details);
        if (!$log_stmt->execute()) {
            throw new Exception("Execute log statement error: " . $log_stmt->error);
        }
        $delete_sql = "DELETE FROM $user_type WHERE $idField = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        if (!$delete_stmt) {
            throw new Exception("Prepare delete statement error: " . $conn->error);
        }
        $delete_stmt->bind_param('s', $user_id);
        if (!$delete_stmt->execute()) {
            throw new Exception("Execute delete statement error: " . $delete_stmt->error);
        }
        if ($delete_stmt->affected_rows === 0) {
            throw new Exception("No rows were deleted. User may not exist.");
        }
        $conn->commit();
        return json_encode([
            'success' => true,
            'message' => 'User deleted successfully',
            'deleted_user' => ['id' => $user_id, 'type' => $user_type, 'name' => $user_name ]
        ]);
        
        
    } catch (Exception $e) {
        $conn->rollback();
        if(str_contains($e->getMessage(), 'Cannot delete or update a parent row')){  
            return json_encode(['success' => false,'message' => 'User have records, can only be set inactive/suspended at edit action','error' => $e->getMessage()]); 
            
        }
        return json_encode(['success' => false,'message' => 'Failed to delete user','error' => $e->getMessage()]);
        
    }
}