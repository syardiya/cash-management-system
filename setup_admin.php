<?php
/**
 * Setup Admin User Script
 * Resolves "Invalid Credentials" issue by creating/resetting admin user
 */

require_once 'config/config.php';

// Check database connection
$db = Database::getInstance();
$conn = $db->getConnection();

$username = 'admin';
$password = 'Admin123!';
$email = 'admin@yourcompany.com';
$org_slug = 'demo-org';

echo "<h1>🛠️ Admin Account Setup</h1>";

try {
    // 1. Ensure Organization Exists
    $stmt = $conn->prepare("SELECT id FROM organizations WHERE slug = ?");
    $stmt->execute([$org_slug]);
    $org = $stmt->fetch();
    
    $org_id = 0;
    
    if (!$org) {
        echo "<p>Creating organization...</p>";
        $stmt = $conn->prepare("INSERT INTO organizations (name, slug, email, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Demo Organization', $org_slug, 'demo@cashmanagement.com', '081234567890', 'Jakarta']);
        $org_id = $conn->lastInsertId();
    } else {
        $org_id = $org['id'];
        echo "<p>Organization exists (ID: $org_id)</p>";
    }
    
    // 2. Check/Create Admin User
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Hash password with compatibility
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    if ($user) {
        echo "<p>Resetting password for user '$username'...</p>";
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, organization_id = ?, status = 'active', locked_until = NULL, failed_login_attempts = 0 WHERE username = ?");
        $stmt->execute([$hash, $org_id, $username]);
        echo "<p style='color: green;'>✅ Password reset successfully!</p>";
    } else {
        echo "<p>Creating new admin user '$username'...</p>";
        $stmt = $conn->prepare("INSERT INTO users (organization_id, username, email, password_hash, full_name, role, status, email_verified) VALUES (?, ?, ?, ?, ?, ?, 'active', 1)");
        $stmt->execute([$org_id, $username, $email, $hash, 'Administrator', 'admin']);
        echo "<p style='color: green;'>✅ User created successfully!</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 Login Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> admin</li>";
    echo "<li><strong>Password:</strong> Admin123!</li>";
    echo "</ul>";
    echo "<p><a href='login.php' style='background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error: " . $e->getMessage() . "</h3>";
}
