<?php
// require db file
require_once '/var/www/config/db.php';

// test db connection (rm if successful)
try {
    // Test 1: Connection
    echo "✅ Connected to database successfully<br>";

    // Test 2: Run a simple query
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbName = $stmt->fetchColumn();
    echo "✅ Using database: " . htmlspecialchars($dbName) . "<br>";

    // Test 3: List your tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "✅ Tables found: " . count($tables) . "<br>";
    foreach ($tables as $table) {
        echo " - " . htmlspecialchars(array_values($table)[0]) . "<br>";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}

?>
