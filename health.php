<?php
/**
 * Server diagnostic — visit /health.php then delete this file on production.
 */
header('Content-Type: text/plain; charset=utf-8');

echo "Niche Society — server check\n";
echo "============================\n\n";
echo 'PHP: ' . PHP_VERSION . "\n";
echo 'Host: ' . ($_SERVER['HTTP_HOST'] ?? '?') . "\n";
echo 'Document root: ' . ($_SERVER['DOCUMENT_ROOT'] ?? '?') . "\n\n";

$root = __DIR__;
$checks = [
    'config/config.php' => 'Main config (required)',
    'config/database.php' => 'Database loader (required)',
    'config/database.local.php' => 'DB credentials (required on cPanel)',
    'config/admin-settings.php' => 'Admin settings (required for CMS)',
    'index.php' => 'Homepage',
    '.htaccess' => 'Apache rules',
    'logs/' => 'Log folder (writable)',
];

foreach ($checks as $path => $label) {
    $full = $root . '/' . $path;
    $ok = (str_ends_with($path, '/')) ? is_dir($full) : is_file($full);
    echo ($ok ? '[OK] ' : '[MISSING] ') . $label . " ($path)\n";
}

echo "\n--- Config load test ---\n";
$config = $root . '/config/config.php';
if (!is_file($config)) {
    echo "STOP: config/config.php missing. Pull latest GitHub or copy config.example.php.\n";
    exit;
}

try {
    require_once $config;
    echo "[OK] config.php loaded\n";
    echo 'SITE_URL: ' . (defined('SITE_URL') ? SITE_URL : '?') . "\n";
    echo 'IS_LOCAL: ' . (defined('IS_LOCAL') ? (IS_LOCAL ? 'yes' : 'no') : '?') . "\n";
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "[OK] Database connected\n";
    } else {
        echo "[FAIL] Database not connected — create config/database.local.php with cPanel MySQL details\n";
    }
} catch (Throwable $e) {
    echo "[FAIL] " . $e->getMessage() . "\n";
}

echo "\nDelete health.php after fixing issues.\n";
