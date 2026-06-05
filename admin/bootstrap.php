<?php
/**
 * Admin bootstrap — auth, config, CMS tables
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../functions/cms.php';
require_once __DIR__ . '/lib/password.php';
require_once __DIR__ . '/lib/security.php';

cmsEnsureTables($pdo);
adminEnsureResetTable($pdo);
adminEnsureLoginAttemptsTable($pdo);
cmsEnsureDefaultServiceCategories($pdo);

// Auto-import hardcoded content when database has no services yet
$serviceCount = (int) $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
if ($serviceCount === 0 && empty($_GET['skip_auto_seed'])) {
    require_once __DIR__ . '/seed-defaults.php';
    cmsSeedDefaults($pdo, true);
}

$admin_settings_file = __DIR__ . '/../config/admin-settings.php';
$maintenance_settings = ['enabled' => false, 'message' => '', 'admin_bypass' => true];
$site_settings_file = [];
$admin_credentials = ['username' => 'admin', 'password' => ''];

if (file_exists($admin_settings_file)) {
    include $admin_settings_file;
}

$admin_credentials = array_merge(
    ['username' => 'admin', 'password' => ''],
    is_array($admin_credentials ?? null) ? $admin_credentials : []
);

function adminIsAuthenticated(): bool
{
    if (empty($_SESSION['admin_authenticated'])) {
        return false;
    }
    $timeout = defined('LOGIN_TIMEOUT') ? max(60, (int) LOGIN_TIMEOUT) : 900;
    $last = (int) ($_SESSION['admin_last_activity'] ?? $_SESSION['admin_login_at'] ?? 0);
    if ($last > 0 && (time() - $last) > $timeout) {
        unset($_SESSION['admin_authenticated'], $_SESSION['admin_last_activity'], $_SESSION['admin_login_at']);
        return false;
    }
    $_SESSION['admin_last_activity'] = time();
    return true;
}

function adminRequireAuth(): void
{
    if (!adminIsAuthenticated()) {
        return;
    }
}

function adminRedirect(string $query = ''): void
{
    $url = rtrim(SITE_URL, '/') . '/admin/index.php' . ($query ? '?' . $query : '');
    header('Location: ' . $url);
    exit;
}

function adminFlash(string $type, string $message): void
{
    $_SESSION['admin_flash'] = ['type' => $type, 'message' => $message];
}

function adminGetFlash(): ?array
{
    if (empty($_SESSION['admin_flash'])) {
        return null;
    }
    $flash = $_SESSION['admin_flash'];
    unset($_SESSION['admin_flash']);
    return $flash;
}

function adminWriteSettingsFile(array $maintenance, array $site, array $credentials): bool
{
    $path = __DIR__ . '/../config/admin-settings.php';
    $content = "<?php\n// Admin Settings — updated " . date('Y-m-d H:i:s') . "\n\n";
    $content .= '$maintenance_settings = ' . var_export($maintenance, true) . ";\n\n";
    $content .= '$site_settings = ' . var_export($site, true) . ";\n\n";
    $content .= '$admin_credentials = ' . var_export($credentials, true) . ";\n";
    return file_put_contents($path, $content) !== false;
}

function adminCsrfToken(): string
{
    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf_token'];
}

function adminVerifyCsrf(?string $token): bool
{
    return $token !== null && $token !== ''
        && !empty($_SESSION['admin_csrf_token'])
        && hash_equals($_SESSION['admin_csrf_token'], $token);
}
