<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// Handle language switch
handleLanguageSwitch();

// Get current language
$lang = getCurrentLang();
$dir = getTextDirection($lang);

// Page settings
$currentPage = 'services';
$pageTitle = $lang === 'ar' ? 'خدمة الكونسيرج VIP - نيش سوسايتي' : 'VIP Logistics & Consulting Service - Niche Society';
$pageDescription = $lang === 'ar' ? 'مساعدة شخصية حصرية على مدار الساعة لتلبية جميع احتياجاتكم ورغباتكم بأعلى معايير الخدمة والسرية.' : 'Exclusive 24/7 personal assistance to meet all your needs and desires with the highest standards of service and discretion.';

// CSRF token for contact form
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'includes/header.php';
?>

<!-- Sticky Back Button -->
<a href="<?= url('services.php') ?>#service-5" class="back-button back-button-sticky">
    <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
    <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
</a>

<!-- Back Button & Page Header -->
<section class="service-detail-header">
    <div class="container">
        <div class="service-detail-nav">
            <a href="<?= url('services.php') ?>#service-5" class="back-button">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
            </a>
        </div>
        <div class="service-detail-title-section">
            <div class="service-badge-header"><?php echo formatNumber('05'); ?></div>
            <h1 class="service-detail-title"><?php echo $lang === 'ar' ? 'خدمة الكونسيرج VIP' : 'VIP Logistics & Consulting Service'; ?></h1>
            <p class="service-detail-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'مساعدة شخصية حصرية على مدار الساعة لتلبية جميع احتياجاتكم ورغباتكم' 
                    : 'Exclusive 24/7 personal assistance to meet all your needs and desires'; ?>
            </p>
            <div class="service-meta-badges">
                <span class="meta-badge"><i class="bi bi-award-fill"></i> <?php echo $lang === 'ar' ? 'معتمد ISO 9001' : 'ISO 9001 Certified'; ?></span>
                <span class="meta-badge"><i class="bi bi-clock-fill"></i> <?php echo $lang === 'ar' ? 'متاح 24/7' : '24/7 Available'; ?></span>
                <span class="meta-badge"><i class="bi bi-shield-check-fill"></i> <?php echo $lang === 'ar' ? 'سرية تامة' : 'Complete Discretion'; ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Service Overview -->
<section class="section bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <h2 class="section-title"><?php echo $lang === 'ar' ? 'نظرة عامة على الخدمة' : 'Service Overview'; ?></h2>
                <p class="lead">
                    <?php echo $lang === 'ar' 
                        ? 'نقدم خدمة كونسيرج شاملة ومخصصة للعملاء المميزين الذين يبحثون عن راحة تامة وخدمة استثنائية. فريقنا المتخصص متاح على مدار الساعة لتلبية أي طلب، مهما كان معقداً أو فريداً.' 
                        : 'We provide comprehensive and customized concierge services for distinguished clients seeking complete comfort and exceptional service. Our specialized team is available 24/7 to fulfill any request, no matter how complex or unique.'; ?>
                </p>
                <p>
                    <?php echo $lang === 'ar' 
                        ? 'من تنسيق السفر الفاخر إلى إدارة نمط الحياة الشخصي، نحن نتعامل مع كل التفاصيل حتى تتمكن من التركيز على ما يهمك حقاً. خبرتنا تمتد لأكثر من 25 عاماً في خدمة الشخصيات الرفيعة.' 
                        : 'From luxury travel coordination to personal lifestyle management, we handle every detail so you can focus on what truly matters. Our experience spans over 25 years serving distinguished personalities.'; ?>
                </p>
                <div class="service-stats mt-4">
                    <div class="stat-box">
                        <h3><?php echo formatNumber('24'); ?>/<?php echo formatNumber('7'); ?></h3>
                        <p><?php echo $lang === 'ar' ? 'متاح دائماً' : 'Always Available'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo formatNumber('1000'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'طلب مُنفذ' : 'Requests Fulfilled'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>100%</h3>
                        <p><?php echo $lang === 'ar' ? 'معدل الرضا' : 'Satisfaction Rate'; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="service-image-wrapper">
                    <img src="<?= url('assets/images/service-2-914x1024.png') ?>" alt="<?php echo $lang === 'ar' ? 'خدمة الكونسيرج VIP' : 'VIP Concierge Service'; ?>" class="img-fluid service-detail-img">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Key Features -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'ما نقدمه لك' : 'What We Offer'; ?></h2>
            <p class="section-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'خدمات شاملة تغطي جميع جوانب نمط حياتك' 
                    : 'Comprehensive services covering all aspects of your lifestyle'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $features = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:airplane.svg?color=%23602234',
                    'title_en' => 'Luxury Travel Coordination',
                    'title_ar' => 'تنسيق السفر الفاخر',
                    'desc_en' => 'Private jet arrangements, luxury hotel bookings, exclusive experiences, and seamless travel logistics.',
                    'desc_ar' => 'ترتيبات الطائرات الخاصة، حجوزات الفنادق الفاخرة، تجارب حصرية، ولوجستيات سفر سلسة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:silverware-fork-knife.svg?color=%23602234',
                    'title_en' => 'Exclusive Dining & Reservations',
                    'title_ar' => 'المطاعم الحصرية والحجوزات',
                    'desc_en' => 'Access to exclusive restaurants, private chef services, and culinary experiences.',
                    'desc_ar' => 'الوصول إلى المطاعم الحصرية، خدمات الشيف الخاص، والتجارب الطهوية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:shopping.svg?color=%23602234',
                    'title_en' => 'Luxury Shopping & Personal Shopping',
                    'title_ar' => 'التسوق الفاخر والتسوق الشخصي',
                    'desc_en' => 'Personal shopping services, exclusive brand access, custom fittings, and gift procurement.',
                    'desc_ar' => 'خدمات التسوق الشخصي، الوصول الحصري للعلامات التجارية، القياسات المخصصة، وشراء الهدايا.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:ticket.svg?color=%23602234',
                    'title_en' => 'Event & Entertainment Access',
                    'title_ar' => 'الوصول للفعاليات والترفيه',
                    'desc_en' => 'VIP tickets to exclusive events, private concerts, cultural experiences, and entertainment access.',
                    'desc_ar' => 'تذاكر VIP للفعاليات الحصرية، الحفلات الخاصة، التجارب الثقافية، والوصول للترفيه.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:spa.svg?color=%23602234',
                    'title_en' => 'Wellness & Personal Care',
                    'title_ar' => 'العافية والعناية الشخصية',
                    'desc_en' => 'Spa appointments, wellness retreats, personal trainers, nutritionists, and health consultations.',
                    'desc_ar' => 'مواعيد السبا، منتجعات العافية، المدربين الشخصيين، أخصائيي التغذية، واستشارات الصحة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:gift.svg?color=%23602234',
                    'title_en' => 'Gift Services & Special Occasions',
                    'title_ar' => 'خدمات الهدايا والمناسبات الخاصة',
                    'desc_en' => 'Thoughtful gift selection, custom gift wrapping, surprise arrangements, and special occasion planning.',
                    'desc_ar' => 'اختيار الهدايا المدروسة، تغليف الهدايا المخصص، ترتيبات المفاجآت، وتخطيط المناسبات الخاصة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:calendar-check.svg?color=%23602234',
                    'title_en' => 'Personal Schedule Management',
                    'title_ar' => 'إدارة الجدول الشخصي',
                    'desc_en' => 'Calendar coordination, appointment scheduling, reminder services, and time management optimization.',
                    'desc_ar' => 'تنسيق التقويم، جدولة المواعيد، خدمات التذكير، وتحسين إدارة الوقت.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:file-document.svg?color=%23602234',
                    'title_en' => 'Document & Administrative Services',
                    'title_ar' => 'خدمات الوثائق والإدارة',
                    'desc_en' => 'Document preparation, visa assistance, legal document coordination, and administrative support.',
                    'desc_ar' => 'إعداد الوثائق، مساعدة التأشيرات، تنسيق الوثائق القانونية، والدعم الإداري.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:briefcase.svg?color=%23602234',
                    'title_en' => 'Business & Professional Services',
                    'title_ar' => 'الخدمات التجارية والمهنية',
                    'desc_en' => 'Business meeting coordination, networking facilitation, corporate event planning, and professional support.',
                    'desc_ar' => 'تنسيق الاجتماعات التجارية، تسهيل التواصل، تخطيط الفعاليات التجارية، والدعم المهني.'
                ]
            ];

            foreach ($features as $index => $feature) :
            ?>
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="feature-card">
                    <div class="feature-icon">
                        <?php if (isset($feature['icon_url'])): ?>
                            <img src="<?php echo $feature['icon_url']; ?>" alt="<?php echo $lang === 'ar' ? $feature['title_ar'] : $feature['title_en']; ?>" class="feature-icon-img">
                        <?php elseif (isset($feature['icon'])): ?>
                            <i class="fas <?php echo $feature['icon']; ?>"></i>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo $lang === 'ar' ? $feature['title_ar'] : $feature['title_en']; ?></h3>
                    <p><?php echo $lang === 'ar' ? $feature['desc_ar'] : $feature['desc_en']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Service Levels Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'مستويات الخدمة' : 'Service Levels'; ?></h2>
            <p class="section-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'باقات مخصصة تناسب احتياجاتك' 
                    : 'Customized packages to suit your needs'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $levels = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:star.svg?color=%23602234',
                    'title_en' => 'Essential Concierge',
                    'title_ar' => 'الكونسيرج الأساسي',
                    'desc_en' => 'Core concierge services including travel coordination, dining reservations, and basic lifestyle management.',
                    'desc_ar' => 'خدمات الكونسيرج الأساسية بما في ذلك تنسيق السفر، حجوزات المطاعم، وإدارة نمط الحياة الأساسية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:crown.svg?color=%23602234',
                    'title_en' => 'Premium Concierge',
                    'title_ar' => 'الكونسيرج المميز',
                    'desc_en' => 'Enhanced services with dedicated concierge, priority access, and comprehensive lifestyle management.',
                    'desc_ar' => 'خدمات محسنة مع كونسيرج مخصص، الوصول بالأولوية، وإدارة نمط الحياة الشاملة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:diamond-stone.svg?color=%23602234',
                    'title_en' => 'Elite Concierge',
                    'title_ar' => 'الكونسيرج النخبوي',
                    'desc_en' => 'Ultimate personalized service with 24/7 dedicated team, exclusive access, and unlimited requests.',
                    'desc_ar' => 'خدمة شخصية نهائية مع فريق مخصص على مدار 24/7، الوصول الحصري، وطلبات غير محدودة.'
                ]
            ];

            foreach ($levels as $index => $level) :
            ?>
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="service-level-card">
                    <div class="service-level-icon">
                        <?php if (isset($level['icon_url'])): ?>
                            <img src="<?php echo $level['icon_url']; ?>" alt="<?php echo $lang === 'ar' ? $level['title_ar'] : $level['title_en']; ?>" class="service-level-icon-img">
                        <?php elseif (isset($level['icon'])): ?>
                            <i class="fas <?php echo $level['icon']; ?>"></i>
                        <?php endif; ?>
                    </div>
                    <h4><?php echo $lang === 'ar' ? $level['title_ar'] : $level['title_en']; ?></h4>
                    <p><?php echo $lang === 'ar' ? $level['desc_ar'] : $level['desc_en']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'كيف نعمل' : 'How We Work'; ?></h2>
            <p class="section-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'عملية بسيطة لتلبية احتياجاتك' 
                    : 'Simple process to fulfill your needs'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $steps = [
                [
                    'number' => '01',
                    'title_en' => 'Initial Consultation',
                    'title_ar' => 'الاستشارة الأولية',
                    'desc_en' => 'Understanding your preferences, lifestyle, and specific needs to customize our service approach.',
                    'desc_ar' => 'فهم تفضيلاتك ونمط حياتك واحتياجاتك المحددة لتخصيص نهج خدمتنا.'
                ],
                [
                    'number' => '02',
                    'title_en' => 'Service Activation',
                    'title_ar' => 'تفعيل الخدمة',
                    'desc_en' => 'Dedicated concierge assignment and establishment of communication channels for seamless access.',
                    'desc_ar' => 'تعيين كونسيرج مخصص وإنشاء قنوات التواصل للوصول السلس.'
                ],
                [
                    'number' => '03',
                    'title_en' => 'Request Fulfillment',
                    'title_ar' => 'تنفيذ الطلبات',
                    'desc_en' => 'Rapid response and execution of your requests with attention to detail and discretion.',
                    'desc_ar' => 'استجابة سريعة وتنفيذ طلباتك مع الاهتمام بالتفاصيل والسرية.'
                ],
                [
                    'number' => '04',
                    'title_en' => 'Ongoing Relationship',
                    'title_ar' => 'العلاقة المستمرة',
                    'desc_en' => 'Continuous service refinement, proactive suggestions, and building a trusted partnership.',
                    'desc_ar' => 'تحسين الخدمة المستمر، الاقتراحات الاستباقية، وبناء شراكة موثوقة.'
                ]
            ];

            foreach ($steps as $step) :
            ?>
            <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up">
                <div class="process-step">
                    <div class="step-number"><?php echo formatNumber($step['number']); ?></div>
                    <h4><?php echo $lang === 'ar' ? $step['title_ar'] : $step['title_en']; ?></h4>
                    <p><?php echo $lang === 'ar' ? $step['desc_ar'] : $step['desc_en']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="section bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <div class="service-image-wrapper">
                    <img src="<?= url('assets/images/service-2-914x1024.png') ?>" alt="<?php echo $lang === 'ar' ? 'فوائد الخدمة' : 'Service Benefits'; ?>" class="img-fluid service-detail-img">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title"><?php echo $lang === 'ar' ? 'لماذا تختار خدماتنا' : 'Why Choose Our Services'; ?></h2>
                <div class="benefits-list">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'متاح على مدار الساعة' : '24/7 Availability'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'فريقك المخصص متاح دائماً لتلبية احتياجاتك في أي وقت' : 'Your dedicated team is always available to fulfill your needs anytime'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'وصول حصري' : 'Exclusive Access'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'علاقات طويلة الأمد تمنحك الوصول إلى تجارب وخدمات حصرية' : 'Long-standing relationships grant you access to exclusive experiences and services'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'خدمة شخصية' : 'Personalized Service'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'كل طلب يُعالج بشكل شخصي وفقاً لتفضيلاتك وأسلوب حياتك' : 'Every request is handled personally according to your preferences and lifestyle'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'سرية تامة' : 'Complete Discretion'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'جميع التفاعلات تتم بسرية تامة واحترام للخصوصية' : 'All interactions are handled with complete discretion and respect for privacy'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'توفير الوقت' : 'Time Saving'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'نوفر عليك ساعات من البحث والتنسيق، نتعامل مع كل شيء' : 'We save you hours of research and coordination, we handle everything'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'الأسئلة الشائعة' : 'Frequently Asked Questions'; ?></h2>
        </div>

        <div class="faq-accordion" data-aos="fade-up">
            <?php
            $faqs = [
                [
                    'q_en' => 'What types of requests can you fulfill?',
                    'q_ar' => 'ما أنواع الطلبات التي يمكنكم تنفيذها؟',
                    'a_en' => 'We can fulfill virtually any request that is legal and ethical. From simple tasks like restaurant reservations to complex arrangements like private jet charters, exclusive event access, or custom experiences. If it\'s important to you, we\'ll make it happen.',
                    'a_ar' => 'يمكننا تنفيذ أي طلب تقريباً يكون قانونياً وأخلاقياً. من المهام البسيطة مثل حجوزات المطاعم إلى الترتيبات المعقدة مثل استئجار الطائرات الخاصة، الوصول للفعاليات الحصرية، أو التجارب المخصصة. إذا كان مهماً بالنسبة لك، سنجعله يحدث.'
                ],
                [
                    'q_en' => 'How quickly can you respond to requests?',
                    'q_ar' => 'ما مدى سرعة استجابتكم للطلبات؟',
                    'a_en' => 'Response times vary based on request complexity. Simple requests like reservations are typically handled within hours. Complex arrangements may take 24-48 hours. For urgent matters, we provide immediate priority handling.',
                    'a_ar' => 'تختلف أوقات الاستجابة حسب تعقيد الطلب. الطلبات البسيطة مثل الحجوزات يتم التعامل معها عادة خلال ساعات. الترتيبات المعقدة قد تستغرق 24-48 ساعة. للمسائل العاجلة، نقدم معالجة أولوية فورية.'
                ],
                [
                    'q_en' => 'Is there a limit to the number of requests?',
                    'q_ar' => 'هل هناك حد لعدد الطلبات؟',
                    'a_en' => 'Service limits depend on your chosen package. Essential and Premium packages have monthly request limits, while Elite packages offer unlimited requests. We can discuss custom arrangements based on your needs.',
                    'a_ar' => 'تعتمد حدود الخدمة على الحزمة المختارة. الحزم الأساسية والمميزة لها حدود طلبات شهرية، بينما تقدم الحزم النخبوية طلبات غير محدودة. يمكننا مناقشة ترتيبات مخصصة بناءً على احتياجاتك.'
                ],
                [
                    'q_en' => 'Do you provide services internationally?',
                    'q_ar' => 'هل تقدمون خدمات دولياً؟',
                    'a_en' => 'Yes, we provide concierge services globally. Our network extends across the Middle East, Europe, Asia, and the Americas. We coordinate travel, accommodations, and services wherever you need them.',
                    'a_ar' => 'نعم، نقدم خدمات الكونسيرج عالمياً. تمتد شبكتنا عبر الشرق الأوسط وأوروبا وآسيا والأمريكتين. نتولى تنسيق السفر والإقامة والخدمات أينما كنت بحاجة إليها.'
                ],
                [
                    'q_en' => 'How do you ensure privacy and discretion?',
                    'q_ar' => 'كيف تضمنون الخصوصية والسرية؟',
                    'a_en' => 'All our concierge staff sign strict NDAs and are trained in discretion protocols. We have a 25-year track record of maintaining complete confidentiality. Your information and requests are never shared publicly or with third parties.',
                    'a_ar' => 'جميع موظفي الكونسيرج لدينا يوقعون اتفاقيات سرية صارمة ويتدربون على بروتوكولات السرية. لدينا سجل حافل لمدة 25 عاماً في الحفاظ على السرية التامة. معلوماتك وطلباتك لا تُشارك علناً أو مع أطراف ثالثة أبداً.'
                ]
            ];

            foreach ($faqs as $index => $faq) :
            ?>
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFAQ(this)">
                    <?php echo $lang === 'ar' ? $faq['q_ar'] : $faq['q_en']; ?>
                </button>
                <div class="faq-answer">
                    <p><?php echo $lang === 'ar' ? $faq['a_ar'] : $faq['a_en']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h2><?php echo $lang === 'ar' ? 'هل أنت مستعد لتجربة خدمة الكونسيرج الاستثنائية؟' : 'Ready to Experience Exceptional Concierge Service?'; ?></h2>
                <p class="lead mb-4">
                    <?php echo $lang === 'ar' 
                        ? 'احصل على استشارة مجانية لمناقشة احتياجاتك' 
                        : 'Get a free consultation to discuss your needs'; ?>
                </p>
                <a href="contact.php" class="btn btn-primary btn-lg"><?php echo $lang === 'ar' ? 'احجز استشارتك المجانية' : 'Book Your Free Consultation'; ?></a>
                <p class="mt-3">
                    <small><i class="fas fa-phone"></i> <?php echo $lang === 'ar' ? 'أو اتصل بنا: ' : 'Or call us: '; ?>+966532447976</small>
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function toggleFAQ(button) {
    const item = button.parentElement;
    const isOpen = item.classList.contains('active');
    
    // Close all FAQ items
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
    
    // Open clicked item if it wasn't open
    if (!isOpen) {
        item.classList.add('active');
    }
}

// Sticky Back Button
(function() {
    const stickyButton = document.querySelector('.back-button-sticky');
    const header = document.querySelector('.service-detail-header');
    
    if (stickyButton && header) {
        const headerBottom = header.offsetTop + header.offsetHeight;
        
        function handleScroll() {
            if (window.pageYOffset > headerBottom) {
                stickyButton.classList.add('show');
            } else {
                stickyButton.classList.remove('show');
            }
        }
        
        window.addEventListener('scroll', handleScroll);
        handleScroll(); // Check on load
    }
})();
</script>

<?php require_once 'includes/footer.php'; ?>

