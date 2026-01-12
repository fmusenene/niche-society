<?php
/**
 * Main Configuration File - EXAMPLE
 * Copy this file to config.php and update with your actual values
 */

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Site Configuration
define('SITE_NAME', 'Niche Society');
define('SITE_URL', 'http://localhost:8888/niche-society'); // Update for production
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOADS_URL', SITE_URL . '/uploads');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('FUNCTIONS_PATH', ROOT_PATH . '/functions');
define('LANG_PATH', ROOT_PATH . '/lang');
define('LOGS_PATH', ROOT_PATH . '/logs');

// Database
require_once __DIR__ . '/database.php';

// Default Language
define('DEFAULT_LANG', 'ar');
define('AVAILABLE_LANGS', ['ar', 'en']);

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Riyadh');

// Contact Information
define('CONTACT_EMAIL', 'info@niche-society.com');
define('CONTACT_PHONE', '+966 XX XXX XXXX');
define('CONTACT_ADDRESS_AR', 'الرياض، المملكة العربية السعودية');
define('CONTACT_ADDRESS_EN', 'Riyadh, Saudi Arabia');

// Social Media Links
define('SOCIAL_FACEBOOK', 'https://facebook.com/nichesociety');
define('SOCIAL_TWITTER', 'https://twitter.com/nichesociety');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/nichesociety');
define('SOCIAL_LINKEDIN', 'https://linkedin.com/company/nichesociety');

// SEO
define('SITE_DESCRIPTION_AR', 'نيش سوسايتي - حلول إدارية وتنظيمية استثنائية');
define('SITE_DESCRIPTION_EN', 'Niche Society - Exceptional Management Solutions');
define('SITE_KEYWORDS_AR', 'إدارة، تنظيم، خدمات فاخرة، نيش سوسايتي');
define('SITE_KEYWORDS_EN', 'management, organization, luxury services, niche society');

// Google Analytics (optional)
define('GA_TRACKING_ID', ''); // Add your GA tracking ID

// reCAPTCHA (optional)
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// Mail Configuration (for contact forms)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@example.com');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_FROM_EMAIL', 'noreply@niche-society.com');
define('SMTP_FROM_NAME', 'Niche Society');

// Security
define('ENCRYPTION_KEY', 'your-random-32-character-key-here'); // Generate a random key
define('HASH_ALGO', 'sha256');

// Rate Limiting
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes in seconds

// File Upload Settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Cache Settings
define('ENABLE_CACHE', false); // Enable in production
define('CACHE_DURATION', 3600); // 1 hour
