<?php
/**
 * Test file to verify maintenance mode is working
 */

session_start();

// Load maintenance checker
require_once __DIR__ . '/functions/maintenance-check.php';

echo "<h1>Maintenance Test</h1>";

// Load admin settings to check current status
$admin_settings_file = __DIR__ . '/config/admin-settings.php';
$maintenance_settings = [];

if (file_exists($admin_settings_file)) {
    include $admin_settings_file;
}

$maintenance_enabled = $maintenance_settings['enabled'] ?? false;

echo "<h2>Current Status:</h2>";
echo "<p><strong>Maintenance Enabled:</strong> " . ($maintenance_enabled ? 'YES' : 'NO') . "</p>";
echo "<p><strong>Current File:</strong> " . basename(__FILE__) . "</p>";
echo "<p><strong>Session Admin Auth:</strong> " . (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true ? 'YES' : 'NO') . "</p>";

echo "<h2>Admin Settings:</h2>";
echo "<pre>";
print_r($maintenance_settings);
echo "</pre>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Test Links:</h2>";
echo "<p><a href='/'>Homepage</a></p>";
echo "<p><a href='/maintenance.php'>Maintenance Page</a></p>";
echo "<p><a href='/admin/maintenance-admin.php'>Admin Panel</a></p>";
?>
