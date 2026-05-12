<?php
/**
 * Test Phone Number Loading
 */

// Load admin settings
$admin_settings_file = __DIR__ . '/config/admin-settings.php';
$site_settings = [];
$maintenance_settings = [];

if (file_exists($admin_settings_file)) {
    include $admin_settings_file;
}

// Use admin settings if available, otherwise fall back to defaults
$admin_email = isset($site_settings['admin_email']) ? $site_settings['admin_email'] : 'info@niche-society.com';
$company_phone = isset($site_settings['company_phone']) ? $site_settings['company_phone'] : '+966 1 1 296 7735';
$company_name = isset($site_settings['company_name']) ? $site_settings['company_name'] : 'Niche Society';

echo "<h1>Phone Number Test</h1>";
echo "<h2>Admin Settings:</h2>";
echo "<pre>";
print_r($site_settings);
echo "</pre>";

echo "<h2>Loaded Variables:</h2>";
echo "<p><strong>Admin Email:</strong> " . htmlspecialchars($admin_email) . "</p>";
echo "<p><strong>Company Phone:</strong> " . htmlspecialchars($company_phone) . "</p>";
echo "<p><strong>Company Name:</strong> " . htmlspecialchars($company_name) . "</p>";

echo "<h2>Direct File Check:</h2>";
echo "<p>Admin settings file exists: " . (file_exists($admin_settings_file) ? 'YES' : 'NO') . "</p>";
echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($admin_settings_file)) . "</p>";
?>
