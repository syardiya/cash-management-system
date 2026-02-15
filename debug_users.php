<?php
/**
 * Advanced Debug Script - Check Registered Users & Table Health
 */
require_once 'config/config.php';

echo "<h1>🔍 Database Health Checker</h1>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // 0. Check Table Existence
    echo "<h2>📋 Table Check</h2>";
    $tables = ['organizations', 'users', 'categories', 'transactions', 'account_sources'];
    echo "<ul>";
    foreach ($tables as $t) {
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$t]);
        $exists = $stmt->fetch();
        echo "<li>Table <strong>$t</strong>: " . ($exists ? "<span style='color:green;'>OK</span>" : "<span style='color:red;'>MISSING!</span>") . "</li>";
    }
    echo "</ul>";

    // 1. Write Test (Test if INSERT works)
    echo "<h2>📝 Write Test</h2>";
    try {
        $test_slug = 'test-' . time();
        $stmt = $conn->prepare("INSERT INTO organizations (name, slug, email) VALUES (?, ?, ?)");
        $stmt->execute(['Test Org', $test_slug, 'test-' . time() . '@example.com']);
        $new_id = $conn->lastInsertId();
        
        if ($new_id > 0) {
            echo "<p style='color: green;'>✅ Write Test SUCCESS! Inserted organization ID: $new_id</p>";
            // Clean up
            $conn->prepare("DELETE FROM organizations WHERE id = ?")->execute([$new_id]);
        } else {
            echo "<p style='color: red;'>❌ Write Test FAILED! lastInsertId returned 0.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Write Test ERROR: " . $e->getMessage() . "</p>";
    }

    // 2. Check Organizations
    echo "<h2>🏢 Registered Organizations</h2>";
    $stmt = $conn->query("SELECT id, name, slug, member_access_code, status FROM organizations");
    $orgs = $stmt->fetchAll();
    
    if (empty($orgs)) {
        echo "<p style='color: red;'>No organizations found!</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Slug (Login Member)</th><th>Access Code</th><th>Status</th></tr>";
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
    
    // 3. Check Users
    echo "<h2>👤 Registered Users</h2>";
    $stmt = $conn->query("SELECT id, organization_id, username, email, role, status FROM users");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p style='color: red;'>No users found! Tables might be empty.</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Org ID</th><th>Username (Login Admin)</th><th>Email</th><th>Role</th><th>Status</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['organization_id']}</td>";
            echo "<td><strong style='color: blue;'>{$user['username']}</strong></td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h3>💡 Diagnostic Tips:</h3>";
    echo "<ul>";
    echo "<li>Jika tabel <strong>MISSING</strong>: Berarti Anda harus import <code>database.sql</code> manual di phpMyAdmin hosting.</li>";
    echo "<li>Jika tabel OK tapi kosong: Coba ulangi pendaftaran di <code>/register.php</code>.</li>";
    echo "<li>Gunakan <strong>Username</strong> yang muncul di tabel Users untuk login Admin.</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Critical Error: " . $e->getMessage() . "</p>";
}
