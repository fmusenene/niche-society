<?php
/**
 * Database Configuration - EXAMPLE
 * Copy this file to database.php and update with your actual database credentials
 * 
 * MySQL connection settings with environment detection
 */

// Database credentials - UPDATE THESE
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// MAMP socket path (only for local development with MAMP)
define('DB_SOCKET', '/Applications/MAMP/tmp/mysql/mysql.sock');

// Detect environment
$isLocal = (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false) || 
           (strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false);

// Create PDO connection
try {
    // Use socket for local MAMP, standard host:port for production
    if ($isLocal && file_exists(DB_SOCKET)) {
        $dsn = "mysql:unix_socket=" . DB_SOCKET . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    } else {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    }
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please check your configuration.");
}

return $pdo;
