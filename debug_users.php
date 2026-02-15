<?php
/**
 * Advanced Debug & Repair Script - Check Registered Users & Table Health
 */
require_once 'config/config.php';

// --- ACTION HANDLER (Repair) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete_org' && isset($_GET['id'])) {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("DELETE FROM organizations WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        header("Location: debug_users.php?msg=Organization deleted successfully");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'reset_all') {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
        $conn->exec("TRUNCATE TABLE users");
        $conn->exec("TRUNCATE TABLE organizations");
        $conn->exec("TRUNCATE TABLE categories");
        $conn->exec("TRUNCATE TABLE transactions");
        $conn->exec("TRUNCATE TABLE account_sources");
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        header("Location: debug_users.php?msg=Database reset successfully");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

echo "<h1>🔍 Database Health & Repair Tools</h1>";
if (isset($_GET['msg'])) echo "<p style='color: green; font-weight: bold;'>✅ " . htmlspecialchars($_GET['msg']) . "</p>";
if (isset($error)) echo "<p style='color: red; font-weight: bold;'>❌ Error: $error</p>";

echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // 0. Check Table Existence
    echo "<h2>📋 Table Check</h2>";
    $tables = ['organizations', 'users', 'categories', 'transactions', 'account_sources'];
    echo "<ul>";
    foreach ($tables as $t) {
        $stmt = $conn->query("SHOW TABLES LIKE '$t'");
        $exists = $stmt->fetch();
        echo "<li>Table <strong>$t</strong>: " . ($exists ? "<span style='color:green;'>OK</span>" : "<span style='color:red;'>MISSING!</span>") . "</li>";
    }
    echo "</ul>";

    // 1. Check Organizations
    echo "<h2>🏢 Registered Organizations</h2>";
    echo "<p><small>Jika organisasi terdaftar tapi User kosong (nol), silakan HAPUS organisasi tersebut agar bisa Register ulang dengan nama yang sama.</small></p>";
    
    $stmt = $conn->query("SELECT o.*, (SELECT COUNT(*) FROM users u WHERE u.organization_id = o.id) as user_count FROM organizations o");
    $orgs = $stmt->fetchAll();
    
    if (empty($orgs)) {
        echo "<p style='color: red;'>No organizations found!</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f1f5f9;'><th>ID</th><th>Name</th><th>Slug</th><th>Access Code</th><th>Users</th><th>Actions</th></tr>";
        foreach ($orgs as $org) {
            echo "<tr>";
            echo "<td>{$org['id']}</td>";
            echo "<td><strong>{$org['name']}</strong></td>";
            echo "<td><code>{$org['slug']}</code></td>";
            echo "<td><code>{$org['member_access_code']}</code></td>";
            echo "<td>" . ($org['user_count'] > 0 ? $org['user_count'] : "<span style='color:red;'>0 (No Admin!)</span>") . "</td>";
            echo "<td><a href='debug_users.php?action=delete_org&id={$org['id']}' onclick='return confirm(\"Are you sure?\")' style='color: red;'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 2. Check Users
    echo "<h2>👤 Registered Users</h2>";
    $stmt = $conn->query("SELECT u.*, o.name as org_name FROM users u JOIN organizations o ON u.organization_id = o.id");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p style='color: red;'>No users found!</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f1f5f9;'><th>ID</th><th>Organization</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['org_name']}</td>";
            echo "<td><strong style='color: blue;'>{$user['username']}</strong></td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h2>🧨 Danger Zone</h2>";
    echo "<p>Reset semua data organisasi dan user ke kondisi awal (kosongkan semua tabel):</p>";
    echo "<a href='debug_users.php?action=reset_all' onclick='return confirm(\"PERHATIAN: Ini akan menghapus SEMUA data organisasi, user, dan transaksi. Lanjutkan?\")' style='background: #ef4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>RESET DATABASE TOTAL</a>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Critical Error: " . $e->getMessage() . "</p>";
}
