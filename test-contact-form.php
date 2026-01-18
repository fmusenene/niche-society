<?php
/**
 * Test Contact Form Submission
 * 
 * This file helps diagnose contact form submission issues
 * Access it via: yourdomain.com/test-contact-form.php
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/helpers.php';

$lang = getCurrentLanguage();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .info-box { background: #f0f0f0; padding: 15px; margin: 10px 0; border-left: 4px solid #602234; }
        .success { background: #d4edda; border-left-color: #28a745; }
        .error { background: #f8d7da; border-left-color: #dc3545; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        h2 { color: #602234; }
    </style>
</head>
<body>
    <h1>Contact Form Diagnostic Tool</h1>
    
    <div class="info-box">
        <h2>Server Information</h2>
        <pre><?php
            echo "PHP Version: " . phpversion() . "\n";
            echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
            echo "Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'Not Set') . "\n";
            echo "Content Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not Set') . "\n";
            echo "Content Length: " . ($_SERVER['CONTENT_LENGTH'] ?? 'Not Set') . "\n";
        ?></pre>
    </div>
    
    <div class="info-box">
        <h2>Form Test</h2>
        <form method="POST" action="test-contact-form.php" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="test" value="1">
            <label>Test Name: <input type="text" name="test_name" value="Test User" required></label><br><br>
            <button type="submit">Test Form Submission</button>
        </form>
    </div>
    
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="info-box success">
            <h2>✓ POST Request Received</h2>
            <p><strong>POST Data:</strong></p>
            <pre><?php print_r($_POST); ?></pre>
            <p><strong>REQUEST_METHOD:</strong> <?= $_SERVER['REQUEST_METHOD'] ?></p>
            <p><strong>POST Data Count:</strong> <?= count($_POST) ?></p>
        </div>
    <?php else: ?>
        <div class="info-box error">
            <h2>✗ No POST Data</h2>
            <p><strong>REQUEST_METHOD:</strong> <?= $_SERVER['REQUEST_METHOD'] ?? 'Not Set' ?></p>
            <p>Submit the test form above to check if POST requests work.</p>
        </div>
    <?php endif; ?>
    
    <div class="info-box">
        <h2>Session Information</h2>
        <pre><?php
            echo "Session ID: " . session_id() . "\n";
            echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "\n";
            echo "CSRF Token: " . ($_SESSION['csrf_token'] ?? 'Not Set') . "\n";
        ?></pre>
    </div>
    
    <div class="info-box">
        <h2>Contact Handler Path</h2>
        <p><strong>Expected Path:</strong> <?= url('contact-handler.php') ?></p>
        <p><strong>File Exists:</strong> <?= file_exists(__DIR__ . '/contact-handler.php') ? 'Yes' : 'No' ?></p>
    </div>
    
    <div class="info-box">
        <h2>Recommendations</h2>
        <ul>
            <li>If POST requests work here but not in the contact form, check JavaScript console for errors</li>
            <li>If REQUEST_METHOD is not POST, your server/proxy might be modifying it</li>
            <li>Check server error logs for detailed error messages</li>
            <li>Verify .htaccess rules aren't interfering with form submissions</li>
        </ul>
    </div>
</body>
</html>
