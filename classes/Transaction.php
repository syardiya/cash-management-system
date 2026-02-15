<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Transaction Management Class
 * Handles income, expense, and transfer transactions
 */
class Transaction {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Create new transaction
     */
    public function create($data) {
        try {
            $this->conn->beginTransaction();
            
            // Validate input
            if (empty($data['account_source_id']) || empty($data['amount']) || empty($data['transaction_type'])) {
                throw new Exception('Required fields are missing');
            }
            
            // Insert transaction
            $stmt = $this->conn->prepare("
                INSERT INTO transactions (
                    organization_id, account_source_id, transaction_type,
                    category, amount, description, transaction_date, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['organization_id'],
                $data['account_source_id'],
                $data['transaction_type'],
                $data['category'],
                $data['amount'],
                $data['description'] ?? null,
                $data['transaction_date'] ?? date('Y-m-d'),
                $data['created_by']
            ]);
            
            if (!$result) {
                throw new Exception('Failed to create transaction');
            }
            
            $transaction_id = $this->conn->lastInsertId();
            
            // Update account balance
            $this->updateAccountBalance(
                $data['account_source_id'],
                $data['amount'],
                $data['transaction_type']
            );
            
            $this->conn->commit();
            
            // Run anomaly detection
            $this->detectAnomalies($transaction_id, $data);
            
            return [
                'status' => true,
                'message' => 'Transaction created successfully',
                'transaction_id' => $transaction_id
            ];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Transaction Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get transactions for an organization
     */
    public function getTransactions($org_id, $filters = []) {
        $query = "
            SELECT t.*, a.name as account_name, a.type as account_type,
                   u.full_name as created_by_name
            FROM transactions t
            JOIN account_sources a ON t.account_source_id = a.id
            JOIN users u ON t.created_by = u.id
            WHERE t.organization_id = ?
        ";
        
        $params = [$org_id];
        
        // Add filters
        if (!empty($filters['type'])) {
            $query .= " AND t.transaction_type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['account_id'])) {
            $query .= " AND t.account_source_id = ?";
            $params[] = $filters['account_id'];
        }
        
        if (!empty($filters['start_date'])) {
            $query .= " AND t.transaction_date >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $query .= " AND t.transaction_date <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['category'])) {
            $query .= " AND t.category = ?";
            $params[] = $filters['category'];
        }
        
        $query .= " ORDER BY t.transaction_date DESC, t.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get transaction statistics
     */
    public function getStatistics($org_id, $period = 'month') {
        $date_condition = $this->getDateCondition($period);
        
        $stmt = $this->conn->prepare("
            SELECT 
                transaction_type,
                COUNT(*) as count,
                SUM(amount) as total,
                AVG(amount) as average
            FROM transactions
            WHERE organization_id = ? AND {$date_condition}
            GROUP BY transaction_type
        ");
        
        $stmt->execute([$org_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update account balance
     */
    private function updateAccountBalance($account_id, $amount, $type) {
        $operator = ($type === 'income') ? '+' : '-';
        
        $stmt = $this->conn->prepare("
            UPDATE account_sources
            SET current_balance = current_balance {$operator} ?
            WHERE id = ?
        ");
        
        $stmt->execute([$amount, $account_id]);
    }
    
    /**
     * Detect anomalies in transactions (AI-like rule-based detection)
     */
    private function detectAnomalies($transaction_id, $data) {
        try {
            $org_id = $data['organization_id'];
            $account_id = $data['account_source_id'];
            $amount = $data['amount'];
            
            // 1. Check for unusually high expense
            if ($data['transaction_type'] === 'expense') {
                $stmt = $this->conn->prepare("
                    SELECT AVG(amount) as avg_amount, STDDEV(amount) as std_dev
                    FROM transactions
                    WHERE organization_id = ? AND transaction_type = 'expense'
                    AND created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
                ");
                $stmt->execute([$org_id]);
                $stats = $stmt->fetch();
                
                if ($stats && $stats['avg_amount'] > 0) {
                    $threshold = $stats['avg_amount'] + (2 * ($stats['std_dev'] ?? 0));
                    
                    if ($amount > $threshold && $amount > $stats['avg_amount'] * 2) {
                        $this->createAnomalyLog([
                            'organization_id' => $org_id,
                            'transaction_id' => $transaction_id,
                            'anomaly_type' => 'high_expense',
                            'severity' => $amount > $threshold * 2 ? 'critical' : 'high',
                            'description' => sprintf(
                                'Pengeluaran tinggi terdeteksi: %s (rata-rata: %s). Pengeluaran ini %.0f%% lebih tinggi dari rata-rata.',
                                format_currency($amount),
                                format_currency($stats['avg_amount']),
                                (($amount / $stats['avg_amount']) - 1) * 100
                            ),
                            'suggested_action' => 'Periksa apakah transaksi ini valid. Pastikan tidak ada kesalahan input atau transaksi yang tidak sah.'
                        ]);
                    }
                }
            }
            
            // 2. Check for rapid transactions
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count
                FROM transactions
                WHERE organization_id = ? AND account_source_id = ?
                AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ");
            $stmt->execute([$org_id, $account_id]);
            $rapid = $stmt->fetch();
            
            if ($rapid['count'] >= 5) {
                $this->createAnomalyLog([
                    'organization_id' => $org_id,
                    'transaction_id' => $transaction_id,
                    'anomaly_type' => 'rapid_transactions',
                    'severity' => 'medium',
                    'description' => sprintf(
                        'Terdeteksi %d transaksi dalam 5 menit terakhir. Aktivitas yang tidak biasa.',
                        $rapid['count']
                    ),
                    'suggested_action' => 'Periksa apakah ada aktivitas yang mencurigakan atau kesalahan input berulang.'
                ]);
            }
            
            // 3. Check for negative balance
            $stmt = $this->conn->prepare("
                SELECT current_balance FROM account_sources WHERE id = ?
            ");
            $stmt->execute([$account_id]);
            $account = $stmt->fetch();
            
            if ($account && $account['current_balance'] < 0) {
                $this->createAnomalyLog([
                    'organization_id' => $org_id,
                    'transaction_id' => $transaction_id,
                    'anomaly_type' => 'negative_balance',
                    'severity' => 'high',
                    'description' => sprintf(
                        'Saldo akun menjadi negatif: %s. Periksa transaksi terakhir.',
                        format_currency($account['current_balance'])
                    ),
                    'suggested_action' => 'Pastikan saldo awal sudah benar atau ada transaksi yang perlu diperbaiki.'
                ]);
            }
            
            // 4. Check for duplicate suspicious transactions
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count
                FROM transactions
                WHERE organization_id = ? 
                AND account_source_id = ?
                AND amount = ?
                AND transaction_type = ?
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$org_id, $account_id, $amount, $data['transaction_type']]);
            $duplicate = $stmt->fetch();
            
            if ($duplicate['count'] >= 2) {
                $this->createAnomalyLog([
                    'organization_id' => $org_id,
                    'transaction_id' => $transaction_id,
                    'anomaly_type' => 'duplicate_suspicious',
                    'severity' => 'medium',
                    'description' => sprintf(
                        'Terdeteksi %d transaksi identik (%s) dalam 1 jam terakhir.',
                        $duplicate['count'],
                        format_currency($amount)
                    ),
                    'suggested_action' => 'Periksa apakah ada duplikasi data atau kesalahan input.'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Anomaly Detection Error: " . $e->getMessage());
        }
    }
    
    /**
     * Create anomaly log
     */
    private function createAnomalyLog($data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO anomaly_logs (
                    organization_id, transaction_id, anomaly_type,
                    severity, description, suggested_action
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['organization_id'],
                $data['transaction_id'],
                $data['anomaly_type'],
                $data['severity'],
                $data['description'],
                $data['suggested_action']
            ]);
        } catch (PDOException $e) {
            error_log("Anomaly Log Error: " . $e->getMessage());
        }
    }
    
    /**
     * Get date condition for queries
     */
    private function getDateCondition($period) {
        switch ($period) {
            case 'today':
                return "DATE(transaction_date) = CURDATE()";
            case 'week':
                return "transaction_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'month':
                return "transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            case 'year':
                return "transaction_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            default:
                return "1=1";
        }
    }
    
    /**
     * Delete transaction
     */
    public function delete($transaction_id, $org_id) {
        try {
            $this->conn->beginTransaction();
            
            // Get transaction details
            $stmt = $this->conn->prepare("
                SELECT * FROM transactions 
                WHERE id = ? AND organization_id = ?
            ");
            $stmt->execute([$transaction_id, $org_id]);
            $transaction = $stmt->fetch();
            
            if (!$transaction) {
                throw new Exception('Transaction not found');
            }
            
            // Reverse balance update
            $operator = ($transaction['transaction_type'] === 'income') ? '-' : '+';
            $stmt = $this->conn->prepare("
                UPDATE account_sources
                SET current_balance = current_balance {$operator} ?
                WHERE id = ?
            ");
            $stmt->execute([$transaction['amount'], $transaction['account_source_id']]);
            
            // Delete transaction
            $stmt = $this->conn->prepare("DELETE FROM transactions WHERE id = ?");
            $stmt->execute([$transaction_id]);
            
            $this->conn->commit();
            
            return ['status' => true, 'message' => 'Transaction deleted successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Transaction Delete Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
