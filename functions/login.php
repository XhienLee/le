<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function login($userId, $pwd, $user_type){
    require 'db_connect.php';
    if ($user_type === 'admin') {
        $table = 'admins';
        $id_field = 'adminId';
        $redirect = 'admin/index.php';
    } elseif ($user_type === 'instructor') {
        $table = 'instructors';
        $id_field = 'instructorId';
        $redirect = 'instructor/index.php';
    } elseif ($user_type === 'student') {
        $table = 'students';
        $id_field = 'studentId';
        $redirect = 'student/index.php';
    } else{
        return "Invalid Parameter";
    }
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $id_field = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($pwd, $user['password'])) {
            $_SESSION['last_activity'] = time();
            $_SESSION['user_id'] = $user[$id_field];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_type'] = $user_type;
            $_SESSION['email'] = $user['email'];
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $log_id = 'LOG' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $log_stmt = $conn->prepare("INSERT INTO authentication_logs (logId, user_type, user_id, action, ip_address, user_agent) VALUES (?, ?, ?, 'login', ?, ?)");
            $log_stmt->bind_param("sssss", $log_id, $user_type, $userId, $ip_address, $user_agent);
            $log_stmt->execute();
            header("Location: $redirect");
            exit();
        } else {
            return "Invalid username or password.";
        }
    } else {
        return "Invalid username or password.";
    }
}
