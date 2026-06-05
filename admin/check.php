<?php
/**
 * Admin diagnostic — visit /admin/check.php then delete on production.
 */
header('Content-Type: text/plain; charset=utf-8');

echo "Admin check\n===========\n\n";

try {
    require_once __DIR__ . '/../config/config.php';
    echo "[OK] config.php\n";
    echo 'PHP: ' . PHP_VERSION . "\n";
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "[OK] Database connected\n";
    } else {
        echo "[FAIL] No database connection\n";
        exit;
    }

    require_once __DIR__ . '/../functions/cms.php';
    cmsEnsureTables($pdo);
    echo "[OK] CMS tables ensured\n";

    $adminSettings = __DIR__ . '/../config/admin-settings.php';
    echo (is_file($adminSettings) ? '[OK] ' : '[MISSING] ') . "config/admin-settings.php\n";

    $count = (int) $pdo->query('SELECT COUNT(*) FROM services')->fetchColumn();
    echo "Services in DB: {$count}\n";

    if (is_file(__DIR__ . '/seed-defaults.php')) {
        echo "[OK] seed-defaults.php present\n";
    }

    echo "\nTry admin login: /admin/index.php\n";
    if ($count === 0) {
        echo "Tip: first visit may import services; use /admin/index.php?skip_auto_seed=1 if that fails.\n";
    }
} catch (Throwable $e) {
    echo '[FAIL] ' . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}

echo "\nDelete admin/check.php when done.\n";
