<?php
/**
 * Maintenance Mode Checker
 * Redirects public visitors to maintenance.php when maintenance mode is enabled.
 */

function checkMaintenanceMode(): bool
{
    if (PHP_SAPI === 'cli' || (defined('SKIP_MAINTENANCE_CHECK') && SKIP_MAINTENANCE_CHECK)) {
        return false;
    }

    $current_file = basename($_SERVER['SCRIPT_FILENAME'] ?? $_SERVER['PHP_SELF'] ?? '');
    $exempt_scripts = ['maintenance.php'];
    if (in_array($current_file, $exempt_scripts, true)) {
        return false;
    }

    $admin_settings_file = __DIR__ . '/../config/admin-settings.php';
    $maintenance_settings = [];

    if (file_exists($admin_settings_file)) {
        include $admin_settings_file;
    }

    $maintenance_enabled = !empty($maintenance_settings['enabled']);
    if (!$maintenance_enabled) {
        return false;
    }

    $admin_bypass = $maintenance_settings['admin_bypass'] ?? true;
    if ($admin_bypass) {
        $current_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_admin_page = strpos($current_uri, '/admin/') !== false;
        $is_admin_session = !empty($_SESSION['admin_authenticated']);

        if ($is_admin_page || $is_admin_session) {
            return true;
        }
    }

    $redirect_url = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/maintenance.php';
    header('Location: ' . $redirect_url);
    exit;
}
