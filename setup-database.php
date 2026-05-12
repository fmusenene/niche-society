<?php
// Quick database setup script
require_once 'config/config.php';

try {
    // Connect to MySQL without database
    $pdo = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        'root',
        '', // XAMPP default is empty password
        [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS niche_society CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database 'niche_society' created\n";
    
    // Use the database
    $pdo->exec("USE niche_society");
    
    // Read and execute schema file
    $schema = file_get_contents('database/schema.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ All tables created successfully!\n\n";
    
    // Show created tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Created tables:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    
    echo "\n🎉 DATABASE SETUP COMPLETE! You can now use the website.\n";
    
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}
