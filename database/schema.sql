-- Database Schema for Niche Society Website
-- MySQL 8.x

-- Create database
CREATE DATABASE IF NOT EXISTS niche_society CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE niche_society;

-- ========================================
-- USERS TABLE
-- ========================================
CREATE TABLE users (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: Admin@123)
INSERT INTO users (username, email, password_hash, first_name, last_name, role) VALUES
('admin', 'admin@niche-society.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin');

-- ========================================
-- SERVICES TABLE
-- ========================================
CREATE TABLE services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE NOT NULL,
    category ENUM('household', 'events', 'protocol', 'properties', 'consulting', 'vip') NOT NULL,
    title_ar VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) NOT NULL,
    description_ar TEXT NOT NULL,
    description_en TEXT NOT NULL,
    content_ar LONGTEXT,
    content_en LONGTEXT,
    icon VARCHAR(100),
    image VARCHAR(255),
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    meta_title_ar VARCHAR(200),
    meta_title_en VARCHAR(200),
    meta_description_ar TEXT,
    meta_description_en TEXT,
    meta_keywords_ar VARCHAR(255),
    meta_keywords_en VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_slug (slug),
    INDEX idx_featured (featured),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample services
INSERT INTO services (slug, category, title_ar, title_en, description_ar, description_en, featured, display_order, status) VALUES
('household-management', 'household', 'إدارة الممتلكات', 'Household Management', 'حلول ذكية لإدارة ممتلكاتكم بكفاءة واحترافية عالية', 'Smart solutions to manage your properties efficiently and professionally', TRUE, 1, 'active'),
('event-management', 'events', 'إدارة الفعاليات', 'Event Management', 'تنظيم فعاليات استثنائية من التخطيط إلى التنفيذ', 'Organizing exceptional events from planning to execution', TRUE, 2, 'active'),
('protocol-etiquette', 'protocol', 'البروتوكول والإتيكيت', 'Protocol & Etiquette', 'برامج تدريبية متخصصة في البروتوكول الملكي', 'Specialized training programs in royal protocol', TRUE, 3, 'active'),
('property-services', 'properties', 'خدمات الممتلكات', 'Property Services', 'إدارة شاملة للممتلكات تشمل الصيانة والتطوير', 'Comprehensive property management including maintenance', TRUE, 4, 'active'),
('consulting', 'consulting', 'الاستشارات', 'Consulting Services', 'استشارات متخصصة في إدارة الأعمال والعائلات', 'Specialized consultations in business management', TRUE, 5, 'active'),
('vip-services', 'vip', 'خدمات VIP', 'VIP Services', 'خدمات حصرية للشخصيات الرفيعة', 'Exclusive services for distinguished individuals', TRUE, 6, 'active');

-- ========================================
-- BLOG POSTS TABLE
-- ========================================
CREATE TABLE blog_posts (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- SUCCESS STORIES TABLE
-- ========================================
CREATE TABLE success_stories (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- CONTACT SUBMISSIONS TABLE
-- ========================================
CREATE TABLE contact_submissions (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TESTIMONIALS TABLE
-- ========================================
CREATE TABLE testimonials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_name_ar VARCHAR(200) NOT NULL,
    client_name_en VARCHAR(200) NOT NULL,
    client_position_ar VARCHAR(200),
    client_position_en VARCHAR(200),
    client_photo VARCHAR(255),
    testimonial_ar TEXT NOT NULL,
    testimonial_en TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    service_category VARCHAR(100),
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_featured (featured),
    INDEX idx_status (status),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- MEDIA LIBRARY TABLE
-- ========================================
CREATE TABLE media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    width INT,
    height INT,
    alt_text_ar VARCHAR(255),
    alt_text_en VARCHAR(255),
    caption_ar TEXT,
    caption_en TEXT,
    uploaded_by INT UNSIGNED,
    folder VARCHAR(100) DEFAULT 'general',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_file_type (file_type),
    INDEX idx_folder (folder),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- SITE SETTINGS TABLE
-- ========================================
CREATE TABLE site_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'number', 'boolean', 'json') DEFAULT 'text',
    category VARCHAR(50) DEFAULT 'general',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, category, description) VALUES
('site_name_ar', 'نيش سوسايتي', 'text', 'general', 'Site name in Arabic'),
('site_name_en', 'Niche Society', 'text', 'general', 'Site name in English'),
('site_email', 'info@niche-society.com', 'text', 'contact', 'Main contact email'),
('site_phone', '+966 XX XXX XXXX', 'text', 'contact', 'Main contact phone'),
('site_address_ar', 'الرياض، المملكة العربية السعودية', 'text', 'contact', 'Address in Arabic'),
('site_address_en', 'Riyadh, Saudi Arabia', 'text', 'contact', 'Address in English'),
('facebook_url', 'https://facebook.com/nichesociety', 'text', 'social', 'Facebook page URL'),
('twitter_url', 'https://twitter.com/nichesociety', 'text', 'social', 'Twitter profile URL'),
('instagram_url', 'https://instagram.com/nichesociety', 'text', 'social', 'Instagram profile URL'),
('linkedin_url', 'https://linkedin.com/company/nichesociety', 'text', 'social', 'LinkedIn company URL'),
('iso_certificate', '25EQQN01', 'text', 'company', 'ISO certificate number'),
('iso_valid_until', '2028-11-04', 'text', 'company', 'ISO certificate validity date');

-- ========================================
-- PAGE VIEWS TRACKING TABLE
-- ========================================
CREATE TABLE page_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_url VARCHAR(500) NOT NULL,
    page_title VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    referrer VARCHAR(500),
    session_id VARCHAR(100),
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_page_url (page_url(255)),
    INDEX idx_ip_address (ip_address),
    INDEX idx_viewed_at (viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- NEWSLETTER SUBSCRIBERS TABLE
-- ========================================
CREATE TABLE newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    preferred_language ENUM('ar', 'en') DEFAULT 'ar',
    status ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    verification_token VARCHAR(100),
    verified_at TIMESTAMP NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- ACTIVITY LOG TABLE
-- ========================================
CREATE TABLE activity_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT UNSIGNED,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- CREATE VIEWS FOR REPORTING
-- ========================================

-- View: Active Services Count by Category
CREATE VIEW v_services_by_category AS
SELECT 
    category,
    COUNT(*) as total_services,
    SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featured_services
FROM services
WHERE status = 'active'
GROUP BY category;

-- View: Recent Contact Submissions
CREATE VIEW v_recent_contacts AS
SELECT 
    id,
    name,
    email,
    phone,
    subject,
    service_interest,
    status,
    created_at
FROM contact_submissions
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY created_at DESC;

-- View: Popular Blog Posts
CREATE VIEW v_popular_posts AS
SELECT 
    id,
    slug,
    title_en,
    title_ar,
    views,
    published_at
FROM blog_posts
WHERE status = 'published'
ORDER BY views DESC
LIMIT 10;

-- ========================================
-- CREATE STORED PROCEDURES
-- ========================================

DELIMITER //

-- Procedure: Log User Activity
CREATE PROCEDURE sp_log_activity(
    IN p_user_id INT,
    IN p_action VARCHAR(100),
    IN p_entity_type VARCHAR(50),
    IN p_entity_id INT,
    IN p_description TEXT,
    IN p_ip_address VARCHAR(45)
)
BEGIN
    INSERT INTO activity_log (user_id, action, entity_type, entity_id, description, ip_address)
    VALUES (p_user_id, p_action, p_entity_type, p_entity_id, p_description, p_ip_address);
END //

-- Procedure: Get Service Statistics
CREATE PROCEDURE sp_get_service_stats()
BEGIN
    SELECT 
        category,
        COUNT(*) as total,
        SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featured_count,
        AVG(display_order) as avg_order
    FROM services
    WHERE status = 'active'
    GROUP BY category;
END //

DELIMITER ;

-- ========================================
-- CREATE INDEXES FOR PERFORMANCE
-- ========================================

-- Additional composite indexes for common queries
CREATE INDEX idx_services_category_status ON services(category, status);
CREATE INDEX idx_blog_status_published ON blog_posts(status, published_at);
CREATE INDEX idx_contact_status_created ON contact_submissions(status, created_at);

-- ========================================
-- GRANT PRIVILEGES (Adjust as needed)
-- ========================================

-- Grant all privileges to root user (development environment)
-- In production, create a specific user with limited privileges
-- CREATE USER 'niche_user'@'localhost' IDENTIFIED BY 'secure_password';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON niche_society.* TO 'niche_user'@'localhost';
-- FLUSH PRIVILEGES;
