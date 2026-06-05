<?php
/**
 * Quick Maintenance Mode Control Script
 * Use this to quickly toggle maintenance mode when hosted
 */

// Simple password protection - change this to your preferred password
define('CONTROL_PASSWORD', 'admin123');

// Check if password is provided
$password = $_POST['password'] ?? $_GET['password'] ?? '';

if ($password !== CONTROL_PASSWORD) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Maintenance Control</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 50px; background: #f5f5f5; }
            .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
            input[type="submit"] { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
            .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
            .on { background: #d4edda; color: #155724; }
            .off { background: #f8d7da; color: #721c24; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Maintenance Control</h2>
            <form method="post">
                <label>Password:</label>
                <input type="password" name="password" required>
                <input type="submit" value="Login">
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Load admin settings
$admin_settings_file = __DIR__ . '/config/admin-settings.php';
$maintenance_settings = [];
$site_settings = [];
$admin_credentials = ['username' => 'admin', 'password' => ''];

if (file_exists($admin_settings_file)) {
    include $admin_settings_file;
}

// Handle actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = '';

if ($action === 'on') {
    $maintenance_settings['enabled'] = true;
    $message = 'Maintenance mode ENABLED';
} elseif ($action === 'off') {
    $maintenance_settings['enabled'] = false;
    $message = 'Maintenance mode DISABLED';
}

// Save settings if action was taken
if ($action && $message) {
    $settings_content = "<?php\n\n";
    $settings_content .= "// Admin Settings\n";
    $settings_content .= "\$site_settings = [\n";
    $settings_content .= "    'admin_email' => '" . ($site_settings['admin_email'] ?? 'info@niche-society.com') . "',\n";
    $settings_content .= "    'company_phone' => '" . ($site_settings['company_phone'] ?? '+966532447976') . "',\n";
    $settings_content .= "    'company_name' => '" . ($site_settings['company_name'] ?? 'Niche Society') . "'\n";
    $settings_content .= "];\n\n";
    $settings_content .= "// Maintenance Settings\n";
    $settings_content .= "\$maintenance_settings = [\n";
    $settings_content .= "    'enabled' => " . ($maintenance_settings['enabled'] ? 'true' : 'false') . ",\n";
    $settings_content .= "    'message' => '" . addslashes($maintenance_settings['message'] ?? 'We are currently performing scheduled maintenance to improve your experience.') . "',\n";
    $settings_content .= "    'admin_bypass' => true\n";
    $settings_content .= "];\n\n";
    $settings_content .= "\$admin_credentials = [\n";
    $settings_content .= "    'username' => '" . addslashes($admin_credentials['username'] ?? 'admin') . "',\n";
    $settings_content .= "    'password' => '" . addslashes($admin_credentials['password'] ?? '') . "'\n";
    $settings_content .= "];\n";

    file_put_contents($admin_settings_file, $settings_content);
}

// Show control panel
$current_status = $maintenance_settings['enabled'] ?? false;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Maintenance Control</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 50px; background: #f5f5f5; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .status { padding: 15px; margin: 15px 0; border-radius: 5px; text-align: center; font-weight: bold; }
        .on { background: #d4edda; color: #155724; }
        .off { background: #f8d7da; color: #721c24; }
        .buttons { display: flex; gap: 10px; justify-content: center; margin: 20px 0; }
        button { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-on { background: #28a745; color: white; }
        .btn-off { background: #dc3545; color: white; }
        .btn-test { background: #007cba; color: white; }
        .message { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Maintenance Mode Control</h2>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="status <?php echo $current_status ? 'on' : 'off'; ?>">
            Maintenance Mode is currently: <strong><?php echo $current_status ? 'ENABLED' : 'DISABLED'; ?></strong>
        </div>
        
        <div class="buttons">
            <form method="post" style="display: inline;">
                <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                <input type="hidden" name="action" value="on">
                <button type="submit" class="btn-on" <?php echo $current_status ? 'disabled' : ''; ?>>Enable</button>
            </form>
            
            <form method="post" style="display: inline;">
                <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                <input type="hidden" name="action" value="off">
                <button type="submit" class="btn-off" <?php echo !$current_status ? 'disabled' : ''; ?>>Disable</button>
            </form>
        </div>
        
        <div class="buttons">
            <a href="maintenance.php" class="btn-test" style="padding: 10px 20px; text-decoration: none; background: #007cba; color: white; border-radius: 5px; display: inline-block;">Test Maintenance Page</a>
            <a href="index.php" class="btn-test" style="padding: 10px 20px; text-decoration: none; background: #007cba; color: white; border-radius: 5px; display: inline-block;">Test Homepage</a>
        </div>
        
        <h3>Quick Access URLs:</h3>
        <ul>
            <li><strong>Admin Panel:</strong> <a href="admin/maintenance-admin.php">admin/maintenance-admin.php</a></li>
            <li><strong>Quick Control:</strong> <a href="maintenance-control.php">maintenance-control.php</a></li>
            <li><strong>Maintenance Page:</strong> <a href="maintenance.php">maintenance.php</a></li>
        </ul>
        
        <p><small>Default password: <code>admin123</code> (change this in the file for security)</small></p>
    </div>
</body>
</html>
