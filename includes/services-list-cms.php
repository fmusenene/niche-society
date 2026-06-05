<?php
/**
 * Renders services listing blocks from database (same layout as services.php)
 * Expects: $lang, $dir, $pdo
 */
$cmsServices = cmsGetActiveServices($pdo);
$index = 0;
foreach ($cmsServices as $svc):
    $index++;
    $bg = ($index % 2 === 1) ? 'bg-white' : 'bg-cream';
    $orderImg = ($index % 2 === 1) ? 'order-lg-2' : '';
    $orderText = ($index % 2 === 1) ? 'order-lg-1' : '';
    $title = cmsLang($svc, 'title', $lang);
    $desc = cmsLang($svc, 'description', $lang);
    $features = cmsParseLines($svc['listing_features_' . $lang] ?? '');
    if (empty($features)) {
        $features = cmsParseLines($svc['listing_features_' . ($lang === 'ar' ? 'en' : 'ar')] ?? '');
    }
    $img = !empty($svc['image']) ? url($svc['image']) : url('assets/images/service.png');
    $icon = !empty($svc['icon']) ? $svc['icon'] : 'bi-star';
    $anchor = (int) ($svc['display_order'] ?: $index);
?>
    <section id="service-<?= $anchor ?>" class="service-detail-section <?= $bg ?>">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 <?= $orderImg ?>" data-aos="fade-left">
                    <div class="service-image">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" class="img-fluid">
                        <div class="service-badge"><?= formatNumber((string)$anchor) ?></div>
                    </div>
                </div>
                <div class="col-lg-6 <?= $orderText ?>" data-aos="fade-right">
                    <div class="service-header-mobile">
                        <div class="service-icon"><i class="bi <?= htmlspecialchars($icon) ?>"></i></div>
                        <h2 class="service-title"><?= htmlspecialchars($title) ?></h2>
                    </div>
                    <div class="divider"></div>
                    <p class="lead-text"><?= htmlspecialchars($desc) ?></p>
                    <?php if ($features): ?>
                    <ul class="service-features-list">
                        <?php foreach ($features as $feat): ?>
                        <li><i class="bi bi-check-circle-fill"></i><span><?= htmlspecialchars($feat) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <a href="<?= url('service.php?slug=' . urlencode($svc['slug'])) ?>" class="btn btn-primary mt-3">
                        <?= $lang === 'ar' ? 'تفاصيل الخدمة' : 'Service Details' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
<?php endforeach; ?>
