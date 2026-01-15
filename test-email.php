<?php
/**
 * Email Diagnostic Test Script
 * Tests if emails can be sent to info@niche-society.com
 */

require_once __DIR__ . '/config/config.php';

// Test email configuration
echo "<h2>Email Configuration Test</h2>";
echo "<pre>";

echo "CONTACT_EMAIL: " . CONTACT_EMAIL . "\n";
echo "SMTP_ENABLED: " . (SMTP_ENABLED ? 'Yes' : 'No') . "\n";

if (SMTP_ENABLED) {
    echo "SMTP_HOST: " . SMTP_HOST . "\n";
    echo "SMTP_PORT: " . SMTP_PORT . "\n";
    echo "SMTP_USERNAME: " . (SMTP_USERNAME ? 'Set' : 'Not Set') . "\n";
} else {
    echo "\n⚠️  SMTP is disabled. Using PHP mail() function.\n";
    echo "Note: PHP mail() may not work on localhost/XAMPP without mail server configuration.\n";
}

echo "\n=== Testing Email Sending ===\n";

// Test 1: Send test email to info@niche-society.com
$to = CONTACT_EMAIL;
$subject = "Test Email from Niche Society Website";
$message = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { background: #602234; color: white; padding: 20px; }
        .content { padding: 20px; }
    </style>
</head>
<body>
    <div class='header'>
        <h2>Test Email</h2>
    </div>
    <div class='content'>
        <p>This is a test email to verify that emails are being sent correctly to <strong>info@niche-society.com</strong>.</p>
        <p>Time: " . date('Y-m-d H:i:s') . "</p>
        <p>If you receive this email, the email system is working correctly.</p>
    </div>
</body>
</html>
";

$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=utf-8',
    'From: Niche Society Website <' . CONTACT_EMAIL . '>',
    'Reply-To: ' . CONTACT_EMAIL,
    'X-Mailer: PHP/' . phpversion()
];

echo "Sending test email to: $to\n";
echo "Subject: $subject\n";
echo "From: " . CONTACT_EMAIL . "\n";

// Remove error suppression to see actual errors
$result = mail($to, $subject, $message, implode("\r\n", $headers));

if ($result) {
    echo "\n✅ Email sent successfully!\n";
    echo "Check the inbox (and spam folder) for: $to\n";
} else {
    $error = error_get_last();
    echo "\n❌ Email failed to send!\n";
    echo "Error: " . ($error ? $error['message'] : 'Unknown error') . "\n";
    echo "\nPossible issues:\n";
    echo "1. Mail server not configured on this server\n";
    echo "2. PHP mail() function disabled\n";
    echo "3. Firewall blocking outbound email\n";
    echo "4. SMTP configuration needed\n";
}

echo "\n=== Email Function Check ===\n";
if (function_exists('mail')) {
    echo "✅ PHP mail() function is available\n";
} else {
    echo "❌ PHP mail() function is NOT available\n";
}

echo "\n=== PHP Configuration ===\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";

echo "\n=== Recommendations ===\n";
if (!SMTP_ENABLED) {
    echo "1. Enable SMTP in config/config.php\n";
    echo "2. Configure SMTP settings (host, port, username, password)\n";
    echo "3. Use a service like SendGrid, Mailgun, or your hosting provider's SMTP\n";
}
echo "4. Check spam/junk folder for test emails\n";
echo "5. Verify email address exists and can receive emails\n";
echo "6. Check server mail logs for delivery issues\n";

echo "</pre>";

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/logs')) {
    @mkdir(__DIR__ . '/logs', 0755, true);
    echo "<p>Created logs directory for email debugging.</p>";
}

echo "<p><strong>Note:</strong> Check the <code>logs/</code> directory for detailed email logs.</p>";
?>
