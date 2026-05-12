<?php
/**
 * Navigation & Error Testing Script
 * Tests all pages, links, and checks for errors
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'functions/helpers.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$results = [];
$errors = [];

// Test database connection
try {
    $pdo->query("SELECT 1");
    $results[] = "✅ Database connection successful";
} catch (PDOException $e) {
    $errors[] = "❌ Database connection failed: " . $e->getMessage();
}

// List all PHP files that should exist
$requiredPages = [
    'index.php' => 'Homepage',
    'about.php' => 'About Page',
    'services.php' => 'Services Page',
    'contact.php' => 'Contact Page',
    'contact-handler.php' => 'Contact Handler',
    'blog.php' => 'Blog Page',
    'service-household-management.php' => 'Household Management Service',
];

// Check if required files exist
foreach ($requiredPages as $file => $name) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $results[] = "✅ $name ($file) exists";
        
        // Check for PHP syntax errors
        $output = [];
        $returnCode = 0;
        exec("php -l " . escapeshellarg(__DIR__ . '/' . $file) . " 2>&1", $output, $returnCode);
        
        if ($returnCode !== 0) {
            $errors[] = "❌ $name has PHP syntax errors: " . implode("\n", $output);
        } else {
            $results[] = "✅ $name has valid PHP syntax";
        }
    } else {
        $errors[] = "❌ $name ($file) is missing";
    }
}

// Check if required includes exist
$requiredIncludes = [
    'includes/header.php' => 'Header Include',
    'includes/footer.php' => 'Footer Include',
];

foreach ($requiredIncludes as $file => $name) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $results[] = "✅ $name exists";
    } else {
        $errors[] = "❌ $name is missing";
    }
}

// Check if CSS file exists
if (file_exists(__DIR__ . '/assets/css/style.css')) {
    $results[] = "✅ Main CSS file exists";
    $cssSize = filesize(__DIR__ . '/assets/css/style.css');
    $results[] = "ℹ️ CSS file size: " . round($cssSize / 1024, 2) . " KB";
} else {
    $errors[] = "❌ Main CSS file is missing";
}

// Check database tables
try {
    $tables = [
        'posts',
        'post_categories',
        'contact_forms',
        'newsletter_subscribers',
        'rate_limit_log',
        'security_logs'
    ];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $results[] = "✅ Table '$table' exists";
            
            // Get row count
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            $results[] = "ℹ️ Table '$table' has $count rows";
        } else {
            $errors[] = "❌ Table '$table' is missing";
        }
    }
} catch (PDOException $e) {
    $errors[] = "❌ Error checking database tables: " . $e->getMessage();
}

// Check configuration
if (defined('SITE_URL')) {
    $results[] = "✅ SITE_URL configured: " . SITE_URL;
} else {
    $errors[] = "❌ SITE_URL not configured";
}

if (defined('CONTACT_EMAIL')) {
    $results[] = "✅ CONTACT_EMAIL configured: " . CONTACT_EMAIL;
} else {
    $errors[] = "❌ CONTACT_EMAIL not configured";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation & Error Test - Niche Society</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 40px 20px;
            background: #f5f5f5;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #602234;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .summary-card {
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .summary-card.success {
            background: #d4edda;
            border: 2px solid #28a745;
        }
        
        .summary-card.error {
            background: #f8d7da;
            border: 2px solid #dc3545;
        }
        
        .summary-card h2 {
            font-size: 2.5rem;
            margin-bottom: 5px;
        }
        
        .summary-card.success h2 {
            color: #28a745;
        }
        
        .summary-card.error h2 {
            color: #dc3545;
        }
        
        .summary-card p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section h2 {
            color: #602234;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FFF7E7;
            font-size: 1.5rem;
        }
        
        .results-list {
            list-style: none;
        }
        
        .results-list li {
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 4px;
            font-size: 0.95rem;
        }
        
        .results-list li:nth-child(odd) {
            background: #f8f9fa;
        }
        
        .error-item {
            background: #fff3cd !important;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .footer {
            text-align: center;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #602234;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #4a1a28;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Niche Society - System Test</h1>
        <p class="subtitle">Comprehensive navigation and error checking</p>
        
        <div class="summary">
            <div class="summary-card success">
                <h2><?= count($results) ?></h2>
                <p>Successful Tests</p>
            </div>
            <div class="summary-card error">
                <h2><?= count($errors) ?></h2>
                <p>Errors Found</p>
            </div>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="section">
            <h2>❌ Errors & Issues</h2>
            <ul class="results-list">
                <?php foreach ($errors as $error): ?>
                <li class="error-item"><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="section">
            <h2>✅ Successful Tests</h2>
            <ul class="results-list">
                <?php foreach ($results as $result): ?>
                <li><?= htmlspecialchars($result) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>Test completed on:</strong> <?= date('Y-m-d H:i:s') ?></p>
            <p><strong>Total Tests:</strong> <?= count($results) + count($errors) ?></p>
            <a href="index.php" class="btn">← Back to Homepage</a>
        </div>
    </div>
</body>
</html>
