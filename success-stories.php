<?php
/**
 * Success Stories Page - Niche Society
 * 
 * Displays client success stories with filtering and pagination
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// Handle language switch
handleLanguageSwitch();

$lang = getCurrentLanguage();
$t = getTranslations($lang);
$dir = getTextDirection($lang);

// Pagination settings
$storiesPerPage = 9;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $storiesPerPage;

// Service category filter
$serviceFilter = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : '';

// Client type filter
$clientTypeFilter = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';

// Build SQL query
$whereClauses = ["status = 'active'"];
$params = [];

if ($serviceFilter) {
    $whereClauses[] = "service_category = ?";
    $params[] = $serviceFilter;
}

if ($clientTypeFilter) {
    $whereClauses[] = "client_type = ?";
    $params[] = $clientTypeFilter;
}

$whereSQL = implode(" AND ", $whereClauses);

// Get total stories count
$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM success_stories WHERE {$whereSQL}");
$countStmt->execute($params);
$totalStories = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalStories / $storiesPerPage);

// Get stories
$titleCol = $lang === 'ar' ? 'title_ar' : 'title_en';
$descriptionCol = $lang === 'ar' ? 'description_ar' : 'description_en';
$clientNameCol = $lang === 'ar' ? 'client_name_ar' : 'client_name_en';

$storiesStmt = $pdo->prepare("
    SELECT id, slug, {$titleCol} as title, {$descriptionCol} as description, 
           {$clientNameCol} as client_name, client_type, service_category, 
           image, project_date, featured
    FROM success_stories 
    WHERE {$whereSQL}
    ORDER BY featured DESC, display_order ASC, project_date DESC
    LIMIT {$storiesPerPage} OFFSET {$offset}
");
$storiesStmt->execute($params);
$stories = $storiesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get service categories from success_stories
$servicesStmt = $pdo->prepare("SELECT DISTINCT service_category FROM success_stories WHERE status = 'active' AND service_category != '' AND service_category IS NOT NULL ORDER BY service_category");
$servicesStmt->execute();
$serviceCategories = $servicesStmt->fetchAll(PDO::FETCH_COLUMN);

// Get client types
$clientTypes = ['royal', 'government', 'corporate', 'individual'];

$pageTitle = $lang === 'ar' ? 'قصص النجاح - نيش سوسيتي' : 'Success Stories - Niche Society';
$pageDescription = $lang === 'ar' 
    ? 'اكتشف قصص نجاح عملائنا المميزين في إدارة المنازل والفعاليات الفاخرة' 
    : 'Discover success stories from our distinguished clients in luxury household and event management';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
    <?= getMetaTags($pageTitle, $pageDescription, getCurrentUrl()) ?>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <?php if ($lang === 'ar'): ?>
    <link rel="stylesheet" href="<?= url('assets/css/rtl.css') ?>">
    <?php endif; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="page-hero" style="background-image: url('<?= url('assets/images/sunlit-library-escape-701x1024.jpg') ?>');">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title" data-aos="fade-up">
                    <?= $lang === 'ar' ? 'قصص النجاح' : 'Success Stories' ?>
                </h1>
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: #000F2B !important; opacity: 1 !important; visibility: visible !important; text-shadow: 0 2px 4px rgba(255, 255, 255, 0.8);">
                    <?= $lang === 'ar' 
                        ? 'شهادات من عملائنا المميزين'
                        : 'Testimonials from our distinguished clients'
                    ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 mb-5 mb-lg-0" data-aos="fade-right">
                    <!-- Service Filter -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title"><?= $lang === 'ar' ? 'الخدمات' : 'Services' ?></h3>
                        <ul class="category-list">
                            <li>
                                <a href="<?= url('success-stories.php') ?>" class="<?= !$serviceFilter ? 'active' : '' ?>">
                                    <?= $lang === 'ar' ? 'جميع القصص' : 'All Stories' ?>
                                    <span class="count"><?= $totalStories ?></span>
                                </a>
                            </li>
                            <?php foreach ($serviceCategories as $service): ?>
                            <li>
                                <a href="<?= url('success-stories.php?service=' . urlencode($service)) ?>" 
                                   class="<?= $serviceFilter == $service ? 'active' : '' ?>">
                                    <?= htmlspecialchars($service) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Client Type Filter -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title"><?= $lang === 'ar' ? 'نوع العميل' : 'Client Type' ?></h3>
                        <ul class="category-list">
                            <?php foreach ($clientTypes as $type): ?>
                            <li>
                                <a href="<?= url('success-stories.php?type=' . urlencode($type) . ($serviceFilter ? '&service=' . urlencode($serviceFilter) : '')) ?>" 
                                   class="<?= $clientTypeFilter == $type ? 'active' : '' ?>">
                                    <?= $lang === 'ar' 
                                        ? ($type === 'royal' ? 'عائلات ملكية' : ($type === 'government' ? 'حكومي' : ($type === 'corporate' ? 'شركات' : 'أفراد')))
                                        : ucfirst($type)
                                    ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Link to News/Blog -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title"><?= $lang === 'ar' ? 'المزيد' : 'More' ?></h3>
                        <ul class="category-list">
                            <li>
                                <a href="<?= url('blog.php') ?>">
                                    <i class="bi bi-newspaper"></i>
                                    <?= $lang === 'ar' ? 'الأخبار والمقالات' : 'News & Articles' ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Stories Grid -->
                <div class="col-lg-9" data-aos="fade-left">
                    <!-- Active Filters -->
                    <?php if ($serviceFilter || $clientTypeFilter): ?>
                    <div class="active-filters mb-4">
                        <?php if ($serviceFilter): ?>
                        <span class="filter-tag">
                            <?= $lang === 'ar' ? 'الخدمة:' : 'Service:' ?> <?= htmlspecialchars($serviceFilter) ?>
                            <a href="<?= url('success-stories.php' . ($clientTypeFilter ? '?type=' . urlencode($clientTypeFilter) : '')) ?>">×</a>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($clientTypeFilter): ?>
                        <span class="filter-tag">
                            <?= $lang === 'ar' ? 'النوع:' : 'Type:' ?> <?= htmlspecialchars($clientTypeFilter) ?>
                            <a href="<?= url('success-stories.php' . ($serviceFilter ? '?service=' . urlencode($serviceFilter) : '')) ?>">×</a>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Results Count -->
                    <div class="results-info mb-4">
                        <p class="text-muted">
                            <?= $lang === 'ar' 
                                ? "عرض {$offset}-" . min($offset + $storiesPerPage, $totalStories) . " من {$totalStories} قصة نجاح"
                                : "Showing {$offset}-" . min($offset + $storiesPerPage, $totalStories) . " of {$totalStories} success stories"
                            ?>
                        </p>
                    </div>

                    <?php if (empty($stories)): ?>
                    <!-- No Results -->
                    <div class="no-results text-center py-5">
                        <i class="bi bi-trophy" style="font-size: 4rem; color: #ccc;"></i>
                        <h3 class="mt-3"><?= $lang === 'ar' ? 'لا توجد قصص نجاح' : 'No Success Stories Found' ?></h3>
                        <p class="text-muted">
                            <?= $lang === 'ar' 
                                ? 'لم نتمكن من العثور على أي قصص نجاح تطابق معايير البحث'
                                : 'We couldn\'t find any success stories matching your criteria'
                            ?>
                        </p>
                        <a href="<?= url('success-stories.php') ?>" class="btn btn-primary mt-3">
                            <?= $lang === 'ar' ? 'عرض جميع القصص' : 'View All Stories' ?>
                        </a>
                    </div>
                    <?php else: ?>
                    <!-- Stories Grid -->
                    <div class="row">
                        <?php foreach ($stories as $story): ?>
                        <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up">
                            <article class="blog-card success-story-card">
                                <div class="blog-card-image">
                                    <?php if ($story['image']): ?>
                                    <img src="<?= url($story['image']) ?>" alt="<?= htmlspecialchars($story['title']) ?>">
                                    <?php else: ?>
                                    <img src="<?= url('assets/images/niche-society-homepage-1-scaled.jpg') ?>" alt="<?= htmlspecialchars($story['title']) ?>">
                                    <?php endif; ?>
                                    <?php if ($story['featured']): ?>
                                    <span class="featured-badge">
                                        <i class="bi bi-star-fill"></i>
                                        <?= $lang === 'ar' ? 'مميز' : 'Featured' ?>
                                    </span>
                                    <?php endif; ?>
                                    <div class="blog-card-overlay">
                                        <a href="<?= url('success-story.php?slug=' . $story['slug']) ?>" class="read-more-btn">
                                            <?= $lang === 'ar' ? 'اقرأ القصة' : 'Read Story' ?>
                                            <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="blog-card-content">
                                    <div class="blog-card-meta">
                                        <?php if ($story['client_name']): ?>
                                        <span class="client-name">
                                            <i class="bi bi-person"></i>
                                            <?= htmlspecialchars($story['client_name']) ?>
                                        </span>
                                        <?php endif; ?>
                                        <?php if ($story['project_date']): ?>
                                        <span class="date">
                                            <i class="bi bi-calendar"></i>
                                            <?= date('M Y', strtotime($story['project_date'])) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="blog-card-title">
                                        <a href="<?= url('success-story.php?slug=' . $story['slug']) ?>">
                                            <?= htmlspecialchars($story['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="blog-card-excerpt">
                                        <?= htmlspecialchars($story['description']) ?>
                                    </p>
                                    <?php if ($story['service_category']): ?>
                                    <span class="story-category">
                                        <i class="bi bi-tag"></i>
                                        <?= htmlspecialchars($story['service_category']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('success-stories.php?page=' . ($currentPage - 1) . ($serviceFilter ? '&service=' . urlencode($serviceFilter) : '') . ($clientTypeFilter ? '&type=' . urlencode($clientTypeFilter) : '')) ?>">
                                    <i class="bi bi-chevron-<?= $dir === 'rtl' ? 'right' : 'left' ?>"></i>
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            if ($startPage > 1): ?>
                                <li class="page-item"><a class="page-link" href="<?= url('success-stories.php?page=1' . ($serviceFilter ? '&service=' . urlencode($serviceFilter) : '') . ($clientTypeFilter ? '&type=' . urlencode($clientTypeFilter) : '')) ?>">1</a></li>
                                <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('success-stories.php?page=' . $i . ($serviceFilter ? '&service=' . urlencode($serviceFilter) : '') . ($clientTypeFilter ? '&type=' . urlencode($clientTypeFilter) : '')) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item"><a class="page-link" href="<?= url('success-stories.php?page=' . $totalPages . ($serviceFilter ? '&service=' . urlencode($serviceFilter) : '') . ($clientTypeFilter ? '&type=' . urlencode($clientTypeFilter) : '')) ?>"><?= $totalPages ?></a></li>
                            <?php endif; ?>

                            <!-- Next -->
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('success-stories.php?page=' . ($currentPage + 1) . ($serviceFilter ? '&service=' . urlencode($serviceFilter) : '') . ($clientTypeFilter ? '&type=' . urlencode($clientTypeFilter) : '')) ?>">
                                    <i class="bi bi-chevron-<?= $dir === 'rtl' ? 'left' : 'right' ?>"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section bg-cream">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0 text-center text-lg-start" data-aos="fade-right">
                    <h2><?= $lang === 'ar' ? 'شاركنا قصتك' : 'Share Your Story' ?></h2>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'هل لديك قصة نجاح تود مشاركتها؟ تواصل معنا'
                            : 'Do you have a success story to share? Contact us'
                        ?>
                    </p>
                </div>
                <div class="col-lg-4 text-center text-lg-end" data-aos="fade-left">
                    <a href="<?= url('contact.php') ?>" class="btn btn-primary btn-lg">
                        <?= $lang === 'ar' ? 'تواصل معنا' : 'Contact Us' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
    <script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
