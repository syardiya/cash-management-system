<?php
require_once __DIR__ . '/../config/database.php';

/**
 * User Authentication and Management Class
 * Implements secure authentication with protection against common attacks
 */
class User {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Register new user
     * @param array $data User registration data
     * @return array Result with status and message
     */
    public function register($data) {
        try {
            // Validate input
            $errors = $this->validateRegistration($data);
            if (!empty($errors)) {
                return ['status' => false, 'message' => implode(', ', $errors)];
            }
            
            // Check if email already exists
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return ['status' => false, 'message' => 'Email already registered'];
            }
            
            // Check if username already exists
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                return ['status' => false, 'message' => 'Username already taken'];
            }
            
            // Hash password using Argon2id (or bcrypt as fallback)
            if (defined('PASSWORD_ARGON2ID')) {
                $password_hash = password_hash($data['password'], PASSWORD_ARGON2ID);
            } else {
                $password_hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            }
            
            // Generate email verification token
            $verification_token = bin2hex(random_bytes(32));
            
            // Insert user
            $stmt = $this->conn->prepare("
                INSERT INTO users (
                    organization_id, username, email, password_hash, 
                    full_name, role, email_verification_token
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['organization_id'],
                $data['username'],
                $data['email'],
                $password_hash,
                $data['full_name'],
                $data['role'] ?? 'user',
                $verification_token
            ]);
            
            if ($result) {
                $user_id = $this->conn->lastInsertId();
                
                // Log activity
                $this->logActivity($user_id, $data['organization_id'], 'user_registered', 'users', $user_id);
                
                return [
                    'status' => true, 
                    'message' => 'Registration successful',
                    'user_id' => $user_id,
                    'verification_token' => $verification_token
                ];
            }
            
            return ['status' => false, 'message' => 'Registration failed'];
            
        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error occurred'];
        }
    }
    
    /**
     * User login with brute force protection
     * @param string $username Username or email
     * @param string $password Password
     * @return array Result with status and message
     */
    public function login($username, $password) {
        try {
            // Get user by username or email
            $stmt = $this->conn->prepare("
                SELECT u.*, o.name as org_name, o.status as org_status 
                FROM users u
                JOIN organizations o ON u.organization_id = o.id
                WHERE (u.username = ? OR u.email = ?) AND u.status = 'active'
            ");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                // Log failed attempt
                $this->logActivity(null, null, 'login_failed', null, null, ['username' => $username]);
                return ['status' => false, 'message' => 'Invalid credentials'];
            }
            
            // Check if account is locked
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
                return ['status' => false, 'message' => "Account locked. Try again in {$remaining} minutes"];
            }
            
            // Check organization status
            if ($user['org_status'] !== 'active') {
                return ['status' => false, 'message' => 'Organization is not active'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                // Increment failed attempts
                $failed_attempts = $user['failed_login_attempts'] + 1;
                $locked_until = null;
                
                if ($failed_attempts >= MAX_LOGIN_ATTEMPTS) {
                    $locked_until = date('Y-m-d H:i:s', time() + LOCKOUT_TIME);
                }
                
                $stmt = $this->conn->prepare("
                    UPDATE users 
                    SET failed_login_attempts = ?, locked_until = ?
                    WHERE id = ?
                ");
                $stmt->execute([$failed_attempts, $locked_until, $user['id']]);
                
                $this->logActivity($user['id'], $user['organization_id'], 'login_failed', 'users', $user['id']);
                
                return ['status' => false, 'message' => 'Invalid credentials'];
            }
            
            // Check if password needs rehashing (if algorithm was upgraded)
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$new_hash, $user['id']]);
            }
            
            // Reset failed attempts and update last login
            $stmt = $this->conn->prepare("
                UPDATE users 
                SET failed_login_attempts = 0, locked_until = NULL, last_login = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$user['id']]);
            
            // Create session
            $session_id = $this->createSession($user['id'], $user['organization_id']);
            
            // Log successful login
            $this->logActivity($user['id'], $user['organization_id'], 'login_success', 'users', $user['id']);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['organization_id'] = $user['organization_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['org_name'] = $user['org_name'];
            $_SESSION['session_id'] = $session_id;
            $_SESSION['last_activity'] = time();
            
            return [
                'status' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ];
            
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Login error occurred'];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['session_id'])) {
            // Delete session from database
            $stmt = $this->conn->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->execute([$_SESSION['session_id']]);
        }
        
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], $_SESSION['organization_id'] ?? null, 'logout', null, null);
        }
        
        // Destroy session
        session_unset();
        session_destroy();
    }
    
    /**
     * Create secure session
     */
    private function createSession($user_id, $org_id) {
        $session_id = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);
        
        $stmt = $this->conn->prepare("
            INSERT INTO sessions (id, user_id, organization_id, ip_address, user_agent, expires_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $session_id,
            $user_id,
            $org_id,
            get_user_ip(),
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            $expires_at
        ]);
        
        return $session_id;
    }
    
    /**
     * Validate registration data
     */
    private function validateRegistration($data) {
        $errors = [];
        
        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        }
        
        // Password strength check
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]/', $data['password'])) {
            $errors[] = 'Password must contain uppercase, lowercase, number, and special character';
        }
        
        if (empty($data['full_name'])) {
            $errors[] = 'Full name is required';
        }
        
        return $errors;
    }
    
    /**
     * Log activity for audit trail
     */
    private function logActivity($user_id, $org_id, $action, $entity_type = null, $entity_id = null, $details = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO activity_logs (
                    organization_id, user_id, action, entity_type, entity_id,
                    ip_address, user_agent, details
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $org_id,
                $user_id,
                $action,
                $entity_type,
                $entity_id,
                get_user_ip(),
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                $details ? json_encode($details) : null
            ]);
        } catch (PDOException $e) {
            error_log("Activity Log Error: " . $e->getMessage());
        }
    }
    
    /**
     * Clean expired sessions
     */
    public function cleanExpiredSessions() {
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE expires_at < NOW()");
        $stmt->execute();
    }
}
