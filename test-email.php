<?php
/**
 * Email Delivery Test Script
 * Upload to production, run once via browser, then DELETE this file.
 * URL: https://niche-society.com/test-email.php?key=niche2026test
 */

$key = $_GET['key'] ?? '';
if ($key !== 'niche2026test') {
    http_response_code(403);
    die('Access denied. Use ?key=niche2026test');
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

if (file_exists(__DIR__ . '/config/email.php')) {
    require_once __DIR__ . '/config/email.php';
}
if (defined('SMTP_ENABLED') && SMTP_ENABLED && file_exists(__DIR__ . '/functions/mail-smtp.php')) {
    require_once __DIR__ . '/functions/mail-smtp.php';
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== Niche Society Email Test ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP: " . phpversion() . "\n\n";

echo "--- Configuration ---\n";
echo "SMTP_ENABLED: " . (defined('SMTP_ENABLED') && SMTP_ENABLED ? 'YES' : 'NO') . "\n";
echo "SMTP_HOST: " . (defined('SMTP_HOST') ? SMTP_HOST : 'NOT SET') . "\n";
echo "SMTP_PORT: " . (defined('SMTP_PORT') ? SMTP_PORT : 'NOT SET') . "\n";
echo "SMTP_USERNAME: " . (defined('SMTP_USERNAME') ? SMTP_USERNAME : 'NOT SET') . "\n";
echo "SMTP_PASSWORD: " . (defined('SMTP_PASSWORD') ? str_repeat('*', strlen(SMTP_PASSWORD)) : 'NOT SET') . "\n";
echo "CONTACT_EMAIL: " . CONTACT_EMAIL . "\n";
echo "CONTACT_EMAIL_CC: " . (defined('CONTACT_EMAIL_CC') ? CONTACT_EMAIL_CC : 'NOT SET') . "\n";
echo "sendMailSMTP exists: " . (function_exists('sendMailSMTP') ? 'YES' : 'NO') . "\n\n";

if (!function_exists('sendMailSMTP')) {
    die("ERROR: sendMailSMTP function not loaded. Check config/email.php and functions/mail-smtp.php");
}

$fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : CONTACT_EMAIL;
$fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Niche Society';
$subject = 'Test Email - ' . date('H:i:s');
$body = '<html><body><h2>Email Delivery Test</h2><p>Sent at ' . date('Y-m-d H:i:s') . '</p><p>If you see this, delivery is working.</p></body></html>';

// Test 1: Primary (info@)
echo "--- Test 1: Send to " . CONTACT_EMAIL . " ---\n";
$r1 = sendMailSMTP(CONTACT_EMAIL, $subject . ' [Primary]', $body, $fromEmail, $fromName, null);
echo "Result: " . ($r1 ? 'SUCCESS' : 'FAILED') . "\n\n";

// Test 2: Backup (Khadeeja@)
$cc = defined('CONTACT_EMAIL_CC') ? CONTACT_EMAIL_CC : '';
if ($cc) {
    echo "--- Test 2: Send to $cc ---\n";
    $r2 = sendMailSMTP($cc, $subject . ' [Backup]', $body, $fromEmail, $fromName, null);
    echo "Result: " . ($r2 ? 'SUCCESS' : 'FAILED') . "\n\n";
} else {
    $r2 = false;
    echo "--- Test 2: SKIPPED (no CONTACT_EMAIL_CC) ---\n\n";
}

// Test 3: External (Gmail)
$ext = 'fmusenene@gmail.com';
echo "--- Test 3: Send to $ext ---\n";
$r3 = sendMailSMTP($ext, $subject . ' [External]', $body, $fromEmail, $fromName, null);
echo "Result: " . ($r3 ? 'SUCCESS' : 'FAILED') . "\n\n";

echo "=== SUMMARY ===\n";
echo CONTACT_EMAIL . ": " . ($r1 ? 'SENT' : 'FAILED') . "\n";
echo ($cc ?: 'N/A') . ": " . ($r2 ? 'SENT' : 'FAILED') . "\n";
echo "$ext: " . ($r3 ? 'SENT' : 'FAILED') . "\n\n";
echo "Check all inboxes now. DELETE this file after testing!\n";
