# Helper Functions Documentation
## Niche Society Website - Complete Function Reference

---

## TABLE OF CONTENTS

1. [Session Management](#1-session-management) - 4 functions
2. [Database Operations](#2-database-operations) - 8 functions
3. [Form Validation](#3-form-validation) - 4 functions
4. [Contact Form Processing](#4-contact-form-processing) - 6 functions
5. [Blog Functions](#5-blog-functions) - 7 functions
6. [Testimonials](#6-testimonials) - 2 functions
7. [Services](#7-services) - 3 functions
8. [Success Stories](#8-success-stories) - 2 functions
9. [Image Handling](#9-image-handling) - 4 functions
10. [URL & Routing](#10-url--routing) - 5 functions
11. [Pagination](#11-pagination) - 2 functions
12. [Email Templates](#12-email-templates) - 1 function
13. [Logging & Debugging](#13-logging--debugging) - 4 functions
14. [Security Helpers](#14-security-helpers) - 5 functions
15. [Site Settings](#15-site-settings) - 2 functions
16. [Core Utilities](#16-core-utilities-from-helpersphp) - 17 functions

**Total: 76 Helper Functions**

---

## 1. SESSION MANAGEMENT

### `initSession()`
Initialize PHP session with security settings.
```php
initSession();
```
**When to use:** Call once at the start of your application (already called in config.php)

### `setFlashMessage($type, $message)`
Set a one-time message to display after redirect.
```php
setFlashMessage('success', 'Form submitted successfully!');
setFlashMessage('error', 'Please fix the errors below.');
setFlashMessage('warning', 'Your session is about to expire.');
setFlashMessage('info', 'New feature available!');
```

### `getFlashMessage()`
Get and clear flash message.
```php
$flash = getFlashMessage();
if ($flash) {
    echo "<div class='alert alert-{$flash['type']}'>{$flash['message']}</div>";
}
```

### `destroySession()`
Properly destroy session (for logout).
```php
destroySession();
redirect('/index.php');
```

---

## 2. DATABASE OPERATIONS

### `db()`
Get PDO database instance (singleton pattern).
```php
$pdo = db();
```

### `dbQuery($sql, $params = [])`
Execute prepared SQL query with parameters.
```php
$stmt = dbQuery('SELECT * FROM users WHERE email = ?', [$email]);
$users = $stmt->fetchAll();
```

### `dbFetchOne($sql, $params = [])`
Fetch single row as associative array.
```php
$user = dbFetchOne('SELECT * FROM users WHERE id = ?', [5]);
echo $user['name']; // Access column values
```

### `dbFetchAll($sql, $params = [])`
Fetch all rows as array of associative arrays.
```php
$posts = dbFetchAll('SELECT * FROM blog_posts WHERE status = ?', ['published']);
foreach ($posts as $post) {
    echo $post['title'];
}
```

### `dbInsert($table, $data)`
Insert data into table, returns last insert ID.
```php
$id = dbInsert('users', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+966501234567'
]);
echo "New user ID: $id";
```

### `dbUpdate($table, $data, $where)`
Update table data, returns affected rows count.
```php
$affected = dbUpdate(
    'users', 
    ['name' => 'Jane Doe', 'updated_at' => date('Y-m-d H:i:s')], 
    ['id' => 5]
);
```

### `dbDelete($table, $where)`
Delete from table, returns deleted rows count.
```php
$deleted = dbDelete('users', ['id' => 5]);
```

### `dbCount($table, $where = [])`
Count rows in table.
```php
$totalPosts = dbCount('blog_posts', ['status' => 'published']);
$allUsers = dbCount('users'); // Count all rows
```

---

## 3. FORM VALIDATION

### `validateRequired($value, $fieldName)`
Validate required field, returns error message or null.
```php
$error = validateRequired($_POST['name'], 'Name');
if ($error) {
    $errors['name'] = $error;
}
```

### `validateLength($value, $min, $max, $fieldName)`
Validate string length (UTF-8 safe).
```php
$error = validateLength($_POST['message'], 10, 500, 'Message');
if ($error) {
    $errors['message'] = $error;
}
```

### `validatePhone($phone)`
Validate Saudi phone number format, returns boolean.
```php
if (!validatePhone($phone)) {
    $errors['phone'] = 'Invalid phone number format';
}
// Accepts: +966501234567, 966501234567, 0501234567, 501234567
```

### `validateContactForm($data)`
Validate entire contact form, returns array of errors.
```php
$errors = validateContactForm($_POST);
if (empty($errors)) {
    // Process form
} else {
    // Display errors
    foreach ($errors as $field => $error) {
        echo "<div class='error'>$error</div>";
    }
}
```

---

## 4. CONTACT FORM PROCESSING

### `processContactForm($data)`
Process and save contact form, returns boolean.
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (processContactForm($_POST)) {
        setFlashMessage('success', t('contact.success', 'Thank you! We will contact you soon.'));
        redirect('/contact.php');
    } else {
        $errors = $_SESSION['form_errors'] ?? [];
        $formData = $_SESSION['form_data'] ?? [];
    }
}
```

### `sendContactNotificationEmail($data)`
Send notification to admin about new contact submission.
```php
// Called automatically by processContactForm()
// Manual usage:
$data = [
    'name' => 'John',
    'email' => 'john@example.com',
    'subject' => 'Inquiry',
    'message' => 'Hello...',
    'preferred_language' => 'en'
];
sendContactNotificationEmail($data);
```

### `sendContactConfirmationEmail($data)`
Send confirmation email to user.
```php
// Called automatically by processContactForm()
```

### `getContactSubmissions($limit = 50, $offset = 0, $status = null)`
Get contact submissions for admin panel.
```php
// Get all submissions
$submissions = getContactSubmissions();

// Get only new submissions
$newSubmissions = getContactSubmissions(50, 0, 'new');

// Pagination
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * 50;
$submissions = getContactSubmissions(50, $offset);
```

### `markContactAsRead($id)`
Mark contact submission as read.
```php
if (markContactAsRead($submissionId)) {
    echo "Marked as read";
}
```

---

## 5. BLOG FUNCTIONS

### `getAllBlogPosts($limit = 10, $offset = 0, $status = 'published')`
Get paginated blog posts (language-aware).
```php
// Homepage blog section (latest 3 posts)
$recentPosts = getAllBlogPosts(3, 0);

// Blog page with pagination
$page = $_GET['page'] ?? 1;
$postsPerPage = POSTS_PER_PAGE; // 9
$offset = ($page - 1) * $postsPerPage;
$posts = getAllBlogPosts($postsPerPage, $offset);
```

### `getBlogPostBySlug($slug)`
Get single blog post by slug (auto-increments views).
```php
$slug = $_GET['slug'] ?? '';
$post = getBlogPostBySlug($slug);

if ($post) {
    echo "<h1>{$post['title']}</h1>";
    echo "<div>{$post['content']}</div>";
} else {
    // 404 - Post not found
}
```

### `getRecentBlogPosts($limit = 5)`
Get recent blog posts (shortcut for sidebar/widgets).
```php
$recentPosts = getRecentBlogPosts(5);
foreach ($recentPosts as $post) {
    echo "<li><a href='/blog-single.php?slug={$post['slug']}'>{$post['title']}</a></li>";
}
```

### `getBlogPostsByCategory($category, $limit = 10, $offset = 0)`
Get posts filtered by category.
```php
$category = $_GET['category'] ?? 'news';
$posts = getBlogPostsByCategory($category, POSTS_PER_PAGE);
```

### `getBlogPostCount($category = null)`
Get total published posts count.
```php
// Total posts
$totalPosts = getBlogPostCount();

// Posts in category
$totalNews = getBlogPostCount('news');

// Calculate pagination
$totalPages = ceil($totalPosts / POSTS_PER_PAGE);
```

### `searchBlogPosts($query, $limit = 20)`
Search posts by title, excerpt, or content.
```php
if (isset($_GET['search'])) {
    $query = sanitize($_GET['search']);
    $results = searchBlogPosts($query);
    
    echo "<h2>Search Results for: " . htmlspecialchars($query) . "</h2>";
    foreach ($results as $post) {
        // Display search results
    }
}
```

---

## 6. TESTIMONIALS

### `getTestimonials($limit = 10, $featured = false)`
Get active testimonials (language-aware).
```php
// Homepage - featured testimonials only
$testimonials = getTestimonials(3, true);

// Success Stories page - all testimonials
$testimonials = getTestimonials(12);
```

### `getRandomTestimonials($count = 3)`
Get random testimonials for variety.
```php
$randomTestimonials = getRandomTestimonials(3);
```

---

## 7. SERVICES

### `getAllServices($featured = false)`
Get all active services (language-aware).
```php
// Homepage - featured services only
$services = getAllServices(true);

// Services page - all services
$allServices = getAllServices();
```

### `getServiceBySlug($slug)`
Get single service details.
```php
$slug = $_GET['service'] ?? '';
$service = getServiceBySlug($slug);

if ($service) {
    generateMetaTags(
        $service['meta_title'] ?? $service['title'],
        $service['meta_description'] ?? $service['description'],
        '',
        $service['image']
    );
}
```

### `getServicesByCategory()`
Get services grouped by category.
```php
$grouped = getServicesByCategory();
foreach ($grouped as $category => $services) {
    echo "<h3>$category</h3>";
    foreach ($services as $service) {
        echo "<div>{$service['title']}</div>";
    }
}
```

---

## 8. SUCCESS STORIES

### `getSuccessStories($limit = 10, $offset = 0, $featured = false)`
Get success stories (language-aware).
```php
// Homepage - featured stories
$stories = getSuccessStories(3, 0, true);

// Success Stories page - all stories
$stories = getSuccessStories(12);
```

### `getSuccessStoryBySlug($slug)`
Get single success story details.
```php
$slug = $_GET['story'] ?? '';
$story = getSuccessStoryBySlug($slug);
```

---

## 9. IMAGE HANDLING

### `getImageUrl($filename, $folder = '')`
Get full image URL.
```php
// Simple image
echo getImageUrl('logo.png'); // https://site.com/assets/images/logo.png

// Image in subfolder
echo getImageUrl('hero.jpg', 'banners'); // .../images/banners/hero.jpg

// Handles missing images
echo getImageUrl(''); // Returns placeholder image URL
```

### `imageExists($filename, $folder = '')`
Check if image file exists on server.
```php
if (imageExists($post['featured_image'])) {
    echo getImageUrl($post['featured_image']);
} else {
    echo getImageUrl('placeholder.jpg');
}
```

### `generateResponsiveImage($filename, $alt = '', $class = '', $lazy = true)`
Generate responsive image HTML tag.
```php
echo generateResponsiveImage('hero.jpg', 'Hero Banner', 'img-fluid');
// Output: <img src="..." alt="Hero Banner" class="img-fluid" loading="lazy">
```

### `getImagePath($filename, $folder = '')`
Get server file path (for file operations).
```php
$path = getImagePath('logo.png');
if (file_exists($path)) {
    $size = filesize($path);
}
```

---

## 10. URL & ROUTING

### `url($path, $params = [])`
Generate URL with language support.
```php
// Simple page
echo url('services.php'); // https://site.com/services.php?lang=ar

// With parameters
echo url('blog.php', ['category' => 'news', 'page' => 2]);
// https://site.com/blog.php?lang=ar&category=news&page=2
```

### `assetUrl($path)`
Get asset URL (CSS, JS, images).
```php
<link rel="stylesheet" href="<?= assetUrl('css/style.css') ?>">
<script src="<?= assetUrl('js/main.js') ?>"></script>
<img src="<?= assetUrl('images/logo.png') ?>">
```

### `currentPage()`
Get current page name without extension.
```php
$page = currentPage(); // Returns 'index', 'about', 'services', etc.

// Useful for navigation active state
$isActive = (currentPage() === 'services') ? 'active' : '';
```

### `generateBreadcrumb($items)`
Generate breadcrumb navigation HTML.
```php
echo generateBreadcrumb([
    ['title' => t('home'), 'url' => '/'],
    ['title' => t('blog'), 'url' => '/blog.php'],
    'Post Title' // Last item without URL
]);
```

### `slugify($text, $lang = 'en')`
Convert text to URL-friendly slug.
```php
$slug = slugify('Hello World'); // hello-world
$slugAr = slugify('مرحبا بكم', 'ar'); // مرحبا-بكم
```

---

## 11. PAGINATION

### `paginate($total, $perPage, $currentPage = 1)`
Calculate pagination data.
```php
$page = $_GET['page'] ?? 1;
$totalPosts = getBlogPostCount();
$pagination = paginate($totalPosts, POSTS_PER_PAGE, $page);

// Use pagination data
$posts = getAllBlogPosts($pagination['per_page'], $pagination['offset']);

echo "Page {$pagination['current_page']} of {$pagination['total_pages']}";
```

### `generatePaginationLinks($pagination, $baseUrl)`
Generate pagination HTML links.
```php
$pagination = paginate($totalPosts, 9, $currentPage);
echo generatePaginationLinks($pagination, '/blog.php?lang=ar');
```

---

## 12. EMAIL TEMPLATES

### `emailTemplate($template, $data = [])`
Load and render email template.
```php
// Available templates: 'contact-notification', 'contact-confirmation'

$html = emailTemplate('contact-confirmation', [
    'name' => 'John',
    'company_name' => SITE_NAME
]);

sendEmail('john@example.com', 'Thank you', $html);
```

---

## 13. LOGGING & DEBUGGING

### `logError($message, $context = [])`
Log error to file.
```php
try {
    // Some operation
} catch (Exception $e) {
    logError('Failed to process form', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
```

### `logActivity($action, $entityType = null, $entityId = null, $description = '')`
Log user activity to database.
```php
logActivity('contact_form_submitted', 'contact_submissions', $submissionId);
logActivity('blog_post_viewed', 'blog_posts', $postId);
logActivity('user_login', 'users', $userId, 'Successful login');
```

### `debugDump($data, $die = false)`
Debug output (only in DEBUG_MODE).
```php
debugDump($post); // Pretty print variable
debugDump($_POST, true); // Dump and stop execution
```

### `isDebugMode()`
Check if debug mode is enabled.
```php
if (isDebugMode()) {
    echo "Running in debug mode";
}
```

---

## 14. SECURITY HELPERS

### `hashPassword($password)`
Hash password for admin users.
```php
$hash = hashPassword('Admin@123');
// Store $hash in database
```

### `verifyPassword($password, $hash)`
Verify password against hash.
```php
$user = dbFetchOne('SELECT * FROM users WHERE email = ?', [$email]);
if ($user && verifyPassword($password, $user['password_hash'])) {
    // Login successful
}
```

### `generateRandomToken($length = 32)`
Generate random token for verification/reset.
```php
$resetToken = generateRandomToken(32);
$verifyToken = generateRandomToken(64);
```

### `isRateLimited($key, $maxAttempts = 5, $period = 300)`
Check rate limiting (prevents spam).
```php
$ip = $_SERVER['REMOTE_ADDR'];
if (isRateLimited("contact_form_$ip", 5, 300)) {
    die('Too many attempts. Please try again in 5 minutes.');
}

// Process form...
```

### `preventXSS($input)`
Additional XSS protection (allows safe HTML).
```php
$cleanHtml = preventXSS($userInput);
```

---

## 15. SITE SETTINGS

### `getSetting($key, $default = null)`
Get site setting from database.
```php
$siteName = getSetting('site_name_ar', 'نيش سوسايتي');
$phone = getSetting('site_phone', '+966532447976');
$isoCert = getSetting('iso_certificate', '25EQQN01');
```

### `updateSetting($key, $value)`
Update site setting (for admin panel).
```php
updateSetting('site_email', 'newemail@niche-society.com');
updateSetting('facebook_url', 'https://facebook.com/newpage');
```

---

## 16. CORE UTILITIES (from helpers.php)

### `getCurrentLang()`
Get current active language.
```php
$lang = getCurrentLang(); // 'ar' or 'en'
```

### `setLanguage($lang)`
Set user language preference.
```php
if (isset($_GET['lang'])) {
    setLanguage($_GET['lang']);
    redirect(getCurrentUrl());
}
```

### `loadTranslations($lang = null)`
Load translation file (JSON).
```php
$translations = loadTranslations('ar');
```

### `t($key, $default = '')`
Get translated text.
```php
echo t('nav.home', 'Home');
echo t('contact.title', 'Contact Us');
echo t('services.title', 'Our Services');
```

### `sanitize($data)`
Sanitize input (recursive for arrays).
```php
$clean = sanitize($_POST['name']);
$cleanArray = sanitize($_POST); // Sanitizes all array values
```

### `isValidEmail($email)`
Validate email format.
```php
if (isValidEmail($email)) {
    // Valid email
}
```

### `generateCsrfToken()`
Generate CSRF token for forms.
```php
<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
```

### `verifyCsrfToken($token)`
Verify CSRF token.
```php
if (!verifyCsrfToken($_POST['csrf_token'])) {
    die('Invalid security token');
}
```

### `redirect($url)`
Redirect to URL and exit.
```php
redirect('/thank-you.php');
redirect(SITE_URL . '/index.php');
```

### `getCurrentUrl()`
Get current page URL.
```php
$url = getCurrentUrl(); // /services.php?lang=ar
```

### `isActive($page)`
Check if page is active (for navigation).
```php
<li class="<?= isActive('services.php') ?>">
    <a href="services.php">Services</a>
</li>
```

### `formatDate($date, $format = 'Y-m-d')`
Format date with language support.
```php
echo formatDate('2025-01-15'); // Arabic: ١٥ يناير ٢٠٢٥ | English: January 15, 2025
```

### `sendEmail($to, $subject, $message, $from = CONTACT_EMAIL)`
Send email.
```php
sendEmail(
    'customer@example.com',
    'Thank you for contacting us',
    '<p>We received your message...</p>'
);
```

### `generateMetaTags($title, $description, $keywords = '', $image = '')`
Generate SEO meta tags.
```php
generateMetaTags(
    t('services.title', 'Our Services'),
    t('services.description', 'We provide premium household management services...'),
    'household management, luxury services, Saudi Arabia',
    'services-hero.jpg'
);
```

### `humanFileSize($bytes, $decimals = 2)`
Human-readable file size.
```php
echo humanFileSize(5242880); // 5.00 MB
```

### `truncate($text, $length = 150, $suffix = '...')`
Truncate text (UTF-8 safe).
```php
echo truncate($post['excerpt'], 100);
```

### `isAdmin()`
Check if user is admin.
```php
if (isAdmin()) {
    // Show admin menu
}
```

### `requireAdmin()`
Require admin access (redirects if not admin).
```php
requireAdmin(); // Place at top of admin pages
```

---

## USAGE PATTERNS

### Complete Contact Form Example

```php
<?php
require_once 'config/config.php';
require_once 'functions/helpers.php';

initSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check rate limiting
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isRateLimited("contact_$ip", 5, 300)) {
        setFlashMessage('error', t('error.rate_limit', 'Too many attempts. Please wait.'));
        redirect('/contact.php');
    }
    
    // Process form
    if (processContactForm($_POST)) {
        setFlashMessage('success', t('contact.success', 'Thank you! We will contact you soon.'));
        redirect('/contact.php');
    } else {
        $errors = $_SESSION['form_errors'] ?? [];
        $formData = $_SESSION['form_data'] ?? [];
    }
}

// Get flash message
$flash = getFlashMessage();
?>

<!-- Display flash message -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>">
    <?= $flash['message'] ?>
</div>
<?php endif; ?>

<!-- Contact Form -->
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    
    <input type="text" name="name" value="<?= $formData['name'] ?? '' ?>" required>
    <?php if (isset($errors['name'])): ?>
        <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    
    <!-- More form fields... -->
    
    <button type="submit"><?= t('contact.submit', 'Send Message') ?></button>
</form>
```

### Complete Blog Page Example

```php
<?php
require_once 'config/config.php';
require_once 'functions/helpers.php';

// Get pagination
$page = max(1, intval($_GET['page'] ?? 1));
$category = $_GET['category'] ?? null;

// Get posts
if ($category) {
    $totalPosts = dbCount('blog_posts', ['status' => 'published', 'category' => $category]);
    $pagination = paginate($totalPosts, POSTS_PER_PAGE, $page);
    $posts = getBlogPostsByCategory($category, $pagination['per_page'], $pagination['offset']);
} else {
    $totalPosts = getBlogPostCount();
    $pagination = paginate($totalPosts, POSTS_PER_PAGE, $page);
    $posts = getAllBlogPosts($pagination['per_page'], $pagination['offset']);
}

// SEO
generateMetaTags(
    t('blog.title', 'Our Blog'),
    t('blog.description', 'Latest news and insights...'),
    'blog, news, articles',
    'blog-hero.jpg'
);
?>

<!-- Display posts -->
<?php foreach ($posts as $post): ?>
<article>
    <img src="<?= getImageUrl($post['featured_image']) ?>" alt="<?= $post['title'] ?>">
    <h2><a href="blog-single.php?slug=<?= $post['slug'] ?>"><?= $post['title'] ?></a></h2>
    <p><?= $post['excerpt'] ?></p>
    <span><?= formatDate($post['published_at']) ?></span>
</article>
<?php endforeach; ?>

<!-- Pagination -->
<?= generatePaginationLinks($pagination, '/blog.php?lang=' . getCurrentLang()) ?>
```

---

## CONFIGURATION CONSTANTS

All available constants (from config.php):

```php
// Site
SITE_NAME, SITE_TAGLINE, SITE_URL, ASSETS_URL, ROOT_PATH

// Contact
CONTACT_EMAIL, ADMIN_EMAIL, CONTACT_PHONE
CONTACT_ADDRESS_AR, CONTACT_ADDRESS_EN

// Languages
DEFAULT_LANG, SUPPORTED_LANGUAGES

// Security
CSRF_TOKEN_EXPIRY, MAX_LOGIN_ATTEMPTS
RATE_LIMIT_PERIOD, RATE_LIMIT_MAX_ATTEMPTS

// Pagination
POSTS_PER_PAGE, TESTIMONIALS_PER_PAGE, SERVICES_PER_PAGE, CONTACTS_PER_PAGE

// Files
UPLOAD_MAX_SIZE, ALLOWED_FILE_TYPES, UPLOAD_PATH

// Email
SMTP_ENABLED, SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD

// Cache
CACHE_ENABLED, CACHE_DURATION, CACHE_PATH

// Debug
DEBUG_MODE, LOG_PATH, LOG_ERRORS

// Session
SESSION_LIFETIME, SESSION_NAME

// Social Media
SOCIAL_FACEBOOK, SOCIAL_TWITTER, SOCIAL_INSTAGRAM, SOCIAL_LINKEDIN

// Company
ISO_CERTIFICATE_NUMBER, ISO_VALID_UNTIL
```

---

## BEST PRACTICES

### 1. Always Use Prepared Statements
```php
// GOOD ✅
$post = dbFetchOne('SELECT * FROM blog_posts WHERE id = ?', [$id]);

// BAD ❌ - SQL injection risk
$post = dbFetchOne("SELECT * FROM blog_posts WHERE id = $id");
```

### 2. Sanitize All User Input
```php
$name = sanitize($_POST['name']);
$email = sanitize($_POST['email']);
```

### 3. Use CSRF Protection
```php
// In form
<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

// In processor
if (!verifyCsrfToken($_POST['csrf_token'])) {
    die('Security validation failed');
}
```

### 4. Implement Rate Limiting
```php
if (isRateLimited("form_$ip", 5, 300)) {
    die('Too many attempts');
}
```

### 5. Use Flash Messages for User Feedback
```php
setFlashMessage('success', 'Action completed');
redirect('/page.php');
```

### 6. Log Important Actions
```php
logActivity('user_login', 'users', $userId);
logError('Database connection failed', ['error' => $e->getMessage()]);
```

### 7. Use Translation Keys
```php
// GOOD ✅
echo t('nav.home', 'Home');

// BAD ❌
echo 'Home'; // Not translatable
```

---

## ERROR HANDLING

### Try-Catch Pattern
```php
try {
    $id = dbInsert('blog_posts', $data);
    logActivity('post_created', 'blog_posts', $id);
    setFlashMessage('success', 'Post created successfully');
} catch (Exception $e) {
    logError('Failed to create post', [
        'error' => $e->getMessage(),
        'data' => $data
    ]);
    setFlashMessage('error', 'Failed to create post');
}
```

### Validation Pattern
```php
$errors = validateContactForm($_POST);

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    redirect('/contact.php');
    exit;
}

// Process valid form...
```

---

**Document Version:** 1.0  
**Last Updated:** December 22, 2025  
**Total Functions:** 76 helper functions  
**Files:** helpers.php (17 functions) + helpers-extended.php (59 functions)
