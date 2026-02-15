<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Organization Management Class
 */
class Organization {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Create new organization
     */
    public function create($data) {
        try {
            // Validate input
            if (empty($data['name']) || empty($data['email'])) {
                return ['status' => false, 'message' => 'Name and email are required'];
            }
            
            // Generate unique slug
            $slug = $this->generateSlug($data['name']);
            
            // Generate access code
            $access_code = $this->generateAccessCode();
            
            // Check if email exists
            $stmt = $this->conn->prepare("SELECT id FROM organizations WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return ['status' => false, 'message' => 'Email already registered'];
            }
            
            // Insert organization
            $stmt = $this->conn->prepare("
                INSERT INTO organizations (name, slug, email, phone, address, member_access_code)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['name'],
                $slug,
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $access_code
            ]);
            
            if ($result) {
                $org_id = $this->conn->lastInsertId();
                
                // Create default categories for new organization
                $this->createDefaultCategories($org_id);
                
                return [
                    'status' => true,
                    'message' => 'Organization created successfully',
                    'organization_id' => $org_id,
                    'slug' => $slug,
                    'access_code' => $access_code
                ];
            }
            
            return ['status' => false, 'message' => 'Failed to create organization'];
            
        } catch (PDOException $e) {
            error_log("Organization Creation Error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error occurred'];
        }
    }
    
    /**
     * Get organization by ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM organizations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Generate unique access code
     */
    private function generateAccessCode() {
        do {
            // Generate 6-char random alphanumeric code
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            
            // Check unicity
            $stmt = $this->conn->prepare("SELECT id FROM organizations WHERE member_access_code = ?");
            $stmt->execute([$code]);
        } while ($stmt->fetch());
        
        return $code;
    }

    /**
     * Verify access code for login
     */
    public function verifyAccessCode($slug, $code) {
        $stmt = $this->conn->prepare("SELECT id, name, status FROM organizations WHERE slug = ? AND member_access_code = ?");
        $stmt->execute([$slug, $code]);
        return $stmt->fetch();
    }

    /**
     * Generate unique slug from name
     */
    private function generateSlug($name) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        // Check if slug exists
        $stmt = $this->conn->prepare("SELECT id FROM organizations WHERE slug = ?");
        $stmt->execute([$slug]);
        
        if ($stmt->fetch()) {
            $slug .= '-' . uniqid();
        }
        
        return $slug;
    }
    
    /**
     * Create default categories for organization
     */
    private function createDefaultCategories($org_id) {
        $categories = [
            ['Salary', 'income', 'fa-money-bill', '#10b981'],
            ['Investment Returns', 'income', 'fa-chart-line', '#059669'],
            ['Donations', 'income', 'fa-hand-holding-heart', '#34d399'],
            ['Food & Dining', 'expense', 'fa-utensils', '#ef4444'],
            ['Transportation', 'expense', 'fa-car', '#f97316'],
            ['Utilities', 'expense', 'fa-bolt', '#eab308'],
            ['Entertainment', 'expense', 'fa-film', '#ec4899'],
            ['Healthcare', 'expense', 'fa-heartbeat', '#dc2626'],
            ['Education', 'expense', 'fa-graduation-cap', '#8b5cf6'],
            ['Shopping', 'expense', 'fa-shopping-cart', '#f59e0b']
        ];
        
        $stmt = $this->conn->prepare("
            INSERT INTO categories (organization_id, name, type, icon, color)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($categories as $category) {
            $stmt->execute([
                $org_id,
                $category[0],
                $category[1],
                $category[2],
                $category[3]
            ]);
        }
    }
}
