<?php
/**
 * Registration API Endpoint
 * Handles organization and admin user registration with Transactions
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
    // 1. Sanitize & Validate Inputs
    $org_name = sanitize_input($_POST['org_name'] ?? '');
    $org_email = sanitize_input($_POST['org_email'] ?? '');
    $org_phone = sanitize_input($_POST['org_phone'] ?? '');
    $org_address = sanitize_input($_POST['org_address'] ?? '');
    
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($org_name) || empty($org_email) || empty($full_name) || 
        empty($username) || empty($email) || empty($password)) {
        echo json_encode(['status' => false, 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // 2. Pre-Check existing data to avoid partial failures
    $stmt = $conn->prepare("SELECT id FROM organizations WHERE email = ? OR name = ?");
    $stmt->execute([$org_email, $org_name]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => false, 'message' => 'Email organisasi atau nama organisasi sudah terdaftar.']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => false, 'message' => 'Username atau Email User sudah digunakan.']);
        exit;
    }

    // 3. Start TRANSACTION (Ensure integrity)
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
    
    // Commit everything
    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Registrasi Berhasil! Silakan masuk dengan akun Admin Anda.',
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
