<?php
/**
 * Homepage
 * Niche Society Website
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/helpers.php';

// Handle language switch
handleLanguageSwitch();

// Page meta information
$pageTitle = t('home_title', 'نيش سوسايتي - خدمات الإدارة الفاخرة');
$pageDescription = t('home_description', 'نقدم حلولاً متكاملة لإدارة الممتلكات والفعاليات الفاخرة مع 25 عاماً من الخبرة في خدمة الشخصيات الرفيعة في المملكة العربية السعودية');
$pageKeywords = t('home_keywords', 'إدارة فاخرة، إدارة ممتلكات، إدارة فعاليات، بروتوكول ملكي، خدمات VIP، نيش سوسايتي');

include __DIR__ . '/includes/header.php';

generateMetaTags($pageTitle, $pageDescription, $pageKeywords, 'niche-society-homepage-1-scaled.jpg');
?>

<!-- Main Content -->
<main class="homepage">

<!-- Hero Section -->
<section class="hero-premium">
    <!-- Background Image -->
    <div class="hero-bg-container">
        <div class="hero-bg-image" style="background-image: url('<?php echo ASSETS_URL; ?>/images/niche-society-homepage-1-scaled.jpg');"></div>
        <div class="hero-black-overlay"></div>
    </div>
    
    <!-- Hero Content -->
    <div class="hero-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center">
                    <!-- Badge -->
                    <div class="hero-premium-badge">
                        <div class="badge-line"></div>
                        <span class="badge-year">EST. 2000</span>
                        <div class="badge-line"></div>
                    </div>
                    
                    <!-- Title -->
                    <div class="hero-text-animated">
                        <h1 class="hero-main-title">
                            <?php if (getCurrentLang() === 'ar'): ?>
                                <span class="title-line-1">إدارة <span class="text-gold">احترافية</span></span>
                                <span class="title-line-2">لحياة <span class="text-cream">استثنائية</span></span>
                            <?php else: ?>
                                <span class="title-line-1">Professional <span class="text-gold">Management</span></span>
                                <span class="title-line-2">For Exceptional <span class="text-cream">Lives</span></span>
                            <?php endif; ?>
                        </h1>
                        
                        <!-- Subtitle -->
                        <p class="hero-subtitle">
                            <?php echo getCurrentLang() === 'ar'
                                ? '25 عامًا من التميز في خدمة الشخصيات الرفيعة بخصوصية تامة ومعايير لا تُضاهى'
                                : '25 Years of Excellence Serving Distinguished Clients with Absolute Privacy'; ?>
                        </p>
                    </div>
                    
                    <!-- Stats -->
                    <div class="hero-stats-premium">
                        <div class="stat-premium">
                            <div class="stat-icon"><i class="bi bi-award"></i></div>
                            <div class="stat-content">
                                <div class="stat-value"><?php echo formatNumber('25'); ?>+</div>
                                <div class="stat-text"><?php echo getCurrentLang() === 'ar' ? 'عامًا' : 'Years'; ?></div>
                            </div>
                        </div>
                        <div class="stat-separator"></div>
                        <div class="stat-premium">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-content">
                                <div class="stat-value"><?php echo formatNumber('500'); ?>+</div>
                                <div class="stat-text"><?php echo getCurrentLang() === 'ar' ? 'عميل' : 'Clients'; ?></div>
                            </div>
                        </div>
                        <div class="stat-separator"></div>
                        <div class="stat-premium">
                            <div class="stat-icon"><i class="bi bi-star"></i></div>
                            <div class="stat-content">
                                <div class="stat-value">ISO</div>
                                <div class="stat-text"><?php echo getCurrentLang() === 'ar' ? 'معتمد' : 'Certified'; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA -->
                    <div class="hero-cta-premium">
                        <a href="<?php echo url('services.php'); ?>" class="btn-premium-primary">
                            <span class="btn-text">
                                <i class="bi bi-grid-3x3"></i>
                                <?php echo getCurrentLang() === 'ar' ? 'استكشف خدماتنا' : 'Our Services'; ?>
                            </span>
                        </a>
                        <a href="<?php echo url('contact.php'); ?>" class="btn-premium-outline">
                            <span class="btn-text">
                                <i class="bi bi-envelope"></i>
                                <?php echo getCurrentLang() === 'ar' ? 'تواصل معنا' : 'Contact Us'; ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
                     
 

<!-- About Section -->
<section class="about-section-minimal">
    <div class="container">
        <!-- Section Header -->
        <div class="about-header" data-aos="fade-up">
            <div class="about-decorative-line"></div>
            <div class="about-badge-minimal">
                <span><?php echo getCurrentLang() === 'ar' ? 'من نحن' : 'About Us'; ?></span>
            </div>
            <div class="about-decorative-line"></div>
        </div>
        
        <!-- Main Content -->
        <div class="row justify-content-center">
            <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
                <div class="about-main-content">
                    <h2 class="about-minimal-title">
                        <?php echo getCurrentLang() === 'ar'
                            ? 'شركة متخصصة في تقديم'
                            : 'Specialized in Providing'; ?>
                        <span class="title-emphasis">
                            <?php echo getCurrentLang() === 'ar'
                                ? 'حلول إدارية وتنظيمية استثنائية'
                                : 'Exceptional Management Solutions'; ?>
                        </span>
                    </h2>
                    
                    <div class="about-description-minimal">
                        <p class="lead-text">
                            <?php echo getCurrentLang() === 'ar'
                                ? 'نيش سوسايتي هي شركة متخصصة في تقديم حلول إدارية وتنظيمية بمعايير تعيد تعريف معنى التميز، تشمل الممتلكات الخاصة، العقارات، الإتيكيت والبروتوكولات الرسمية، اللوجستيات، العلاقات العامة، التدريب، والخدمات التشغيلية الراقية.'
                                : 'Niche Society specializes in providing management and organizational solutions that redefine excellence, covering private properties, real estate, etiquette and official protocols, logistics, public relations, training, and premium operational services.'; ?>
                        </p>
                        <p>
                            <?php echo getCurrentLang() === 'ar'
                                ? 'بأكثر من 25 عامًا من الخبرة في خدمة العائلات الملكية والشخصيات البارزة والعملاء الدوليين، نُدير العمليات ونُنسق التفاصيل بأسلوب يجمع بين الدقة، الخصوصية، والأناقة.'
                                : 'With over 25 years of experience serving royal families, high-profile individuals, and international clients, we manage operations and coordinate details in a style that combines precision, privacy, and sophistication.'; ?>
                        </p>
                    </div>
                    
                    <!-- CTA -->
                    <div class="about-cta-minimal">
                        <a href="#" class="btn-minimal-primary" id="aboutLearnMoreBtn" onclick="
                            event.preventDefault();
                            event.stopPropagation();
                            const modal = document.getElementById('aboutModal');
                            if (modal) {
                                modal.style.display = 'block';
                                modal.style.visibility = 'visible';
                                modal.style.opacity = '1';
                                modal.classList.add('modal-visible');
                                modal.classList.remove('modal-hidden');
                                setTimeout(function() {
                                    modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }, 50);
                            }
                            return false;
                        ">
                            <span><?php echo getCurrentLang() === 'ar' ? 'اعرف المزيد عنا' : 'Learn More About Us'; ?></span>
                            <i class="bi bi-arrow-<?php echo getCurrentLang() === 'ar' ? 'left' : 'right'; ?>"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Modal / Expanded Section -->
<section class="about-modal-section" id="aboutModal" style="display: none; opacity: 0; visibility: hidden;">
    <!-- Inline script for close function (ensures it's available immediately) -->
    <script>
    function closeAboutModalHandler() {
        console.log('closeAboutModalHandler called - redirecting to home');
        window.location.href = 'index.php';
        return false;
    }
    </script>
    <div class="container">
        <!-- Close Button -->
        <div class="row">
            <div class="col-12 text-end mb-4">
                <button type="button" class="btn-close-modal" id="closeAboutModal" onclick="closeAboutModalHandler(); return false;" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="#602234" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- CEO Section and Company Information - Balanced Layout -->
        <div class="row align-items-start g-4">
            <!-- CEO Section - First (Left on desktop, Top on mobile) -->
            <div class="col-lg-5 order-lg-1 order-1" data-aos="fade-up" data-aos-delay="200">
                <div class="ceo-card-small">
                    <!-- CEO Image -->
                    <div class="ceo-card-image-wrapper">
                        <img src="<?php echo getImageUrl('Niche-Society-Arabic-ceo-1-661x1024.png'); ?>" alt="<?php echo getCurrentLang() === 'ar' ? 'المدير التنفيذي' : 'CEO'; ?>" class="ceo-portrait-small">
                    </div>
                    
                    <!-- CEO Quotes -->
                    <div class="ceo-card-body">
                        <div class="ceo-quotes">
                            <p class="ceo-quote-text">
                                <?php echo getCurrentLang() === 'ar'
                                    ? 'وُلدت نيش سوسايتي من شغفي العميق للتحدي، ورغبة لا تهدأ في ابتكار حلول عصرية تُترجم أعلى معايير الجودة والدقة. لطالما كان بناء الأنظمة، ومتابعة تفاصيل التفاصيل، وتحقيق نتائج ملموسة هو ما يُلهمني ويدفعني للاستمرار. نيش سوسايتي ليست مجرد مشروع، بل ثمرة سنوات من التجربة والتنوع الثقافي. تأسست لتقدم خدمات ترتقي بالخصوصية، وتُعزز الإنتاجية بأعلى درجات الحرفية والأناقة. لأن ثقتكم وابتسامتكم.. هما مصدر فخرنا، وأساس قوة استمرارنا.'
                                    : 'Niche Society was born from my deep passion for challenge, and an unceasing desire to create modern solutions that translate the highest standards of quality and precision. Building systems, following up on the smallest details, and achieving tangible results have always been what inspires me and drives me to continue. Niche Society is not just a project, but the fruit of years of experience and cultural diversity. It was founded to provide services that elevate privacy and enhance productivity with the highest degree of craftsmanship and elegance. Because your trust and your smile.. are the source of our pride, and the foundation of our strength to continue.'; ?>
                            </p>
                        </div>
                        
                        <!-- CEO Signature -->
                        <div class="ceo-signature-block">
                            <div class="ceo-name-signature">
                                <strong><?php echo getCurrentLang() === 'ar' ? 'خديجة' : 'Khadeeja'; ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Company Information Section - Second (Right on desktop, Bottom on mobile) -->
            <div class="col-lg-7 order-lg-2 order-2" data-aos="fade-up" data-aos-delay="300">
                <div class="content-box">
                    <h2 class="section-title">
                        <?php echo getCurrentLang() === 'ar' ? 'من نحن؟' : 'Who are we?'; ?>
                    </h2>
                    <div class="divider"></div>
                    <p class="lead-text">
                        <?php echo getCurrentLang() === 'ar'
                            ? 'نيش سوسايتي شركة متخصصة في تقديم حلول إدارية وتنظيمية بمعايير تعيد تعريف معنى التميز، تشمل الممتلكات الخاصة، العقارات، الإتيكيت والبروتوكولات الرسمية، اللوجستيات، العلاقات العامة، والخدمات التشغيلية الراقية.'
                            : 'Niche Society is a company specialized in providing administrative and organizational solutions with standards that redefine the meaning of excellence, including private property, real estate, official etiquette and protocols, logistics, public relations, and high-end operational services.'; ?>
                    </p>
                    <p>
                        <?php echo getCurrentLang() === 'ar'
                            ? 'بأكثر من 25 عامًا من الخبرة في خدمة الشخصيات الرفيعة والعملاء الدوليين، نُدير العمليات ونُنسق التفاصيل بأسلوب يجمع بين الدقة، الخصوصية، والرقي؛ لأن العملاء الاستثنائيين لا يبحثون عن خدمة، بل عن تجربة تُدار بثقة وتُنفذ بسلاسة وبأسلوب استثنائي يشبههم.'
                            : 'With over 25 years of experience serving high-profile individuals and international clients, we manage operations and coordinate details in a style that combines precision, privacy, and sophistication. Exceptional clients are not looking for service, but for an experience that is confidently managed and executed with an exceptionally smooth style similar to theirs.'; ?>
                    </p>
                    <p>
                        <?php echo getCurrentLang() === 'ar'
                            ? 'نُدير كل شيء من خلف الكواليس وننسق كل ما هو ظاهر بأسلوب شامل يبدأ من الأساس ولا ينتهي عند التفاصيل. نحن نُحوّل كل موقع إلى منظومة متكاملة تُدار بكفاءة وتُقدّم برقي، لأننا لا نقدّم حلولًا جاهزة، بل نُعيد هندسة كل تجربة من الصفر ونرفعها إلى أعلى المعايير.'
                            : 'We manage everything behind the scenes, and coordinate everything that is visible, in a comprehensive manner that begins with the foundation and does not end with the details. We transform each site into an integrated system that is efficiently managed and presented with sophistication, because we do not present ready-made solutions, but rather we re-engineer each experience from scratch and raise it to the highest standards.'; ?>
                    </p>
                    <p>
                        <?php echo getCurrentLang() === 'ar'
                            ? 'نؤمن أن الفخامة الحقيقية تنبع من الطمأنينة، وأن التميز يُقاس بالإتقان. منهجيتنا تجمع بين كفاءة التشغيل وتنسيق نمط الحياة لتقديم خدمات تنسجم مع أسلوب حياة ذوي الذوق الرفيع، وتعكس أعلى درجات الراحة، الخصوصية، والأناقة.'
                            : 'We believe that true luxury stems from tranquility, and that excellence is measured by mastery. Our approach combines operational efficiency and life coordination to provide services that blend with the lifestyle of people of refined taste and reflect the highest levels of comfort, privacy, and elegance.'; ?>
                    </p>
                    <p class="mb-0">
                        <?php echo getCurrentLang() === 'ar'
                            ? 'من الإدارة اليومية إلى التنسيق المتخصص، لا تقدم نيش سوسايتي مجرد خدمة، بل تبني علاقة قائمة على الثقة، وتجسّد الفخامة الهادئة، وتمنح راحة البال في كل تفصيلة.'
                            : 'From day-to-day management to specialized coordination, Niche Society doesn\'t just provide a service, it builds a relationship based on trust, embodies quiet luxury, and provides peace of mind in every detail.'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Philosophy Section: Vision, Goals & Values -->
<section class="philosophy-section">
    <div class="philosophy-bg-pattern"></div>
    <div class="container">
        <!-- Artistic Header -->
        <div class="philosophy-header">
            <div class="philosophy-intro">
                <span class="philosophy-badge"><?php echo t('pillars_badge', 'ما يحركنا'); ?></span>
                <h2 class="philosophy-main-title"><?php echo t('philosophy_main_title', 'فلسفتنا'); ?></h2>
                <p class="philosophy-subtitle"><?php echo t('philosophy_subtitle', 'نحن لا نقدم خدمات فحسب، بل نبني إرثًا من التميز والاحترافية'); ?></p>
            </div>
        </div>
        
        <!-- Pillars Grid -->
        <div class="philosophy-grid">
            <!-- Vision - Featured Large Card -->
            <div class="philosophy-item vision-item">
                <div class="philosophy-card vision-card">
                    <div class="card-number"><?php echo formatNumber('01'); ?></div>
                    <div class="card-corner top-left"></div>
                    <div class="card-corner bottom-right"></div>
                    <div class="philosophy-content">
                        <h3 class="philosophy-card-title"><?php echo t('vision_title', 'رؤيتنا'); ?></h3>
                        <div class="title-underline"></div>
                        <p class="philosophy-text">
                            <?php echo t('vision_text', 'أن نكون المرجع الأول في تقديم حلول إدارية وتنظيمية تُعيد تعريف معنى الإدارة الراقية، عبر خدمات تُنفّذ بهدوء، وتُصمّم حسب السياق، وتُدار بثقة. كما نطمح إلى تحويل كل موقع وكل دور إلى منظومة متكاملة تُجسّد الفخامة الهادئة، وتُعبر عن أصحابها، لأننا لا نُقدّم خدمات جاهزة، بل نُهندس التجربة من الصفر، ونرتقي بها إلى مستوى غير مسبوق.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Goals - Top Right -->
            <div class="philosophy-item goals-item">
                <div class="philosophy-card goals-card">
                    <div class="card-number"><?php echo formatNumber('02'); ?></div>
                    <div class="card-corner top-left"></div>
                    <div class="card-corner bottom-right"></div>
                    <div class="philosophy-content">
                        <h3 class="philosophy-card-title"><?php echo t('goals_title', 'أهدافنا'); ?></h3>
                        <div class="title-underline"></div>
                        <p class="philosophy-text">
                            <?php echo t('goals_text', 'أن نُبسّط التعقيد، ونُعيد ترتيب المشهد بأسلوب يُضيف قيمة حقيقية لكل لحظة، وأن نُقدّم خدمات غير محدودة تشمل إدارة العمليات، التنسيق اللوجستي، تنظيم البروتوكولات، والدعم التنفيذي، من خلف الكواليس وحتى أدق التفاصيل، لنحوّل كل تجربة إلى رحلة سلسة، راقية، ومُصمّمة حسب رؤية العميل وتطلعاته.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Values - Bottom Right -->
            <div class="philosophy-item values-item">
                <div class="philosophy-card values-card">
                    <div class="card-number"><?php echo formatNumber('03'); ?></div>
                    <div class="card-corner top-left"></div>
                    <div class="card-corner bottom-right"></div>
                    <div class="philosophy-content">
                        <h3 class="philosophy-card-title"><?php echo t('values_title', 'قيمنا'); ?></h3>
                        <div class="title-underline"></div>
                        <p class="philosophy-text">
                            <?php echo t('values_text', '١. الخصوصية: نُدير كل شيء من خلف الكواليس، باحترام تام للمساحات الشخصية، لأن الهدوء هو جوهر الفخامة، والثقة تبدأ من الصمت المدروس. ٢. الدقة: نُنفّذ كل مهمة وكأنها الأولى، ونُعامل كل تفصيلة وكأنها الأهم، لأن الإتقان ليس خيارًا، بل أسلوب عمل. ٣. الثقة: نبنيها عبر أداء لا يتغيّر، وشفافية لا تُطلب، لأن العلاقة مع نيش سوسايتي تبدأ بالاطمئنان، وتستمر بالثبات. ٤. الفخامة: ليست في الشكل، بل في الشعور. نُصمّم كل تجربة لتُشبه أصحابها، وتُعبّر عنهم، وتُقدَّم بروح راقية ولمسة محسوبة. ٥. الانسجام: كل ما في نيش سوسايتي يعمل بتناغم: الفرق، الأنظمة، والخدمات؛ لأننا لا نُدير فقط، بل نُنسّق الحياة لتتحوّل إلى تجربة استثنائية تعبر عن أصحابها.'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section-elegant">
    <div class="container">
        <!-- Section Header -->
        <div class="services-header-elegant" data-aos="fade-up">
            <span class="services-badge-elegant"><?php echo t('services_badge', 'خدماتنا'); ?></span>
            <h2 class="services-title-elegant"><?php echo t('services_title', 'حلول متكاملة لإدارة استثنائية'); ?></h2>
            <div class="services-decorative-line">
                <span class="line-dot"></span>
                <span class="line-dot"></span>
                <span class="line-dot"></span>
            </div>
        </div>
        
        <!-- Services Grid -->
        <div class="services-grid-elegant">
            <!-- Service 1: Smart Property Management -->
            <div class="service-card-elegant" data-aos="fade-up" data-aos-delay="100">
                <div class="service-image-elegant">
                    <div class="service-badge-number"><?php echo formatNumber('01'); ?></div>
                    <!-- Swapped image: now using former Event Management visual -->
                    <img src="<?php echo getImageUrl('service.png'); ?>" alt="<?php echo t('service1_title', 'خدمات الإدارة الذكية لممتلكاتك'); ?>">
                    <div class="service-image-overlay"></div>
                </div>
                <div class="service-content-elegant">
                    <h3 class="service-title-elegant"><?php echo t('service1_title', 'خدمات الإدارة الذكية لممتلكاتك'); ?></h3>
                    <p class="service-description-elegant">
                        <?php echo t('service1_desc', 'نحوّل كل موقع إلى منظومة متكاملة تُدار بأنظمة ذكية وتُنسّق بتواصل فعّال، لتبقى على اطلاع دائم بكل التفاصيل دون عناء أو تعقيد.'); ?>
                    </p>
                    <ul class="service-features-elegant">
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service1_feat1', 'تأسيس نظام داخلي شامل لكافة الأعمال'); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service1_feat2', 'إدارة العمليات اليومية وتقييمها'); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service1_feat3', 'لوحات تحكم رقمية وتقارير مخصصة'); ?>
                        </li>
                    </ul>
                    <a href="<?php echo url('service-household-management.php'); ?>" class="service-link-elegant">
                        <span><?php echo t('learn_more', 'اعرف المزيد'); ?></span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M<?php echo getCurrentLang() === 'ar' ? '12 4L6 10L12 16' : '8 4L14 10L8 16'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Service 2: Event Management -->
            <div class="service-card-elegant" data-aos="fade-up" data-aos-delay="200">
                <div class="service-image-elegant">
                    <div class="service-badge-number"><?php echo formatNumber('02'); ?></div>
                    <img src="<?php echo getImageUrl('service-5.jpg'); ?>" alt="<?php echo t('service2_title', 'إدارة الفعاليات'); ?>">
                    <div class="service-image-overlay"></div>
                </div>
                <div class="service-content-elegant">
                    <h3 class="service-title-elegant"><?php echo t('service2_title', 'إدارة الفعاليات'); ?></h3>
                    <p class="service-description-elegant">
                        <?php echo t('service2_desc', 'من اللقاءات الخاصة إلى الاحتفالات الكبرى، نُصمّم الانطباع العام، ونُشرف على كل تفصيلة لتنعكس الأناقة في الجو، والخصوصية في التنفيذ.'); ?>
                    </p>
                    <ul class="service-features-elegant">
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service2_feat1', 'تصميم تجارب متخصصة لكل نوع من الفعاليات'); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service2_feat2', 'إدارة الخدمات اللوجستية بدقة وكفاءة'); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service2_feat3', 'إشراف ميداني وتنسيق فريق محترف'); ?>
                        </li>
                    </ul>
                    <a href="<?php echo url('service-event-management.php'); ?>" class="service-link-elegant">
                        <span><?php echo t('learn_more', 'اعرف المزيد'); ?></span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M<?php echo getCurrentLang() === 'ar' ? '12 4L6 10L12 16' : '8 4L14 10L8 16'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Service 3: Protocol & Etiquette -->
            <div class="service-card-elegant" data-aos="fade-up" data-aos-delay="300">
                <div class="service-image-elegant">
                    <div class="service-badge-number"><?php echo formatNumber('03'); ?></div>
                    <img src="<?php echo getImageUrl('service-6.jpg'); ?>" alt="<?php echo t('service3_title', 'البروتوكول وفن الإتيكيت'); ?>">
                    <div class="service-image-overlay"></div>
                </div>
                <div class="service-content-elegant">
                    <h3 class="service-title-elegant"><?php echo t('service3_title', 'البروتوكول وفن الإتيكيت'); ?></h3>
                    <p class="service-description-elegant">
                        <?php echo t('service3_desc', 'نقدم برامج استشارية وتدريبية مخصصة لتعزيز التواصل والسلوك في الإطارات الرسمية واليومية، مع التركيز على الأسلوب الشخصي والحضور الدبلوماسي.'); ?>
                    </p>
                    <ul class="service-features-elegant">
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service3_feat1', 'برامج تدريبية لفن التواصل الدبلوماسي'); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service3_feat2', 'استشارات في السلوك الرسمي والعلاقات العامة'); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="12" height="12" stroke="#602234" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#602234" stroke-width="1.5"/>
                            </svg>
                            <?php echo t('service3_feat3', 'تطوير الحضور والأسلوب الشخصي المهني'); ?>
                        </li>
                    </ul>
                    <a href="<?php echo url('service-protocol-etiquette.php'); ?>" class="service-link-elegant">
                        <span><?php echo t('learn_more', 'اعرف المزيد'); ?></span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M<?php echo getCurrentLang() === 'ar' ? '12 4L6 10L12 16' : '8 4L14 10L8 16'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- View All Button -->
        <div class="services-footer-elegant" data-aos="fade-up" data-aos-delay="400">
            <a href="services.php" class="view-all-services-btn">
                <?php echo t('view_all_services', 'عرض جميع الخدمات'); ?>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M<?php echo getCurrentLang() === 'ar' ? '15 6L9 12L15 18' : '9 6L15 12L9 18'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- ISO Certification Banner -->
<section class="iso-banner">
    <div class="container">
        <div class="iso-grid">
            <div class="iso-item" data-aos="fade-up" data-aos-delay="0">
                <div class="iso-number"><?php echo t('iso_years', '+25 عامًا'); ?></div>
                <div class="iso-label"><?php echo t('iso_years_desc', 'من التميز في الخدمة'); ?></div>
            </div>
            
            <div class="iso-item iso-highlight" data-aos="fade-up" data-aos-delay="100">
                <div class="iso-icon">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 4L27.09 16.26L39.17 12.83L32.49 23.51L44 27.5L33.09 33.74L36.52 45.83L24 39.15L11.48 45.83L14.91 33.74L4 27.5L15.51 23.51L8.83 12.83L20.91 16.26L24 4Z" fill="#602234" stroke="#602234" stroke-width="2"/>
                    </svg>
                </div>
                <div class="iso-number"><?php echo t('iso_certified', 'معتمدون ISO 9001'); ?></div>
                <div class="iso-label"><?php echo t('iso_cert_number', 'شهادة رقم: 25EQQN01'); ?></div>
            </div>
            
            <div class="iso-item" data-aos="fade-up" data-aos-delay="200">
                <div class="iso-number"><?php echo t('iso_royal', 'عملاء ملكيون'); ?></div>
                <div class="iso-label"><?php echo t('iso_royal_desc', 'موثوقون من قبل عائلات رفيعة'); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="section-badge" data-aos="fade-up"><?php echo t('stats_badge', 'تأثيرنا'); ?></div>
        <h2 class="section-title" data-aos="fade-up" data-aos-delay="100"><?php echo t('stats_title', 'التميز المثبت بالأرقام'); ?></h2>
        
        <div class="stats-grid">
            <div class="stat-item" data-aos="fade-up" data-aos-delay="0">
                <div class="stat-number"><?php echo t('stat1_number', '+25'); ?></div>
                <div class="stat-label"><?php echo t('stat1_label', 'عامًا من الخبرة'); ?></div>
            </div>
            
            <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-number"><?php echo t('stat2_number', '+100'); ?></div>
                <div class="stat-label"><?php echo t('stat2_label', 'عقار تحت الإدارة'); ?></div>
            </div>
            
            <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-number"><?php echo t('stat3_number', '+500'); ?></div>
                <div class="stat-label"><?php echo t('stat3_label', 'فعالية منفذة بنجاح'); ?></div>
            </div>
            
            <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-number"><?php echo t('stat4_number', '99%'); ?></div>
                <div class="stat-label"><?php echo t('stat4_label', 'رضا العملاء'); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-section">
    <div class="container">
        <div class="section-badge" data-aos="fade-up"><?php echo t('why_badge', 'لماذا نيش سوسايتي'); ?></div>
        <h2 class="section-title" data-aos="fade-up" data-aos-delay="100"><?php echo t('why_title', 'فرق نيش سوسايتي'); ?></h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200"><?php echo t('why_subtitle', 'نحن لا نكتفي بالإدارة، بل نهندس تجارب استثنائية'); ?></p>
        
        <div class="why-grid">
            <div class="why-card" data-aos="fade-up" data-aos-delay="0">
                <div class="why-icon">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="8" y="8" width="24" height="24" stroke="#602234" stroke-width="2"/>
                        <path d="M15 20L18 23L25 16" stroke="#602234" stroke-width="2"/>
                    </svg>
                </div>
                <h3><?php echo t('why1_title', 'سرية مطلقة'); ?></h3>
                <p><?php echo t('why1_desc', 'نعمل من وراء الكواليس مع احترام تام للخصوصية والمساحات الشخصية'); ?></p>
            </div>
            
            <div class="why-card" data-aos="fade-up" data-aos-delay="100">
                <div class="why-icon">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="12" stroke="#602234" stroke-width="2"/>
                        <circle cx="20" cy="20" r="6" stroke="#602234" stroke-width="2"/>
                        <circle cx="20" cy="20" r="2" fill="#602234"/>
                    </svg>
                </div>
                <h3><?php echo t('why2_title', 'دقة لا تقبل التنازل'); ?></h3>
                <p><?php echo t('why2_desc', 'ننفذ كل مهمة وكأنها الأولى، ونتعامل مع كل تفصيلة وكأنها الأهم'); ?></p>
            </div>
            
            <div class="why-card" data-aos="fade-up" data-aos-delay="200">
                <div class="why-icon">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 8L28 8L32 12L32 28L28 32L12 32L8 28L8 12L12 8Z" stroke="#602234" stroke-width="2"/>
                        <path d="M16 20L20 24L24 16" stroke="#602234" stroke-width="2"/>
                    </svg>
                </div>
                <h3><?php echo t('why3_title', 'حلول مخصصة'); ?></h3>
                <p><?php echo t('why3_desc', 'خدمات مصممة خصيصًا بحسب رؤيتكم وطموحاتكم'); ?></p>
            </div>
            
            <div class="why-card" data-aos="fade-up" data-aos-delay="300">
                <div class="why-icon">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="14" stroke="#602234" stroke-width="2"/>
                        <path d="M20 10V20L26 26" stroke="#602234" stroke-width="2"/>
                    </svg>
                </div>
                <h3><?php echo t('why4_title', 'تميز على مدار الساعة'); ?></h3>
                <p><?php echo t('why4_desc', 'دعم مستمر وتوفر دائم عندما تحتاجوننا'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Team Showcase Section -->
<section class="team-section">
    <div class="container">
        <div class="team-content">
            <div class="team-text" data-aos="fade-<?php echo getCurrentLang() === 'ar' ? 'left' : 'right'; ?>">
                <div class="section-badge"><?php echo t('team_badge', 'فريقنا'); ?></div>
                <h2><?php echo t('team_title', 'محترفون ملتزمون بالتميز'); ?></h2>
                <p><?php echo t('team_desc', 'خبراء متخصصون في إدارة الممتلكات الفاخرة والفعاليات الرفيعة. نعمل بصمت، ننفذ بدقة، ونسلم بتميز.'); ?></p>
                <a href="javascript:void(0)" class="btn-primary">
                    <?php echo t('team_cta', 'تعرف على فريقنا'); ?>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M<?php echo getCurrentLang() === 'ar' ? '12 4L6 10L12 16' : '8 4L14 10L8 16'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                    </svg>
                </a>
            </div>
            
            <div class="team-image" data-aos="fade-<?php echo getCurrentLang() === 'ar' ? 'right' : 'left'; ?>">
                <img src="assets/images/TEAM-scaled.jpg" alt="<?php echo t('team_title', 'محترفون ملتزمون بالتميز'); ?>">
            </div>
        </div>
    </div>
</section>

<!-- Final Call-to-Action Section -->
<section class="final-cta-section">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <div class="section-badge"><?php echo t('cta_badge', 'ابدأ الآن'); ?></div>
            <h2><?php echo t('cta_title', 'هل أنتم مستعدون لتجربة إدارة استثنائية؟'); ?></h2>
            <p><?php echo t('cta_subtitle', 'تواصلوا معنا اليوم لنبدأ رحلة تحويل ممتلكاتكم وفعالياتكم إلى تجارب استثنائية'); ?></p>
            
            <div class="cta-actions">
                <a href="#contact" class="btn-primary btn-large">
                    <?php echo t('cta_button', 'احصل على استشارة مجانية'); ?>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M<?php echo getCurrentLang() === 'ar' ? '14 6L8 12L14 18' : '10 6L16 12L10 18'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                    </svg>
                </a>
                
                <div class="cta-or"><?php echo t('cta_or', 'أو'); ?></div>
                
                <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn-secondary btn-large">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 7H15" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="16" r="1.5" fill="currentColor"/>
                    </svg>
                    <?php echo t('cta_call', 'اتصل بنا الآن'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

</main>

<!-- Lock hero title font-weight at 200 -->
<script>
(function() {
    // Function to lock font-weight at 200
    function lockHeroTitleWeight() {
        const heroTitle = document.querySelector('.hero-main-title');
        if (heroTitle) {
            // Lock font-weight for the main element
            heroTitle.style.fontWeight = '200';
            heroTitle.style.setProperty('font-weight', '200', 'important');
            
            // Lock font-weight for all children
            const allChildren = heroTitle.querySelectorAll('*');
            allChildren.forEach(function(child) {
                child.style.fontWeight = '200';
                child.style.setProperty('font-weight', '200', 'important');
            });
        }
    }
    
    // Run immediately
    lockHeroTitleWeight();
    
    // Run after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', lockHeroTitleWeight);
    } else {
        lockHeroTitleWeight();
    }
    
    // Run after fonts are loaded
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function() {
            lockHeroTitleWeight();
        });
    }
    
    // Run after a short delay to catch any late font loading
    setTimeout(lockHeroTitleWeight, 100);
    setTimeout(lockHeroTitleWeight, 500);
    setTimeout(lockHeroTitleWeight, 1000);
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
