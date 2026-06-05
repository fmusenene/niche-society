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
$pageTitle = $lang === 'ar' ? 'إدارة الممتلكات - نيش سوسايتي' : 'Property Management - Niche Society';
$pageDescription = $lang === 'ar' ? 'إدارة شاملة لممتلكاتكم العقارية مع دمج الأنظمة الذكية والتقنيات الحديثة لضمان أعلى مستويات الكفاءة والراحة.' : 'Comprehensive management of your properties with integration of smart systems and modern technologies to ensure the highest levels of efficiency and comfort.';

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
<a href="<?= url('services.php') ?>#service-2" class="back-button back-button-sticky">
    <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
    <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
</a>

<!-- Back Button & Page Header -->
<section class="service-detail-header">
    <div class="container">
        <div class="service-detail-nav">
            <a href="<?= url('services.php') ?>#service-2" class="back-button">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
            </a>
        </div>
        <div class="service-detail-title-section">
            <div class="service-badge-header"><?php echo formatNumber('02'); ?></div>
            <h1 class="service-detail-title"><?php echo $lang === 'ar' ? 'إدارة الممتلكات' : 'Property Management'; ?></h1>
            <p class="service-detail-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'إدارة شاملة لممتلكاتكم العقارية مع دمج الأنظمة الذكية والتقنيات الحديثة' 
                    : 'Comprehensive management of your properties with integration of smart systems and modern technologies'; ?>
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
                        ? 'نوفر حلولاً متكاملة لإدارة جميع أنواع الممتلكات العقارية من الفلل والقصور إلى المجمعات السكنية والتجارية. ندمج أحدث التقنيات الذكية لضمان إدارة فعالة ومربحة.' 
                        : 'We provide comprehensive solutions for managing all types of real estate properties from villas and palaces to residential and commercial complexes. We integrate the latest smart technologies to ensure efficient and profitable management.'; ?>
                </p>
                <p>
                    <?php echo $lang === 'ar' 
                        ? 'فريقنا المتخصص يتولى جميع جوانب إدارة الممتلكات من الصيانة الوقائية إلى إدارة الإيجارات والعلاقات مع المستأجرين، مع الحفاظ على أعلى معايير الجودة والكفاءة.' 
                        : 'Our specialized team handles all aspects of property management from preventive maintenance to rental management and tenant relations, while maintaining the highest standards of quality and efficiency.'; ?>
                </p>
                <div class="service-stats mt-4">
                    <div class="stat-box">
                        <h3><?php echo formatNumber('500'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'ممتلكات مُدارة' : 'Properties Managed'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>98%</h3>
                        <p><?php echo $lang === 'ar' ? 'معدل الرضا' : 'Satisfaction Rate'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo formatNumber('24'); ?>/<?php echo formatNumber('7'); ?></h3>
                        <p><?php echo $lang === 'ar' ? 'دعم متواصل' : 'Continuous Support'; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="service-image-wrapper">
                    <img src="<?= url('assets/images/service-3.jpg') ?>" alt="<?php echo $lang === 'ar' ? 'إدارة الممتلكات' : 'Property Management'; ?>" class="img-fluid service-detail-img">
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
                    ? 'خدمات شاملة تغطي جميع احتياجات ممتلكاتك' 
                    : 'Comprehensive services covering all your property needs'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $features = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:office-building.svg?color=%23602234',
                    'title_en' => 'Smart Building Systems',
                    'title_ar' => 'أنظمة المباني الذكية',
                    'desc_en' => 'Seamless integration of Internet of Things enabled technologies, intelligent climate control, advanced lighting solutions, and integrated security management to enhance comfort, efficiency, and overall building performance.',
                    'desc_ar' => 'تكامل سلس لتقنيات مدعومة بإنترنت الأشياء، التحكم الذكي في المناخ، حلول إضاءة متقدمة، وإدارة أمنية متكاملة لتعزيز الراحة والكفاءة والأداء الشامل للمبنى.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:wrench.svg?color=%23602234',
                    'title_en' => 'Preventive Maintenance',
                    'title_ar' => 'الصيانة الوقائية',
                    'desc_en' => 'Scheduled maintenance programs, regular inspections, and proactive repairs to prevent costly breakdowns.',
                    'desc_ar' => 'برامج الصيانة المجدولة، الفحوصات المنتظمة، والإصلاحات الاستباقية لمنع الأعطال المكلفة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account-group.svg?color=%23602234',
                    'title_en' => 'Staff & Contractor Management',
                    'title_ar' => 'إدارة الموظفين والمقاولين',
                    'desc_en' => 'Oversight of on-site staff, vendor relationships, and contractor coordination for seamless operations.',
                    'desc_ar' => 'الإشراف على الموظفين في الموقع، علاقات الموردين، وتنسيق المقاولين لعمليات سلسة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:chart-line.svg?color=%23602234',
                    'title_en' => 'Performance Analytics',
                    'title_ar' => 'تحليلات الأداء',
                    'desc_en' => 'Comprehensive reporting on property performance, energy consumption, and cost optimization opportunities.',
                    'desc_ar' => 'تقارير شاملة عن أداء الممتلكات، استهلاك الطاقة، وفرص تحسين التكاليف.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:shield-check.svg?color=%23602234',
                    'title_en' => 'Security & Safety',
                    'title_ar' => 'الأمن والسلامة',
                    'desc_en' => 'Advanced security systems management, access control, surveillance, and emergency response protocols.',
                    'desc_ar' => 'إدارة أنظمة الأمن المتقدمة، التحكم في الوصول، المراقبة، وبروتوكولات الاستجابة للطوارئ.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:lightning-bolt.svg?color=%23602234',
                    'title_en' => 'Energy Optimization',
                    'title_ar' => 'تحسين الطاقة',
                    'desc_en' => 'Energy efficiency audits, smart grid integration, and sustainable practices to reduce operational costs.',
                    'desc_ar' => 'تدقيقات كفاءة الطاقة، تكامل الشبكة الذكية، والممارسات المستدامة لتقليل التكاليف التشغيلية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:currency-usd.svg?color=%23602234',
                    'title_en' => 'Financial Management',
                    'title_ar' => 'الإدارة المالية',
                    'desc_en' => 'Budget planning, expense tracking, vendor payment processing, and financial reporting.',
                    'desc_ar' => 'تخطيط الميزانية، تتبع النفقات، معالجة مدفوعات الموردين، والتقارير المالية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:handshake.svg?color=%23602234',
                    'title_en' => 'Tenant Relations',
                    'title_ar' => 'علاقات المستأجرين',
                    'desc_en' => 'Professional tenant communication, lease management, and conflict resolution services.',
                    'desc_ar' => 'التواصل المهني مع المستأجرين، إدارة الإيجارات، وخدمات حل النزاعات.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:file-document-check.svg?color=%23602234',
                    'title_en' => 'Compliance & Documentation',
                    'title_ar' => 'الامتثال والتوثيق',
                    'desc_en' => 'Ensuring regulatory compliance, maintaining proper documentation, and handling legal requirements.',
                    'desc_ar' => 'ضمان الامتثال للوائح، الحفاظ على التوثيق المناسب، والتعامل مع المتطلبات القانونية.'
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

<!-- Process Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'كيف نعمل' : 'How We Work'; ?></h2>
            <p class="section-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'منهجية عمل احترافية لضمان أفضل النتائج' 
                    : 'Professional methodology to ensure the best results'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $steps = [
                [
                    'number' => '01',
                    'title_en' => 'Property Assessment',
                    'title_ar' => 'تقييم الممتلكات',
                    'desc_en' => 'Comprehensive evaluation of property condition, systems, infrastructure, and management requirements.',
                    'desc_ar' => 'تقييم شامل لحالة الممتلكات والأنظمة والبنية التحتية ومتطلبات الإدارة.'
                ],
                [
                    'number' => '02',
                    'title_en' => 'Strategic Planning',
                    'title_ar' => 'التخطيط الاستراتيجي',
                    'desc_en' => 'Develop customized management strategy with clear objectives, timelines, and performance metrics.',
                    'desc_ar' => 'تطوير استراتيجية إدارة مخصصة بأهداف واضحة وجداول زمنية ومقاييس أداء.'
                ],
                [
                    'number' => '03',
                    'title_en' => 'System Integration',
                    'title_ar' => 'تكامل الأنظمة',
                    'desc_en' => 'Deploy smart technologies, establish management protocols, and train on-site personnel.',
                    'desc_ar' => 'نشر التقنيات الذكية، إنشاء بروتوكولات الإدارة، وتدريب الموظفين في الموقع.'
                ],
                [
                    'number' => '04',
                    'title_en' => 'Continuous Optimization',
                    'title_ar' => 'التحسين المستمر',
                    'desc_en' => 'Regular monitoring, performance analysis, and strategic adjustments to maximize efficiency and value.',
                    'desc_ar' => 'المراقبة المنتظمة، تحليل الأداء، والتعديلات الاستراتيجية لتعظيم الكفاءة والقيمة.'
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
<section class="section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <div class="service-image-wrapper">
                    <img src="<?= url('assets/images/service-3.jpg') ?>" alt="<?php echo $lang === 'ar' ? 'فوائد الخدمة' : 'Service Benefits'; ?>" class="img-fluid service-detail-img">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title"><?php echo $lang === 'ar' ? 'لماذا تختار خدماتنا' : 'Why Choose Our Services'; ?></h2>
                <div class="benefits-list">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'زيادة قيمة الممتلكات' : 'Increase Property Value'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'صيانة احترافية وتحسينات استراتيجية تزيد من قيمة ممتلكاتك' : 'Professional maintenance and strategic improvements increase your property value'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'تقليل التكاليف التشغيلية' : 'Reduce Operating Costs'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'تحسين كفاءة الطاقة وإدارة مالية ذكية تقلل النفقات' : 'Energy efficiency optimization and smart financial management reduce expenses'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'تقنيات ذكية متقدمة' : 'Advanced Smart Technologies'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'تكامل أحدث أنظمة المباني الذكية لراحة وتوفير أفضل' : 'Integration of latest smart building systems for optimal comfort and savings'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'إدارة احترافية شاملة' : 'Comprehensive Professional Management'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'فريق متخصص يتولى جميع جوانب إدارة الممتلكات' : 'Specialized team handling all aspects of property management'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'تقارير شفافة ومنتظمة' : 'Transparent Regular Reports'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'تقارير مفصلة عن الأداء والمالية والصيانة' : 'Detailed reports on performance, finances, and maintenance'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'الأسئلة الشائعة' : 'Frequently Asked Questions'; ?></h2>
        </div>

        <div class="faq-accordion" data-aos="fade-up">
            <?php
            $faqs = [
                [
                    'q_en' => 'What types of properties do you manage?',
                    'q_ar' => 'ما أنواع الممتلكات التي تديرونها؟',
                    'a_en' => 'We manage all types of properties including luxury villas, palaces, residential complexes, commercial buildings, and mixed-use developments. Our services are scalable to properties of any size.',
                    'a_ar' => 'نحن ندير جميع أنواع الممتلكات بما في ذلك الفلل الفاخرة والقصور والمجمعات السكنية والمباني التجارية والمشاريع متعددة الاستخدامات. خدماتنا قابلة للتوسع لممتلكات من أي حجم.'
                ],
                [
                    'q_en' => 'How do you integrate smart building technologies?',
                    'q_ar' => 'كيف تدمجون تقنيات المباني الذكية؟',
                    'a_en' => 'We assess your property\'s infrastructure and recommend appropriate smart systems for climate control, security, lighting, and energy management. Our team handles installation, integration, and training.',
                    'a_ar' => 'نقوم بتقييم البنية التحتية لممتلكاتك ونوصي بأنظمة ذكية مناسبة للتحكم في المناخ والأمن والإضاءة وإدارة الطاقة. يتولى فريقنا التثبيت والتكامل والتدريب.'
                ],
                [
                    'q_en' => 'What maintenance services are included?',
                    'q_ar' => 'ما هي خدمات الصيانة المدرجة؟',
                    'a_en' => 'We provide comprehensive maintenance including preventive schedules, emergency repairs, HVAC systems, plumbing, electrical, landscaping, and pool maintenance. All services are coordinated and documented.',
                    'a_ar' => 'نوفر صيانة شاملة تشمل الجداول الوقائية، الإصلاحات الطارئة، أنظمة التدفئة والتهوية وتكييف الهواء، السباكة، الكهرباء، تنسيق الحدائق، وصيانة المسابح. جميع الخدمات منسقة وموثقة.'
                ],
                [
                    'q_en' => 'How do you handle tenant relations?',
                    'q_ar' => 'كيف تتعاملون مع علاقات المستأجرين؟',
                    'a_en' => 'We provide professional tenant communication, lease administration, rent collection, maintenance requests handling, and conflict resolution. All interactions are documented and handled with discretion.',
                    'a_ar' => 'نوفر التواصل المهني مع المستأجرين، إدارة الإيجارات، تحصيل الإيجار، معالجة طلبات الصيانة، وحل النزاعات. جميع التفاعلات موثقة ومعالجة بسرية.'
                ],
                [
                    'q_en' => 'What reporting do you provide?',
                    'q_ar' => 'ما التقارير التي تقدمونها؟',
                    'a_en' => 'We provide monthly comprehensive reports covering financial performance, maintenance activities, energy consumption, tenant relations, and property condition. Custom reports are available upon request.',
                    'a_ar' => 'نقدم تقارير شاملة شهرية تغطي الأداء المالي، أنشطة الصيانة، استهلاك الطاقة، علاقات المستأجرين، وحالة الممتلكات. التقارير المخصصة متاحة عند الطلب.'
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
                <h2><?php echo $lang === 'ar' ? 'هل أنت مستعد لإدارة احترافية لممتلكاتك؟' : 'Ready for Professional Property Management?'; ?></h2>
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

