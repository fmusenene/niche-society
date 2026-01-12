<?php
/**
 * Footer Template - Professional Interactive Design
 * Niche Society Website - Burgundy/Cream Corporate Theme
 */

$lang = getCurrentLang();
$isArabic = ($lang === 'ar');
?>

<style>
/* Footer Interactive Styles */
.footer-corporate {
    background: #602234 !important;
    color: #fffaf3 !important;
    padding: 15px 0 0 !important; /* very compact height */
    margin-top: 25px !important;  /* minimal gap above footer */
    position: relative;
}

.footer-corporate::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #fffaf3 0%, #fffaf3 50%, #fffaf3 100%);
}

.footer-logo-img {
    height: 28px!important;
    margin-bottom: 12px!important;
    filter: brightness(1.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.footer-logo-img:hover {
    filter: brightness(1.4);
    transform: scale(1.05);
}

.footer-description {
    color: rgba(255, 250, 243, 0.9) !important;
    font-size: 13px !important;
    line-height: 1.6 !important;
    margin-bottom: 12px !important;
    font-family: 'IBM Plex Sans', sans-serif !important;
    max-width: 90% !important;
}

.footer-iso-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 10px 16px !important;
    background: rgba(255, 250, 243, 0.12) !important;
    border: 2px solid rgba(255, 250, 243, 0.35) !important;
    color: #fffaf3 !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer;
    border-radius: 0 !important;
}

.footer-iso-badge:hover {
    background: rgba(255, 250, 243, 0.22) !important;
    border-color: #fffaf3 !important;
    transform: translateX(-4px) !important;
}

.footer-iso-badge i {
    font-size: 14px;
}

/* ISO Certificate Modal Styles */
.iso-certificate-modal {
    display: none !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 99999 !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.iso-certificate-modal.active {
    display: flex !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}

.iso-certificate-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(5px);
}

.iso-certificate-modal-content {
    position: relative !important;
    max-width: 90% !important;
    max-height: 90vh !important;
    background: #fffaf3 !important;
    border: 3px solid #602234 !important;
    box-shadow: 0 20px 60px rgba(96, 34, 52, 0.5) !important;
    z-index: 100000 !important;
    overflow: auto;
    border-radius: 8px;
    margin: auto;
}

.iso-certificate-modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #602234;
    color: #fffaf3;
    border: 2px solid #602234;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.3s ease;
    z-index: 10002;
}

.iso-certificate-modal-close:hover {
    background: #4a1a29;
    border-color: #602234;
    transform: scale(1.1);
}

.iso-certificate-modal-body {
    padding: 20px;
    text-align: center;
}

.iso-certificate-image {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .iso-certificate-modal-content {
        max-width: 95%;
        max-height: 95vh;
    }
    
    .iso-certificate-modal-close {
        top: 10px;
        right: 10px;
        width: 35px;
        height: 35px;
        font-size: 18px;
    }
    
    .iso-certificate-modal-body {
        padding: 15px;
    }
}

.footer-heading {
    color: #fffaf3 !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    margin-bottom: 14px !important;
    padding-bottom: 8px !important;
    border-bottom: 2px solid #fffaf3 !important;
    display: inline-block !important;
    font-family: 'IBM Plex Sans', sans-serif !important;
}

.footer-links-list {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.footer-links-list li {
    margin-bottom: 8px !important;
}

.footer-links-list a {
    color: rgba(255, 250, 243, 0.85) !important;
    text-decoration: none !important;
    font-size: 15px !important;
    line-height: 1.6 !important;
    font-family: 'IBM Plex Sans', sans-serif !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    display: inline-block;
    position: relative;
    padding-left: 0;
}

.footer-links-list a::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 2px;
    background: #fffaf3;
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.footer-links-list a:hover {
    color: #fffaf3 !important;
    padding-left: 20px !important;
}

.footer-links-list a:hover::before {
    width: 14px;
}

.footer-contact-list {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 0 24px 0 !important;
}

.footer-contact-item {
    display: flex !important;
    align-items: flex-start !important;
    gap: 10px !important;
    color: rgba(255, 250, 243, 0.85) !important;
    font-size: 13px !important;
    line-height: 1.5 !important;
    margin-bottom: 10px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer;
}

.footer-contact-item:hover {
    color: #fffaf3 !important;
    transform: translateX(-4px) !important;
}

.footer-contact-item i {
    color: #fffaf3 !important;
    font-size: 14px !important;
    min-width: 18px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.footer-contact-item:hover i {
    transform: scale(1.2);
}

.footer-social-links {
    display: flex !important;
    gap: 14px !important;
    flex-wrap: wrap !important;
}

.footer-social-link {
    width: 36px !important;
    height: 36px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(255, 250, 243, 0.1) !important;
    border: 2px solid #fffaf3 !important;
    color: #fffaf3 !important;
    font-size: 15px !important;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative;
    overflow: hidden;
    border-radius: 0 !important;
}

.footer-social-link::before {
    content: '';
    position: absolute;
    inset: 0;
    background: #fffaf3;
    transform: translateY(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 0;
}

.footer-social-link i {
    position: relative;
    z-index: 1;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color: #fffaf3 !important;
    font-family: "bootstrap-icons", "Bootstrap Icons" !important;
    font-style: normal !important;
    font-weight: normal !important;
    opacity: 1 !important;
    visibility: visible !important;
    display: inline-block !important;
}

.footer-social-link:hover {
    border-color: #fffaf3 !important;
    background: transparent !important;
    transform: translateY(-5px) !important;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5) !important;
}

.footer-social-link:hover::before {
    transform: translateY(0);
}

.footer-social-link:hover i {

    color: #602234 !important;
    transform: scale(1.2) rotate(360deg) !important;
}

.footer-bottom {
    margin-top: 10px !important;  /* minimal gap above bottom bar */
    padding: 5px 0 !important;    /* very small bottom bar height */
    background: rgba(0, 0, 0, 0.2) !important;
    border-top: 2px solid rgba(255, 247, 231, 0.15) !important;
    width: 100% !important;
    position: relative;
    left: 0;
    right: 0;
}

.footer-copyright {
    color: rgba(255, 250, 243, 0.7) !important;
    font-size: 13px !important;
    line-height: 1.6 !important;
    margin: 0 !important;
    font-family: 'IBM Plex Sans', sans-serif !important;
}

.footer-legal-links a {
    color: rgba(255, 250, 243, 0.7) !important;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    margin: 0 10px !important;
    font-size: 13px !important;
}

.footer-legal-links a:hover {
    color: #fffaf3 !important;
    text-decoration: underline !important;
}

/* Responsive Design */
@media (max-width: 992px) {
    .footer-corporate {
        padding: 50px 0 0 !important;
        margin-top: 70px !important;
    }
    
    .footer-logo-img {
        height: 30px !important;
        margin-bottom: 20px !important;
    }
    
    .footer-heading {
        font-size: 14px !important;
        margin-bottom: 20px !important;
        padding-bottom: 10px !important;
    }
    
    .footer-description {
        font-size: 13px !important;
        line-height: 1.7 !important;
        margin-bottom: 18px !important;
    }
    
    .footer-links-list a,
    .footer-contact-item {
        font-size: 13px !important;
    }
    
    .footer-links-list li {
        margin-bottom: 10px !important;
    }
    
    .footer-contact-item {
        margin-bottom: 12px !important;
        gap: 12px !important;
    }
    
    .footer-contact-list {
        margin-bottom: 20px !important;
    }
    
    .footer-iso-badge {
        font-size: 11px !important;
        padding: 8px 14px !important;
    }
}

@media (max-width: 768px) {
    .footer-corporate {
        padding: 40px 0 0 !important;
        margin-top: 60px !important;
    }
    
    .footer-logo-img {
        height: 28px !important;
        margin-bottom: 18px !important;
    }
    
    .footer-description {
        font-size: 13px !important;
        line-height: 1.7 !important;
        margin-bottom: 16px !important;
        max-width: 100% !important;
    }
    
    .footer-iso-badge {
        font-size: 11px !important;
        padding: 8px 12px !important;
    }
    
    .footer-heading {
        margin-top: 28px !important;
        font-size: 14px !important;
        margin-bottom: 18px !important;
        padding-bottom: 10px !important;
    }
    
    .footer-heading:first-of-type {
        margin-top: 0 !important;
    }
    
    .footer-links-list li {
        margin-bottom: 10px !important;
    }
    
    .footer-links-list a {
        font-size: 13px !important;
    }
    
    .footer-contact-item {
        margin-bottom: 12px !important;
        gap: 10px !important;
        font-size: 13px !important;
    }
    
    .footer-contact-item i {
        font-size: 13px !important;
        min-width: 16px !important;
    }
    
    .footer-contact-list {
        margin-bottom: 18px !important;
    }
    
    .footer-social-links {
        gap: 10px !important;
        justify-content: center !important;
    }
    
    .footer-social-link {
        width: 34px !important;
        height: 34px !important;
        font-size: 14px !important;
    }
    
    .footer-bottom {
        text-align: center !important;
        margin-top: 40px !important;
        padding: 16px 0 !important;
    }
    
    .footer-copyright {
        font-size: 12px !important;
        line-height: 1.6 !important;
    }
    
    .footer-legal-links {
        margin-top: 8px !important;
        display: block !important;
    }
    
    .footer-legal-links a {
        font-size: 12px !important;
        margin: 0 6px !important;
    }
    
    /* Center align all columns on mobile */
    .footer-corporate .col-md-6,
    .footer-corporate .col-lg-4,
    .footer-corporate .col-lg-2,
    .footer-corporate .col-lg-3 {
        text-align: center !important;
    }
    
    .footer-corporate .row {
        row-gap: 35px !important;
    }
}

@media (max-width: 576px) {
    .footer-corporate {
        padding: 35px 0 0 !important;
        margin-top: 50px !important;
    }
    
    /* Footer 2 columns on mobile */
    .footer-corporate .col-lg-4,
    .footer-corporate .col-lg-2,
    .footer-corporate .col-lg-3,
    .footer-corporate .col-md-6 {
        flex: 0 0 50% !important;
        max-width: 50% !important;
    }
    
    .footer-logo-img {
        height: 26px !important;
    }
    
    .footer-description {
        font-size: 12px !important;
    }
    
    .footer-heading {
        font-size: 13px !important;
        margin-bottom: 16px !important;
    }
    
    .footer-links-list a,
    .footer-contact-item {
        font-size: 12px !important;
    }
    
    .footer-social-link {
        width: 32px !important;
        height: 32px !important;
        font-size: 13px !important;
    }
    
    .footer-social-links {
        gap: 8px !important;
    }
    
    .footer-bottom {
        padding: 14px 0 !important;
    }
    
    .footer-copyright {
        font-size: 11px !important;
    }
    
    .footer-legal-links a {
        font-size: 11px !important;
        margin: 0 4px !important;
        display: inline-block;
    }
}
</style>

<!-- Footer - Burgundy Corporate Theme -->
<footer class="footer-corporate">
    <div class="container">
        <div class="row g-5">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6">
                <img src="<?php echo ASSETS_URL; ?>/images/logo-light.png" 
                     alt="<?php echo SITE_NAME; ?>" 
                     class="footer-logo-img">
                <p class="footer-description">
                    <?php echo $isArabic 
                        ? 'نيش سوسايتي - الريادة في خدمات الإدارة الفاخرة منذ 25 عاماً. نقدم حلولاً متكاملة لإدارة الممتلكات والفعاليات مع أعلى معايير الجودة والسرية.'
                        : 'Niche Society - Leading luxury management services for 25 years. We provide integrated solutions for property and event management with the highest standards of quality and confidentiality.'; 
                    ?>
                </p>
                <div class="footer-iso-badge" id="isoCertificateBtn" onclick="openIsoCertificateModal()" style="cursor: pointer;">
                    <i class="bi bi-award-fill"></i>
                    <span><?php echo $isArabic ? 'معتمدون' : 'ISO Certified'; ?>: <?php echo ISO_CERTIFICATE_NUMBER; ?></span>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5 class="footer-heading"><?php echo $isArabic ? 'روابط سريعة' : 'Quick Links'; ?></h5>
                <ul class="footer-links-list">
                    <li><a href="<?php echo SITE_URL; ?>/index.php"><?php echo $isArabic ? 'الرئيسية' : 'Home'; ?></a></li>
                    <li><a href="<?php echo SITE_URL; ?>/about.php"><?php echo $isArabic ? 'من نحن' : 'About Us'; ?></a></li>
                    <li><a href="<?php echo SITE_URL; ?>/services.php"><?php echo $isArabic ? 'خدماتنا' : 'Our Services'; ?></a></li>
                    <!-- Success Stories and Blog links removed as requested -->
                    <li><a href="<?php echo SITE_URL; ?>/contact.php"><?php echo $isArabic ? 'اتصل بنا' : 'Contact Us'; ?></a></li>
                </ul>
            </div>
            
            <!-- Services -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-heading"><?php echo $isArabic ? 'خدماتنا' : 'Our Services'; ?></h5>
                <ul class="footer-links-list">
                    <li><a href="<?php echo SITE_URL; ?>/services.php?category=household"><?php echo $isArabic ? 'إدارة الممتلكات' : 'Household Management'; ?></a></li>
                    <li><a href="<?php echo SITE_URL; ?>/services.php?category=events"><?php echo $isArabic ? 'إدارة الفعاليات' : 'Event Management'; ?></a></li>
                    <li><a href="<?php echo SITE_URL; ?>/services.php?category=protocol"><?php echo $isArabic ? 'البروتوكول والإتيكيت' : 'Protocol & Etiquette'; ?></a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-heading"><?php echo $isArabic ? 'معلومات التواصل' : 'Contact Info'; ?></h5>
                <ul class="footer-contact-list">
                    <li class="footer-contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?php echo $isArabic ? 'الرياض، المملكة العربية السعودية' : 'Riyadh, Saudi Arabia'; ?></span>
                    </li>
                    <li class="footer-contact-item">
                        <i class="bi bi-telephone-fill"></i>
                        <span class="phone-number-ltr"><?php echo formatNumber(CONTACT_PHONE); ?></span>
                    </li>
                    <li class="footer-contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <span><?php echo CONTACT_EMAIL; ?></span>
                    </li>
                </ul>
                
                <!-- Social Links -->
                <div class="footer-social-links">
                    <a href="<?php echo SOCIAL_LINKEDIN; ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="<?php echo SOCIAL_TWITTER; ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="Twitter">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Bar - Full Width -->
    <div class="footer-bottom">
        <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-12 text-center">
                        <p class="footer-copyright">
                            &copy; <?php echo $isArabic ? formatNumber(date('Y')) : date('Y'); ?> Niche Society. <?php echo $isArabic ? 'جميع الحقوق محفوظة' : 'All Rights Reserved'; ?>.
                            <span class="footer-legal-links">
                                <a href="<?php echo SITE_URL; ?>/privacy.php"><?php echo $isArabic ? 'سياسة الخصوصية' : 'Privacy Policy'; ?></a> | 
                                <a href="<?php echo SITE_URL; ?>/terms.php"><?php echo $isArabic ? 'الشروط والأحكام' : 'Terms & Conditions'; ?></a>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- ISO Certificate Modal -->
<div id="isoCertificateModal" class="iso-certificate-modal">
    <div class="iso-certificate-modal-overlay" onclick="closeIsoCertificateModal()"></div>
    <div class="iso-certificate-modal-content">
        <button class="iso-certificate-modal-close" id="isoCertificateClose" onclick="closeIsoCertificateModal()">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="iso-certificate-modal-body">
            <img src="<?php echo ASSETS_URL; ?>/images/Certificate.png" alt="ISO 9001:2015 Certificate" class="iso-certificate-image">
        </div>
    </div>
</div>

<!-- ISO Certificate Modal Script (Inline for immediate execution) -->
<script>
// ISO Certificate Modal Functions (Global scope for inline onclick)
function openIsoCertificateModal() {
    console.log('openIsoCertificateModal called');
    const modal = document.getElementById('isoCertificateModal');
    if (modal) {
        console.log('Modal found, adding active class');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        console.error('ISO Certificate Modal not found!');
    }
}

function closeIsoCertificateModal() {
    console.log('closeIsoCertificateModal called');
    const modal = document.getElementById('isoCertificateModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Also attach event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up ISO modal');
    const isoCertificateBtn = document.getElementById('isoCertificateBtn');
    const isoCertificateModal = document.getElementById('isoCertificateModal');
    const isoCertificateClose = document.getElementById('isoCertificateClose');
    
    console.log('ISO Modal Elements:', {
        btn: isoCertificateBtn,
        modal: isoCertificateModal,
        close: isoCertificateClose
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isoCertificateModal && isoCertificateModal.classList.contains('active')) {
            closeIsoCertificateModal();
        }
    });
    
    // Additional overlay click handler
    const overlay = isoCertificateModal?.querySelector('.iso-certificate-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeIsoCertificateModal();
            }
        });
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>

</body>
</html>
