<?php
include "../functions/users.php";
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'insert' => [
            [
                'message' => 'Invalid request method. Only POST requests are accepted.',
                'status' => false
            ]
        ]
    ]);
    exit;
}

$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (!$data || !isset($data['type'])) {
    echo json_encode([
        'insert' => [
            [
                'message' => 'Invalid or missing data.',
                'status' => false
            ]
        ]
    ]);
    exit;
}

if(!isset($data['admin'])){
    echo json_encode([
        'insert' => [
            [
                'message' => 'Invalid or missing data.',
                'status' => false
            ]
        ]
    ]);
    exit;
}

$type = $data['type'];
if (!in_array($type, ['student', 'instructor', 'admin'])) {
    echo json_encode([
        'insert' => [
            [
                'message' => 'Invalid user type. Allowed types are: student, instructor, admin.',
                'status' => false
            ]
        ]
    ]);
    exit;
}
$users = $data['users'];
$insertResults = [];
$adminId = $data['admin'];
foreach ($users as $index => $user) {
    $userErrors = [];
    $requiredFields = ['name', 'date_of_birth'];
    foreach ($requiredFields as $field) {
        if (!isset($user[$field]) || empty($user[$field])) {
            $userErrors[] = "Missing required field: {$field}";
        }
    }
    if (!empty($userErrors)) {
        $insertResults[] = [
            'message' => "User '{$user['name']}' validation failed: " . implode(', ', $userErrors),
            'status' => false
        ];
        continue;
    }
    try {
        $user['password'] = generatePassword($user['name'], $user['date_of_birth']);
        $user['email'] = generateEmail($user['name'], $user['date_of_birth']);
        $user['id'] = generateId($user['name'], $user['date_of_birth']);
        
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $insertResults[] = [
                'message' => "User '{$user['name']}' has invalid email format: {$user['email']}",
                'status' => false
            ];
            continue;
        }
    } catch (Exception $e) {
        $insertResults[] = [
            'message' => "User '{$user['name']}' data generation failed: " . $e->getMessage(),
            'status' => false
        ];
        continue;
    }
    try {
        $result = insertUser([$user], $type, $adminId);
        $resultData = json_decode($result, true);
        if (!$resultData['success']) {
            $message = $resultData['message'] ?? 'Unknown insertion error';
            if (strpos(strtolower($message), 'duplicate') !== false || 
                strpos(strtolower($message), 'primary key') !== false ||
                strpos(strtolower($message), 'unique') !== false) {
                $insertResults[] = [
                    'message' => "User '{$user['name']}' already exists (duplicate key)",
                    'status' => false
                ];
            } else {
                $insertResults[] = [
                    'message' => "User '{$user['name']}' insertion failed: " . $message,
                    'status' => false
                ];
            }
        } else {
            $insertResults[] = [
                'message' => "User '{$user['name']}' inserted successfully",
                'status' => true
            ];
        }
        
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        if (strpos(strtolower($errorMessage), 'duplicate') !== false || 
            strpos(strtolower($errorMessage), 'primary key') !== false ||
            strpos(strtolower($errorMessage), 'unique') !== false) {
            $insertResults[] = [
                'message' => "User '{$user['name']}' already exists (duplicate key)",
                'status' => false
            ];
        } else {
            $insertResults[] = [
                'message' => "User '{$user['name']}' insertion failed: " . $errorMessage,
                'status' => false
            ];
        }
    }
}
if (empty($insertResults)) {
    $insertResults[] = [
        'message' => 'No users provided for insertion',
        'status' => false
    ];
}

echo json_encode(['insert' => $insertResults]);
exit;
?>