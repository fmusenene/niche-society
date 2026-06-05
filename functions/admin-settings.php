<?php
/**
 * Load admin-settings.php fresh (avoids OPcache stale reads on cPanel/LiteSpeed).
 */
function adminSettingsFilePath(): string
{
    return dirname(__DIR__) . '/config/admin-settings.php';
}

function adminInvalidateSettingsCache(?string $path = null): void
{
    $path = $path ?? adminSettingsFilePath();
    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate($path, true);
    }
    clearstatcache(true, $path);
}

function adminLoadSettingsFile(): array
{
    $maintenance_settings = ['enabled' => false, 'message' => '', 'admin_bypass' => true];
    $site_settings = [];
    $admin_credentials = ['username' => 'admin', 'password' => ''];

    $path = adminSettingsFilePath();
    if (is_file($path)) {
        adminInvalidateSettingsCache($path);
        include $path;
    }

    $admin_credentials = array_merge(
        ['username' => 'admin', 'password' => ''],
        is_array($admin_credentials ?? null) ? $admin_credentials : []
    );

    return [
        'maintenance_settings' => is_array($maintenance_settings ?? null) ? $maintenance_settings : ['enabled' => false, 'message' => '', 'admin_bypass' => true],
        'site_settings' => is_array($site_settings ?? null) ? $site_settings : [],
        'admin_credentials' => $admin_credentials,
    ];
}

function adminMaintenanceEnabled(): bool
{
    $settings = adminLoadSettingsFile();
    return !empty($settings['maintenance_settings']['enabled']);
}
