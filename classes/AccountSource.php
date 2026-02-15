<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Account Source Management Class
 * Manages different cash sources (Bank, E-wallet, Cash, etc.)
 */
class AccountSource {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Create new account source
     */
    public function create($data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO account_sources (
                    organization_id, name, type, account_number,
                    initial_balance, current_balance, currency,
                    description, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $initial_balance = $data['initial_balance'] ?? 0;
            
            $result = $stmt->execute([
                $data['organization_id'],
                $data['name'],
                $data['type'],
                $data['account_number'] ?? null,
                $initial_balance,
                $initial_balance, // current_balance starts with initial_balance
                $data['currency'] ?? 'IDR',
                $data['description'] ?? null,
                $data['created_by']
            ]);
            
            if ($result) {
                return [
                    'status' => true,
                    'message' => 'Account created successfully',
                    'account_id' => $this->conn->lastInsertId()
                ];
            }
            
            return ['status' => false, 'message' => 'Failed to create account'];
            
        } catch (PDOException $e) {
            error_log("Account Creation Error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error occurred'];
        }
    }
    
    /**
     * Get all accounts for organization
     */
    public function getAccounts($org_id, $active_only = true) {
        $query = "SELECT * FROM account_sources WHERE organization_id = ?";
        
        if ($active_only) {
            $query .= " AND status = 'active'";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$org_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get total balance from all accounts
     */
    public function getTotalBalance($org_id) {
        $stmt = $this->conn->prepare("
            SELECT 
                SUM(current_balance) as total_balance,
                COUNT(*) as total_accounts,
                SUM(CASE WHEN type = 'bank' THEN current_balance ELSE 0 END) as bank_balance,
                SUM(CASE WHEN type = 'e-wallet' THEN current_balance ELSE 0 END) as ewallet_balance,
                SUM(CASE WHEN type = 'cash' THEN current_balance ELSE 0 END) as cash_balance
            FROM account_sources
            WHERE organization_id = ? AND status = 'active'
        ");
        
        $stmt->execute([$org_id]);
        return $stmt->fetch();
    }
    
    /**
     * Get account by ID
     */
    public function getById($id, $org_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM account_sources 
            WHERE id = ? AND organization_id = ?
        ");
        $stmt->execute([$id, $org_id]);
        return $stmt->fetch();
    }
    
    /**
     * Update account
     */
    public function update($id, $org_id, $data) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE account_sources
                SET name = ?, type = ?, account_number = ?,
                    description = ?, status = ?
                WHERE id = ? AND organization_id = ?
            ");
            
            $result = $stmt->execute([
                $data['name'],
                $data['type'],
                $data['account_number'] ?? null,
                $data['description'] ?? null,
                $data['status'] ?? 'active',
                $id,
                $org_id
            ]);
            
            if ($result) {
                return ['status' => true, 'message' => 'Account updated successfully'];
            }
            
            return ['status' => false, 'message' => 'Failed to update account'];
            
        } catch (PDOException $e) {
            error_log("Account Update Error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error occurred'];
        }
    }
}
