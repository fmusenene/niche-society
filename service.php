<?php
/**
 * Service detail router — known services use original full pages; new CMS-only slugs use the simple template.
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/cms.php';

handleLanguageSwitch();

$lang = getCurrentLang();
$dir = getTextDirection($lang);
$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'])) : '';

if ($slug === '') {
    header('Location: ' . url('services.php'));
    exit;
}

$legacyMap = [
    'household-management' => 'service-household-management.php',
    'property-management' => 'service-property-management.php',
    'event-management' => 'service-event-management.php',
    'protocol-etiquette' => 'service-protocol-etiquette.php',
    'vip-concierge' => 'service-vip-concierge.php',
    'staff-recruitment' => 'service-staff-recruitment.php',
];
if (isset($legacyMap[$slug])) {
    header('Location: ' . url($legacyMap[$slug]), true, 301);
    exit;
}

cmsEnsureTables($pdo);
$service = cmsGetServiceBySlug($pdo, $slug);
if (!$service) {
    header('Location: ' . url('services.php'));
    exit;
}

$d = $service['detail'];
$features = $lang === 'ar' ? $service['listing_features_ar'] : $service['listing_features_en'];
if (empty($features)) {
    $features = $lang === 'ar' ? $service['listing_features_en'] : $service['listing_features_ar'];
}

$pageTitle = cmsLang($service, 'title', $lang) . ' - ' . SITE_NAME;
$pageDescription = cmsLang($service, 'description', $lang);

require_once __DIR__ . '/includes/header.php';
?>

<a href="<?= url('services.php') ?>#service-<?= (int)$service['display_order'] ?>" class="back-button back-button-sticky">
    <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
    <span><?= $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services' ?></span>
</a>

<section class="service-detail-header">
    <div class="container">
        <div class="service-detail-nav">
            <a href="<?= url('services.php') ?>" class="back-button">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <span><?= $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services' ?></span>
            </a>
        </div>
        <div class="service-detail-title-section">
            <div class="service-badge-header"><?= formatNumber((string)(int)$service['display_order']) ?></div>
            <h1 class="service-detail-title"><?= htmlspecialchars(cmsLang($service, 'title', $lang)) ?></h1>
            <p class="service-detail-subtitle"><?= htmlspecialchars($d['subtitle_' . $lang] ?? cmsLang($service, 'description', $lang)) ?></p>
            <div class="service-meta-badges">
                <span class="meta-badge"><i class="bi bi-award-fill"></i> <?= $lang === 'ar' ? 'معتمد ISO 9001' : 'ISO 9001 Certified' ?></span>
                <span class="meta-badge"><i class="bi bi-clock-fill"></i> <?= $lang === 'ar' ? 'متاح 24/7' : '24/7 Available' ?></span>
                <span class="meta-badge"><i class="bi bi-shield-check-fill"></i> <?= $lang === 'ar' ? 'سرية تامة' : 'Complete Discretion' ?></span>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <?php if (!empty($service['image'])): ?>
                <img src="<?= cmsServiceImageUrl($service['image']) ?>" alt="" class="img-fluid rounded">
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <h2 class="section-title"><?= htmlspecialchars($d['overview_title_' . $lang] ?? ($lang === 'ar' ? 'نظرة عامة على الخدمة' : 'Service Overview')) ?></h2>
                <?php if (!empty($d['overview_p1_' . $lang])): ?>
                <p class="lead"><?= nl2br(htmlspecialchars($d['overview_p1_' . $lang])) ?></p>
                <?php endif; ?>
                <?php if (!empty($d['overview_p2_' . $lang])): ?>
                <p><?= nl2br(htmlspecialchars($d['overview_p2_' . $lang])) ?></p>
                <?php endif; ?>
                <?php if (!empty($features)): ?>
                <ul class="service-features-list mt-4">
                    <?php foreach ($features as $feat): ?>
                    <li><i class="bi bi-check-circle-fill"></i><span><?= htmlspecialchars($feat) ?></span></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container text-center">
        <a href="<?= url('contact.php') ?>" class="btn btn-primary btn-lg"><?= $lang === 'ar' ? 'احجز استشارتك' : 'Book Your Consultation' ?></a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
