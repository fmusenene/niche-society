# Niche Society Website - PHP/MySQL Project

## Project Overview
Complete PHP/MySQL website for Niche Society, a luxury estate and event management company with 25 years of experience serving royal families and distinguished clients in Saudi Arabia.

## Technology Stack
- **Backend**: PHP 8.x
- **Database**: MySQL 8.x
- **Frontend**: Bootstrap 5.3.2
- **Languages**: Arabic (primary, RTL) & English
- **Server**: MAMP (macOS)
- **Design**: Quiet luxury aesthetic with burgundy (#602335) and cream (#FFF6E7) colors

## Project Structure
```
niche-society/
├── assets/
│   ├── css/
│   │   ├── style.css         # Main stylesheet
│   │   └── rtl.css            # RTL (Arabic) styles
│   ├── js/
│   │   └── main.js            # Main JavaScript
│   └── images/                # Site images
├── config/
│   ├── config.php             # Main configuration
│   └── database.php           # Database connection
├── database/
│   └── schema.sql             # Database schema
├── docs/
│   ├── 01-ISO-CERTIFICATION.md
│   ├── 02-COMPANY-PROFILE.md
│   ├── 03-SERVICES-PROTOCOL-ETIQUETTE.md
│   ├── 04-SERVICES-HOUSEHOLD-MANAGEMENT.md
│   ├── 05-SERVICES-EVENT-MANAGEMENT.md
│   ├── 06-PROJECT-REQUIREMENTS.md
│   ├── IMAGE-CATALOG.xml      # Complete image documentation
│   ├── README.md
│   └── WEBSITE-CONTENT-ANALYSIS.md
├── functions/
│   └── helpers.php            # Helper functions
├── includes/
│   ├── header.php             # Header template
│   └── footer.php             # Footer template
├── lang/
│   ├── ar.json                # Arabic translations
│   └── en.json                # English translations
├── logs/
│   └── activity.log           # Activity logging
├── website-backup/            # Original website backup
└── index.php                  # Homepage

## Database Configuration
**Database Name**: niche_society
**Host**: localhost (via socket)
**User**: root
**Password**: root
**Socket**: /Applications/MAMP/tmp/mysql/mysql.sock
**Charset**: utf8mb4_unicode_ci

## Key Features
1. **Bilingual Support**: Full Arabic/English with RTL for Arabic
2. **ISO Certification**: Certificate 25EQQN01 (valid until 2028-11-04)
3. **Services**: 6 main categories (Household, Events, Protocol, Properties, Consulting, VIP)
4. **Security**: CSRF protection, input sanitization, prepared statements
5. **SEO**: Meta tags, Open Graph, structured data ready
6. **Responsive**: Mobile-first Bootstrap 5 design
7. **Image Optimization**: Complete catalog with recommendations (see IMAGE-CATALOG.xml)

## Installation Steps

### 1. Database Setup
```bash
# Start MAMP MySQL server
# Import database schema
mysql -u root -p --socket=/Applications/MAMP/tmp/mysql/mysql.sock < database/schema.sql
```

### 2. Configuration
- Database credentials already configured in `config/database.php`
- Site settings in `config/config.php`
- Adjust URLs if needed (default: http://localhost/niche-society)

### 3. Assets Setup
```bash
# Copy images from website-backup to assets/images/
cp -r website-backup/niche-society.com/wp-content/uploads/* assets/images/

# Create necessary directories
mkdir -p logs
chmod 755 logs
```

### 4. Access the Site
```
Homepage: http://localhost/niche-society/index.php
```

## Default Credentials
**Admin User**:
- Username: `admin`
- Email: `admin@niche-society.com`
- Password: `Admin@123`

## Brand Guidelines
### Colors
- **Primary**: #602335 (Burgundy/Maroon)
- **Secondary**: #FFF6E7 (Cream)
- **Accent**: #8B4A62 (Light Burgundy)
- **Dark**: #2C1A23

### Fonts
- **Arabic**: Tajawal (Google Fonts)
- **English**: Playfair Display (headings) + Lato (body)

### Logo
- Main logo: `assets/images/logo.png` (120x57px)
- Light logo (footer): `assets/images/logo-light.png`
- Favicon: Multiple sizes in `assets/images/favicon-*.png`

## Image Optimization
See `docs/IMAGE-CATALOG.xml` for:
- Complete image inventory (55 files)
- Optimization recommendations
- SEO alt text guidelines
- Accessibility best practices

**Critical optimizations needed**:
- service.png: 1.1M → 200K
- Niche-Society-Arabic-CP2.png: 2.3M → 600K
- Mission/Vision/Values PNGs: 1M-1.5M → 400K each
- Convert JPEGs to WebP for 30-40% size reduction

## File Structure Documentation

### Core PHP Files
- **config/config.php**: Site-wide settings, constants, error handling
- **config/database.php**: PDO connection with error handling
- **functions/helpers.php**: 25+ utility functions (translation, sanitization, email, etc.)
- **includes/header.php**: Responsive navigation, language switcher, meta tags
- **includes/footer.php**: Social links, ISO badge, sitemap
- **index.php**: Homepage with hero, services grid, CTA sections

### Translation System
- JSON-based translations in `lang/` folder
- `t()` function for text retrieval
- `getCurrentLang()` and `setLanguage()` for language management
- Session-based language persistence

### Security Features
- CSRF token generation/verification
- Input sanitization (recursive array support)
- Prepared statements (PDO)
- Session timeout
- XSS protection via htmlspecialchars

## Development Guidelines

### Adding New Pages
1. Create PHP file in root directory
2. Include configuration and helpers:
   ```php
   <?php
   require_once __DIR__ . '/config/config.php';
   require_once __DIR__ . '/functions/helpers.php';
   ```
3. Add translations to `lang/ar.json` and `lang/en.json`
4. Include header/footer templates
5. Use `generateMetaTags()` for SEO

### Database Operations
```php
// Use PDO prepared statements
$stmt = $pdo->prepare("SELECT * FROM services WHERE status = ? AND category = ?");
$stmt->execute(['active', 'household']);
$services = $stmt->fetchAll();
```

### Translation Usage
```php
// In PHP
echo t('nav_home', 'الرئيسية');

// In HTML
<h1><?php echo t('hero_title', 'Default Text'); ?></h1>
```

## Pages to Build
1. ✅ **Homepage** (index.php) - Completed
2. ⏳ **About Us** (about.php) - Pending
3. ⏳ **Services** (services.php) - Pending
4. ⏳ **Success Stories** (success-stories.php) - Pending
5. ⏳ **Blog** (blog.php) - Pending
6. ⏳ **Contact** (contact.php) - Pending
7. ⏳ **Admin Panel** (admin/) - Pending

## API Endpoints (Future)
- Contact form submission
- Newsletter subscription
- Blog post loading
- Service inquiries

## Performance Optimization
- Lazy loading for images
- CSS/JS minification
- Database query optimization
- Caching strategy (Redis/Memcached)
- CDN for static assets

## SEO Checklist
- [x] Meta tags (title, description)
- [x] Open Graph tags
- [x] Twitter Card tags
- [x] Semantic HTML5
- [x] Alt text for images
- [ ] XML sitemap
- [ ] Robots.txt
- [ ] Structured data (Schema.org)
- [ ] Canonical URLs
- [ ] 301 redirects

## Security Checklist
- [x] CSRF protection
- [x] Input sanitization
- [x] Prepared statements
- [x] Session security
- [ ] HTTPS enforcement
- [ ] Content Security Policy
- [ ] Rate limiting
- [ ] SQL injection prevention
- [ ] XSS prevention

## Testing
- [ ] Cross-browser compatibility (Chrome, Safari, Firefox, Edge)
- [ ] Mobile responsiveness (iOS, Android)
- [ ] RTL layout (Arabic)
- [ ] Form validation
- [ ] Database operations
- [ ] Session management
- [ ] Language switching
- [ ] Performance benchmarks

## Deployment Checklist
- [ ] Change database credentials
- [ ] Disable error display
- [ ] Enable HTTPS
- [ ] Set up backups
- [ ] Configure email
- [ ] Add analytics
- [ ] Test contact form
- [ ] Verify translations
- [ ] Optimize images
- [ ] Minify CSS/JS
- [ ] Create sitemap
- [ ] Submit to search engines

## Support & Maintenance
- Regular backups (daily recommended)
- Security updates
- Content updates via admin panel
- Performance monitoring
- Analytics review
- Image optimization
- Database optimization
- Error log monitoring

## Contact Information
**Company**: Niche Society
**Website**: https://niche-society.com
**Email**: info@niche-society.com
**Location**: Riyadh, Saudi Arabia
**ISO Certificate**: 25EQQN01 (valid until 2028-11-04)

## License
Proprietary - © 2025 Niche Society. All rights reserved.

## Version History
- **v1.0.0** (2025-01-21): Initial PHP/MySQL implementation
  - Core architecture setup
  - Database schema
  - Homepage complete
  - Bilingual support (Arabic/English)
  - Image catalog documentation
  - Translation system
  - Security features
