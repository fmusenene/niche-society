<?php
/**
 * Database Configuration
 * XAMPP (Windows) — update credentials if yours differ
 * For production: copy database.local.php.example → database.local.php (not committed)
 */

$localDbFile = __DIR__ . '/database.local.php';
if (is_file($localDbFile)) {
    require_once $localDbFile;
}

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'niche_society');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

// MAMP socket (unused on XAMPP; only used when the path exists)
if (!defined('DB_SOCKET')) {
    define('DB_SOCKET', '/Applications/MAMP/tmp/mysql/mysql.sock');
}

$isLocal = (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false)
    || (strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false);

try {
    if ($isLocal && defined('DB_SOCKET') && file_exists(DB_SOCKET)) {
        $dsn = 'mysql:unix_socket=' . DB_SOCKET . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    } else {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    }

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('Database Connection Error: ' . $e->getMessage());
    if (defined('IS_LOCAL') && IS_LOCAL) {
        die('Database connection failed. Create the database "niche_society" in phpMyAdmin and import database/schema.sql');
    }
    http_response_code(503);
    die('Database connection failed. Check config/database.local.php on the server.');
}

return $pdo;
