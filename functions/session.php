<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define('INACTIVITY_LIMIT', 3600);
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > INACTIVITY_LIMIT)) {
    session_unset();
    session_destroy();
    header("Location: ../index.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}
function requireLogin($default_redirect = 'index.php') {
    if (!isLoggedIn()) {
        header("Location: $default_redirect");
        exit();
    }    
    $user_type = $_SESSION['user_type'] ?? null;    
    $redirects = [
        'admin' => '/le/admin/index.php',
        'instructor' => '/le/instructor/index.php',
        'student' => '/le/student/index.php'
    ];    
    if (!isset($redirects[$user_type])) {
        header("Location: /le/index.php");
        exit();
    }
    
    $correct_path = $redirects[$user_type];
    $current_script = $_SERVER['SCRIPT_NAME'];
    $user_directory = "/le/{$user_type}/";    
    if (!str_contains($current_script, $user_directory)) {
        header("Location: {$correct_path}");
        exit();
    }    
    return true;
}