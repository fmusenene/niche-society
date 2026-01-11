# PHP Helper Functions Plan
## Niche Society Website - Complete Logic Architecture

---

## EXISTING FUNCTIONS (Already in helpers.php) ✅

### Language & Translation
- `getCurrentLang()` - Get active language
- `setLanguage($lang)` - Set user language
- `loadTranslations($lang)` - Load translation file
- `t($key, $default)` - Get translated text

### Security
- `sanitize($data)` - Sanitize input (recursive for arrays)
- `isValidEmail($email)` - Validate email format
- `generateCsrfToken()` - Generate CSRF token
- `verifyCsrfToken($token)` - Verify CSRF token

### Navigation & URLs
- `redirect($url)` - Redirect to URL
- `getCurrentUrl()` - Get current page URL
- `isActive($page)` - Check if page is active

### Utilities
- `formatDate($date, $format)` - Format date with language support
- `sendEmail($to, $subject, $message)` - Send email
- `generateMetaTags($title, $description, $keywords, $image)` - Generate SEO meta tags
- `humanFileSize($bytes, $decimals)` - Human readable file size
- `truncate($text, $length, $suffix)` - Truncate text with ellipsis

---

## NEEDED FUNCTIONS (To be added)

### 1. SESSION MANAGEMENT
**Functions:**
- `initSession()` - Initialize session with security settings
- `setFlashMessage($type, $message)` - Set success/error flash message
- `getFlashMessage()` - Get and clear flash message
- `destroySession()` - Properly destroy session

**Usage:** Contact form success/error messages, login systems

---

### 2. DATABASE OPERATIONS
**Functions:**
- `db()` - Get PDO instance (singleton pattern)
- `dbQuery($sql, $params)` - Execute query with prepared statements
- `dbFetchOne($sql, $params)` - Fetch single row
- `dbFetchAll($sql, $params)` - Fetch all rows
- `dbInsert($table, $data)` - Insert data into table
- `dbUpdate($table, $data, $where)` - Update table data
- `dbDelete($table, $where)` - Delete from table
- `dbCount($table, $where)` - Count rows

**Usage:** Contact form submissions, blog posts, testimonials

---

### 3. FORM HANDLING
**Functions:**
- `validateContactForm($data)` - Validate contact form data
- `processContactForm($data)` - Process and save contact form
- `validateRequired($value, $field)` - Check required fields
- `validatePhone($phone)` - Validate phone number
- `validateLength($value, $min, $max)` - Validate string length

**Usage:** Contact page form submission

---

### 4. IMAGE HANDLING
**Functions:**
- `getImageUrl($filename)` - Get full image URL
- `imageExists($filename)` - Check if image exists
- `generateResponsiveImage($filename, $alt, $class)` - Generate responsive img tag
- `getImagePath($filename)` - Get server path to image

**Usage:** All pages with images

---

### 5. URL & ROUTING
**Functions:**
- `url($path, $params)` - Generate URL with language support
- `assetUrl($path)` - Get asset URL (CSS/JS/images)
- `currentPage()` - Get current page name
- `generateBreadcrumb($items)` - Generate breadcrumb navigation
- `slugify($text)` - Convert text to URL-friendly slug

**Usage:** Navigation, links, SEO

---

### 6. BLOG FUNCTIONS
**Functions:**
- `getAllPosts($limit, $offset)` - Get paginated blog posts
- `getPostBySlug($slug)` - Get single post
- `getRecentPosts($limit)` - Get recent posts
- `getPostsByCategory($category, $limit)` - Get posts by category
- `savePost($data)` - Save/update blog post
- `deletePost($id)` - Delete blog post
- `getPostExcerpt($post, $length)` - Generate post excerpt

**Usage:** Blog page, blog single page, homepage blog section

---

### 7. TESTIMONIALS
**Functions:**
- `getAllTestimonials($limit)` - Get testimonials
- `getTestimonialById($id)` - Get single testimonial
- `saveTestimonial($data)` - Save testimonial
- `getRandomTestimonials($count)` - Get random testimonials

**Usage:** Homepage, success stories page

---

### 8. CONTACT SUBMISSIONS
**Functions:**
- `saveContactSubmission($data)` - Save contact form to database
- `getContactSubmissions($limit, $offset)` - Get submissions (admin)
- `markSubmissionAsRead($id)` - Mark as read
- `deleteSubmission($id)` - Delete submission

**Usage:** Contact form processing

---

### 9. SERVICE FUNCTIONS
**Functions:**
- `getAllServices()` - Get all services
- `getServiceBySlug($slug)` - Get single service
- `getServiceCategories()` - Get service categories

**Usage:** Services page, homepage services grid

---

### 10. VALIDATION UTILITIES
**Functions:**
- `validateFormData($data, $rules)` - Generic form validator
- `getValidationErrors()` - Get validation error messages
- `isValidUrl($url)` - Validate URL
- `isValidDate($date)` - Validate date format
- `sanitizeHtml($html)` - Sanitize HTML content

**Usage:** All forms, data processing

---

### 11. CACHING (Optional - Performance)
**Functions:**
- `cacheGet($key)` - Get cached data
- `cacheSet($key, $data, $ttl)` - Set cache
- `cacheClear($key)` - Clear specific cache
- `cacheFlush()` - Clear all cache

**Usage:** Blog posts, translations, database queries

---

### 12. LOGGING & DEBUGGING
**Functions:**
- `logError($message, $context)` - Log error to file
- `logActivity($action, $data)` - Log user activity
- `debugDump($data)` - Debug output (dev only)
- `isDebugMode()` - Check if debug mode is enabled

**Usage:** Error tracking, debugging, security monitoring

---

### 13. LANGUAGE UTILITIES
**Functions:**
- `getLanguageFlag($lang)` - Get flag emoji/icon
- `getLanguageName($lang)` - Get language display name
- `getAvailableLanguages()` - Get all supported languages
- `detectUserLanguage()` - Detect browser language

**Usage:** Language switcher, localization

---

### 14. SECURITY HELPERS
**Functions:**
- `hashPassword($password)` - Hash password (for admin)
- `verifyPassword($password, $hash)` - Verify hashed password
- `generateRandomToken($length)` - Generate random token
- `rateLimit($key, $max, $period)` - Rate limiting for forms
- `preventXSS($input)` - Additional XSS protection

**Usage:** Security, spam prevention, admin authentication

---

### 15. PAGINATION
**Functions:**
- `paginate($total, $perPage, $currentPage)` - Calculate pagination
- `generatePaginationLinks($total, $perPage, $currentPage)` - Generate HTML links
- `getPaginationOffset($page, $perPage)` - Calculate SQL offset

**Usage:** Blog listing, admin panels

---

### 16. SOCIAL MEDIA
**Functions:**
- `getSocialLinks()` - Get social media links from config
- `generateShareLinks($url, $title)` - Generate social share links
- `getSocialIcon($platform)` - Get social platform SVG icon

**Usage:** Header, footer, blog posts

---

### 17. FILE UPLOAD (If needed later)
**Functions:**
- `uploadFile($file, $destination)` - Handle file upload
- `validateFileUpload($file, $allowed)` - Validate uploaded file
- `deleteFile($path)` - Delete file safely
- `getFileExtension($filename)` - Get file extension

**Usage:** Future admin panel, blog post images

---

## IMPLEMENTATION PRIORITY

### Phase 1: CRITICAL (Implement Now)
1. Session Management (4 functions)
2. Database Operations (8 functions)
3. Form Handling (5 functions)
4. Image Handling (4 functions)
5. URL & Routing (5 functions)

### Phase 2: IMPORTANT (For Core Pages)
6. Contact Submissions (4 functions)
7. Validation Utilities (5 functions)
8. Security Helpers (5 functions)
9. Language Utilities (4 functions)

### Phase 3: OPTIONAL (For Blog/Advanced Features)
10. Blog Functions (7 functions)
11. Testimonials (4 functions)
12. Service Functions (3 functions)
13. Pagination (3 functions)
14. Social Media (3 functions)
15. Logging & Debugging (4 functions)
16. Caching (4 functions)
17. File Upload (4 functions)

---

## DATABASE TABLES NEEDED

Based on helper functions, we need these tables:

### 1. contact_submissions
```sql
- id (INT PRIMARY KEY AUTO_INCREMENT)
- name (VARCHAR 255)
- email (VARCHAR 255)
- phone (VARCHAR 50)
- service_interest (VARCHAR 100)
- message (TEXT)
- is_read (BOOLEAN DEFAULT 0)
- created_at (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
- ip_address (VARCHAR 45)
```

### 2. blog_posts
```sql
- id (INT PRIMARY KEY AUTO_INCREMENT)
- slug (VARCHAR 255 UNIQUE)
- title_ar (VARCHAR 255)
- title_en (VARCHAR 255)
- excerpt_ar (TEXT)
- excerpt_en (TEXT)
- content_ar (LONGTEXT)
- content_en (LONGTEXT)
- featured_image (VARCHAR 255)
- category (VARCHAR 100)
- status (ENUM: 'draft', 'published')
- author (VARCHAR 100)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- published_at (TIMESTAMP)
```

### 3. testimonials
```sql
- id (INT PRIMARY KEY AUTO_INCREMENT)
- name (VARCHAR 255)
- role (VARCHAR 255)
- quote_ar (TEXT)
- quote_en (TEXT)
- image (VARCHAR 255)
- display_order (INT)
- is_active (BOOLEAN DEFAULT 1)
- created_at (TIMESTAMP)
```

### 4. services (Static or Dynamic)
```sql
- id (INT PRIMARY KEY AUTO_INCREMENT)
- slug (VARCHAR 100 UNIQUE)
- title_ar (VARCHAR 255)
- title_en (VARCHAR 255)
- description_ar (TEXT)
- description_en (TEXT)
- icon (VARCHAR 255)
- image (VARCHAR 255)
- display_order (INT)
- is_active (BOOLEAN DEFAULT 1)
```

### 5. activity_log (Optional - Security)
```sql
- id (INT PRIMARY KEY AUTO_INCREMENT)
- action (VARCHAR 100)
- user_ip (VARCHAR 45)
- user_agent (TEXT)
- data (JSON)
- created_at (TIMESTAMP)
```

---

## CONFIGURATION ADDITIONS

Add to `config/config.php`:

```php
// Security
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('RATE_LIMIT_PERIOD', 300); // 5 minutes

// Pagination
define('POSTS_PER_PAGE', 9);
define('TESTIMONIALS_PER_PAGE', 6);

// File Upload
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Email
define('ADMIN_EMAIL', 'admin@niche-society.com');
define('SMTP_ENABLED', false); // Set to true for SMTP

// Caching
define('CACHE_ENABLED', false);
define('CACHE_DURATION', 3600);
define('CACHE_PATH', __DIR__ . '/../cache');

// Debug
define('DEBUG_MODE', true); // Set to false in production
define('LOG_PATH', __DIR__ . '/../logs');
define('LOG_ERRORS', true);

// Paths
define('UPLOAD_PATH', __DIR__ . '/../uploads');
define('IMAGE_PATH', __DIR__ . '/../assets/images');
```

---

## ERROR HANDLING STRATEGY

### Development (DEBUG_MODE = true):
- Display errors on screen
- Log all errors to file
- Show detailed error messages

### Production (DEBUG_MODE = false):
- Hide errors from users
- Log all errors silently
- Show generic error page
- Send critical errors to admin email

---

## NEXT STEPS

1. ✅ Review this plan
2. 🔄 Implement Phase 1 functions (Critical)
3. 🔄 Update database schema with all tables
4. 🔄 Add configuration constants
5. 🔄 Create error handling wrapper
6. 🔄 Test all functions
7. 🔄 Document usage examples
8. 🔄 Implement Phase 2 and 3 as needed

---

## FUNCTION NAMING CONVENTIONS

- **Actions:** `savePost()`, `deleteUser()`, `sendEmail()`
- **Getters:** `getPostById()`, `getAllPosts()`, `getCurrentUser()`
- **Validators:** `validateEmail()`, `isValidUrl()`, `checkPermission()`
- **Generators:** `generateToken()`, `generatePagination()`, `generateSlug()`
- **Utilities:** `formatDate()`, `truncate()`, `slugify()`

---

## DOCUMENTATION STANDARD

Each function MUST have:
```php
/**
 * Brief description of what function does
 * 
 * @param type $param Description
 * @return type Description
 * @throws Exception When error occurs
 * 
 * @example
 * $result = functionName($param);
 */
```

---

Document Version: 1.0
Last Updated: December 22, 2025
