<?php
/**
 * Blog Post Detail Page - Niche Society
 * Displays individual blog post articles on your website
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// Handle language switch
handleLanguageSwitch();

$lang = getCurrentLanguage();
$t = getTranslations($lang);
$dir = getTextDirection($lang);

// Get slug from URL
$slug = isset($_GET['slug']) ? htmlspecialchars($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: ' . url('blog.php'));
    exit;
}

// Get post by slug
$titleCol = $lang === 'ar' ? 'title_ar' : 'title_en';
$contentCol = $lang === 'ar' ? 'content_ar' : 'content_en';
$excerptCol = $lang === 'ar' ? 'excerpt_ar' : 'excerpt_en';

$stmt = $pdo->prepare("
    SELECT id, slug, {$titleCol} as title, {$excerptCol} as excerpt, {$contentCol} as content,
           featured_image, category, tags, published_at, views
    FROM blog_posts 
    WHERE slug = ? AND status = 'published'
");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// If post not found, redirect to blog
if (!$post) {
    header('Location: ' . url('blog.php'));
    exit;
}

// Increment view count
$updateViewsStmt = $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
$updateViewsStmt->execute([$post['id']]);

// Get original source URL if available (for "Read on original site" link)
$sourceUrl = null;
if (!empty($post['tags']) && preg_match('/source_url:([^\s]+)/', $post['tags'], $matches)) {
    $sourceUrl = $matches[1];
}

// Function to fetch full article content from source URL
function fetchFullArticleContent($url) {
    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }
    
    // Use cURL to fetch the article
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || empty($html)) {
        return null;
    }
    
    // Try to extract article content using common patterns
    // This is a simplified version - you might want to use a library like Goutte or DiDOM for better extraction
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new DOMXPath($dom);
    
    // Try common article content selectors
    $contentSelectors = [
        '//article',
        '//div[contains(@class, "article")]',
        '//div[contains(@class, "content")]',
        '//div[contains(@class, "post-content")]',
        '//div[contains(@class, "entry-content")]',
        '//div[@id="content"]',
        '//main',
    ];
    
    foreach ($contentSelectors as $selector) {
        $nodes = $xpath->query($selector);
        if ($nodes && $nodes->length > 0) {
            $content = '';
            foreach ($nodes as $node) {
                $content .= $dom->saveHTML($node);
            }
            if (mb_strlen(strip_tags($content)) > 500) { // Only return if substantial content
                return $content;
            }
        }
    }
    
    return null;
}

// Check if content is too short (likely just an excerpt)
$content = $post['content'] ?? '';
$contentLength = mb_strlen(strip_tags($content));
$isContentShort = $contentLength < 500; // Less than 500 characters is likely just an excerpt

// Try to fetch full content if it's short and we have a source URL
$fullContent = null;
if ($isContentShort && $sourceUrl) {
    $fullContent = fetchFullArticleContent($sourceUrl);
    if ($fullContent) {
        $content = $fullContent;
    }
}

// Get related posts (same category, excluding current post)
$relatedStmt = $pdo->prepare("
    SELECT slug, {$titleCol} as title, {$excerptCol} as excerpt, featured_image, published_at
    FROM blog_posts 
    WHERE category = ? AND slug != ? AND status = 'published'
    ORDER BY published_at DESC
    LIMIT 3
");
$relatedStmt->execute([$post['category'] ?? '', $slug]);
$relatedPosts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

// Fix image URL
$imageUrl = (!empty($post['featured_image']) && preg_match('/^https?:\/\//', $post['featured_image'])) 
    ? $post['featured_image'] 
    : url($post['featured_image'] ?? 'assets/images/niche-society-homepage-1-scaled.jpg');

$pageTitle = htmlspecialchars($post['title']) . ' - ' . ($lang === 'ar' ? 'نيش سوسيتي' : 'Niche Society');
$pageDescription = htmlspecialchars($post['excerpt']);
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

    <!-- Article Header -->
    <section class="page-hero" style="background-image: url('<?= $imageUrl ?>'); min-height: 50vh;">
        <div class="container">
            <div class="hero-content">
                <?php if ($post['category']): ?>
                <div class="blog-category-tag" style="margin-bottom: 1rem;">
                    <?= htmlspecialchars(strtoupper($post['category'])) ?>
                </div>
                <?php endif; ?>
                <h1 class="hero-title" data-aos="fade-up">
                    <?= htmlspecialchars($post['title']) ?>
                </h1>
                <div class="hero-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: #000F2B !important; opacity: 1 !important; visibility: visible !important; text-shadow: 0 2px 4px rgba(255, 255, 255, 0.8);">
                    <span>
                        <i class="bi bi-calendar"></i>
                        <?= date('M d, Y', strtotime($post['published_at'])) ?>
                    </span>
                    <span style="margin: 0 1rem;">•</span>
                    <span>
                        <i class="bi bi-eye"></i>
                        <?= number_format($post['views']) ?> <?= $lang === 'ar' ? 'مشاهدة' : 'views' ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Article Content -->
    <section class="section">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <article class="blog-post-content">
                        <?php if ($post['featured_image']): ?>
                        <div class="blog-post-image mb-4">
                            <img src="<?= $imageUrl ?>" 
                                 alt="<?= htmlspecialchars($post['title']) ?>"
                                 class="img-fluid"
                                 onerror="this.onerror=null; this.src='<?= url('assets/images/niche-society-homepage-1-scaled.jpg') ?>';"
                                 loading="lazy">
                        </div>
                        <?php endif; ?>
                        
                        <div class="blog-post-body">
                            <?php 
                            // Display content (already HTML from RSS feed)
                            // Clean up and format the content
                            // $content is already set above with full content fetching logic
                            
                            // Display content with proper formatting
                            if (empty($content)) {
                                // If no content at all, show excerpt and link to source
                                echo '<p class="lead">' . htmlspecialchars($post['excerpt']) . '</p>';
                                if ($sourceUrl) {
                                    echo '<div class="alert alert-info mt-4" style="background: #f0f4f8; border-left: 4px solid #602234; padding: 1.5rem;">';
                                    echo '<p style="margin: 0 0 1rem 0; font-size: 1.1rem;">';
                                    echo $lang === 'ar' 
                                        ? 'لم يتم العثور على المحتوى الكامل. يرجى قراءة المقال على الموقع الأصلي.'
                                        : 'Full article content not available. Please read the full article on the original website.';
                                    echo '</p>';
                                    echo '<a href="' . htmlspecialchars($sourceUrl) . '" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="background: #602234; border-color: #602234;">';
                                    echo $lang === 'ar' ? 'اقرأ المقال الكامل' : 'Read Full Article';
                                    echo ' <i class="bi bi-arrow-' . ($dir === 'rtl' ? 'left' : 'right') . '"></i>';
                                    echo '</a>';
                                    echo '</div>';
                                }
                            } elseif (strip_tags($content) !== $content) {
                                // HTML content - sanitize and display
                                // Remove script tags and other potentially dangerous elements
                                $content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $content);
                                
                                // Clean up common RSS feed artifacts
                                $content = preg_replace('/<div[^>]*class="[^"]*feed[^"]*"[^>]*>.*?<\/div>/is', '', $content);
                                $content = preg_replace('/<a[^>]*href="[^"]*feed[^"]*"[^>]*>.*?<\/a>/is', '', $content);
                                
                                // Fix oversized icons/images by removing problematic inline styles
                                // Remove large width/height from inline styles on images
                                $content = preg_replace_callback('/<img([^>]*)\s+style="([^"]*)"([^>]*)>/i', function($matches) {
                                    $before = $matches[1];
                                    $style = $matches[2];
                                    $after = $matches[3];
                                    
                                    // Remove width and height from style if they're too large
                                    $style = preg_replace('/width\s*:\s*\d+px/i', '', $style);
                                    $style = preg_replace('/height\s*:\s*\d+px/i', '', $style);
                                    $style = preg_replace('/;\s*;/', ';', $style);
                                    $style = trim($style, '; ');
                                    
                                    return '<img' . $before . ($style ? ' style="' . $style . '"' : '') . $after . '>';
                                }, $content);
                                
                                // Remove width and height attributes that are too large
                                $content = preg_replace('/<img([^>]*)\s+width=["\']?\d{3,}["\']?/i', '<img$1', $content);
                                $content = preg_replace('/<img([^>]*)\s+height=["\']?\d{3,}["\']?/i', '<img$1', $content);
                                
                                // Fix SVG icons - ensure they have proper sizing
                                $content = preg_replace('/<svg([^>]*)>/i', '<svg$1 style="max-width: 24px; width: 24px; height: 24px; vertical-align: middle; display: inline-block;">', $content);
                                
                                // Remove oversized divs
                                $content = preg_replace('/<div([^>]*)\s+style="[^"]*width\s*:\s*\d{3,}px[^"]*">/i', '<div$1 style="max-width: 100%; width: 100%;">', $content);
                                
                                echo $content;
                                
                                // If content is still short, show prominent link to source
                                if ($isContentShort && $sourceUrl && !$fullContent) {
                                    echo '<div class="alert alert-info mt-4" style="background: #f0f4f8; border-left: 4px solid #602234; padding: 1.5rem;">';
                                    echo '<p style="margin: 0 0 1rem 0; font-size: 1.1rem; font-weight: 600;">';
                                    echo $lang === 'ar' 
                                        ? 'اقرأ المزيد من التفاصيل على الموقع الأصلي'
                                        : 'Read more details on the original website';
                                    echo '</p>';
                                    echo '<a href="' . htmlspecialchars($sourceUrl) . '" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="background: #602234; border-color: #602234;">';
                                    echo $lang === 'ar' ? 'اقرأ المقال الكامل' : 'Read Full Article';
                                    echo ' <i class="bi bi-arrow-' . ($dir === 'rtl' ? 'left' : 'right') . '"></i>';
                                    echo '</a>';
                                    echo '</div>';
                                }
                            } else {
                                // Plain text - convert to paragraphs
                                $paragraphs = preg_split('/\n\s*\n/', trim($content));
                                foreach ($paragraphs as $para) {
                                    if (!empty(trim($para))) {
                                        echo '<p>' . nl2br(htmlspecialchars(trim($para))) . '</p>';
                                    }
                                }
                                
                                // If content is still short, show prominent link to source
                                if ($isContentShort && $sourceUrl && !$fullContent) {
                                    echo '<div class="alert alert-info mt-4" style="background: #f0f4f8; border-left: 4px solid #602234; padding: 1.5rem;">';
                                    echo '<p style="margin: 0 0 1rem 0; font-size: 1.1rem; font-weight: 600;">';
                                    echo $lang === 'ar' 
                                        ? 'اقرأ المزيد من التفاصيل على الموقع الأصلي'
                                        : 'Read more details on the original website';
                                    echo '</p>';
                                    echo '<a href="' . htmlspecialchars($sourceUrl) . '" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="background: #602234; border-color: #602234;">';
                                    echo $lang === 'ar' ? 'اقرأ المقال الكامل' : 'Read Full Article';
                                    echo ' <i class="bi bi-arrow-' . ($dir === 'rtl' ? 'left' : 'right') . '"></i>';
                                    echo '</a>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                        
                        <?php if ($sourceUrl): ?>
                        <div class="blog-post-source mt-4 pt-4 border-top" style="border-top: 1px solid rgba(96, 34, 52, 0.15); padding-top: 1.5rem;">
                            <p style="color: #666; font-size: 0.95rem; margin: 0;">
                                <i class="bi bi-link-45deg" style="margin-<?= $dir === 'rtl' ? 'left' : 'right'; ?>: 0.5rem;"></i>
                                <?= $lang === 'ar' ? 'المصدر الأصلي:' : 'Original Source:' ?>
                                <a href="<?= htmlspecialchars($sourceUrl) ?>" target="_blank" rel="noopener noreferrer" style="color: #602234; text-decoration: underline; font-weight: 500;">
                                    <?= htmlspecialchars(parse_url($sourceUrl, PHP_URL_HOST)) ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                    </article>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <aside class="blog-sidebar">
                        <!-- Search Widget -->
                        <div class="sidebar-widget">
                            <h3 class="widget-title"><?= $lang === 'ar' ? 'البحث' : 'Search' ?></h3>
                            <form action="<?= url('blog.php') ?>" method="GET" class="search-form">
                                <div class="search-input-group">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="<?= $lang === 'ar' ? 'ابحث...' : 'Search...' ?>"
                                        value=""
                                    >
                                    <button type="submit" class="search-btn">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Categories Widget -->
                        <div class="sidebar-widget">
                            <h3 class="widget-title"><?= $lang === 'ar' ? 'التصنيفات' : 'Categories' ?></h3>
                            <?php
                            // Get categories with counts
                            $categoriesStmt = $pdo->prepare("
                                SELECT category, COUNT(*) as count 
                                FROM blog_posts 
                                WHERE status = 'published' AND category != '' AND category IS NOT NULL 
                                GROUP BY category 
                                ORDER BY category
                            ");
                            $categoriesStmt->execute();
                            $categoriesWithCounts = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            // Get total count
                            $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published'");
                            $totalCount = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <ul class="category-list">
                                <li>
                                    <a href="<?= url('blog.php') ?>">
                                        <?= $lang === 'ar' ? 'الكل' : 'All' ?>
                                        <span class="count">(<?= $totalCount ?>)</span>
                                    </a>
                                </li>
                                <?php foreach ($categoriesWithCounts as $cat): ?>
                                <li>
                                    <a href="<?= url('blog.php?category=' . urlencode($cat['category'])) ?>">
                                        <?= htmlspecialchars($cat['category']) ?>
                                        <span class="count">(<?= $cat['count'] ?>)</span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Recent Posts Widget -->
                        <div class="sidebar-widget">
                            <h3 class="widget-title"><?= $lang === 'ar' ? 'المقالات الحديثة' : 'Recent Posts' ?></h3>
                            <?php
                            // Get recent posts with featured images
                            $recentStmt = $pdo->prepare("
                                SELECT slug, {$titleCol} as title, featured_image, published_at 
                                FROM blog_posts 
                                WHERE status = 'published' AND published_at <= NOW() AND slug != ?
                                ORDER BY published_at DESC 
                                LIMIT 5
                            ");
                            $recentStmt->execute([$slug]);
                            $recentPosts = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <ul class="recent-posts-list">
                                <?php foreach ($recentPosts as $recent): 
                                    $recentImageUrl = (!empty($recent['featured_image']) && preg_match('/^https?:\/\//', $recent['featured_image'])) 
                                        ? $recent['featured_image'] 
                                        : url($recent['featured_image'] ?? 'assets/images/niche-society-homepage-1-scaled.jpg');
                                ?>
                                <li>
                                    <a href="<?= url('blog-post.php?slug=' . $recent['slug']) ?>" class="recent-post-item">
                                        <div class="recent-post-thumbnail">
                                            <img src="<?= $recentImageUrl ?>" 
                                                 alt="<?= htmlspecialchars($recent['title']) ?>"
                                                 onerror="this.onerror=null; this.src='<?= url('assets/images/niche-society-homepage-1-scaled.jpg') ?>';"
                                                 loading="lazy">
                                        </div>
                                        <div class="recent-post-info">
                                            <h4 class="recent-post-title"><?= htmlspecialchars($recent['title']) ?></h4>
                                            <span class="recent-post-date">
                                                <?= date('M d, Y', strtotime($recent['published_at'])) ?>
                                            </span>
                                        </div>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Tags Widget -->
                        <div class="sidebar-widget">
                            <h3 class="widget-title"><?= $lang === 'ar' ? 'الوسوم' : 'Tags' ?></h3>
                            <?php
                            // Extract tags from all posts
                            $tagsStmt = $pdo->query("
                                SELECT tags 
                                FROM blog_posts 
                                WHERE status = 'published' AND tags IS NOT NULL AND tags != ''
                            ");
                            $allTags = [];
                            while ($row = $tagsStmt->fetch(PDO::FETCH_ASSOC)) {
                                // Extract tags (skip source_url and other metadata)
                                if (preg_match_all('/\btag:([^\s]+)/', $row['tags'], $matches)) {
                                    $allTags = array_merge($allTags, $matches[1]);
                                }
                            }
                            // Count and sort tags
                            $tagCounts = array_count_values($allTags);
                            arsort($tagCounts);
                            $popularTags = array_slice(array_keys($tagCounts), 0, 10);
                            
                            // If no tags found, use common tags based on categories
                            if (empty($popularTags)) {
                                $popularTags = ['Business', 'Technology', 'Management', 'Luxury', 'Service', 'Property', 'Estate', 'Protocol', 'Concierge', 'VIP'];
                            }
                            ?>
                            <div class="tags-cloud">
                                <?php foreach ($popularTags as $tag): ?>
                                <a href="<?= url('blog.php?search=' . urlencode($tag)) ?>" class="tag-badge">
                                    <?= htmlspecialchars(ucfirst($tag)) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Posts -->
    <?php if (!empty($relatedPosts)): ?>
    <section class="section bg-cream">
        <div class="container">
            <h2 class="section-title text-center mb-5">
                <?= $lang === 'ar' ? 'مقالات ذات صلة' : 'Related Articles' ?>
            </h2>
            <div class="row">
                <?php foreach ($relatedPosts as $related): ?>
                <div class="col-md-4 mb-4">
                    <div class="blog-card">
                        <div class="blog-card-image">
                            <a href="<?= url('blog-post.php?slug=' . $related['slug']) ?>">
                                <?php 
                                $relatedImageUrl = (!empty($related['featured_image']) && preg_match('/^https?:\/\//', $related['featured_image'])) 
                                    ? $related['featured_image'] 
                                    : url($related['featured_image'] ?? 'assets/images/niche-society-homepage-1-scaled.jpg');
                                ?>
                                <img src="<?= $relatedImageUrl ?>" alt="<?= htmlspecialchars($related['title']) ?>">
                            </a>
                        </div>
                        <div class="blog-card-content">
                            <h3 class="blog-card-title">
                                <a href="<?= url('blog-post.php?slug=' . $related['slug']) ?>">
                                    <?= htmlspecialchars($related['title']) ?>
                                </a>
                            </h3>
                            <p class="blog-card-excerpt">
                                <?= htmlspecialchars($related['excerpt']) ?>
                            </p>
                            <a href="<?= url('blog-post.php?slug=' . $related['slug']) ?>" class="blog-read-more-link">
                                <?= $lang === 'ar' ? 'اقرأ المزيد' : 'Read More' ?>
                                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Back to Blog -->
    <section class="section">
        <div class="container text-center">
            <a href="<?= url('blog.php') ?>" class="btn btn-primary">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <?= $lang === 'ar' ? 'العودة إلى المدونة' : 'Back to Blog' ?>
            </a>
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
