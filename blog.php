<?php
/**
 * Blog/Success Stories Page - Niche Society
 * 
 * Displays blog posts and success stories with pagination,
 * categories, and search functionality
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

$lang = getCurrentLanguage();
$t = getTranslations($lang);
$dir = getTextDirection($lang);

// Pagination settings
$postsPerPage = 9;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $postsPerPage;

// Category filter
$categoryFilter = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';

// Search query
$searchQuery = isset($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : '';

// Build SQL query
$whereClauses = ["status = 'published'", "published_at <= NOW()"];
$params = [];

if ($categoryFilter) {
    $whereClauses[] = "category = ?";
    $params[] = $categoryFilter;
}

if ($searchQuery) {
    $whereClauses[] = $lang === 'ar' 
        ? "(title_ar LIKE ? OR content_ar LIKE ?)"
        : "(title_en LIKE ? OR content_en LIKE ?)";
    $searchTerm = "%{$searchQuery}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereSQL = implode(" AND ", $whereClauses);

// Get total posts count
$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM blog_posts WHERE {$whereSQL}");
$countStmt->execute($params);
$totalPosts = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalPosts / $postsPerPage);

// Get posts
$titleCol = $lang === 'ar' ? 'title_ar' : 'title_en';
$excerptCol = $lang === 'ar' ? 'excerpt_ar' : 'excerpt_en';

$postsStmt = $pdo->prepare("
    SELECT id, slug, {$titleCol} as title, {$excerptCol} as excerpt, 
           featured_image, category, published_at, views
    FROM blog_posts 
    WHERE {$whereSQL}
    ORDER BY published_at DESC
    LIMIT {$postsPerPage} OFFSET {$offset}
");
$postsStmt->execute($params);
$posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories from blog_posts
$categoriesStmt = $pdo->prepare("SELECT DISTINCT category FROM blog_posts WHERE status = 'published' AND category != '' AND category IS NOT NULL ORDER BY category");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = $lang === 'ar' ? 'المدونة وقصص النجاح - نيش سوسيتي' : 'Blog & Success Stories - Niche Society';
$pageDescription = $lang === 'ar' 
    ? 'اطلع على أحدث المقالات وقصص نجاح عملائنا في إدارة المنازل والفعاليات الفاخرة' 
    : 'Explore our latest articles and client success stories in luxury household and event management';
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
                    <?= $lang === 'ar' ? 'المدونة وقصص النجاح' : 'Blog & Success Stories' ?>
                </h1>
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: #000F2B !important; opacity: 1 !important; visibility: visible !important; text-shadow: 0 2px 4px rgba(255, 255, 255, 0.8);">
                    <?= $lang === 'ar' 
                        ? 'رؤى وإلهام من عالم الإدارة الفاخرة'
                        : 'Insights and inspiration from the world of luxury management'
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
                    <!-- Search Box -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title"><?= $lang === 'ar' ? 'البحث' : 'Search' ?></h3>
                        <form action="" method="GET" class="search-form">
                            <?php if ($categoryFilter): ?>
                            <input type="hidden" name="category" value="<?= $categoryFilter ?>">
                            <?php endif; ?>
                            <div class="search-input-group">
                                <input 
                                    type="text" 
                                    name="search" 
                                    class="form-control" 
                                    placeholder="<?= $lang === 'ar' ? 'ابحث...' : 'Search...' ?>"
                                    value="<?= htmlspecialchars($searchQuery) ?>"
                                >
                                <button type="submit" class="search-btn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title"><?= $lang === 'ar' ? 'التصنيفات' : 'Categories' ?></h3>
                        <ul class="category-list">
                            <li>
                                <a href="<?= url('blog.php') ?>" class="<?= !$categoryFilter ? 'active' : '' ?>">
                                    <?= $lang === 'ar' ? 'جميع المقالات' : 'All Articles' ?>
                                    <span class="count"><?= $totalPosts ?></span>
                                </a>
                            </li>
                            <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?= url('blog.php?category=' . urlencode($category)) ?>" 
                                   class="<?= $categoryFilter == $category ? 'active' : '' ?>">
                                    <?= htmlspecialchars($category) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Recent Posts -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title"><?= $lang === 'ar' ? 'المقالات الحديثة' : 'Recent Posts' ?></h3>
                        <?php
                        $recentStmt = $pdo->prepare("
                            SELECT slug, {$titleCol} as title, published_at 
                            FROM blog_posts 
                            WHERE status = 'published' AND published_at <= NOW()
                            ORDER BY published_at DESC 
                            LIMIT 5
                        ");
                        $recentStmt->execute();
                        $recentPosts = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <ul class="recent-posts-list">
                            <?php foreach ($recentPosts as $recent): ?>
                            <li>
                                <a href="<?= url('blog-post.php?slug=' . $recent['slug']) ?>">
                                    <?= htmlspecialchars($recent['title']) ?>
                                </a>
                                <span class="post-date">
                                    <?= date('M d, Y', strtotime($recent['published_at'])) ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Posts Grid -->
                <div class="col-lg-9" data-aos="fade-left">
                    <!-- Active Filters -->
                    <?php if ($searchQuery || $categoryFilter): ?>
                    <div class="active-filters mb-4">
                        <?php if ($searchQuery): ?>
                        <span class="filter-tag">
                            <?= $lang === 'ar' ? 'البحث:' : 'Search:' ?> "<?= htmlspecialchars($searchQuery) ?>"
                            <a href="<?= url('blog.php' . ($categoryFilter ? '?category=' . $categoryFilter : '')) ?>">×</a>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($categoryFilter): ?>
                        <span class="filter-tag">
                            <?= $lang === 'ar' ? 'التصنيف:' : 'Category:' ?> <?= htmlspecialchars($categoryFilter) ?>
                            <a href="<?= url('blog.php' . ($searchQuery ? '?search=' . urlencode($searchQuery) : '')) ?>">×</a>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Results Count -->
                    <div class="results-info mb-4">
                        <p class="text-muted">
                            <?= $lang === 'ar' 
                                ? "عرض {$offset}-" . min($offset + $postsPerPage, $totalPosts) . " من {$totalPosts} مقالة"
                                : "Showing {$offset}-" . min($offset + $postsPerPage, $totalPosts) . " of {$totalPosts} articles"
                            ?>
                        </p>
                    </div>

                    <?php if (empty($posts)): ?>
                    <!-- No Results -->
                    <div class="no-results text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                        <h3 class="mt-3"><?= $lang === 'ar' ? 'لا توجد مقالات' : 'No Articles Found' ?></h3>
                        <p class="text-muted">
                            <?= $lang === 'ar' 
                                ? 'لم نتمكن من العثور على أي مقالات تطابق معايير البحث'
                                : 'We couldn\'t find any articles matching your criteria'
                            ?>
                        </p>
                        <a href="<?= url('blog.php') ?>" class="btn btn-primary mt-3">
                            <?= $lang === 'ar' ? 'عرض جميع المقالات' : 'View All Articles' ?>
                        </a>
                    </div>
                    <?php else: ?>
                    <!-- Posts Grid -->
                    <div class="row">
                        <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up">
                            <article class="blog-card">
                                <div class="blog-card-image">
                                    <?php if ($post['featured_image']): ?>
                                    <img src="<?= url($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                                    <?php else: ?>
                                    <img src="<?= url('assets/images/niche-society-homepage-1-scaled.jpg') ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                                    <?php endif; ?>
                                    <div class="blog-card-overlay">
                                        <a href="<?= url('blog-post.php?slug=' . $post['slug']) ?>" class="read-more-btn">
                                            <?= $lang === 'ar' ? 'اقرأ المزيد' : 'Read More' ?>
                                            <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="blog-card-content">
                                    <div class="blog-card-meta">
                                        <span class="date">
                                            <i class="bi bi-calendar"></i>
                                            <?= date('M d, Y', strtotime($post['published_at'])) ?>
                                        </span>
                                        <span class="views">
                                            <i class="bi bi-eye"></i>
                                            <?= $post['views_count'] ?>
                                        </span>
                                    </div>
                                    <h3 class="blog-card-title">
                                        <a href="<?= url('blog-post.php?slug=' . $post['slug']) ?>">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="blog-card-excerpt">
                                        <?= htmlspecialchars($post['excerpt']) ?>
                                    </p>
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
                                <a class="page-link" href="<?= url('blog.php?page=' . ($currentPage - 1) . ($categoryFilter ? '&category=' . $categoryFilter : '') . ($searchQuery ? '&search=' . urlencode($searchQuery) : '')) ?>">
                                    <i class="bi bi-chevron-<?= $dir === 'rtl' ? 'right' : 'left' ?>"></i>
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            if ($startPage > 1): ?>
                                <li class="page-item"><a class="page-link" href="<?= url('blog.php?page=1' . ($categoryFilter ? '&category=' . $categoryFilter : '') . ($searchQuery ? '&search=' . urlencode($searchQuery) : '')) ?>">1</a></li>
                                <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('blog.php?page=' . $i . ($categoryFilter ? '&category=' . $categoryFilter : '') . ($searchQuery ? '&search=' . urlencode($searchQuery) : '')) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item"><a class="page-link" href="<?= url('blog.php?page=' . $totalPages . ($categoryFilter ? '&category=' . $categoryFilter : '') . ($searchQuery ? '&search=' . urlencode($searchQuery) : '')) ?>"><?= $totalPages ?></a></li>
                            <?php endif; ?>

                            <!-- Next -->
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('blog.php?page=' . ($currentPage + 1) . ($categoryFilter ? '&category=' . $categoryFilter : '') . ($searchQuery ? '&search=' . urlencode($searchQuery) : '')) ?>">
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

    <!-- Newsletter CTA -->
    <section class="section bg-cream">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0 text-center text-lg-start" data-aos="fade-right">
                    <h2><?= $lang === 'ar' ? 'اشترك في النشرة الإخبارية' : 'Subscribe to Our Newsletter' ?></h2>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'احصل على آخر الأخبار والمقالات مباشرة إلى بريدك الإلكتروني'
                            : 'Get the latest news and articles directly to your inbox'
                        ?>
                    </p>
                </div>
                <div class="col-lg-4 text-center text-lg-end" data-aos="fade-left">
                    <a href="<?= url('contact.php') ?>" class="btn btn-primary btn-lg">
                        <?= $lang === 'ar' ? 'اشترك الآن' : 'Subscribe Now' ?>
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
