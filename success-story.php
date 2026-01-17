<?php
/**
 * Success Story Detail Page - Niche Society
 * Displays individual success story articles on your website
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
    header('Location: ' . url('success-stories.php'));
    exit;
}

// Get story by slug
$titleCol = $lang === 'ar' ? 'title_ar' : 'title_en';
$descriptionCol = $lang === 'ar' ? 'description_ar' : 'description_en';
$contentCol = $lang === 'ar' ? 'content_ar' : 'content_en';
$clientNameCol = $lang === 'ar' ? 'client_name_ar' : 'client_name_en';

$stmt = $pdo->prepare("
    SELECT id, slug, {$titleCol} as title, {$descriptionCol} as description, {$contentCol} as content,
           {$clientNameCol} as client_name, client_type, service_category, 
           image, project_date, featured
    FROM success_stories 
    WHERE slug = ? AND status = 'active'
");
$stmt->execute([$slug]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);

// If story not found, redirect to success stories
if (!$story) {
    header('Location: ' . url('success-stories.php'));
    exit;
}

// Get related stories (same service category, excluding current story)
$relatedStmt = $pdo->prepare("
    SELECT slug, {$titleCol} as title, {$descriptionCol} as description, image, project_date
    FROM success_stories 
    WHERE service_category = ? AND slug != ? AND status = 'active'
    ORDER BY project_date DESC
    LIMIT 3
");
$relatedStmt->execute([$story['service_category'] ?? '', $slug]);
$relatedStories = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

// Fix image URL
$imageUrl = (!empty($story['image']) && preg_match('/^https?:\/\//', $story['image'])) 
    ? $story['image'] 
    : url($story['image'] ?? 'assets/images/niche-society-homepage-1-scaled.jpg');

$pageTitle = htmlspecialchars($story['title']) . ' - ' . ($lang === 'ar' ? 'قصص النجاح - نيش سوسيتي' : 'Success Stories - Niche Society');
$pageDescription = htmlspecialchars($story['description']);
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

    <!-- Story Header -->
    <section class="page-hero" style="background-image: url('<?= $imageUrl ?>'); min-height: 50vh;">
        <div class="container">
            <div class="hero-content">
                <?php if ($story['service_category']): ?>
                <div class="blog-category-tag" style="margin-bottom: 1rem;">
                    <?= htmlspecialchars(strtoupper($story['service_category'])) ?>
                </div>
                <?php endif; ?>
                <h1 class="hero-title" data-aos="fade-up">
                    <?= htmlspecialchars($story['title']) ?>
                </h1>
                <div class="hero-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: #000F2B !important; opacity: 1 !important; visibility: visible !important; text-shadow: 0 2px 4px rgba(255, 255, 255, 0.8);">
                    <?php if ($story['client_name']): ?>
                    <span>
                        <i class="bi bi-person-circle"></i>
                        <?= htmlspecialchars($story['client_name']) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($story['project_date']): ?>
                    <span style="margin: 0 1rem;">•</span>
                    <span>
                        <i class="bi bi-calendar"></i>
                        <?= date('M Y', strtotime($story['project_date'])) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Content -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <article class="blog-post-content">
                        <?php if ($story['image']): ?>
                        <div class="blog-post-image mb-4">
                            <img src="<?= $imageUrl ?>" 
                                 alt="<?= htmlspecialchars($story['title']) ?>"
                                 class="img-fluid"
                                 onerror="this.onerror=null; this.src='<?= url('assets/images/niche-society-homepage-1-scaled.jpg') ?>';"
                                 loading="lazy">
                        </div>
                        <?php endif; ?>
                        
                        <div class="blog-post-body">
                            <?php 
                            // Display content (already HTML from database)
                            $content = $story['content'] ?? $story['description'];
                            
                            // If content is HTML, output as-is; otherwise, format as paragraphs
                            if (strip_tags($content) !== $content) {
                                // HTML content
                                echo $content;
                            } else {
                                // Plain text - convert to paragraphs
                                $paragraphs = preg_split('/\n\s*\n/', trim($content));
                                foreach ($paragraphs as $para) {
                                    if (!empty(trim($para))) {
                                        echo '<p>' . nl2br(htmlspecialchars(trim($para))) . '</p>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Stories -->
    <?php if (!empty($relatedStories)): ?>
    <section class="section bg-cream">
        <div class="container">
            <h2 class="section-title text-center mb-5">
                <?= $lang === 'ar' ? 'قصص ذات صلة' : 'Related Success Stories' ?>
            </h2>
            <div class="row">
                <?php foreach ($relatedStories as $related): ?>
                <div class="col-md-4 mb-4">
                    <div class="blog-card">
                        <div class="blog-card-image">
                            <a href="<?= url('success-story.php?slug=' . $related['slug']) ?>">
                                <?php 
                                $relatedImageUrl = (!empty($related['image']) && preg_match('/^https?:\/\//', $related['image'])) 
                                    ? $related['image'] 
                                    : url($related['image'] ?? 'assets/images/niche-society-homepage-1-scaled.jpg');
                                ?>
                                <img src="<?= $relatedImageUrl ?>" alt="<?= htmlspecialchars($related['title']) ?>">
                            </a>
                        </div>
                        <div class="blog-card-content">
                            <h3 class="blog-card-title">
                                <a href="<?= url('success-story.php?slug=' . $related['slug']) ?>">
                                    <?= htmlspecialchars($related['title']) ?>
                                </a>
                            </h3>
                            <p class="blog-card-excerpt">
                                <?= htmlspecialchars($related['description']) ?>
                            </p>
                            <a href="<?= url('success-story.php?slug=' . $related['slug']) ?>" class="blog-read-more-link">
                                <?= $lang === 'ar' ? 'اقرأ القصة' : 'Read Story' ?>
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

    <!-- Back to Success Stories -->
    <section class="section">
        <div class="container text-center">
            <a href="<?= url('success-stories.php') ?>" class="btn btn-primary">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <?= $lang === 'ar' ? 'العودة إلى قصص النجاح' : 'Back to Success Stories' ?>
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
