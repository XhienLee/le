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

function requireLogin($default_redirect = '/le/index.php') {
    if (!isLoggedIn()) {
        header("Location: $default_redirect");
        exit();
    }
    $user_type = $_SESSION['user_type'] ?? null;
    $redirects = [
        'admin' => '/le/admin/',
        'instructor' => '/le/instructor/',
        'student' => '/le/student/'
    ];
    if (!isset($redirects[$user_type])) {
        session_unset();
        session_destroy();
        header("Location: /le/index.php?error=invalid_user_type");
        exit();
    }
    $current_script = $_SERVER['SCRIPT_NAME'];
    $user_directory = $redirects[$user_type];
    if (!str_starts_with($current_script, $user_directory)) {
        $default_page = $user_directory . 'index.php';
        if ($current_script !== $default_page) {
            header("Location: $default_page");
            exit();
        }
    }
    return true;
}

function requireLoginSafe($allowed_paths = []) {
    if (!isLoggedIn()) {
        header("Location: /le/index.php");
        exit();
    }
    
    $user_type = $_SESSION['user_type'] ?? null;
    $current_script = $_SERVER['SCRIPT_NAME'];
    $user_directories = [
        'admin' => '/le/admin/',
        'instructor' => '/le/instructor/',
        'student' => '/le/student/'
    ];
    if (!isset($user_directories[$user_type])) {
        session_unset();
        session_destroy();
        header("Location: /le/index.php?error=invalid_user_type");
        exit();
    }
    $user_directory = $user_directories[$user_type];
    if (!empty($allowed_paths)) {
        $full_allowed_paths = array_map(function($path) use ($user_directory) {
            return $user_directory . ltrim($path, '/');
        }, $allowed_paths);
        
        if (!in_array($current_script, $full_allowed_paths)) {
            header("Location: {$user_directory}index.php");
            exit();
        }
    } else {
        if (!str_starts_with($current_script, $user_directory)) {
            header("Location: {$user_directory}index.php");
            exit();
        }
    }
    
    return true;
}
function logout($redirect_to = '/le/index.php') {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
    header("Location: $redirect_to");
    exit();
}
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'type' => $_SESSION['user_type'],
        'username' => $_SESSION['username'] ?? null,
        'name' => $_SESSION['name'] ?? null
    ];
}
?>