<?php
/**
 * Browser-based Database Table Table Installer
 * Runs SQL statements via PDO for maximum compatibility
 */
require_once 'config/config.php';

echo "<h1>🚀 Database Table Installer</h1>";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $sql_file = 'database.sql';
    if (!file_exists($sql_file)) {
        die("<p style='color: red;'>Error: $sql_file not found!</p>");
    }

    $sql = file_get_contents($sql_file);
    
    // Remove comments
    $sql = preg_replace('/--.*?\n/', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $success = 0;
    $errors = 0;

    echo "<ul>";
    foreach ($statements as $stmt_text) {
        if (empty($stmt_text)) continue;
        
        try {
            $conn->exec($stmt_text);
            $success++;
            // Extract table name for display if possible
            if (preg_match('/CREATE TABLE (`?\w+`?)/i', $stmt_text, $matches)) {
            }
        } catch (PDOException $e) {
            $errors++;
            echo "<li style='color: orange;'>Statement failed: " . substr($stmt_text, 0, 50) . "... <br>Error: " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";

    echo "<hr>";
    echo "<h3>Execution Results:</h3>";
    echo "<p>✅ Successfully executed: $success statements</p>";
    if ($errors > 0) {
        echo "<p style='color: orange;'>⚠️ Failed: $errors statements (Some might be expected if tables already exist)</p>";
    }
    
    echo "<div style='background: #e0f2fe; padding: 20px; border-radius: 10px;'>";
    echo "<h4>🎉 Apa selanjutnya?</h4>";
    echo "<p>1. Cek <a href='debug_users.php'>Debug Page</a> untuk memastikan tabel OK.</p>";
    echo "<p>2. Pergi ke <a href='register.php'>Halaman Register</a> untuk buat akun Admin asli.</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Critical Error: " . $e->getMessage() . "</p>";
}
