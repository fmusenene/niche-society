<?php
/**
 * Maintenance Mode Checker
 * Checks if maintenance mode is enabled and redirects to maintenance page
 */

function checkMaintenanceMode() {
    // Load admin settings
    $admin_settings_file = __DIR__ . '/../config/admin-settings.php';
    $maintenance_settings = [];
    
    if (file_exists($admin_settings_file)) {
        include $admin_settings_file;
    }
    
    // Check if maintenance mode is enabled
    $maintenance_enabled = $maintenance_settings['enabled'] ?? false;
    
    // Debug: Log maintenance status
    error_log("Maintenance check - Enabled: " . ($maintenance_enabled ? 'true' : 'false'));
    
    // Allow admin bypass if enabled
    $admin_bypass = $maintenance_settings['admin_bypass'] ?? true;
    $is_admin = false;
    
    if ($admin_bypass) {
        // Check if user is accessing admin pages or has admin session
        $current_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_admin_page = strpos($current_uri, '/admin/') !== false;
        $is_admin_session = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
        
        $is_admin = $is_admin_page || $is_admin_session;
        
        // Debug: Log admin status
        error_log("Admin bypass - Admin page: " . ($is_admin_page ? 'true' : 'false') . 
                 ", Admin session: " . ($is_admin_session ? 'true' : 'false') . 
                 ", Is admin: " . ($is_admin ? 'true' : 'false'));
    }
    
    // If maintenance is enabled and user is not admin, redirect to maintenance page
    if ($maintenance_enabled && !$is_admin) {
        // Don't redirect if already on maintenance page
        $current_file = basename($_SERVER['PHP_SELF']);
        
        // Debug: Log redirect decision
        error_log("Redirect check - Current file: " . $current_file . ", Should redirect: " . ($current_file !== 'maintenance.php' ? 'true' : 'false'));
        
        if ($current_file !== 'maintenance.php') {
            $redirect_url = (defined('SITE_URL') ? SITE_URL : 'http://localhost/niche-society-main') . '/maintenance.php';
            error_log("Redirecting to: " . $redirect_url);
            header('Location: ' . $redirect_url);
            exit;
        }
    }
    
    return $maintenance_enabled;
}
?>
