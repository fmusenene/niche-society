# Niche Society Website - Complete Implementation Plan

## Document Overview
This document provides a comprehensive, section-by-section breakdown of all pages to be implemented for the Niche Society website migration from WordPress to custom PHP.

---

## 1. HOMEPAGE (index.php)

### Status: ⏳ Partially Complete (Hero Only)

### Completed Sections:
1. **Hero Section**
   - Background: niche-society-homepage-1-scaled.jpg
   - Title (AR): "التميز في خدمات الإدارة الفاخرة"
   - Title (EN): "Excellence in Luxury Management Services"
   - Subtitle (AR): "منذ 25 عامًا نُدير ممتلكات ونُنسّق حياة أصحاب الذوق الرفيع"
   - CTA Buttons: "Explore Services", "Get Consultation"
   - Animations: AOS fade-up effects

### Sections To Implement:

2. **Services Grid Section**
   - Title (AR): "جميع الخدمات" / (EN): "All Services"
   - Layout: 3x2 Grid (6 service cards)
   - Service Cards:
     1. Household Management ("خدمات الإدارة الذكية لممتلكاتك")
        - Image: service.png
        - Link: services/#HouseholdManagement
     2. Lifestyle Concierge ("خدمات الكونسيرج")
        - Image: service-2.png
        - Link: services/#LifestyleConcierge
     3. Property Management ("إدارة العقارات الفاخرة")
        - Image: Whisk_dff5d53dd7fb1c1a8bf48b8d9252887cdr.jpg
        - Link: services/#PropertyManagement
     4. Training & Consulting ("التدريب والاستشارات")
        - Image: shutterstock_2494702611-scaled.jpg
        - Link: services/#Training
     5. Event Management ("إدارة الفعاليات")
        - Image: Whisk_ffd7dd7fcf61a53b52142591ecf08343dr.jpg
        - Link: services/#EventManagement
     6. Protocol & Etiquette ("البروتوكول وفن الإتيكيت")
        - Image: shutterstock_2517480229-scaled.jpg
        - Link: services/#Protocol
   - Card Design: Image on top, title overlay, hover effects

3. **Team Introduction Section**
   - Background: Full-width image (TEAM-scaled.jpg)
   - Title (AR): "تعرفوا على فريق نيش سوسايتي"
   - Title (EN): "Meet the Niche Society Team"
   - Description (AR): Rich text about team expertise (starts with "في نيش سوسايتي، الفريق ليس مجرد أسماء...")
   - Layout: Centered text over background image with overlay
   - Text Alignment: RTL for Arabic

4. **Testimonials Section**
   - Title (AR): "ماذا يقول عملاؤنا" / (EN): "What People Are Saying"
   - Subtitle: "Maecenas ullam, Mollis suscipit..."
   - Layout: 3-column carousel/grid
   - Testimonial Cards:
     1. Philip Watson - Player
        - Image: client-2-free-img.jpg
        - Quote: "Arcu iste nihil dolorum..."
     2. Emma Roberts - Tracker
        - Image: client-5-free-img.jpg
        - Quote: "Quaerat mus ut..."
     3. Olivia Spencer - Coach
        - Image: client-1-free-img.jpg
        - Quote: "Impedit sollicitudin..."
   - Card Design: Circular image, centered quote, client name and role below

5. **Blog Preview Section**
   - Title (AR): "آخر المقالات" / (EN): "Health & Nutrition Articles"
   - Subtitle: "Maecenas ullam, Mollis suscipit..."
   - Layout: 3-column grid
   - Blog Post Cards:
     1. Post 1:
        - Image: blog01-free-imh-1.jpg
        - Title: "Corrupti Explicabo Congue Placea Felis"
        - Excerpt: "Dolor eum doloremque..."
        - CTA: "Read More"
     2. Post 2:
        - Image: blog02-free-img.jpg
        - Title: "Lobortis Sapien, Nisi Donec Perferendis"
        - Excerpt: "Dolor eum doloremque..."
        - CTA: "Read More"
     3. Post 3:
        - Image: image-12.jpg
        - Title: "Nunc Officiis Tenetur Ad Duis Ipsam Magni Vehicula"
        - Excerpt: "Dolor eum doloremque..."
        - CTA: "Read More"

6. **Featured Logos Section**
   - Title (AR): "ظهرنا في" / (EN): "Featured In :"
   - Layout: Horizontal gallery/slider
   - Logos: 
     - logo-1.svg
     - logo-2.svg
     - logo-3.svg
     - logo-4.svg
     - logo-5.svg
   - Display: Grayscale with color on hover

7. **Final CTA Section** (Optional)
   - Background: Burgundy gradient
   - Title: Call-to-action for consultation
   - Button: Link to contact page

---

## 2. ABOUT PAGE (about.php)

### Status: ❌ Not Started

### Sections To Implement:

1. **Hero Section**
   - Background: Burgundy (#602234) with triangle shape divider
   - Title (AR): "من نحن" / (EN): "About"
   - Layout: Centered text with background

2. **Who We Are Section**
   - Title (AR): "من نحن؟" / (EN): "Who We Are?"
   - Content:
     - Paragraph 1 (AR): "نيش سوسايتي هي شركة متخصصة في تقديم حلول إدارية وتنظيمية..."
     - Paragraph 1 (EN): "Niche Society is a specialized company offering management and organizational solutions..."
     - Paragraph 2: Description of 25+ years experience serving royal families
     - Paragraph 3: Description of behind-the-scenes management approach
     - Paragraph 4: Philosophy about luxury and excellence
     - Paragraph 5: Mission statement about trust and peace of mind
   - Layout: Single column, centered text with good spacing
   - Text Direction: RTL for Arabic

3. **Values/Approach Section** (If content exists)
   - Title: "Our Approach" or similar
   - Layout: Icon grid or text blocks
   - Content: Key principles and values

4. **Experience Highlights**
   - Years of Experience: 25+
   - Key Achievements
   - ISO Certification mention

5. **CTA Section**
   - Button: "Contact Us" or "Learn More About Our Services"
   - Link: To services or contact page

---

## 3. SERVICES PAGE (services.php)

### Status: ❌ Not Started

### Sections To Implement:

1. **Hero Section**
   - Background: Burgundy (#602234) with triangle shape divider
   - Title (AR): "خدماتنا" / (EN): "Services"
   - Layout: Centered text

2. **Service #1: Household Management (HouseholdManagement anchor)**
   - Layout: Two-column (Image left, Content right)
   - Image: service.png (730x815px)
   - Title (AR): "خدمات الإدارة الذكية لممتلكاتك"
   - Title (EN): "Smart Management Services for Your Properties"
   - Description: "نحوّّل كل موقع إلى منظومة متكاملة تدُار بأنظمة ذكية وتُُنسّّق بتواصل فعّّال..."
   - Content Structure:
     - Introduction paragraph
     - Key features list
     - Benefits explanation
   - Background: Light (#f3f7ff)

3. **Service #2: Lifestyle Concierge (LifestyleConcierge anchor)**
   - Layout: Two-column (Content left, Image right - alternating)
   - Image: service-2.png
   - Title (AR): "خدمات الكونسيرج"
   - Title (EN): "Lifestyle Concierge Services"
   - Description: Full service description
   - Background: White

4. **Service #3: Property Management (PropertyManagement anchor)**
   - Layout: Two-column (Image left, Content right)
   - Image: Whisk_dff5d53dd7fb1c1a8bf48b8d9252887cdr.jpg
   - Title (AR): "إدارة العقارات الفاخرة"
   - Title (EN): "Luxury Property Management"
   - Description: Full service description
   - Background: Light (#f3f7ff)

5. **Service #4: Training & Consulting (Training anchor)**
   - Layout: Two-column (Content left, Image right)
   - Image: shutterstock_2494702611-scaled.jpg
   - Title (AR): "التدريب والاستشارات"
   - Title (EN): "Training & Consulting"
   - Description: Full service description
   - Background: White

6. **Service #5: Event Management (EventManagement anchor)**
   - Layout: Two-column (Image left, Content right)
   - Image: Whisk_ffd7dd7fcf61a53b52142591ecf08343dr.jpg
   - Title (AR): "إدارة الفعاليات"
   - Title (EN): "Event Management"
   - Description: Full service description
   - Background: Light (#f3f7ff)

7. **Service #6: Protocol & Etiquette (Protocol anchor)**
   - Layout: Two-column (Content left, Image right)
   - Image: shutterstock_2517480229-scaled.jpg
   - Title (AR): "البروتوكول وفن الإتيكيت"
   - Title (EN): "Protocol & Etiquette"
   - Description: Full service description
   - Background: White

8. **CTA Section**
   - Title: "Ready to Experience Excellence?"
   - Description: Brief text about getting started
   - Button: "Request a Consultation"
   - Link: To contact page

---

## 4. CONTACT PAGE (contact.php)

### Status: ❌ Not Started

### Sections To Implement:

1. **Hero Section**
   - Background: Burgundy (#602234) with triangle shape divider
   - Title (AR): "تواصل معنا" / (EN): "Contact Us"
   - Layout: Centered text

2. **Contact Information Section**
   - Layout: Three-column grid
   - Contact Cards:
     1. Location Card:
        - Icon: Map pin
        - Title (AR): "العنوان" / (EN): "Address"
        - Content: "Qurtubah - Riyadh KSA"
     2. Email Card:
        - Icon: Email
        - Title (AR): "البريد الإلكتروني" / (EN): "Email"
        - Content: "info@niche-society.com"
     3. Phone Card:
        - Icon: Phone
        - Title (AR): "الهاتف" / (EN): "Phone"
        - Content: "+966 532 447 976"
   - Design: Cards with icon, title, and contact info

3. **Contact Form Section**
   - Layout: Centered form, max-width 800px
   - Form Fields:
     1. Name (text input)
        - Label (AR): "الاسم" / (EN): "Name"
        - Required: Yes
     2. Email (email input)
        - Label (AR): "البريد الإلكتروني" / (EN): "Email"
        - Required: Yes
     3. Phone (tel input)
        - Label (AR): "الهاتف" / (EN): "Phone"
        - Required: No
     4. Service Interest (select dropdown)
        - Label (AR): "الخدمة المطلوبة" / (EN): "Service of Interest"
        - Options: All services listed
        - Required: No
     5. Message (textarea)
        - Label (AR): "الرسالة" / (EN): "Message"
        - Required: Yes
        - Rows: 6
     6. Submit Button
        - Text (AR): "إرسال" / (EN): "Send Message"
        - Style: Burgundy background, cream text
   - Validation: HTML5 + JavaScript
   - Success Message: Display after submission
   - Error Handling: Show field-specific errors

4. **Business Hours Section**
   - Title (AR): "ساعات العمل" / (EN): "Business Hours"
   - Content: "Sat-Thu 9:00 AM - 6:00 PM"
   - Layout: Centered card or info box

5. **Map Section** (Optional)
   - Google Maps embed or static map
   - Location: Riyadh, Saudi Arabia (Qurtubah area)
   - Design: Full-width or contained

6. **Social Media Links**
   - Layout: Horizontal icon row
   - Links: LinkedIn, Facebook, Twitter/X, Instagram
   - Design: Icon buttons with hover effects

---

## 5. SUCCESS STORIES PAGE (success-stories.php)

### Status: ❌ Not Started

### Sections To Implement:

1. **Hero Section**
   - Background: Burgundy (#602234) with triangle shape divider
   - Title (AR): "قصص النجاح" / (EN): "Success Stories"
   - Subtitle: Brief introduction to client testimonials
   - Layout: Centered text

2. **Testimonials Grid Section**
   - Layout: 2-3 column grid (responsive)
   - Testimonial Card Structure:
     - Client photo (circular)
     - Quote text
     - Client name
     - Client role/title
     - Company/context (if applicable)
   - Include at least 6-9 testimonials
   - Design: Card with shadow, hover effects
   - Content from homepage testimonials + additional ones

3. **Client Logos Section**
   - Title (AR): "عملاؤنا" / (EN): "Our Clients"
   - Layout: Logo grid or slider
   - Logos: Featured company/family logos (if permitted)
   - Design: Grayscale with color on hover

4. **Case Studies Section** (If content exists)
   - Title: "Featured Projects"
   - Layout: Alternating image/text blocks
   - Case Study Structure:
     - Project image
     - Project title
     - Challenge description
     - Solution provided
     - Results achieved
   - Number: 2-4 case studies

5. **CTA Section**
   - Title: "Become Our Next Success Story"
   - Button: "Start Your Journey"
   - Link: To contact page

---

## 6. BLOG PAGE (blog.php)

### Status: ❌ Not Started

### Sections To Implement:

1. **Hero Section**
   - Background: Burgundy (#602234) with triangle shape divider
   - Title (AR): "المدونة" / (EN): "Blog"
   - Subtitle: Brief description of blog content
   - Layout: Centered text

2. **Blog Posts Grid**
   - Layout: 3-column grid (responsive to 2-col, then 1-col)
   - Post Card Structure:
     - Featured image
     - Post title
     - Excerpt (150 characters)
     - Read more link
     - Date (optional)
     - Category (optional)
   - Initial Posts:
     1. "Corrupti Explicabo Congue Placea Felis"
     2. "Lobortis Sapien, Nisi Donec Perferendis"
     3. "Nunc Officiis Tenetur Ad Duis Ipsam Magni Vehicula"
   - Pagination: For future posts

3. **Sidebar** (Optional)
   - Categories widget
   - Recent posts widget
   - Search widget

4. **Blog Post Template** (blog-single.php)
   - Post title
   - Featured image
   - Post meta (date, author, category)
   - Post content
   - Share buttons
   - Related posts section

---

## 7. TECHNICAL SPECIFICATIONS

### Global Components (Already Complete):

1. **Header (includes/header.php)** ✅
   - Top bar with contact info and social icons
   - Logo
   - Main navigation menu
   - Language switcher (AR/EN)
   - Mobile responsive hamburger menu
   - Sticky navigation

2. **Footer (includes/footer.php)** ✅
   - 4-column layout
   - Company info with logo and ISO badge
   - Quick links menu
   - Services submenu
   - Contact information
   - Social media icons
   - Bottom bar with copyright and legal links

3. **Configuration Files** ✅
   - config/config.php: Site constants
   - config/database.php: Database connection
   - functions/helpers.php: Helper functions

### Design System:

**Colors:**
- Primary Burgundy: #602234, #602335
- Cream: #FFF7E7
- Navy: #000f2b
- Gray: #4b4f58
- Light Blue: #f3f7ff
- White: #ffffff

**Typography:**
- Font Family: IBM Plex Sans (Arabic & English)
- Headings: 
  - H1: 70px (40px mobile) / 4.375rem
  - H2: 40px (30px mobile) / 2.5rem
  - H3: 30px (25px mobile) / 1.875rem
  - H4: 23px (20px mobile) / 1.4375rem
  - H5: 18px / 1.125rem
  - H6: 15px / 0.9375rem
- Body: 16px / 1rem
- Line Height: 1.5em

**Spacing:**
- Section Padding: 80px top/bottom (desktop), 40px (tablet), 20px (mobile)
- Container Max Width: 1200px
- Grid Gap: 30px

**Buttons:**
- Primary: Burgundy background, cream text
- Secondary: Cream background, burgundy text
- Border Radius: 3px
- Padding: 15px 40px
- Font: PT Sans, 15px, Uppercase
- Hover: Slightly darker shade

**Animations:**
- AOS Library: fade-up, fade-in effects
- Duration: 600ms
- Delay: Staggered by 100-200ms

### Responsive Breakpoints:
- Desktop: 922px+
- Tablet: 544px - 921px
- Mobile: 0 - 543px

### Language Support:
- Default: Arabic (AR)
- Secondary: English (EN)
- Method: PHP session + URL parameter + cookie
- Translation Files: lang/ar.json, lang/en.json
- RTL Support: Automatic based on language

---

## 8. ASSETS INVENTORY

### Images Required:

**Homepage:**
- niche-society-homepage-1-scaled.jpg (Hero)
- service.png (Service 1)
- service-2.png (Service 2)
- Whisk_dff5d53dd7fb1c1a8bf48b8d9252887cdr.jpg (Service 3)
- shutterstock_2494702611-scaled.jpg (Service 4)
- Whisk_ffd7dd7fcf61a53b52142591ecf08343dr.jpg (Service 5)
- shutterstock_2517480229-scaled.jpg (Service 6)
- TEAM-scaled.jpg (Team section)
- client-2-free-img.jpg (Testimonial 1)
- client-5-free-img.jpg (Testimonial 2)
- client-1-free-img.jpg (Testimonial 3)
- blog01-free-imh-1.jpg (Blog post 1)
- blog02-free-img.jpg (Blog post 2)
- image-12.jpg (Blog post 3)
- logo-1.svg through logo-5.svg (Featured logos)

**Logos:**
- brand-logo.pdf-3.png (Main logo)
- niche-logo-png.png (Footer logo)

**Icons:**
- Bootstrap Icons (CDN)
- Social media SVGs (inline in footer)

**Favicon:**
- cropped-brand-logo.pdf-1-32x32.png
- cropped-brand-logo.pdf-1-192x192.png
- cropped-brand-logo.pdf-1-180x180.png
- cropped-brand-logo.pdf-1-270x270.png

### CSS Files:
- assets/css/style.css (Custom styles)
- assets/css/rtl.css (RTL overrides)
- Bootstrap 5.3.2 (CDN)
- Animate.css 4.1.1 (CDN)

### JavaScript Files:
- assets/js/main.js (Custom scripts)
- Bootstrap 5.3.2 JS (CDN)
- AOS Library (CDN)

---

## 9. DEVELOPMENT PRIORITIES

### Phase 1: Core Pages (Week 1)
1. Complete Homepage (index.php) - Add remaining 6 sections
2. Create About Page (about.php) - All 5 sections
3. Create Services Page (services.php) - All 8 sections

### Phase 2: Secondary Pages (Week 2)
4. Create Contact Page (contact.php) - All 6 sections including form
5. Create Success Stories Page (success-stories.php) - All 5 sections
6. Create Blog Page (blog.php) - Grid and single template

### Phase 3: Polish & Testing (Week 3)
7. Transfer all images from backup to assets/images/
8. Create custom CSS (assets/css/style.css)
9. Create custom JavaScript (assets/js/main.js)
10. Translation files (lang/ar.json, lang/en.json)
11. Database schema for contact form and blog
12. Cross-browser testing
13. Mobile responsiveness testing
14. Performance optimization
15. SEO meta tags for all pages

---

## 10. CONTENT EXTRACTION NOTES

### From WordPress Backup Analysis:
- Content is in Arabic (primary) and English (secondary)
- Elementor was used for page building (sections in containers)
- Images are in wp-content/uploads/ folders organized by year/month
- Text content needs extraction from HTML files (avoid CSS extraction)
- Service descriptions need full content extraction
- About page has rich text paragraphs
- Contact form needs backend implementation

### Content Gaps To Fill:
- Full service descriptions for all 6 services
- Additional testimonials for success stories page
- Blog post content (currently only titles and excerpts)
- Case study content (if applicable)
- Team member information (names, roles, photos)
- Detailed about page content

---

## 11. QUALITY CHECKLIST

### For Each Page:
- [ ] Bilingual content (AR/EN)
- [ ] Responsive design (desktop, tablet, mobile)
- [ ] RTL support for Arabic
- [ ] SEO meta tags
- [ ] Open Graph tags
- [ ] Proper heading hierarchy (H1 → H2 → H3)
- [ ] Alt text for all images
- [ ] Loading animations (AOS)
- [ ] Hover effects on interactive elements
- [ ] Form validation (where applicable)
- [ ] Accessibility (ARIA labels, keyboard navigation)
- [ ] Page load optimization (lazy loading images)
- [ ] Cross-browser compatibility
- [ ] Mobile touch-friendly elements
- [ ] Consistent spacing and alignment

---

## Document Version: 1.0
## Last Updated: December 22, 2025
## Status: Planning Phase - Ready for Implementation
