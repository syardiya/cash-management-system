<?php
/**
 * Reset Password API Endpoint
 * Verifies token and updates password
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $token = sanitize_input($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($token) || empty($password)) {
        echo json_encode(['status' => false, 'message' => 'Token and password are required']);
        exit;
    }
    
    $userObj = new User();
    $result = $userObj->resetPassword($token, $password);
    
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'An error occurred.']);
}
