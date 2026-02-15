<?php
/**
 * Login API Endpoint
 * Handles both Admin (Username/Pass) and Member (Slug/Access Code) login
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Organization.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check Login Type
$login_type = $_POST['login_type'] ?? 'admin';

try {
    if ($login_type === 'admin') {
        // --- ADMIN LOGIN LOGIC ---
        $username = sanitize_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            echo json_encode(['status' => false, 'message' => 'Username and password are required']);
            exit;
        }
        
        $userObj = new User();
        $result = $userObj->login($username, $password);
        
        echo json_encode($result);
        
    } elseif ($login_type === 'member') {
        // --- MEMBER LOGIN LOGIC ---
        $org_slug = sanitize_input($_POST['org_slug'] ?? '');
        $access_code = sanitize_input($_POST['access_code'] ?? '');
        
        if (empty($org_slug) || empty($access_code)) {
            echo json_encode(['status' => false, 'message' => 'Organization ID and Access Code are required']);
            exit;
        }
        
        // Verify Access Code
        $orgObj = new Organization();
        $org = $orgObj->verifyAccessCode($org_slug, $access_code);
        
        if ($org) {
            // Check if organization is active
            if ($org['status'] !== 'active') {
                echo json_encode(['status' => false, 'message' => 'Organization is not active']);
                exit;
            }
            
            // Set Session for Member
            $_SESSION['user_id'] = 0; // Dummy ID for member
            $_SESSION['organization_id'] = $org['id'];
            $_SESSION['username'] = 'member_' . $org['id'];
            $_SESSION['full_name'] = 'Member';
            $_SESSION['role'] = 'member';
            $_SESSION['org_name'] = $org['name'];
            $_SESSION['last_activity'] = time();
            
            echo json_encode([
                'status' => true, 
                'message' => 'Login successful',
                'redirect' => 'dashboard.php'
            ]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Invalid Organization ID or Access Code']);
        }
        
    } else {
        echo json_encode(['status' => false, 'message' => 'Invalid login type']);
    }
    
} catch (Exception $e) {
    error_log("Login API Error: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Login failed. Please try again.'
    ]);
}
