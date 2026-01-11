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
$pageTitle = $lang === 'ar' ? 'إدارة المنازل الذكية - نيتش سوسايتي' : 'Smart Household Management - Niche Society';
$pageDescription = $lang === 'ar' ? 'خدمات إدارة منازل احترافية تجمع بين الفخامة والكفاءة. فريق متخصص لإدارة جميع احتياجات منزلك بسرية تامة.' : 'Professional household management services combining luxury and efficiency. Specialized team to manage all your home needs with complete discretion.';

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
<a href="<?= url('services.php') ?>#service-1" class="back-button back-button-sticky">
    <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
    <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
</a>

<!-- Back Button & Page Header -->
<section class="service-detail-header">
    <div class="container">
        <div class="service-detail-nav">
            <a href="<?= url('services.php') ?>#service-1" class="back-button">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
            </a>
        </div>
        <div class="service-detail-title-section">
            <div class="service-badge-header"><?php echo formatNumber('01'); ?></div>
            <h1 class="service-detail-title"><?php echo $lang === 'ar' ? 'إدارة المنازل الذكية' : 'Smart Household Management'; ?></h1>
            <p class="service-detail-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'الحل المتكامل لإدارة منزلك بكفاءة واحترافية عالية' 
                    : 'Complete solution for managing your home with high efficiency and professionalism'; ?>
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
                        ? 'نوفر حلولاً متكاملة لإدارة المنازل والفلل والقصور بمعايير عالمية، نجمع بين الخبرة الطويلة والتقنيات الحديثة لضمان راحتك التامة.' 
                        : 'We provide comprehensive solutions for managing homes, villas, and palaces with international standards, combining extensive experience with modern technology to ensure your complete comfort.'; ?>
                </p>
                <p>
                    <?php echo $lang === 'ar' 
                        ? 'فريقنا المتخصص يتولى جميع جوانب إدارة منزلك من الإشراف على الموظفين إلى إدارة الجداول والميزانيات، مع الحفاظ على أعلى معايير الجودة والسرية.' 
                        : 'Our specialized team handles all aspects of your home management from staff supervision to schedule and budget management, while maintaining the highest standards of quality and confidentiality.'; ?>
                </p>
                <div class="service-stats mt-4">
                    <div class="stat-box">
                        <h3><?php echo formatNumber('25'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'عاماً من الخبرة' : 'Years of Experience'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo formatNumber('100'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'عميل راضٍ' : 'Satisfied Clients'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo formatNumber('24'); ?>/<?php echo formatNumber('7'); ?></h3>
                        <p><?php echo $lang === 'ar' ? 'دعم متواصل' : 'Continuous Support'; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="service-image-wrapper">
                    <img src="<?= url('assets/images/service.png') ?>" alt="<?php echo $lang === 'ar' ? 'إدارة المنازل' : 'Household Management'; ?>" class="img-fluid service-detail-img">
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
                    ? 'خدمات شاملة تغطي جميع احتياجات منزلك' 
                    : 'Comprehensive services covering all your home needs'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $features = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account-group.svg?color=%23602234',
                    'title_en' => 'Staff Management',
                    'title_ar' => 'إدارة الموظفين',
                    'desc_en' => 'Complete oversight of household staff including recruitment, training, scheduling, and performance evaluation.',
                    'desc_ar' => 'إشراف كامل على موظفي المنزل بما في ذلك التوظيف والتدريب والجدولة وتقييم الأداء.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:package-variant.svg?color=%23602234',
                    'title_en' => 'Inventory Management',
                    'title_ar' => 'إدارة المخزون',
                    'desc_en' => 'Tracking and managing all household supplies, groceries, and essentials with automated reordering.',
                    'desc_ar' => 'تتبع وإدارة جميع الإمدادات المنزلية والبقالة والأساسيات مع إعادة الطلب التلقائي.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:wrench.svg?color=%23602234',
                    'title_en' => 'Maintenance Coordination',
                    'title_ar' => 'تنسيق الصيانة',
                    'desc_en' => 'Scheduling and overseeing all maintenance work, repairs, and renovations with trusted vendors.',
                    'desc_ar' => 'جدولة والإشراف على جميع أعمال الصيانة والإصلاحات والتجديدات مع البائعين الموثوقين.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:calendar-clock.svg?color=%23602234',
                    'title_en' => 'Schedule Management',
                    'title_ar' => 'إدارة الجداول',
                    'desc_en' => 'Organizing family calendars, appointments, and coordinating household activities seamlessly.',
                    'desc_ar' => 'تنظيم التقاويم العائلية والمواعيد وتنسيق الأنشطة المنزلية بسلاسة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:currency-usd.svg?color=%23602234',
                    'title_en' => 'Budget Management',
                    'title_ar' => 'إدارة الميزانية',
                    'desc_en' => 'Comprehensive financial oversight of household expenses with detailed reporting and cost optimization.',
                    'desc_ar' => 'إشراف مالي شامل على النفقات المنزلية مع تقارير تفصيلية وتحسين التكاليف.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:shield-check.svg?color=%23602234',
                    'title_en' => 'Security Coordination',
                    'title_ar' => 'التنسيق الأمني',
                    'desc_en' => 'Managing security systems, access control, and coordinating with security personnel.',
                    'desc_ar' => 'إدارة الأنظمة الأمنية والتحكم في الوصول والتنسيق مع الأفراد الأمنيين.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:car.svg?color=%23602234',
                    'title_en' => 'Transportation Management',
                    'title_ar' => 'إدارة النقل',
                    'desc_en' => 'Organizing vehicles, drivers, maintenance schedules, and all transportation logistics.',
                    'desc_ar' => 'تنظيم المركبات والسائقين وجداول الصيانة وجميع لوجستيات النقل.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:flower.svg?color=%23602234',
                    'title_en' => 'Garden & Outdoor Management',
                    'title_ar' => 'إدارة الحدائق والمساحات الخارجية',
                    'desc_en' => 'Coordinating landscaping, pool maintenance, and outdoor space upkeep.',
                    'desc_ar' => 'تنسيق الحدائق وصيانة المسابح والحفاظ على المساحات الخارجية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:alert-circle.svg?color=%23602234',
                    'title_en' => 'Emergency Response',
                    'title_ar' => 'الاستجابة للطوارئ',
                    'desc_en' => '24/7 emergency support and rapid response to any household emergencies.',
                    'desc_ar' => 'دعم طوارئ على مدار 24/7 واستجابة سريعة لأي حالات طوارئ منزلية.'
                ]
            ];

            foreach ($features as $index => $feature) :
            ?>
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="feature-card">
                    <div class="feature-icon">
                        <?php if (isset($feature['icon_url'])): ?>
                            <img src="<?php echo $feature['icon_url']; ?>" alt="<?php echo $lang === 'ar' ? $feature['title_ar'] : $feature['title_en']; ?>" class="feature-icon-img">
                        <?php else: ?>
                            <i class="fas <?php echo isset($feature['icon']) ? $feature['icon'] : 'fa-circle'; ?>"></i>
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
                    'title_en' => 'Initial Assessment',
                    'title_ar' => 'التقييم الأولي',
                    'desc_en' => 'We conduct a comprehensive assessment of your home, current staff, and all requirements to understand your unique needs.',
                    'desc_ar' => 'نقوم بإجراء تقييم شامل لمنزلك والموظفين الحاليين وجميع المتطلبات لفهم احتياجاتك الفريدة.'
                ],
                [
                    'number' => '02',
                    'title_en' => 'Custom Plan Development',
                    'title_ar' => 'تطوير خطة مخصصة',
                    'desc_en' => 'Create a detailed management plan tailored to your lifestyle, preferences, and household structure.',
                    'desc_ar' => 'إنشاء خطة إدارة مفصلة مصممة خصيصاً لأسلوب حياتك وتفضيلاتك وهيكل منزلك.'
                ],
                [
                    'number' => '03',
                    'title_en' => 'Implementation & Training',
                    'title_ar' => 'التنفيذ والتدريب',
                    'desc_en' => 'Deploy our management systems, train existing staff, and recruit additional personnel if needed.',
                    'desc_ar' => 'نشر أنظمة الإدارة لدينا وتدريب الموظفين الحاليين وتوظيف موظفين إضافيين إذا لزم الأمر.'
                ],
                [
                    'number' => '04',
                    'title_en' => 'Ongoing Management',
                    'title_ar' => 'الإدارة المستمرة',
                    'desc_en' => 'Continuous oversight with regular reports, quality checks, and adjustments based on your feedback.',
                    'desc_ar' => 'إشراف مستمر مع تقارير منتظمة وفحوصات جودة وتعديلات بناءً على ملاحظاتك.'
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
                    <img src="<?= url('assets/images/service.png') ?>" alt="<?php echo $lang === 'ar' ? 'فوائد الخدمة' : 'Service Benefits'; ?>" class="img-fluid service-detail-img">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title"><?php echo $lang === 'ar' ? 'لماذا تختار خدماتنا' : 'Why Choose Our Services'; ?></h2>
                <div class="benefits-list">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'توفير الوقت والجهد' : 'Save Time & Effort'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'نتولى جميع التفاصيل حتى تتمكن من التركيز على ما يهمك' : 'We handle all details so you can focus on what matters'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'إدارة احترافية' : 'Professional Management'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'فريق ذو خبرة واسعة في إدارة المنازل الفاخرة' : 'Team with extensive experience in luxury home management'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'خفض التكاليف' : 'Cost Reduction'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'تحسين الميزانية والتفاوض مع الموردين لأفضل الأسعار' : 'Budget optimization and vendor negotiations for best prices'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'سرية وخصوصية' : 'Privacy & Discretion'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'التزام تام بالسرية وحماية خصوصية عائلتك' : 'Complete commitment to confidentiality and protecting your family privacy'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'راحة البال' : 'Peace of Mind'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'يعمل منزلك بسلاسة دون الحاجة إلى القلق بشأن التفاصيل' : 'Your home runs smoothly without worrying about details'; ?></p>
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
                    'q_en' => 'What size properties do you manage?',
                    'q_ar' => 'ما حجم العقارات التي تديرونها؟',
                    'a_en' => 'We manage properties of all sizes, from luxury apartments and villas to large estates and palaces. Our services are scalable and customized to your specific property needs.',
                    'a_ar' => 'نحن ندير عقارات من جميع الأحجام، من الشقق الفاخرة والفلل إلى العقارات الكبيرة والقصور. خدماتنا قابلة للتوسع ومخصصة لاحتياجات ممتلكاتك المحددة.'
                ],
                [
                    'q_en' => 'Can you work with our existing staff?',
                    'q_ar' => 'هل يمكنكم العمل مع موظفينا الحاليين؟',
                    'a_en' => 'Absolutely. We can integrate with your existing household staff, providing training, oversight, and management while respecting established relationships and dynamics.',
                    'a_ar' => 'بالتأكيد. يمكننا التكامل مع موظفي منزلك الحاليين، وتوفير التدريب والإشراف والإدارة مع احترام العلاقات والديناميكيات القائمة.'
                ],
                [
                    'q_en' => 'How do you ensure privacy and discretion?',
                    'q_ar' => 'كيف تضمنون الخصوصية والسرية؟',
                    'a_en' => 'All our staff sign strict NDAs, undergo background checks, and are trained in privacy protocols. We have a 25-year track record of maintaining client confidentiality with zero breaches.',
                    'a_ar' => 'جميع موظفينا يوقعون اتفاقيات سرية صارمة، ويخضعون لفحوصات خلفية، ويتدربون على بروتوكولات الخصوصية. لدينا سجل حافل لمدة 25 عامًا في الحفاظ على سرية العملاء دون أي خروقات.'
                ],
                [
                    'q_en' => 'What are your service fees?',
                    'q_ar' => 'ما هي رسوم خدماتكم؟',
                    'a_en' => 'Our fees are customized based on property size, scope of services, and specific requirements. We offer a complimentary consultation to assess your needs and provide a detailed proposal.',
                    'a_ar' => 'يتم تخصيص رسومنا بناءً على حجم الممتلكات ونطاق الخدمات والمتطلبات المحددة. نقدم استشارة مجانية لتقييم احتياجاتك وتقديم عرض تفصيلي.'
                ],
                [
                    'q_en' => 'Do you provide emergency support?',
                    'q_ar' => 'هل تقدمون دعم الطوارئ؟',
                    'a_en' => 'Yes, we provide 24/7 emergency support for all managed properties. Our team is always available to handle urgent situations and coordinate rapid responses.',
                    'a_ar' => 'نعم، نحن نقدم دعم طوارئ على مدار 24/7 لجميع العقارات المدارة. فريقنا متاح دائمًا للتعامل مع المواقف العاجلة وتنسيق الاستجابات السريعة.'
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
                <h2><?php echo $lang === 'ar' ? 'هل أنت مستعد لتجربة إدارة منزلية احترافية؟' : 'Ready to Experience Professional Home Management?'; ?></h2>
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
