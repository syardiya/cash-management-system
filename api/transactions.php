<?php
/**
 * Transaction API Endpoint
 * Handles transaction CRUD operations
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Transaction.php';

require_login();

$transactionObj = new Transaction();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get transactions
            $filters = [
                'type' => $_GET['type'] ?? null,
                'account_id' => $_GET['account_id'] ?? null,
                'start_date' => $_GET['start_date'] ?? null,
                'end_date' => $_GET['end_date'] ?? null,
                'category' => $_GET['category'] ?? null,
                'limit' => $_GET['limit'] ?? null
            ];
            
            $transactions = $transactionObj->getTransactions($_SESSION['organization_id'], $filters);
            echo json_encode(['status' => true, 'data' => $transactions]);
            break;
            
        case 'POST':
            // Create transaction
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                echo json_encode(['status' => false, 'message' => 'Invalid CSRF token']);
                exit;
            }
            
            $data = [
                'organization_id' => $_SESSION['organization_id'],
                'account_source_id' => $_POST['account_id'] ?? null,
                'transaction_type' => $_POST['type'] ?? null,
                'category' => $_POST['category'] ?? null,
                'amount' => $_POST['amount'] ?? null,
                'description' => $_POST['description'] ?? null,
                'transaction_date' => $_POST['date'] ?? date('Y-m-d'),
                'created_by' => $_SESSION['user_id']
            ];
            
            $result = $transactionObj->create($data);
            echo json_encode($result);
            break;
            
        case 'DELETE':
            // Delete transaction
            parse_str(file_get_contents("php://input"), $_DELETE);
            
            if (!verify_csrf_token($_DELETE['csrf_token'] ?? '')) {
                echo json_encode(['status' => false, 'message' => 'Invalid CSRF token']);
                exit;
            }
            
            $transaction_id = $_DELETE['transaction_id'] ?? null;
            $result = $transactionObj->delete($transaction_id, $_SESSION['organization_id']);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['status' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Transaction API Error: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'An error occurred'
    ]);
}
