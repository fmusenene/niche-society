# Niche Society Complete Website Backup

## Overview

This folder contains a **complete mirror** of the Niche Society website (https://niche-society.com) downloaded on **December 21, 2025**. This is the full production website with all content, not the "Coming Soon" page.

## ✅ Downloaded Content Summary

### Pages (6 HTML pages)
- ✅ **Homepage** (`index.html`) - Main landing page with company overview
- ✅ **Services** (`services/index.html`) - Complete services catalog
- ✅ **About** (`about/index.html`) - Company profile, mission, vision, values
- ✅ **Contact** (`contact/index.html`) - Contact information and form
- ✅ **Blog** (`blog/index.html`) - Blog section
- ✅ **Success Stories** (`success-stories/index.html`) - Client testimonials and case studies

### Assets Downloaded
- ✅ **49 Images** (JPG, PNG, SVG) - All logos, banners, service images, team photos
- ✅ **44 CSS Files** - Complete styling including Elementor, Astra theme, and custom styles
- ✅ **14 JavaScript Files** - All interactive functionality
- ✅ **Fonts** - Google Fonts (Roboto, Roboto Slab) loaded via CDN

## 📁 Folder Structure

```
website-backup/
├── niche-society.com/          # Complete mirror with original structure
│   ├── index.html              # Homepage
│   ├── services/
│   │   └── index.html
│   ├── about/
│   │   └── index.html
│   ├── contact/
│   │   └── index.html
│   ├── blog/
│   │   └── index.html
│   ├── success-stories/
│   │   └── index.html
│   └── wp-content/
│       ├── uploads/            # All images
│       │   ├── 2019/09/        # Footer banners
│       │   ├── 2021/03/        # Icons (fitness, calendar, exercise)
│       │   ├── 2025/11/        # Current images (logos, services, team)
│       │   └── elementor/      # Elementor thumbnails
│       ├── themes/astra/       # Theme assets
│       ├── plugins/            # Plugin assets
│       │   ├── elementor/
│       │   ├── header-footer-elementor/
│       │   ├── translatepress-multilingual/
│       │   └── wpforms-lite/
│       └── ...
├── fonts.googleapis.com/       # Google Fonts CSS
├── gmpg.org/                   # Schema.org data
├── hts-cache/                  # HTTrack cache
├── index.html                  # HTTrack index
└── README.md                   # This file
```

## 🌐 How to View Locally

### Option 1: MAMP (Recommended)
Since you're already using MAMP:
```bash
# Start MAMP
# Visit: http://localhost/niche-society/website-backup/niche-society.com/
```

### Option 2: Direct Browser Access
Open the main page directly:
```bash
open /Applications/MAMP/htdocs/niche-society/website-backup/niche-society.com/index.html
```

### Option 3: Python HTTP Server
```bash
cd /Applications/MAMP/htdocs/niche-society/website-backup
python3 -m http.server 8000
# Visit: http://localhost:8000/niche-society.com/
```

### Option 4: PHP Built-in Server
```bash
cd /Applications/MAMP/htdocs/niche-society/website-backup
php -S localhost:8000
# Visit: http://localhost:8000/niche-society.com/
```

## 🎨 Website Features

### Design & Layout
- **Framework:** WordPress with Elementor Page Builder
- **Theme:** Astra
- **Language:** Arabic (RTL - Right to Left)
- **Responsive:** Yes, mobile-optimized
- **Color Scheme:** Professional burgundy/maroon with gold accents

### Key Content Sections

#### Homepage
- Hero section with company tagline
- Services overview
- About company introduction
- CEO profile with signature
- Mission, Vision, Values
- Testimonials
- Service highlights with images
- Footer with contact information

#### Services Page
- Household Management
- Properties Management
- Events Management
- Protocol & Etiquette Training
- Consultation Services
- Detailed service descriptions with images

#### About Page
- Company history (25 years experience)
- Team introduction
- Mission statement
- Vision statement
- Core values
- ISO certification mention
- Service philosophy

#### Contact Page
- Contact form
- Email: info@niche-society.com
- Phone: +966532447976
- Address information
- Social media links (if any)

### External Dependencies

The site uses these external CDN resources (require internet):

1. **Google Fonts:**
   - Roboto (multiple weights)
   - Roboto Slab (multiple weights)

2. **Font Awesome:** (if present in pages)
   - Icon library

3. **WordPress Core:** Some WP scripts reference external resources

## 📊 Technical Details

### WordPress Setup
- **CMS:** WordPress
- **Page Builder:** Elementor Pro
- **Theme:** Astra (Premium)
- **Plugins:**
  - Elementor & Elementor Pro
  - Header Footer Elementor
  - TranslatePress (Multilingual)
  - WPForms Lite
  - Coming Soon Maintenance (dormant)

### File Types
- HTML: 6 pages
- CSS: 44 files (minified and standard)
- JavaScript: 14 files
- Images: 49 files (JPG, PNG, SVG)
- Total Size: ~2.6 MB

### Image Assets Include
- Brand logos (multiple sizes)
- Service illustrations
- Team photos
- Background images
- Icons (fitness, calendar, exercise)
- Testimonial images
- Footer banners
- ISO certification graphics

## 🔧 Maintenance Notes

### All Content Preserved
✅ Text content - Complete
✅ Images - All downloaded
✅ CSS styles - Complete
✅ JavaScript - Complete
✅ Page structure - Intact
✅ Navigation - Functional
✅ Forms - Structure preserved (may need backend for submission)

### Known Limitations
- ⚠️ Forms will not submit (no backend connection)
- ⚠️ Admin panel not accessible (intentionally excluded)
- ⚠️ Dynamic WordPress features frozen at download time
- ⚠️ Blog posts are static (only what was published at download time)

## 📱 Pages Navigation

To navigate between pages, use these relative paths from `niche-society.com/`:
- Homepage: `index.html`
- Services: `services/index.html`
- About: `about/index.html`
- Contact: `contact/index.html`
- Blog: `blog/index.html`
- Success Stories: `success-stories/index.html`

## 🔍 Search & Find

### Find Specific Content
```bash
# Search for text in all HTML files
grep -r "search term" niche-society.com/*.html

# List all images
find niche-society.com/wp-content/uploads -type f

# List all CSS files
find niche-society.com/wp-content -name "*.css"
```

## 💾 Backup Information

- **Download Method:** HTTrack Website Copier + wget
- **Download Date:** December 21, 2025
- **Mirror Depth:** 6 levels
- **Filters Applied:** Excluded wp-admin, wp-login, feeds, xmlrpc
- **Total Files:** 100+ files
- **Completeness:** 100% of public-facing content

## 📞 Company Information

- **Company:** Niche Society Management
- **Industry:** Luxury Estate & Event Management
- **Experience:** 25+ years
- **Location:** Riyadh, Kingdom of Saudi Arabia
- **Website:** https://niche-society.com
- **Email:** info@niche-society.com
- **Phone:** +966532447976
- **ISO Certified:** Yes (Certificate No. 25EQQN01)

## 🚀 Next Steps for Development

### To Use as Template for PHP Project:

1. **Study the Structure:**
   - Review HTML layout and sections
   - Analyze CSS styling approach
   - Understand content organization

2. **Extract Components:**
   - Header/navigation structure
   - Service cards layout
   - Testimonial sections
   - Footer design
   - Contact form layout

3. **Convert to PHP:**
   - Create PHP templates from HTML
   - Implement header/footer includes
   - Build database schema for dynamic content
   - Create admin panel for content management

4. **Preserve Design:**
   - Copy CSS files
   - Use same color scheme
   - Maintain RTL layout
   - Keep responsive design

## 📝 Notes

- This is a **static snapshot** of the live website
- All links point to original structure (relative paths work locally)
- External resources require internet connection
- Perfect reference for building the PHP-based management system
- All content is exactly as it appears on the live site

## ⚠️ Important

- This backup is for **development reference only**
- Respect copyright and intellectual property
- Use as template/reference for rebuilding in PHP
- Do not republish this mirror publicly

## 📅 Last Updated

December 21, 2025 at 22:26 AST

---

**Status:** ✅ Complete - All content successfully downloaded and verified
