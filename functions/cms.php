<?php
/**
 * CMS helpers — database-backed site content
 */

function cmsEnsureTables(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS cms_pages (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        page_key VARCHAR(50) NOT NULL UNIQUE,
        title_ar VARCHAR(255) DEFAULT NULL,
        title_en VARCHAR(255) DEFAULT NULL,
        content_json LONGTEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_type ENUM('text', 'textarea', 'number', 'boolean', 'json') DEFAULT 'text',
        category VARCHAR(50) DEFAULT 'general',
        description VARCHAR(255),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_category (category),
        INDEX idx_key (setting_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    cmsEnsureServicesTable($pdo);

    if (file_exists(__DIR__ . '/invoices.php')) {
        require_once __DIR__ . '/invoices.php';
        cmsEnsureInvoicesTable($pdo);
    }

    if (file_exists(__DIR__ . '/work-documents.php')) {
        require_once __DIR__ . '/work-documents.php';
        cmsEnsureWorkDocumentsTable($pdo);
    }
}

function cmsTableExists(PDO $pdo, string $table): bool
{
    $table = preg_replace('/[^a-z0-9_]/', '', $table);
    $stmt = $pdo->query('SHOW TABLES LIKE ' . $pdo->quote($table));
    return (bool) $stmt->fetch(PDO::FETCH_NUM);
}

function cmsColumnExists(PDO $pdo, string $table, string $column): bool
{
    if (!cmsTableExists($pdo, $table)) {
        return false;
    }
    $table = preg_replace('/[^a-z0-9_]/', '', $table);
    $column = preg_replace('/[^a-z0-9_]/', '', $column);
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}` LIKE " . $pdo->quote($column));
    return (bool) $stmt->fetch(PDO::FETCH_NUM);
}

function cmsEnsureServicesTable(PDO $pdo): void
{
    if (!cmsTableExists($pdo, 'services')) {
        $pdo->exec("CREATE TABLE services (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(100) NOT NULL UNIQUE,
            category VARCHAR(100) NOT NULL DEFAULT 'general',
            title_ar VARCHAR(200) NOT NULL,
            title_en VARCHAR(200) NOT NULL,
            description_ar TEXT NOT NULL,
            description_en TEXT NOT NULL,
            content_ar LONGTEXT,
            content_en LONGTEXT,
            page_data LONGTEXT NULL,
            icon VARCHAR(100) NULL,
            image VARCHAR(255) NULL,
            listing_features_ar TEXT NULL,
            listing_features_en TEXT NULL,
            featured TINYINT(1) DEFAULT 0,
            display_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            meta_title_ar VARCHAR(200) NULL,
            meta_title_en VARCHAR(200) NULL,
            meta_description_ar TEXT NULL,
            meta_description_en TEXT NULL,
            meta_keywords_ar VARCHAR(255) NULL,
            meta_keywords_en VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_slug (slug),
            INDEX idx_featured (featured),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        return;
    }

    $additions = [
        'page_data' => 'LONGTEXT NULL',
        'icon' => 'VARCHAR(100) NULL',
        'listing_features_ar' => 'TEXT NULL',
        'listing_features_en' => 'TEXT NULL',
    ];
    foreach ($additions as $col => $definition) {
        if (!cmsColumnExists($pdo, 'services', $col)) {
            $pdo->exec("ALTER TABLE services ADD COLUMN `{$col}` {$definition}");
        }
    }

    cmsMigrateServiceCategoryColumn($pdo);
}

/** Default service category slugs (used when no custom list is saved). */
function cmsGetDefaultServiceCategories(): array
{
    return ['household', 'events', 'protocol', 'properties', 'consulting', 'vip'];
}

/** Convert ENUM category column to VARCHAR so new categories can be added. */
function cmsMigrateServiceCategoryColumn(PDO $pdo): void
{
    if (!cmsTableExists($pdo, 'services')) {
        return;
    }
    $stmt = $pdo->query("SHOW COLUMNS FROM services LIKE 'category'");
    $col = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($col && stripos($col['Type'] ?? '', 'enum') !== false) {
        $pdo->exec("ALTER TABLE services MODIFY category VARCHAR(100) NOT NULL DEFAULT 'general'");
    }
}

/** Normalize category slug: lowercase letters, numbers, hyphens. */
function cmsNormalizeServiceCategory(string $category): string
{
    $category = strtolower(trim($category));
    $category = preg_replace('/[^a-z0-9]+/', '-', $category);
    return trim($category, '-') ?: 'general';
}

/** All categories for admin datalist (saved list + any used on services). */
function cmsGetServiceCategories(PDO $pdo): array
{
    $json = cmsGetSetting($pdo, 'service_category_list');
    $saved = $json ? json_decode($json, true) : null;
    if (!is_array($saved) || $saved === []) {
        $saved = cmsGetDefaultServiceCategories();
    }

    $fromDb = [];
    if (cmsTableExists($pdo, 'services')) {
        $stmt = $pdo->query("SELECT DISTINCT category FROM services WHERE category IS NOT NULL AND category != '' ORDER BY category");
        $fromDb = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    $merged = [];
    foreach (array_merge($saved, $fromDb) as $c) {
        $n = cmsNormalizeServiceCategory((string) $c);
        if ($n !== '' && !in_array($n, $merged, true)) {
            $merged[] = $n;
        }
    }
    sort($merged);
    return $merged;
}

function cmsSaveServiceCategoryList(PDO $pdo, array $categories): void
{
    $clean = [];
    foreach ($categories as $c) {
        $n = cmsNormalizeServiceCategory((string) $c);
        if ($n !== '' && !in_array($n, $clean, true)) {
            $clean[] = $n;
        }
    }
    sort($clean);
    cmsSetSetting($pdo, 'service_category_list', json_encode($clean, JSON_UNESCAPED_UNICODE), 'services');
}

function cmsEnsureDefaultServiceCategories(PDO $pdo): void
{
    if (cmsGetSetting($pdo, 'service_category_list') !== null) {
        return;
    }
    cmsSaveServiceCategoryList($pdo, cmsGetDefaultServiceCategories());
}

function cmsGetSetting(PDO $pdo, string $key, ?string $default = null): ?string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($row && $row['setting_value'] !== null && $row['setting_value'] !== '') ? $row['setting_value'] : $default;
}

function cmsSetSetting(PDO $pdo, string $key, string $value, string $category = 'general'): void
{
    $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value, category)
        VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), category = VALUES(category)');
    $stmt->execute([$key, $value, $category]);
}

function cmsGetSettingsByCategory(PDO $pdo, string $category): array
{
    $stmt = $pdo->prepare('SELECT setting_key, setting_value FROM site_settings WHERE category = ?');
    $stmt->execute([$category]);
    $out = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $out[$row['setting_key']] = $row['setting_value'];
    }
    return $out;
}

function cmsGetPage(PDO $pdo, string $pageKey): array
{
    $stmt = $pdo->prepare('SELECT * FROM cms_pages WHERE page_key = ? LIMIT 1');
    $stmt->execute([$pageKey]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return [];
    }
    $content = json_decode($row['content_json'] ?? '{}', true);
    return is_array($content) ? array_merge($row, ['sections' => $content]) : $row;
}

function cmsSavePage(PDO $pdo, string $pageKey, array $sections, ?string $titleAr = null, ?string $titleEn = null): void
{
    $json = json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $stmt = $pdo->prepare('INSERT INTO cms_pages (page_key, title_ar, title_en, content_json)
        VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE
        title_ar = VALUES(title_ar), title_en = VALUES(title_en), content_json = VALUES(content_json)');
    $stmt->execute([$pageKey, $titleAr, $titleEn, $json]);
}

function cmsLang(array $row, string $field, string $lang): string
{
    $key = $field . '_' . $lang;
    if (!empty($row[$key])) {
        return $row[$key];
    }
    $fallback = $field . '_' . ($lang === 'ar' ? 'en' : 'ar');
    return $row[$fallback] ?? '';
}

function cmsServiceImageUrl(?string $path): string
{
    if ($path === null || $path === '') {
        return url('assets/images/service.png');
    }
    if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
        return $path;
    }
    if (str_starts_with($path, 'assets/') || str_starts_with($path, 'uploads/')) {
        return url($path);
    }
    if (function_exists('getImageUrl')) {
        return getImageUrl($path);
    }
    return url('assets/images/' . ltrim($path, '/'));
}

/** Original full detail pages — keep layout/content exactly as shipped */
function cmsServiceLegacyUrl(string $slug): string
{
    $map = [
        'household-management' => 'service-household-management.php',
        'property-management' => 'service-property-management.php',
        'event-management' => 'service-event-management.php',
        'protocol-etiquette' => 'service-protocol-etiquette.php',
        'vip-concierge' => 'service-vip-concierge.php',
        'staff-recruitment' => 'service-staff-recruitment.php',
    ];

    return url($map[$slug] ?? ('service.php?slug=' . urlencode($slug)));
}

function cmsParseLines(?string $text): array
{
    if ($text === null || trim($text) === '') {
        return [];
    }
    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));
}

function cmsGetActiveServices(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM services WHERE status = 'active' ORDER BY display_order ASC, id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function cmsGetServiceBySlug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM services WHERE slug = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return null;
    }
    if (!empty($row['page_data'])) {
        $decoded = json_decode($row['page_data'], true);
        $row['detail'] = is_array($decoded) ? $decoded : [];
    } else {
        $row['detail'] = [];
    }
    $row['listing_features_ar'] = cmsParseLines($row['listing_features_ar'] ?? '');
    $row['listing_features_en'] = cmsParseLines($row['listing_features_en'] ?? '');
    return $row;
}

function cmsSaveService(PDO $pdo, array $data, ?int $id = null): int
{
    $fields = [
        'slug', 'category', 'title_ar', 'title_en', 'description_ar', 'description_en',
        'icon', 'image', 'featured', 'display_order', 'status',
        'listing_features_ar', 'listing_features_en', 'page_data',
    ];
    $payload = [];
    foreach ($fields as $f) {
        if (array_key_exists($f, $data)) {
            $payload[$f] = $data[$f];
        }
    }
    if (!isset($payload['content_ar'])) {
        $payload['content_ar'] = '';
    }
    if (!isset($payload['content_en'])) {
        $payload['content_en'] = '';
    }

    if ($id) {
        $sets = [];
        $vals = [];
        foreach ($payload as $k => $v) {
            $sets[] = "$k = ?";
            $vals[] = $v;
        }
        $vals[] = $id;
        $pdo->prepare('UPDATE services SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($vals);
        return $id;
    }
    $cols = array_keys($payload);
    $placeholders = array_fill(0, count($cols), '?');
    $pdo->prepare('INSERT INTO services (' . implode(',', $cols) . ') VALUES (' . implode(',', $placeholders) . ')')
        ->execute(array_values($payload));
    return (int) $pdo->lastInsertId();
}

function cmsDeleteService(PDO $pdo, int $id): void
{
    $pdo->prepare('DELETE FROM services WHERE id = ?')->execute([$id]);
}

function cmsSyncConfigFromSettings(PDO $pdo): void
{
    $map = [
        'site_email' => 'CONTACT_EMAIL',
        'site_phone' => 'CONTACT_PHONE',
        'site_address_ar' => 'CONTACT_ADDRESS_AR',
        'site_address_en' => 'CONTACT_ADDRESS_EN',
        'facebook_url' => 'SOCIAL_FACEBOOK',
        'twitter_url' => 'SOCIAL_TWITTER',
        'instagram_url' => 'SOCIAL_INSTAGRAM',
        'linkedin_url' => 'SOCIAL_LINKEDIN',
        'iso_certificate' => 'ISO_CERTIFICATE_NUMBER',
    ];
    foreach ($map as $dbKey => $const) {
        $val = cmsGetSetting($pdo, $dbKey);
        if ($val !== null && $val !== '' && defined($const)) {
            // runtime only — constants can't be redefined; front-end uses cmsGetSetting fallbacks
        }
    }
}

function cmsContactEmail(PDO $pdo): string
{
    return cmsGetSetting($pdo, 'site_email', defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@niche-society.com');
}

function cmsContactPhone(PDO $pdo): string
{
    return cmsGetSetting($pdo, 'site_phone', defined('CONTACT_PHONE') ? CONTACT_PHONE : '');
}

function cmsPageSection(array $page, string $section, string $lang, string $field, string $default = ''): string
{
    if (empty($page['sections'][$section]) || !is_array($page['sections'][$section])) {
        return $default;
    }
    $s = $page['sections'][$section];
    return $s[$field . '_' . $lang] ?? $s[$field . '_ar'] ?? $s[$field . '_en'] ?? $default;
}
