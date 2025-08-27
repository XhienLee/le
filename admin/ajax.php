<?php
require_once '../functions/db_connect.php';
require_once '../functions/session.php';
require_once '../functions/users.php';
requireLogin();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
    exit;
}
$action = isset($_POST['action']) ? $_POST['action'] : '';
$user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
if (!in_array($user_type, ['students', 'instructors', 'admins'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid user type'
    ]);
    exit;
}

switch ($action) {
    case 'load_data':
        echo json_encode(getUserData($user_type));
        break;
    case 'save_user':
        echo saveUserData();
        break;
    case 'edit_user':
        echo json_encode(['success' => false,'error' => 'Edit functionality not yet implemented']);
        break;
    case 'delete_user':
        echo deleteUser();
        break;
    default:
        echo json_encode(['success' => false,'error' => 'Invalid action']);
        break;
}
?>