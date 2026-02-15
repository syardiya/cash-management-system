<?php
/**
 * Database Configuration
 * Secure database connection with prepared statements
 */

// Check for dynamic credentials (created by setup.php)
$credentials_file = __DIR__ . '/db_credentials.php';

if (file_exists($credentials_file)) {
    require_once $credentials_file;
} else {
    // Default credentials (Localhost)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'cash_management_system');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
}
// Establish connection or redirect to setup
if (!defined('DB_HOST')) {
    // If we are not in CLI and setup.php exists in root, redirect
    if (php_sapi_name() !== 'cli' && file_exists(__DIR__ . '/../setup.php')) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        // Simple redirect attempt to root setup.php
        // Assuming database.php is included from a file in root or subfolder
        // Best effort: just stop if no creds
    }
}

class Database {
    private $conn;
    private static $instance = null;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            error_log("Database Connection Error: " . $e->getMessage());
            
            // Check if we are in production/hosting environment and setup hasn't run
            if (!file_exists(__DIR__ . '/db_credentials.php') && file_exists(__DIR__ . '/../setup.php') && php_sapi_name() !== 'cli') {
                // Try to redirect to setup.php
                // We need to calculate the relative path to setup.php
                $script_path = dirname($_SERVER['SCRIPT_NAME']);
                // Assuming app root is where setup.php is. 
                // A crude redirection to ./setup.php relative to the requested URL might work if we are in root.
                // Better: just die with a link.
                die("
                    <div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                        <h2>Database Connection Failed</h2>
                        <p>It seems you haven't configured the database yet.</p>
                        <a href='setup.php' style='background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Setup Wizard</a>
                    </div>
                ");
            }
            
            die("Database connection failed. Please contact administrator.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
