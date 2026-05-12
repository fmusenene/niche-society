<?php
/**
 * Extended Helper Functions for Niche Society Website
 * 
 * This file contains additional helper functions that work alongside
 * the core helpers.php file. These functions handle:
 * - Database operations
 * - Form validation and processing
 * - Email templates
 * - Session/flash messages
 * - Image handling
 * - Pagination
 * - Blog management
 * - Testimonials management
 * - Contact form processing
 * - URL generation
 * - Security utilities
 * 
 * @package NicheSociety
 * @version 2.0
 */

// Prevent direct access to this file
if (!defined('SITE_NAME') && !defined('SITE_URL')) {
    die('Direct access not permitted');
}

// ============================================================
// SECTION 1: SESSION & FLASH MESSAGES
// ============================================================

/**
 * Initialize PHP session with security settings
 * Call this once at the start of your application
 * 
 * @return void
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        
        session_start();
        
        // Regenerate session ID on first visit
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }
}

/**
 * Set a flash message (one-time message)
 * 
 * @param string $type Message type: 'success', 'error', 'warning', 'info'
 * @param string $message The message text
 * @return void
 * 
 * @example
 * setFlashMessage('success', 'Form submitted successfully!');
 */
function setFlashMessage($type, $message) {
    if (!isset($_SESSION)) {
        initSession();
    }
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash message array with 'type' and 'message' keys, or null
 * 
 * @example
 * $flash = getFlashMessage();
 * if ($flash) {
 *     echo "<div class='alert alert-{$flash['type']}'>{$flash['message']}</div>";
 * }
 */
function getFlashMessage() {
    if (!isset($_SESSION)) {
        initSession();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    
    return null;
}

/**
 * Properly destroy session
 * 
 * @return void
 */
function destroySession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
}

// ============================================================
// SECTION 2: DATABASE OPERATIONS
// ============================================================

/**
 * Get PDO database instance (singleton pattern)
 * 
 * @return PDO Database connection instance
 * @throws PDOException If connection fails
 */
function db() {
    static $pdo = null;
    
    if ($pdo === null) {
        require_once ROOT_PATH . '/config/database.php';
        $pdo = $GLOBALS['pdo'] ?? null;
        
        if (!$pdo) {
            throw new PDOException('Database connection not established');
        }
    }
    
    return $pdo;
}

/**
 * Execute a prepared SQL query
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return PDOStatement Executed statement
 * @throws PDOException On query failure
 * 
 * @example
 * $stmt = dbQuery('SELECT * FROM users WHERE email = ?', [$email]);
 */
function dbQuery($sql, $params = []) {
    $pdo = db();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch single row from database
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return array|null Associative array or null if not found
 * 
 * @example
 * $user = dbFetchOne('SELECT * FROM users WHERE id = ?', [$id]);
 */
function dbFetchOne($sql, $params = []) {
    $stmt = dbQuery($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * Fetch all rows from database
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return array Array of associative arrays
 * 
 * @example
 * $posts = dbFetchAll('SELECT * FROM blog_posts WHERE status = ?', ['published']);
 */
function dbFetchAll($sql, $params = []) {
    $stmt = dbQuery($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Insert data into table
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int Last inserted ID
 * 
 * @example
 * $id = dbInsert('users', [
 *     'name' => 'John',
 *     'email' => 'john@example.com'
 * ]);
 */
function dbInsert($table, $data) {
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    dbQuery($sql, array_values($data));
    
    return db()->lastInsertId();
}

/**
 * Update data in table
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value to update
 * @param array $where WHERE conditions ['column' => 'value']
 * @return int Number of affected rows
 * 
 * @example
 * dbUpdate('users', ['name' => 'Jane'], ['id' => 5]);
 */
function dbUpdate($table, $data, $where) {
    $set = [];
    $values = [];
    
    foreach ($data as $column => $value) {
        $set[] = "$column = ?";
        $values[] = $value;
    }
    
    $whereClause = [];
    foreach ($where as $column => $value) {
        $whereClause[] = "$column = ?";
        $values[] = $value;
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $set) . 
           " WHERE " . implode(' AND ', $whereClause);
    
    $stmt = dbQuery($sql, $values);
    return $stmt->rowCount();
}

/**
 * Delete from table
 * 
 * @param string $table Table name
 * @param array $where WHERE conditions ['column' => 'value']
 * @return int Number of deleted rows
 * 
 * @example
 * dbDelete('users', ['id' => 5]);
 */
function dbDelete($table, $where) {
    $whereClause = [];
    $values = [];
    
    foreach ($where as $column => $value) {
        $whereClause[] = "$column = ?";
        $values[] = $value;
    }
    
    $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereClause);
    $stmt = dbQuery($sql, $values);
    return $stmt->rowCount();
}

/**
 * Count rows in table
 * 
 * @param string $table Table name
 * @param array $where WHERE conditions ['column' => 'value']
 * @return int Number of rows
 * 
 * @example
 * $count = dbCount('blog_posts', ['status' => 'published']);
 */
function dbCount($table, $where = []) {
    $sql = "SELECT COUNT(*) as count FROM $table";
    $values = [];
    
    if (!empty($where)) {
        $whereClause = [];
        foreach ($where as $column => $value) {
            $whereClause[] = "$column = ?";
            $values[] = $value;
        }
        $sql .= " WHERE " . implode(' AND ', $whereClause);
    }
    
    $result = dbFetchOne($sql, $values);
    return (int) $result['count'];
}

// ============================================================
// SECTION 3: FORM VALIDATION
// ============================================================

/**
 * Validate required field
 * 
 * @param mixed $value Field value
 * @param string $fieldName Field name for error message
 * @return string|null Error message or null if valid
 */
function validateRequired($value, $fieldName) {
    if (empty(trim($value))) {
        return t("validation.required", "$fieldName is required");
    }
    return null;
}

/**
 * Validate string length
 * 
 * @param string $value Field value
 * @param int $min Minimum length
 * @param int $max Maximum length
 * @param string $fieldName Field name for error message
 * @return string|null Error message or null if valid
 */
function validateLength($value, $min, $max, $fieldName) {
    $length = mb_strlen($value, 'UTF-8');
    
    if ($length < $min) {
        return t("validation.min_length", "$fieldName must be at least $min characters");
    }
    
    if ($length > $max) {
        return t("validation.max_length", "$fieldName must not exceed $max characters");
    }
    
    return null;
}

/**
 * Validate phone number (Saudi format)
 * 
 * @param string $phone Phone number
 * @return bool True if valid
 * 
 * @example
 * if (!validatePhone($phone)) {
 *     $errors[] = 'Invalid phone number';
 * }
 */
function validatePhone($phone) {
    // Remove spaces and special characters
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Saudi phone patterns: +966XXXXXXXXX or 05XXXXXXXX or 5XXXXXXXX
    $patterns = [
        '/^\+966[0-9]{9}$/',     // +966501234567
        '/^966[0-9]{9}$/',       // 966501234567
        '/^05[0-9]{8}$/',        // 0501234567
        '/^5[0-9]{8}$/'          // 501234567
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $phone)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Validate contact form data
 * 
 * @param array $data Form data
 * @return array Array of errors (empty if valid)
 * 
 * @example
 * $errors = validateContactForm($_POST);
 * if (empty($errors)) {
 *     // Process form
 * }
 */
function validateContactForm($data) {
    $errors = [];
    
    // Validate name
    if ($error = validateRequired($data['name'] ?? '', 'Name')) {
        $errors['name'] = $error;
    } elseif ($error = validateLength($data['name'], 2, 100, 'Name')) {
        $errors['name'] = $error;
    }
    
    // Validate email
    if ($error = validateRequired($data['email'] ?? '', 'Email')) {
        $errors['email'] = $error;
    } elseif (!isValidEmail($data['email'])) {
        $errors['email'] = t('validation.invalid_email', 'Invalid email address');
    }
    
    // Validate phone (optional but must be valid if provided)
    if (!empty($data['phone']) && !validatePhone($data['phone'])) {
        $errors['phone'] = t('validation.invalid_phone', 'Invalid phone number');
    }
    
    // Validate subject
    if ($error = validateRequired($data['subject'] ?? '', 'Subject')) {
        $errors['subject'] = $error;
    } elseif ($error = validateLength($data['subject'], 5, 200, 'Subject')) {
        $errors['subject'] = $error;
    }
    
    // Validate message
    if ($error = validateRequired($data['message'] ?? '', 'Message')) {
        $errors['message'] = $error;
    } elseif ($error = validateLength($data['message'], 10, 2000, 'Message')) {
        $errors['message'] = $error;
    }
    
    // Validate CSRF token
    if (!verifyCsrfToken($data['csrf_token'] ?? '')) {
        $errors['csrf'] = t('validation.csrf_failed', 'Security validation failed');
    }
    
    return $errors;
}

// ============================================================
// SECTION 4: CONTACT FORM PROCESSING
// ============================================================

/**
 * Process contact form submission
 * 
 * @param array $data Form data
 * @return bool True on success, false on failure
 * 
 * @example
 * if (processContactForm($_POST)) {
 *     setFlashMessage('success', 'Thank you! We will contact you soon.');
 *     redirect('/contact.php');
 * }
 */
function processContactForm($data) {
    // Validate form
    $errors = validateContactForm($data);
    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $data;
        return false;
    }
    
    // Sanitize data
    $cleanData = [
        'name' => sanitize($data['name']),
        'email' => sanitize($data['email']),
        'phone' => sanitize($data['phone'] ?? ''),
        'subject' => sanitize($data['subject']),
        'message' => sanitize($data['message']),
        'service_interest' => sanitize($data['service_interest'] ?? ''),
        'preferred_language' => getCurrentLang(),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    // Save to database
    try {
        $id = dbInsert('contact_submissions', $cleanData);
        
        // Send notification email to admin
        sendContactNotificationEmail($cleanData);
        
        // Send confirmation email to user
        sendContactConfirmationEmail($cleanData);
        
        // Log activity
        logActivity('contact_form_submitted', 'contact_submissions', $id);
        
        return true;
    } catch (Exception $e) {
        logError('Contact form submission failed', [
            'error' => $e->getMessage(),
            'data' => $cleanData
        ]);
        return false;
    }
}

/**
 * Send contact notification email to admin
 * 
 * @param array $data Contact form data
 * @return bool True if email sent successfully
 */
function sendContactNotificationEmail($data) {
    $lang = $data['preferred_language'];
    $subject = t('email.contact.admin_subject', 'New Contact Form Submission');
    
    $message = emailTemplate('contact-notification', [
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'subject' => $data['subject'],
        'message' => nl2br($data['message']),
        'service_interest' => $data['service_interest'],
        'date' => formatDate(date('Y-m-d H:i:s'))
    ]);
    
    return sendEmail(
        CONTACT_EMAIL,
        $subject,
        $message,
        $data['email']
    );
}

/**
 * Send contact confirmation email to user
 * 
 * @param array $data Contact form data
 * @return bool True if email sent successfully
 */
function sendContactConfirmationEmail($data) {
    $lang = $data['preferred_language'];
    $subject = t('email.contact.user_subject', 'Thank you for contacting us');
    
    $message = emailTemplate('contact-confirmation', [
        'name' => $data['name'],
        'company_name' => SITE_NAME
    ]);
    
    return sendEmail(
        $data['email'],
        $subject,
        $message,
        CONTACT_EMAIL
    );
}

/**
 * Get contact submissions (for admin)
 * 
 * @param int $limit Number of records to fetch
 * @param int $offset Starting offset
 * @param string $status Filter by status: 'new', 'read', 'replied', 'closed'
 * @return array Array of contact submissions
 */
function getContactSubmissions($limit = 50, $offset = 0, $status = null) {
    $sql = "SELECT * FROM contact_submissions";
    $params = [];
    
    if ($status) {
        $sql .= " WHERE status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return dbFetchAll($sql, $params);
}

/**
 * Mark contact submission as read
 * 
 * @param int $id Submission ID
 * @return bool True on success
 */
function markContactAsRead($id) {
    return dbUpdate('contact_submissions', 
        ['status' => 'read'], 
        ['id' => $id]
    ) > 0;
}

// ============================================================
// SECTION 5: BLOG FUNCTIONS
// ============================================================

/**
 * Get all blog posts with pagination
 * 
 * @param int $limit Posts per page
 * @param int $offset Starting offset
 * @param string $status Filter by status: 'published', 'draft', 'archived'
 * @return array Array of blog posts
 * 
 * @example
 * $posts = getAllBlogPosts(9, 0, 'published');
 */
function getAllBlogPosts($limit = 10, $offset = 0, $status = 'published') {
    $lang = getCurrentLang();
    $titleCol = "title_$lang";
    $excerptCol = "excerpt_$lang";
    
    $sql = "SELECT id, slug, $titleCol as title, $excerptCol as excerpt, 
            featured_image, category, views, published_at, created_at
            FROM blog_posts 
            WHERE status = ? 
            ORDER BY published_at DESC 
            LIMIT ? OFFSET ?";
    
    return dbFetchAll($sql, [$status, $limit, $offset]);
}

/**
 * Get single blog post by slug
 * 
 * @param string $slug Post slug
 * @return array|null Post data or null if not found
 */
function getBlogPostBySlug($slug) {
    $lang = getCurrentLang();
    $titleCol = "title_$lang";
    $excerptCol = "excerpt_$lang";
    $contentCol = "content_$lang";
    
    $sql = "SELECT id, slug, $titleCol as title, $excerptCol as excerpt, 
            $contentCol as content, featured_image, category, tags, 
            views, published_at, created_at, updated_at
            FROM blog_posts 
            WHERE slug = ? AND status = 'published'";
    
    $post = dbFetchOne($sql, [$slug]);
    
    // Increment view count
    if ($post) {
        dbQuery("UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post['id']]);
    }
    
    return $post;
}

/**
 * Get recent blog posts
 * 
 * @param int $limit Number of posts
 * @return array Array of recent posts
 */
function getRecentBlogPosts($limit = 5) {
    return getAllBlogPosts($limit, 0, 'published');
}

/**
 * Get blog posts by category
 * 
 * @param string $category Category name
 * @param int $limit Number of posts
 * @param int $offset Starting offset
 * @return array Array of posts
 */
function getBlogPostsByCategory($category, $limit = 10, $offset = 0) {
    $lang = getCurrentLang();
    $titleCol = "title_$lang";
    $excerptCol = "excerpt_$lang";
    
    $sql = "SELECT id, slug, $titleCol as title, $excerptCol as excerpt, 
            featured_image, category, views, published_at
            FROM blog_posts 
            WHERE status = 'published' AND category = ? 
            ORDER BY published_at DESC 
            LIMIT ? OFFSET ?";
    
    return dbFetchAll($sql, [$category, $limit, $offset]);
}

/**
 * Get total count of published posts
 * 
 * @param string $category Optional category filter
 * @return int Total post count
 */
function getBlogPostCount($category = null) {
    if ($category) {
        return dbCount('blog_posts', ['status' => 'published', 'category' => $category]);
    }
    return dbCount('blog_posts', ['status' => 'published']);
}

/**
 * Search blog posts
 * 
 * @param string $query Search query
 * @param int $limit Number of results
 * @return array Array of matching posts
 */
function searchBlogPosts($query, $limit = 20) {
    $lang = getCurrentLang();
    $titleCol = "title_$lang";
    $excerptCol = "excerpt_$lang";
    $contentCol = "content_$lang";
    
    $searchTerm = "%$query%";
    
    $sql = "SELECT id, slug, $titleCol as title, $excerptCol as excerpt, 
            featured_image, category, published_at
            FROM blog_posts 
            WHERE status = 'published' 
            AND ($titleCol LIKE ? OR $excerptCol LIKE ? OR $contentCol LIKE ?)
            ORDER BY published_at DESC 
            LIMIT ?";
    
    return dbFetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
}

// ============================================================
// SECTION 6: TESTIMONIALS
// ============================================================

/**
 * Get all active testimonials
 * 
 * @param int $limit Number of testimonials
 * @param bool $featured Only featured testimonials
 * @return array Array of testimonials
 */
function getTestimonials($limit = 10, $featured = false) {
    $lang = getCurrentLang();
    $nameCol = "client_name_$lang";
    $positionCol = "client_position_$lang";
    $testimonialCol = "testimonial_$lang";
    
    $sql = "SELECT id, $nameCol as name, $positionCol as position, 
            $testimonialCol as testimonial, client_photo, rating, service_category
            FROM testimonials 
            WHERE status = 'active'";
    
    if ($featured) {
        $sql .= " AND featured = 1";
    }
    
    $sql .= " ORDER BY display_order ASC, created_at DESC LIMIT ?";
    
    return dbFetchAll($sql, [$limit]);
}

/**
 * Get random testimonials
 * 
 * @param int $count Number of testimonials to retrieve
 * @return array Array of random testimonials
 */
function getRandomTestimonials($count = 3) {
    $lang = getCurrentLang();
    $nameCol = "client_name_$lang";
    $positionCol = "client_position_$lang";
    $testimonialCol = "testimonial_$lang";
    
    $sql = "SELECT id, $nameCol as name, $positionCol as position, 
            $testimonialCol as testimonial, client_photo, rating
            FROM testimonials 
            WHERE status = 'active' 
            ORDER BY RAND() 
            LIMIT ?";
    
    return dbFetchAll($sql, [$count]);
}

// ============================================================
// SECTION 7: SERVICES
// ============================================================

/**
 * Get all active services
 * 
 * @param bool $featured Only featured services
 * @return array Array of services
 */
function getAllServices($featured = false) {
    $lang = getCurrentLang();
    $titleCol = "title_$lang";
    $descCol = "description_$lang";
    
    $sql = "SELECT id, slug, category, $titleCol as title, 
            $descCol as description, icon, image, display_order
            FROM services 
            WHERE status = 'active'";
    
    if ($featured) {
        $sql .= " AND featured = 1";
    }
    
    $sql .= " ORDER BY display_order ASC";
    
    return dbFetchAll($sql);
}

/**
 * Get service by slug
 * 
 * @param string $slug Service slug
 * @return array|null Service data or null
 */
function getServiceBySlug($slug) {
    $lang = getCurrentLang();
    $titleCol = "title_$lang";
    $descCol = "description_$lang";
    $contentCol = "content_$lang";
    $metaTitleCol = "meta_title_$lang";
    $metaDescCol = "meta_description_$lang";
    
    $sql = "SELECT id, slug, category, $titleCol as title, 
            $descCol as description, $contentCol as content,
            $metaTitleCol as meta_title, $metaDescCol as meta_description,
            icon, image
            FROM services 
            WHERE slug = ? AND status = 'active'";
    
    return dbFetchOne($sql, [$slug]);
}

/**
 * Get services grouped by category
 * 
 * @return array Associative array of category => services
 */
function getServicesByCategory() {
    $services = getAllServices();
    $grouped = [];
    
    foreach ($services as $service) {
        $category = $service['category'];
        if (!isset($grouped[$category])) {
            $grouped[$category] = [];
        }
        $grouped[$category][] = $service;
    }
    
    return $grouped;
}

// ============================================================
// SECTION 8: SUCCESS STORIES
// ============================================================

/**
 * Get all success stories
 * 
 * @param int $limit Number of stories
 * @param int $offset Starting offset
 * @param bool $featured Only featured stories
 * @return array Array of success stories
 */
function getSuccessStories($limit = 10, $offset = 0, $featured = false) {
    $lang = getCurrentLang();
    $titleCol = "title_$lang";
    $descCol = "description_$lang";
    $clientCol = "client_name_$lang";
    
    $sql = "SELECT id, slug, $clientCol as client_name, client_type,
            $titleCol as title, $descCol as description, 
            image, service_category, project_date
            FROM success_stories 
            WHERE status = 'active'";
    
    if ($featured) {
        $sql .= " AND featured = 1";
    }
    
    $sql .= " ORDER BY display_order ASC, project_date DESC LIMIT ? OFFSET ?";
    
    return dbFetchAll($sql, [$limit, $offset]);
}

/**
 * Get success story by slug
 * 
 * @param string $slug Story slug
 * @return array|null Story data or null
 */
function getSuccessStoryBySlug($slug) {
    $lang = getCurrentLang();
    $titleCol = "title_$lang";
    $descCol = "description_$lang";
    $contentCol = "content_$lang";
    $clientCol = "client_name_$lang";
    
    $sql = "SELECT id, slug, $clientCol as client_name, client_type,
            $titleCol as title, $descCol as description, 
            $contentCol as content, image, service_category, project_date
            FROM success_stories 
            WHERE slug = ? AND status = 'active'";
    
    return dbFetchOne($sql, [$slug]);
}

// ============================================================
// SECTION 9: IMAGE HANDLING
// ============================================================

/**
 * Get full image URL
 * 
 * @param string $filename Image filename
 * @param string $folder Subfolder (optional)
 * @return string Full image URL
 * 
 * @example
 * echo getImageUrl('logo.png'); // https://site.com/assets/images/logo.png
 * echo getImageUrl('hero.jpg', 'banners'); // .../images/banners/hero.jpg
 */
function getImageUrl($filename, $folder = '') {
    if (empty($filename)) {
        return ASSETS_URL . '/images/placeholder.jpg';
    }
    
    $path = $folder ? "images/$folder/$filename" : "images/$filename";
    return ASSETS_URL . '/' . $path;
}

/**
 * Check if image file exists
 * 
 * @param string $filename Image filename
 * @param string $folder Subfolder (optional)
 * @return bool True if image exists
 */
function imageExists($filename, $folder = '') {
    $path = $folder ? "images/$folder/$filename" : "images/$filename";
    return file_exists(ROOT_PATH . "/assets/$path");
}

/**
 * Generate responsive image HTML
 * 
 * @param string $filename Image filename
 * @param string $alt Alt text
 * @param string $class CSS classes
 * @param bool $lazy Enable lazy loading
 * @return string HTML img tag
 * 
 * @example
 * echo generateResponsiveImage('hero.jpg', 'Hero Image', 'img-fluid');
 */
function generateResponsiveImage($filename, $alt = '', $class = '', $lazy = true) {
    $url = getImageUrl($filename);
    $lazyAttr = $lazy ? 'loading="lazy"' : '';
    $alt = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');
    
    return "<img src=\"$url\" alt=\"$alt\" class=\"$class\" $lazyAttr>";
}

/**
 * Get server path to image
 * 
 * @param string $filename Image filename
 * @param string $folder Subfolder (optional)
 * @return string Server file path
 */
function getImagePath($filename, $folder = '') {
    $path = $folder ? "images/$folder/$filename" : "images/$filename";
    return ROOT_PATH . "/assets/$path";
}

// ============================================================
// SECTION 10: URL & ROUTING FUNCTIONS
// ============================================================

/**
 * Get asset URL (CSS, JS, images)
 * 
 * @param string $path Asset path relative to assets folder
 * @return string Complete asset URL
 * 
 * @example
 * echo assetUrl('css/style.css'); // https://site.com/assets/css/style.css
 */
function assetUrl($path) {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Get current page name
 * 
 * @return string Current page filename without extension
 * 
 * @example
 * $page = currentPage(); // Returns 'index', 'about', 'contact', etc.
 */
function currentPage() {
    return basename($_SERVER['PHP_SELF'], '.php');
}

/**
 * Generate breadcrumb navigation
 * 
 * @param array $items Array of ['title' => 'Title', 'url' => 'url'] or just title strings
 * @return string HTML breadcrumb
 * 
 * @example
 * echo generateBreadcrumb([
 *     ['title' => t('home'), 'url' => '/'],
 *     ['title' => t('blog'), 'url' => '/blog.php'],
 *     'Post Title' // Last item (no URL)
 * ]);
 */
function generateBreadcrumb($items) {
    if (empty($items)) {
        return '';
    }
    
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    $count = count($items);
    
    foreach ($items as $index => $item) {
        $isLast = ($index === $count - 1);
        
        if (is_string($item)) {
            $html .= '<li class="breadcrumb-item' . ($isLast ? ' active' : '') . '">';
            $html .= htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
            $html .= '</li>';
        } else {
            $html .= '<li class="breadcrumb-item' . ($isLast ? ' active' : '') . '">';
            if (!$isLast && isset($item['url'])) {
                $html .= '<a href="' . $item['url'] . '">';
            }
            $html .= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
            if (!$isLast && isset($item['url'])) {
                $html .= '</a>';
            }
            $html .= '</li>';
        }
    }
    
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Convert text to URL-friendly slug
 * 
 * @param string $text Text to slugify
 * @param string $lang Language (ar/en)
 * @return string URL-safe slug
 * 
 * @example
 * echo slugify('Hello World'); // hello-world
 * echo slugify('مرحبا بكم', 'ar'); // مرحبا-بكم (URL encoded)
 */
function slugify($text, $lang = 'en') {
    // Convert to lowercase
    $text = mb_strtolower($text, 'UTF-8');
    
    if ($lang === 'ar') {
        // For Arabic, just replace spaces with hyphens
        $text = preg_replace('/\s+/', '-', $text);
    } else {
        // For English, remove special characters
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
    }
    
    return trim($text, '-');
}

// ============================================================
// SECTION 11: PAGINATION
// ============================================================

/**
 * Calculate pagination data
 * 
 * @param int $total Total items
 * @param int $perPage Items per page
 * @param int $currentPage Current page number
 * @return array Pagination data
 * 
 * @example
 * $pagination = paginate($totalPosts, 9, $_GET['page'] ?? 1);
 * // Returns: ['total_pages' => 5, 'offset' => 0, 'has_prev' => false, 'has_next' => true]
 */
function paginate($total, $perPage, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_page' => max(1, $currentPage - 1),
        'next_page' => min($totalPages, $currentPage + 1)
    ];
}

/**
 * Generate pagination HTML
 * 
 * @param array $pagination Pagination data from paginate()
 * @param string $baseUrl Base URL for links
 * @return string HTML pagination links
 */
function generatePaginationLinks($pagination, $baseUrl) {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="' . t('pagination', 'Pagination') . '"><ul class="pagination justify-content-center">';
    
    // Previous button
    $prevDisabled = !$pagination['has_prev'] ? ' disabled' : '';
    $html .= '<li class="page-item' . $prevDisabled . '">';
    if ($pagination['has_prev']) {
        $html .= '<a class="page-link" href="' . $baseUrl . '&page=' . $pagination['prev_page'] . '">';
    } else {
        $html .= '<span class="page-link">';
    }
    $html .= t('previous', 'Previous');
    $html .= $pagination['has_prev'] ? '</a>' : '</span>';
    $html .= '</li>';
    
    // Page numbers
    $start = max(1, $pagination['current_page'] - 2);
    $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $pagination['current_page'] ? ' active' : '';
        $html .= '<li class="page-item' . $active . '">';
        $html .= '<a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a>';
        $html .= '</li>';
    }
    
    // Next button
    $nextDisabled = !$pagination['has_next'] ? ' disabled' : '';
    $html .= '<li class="page-item' . $nextDisabled . '">';
    if ($pagination['has_next']) {
        $html .= '<a class="page-link" href="' . $baseUrl . '&page=' . $pagination['next_page'] . '">';
    } else {
        $html .= '<span class="page-link">';
    }
    $html .= t('next', 'Next');
    $html .= $pagination['has_next'] ? '</a>' : '</span>';
    $html .= '</li>';
    
    $html .= '</ul></nav>';
    return $html;
}

// ============================================================
// SECTION 12: EMAIL TEMPLATES
// ============================================================

/**
 * Load and render email template
 * 
 * @param string $template Template name
 * @param array $data Data to pass to template
 * @return string Rendered HTML email
 */
function emailTemplate($template, $data = []) {
    $lang = getCurrentLang();
    $templates = [
        'contact-notification' => '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #602234;">New Contact Form Submission</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Name:</strong></td><td style="padding: 10px;">' . $data['name'] . '</td></tr>
                    <tr><td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Email:</strong></td><td style="padding: 10px;">' . $data['email'] . '</td></tr>
                    <tr><td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Phone:</strong></td><td style="padding: 10px;">' . $data['phone'] . '</td></tr>
                    <tr><td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Subject:</strong></td><td style="padding: 10px;">' . $data['subject'] . '</td></tr>
                    <tr><td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Service Interest:</strong></td><td style="padding: 10px;">' . $data['service_interest'] . '</td></tr>
                    <tr><td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Message:</strong></td><td style="padding: 10px;">' . $data['message'] . '</td></tr>
                    <tr><td style="padding: 10px;"><strong>Date:</strong></td><td style="padding: 10px;">' . $data['date'] . '</td></tr>
                </table>
            </div>
        ',
        
        'contact-confirmation' => '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #602234;">' . $data['company_name'] . '</h1>
                </div>
                <h2 style="color: #602234;">' . t('email.greeting', 'Hello') . ' ' . $data['name'] . ',</h2>
                <p>' . t('email.contact.confirmation_message', 'Thank you for contacting us. We have received your message and will get back to you as soon as possible.') . '</p>
                <p>' . t('email.contact.confirmation_note', 'Our team typically responds within 24-48 hours.') . '</p>
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666;">
                    <p style="font-size: 12px;">' . $data['company_name'] . '<br>' . CONTACT_EMAIL . '</p>
                </div>
            </div>
        '
    ];
    
    return $templates[$template] ?? '';
}

// ============================================================
// SECTION 13: LOGGING & DEBUGGING
// ============================================================

/**
 * Log error to file
 * 
 * @param string $message Error message
 * @param array $context Additional context data
 * @return void
 */
function logError($message, $context = []) {
    if (!defined('LOG_PATH')) {
        return;
    }
    
    $logFile = ROOT_PATH . '/logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? json_encode($context) : '';
    
    $logMessage = "[$timestamp] ERROR: $message $contextStr\n";
    
    error_log($logMessage, 3, $logFile);
}

/**
 * Log user activity
 * 
 * @param string $action Action performed
 * @param string $entityType Entity type (e.g., 'blog_post', 'user')
 * @param int $entityId Entity ID
 * @param string $description Optional description
 * @return void
 */
function logActivity($action, $entityType = null, $entityId = null, $description = '') {
    try {
        dbInsert('activity_log', [
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (Exception $e) {
        // Silently fail - don't break app if logging fails
    }
}

/**
 * Debug dump (only in development mode)
 * 
 * @param mixed $data Data to dump
 * @param bool $die Whether to die after dump
 * @return void
 */
function debugDump($data, $die = false) {
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        return;
    }
    
    echo '<pre style="background: #f4f4f4; padding: 15px; border: 1px solid #ddd; margin: 10px;">';
    var_dump($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * Check if debug mode is enabled
 * 
 * @return bool True if debug mode is on
 */
function isDebugMode() {
    return defined('DEBUG_MODE') && DEBUG_MODE === true;
}

// ============================================================
// SECTION 14: SECURITY HELPERS
// ============================================================

/**
 * Hash password (for admin users)
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify hashed password
 * 
 * @param string $password Plain text password
 * @param string $hash Hashed password
 * @return bool True if password matches
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random token
 * 
 * @param int $length Token length
 * @return string Random token
 */
function generateRandomToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Simple rate limiting
 * 
 * @param string $key Unique key (e.g., 'contact_form_' . $ip)
 * @param int $maxAttempts Maximum attempts
 * @param int $period Time period in seconds
 * @return bool True if rate limit exceeded
 */
function isRateLimited($key, $maxAttempts = 5, $period = 300) {
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    
    $now = time();
    
    // Clean old entries
    $_SESSION['rate_limit'] = array_filter($_SESSION['rate_limit'], function($data) use ($now, $period) {
        return $now - $data['time'] < $period;
    });
    
    // Check attempts
    if (isset($_SESSION['rate_limit'][$key])) {
        $data = $_SESSION['rate_limit'][$key];
        if ($now - $data['time'] < $period && $data['count'] >= $maxAttempts) {
            return true;
        }
    }
    
    // Log attempt
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'time' => $now];
    } else {
        $_SESSION['rate_limit'][$key]['count']++;
    }
    
    return false;
}

/**
 * Additional XSS protection
 * 
 * @param string $input User input
 * @return string Cleaned input
 */
function preventXSS($input) {
    // Strip tags but allow safe HTML
    $allowedTags = '<p><br><strong><em><ul><ol><li>';
    return strip_tags($input, $allowedTags);
}

// ============================================================
// SECTION 15: SITE SETTINGS
// ============================================================

/**
 * Get site setting value
 * 
 * @param string $key Setting key
 * @param mixed $default Default value if not found
 * @return mixed Setting value
 */
function getSetting($key, $default = null) {
    static $settings = null;
    
    if ($settings === null) {
        $settings = [];
        $results = dbFetchAll("SELECT setting_key, setting_value FROM site_settings");
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Update site setting
 * 
 * @param string $key Setting key
 * @param mixed $value Setting value
 * @return bool True on success
 */
function updateSetting($key, $value) {
    try {
        $exists = dbFetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
        
        if ($exists) {
            dbUpdate('site_settings', ['setting_value' => $value], ['setting_key' => $key]);
        } else {
            dbInsert('site_settings', ['setting_key' => $key, 'setting_value' => $value]);
        }
        
        return true;
    } catch (Exception $e) {
        logError('Failed to update setting', ['key' => $key, 'error' => $e->getMessage()]);
        return false;
    }
}

// ============================================================
// END OF HELPER FUNCTIONS
// ============================================================
