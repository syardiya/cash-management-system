<?php
/**
 * Registration API Endpoint
 * Handles organization and admin user registration
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Organization.php';
require_once __DIR__ . '/../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get POST data
    $org_name = sanitize_input($_POST['org_name'] ?? '');
    $org_email = sanitize_input($_POST['org_email'] ?? '');
    $org_phone = sanitize_input($_POST['org_phone'] ?? '');
    $org_address = sanitize_input($_POST['org_address'] ?? '');
    
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate required fields
    if (empty($org_name) || empty($org_email) || empty($full_name) || 
        empty($username) || empty($email) || empty($password)) {
        echo json_encode(['status' => false, 'message' => 'All required fields must be filled']);
        exit;
    }
    
    // Create organization
    $organizationObj = new Organization();
    $orgResult = $organizationObj->create([
        'name' => $org_name,
        'email' => $org_email,
        'phone' => $org_phone,
        'address' => $org_address
    ]);
    
    if (!$orgResult['status']) {
        echo json_encode($orgResult);
        exit;
    }
    
    // Create admin user
    $userObj = new User();
    $userResult = $userObj->register([
        'organization_id' => $orgResult['organization_id'],
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'full_name' => $full_name,
        'role' => 'admin'
    ]);
    
    if (!$userResult['status']) {
        echo json_encode($userResult);
        exit;
    }
    
    echo json_encode([
        'status' => true,
        'message' => 'Registration successful! You can now login with your credentials.',
        'access_code' => $orgResult['access_code'] ?? null
    ]);
    
} catch (Exception $e) {
    error_log("Registration API Error: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Registration failed. Please try again.'
    ]);
}
