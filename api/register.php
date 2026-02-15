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
    // --- BETTER VALIDATION ---
    $org_name = sanitize_input($_POST['org_name'] ?? '');
    $org_email = sanitize_input($_POST['org_email'] ?? '');
    // ... existing sanitization ...
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // 1. Check if organization or user already exists BEFORE starting
    $stmt = $conn->prepare("SELECT id FROM organizations WHERE email = ? OR name = ?");
    $stmt->execute([$org_email, $org_name]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => false, 'message' => 'Organisasi atau Email Organisasi sudah terdaftar. Silakan gunakan nama/email lain.']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => false, 'message' => 'Username atau Email User sudah terdaftar.']);
        exit;
    }

    // 2. Start TRANSACTION
    $conn->beginTransaction();

    // Create organization
    $organizationObj = new Organization();
    $orgResult = $organizationObj->create([
        'name' => $org_name,
        'email' => $org_email,
        'phone' => $org_phone,
        'address' => $org_address
    ]);
    
    if (!$orgResult['status']) {
        $conn->rollBack();
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
        $conn->rollBack();
        echo json_encode($userResult);
        exit;
    }
    
    // Success - Commit everything
    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Registrasi Berhasil! Silakan login dengan akun yang baru Anda buat.',
        'access_code' => $orgResult['access_code'] ?? null
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Registration API Error: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
    ]);
}
