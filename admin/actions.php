<?php
/**
 * Admin POST actions
 */
require_once __DIR__ . '/bootstrap.php';

if (!adminIsAuthenticated() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    adminRedirect();
}

if (!adminVerifyCsrf($_POST['admin_csrf'] ?? null)) {
    adminFlash('danger', 'Security token expired. Please try again.');
    adminRedirect('section=' . rawurlencode($_POST['section'] ?? 'dashboard'));
}

$section = $_POST['section'] ?? 'dashboard';

try {
    switch ($section) {
        case 'maintenance':
            $maintenance_settings['enabled'] = !empty($_POST['maintenance_enabled']);
            $maintenance_settings['message'] = trim($_POST['maintenance_message'] ?? '');
            $maintenance_settings['admin_bypass'] = true;
            if (!adminWriteSettingsFile($maintenance_settings, $site_settings, $admin_credentials)) {
                throw new RuntimeException('Could not save maintenance settings.');
            }
            adminFlash('success', $maintenance_settings['enabled'] ? 'Maintenance mode enabled.' : 'Maintenance mode disabled.');
            break;

        case 'contact':
            $keys = [
                'site_email' => trim($_POST['site_email'] ?? ''),
                'site_phone' => trim($_POST['site_phone'] ?? ''),
                'site_address_ar' => trim($_POST['site_address_ar'] ?? ''),
                'site_address_en' => trim($_POST['site_address_en'] ?? ''),
                'site_name_ar' => trim($_POST['site_name_ar'] ?? ''),
                'site_name_en' => trim($_POST['site_name_en'] ?? ''),
            ];
            foreach ($keys as $k => $v) {
                cmsSetSetting($pdo, $k, $v, 'contact');
            }
            $site_settings['admin_email'] = $keys['site_email'];
            $site_settings['company_phone'] = $keys['site_phone'];
            $site_settings['company_name'] = $keys['site_name_en'];
            adminWriteSettingsFile($maintenance_settings, $site_settings, $admin_credentials);
            adminFlash('success', 'Contact information saved.');
            break;

        case 'social':
            foreach (['facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url', 'iso_certificate'] as $k) {
                cmsSetSetting($pdo, $k, trim($_POST[$k] ?? ''), $k === 'iso_certificate' ? 'company' : 'social');
            }
            adminFlash('success', 'Social & company settings saved.');
            break;

        case 'about':
            $sections = [
                'hero' => [
                    'title_ar' => trim($_POST['hero_title_ar'] ?? ''),
                    'title_en' => trim($_POST['hero_title_en'] ?? ''),
                ],
                'overview' => [
                    'lead_ar' => trim($_POST['overview_lead_ar'] ?? ''),
                    'lead_en' => trim($_POST['overview_lead_en'] ?? ''),
                    'text_ar' => trim($_POST['overview_text_ar'] ?? ''),
                    'text_en' => trim($_POST['overview_text_en'] ?? ''),
                ],
                'mission' => [
                    'title_ar' => 'الرسالة',
                    'title_en' => 'Our Mission',
                    'text_ar' => trim($_POST['mission_text_ar'] ?? ''),
                    'text_en' => trim($_POST['mission_text_en'] ?? ''),
                ],
                'vision' => [
                    'title_ar' => 'الرؤية',
                    'title_en' => 'Our Vision',
                    'text_ar' => trim($_POST['vision_text_ar'] ?? ''),
                    'text_en' => trim($_POST['vision_text_en'] ?? ''),
                ],
                'values' => [
                    'title_ar' => 'قيمنا',
                    'title_en' => 'Our Values',
                    'text_ar' => trim($_POST['values_text_ar'] ?? ''),
                    'text_en' => trim($_POST['values_text_en'] ?? ''),
                ],
                'story' => [
                    'title_ar' => trim($_POST['story_title_ar'] ?? ''),
                    'title_en' => trim($_POST['story_title_en'] ?? ''),
                    'lead_ar' => trim($_POST['story_lead_ar'] ?? ''),
                    'lead_en' => trim($_POST['story_lead_en'] ?? ''),
                    'text_ar' => trim($_POST['story_text_ar'] ?? ''),
                    'text_en' => trim($_POST['story_text_en'] ?? ''),
                    'text2_ar' => trim($_POST['story_text2_ar'] ?? ''),
                    'text2_en' => trim($_POST['story_text2_en'] ?? ''),
                ],
            ];
            cmsSavePage($pdo, 'about', $sections, 'من نحن', 'About Us');
            adminFlash('success', 'About page content saved.');
            break;

        case 'services_page':
            $sections = [
                'hero' => [
                    'title_ar' => trim($_POST['hero_title_ar'] ?? ''),
                    'title_en' => trim($_POST['hero_title_en'] ?? ''),
                    'subtitle_ar' => trim($_POST['hero_subtitle_ar'] ?? ''),
                    'subtitle_en' => trim($_POST['hero_subtitle_en'] ?? ''),
                ],
                'intro' => [
                    'badge_ar' => trim($_POST['intro_badge_ar'] ?? ''),
                    'badge_en' => trim($_POST['intro_badge_en'] ?? ''),
                    'title_ar' => trim($_POST['intro_title_ar'] ?? ''),
                    'title_en' => trim($_POST['intro_title_en'] ?? ''),
                    'lead_ar' => trim($_POST['intro_lead_ar'] ?? ''),
                    'lead_en' => trim($_POST['intro_lead_en'] ?? ''),
                ],
            ];
            cmsSavePage($pdo, 'services', $sections, 'خدماتنا', 'Our Services');
            adminFlash('success', 'Services page intro saved.');
            break;

        case 'categories_save':
            $raw = trim($_POST['categories'] ?? '');
            $lines = $raw === '' ? [] : preg_split('/\r\n|\r|\n/', $raw);
            cmsSaveServiceCategoryList($pdo, $lines);
            adminFlash('success', 'Service categories saved.');
            $section = 'categories';
            break;

        case 'service_save':
            $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
            $slug = preg_replace('/[^a-z0-9-]/', '', strtolower(trim($_POST['slug'] ?? '')));
            if ($slug === '') {
                throw new InvalidArgumentException('Slug is required (lowercase letters, numbers, hyphens).');
            }
            $detail = [
                'overview_title_ar' => trim($_POST['overview_title_ar'] ?? ''),
                'overview_title_en' => trim($_POST['overview_title_en'] ?? ''),
                'overview_p1_ar' => trim($_POST['overview_p1_ar'] ?? ''),
                'overview_p1_en' => trim($_POST['overview_p1_en'] ?? ''),
                'overview_p2_ar' => trim($_POST['overview_p2_ar'] ?? ''),
                'overview_p2_en' => trim($_POST['overview_p2_en'] ?? ''),
                'subtitle_ar' => trim($_POST['subtitle_ar'] ?? ''),
                'subtitle_en' => trim($_POST['subtitle_en'] ?? ''),
            ];
            $data = [
                'slug' => $slug,
                'category' => cmsNormalizeServiceCategory($_POST['category'] ?? 'general'),
                'title_ar' => trim($_POST['title_ar'] ?? ''),
                'title_en' => trim($_POST['title_en'] ?? ''),
                'description_ar' => trim($_POST['description_ar'] ?? ''),
                'description_en' => trim($_POST['description_en'] ?? ''),
                'icon' => trim($_POST['icon'] ?? 'bi-star'),
                'image' => trim($_POST['image'] ?? ''),
                'featured' => !empty($_POST['featured']) ? 1 : 0,
                'display_order' => (int) ($_POST['display_order'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
                'listing_features_ar' => trim($_POST['listing_features_ar'] ?? ''),
                'listing_features_en' => trim($_POST['listing_features_en'] ?? ''),
                'page_data' => json_encode($detail, JSON_UNESCAPED_UNICODE),
            ];
            cmsSaveService($pdo, $data, $id);
            adminFlash('success', $id ? 'Service updated.' : 'Service created.');
            break;

        case 'service_delete':
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                cmsDeleteService($pdo, $id);
                adminFlash('success', 'Service deleted.');
            }
            $section = 'services';
            break;

        case 'password':
            $result = adminChangePassword(
                $_POST['current_password'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? '',
                $maintenance_settings,
                $site_settings,
                $admin_credentials
            );
            if (!$result['ok']) {
                throw new RuntimeException($result['error']);
            }
            adminFlash('success', $result['message']);
            $section = 'account';
            break;

        case 'invoice_delete':
            require_once __DIR__ . '/../functions/invoices.php';
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                cmsDeleteInvoice($pdo, $id);
                adminFlash('success', 'Proposal deleted.');
            }
            $section = 'invoices';
            break;

        default:
            adminFlash('danger', 'Unknown action.');
    }
} catch (Throwable $e) {
    adminFlash('danger', $e->getMessage());
}

$redirectSection = in_array($section, ['service_save', 'service_delete'], true) ? 'services' : $section;
if ($section === 'password') {
    $redirectSection = 'account';
}
if ($section === 'invoice_delete') {
    $redirectSection = 'invoices';
}
adminRedirect('section=' . urlencode($redirectSection));
