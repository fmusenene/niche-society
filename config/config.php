<?php
/**
 * Main Configuration File
 * Auto-detects HTTPS and site URL; tuned for fast, secure delivery.
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

$secretsFile = __DIR__ . '/secrets.local.php';
if (is_file($secretsFile)) {
    require_once $secretsFile;
}

$httpHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocalHost = (bool) preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/i', $httpHost);
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443)
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

$docRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ''), '/');
$rootPath = rtrim(str_replace('\\', '/', realpath(ROOT_PATH) ?: ROOT_PATH), '/');
$basePath = '';
if ($docRoot !== '' && strncmp($rootPath, $docRoot, strlen($docRoot)) === 0) {
    $basePath = substr($rootPath, strlen($docRoot));
}
if ($basePath === '' && $isLocalHost) {
    $basePath = '/niche-society-main';
}

$protocol = $isHttps ? 'https' : 'http';
if (!defined('IS_LOCAL')) {
    define('IS_LOCAL', $isLocalHost);
}
if (!defined('IS_HTTPS')) {
    define('IS_HTTPS', $isHttps);
}
if (!defined('SITE_URL')) {
    define('SITE_URL', rtrim($protocol . '://' . $httpHost . $basePath, '/'));
}

if (IS_LOCAL) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
}
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Niche Society');
}
if (!defined('ISO_CERTIFICATE_NUMBER')) {
    define('ISO_CERTIFICATE_NUMBER', '25EQQN01');
}
if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', SITE_URL . '/assets');
}
if (!defined('UPLOADS_URL')) {
    define('UPLOADS_URL', SITE_URL . '/uploads');
}

if (!defined('INCLUDES_PATH')) {
    define('INCLUDES_PATH', ROOT_PATH . '/includes');
}
if (!defined('FUNCTIONS_PATH')) {
    define('FUNCTIONS_PATH', ROOT_PATH . '/functions');
}
if (!defined('LANG_PATH')) {
    define('LANG_PATH', ROOT_PATH . '/lang');
}
if (!defined('LOGS_PATH')) {
    define('LOGS_PATH', ROOT_PATH . '/logs');
}

if (!defined('ASSET_VERSION')) {
    $styleCssPath = ROOT_PATH . '/assets/css/style.css';
    define('ASSET_VERSION', file_exists($styleCssPath) ? (string) filemtime($styleCssPath) : '1');
}

require_once __DIR__ . '/database.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log('Database bootstrap did not create $pdo.');
    if (!defined('IS_LOCAL') || !IS_LOCAL) {
        http_response_code(503);
        die('Database is not configured. Add config/database.local.php on the server (see DEPLOY.md).');
    }
}

if (!defined('RSS_AUTO_UPDATE')) {
    define('RSS_AUTO_UPDATE', true);
}
if (!defined('RSS_AUTO_UPDATE_INTERVAL')) {
    // Seconds between automatic RSS runs (default: 1 hour)
    define('RSS_AUTO_UPDATE_INTERVAL', 3600);
}

if (!defined('DEFAULT_LANG')) {
    define('DEFAULT_LANG', 'ar');
}
if (!defined('AVAILABLE_LANGS')) {
    define('AVAILABLE_LANGS', ['ar', 'en']);
}
if (!defined('SUPPORTED_LANGUAGES')) {
    define('SUPPORTED_LANGUAGES', AVAILABLE_LANGS);
}

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_secure', $isHttps ? '1' : '0');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

if (!defined('SKIP_MAINTENANCE_CHECK') || !SKIP_MAINTENANCE_CHECK) {
    require_once FUNCTIONS_PATH . '/maintenance-check.php';
    checkMaintenanceMode();
}

date_default_timezone_set('Asia/Riyadh');

if (!defined('CONTACT_EMAIL')) {
    define('CONTACT_EMAIL', 'info@niche-society.com');
}
if (!defined('CONTACT_PHONE')) {
    define('CONTACT_PHONE', '+966532447976');
}
if (!defined('CONTACT_ADDRESS_AR')) {
    define('CONTACT_ADDRESS_AR', 'الرياض، المملكة العربية السعودية');
}
if (!defined('CONTACT_ADDRESS_EN')) {
    define('CONTACT_ADDRESS_EN', 'Riyadh, Saudi Arabia');
}

if (!defined('SOCIAL_FACEBOOK')) {
    define('SOCIAL_FACEBOOK', 'https://facebook.com/nichesociety');
}
if (!defined('SOCIAL_TWITTER')) {
    define('SOCIAL_TWITTER', 'https://twitter.com/nichesociety');
}
if (!defined('SOCIAL_INSTAGRAM')) {
    define('SOCIAL_INSTAGRAM', 'https://instagram.com/nichesociety');
}
if (!defined('SOCIAL_LINKEDIN')) {
    define('SOCIAL_LINKEDIN', 'https://linkedin.com/company/nichesociety');
}

if (!defined('SITE_DESCRIPTION_AR')) {
    define('SITE_DESCRIPTION_AR', 'نيش سوسايتي - حلول إدارية وتنظيمية استثنائية');
}
if (!defined('SITE_DESCRIPTION_EN')) {
    define('SITE_DESCRIPTION_EN', 'Niche Society - Exceptional Management Solutions');
}
if (!defined('SITE_KEYWORDS_AR')) {
    define('SITE_KEYWORDS_AR', 'إدارة، تنظيم، خدمات فاخرة، نيش سوسايتي');
}
if (!defined('SITE_KEYWORDS_EN')) {
    define('SITE_KEYWORDS_EN', 'management, organization, luxury services, niche society');
}

if (!defined('TRANSLATE_API_KEY')) {
    define('TRANSLATE_API_KEY', '');
}
if (!defined('GA_TRACKING_ID')) {
    define('GA_TRACKING_ID', '');
}
if (!defined('RECAPTCHA_SITE_KEY')) {
    define('RECAPTCHA_SITE_KEY', '');
}
if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', '');
}

if (!defined('SMTP_HOST')) {
    define('SMTP_HOST', 'smtp.example.com');
}
if (!defined('SMTP_PORT')) {
    define('SMTP_PORT', 587);
}
if (!defined('SMTP_USERNAME')) {
    define('SMTP_USERNAME', 'your-email@example.com');
}
if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', 'your-password');
}
if (!defined('SMTP_FROM_EMAIL')) {
    define('SMTP_FROM_EMAIL', 'noreply@niche-society.com');
}
if (!defined('SMTP_FROM_NAME')) {
    define('SMTP_FROM_NAME', 'Niche Society');
}

if (!defined('ENCRYPTION_KEY')) {
    define('ENCRYPTION_KEY', 'change-this-to-a-random-32-char-key');
}
if (!defined('HASH_ALGO')) {
    define('HASH_ALGO', 'sha256');
}

if (!IS_LOCAL && defined('ENCRYPTION_KEY') && ENCRYPTION_KEY === 'change-this-to-a-random-32-char-key') {
    error_log('SECURITY: Set a unique ENCRYPTION_KEY in config/secrets.local.php on production.');
}

if (!defined('MAX_LOGIN_ATTEMPTS')) {
    define('MAX_LOGIN_ATTEMPTS', 5);
}
if (!defined('LOGIN_TIMEOUT')) {
    define('LOGIN_TIMEOUT', 900);
}

if (!defined('MAX_UPLOAD_SIZE')) {
    define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
}
if (!defined('ALLOWED_FILE_TYPES')) {
    define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);
}

if (!defined('ENABLE_CACHE')) {
    define('ENABLE_CACHE', !IS_LOCAL);
}
if (!defined('CACHE_DURATION')) {
    define('CACHE_DURATION', 3600);
}

// Auto-update blog news from RSS in the background (non-blocking)
if (RSS_AUTO_UPDATE && defined('FUNCTIONS_PATH') && is_file(FUNCTIONS_PATH . '/rss-scheduler.php')) {
    require_once FUNCTIONS_PATH . '/rss-scheduler.php';
    rssMaybeRunInBackground();
}
