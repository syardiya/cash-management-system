<?php
/**
 * Anomaly API Endpoint
 * Handles anomaly resolution and retrieval
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

require_login();

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!verify_csrf_token($input['csrf_token'] ?? '')) {
            echo json_encode(['status' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }
        
        if ($input['action'] === 'resolve') {
            $stmt = $conn->prepare("
                UPDATE anomaly_logs 
                SET is_resolved = 1, resolved_by = ?, resolved_at = NOW()
                WHERE id = ? AND organization_id = ?
            ");
            
            $result = $stmt->execute([
                $_SESSION['user_id'],
                $input['anomaly_id'],
                $_SESSION['organization_id']
            ]);
            
            if ($result) {
                echo json_encode(['status' => true, 'message' => 'Anomaly resolved']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Failed to resolve anomaly']);
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $resolved = isset($_GET['resolved']) ? (int)$_GET['resolved'] : 0;
        
        $stmt = $conn->prepare("
            SELECT a.*, t.amount, t.category, t.transaction_date
            FROM anomaly_logs a
            LEFT JOIN transactions t ON a.transaction_id = t.id
            WHERE a.organization_id = ? AND a.is_resolved = ?
            ORDER BY a.detected_at DESC
        ");
        
        $stmt->execute([$_SESSION['organization_id'], $resolved]);
        $anomalies = $stmt->fetchAll();
        
        echo json_encode(['status' => true, 'data' => $anomalies]);
    }
    
} catch (Exception $e) {
    error_log("Anomaly API Error: " . $e->getMessage());
    echo json_encode(['status' => false, 'message' => 'An error occurred']);
}
