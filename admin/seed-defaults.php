<?php
/**
 * Import hardcoded website content into the database
 */
require_once __DIR__ . '/../functions/cms-seed-data.php';

function cmsSeedDefaults(PDO $pdo, bool $forceUpdate = false): int
{
    $imported = 0;

    $about = cmsGetHardcodedAboutPage();
    cmsSavePage($pdo, 'about', $about, 'من نحن', 'About Us');

    $servicesPage = cmsGetHardcodedServicesPage();
    cmsSavePage($pdo, 'services', $servicesPage, 'خدماتنا', 'Our Services');

    foreach (cmsGetHardcodedServices() as $s) {
        $stmt = $pdo->prepare('SELECT id FROM services WHERE slug = ? LIMIT 1');
        $stmt->execute([$s['slug']]);
        $existingId = $stmt->fetchColumn();

        if ($existingId && !$forceUpdate) {
            continue;
        }

        $data = [
            'slug' => $s['slug'],
            'category' => $s['category'],
            'title_ar' => $s['title_ar'],
            'title_en' => $s['title_en'],
            'description_ar' => $s['desc_ar'],
            'description_en' => $s['desc_en'],
            'icon' => $s['icon'],
            'image' => $s['image'],
            'featured' => 1,
            'display_order' => $s['order'],
            'status' => 'active',
            'listing_features_ar' => $s['features_ar'],
            'listing_features_en' => $s['features_en'],
            'page_data' => json_encode($s['detail'], JSON_UNESCAPED_UNICODE),
            'content_ar' => $s['detail']['overview_p1_ar'] ?? '',
            'content_en' => $s['detail']['overview_p1_en'] ?? '',
        ];

        cmsSaveService($pdo, $data, $existingId ? (int) $existingId : null);
        $imported++;
    }

    cmsSetSetting($pdo, 'site_email', CONTACT_EMAIL, 'contact');
    cmsSetSetting($pdo, 'site_phone', CONTACT_PHONE, 'contact');
    cmsSetSetting($pdo, 'site_address_ar', CONTACT_ADDRESS_AR, 'contact');
    cmsSetSetting($pdo, 'site_address_en', CONTACT_ADDRESS_EN, 'contact');
    cmsSetSetting($pdo, 'site_name_en', SITE_NAME, 'contact');
    cmsSetSetting($pdo, 'site_name_ar', 'نيش سوسيتي', 'contact');

    return $imported;
}
