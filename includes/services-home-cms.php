<?php
/**
 * Homepage services grid — from CMS database (first 3 active services)
 * Expects: $lang, $pdo
 */
require_once __DIR__ . '/../functions/cms.php';

cmsEnsureTables($pdo);
$cmsServices = cmsGetActiveServices($pdo);
$homeServices = array_slice($cmsServices, 0, 3);
$delay = 100;

foreach ($homeServices as $svc):
    $title = cmsLang($svc, 'title', $lang);
    $desc = cmsLang($svc, 'description', $lang);
    $features = cmsParseLines($svc['listing_features_' . $lang] ?? '');
    if (empty($features)) {
        $features = cmsParseLines($svc['listing_features_' . ($lang === 'ar' ? 'en' : 'ar')] ?? '');
    }
    $features = array_slice($features, 0, 3);
    $img = !empty($svc['image']) ? cmsServiceImageUrl($svc['image']) : cmsServiceImageUrl('');
    $num = (int) ($svc['display_order'] ?: ($delay / 100));
    $arrowPath = $lang === 'ar' ? 'M12 4L6 10L12 16' : 'M8 4L14 10L8 16';
?>
            <div class="service-card-elegant" data-aos="fade-up" data-aos-delay="<?= (int) $delay ?>">
                <div class="service-image-elegant">
                    <div class="service-badge-number"><?= formatNumber((string) $num) ?></div>
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>">
                    <div class="service-image-overlay"></div>
                </div>
                <div class="service-content-elegant">
                    <h3 class="service-title-elegant"><?= htmlspecialchars($title) ?></h3>
                    <p class="service-description-elegant"><?= htmlspecialchars($desc) ?></p>
                    <?php if ($features): ?>
                    <ul class="service-features-elegant">
                        <?php foreach ($features as $feat): ?>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?= htmlspecialchars($feat) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <a href="<?= url('service.php?slug=' . urlencode($svc['slug'])) ?>" class="service-link-elegant">
                        <span><?= t('learn_more', 'اعرف المزيد') ?></span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="<?= $arrowPath ?>" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                        </svg>
                    </a>
                </div>
            </div>
<?php
    $delay += 100;
endforeach;
