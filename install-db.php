<?php
// Standalone database setup - NO config dependencies
echo "🔧 Setting up database...\n\n";

try {
    // Direct connection to MySQL using MAMP socket
    $pdo = new PDO(
        "mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;charset=utf8mb4",
        'root',
        'root',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ Connected to MySQL\n";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS niche_society CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database 'niche_society' created\n";
    
    // Use database
    $pdo->exec("USE niche_society");
    
    // Read schema file
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    
    // Execute schema (split by semicolons)
    $statements = explode(';', $schema);
    $count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
            $count++;
        }
    }
    
    echo "✅ Executed $count SQL statements\n\n";
    
    // Show created tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Tables created (" . count($tables) . "):\n";
    foreach ($tables as $table) {
        echo "   ✓ $table\n";
    }
    
    echo "\n🎉 SUCCESS! Database is ready. Now refresh your browser!\n";
    
} catch (PDOException $e) {
    die("\n❌ ERROR: " . $e->getMessage() . "\n");
}
