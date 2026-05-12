<?php
/**
 * Debug Maintenance Mode
 */

echo "<h1>Maintenance Mode Debug</h1>";

// Start session
session_start();

echo "<h2>Session Status:</h2>";
echo "<p>Admin Authenticated: " . (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true ? 'YES' : 'NO') . "</p>";

// Load admin settings
$admin_settings_file = __DIR__ . '/config/admin-settings.php';
$maintenance_settings = [];

if (file_exists($admin_settings_file)) {
    include $admin_settings_file;
    echo "<p>Admin settings file: FOUND</p>";
} else {
    echo "<p>Admin settings file: NOT FOUND</p>";
}

echo "<h2>Current Settings:</h2>";
echo "<pre>";
print_r($maintenance_settings);
echo "</pre>";

$maintenance_enabled = $maintenance_settings['enabled'] ?? false;
echo "<h2>Maintenance Status:</h2>";
echo "<p>Enabled: " . ($maintenance_enabled ? 'YES' : 'NO') . "</p>";

// Test redirect logic
$allow_bypass = false;
if ($maintenance_enabled) {
    $admin_bypass = $maintenance_settings['admin_bypass'] ?? true;
    
    if ($admin_bypass) {
        $current_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_admin_page = strpos($current_uri, '/admin/') !== false;
        $is_admin_session = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
        
        $allow_bypass = $is_admin_page || $is_admin_session;
        
        echo "<h2>Bypass Check:</h2>";
        echo "<p>Admin Bypass Enabled: " . ($admin_bypass ? 'YES' : 'NO') . "</p>";
        echo "<p>Current URI: " . htmlspecialchars($current_uri) . "</p>";
        echo "<p>Is Admin Page: " . ($is_admin_page ? 'YES' : 'NO') . "</p>";
        echo "<p>Is Admin Session: " . ($is_admin_session ? 'YES' : 'NO') . "</p>";
        echo "<p>Allow Bypass: " . ($allow_bypass ? 'YES' : 'NO') . "</p>";
    }
}

// Test redirect condition
$should_redirect = $maintenance_enabled && !$allow_bypass;
echo "<h2>Redirect Decision:</h2>";
echo "<p>Maintenance Enabled: " . ($maintenance_enabled ? 'YES' : 'NO') . "</p>";
echo "<p>Allow Bypass: " . ($allow_bypass ? 'YES' : 'NO') . "</p>";
echo "<p>Should Redirect: " . ($should_redirect ? 'YES' : 'NO') . "</p>";

echo "<h2>Test Links:</h2>";
echo "<p><a href='/'>Homepage (should redirect if maintenance enabled)</a></p>";
echo "<p><a href='/maintenance.php'>Maintenance Page</a></p>";
echo "<p><a href='/admin/maintenance-admin.php'>Admin Panel</a></p>";

// If maintenance is enabled and not admin, show redirect simulation
if ($should_redirect) {
    echo "<h2>REDIRECT WOULD HAPPEN HERE</h2>";
    echo "<p style='color: red; font-weight: bold;'>User would be redirected to maintenance.php</p>";
    
    // Uncomment below to actually redirect
    // header('Location: maintenance.php');
    // exit;
}
?>
