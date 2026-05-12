<?php
/**
 * Auto-create database tables when blog_posts is missing.
 * Uses inline DDL so it works regardless of schema file path/splitting.
 */
if (!isset($pdo)) return;
$blogTableExists = false;
try {
    $r = @$pdo->query("SHOW TABLES LIKE 'blog_posts'");
    $blogTableExists = ($r && $r->rowCount() > 0);
    if ($blogTableExists) {
        $count = (int)$pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
        if ($count > 0) return;
    }
} catch (Throwable $e) { /* continue */ }

if (!$blogTableExists) {
// 1. Users (required by blog_posts FK)
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer',
    phone VARCHAR(20),
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("INSERT IGNORE INTO users (id, username, email, password_hash, first_name, last_name, role) VALUES
(1, 'admin', 'admin@niche-society.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin')");

// 2. Blog posts
$pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_id INT UNSIGNED,
    slug VARCHAR(200) UNIQUE NOT NULL,
    title_ar VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    excerpt_ar TEXT,
    excerpt_en TEXT,
    content_ar LONGTEXT NOT NULL,
    content_en LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category VARCHAR(100),
    tags VARCHAR(255),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    views INT DEFAULT 0,
    meta_title_ar VARCHAR(200),
    meta_title_en VARCHAR(200),
    meta_description_ar TEXT,
    meta_description_en TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_published_at (published_at),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 3. Success stories (for success-stories.php)
$pdo->exec("CREATE TABLE IF NOT EXISTS success_stories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(200) UNIQUE NOT NULL,
    client_name_ar VARCHAR(200),
    client_name_en VARCHAR(200),
    client_type ENUM('royal', 'government', 'corporate', 'individual') NOT NULL,
    title_ar VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    description_ar TEXT NOT NULL,
    description_en TEXT NOT NULL,
    content_ar LONGTEXT,
    content_en LONGTEXT,
    image VARCHAR(255),
    service_category VARCHAR(100),
    project_date DATE,
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_client_type (client_type),
    INDEX idx_featured (featured),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 4. Contact submissions (for contact form)
$pdo->exec("CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    service_interest VARCHAR(100),
    preferred_language ENUM('ar', 'en') DEFAULT 'ar',
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    admin_notes TEXT,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

} // end if (!$blogTableExists)

// Seed blog when empty (so blog always shows something)
try {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
    if ($count > 0) return;
} catch (Throwable $e) { return; }

$seeds = [
    ['slug' => 'luxury-property-management-middle-east', 'title_en' => 'Luxury property and estate management in the Middle East', 'title_ar' => 'إدارة العقارات والممتلكات الفاخرة في الشرق الأوسط', 'excerpt_en' => 'Trends in high-end property management and hospitality across the Gulf.', 'excerpt_ar' => 'اتجاهات الإدارة الفاخرة للعقارات والضيافة في الخليج.', 'category' => 'Estate Management', 'tags' => 'region:middle_east'],
    ['slug' => 'event-management-dubai-riyadh', 'title_en' => 'Event management and protocol in Dubai and Riyadh', 'title_ar' => 'إدارة الفعاليات والبروتوكول في دبي والرياض', 'excerpt_en' => 'How top-tier event and protocol services shape major occasions in the region.', 'excerpt_ar' => 'كيف تشكل خدمات الفعاليات والبروتوكول الفعاليات الكبرى في المنطقة.', 'category' => 'Event Management', 'tags' => 'region:middle_east'],
    ['slug' => 'concierge-vip-services-gcc', 'title_en' => 'VIP concierge and high-end services in the GCC', 'title_ar' => 'الكونسيرج والخدمات الفاخرة في دول الخليج', 'excerpt_en' => 'The rise of concierge and white-glove services for discerning clients.', 'excerpt_ar' => 'صعود خدمات الكونسيرج والخدمات المميزة للعملاء المميزين.', 'category' => 'Logistics', 'tags' => 'region:middle_east'],
    ['slug' => 'hospitality-management-saudi-uae', 'title_en' => 'Hospitality and household management in Saudi Arabia and UAE', 'title_ar' => 'الضيافة وإدارة المنازل في السعودية والإمارات', 'excerpt_en' => 'Best practices in luxury hospitality and residential management.', 'excerpt_ar' => 'أفضل الممارسات في الضيافة الفاخرة والإدارة السكنية.', 'category' => 'Hospitality', 'tags' => 'region:middle_east'],
    ['slug' => 'protocol-etiquette-training-region', 'title_en' => 'Protocol and etiquette training in the region', 'title_ar' => 'تدريب البروتوكول والإتيكيت في المنطقة', 'excerpt_en' => 'Why formal protocol and etiquette matter for high-level engagements.', 'excerpt_ar' => 'لماذا يهم البروتوكول والإتيكيت الرسمي في اللقاءات الرفيعة.', 'category' => 'Middle East', 'tags' => 'region:middle_east'],
];
$stmt = $pdo->prepare("INSERT INTO blog_posts (author_id, slug, title_en, title_ar, excerpt_en, excerpt_ar, content_en, content_ar, featured_image, category, tags, status, published_at) VALUES (1, ?, ?, ?, ?, ?, ?, ?, 'assets/images/niche-society-homepage-1-scaled.jpg', ?, ?, 'published', NOW())");
foreach ($seeds as $s) {
    $content = '<p>' . $s['excerpt_en'] . '</p><p>Niche Society delivers luxury management, event management, and protocol services across the Middle East and internationally.</p>';
    $contentAr = '<p>' . $s['excerpt_ar'] . '</p><p>تقدم نيش سوسايتي خدمات الإدارة الفاخرة وإدارة الفعاليات والبروتوكول في الشرق الأوسط وعالمياً.</p>';
    try { $stmt->execute([$s['slug'], $s['title_en'], $s['title_ar'], $s['excerpt_en'], $s['excerpt_ar'], $content, $contentAr, $s['category'], $s['tags']]); } catch (Throwable $e) { /* skip duplicate */ }
}
