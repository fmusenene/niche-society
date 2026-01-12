-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2026 at 01:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `niche_society`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_service_stats` ()   BEGIN
    SELECT 
        category,
        COUNT(*) as total,
        SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featured_count,
        AVG(display_order) as avg_order
    FROM services
    WHERE status = 'active'
    GROUP BY category;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_log_activity` (IN `p_user_id` INT, IN `p_action` VARCHAR(100), IN `p_entity_type` VARCHAR(50), IN `p_entity_id` INT, IN `p_description` TEXT, IN `p_ip_address` VARCHAR(45))   BEGIN
    INSERT INTO activity_log (user_id, action, entity_type, entity_id, description, ip_address)
    VALUES (p_user_id, p_action, p_entity_type, p_entity_id, p_description, p_ip_address);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED DEFAULT NULL,
  `slug` varchar(200) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `excerpt_ar` text DEFAULT NULL,
  `excerpt_en` text DEFAULT NULL,
  `content_ar` longtext NOT NULL,
  `content_en` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `meta_title_ar` varchar(200) DEFAULT NULL,
  `meta_title_en` varchar(200) DEFAULT NULL,
  `meta_description_ar` text DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_forms`
--

CREATE TABLE `contact_forms` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `service_interest` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `status` enum('new','read','replied','closed') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_forms`
--

INSERT INTO `contact_forms` (`id`, `name`, `email`, `phone`, `service_interest`, `message`, `ip_address`, `user_agent`, `status`, `created_at`) VALUES
(1, 'Amaru Mugisha', 'fmusenene@gmail.com', '+96650012167', 'household', 'sample email testing sent via website', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'new', '2026-01-11 06:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `service_interest` varchar(100) DEFAULT NULL,
  `preferred_language` enum('ar','en') DEFAULT 'ar',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `status` enum('new','read','replied','closed') DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `alt_text_ar` varchar(255) DEFAULT NULL,
  `alt_text_en` varchar(255) DEFAULT NULL,
  `caption_ar` text DEFAULT NULL,
  `caption_en` text DEFAULT NULL,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `folder` varchar(100) DEFAULT 'general',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `mime_type`, `width`, `height`, `alt_text_ar`, `alt_text_en`, `caption_ar`, `caption_en`, `uploaded_by`, `folder`, `status`, `created_at`, `updated_at`) VALUES
(1, '1.svg', '1.svg', 'assets/images/1.svg', 'svg', 6985, 'image/svg+xml', NULL, NULL, 'أيقونة', 'Icon', NULL, NULL, NULL, 'ui', 'active', '2026-01-04 09:45:10', '2026-01-04 09:45:10'),
(2, 'Niche-Society-Arabic-CP2.png', 'Niche-Society-Arabic-CP2.png', 'assets/images/Niche-Society-Arabic-CP2.png', 'png', 2398717, 'image/png', 2249, 748, 'ملف الشركة', 'Company Profile', NULL, NULL, NULL, 'company', 'active', '2026-01-04 09:45:10', '2026-01-04 09:45:10'),
(3, 'Niche-Society-Arabic-ceo-1-661x1024.png', 'Niche-Society-Arabic-ceo-1-661x1024.png', 'assets/images/Niche-Society-Arabic-ceo-1-661x1024.png', 'png', 486810, 'image/png', 661, 1024, 'المدير التنفيذي', 'CEO', NULL, NULL, NULL, 'company', 'active', '2026-01-04 09:45:10', '2026-01-04 09:45:10'),
(4, 'Niche-Society-mission.png', 'Niche-Society-mission.png', 'assets/images/Niche-Society-mission.png', 'png', 1099518, 'image/png', 1006, 821, 'مهمة نيش سوسايتي', 'Niche Society Mission', NULL, NULL, NULL, 'company', 'active', '2026-01-04 09:45:10', '2026-01-04 09:45:10'),
(5, 'Niche-Society-values.png', 'Niche-Society-values.png', 'assets/images/Niche-Society-values.png', 'png', 1215715, 'image/png', 720, 1096, 'قيم نيش سوسايتي', 'Niche Society Values', NULL, NULL, NULL, 'company', 'active', '2026-01-04 09:45:10', '2026-01-04 09:45:10'),
(6, 'Niche-Society-vison.png', 'Niche-Society-vison.png', 'assets/images/Niche-Society-vison.png', 'png', 1525254, 'image/png', 1006, 820, 'رؤية نيش سوسايتي', 'Niche Society Vision', NULL, NULL, NULL, 'company', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(7, 'TEAM-scaled.jpg', 'TEAM-scaled.jpg', 'assets/images/TEAM-scaled.jpg', 'jpg', 446525, 'image/jpeg', 2560, 1188, 'فريق نيش سوسايتي', 'Niche Society Team', NULL, NULL, NULL, 'company', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(8, 'brand-logo.pdf-3-120x57.png', 'brand-logo.pdf-3-120x57.png', 'assets/images/brand-logo.pdf-3-120x57.png', 'png', 2333, 'image/png', 120, 57, 'شعار العلامة التجارية', 'Brand Logo', NULL, NULL, NULL, 'logo', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(9, 'cropped-brand-logo.pdf-1-180x180.png', 'cropped-brand-logo.pdf-1-180x180.png', 'assets/images/cropped-brand-logo.pdf-1-180x180.png', 'png', 3197, 'image/png', 180, 180, 'صورة', 'Image', NULL, NULL, NULL, 'logo-favicon', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(10, 'cropped-brand-logo.pdf-1-192x192.png', 'cropped-brand-logo.pdf-1-192x192.png', 'assets/images/cropped-brand-logo.pdf-1-192x192.png', 'png', 3301, 'image/png', 192, 192, 'صورة', 'Image', NULL, NULL, NULL, 'logo-favicon', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(11, 'cropped-brand-logo.pdf-1-270x270.png', 'cropped-brand-logo.pdf-1-270x270.png', 'assets/images/cropped-brand-logo.pdf-1-270x270.png', 'png', 4989, 'image/png', 270, 270, 'صورة', 'Image', NULL, NULL, NULL, 'logo-favicon', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(12, 'cropped-brand-logo.pdf-1-32x32.png', 'cropped-brand-logo.pdf-1-32x32.png', 'assets/images/cropped-brand-logo.pdf-1-32x32.png', 'png', 754, 'image/png', 32, 32, 'صورة', 'Image', NULL, NULL, NULL, 'logo-favicon', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(13, 'logo-light.png', 'logo-light.png', 'assets/images/logo-light.png', 'png', 1906, 'image/png', 150, 150, 'شعار نيش سوسايتي - فاتح', 'Niche Society Logo - Light', NULL, NULL, NULL, 'logo', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(14, 'logo.png', 'logo.png', 'assets/images/logo.png', 'png', 2333, 'image/png', 120, 57, 'شعار نيش سوسايتي', 'Niche Society Logo', NULL, NULL, NULL, 'logo', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(15, 'niche-logo-png-150x150.png', 'niche-logo-png-150x150.png', 'assets/images/niche-logo-png-150x150.png', 'png', 1906, 'image/png', 150, 150, 'شعار نيش سوسايتي مربع', 'Niche Society Square Logo', NULL, NULL, NULL, 'logo', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(16, 'niche-society-homepage-1-scaled.jpg', 'niche-society-homepage-1-scaled.jpg', 'assets/images/niche-society-homepage-1-scaled.jpg', 'jpg', 549383, 'image/jpeg', 2560, 1300, 'داخلية فاخرة - خدمات إدارة الممتلكات', 'Luxury Interior - Property Management Services', NULL, NULL, NULL, 'hero', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(17, 'service-2-914x1024.png', 'service-2-914x1024.png', 'assets/images/service-2-914x1024.png', 'png', 661724, 'image/png', 914, 1024, 'إدارة الفعاليات الفاخرة', 'Luxury Event Management', NULL, NULL, NULL, 'services', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(18, 'service-3.jpg', 'service-3.jpg', 'assets/images/service-3.jpg', 'jpg', 287806, 'image/jpeg', 730, 815, 'البروتوكول والإتيكيت', 'Protocol & Etiquette', NULL, NULL, NULL, 'services', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(19, 'service-4.jpg', 'service-4.jpg', 'assets/images/service-4.jpg', 'jpg', 269867, 'image/jpeg', 730, 815, 'خدمات الممتلكات', 'Property Services', NULL, NULL, NULL, 'services', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(20, 'service-5.jpg', 'service-5.jpg', 'assets/images/service-5.jpg', 'jpg', 282639, 'image/jpeg', 730, 815, 'الاستشارات', 'Consulting Services', NULL, NULL, NULL, 'services', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(21, 'service-6.jpg', 'service-6.jpg', 'assets/images/service-6.jpg', 'jpg', 229264, 'image/jpeg', 730, 815, 'خدمات VIP', 'VIP Services', NULL, NULL, NULL, 'services', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(22, 'service.png', 'service.png', 'assets/images/service.png', 'png', 1124849, 'image/png', 730, 815, 'خدمات الإدارة الذكية للممتلكات', 'Smart Household Management Services', NULL, NULL, NULL, 'services', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11'),
(23, 'sunlit-library-escape-701x1024.jpg', 'sunlit-library-escape-701x1024.jpg', 'assets/images/sunlit-library-escape-701x1024.jpg', 'jpg', 217686, 'image/jpeg', 701, 1024, 'مكتبة خاصة أنيقة', 'Elegant Private Library', NULL, NULL, NULL, 'hero', 'active', '2026-01-04 09:45:11', '2026-01-04 09:45:11');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `preferred_language` enum('ar','en') DEFAULT 'ar',
  `status` enum('active','unsubscribed','bounced') DEFAULT 'active',
  `verification_token` varchar(100) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unsubscribed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_views`
--

CREATE TABLE `page_views` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_url` varchar(500) NOT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_logs`
--

INSERT INTO `security_logs` (`id`, `event_type`, `ip_address`, `user_agent`, `details`, `created_at`) VALUES
(1, 'contact_form_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Form submitted successfully', '2026-01-11 06:05:42');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(100) NOT NULL,
  `category` enum('household','events','protocol','properties','consulting','vip') NOT NULL,
  `title_ar` varchar(200) NOT NULL,
  `title_en` varchar(200) NOT NULL,
  `description_ar` text NOT NULL,
  `description_en` text NOT NULL,
  `content_ar` longtext DEFAULT NULL,
  `content_en` longtext DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `meta_title_ar` varchar(200) DEFAULT NULL,
  `meta_title_en` varchar(200) DEFAULT NULL,
  `meta_description_ar` text DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `meta_keywords_ar` varchar(255) DEFAULT NULL,
  `meta_keywords_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `slug`, `category`, `title_ar`, `title_en`, `description_ar`, `description_en`, `content_ar`, `content_en`, `icon`, `image`, `featured`, `display_order`, `status`, `meta_title_ar`, `meta_title_en`, `meta_description_ar`, `meta_description_en`, `meta_keywords_ar`, `meta_keywords_en`, `created_at`, `updated_at`) VALUES
(1, 'household-management', 'household', 'إدارة الممتلكات', 'Household Management', 'حلول ذكية لإدارة ممتلكاتكم بكفاءة واحترافية عالية', 'Smart solutions to manage your properties efficiently and professionally', NULL, NULL, NULL, 'service.png', 1, 1, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-21 19:54:20', '2026-01-04 09:45:11'),
(2, 'event-management', 'events', 'إدارة الفعاليات', 'Event Management', 'تنظيم فعاليات استثنائية من التخطيط إلى التنفيذ', 'Organizing exceptional events from planning to execution', NULL, NULL, NULL, 'service-2-914x1024.png', 1, 2, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-21 19:54:20', '2026-01-04 09:45:11'),
(3, 'protocol-etiquette', 'protocol', 'البروتوكول والإتيكيت', 'Protocol & Etiquette', 'برامج تدريبية متخصصة في البروتوكول الملكي', 'Specialized training programs in royal protocol', NULL, NULL, NULL, 'service-3.jpg', 1, 3, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-21 19:54:20', '2026-01-04 09:45:11'),
(4, 'property-services', 'properties', 'خدمات الممتلكات', 'Property Services', 'إدارة شاملة للممتلكات تشمل الصيانة والتطوير', 'Comprehensive property management including maintenance', NULL, NULL, NULL, 'service-4.jpg', 1, 4, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-21 19:54:20', '2026-01-04 09:45:11'),
(5, 'consulting', 'consulting', 'الاستشارات', 'Consulting Services', 'استشارات متخصصة في إدارة الأعمال والعائلات', 'Specialized consultations in business management', NULL, NULL, NULL, 'service-5.jpg', 1, 5, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-21 19:54:20', '2026-01-04 09:45:11'),
(6, 'vip-services', 'vip', 'خدمات VIP', 'VIP Services', 'خدمات حصرية للشخصيات الرفيعة', 'Exclusive services for distinguished individuals', NULL, NULL, NULL, 'service-6.jpg', 1, 6, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-21 19:54:20', '2026-01-04 09:45:11');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','number','boolean','json') DEFAULT 'text',
  `category` varchar(50) DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `category`, `description`, `updated_at`) VALUES
(1, 'site_name_ar', 'نيش سوسايتي', 'text', 'general', 'Site name in Arabic', '2025-12-21 19:54:20'),
(2, 'site_name_en', 'Niche Society', 'text', 'general', 'Site name in English', '2025-12-21 19:54:20'),
(3, 'site_email', 'info@niche-society.com', 'text', 'contact', 'Main contact email', '2025-12-21 19:54:20'),
(4, 'site_phone', '+966 XX XXX XXXX', 'text', 'contact', 'Main contact phone', '2025-12-21 19:54:20'),
(5, 'site_address_ar', 'الرياض، المملكة العربية السعودية', 'text', 'contact', 'Address in Arabic', '2025-12-21 19:54:20'),
(6, 'site_address_en', 'Riyadh, Saudi Arabia', 'text', 'contact', 'Address in English', '2025-12-21 19:54:20'),
(7, 'facebook_url', 'https://facebook.com/nichesociety', 'text', 'social', 'Facebook page URL', '2025-12-21 19:54:20'),
(8, 'twitter_url', 'https://twitter.com/nichesociety', 'text', 'social', 'Twitter profile URL', '2025-12-21 19:54:20'),
(9, 'instagram_url', 'https://instagram.com/nichesociety', 'text', 'social', 'Instagram profile URL', '2025-12-21 19:54:20'),
(10, 'linkedin_url', 'https://linkedin.com/company/nichesociety', 'text', 'social', 'LinkedIn company URL', '2025-12-21 19:54:20'),
(11, 'iso_certificate', '25EQQN01', 'text', 'company', 'ISO certificate number', '2025-12-21 19:54:20'),
(12, 'iso_valid_until', '2028-11-04', 'text', 'company', 'ISO certificate validity date', '2025-12-21 19:54:20');

-- --------------------------------------------------------

--
-- Table structure for table `success_stories`
--

CREATE TABLE `success_stories` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(200) NOT NULL,
  `client_name_ar` varchar(200) DEFAULT NULL,
  `client_name_en` varchar(200) DEFAULT NULL,
  `client_type` enum('royal','government','corporate','individual') NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `description_ar` text NOT NULL,
  `description_en` text NOT NULL,
  `content_ar` longtext DEFAULT NULL,
  `content_en` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `service_category` varchar(100) DEFAULT NULL,
  `project_date` date DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_name_ar` varchar(200) NOT NULL,
  `client_name_en` varchar(200) NOT NULL,
  `client_position_ar` varchar(200) DEFAULT NULL,
  `client_position_en` varchar(200) DEFAULT NULL,
  `client_photo` varchar(255) DEFAULT NULL,
  `testimonial_ar` text NOT NULL,
  `testimonial_en` text NOT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `service_category` varchar(100) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('admin','editor','viewer') DEFAULT 'viewer',
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `phone`, `status`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'admin', 'admin@niche-society.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', NULL, 'active', '2025-12-21 19:54:20', '2025-12-21 19:54:20', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_popular_posts`
-- (See below for the actual view)
--
CREATE TABLE `v_popular_posts` (
`id` int(10) unsigned
,`slug` varchar(200)
,`title_en` varchar(255)
,`title_ar` varchar(255)
,`views` int(11)
,`published_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_recent_contacts`
-- (See below for the actual view)
--
CREATE TABLE `v_recent_contacts` (
`id` int(10) unsigned
,`name` varchar(100)
,`email` varchar(100)
,`phone` varchar(20)
,`subject` varchar(200)
,`service_interest` varchar(100)
,`status` enum('new','read','replied','closed')
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_services_by_category`
-- (See below for the actual view)
--
CREATE TABLE `v_services_by_category` (
`category` enum('household','events','protocol','properties','consulting','vip')
,`total_services` bigint(21)
,`featured_services` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Structure for view `v_popular_posts`
--
DROP TABLE IF EXISTS `v_popular_posts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_popular_posts`  AS SELECT `blog_posts`.`id` AS `id`, `blog_posts`.`slug` AS `slug`, `blog_posts`.`title_en` AS `title_en`, `blog_posts`.`title_ar` AS `title_ar`, `blog_posts`.`views` AS `views`, `blog_posts`.`published_at` AS `published_at` FROM `blog_posts` WHERE `blog_posts`.`status` = 'published' ORDER BY `blog_posts`.`views` DESC LIMIT 0, 10 ;

-- --------------------------------------------------------

--
-- Structure for view `v_recent_contacts`
--
DROP TABLE IF EXISTS `v_recent_contacts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_recent_contacts`  AS SELECT `contact_submissions`.`id` AS `id`, `contact_submissions`.`name` AS `name`, `contact_submissions`.`email` AS `email`, `contact_submissions`.`phone` AS `phone`, `contact_submissions`.`subject` AS `subject`, `contact_submissions`.`service_interest` AS `service_interest`, `contact_submissions`.`status` AS `status`, `contact_submissions`.`created_at` AS `created_at` FROM `contact_submissions` WHERE `contact_submissions`.`created_at` >= current_timestamp() - interval 30 day ORDER BY `contact_submissions`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_services_by_category`
--
DROP TABLE IF EXISTS `v_services_by_category`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_services_by_category`  AS SELECT `services`.`category` AS `category`, count(0) AS `total_services`, sum(case when `services`.`featured` = 1 then 1 else 0 end) AS `featured_services` FROM `services` WHERE `services`.`status` = 'active' GROUP BY `services`.`category` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_published_at` (`published_at`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_blog_status_published` (`status`,`published_at`);

--
-- Indexes for table `contact_forms`
--
ALTER TABLE `contact_forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_contact_status_created` (`status`,`created_at`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_file_type` (`file_type`),
  ADD KEY `idx_folder` (`folder`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `page_views`
--
ALTER TABLE `page_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_url` (`page_url`(255)),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_viewed_at` (`viewed_at`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_featured` (`featured`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_services_category_status` (`category`,`status`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Indexes for table `success_stories`
--
ALTER TABLE `success_stories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_client_type` (`client_type`),
  ADD KEY `idx_featured` (`featured`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_featured` (`featured`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_forms`
--
ALTER TABLE `contact_forms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_views`
--
ALTER TABLE `page_views`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `success_stories`
--
ALTER TABLE `success_stories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
