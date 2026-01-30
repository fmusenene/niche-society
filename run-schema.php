<?php
/**
 * Run database schema (tables + views).
 * Uses config/database.php so it works on XAMPP. Run once in browser or CLI.
 */

require_once __DIR__ . '/config/config.php';
// $pdo is set by config.php (it includes database.php)
if (!isset($pdo)) {
    $pdo = require __DIR__ . '/config/database.php';
}

$schemaPath = __DIR__ . '/database/schema.sql';
if (!is_readable($schemaPath)) {
    die("Schema file not found: database/schema.sql\n");
}

$sql = file_get_contents($schemaPath);

// Stop before stored procedures (they contain semicolons inside BEGIN...END)
$procedureStart = strpos($sql, 'DROP PROCEDURE IF EXISTS sp_log_activity');
if ($procedureStart !== false) {
    $sql = substr($sql, 0, $procedureStart);
}

// Split into statements (semicolon at end of line)
$statements = array_filter(
    array_map('trim', preg_split('/;\s*[\r\n]+/', $sql)),
    function ($s) {
        if ($s === '') return false;
        if (preg_match('/^\s*--/', $s)) return false;
        if (trim(strtoupper($s)) === 'END') return false;
        return true;
    }
);

$ok = 0;
$skipped = 0;
$errors = [];

foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if ($stmt === '') continue;
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (PDOException $e) {
        $code = $e->getCode();
        $msg = $e->getMessage();
        // Ignore "already exists" and duplicate key
        if ($code == '42S01' || $code == '42000' || strpos($msg, 'already exists') !== false || strpos($msg, 'Duplicate') !== false) {
            $skipped++;
        } else {
            $errors[] = $msg . ' [' . substr($stmt, 0, 60) . '...]';
        }
    }
}

// Indexes that appear after procedures in schema.sql
$indexes = [
    'CREATE INDEX idx_services_category_status ON services(category, status)',
    'CREATE INDEX idx_blog_status_published ON blog_posts(status, published_at)',
    'CREATE INDEX idx_contact_status_created ON contact_submissions(status, created_at)',
];
foreach ($indexes as $idx) {
    try {
        $pdo->exec($idx);
        $ok++;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) $skipped++;
        else $errors[] = $e->getMessage();
    }
}

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Schema</title></head><body><pre>\n";
echo "Executed: $ok statements. Skipped (already exists): $skipped.\n";
if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $e) echo "  - " . htmlspecialchars($e) . "\n";
}
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "\nTables: " . implode(', ', $tables) . "\n";
echo "\nDone. You can <a href='" . (isset($_SERVER['REQUEST_URI']) ? dirname($_SERVER['REQUEST_URI']) . '/blog.php' : 'blog.php') . "'>open the blog</a> or delete this file (run-schema.php).\n";
echo "</pre></body></html>";
