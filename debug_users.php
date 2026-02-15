<?php
/**
 * Debug Script - Check Registered Users
 * Helps identify why login might fail.
 */
require_once 'config/config.php';

echo "<h1>🔍 Database User Checker</h1>";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

    // 1. Check Organizations
    echo "<h2>🏢 Organizations</h2>";
    $stmt = $conn->query("SELECT id, name, slug, member_access_code, status FROM organizations");
    $orgs = $stmt->fetchAll();
    
    if (empty($orgs)) {
        echo "<p style='color: red;'>No organizations found!</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Slug (for Member Login)</th><th>Access Code</th><th>Status</th></tr>";
        foreach ($orgs as $org) {
            echo "<tr>";
            echo "<td>{$org['id']}</td>";
            echo "<td>{$org['name']}</td>";
            echo "<td><code>{$org['slug']}</code></td>";
            echo "<td><code>{$org['member_access_code']}</code></td>";
            echo "<td>{$org['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 2. Check Users
    echo "<h2>👤 Users (Admins/Sub-accounts)</h2>";
    $stmt = $conn->query("SELECT u.id, u.organization_id, u.username, u.email, u.role, u.status, u.password_hash, o.slug as org_slug 
                          FROM users u 
                          JOIN organizations o ON u.organization_id = o.id");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p style='color: red;'>No users found! Please Register first at register.php or run setup_admin.php.</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Org ID</th><th>Org Slug</th><th>Username (for Admin Login)</th><th>Email</th><th>Role</th><th>Status</th><th>Password Test</th></tr>";
        foreach ($users as $user) {
            $is_demo_pass = password_verify('Admin123!', $user['password_hash']);
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['organization_id']}</td>";
            echo "<td><code>{$user['org_slug']}</code></td>";
            echo "<td><strong style='color: blue;'>{$user['username']}</strong></td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['status']}</td>";
            echo "<td>" . ($is_demo_pass ? "<span style='color: green;'>Match 'Admin123!'</span>" : "<span style='color: orange;'>Custom Password</span>") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<p>💡 <strong>Tips:</strong></p>";
    echo "<ul>";
    echo "<li>Gunakan <strong>Username</strong> di atas untuk login tab <strong>Admin</strong>.</li>";
    echo "<li>Gunakan <strong>Slug</strong> dan <strong>Access Code</strong> untuk login tab <strong>Member</strong>.</li>";
    echo "</ul>";
    echo "<p><a href='login.php'>Kembali ke Login</a></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
