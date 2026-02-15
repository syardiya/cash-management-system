<?php
/**
 * Accounts API Endpoint
 * Handles account CRUD operations
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/AccountSource.php';

require_login();

$accountObj = new AccountSource();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }
        
        // Check if updating status
        if (isset($_GET['action']) && $_GET['action'] === 'update_status') {
            $result = $accountObj->update(
                $_POST['account_id'],
                $_SESSION['organization_id'],
                ['status' => $_POST['status'], 'name' => '', 'type' => ''] // Simplified, should fetch existing data
            );
            echo json_encode($result);
            exit;
        }
        
        // Create or update account
        $data = [
            'organization_id' => $_SESSION['organization_id'],
            'name' => sanitize_input($_POST['name'] ?? ''),
            'type' => sanitize_input($_POST['type'] ?? ''),
            'account_number' => sanitize_input($_POST['account_number'] ?? ''),
            'initial_balance' => floatval($_POST['initial_balance'] ?? 0),
            'currency' => sanitize_input($_POST['currency'] ?? 'IDR'),
            'description' => sanitize_input($_POST['description'] ?? ''),
            'created_by' => $_SESSION['user_id']
        ];
        
        if (!empty($_POST['account_id'])) {
            // Update existing account
            $result = $accountObj->update($_POST['account_id'], $_SESSION['organization_id'], $data);
        } else {
            // Create new account
            $result = $accountObj->create($data);
        }
        
        echo json_encode($result);
        
    } elseif ($method === 'GET') {
        $accounts = $accountObj->getAccounts($_SESSION['organization_id']);
        echo json_encode(['status' => true, 'data' => $accounts]);
        
    } else {
        echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    error_log("Accounts API Error: " . $e->getMessage());
    echo json_encode(['status' => false, 'message' => 'An error occurred']);
}
