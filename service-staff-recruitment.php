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
$pageTitle = $lang === 'ar' ? 'توظيف وتدريب الموظفين - نيتش سوسايتي' : 'Staff Recruitment & Training - Niche Society';
$pageDescription = $lang === 'ar' ? 'اختيار وتطوير أفضل الكفاءات لخدمتكم بأعلى معايير الاحترافية والجودة.' : 'Selecting and developing the best talents to serve you with the highest standards of professionalism and quality.';

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
<a href="<?= url('services.php') ?>#service-6" class="back-button back-button-sticky">
    <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
    <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
</a>

<!-- Back Button & Page Header -->
<section class="service-detail-header">
    <div class="container">
        <div class="service-detail-nav">
            <a href="<?= url('services.php') ?>#service-6" class="back-button">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
            </a>
        </div>
        <div class="service-detail-title-section">
            <div class="service-badge-header"><?php echo formatNumber('06'); ?></div>
            <h1 class="service-detail-title"><?php echo $lang === 'ar' ? 'توظيف وتدريب الموظفين' : 'Staff Recruitment & Training'; ?></h1>
            <p class="service-detail-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'اختيار وتطوير أفضل الكفاءات لخدمتكم بأعلى معايير الاحترافية' 
                    : 'Selecting and developing the best talents to serve you with the highest standards of professionalism'; ?>
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
                        ? 'نقدم خدمة شاملة لتوظيف وتدريب الموظفين المنزليين والمهنيين للعملاء المميزين. عملية اختيار صارمة متعددة المراحل تضمن أن كل موظف يلبي أعلى معايير الكفاءة والموثوقية والاحترافية.' 
                        : 'We provide comprehensive staff recruitment and training services for distinguished clients. Our rigorous multi-stage selection process ensures every employee meets the highest standards of competence, reliability, and professionalism.'; ?>
                </p>
                <p>
                    <?php echo $lang === 'ar' 
                        ? 'من عملية الفحص الأولية إلى التدريب المستمر والتطوير المهني، نضمن أن فريقك مجهز بالكامل لتقديم خدمة استثنائية. خبرتنا تمتد لأكثر من 25 عاماً في خدمة العملاء المميزين.' 
                        : 'From initial vetting to ongoing training and professional development, we ensure your team is fully equipped to deliver exceptional service. Our experience spans over 25 years serving distinguished clients.'; ?>
                </p>
                <div class="service-stats mt-4">
                    <div class="stat-box">
                        <h3><?php echo formatNumber('500'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'موظف مُوظف' : 'Staff Placed'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo formatNumber('98'); ?>%</h3>
                        <p><?php echo $lang === 'ar' ? 'معدل النجاح' : 'Success Rate'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo formatNumber('25'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'عاماً من الخبرة' : 'Years Experience'; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="service-image-wrapper">
                    <img src="<?= url('assets/images/service-4.jpg') ?>" alt="<?php echo $lang === 'ar' ? 'توظيف وتدريب الموظفين' : 'Staff Recruitment & Training'; ?>" class="img-fluid service-detail-img">
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
                    ? 'خدمات شاملة لتوظيف وتطوير الموظفين' 
                    : 'Comprehensive services for staff recruitment and development'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $features = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account-check.svg?color=%23602234',
                    'title_en' => 'Rigorous Vetting Process',
                    'title_ar' => 'عملية فحص صارمة',
                    'desc_en' => 'Multi-stage screening including background checks, reference verification, skills assessment, and character evaluation.',
                    'desc_ar' => 'فحص متعدد المراحل يشمل فحوصات الخلفية، التحقق من المراجع، تقييم المهارات، وتقييم الشخصية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:clipboard-check.svg?color=%23602234',
                    'title_en' => 'Skills & Competency Assessment',
                    'title_ar' => 'تقييم المهارات والكفاءات',
                    'desc_en' => 'Comprehensive evaluation of technical skills, experience level, and ability to meet specific job requirements.',
                    'desc_ar' => 'تقييم شامل للمهارات التقنية ومستوى الخبرة والقدرة على تلبية متطلبات الوظيفة المحددة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:school.svg?color=%23602234',
                    'title_en' => 'Customized Training Programs',
                    'title_ar' => 'برامج تدريب مخصصة',
                    'desc_en' => 'Tailored training programs covering job-specific skills, household protocols, and service excellence standards.',
                    'desc_ar' => 'برامج تدريبية مخصصة تغطي المهارات الخاصة بالوظيفة، بروتوكولات المنزل، ومعايير التميز في الخدمة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:crown.svg?color=%23602234',
                    'title_en' => 'Protocol & Etiquette Training',
                    'title_ar' => 'تدريب البروتوكول والإتيكيت',
                    'desc_en' => 'Specialized training in royal protocol, formal etiquette, and appropriate conduct for high-profile environments.',
                    'desc_ar' => 'تدريب متخصص في البروتوكول الملكي، الإتيكيت الرسمي، والسلوك المناسب للبيئات عالية المستوى.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:chart-line.svg?color=%23602234',
                    'title_en' => 'Performance Evaluation',
                    'title_ar' => 'تقييم الأداء',
                    'desc_en' => 'Regular performance reviews, feedback sessions, and continuous improvement plans to ensure excellence.',
                    'desc_ar' => 'مراجعات الأداء المنتظمة، جلسات الملاحظات، وخطط التحسين المستمر لضمان التميز.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:book-open-variant.svg?color=%23602234',
                    'title_en' => 'Professional Development',
                    'title_ar' => 'التطوير المهني',
                    'desc_en' => 'Ongoing professional development opportunities, skill enhancement, and career growth support.',
                    'desc_ar' => 'فرص التطوير المهني المستمرة، تعزيز المهارات، ودعم النمو الوظيفي.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:shield-check.svg?color=%23602234',
                    'title_en' => 'Security & Confidentiality Training',
                    'title_ar' => 'تدريب الأمن والسرية',
                    'desc_en' => 'Training in privacy protocols, confidentiality agreements, and security awareness for sensitive environments.',
                    'desc_ar' => 'التدريب على بروتوكولات الخصوصية، اتفاقيات السرية، والوعي الأمني للبيئات الحساسة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:translate.svg?color=%23602234',
                    'title_en' => 'Language & Communication Skills',
                    'title_ar' => 'مهارات اللغة والتواصل',
                    'desc_en' => 'Language training, communication skills development, and cross-cultural awareness programs.',
                    'desc_ar' => 'تدريب اللغة، تطوير مهارات التواصل، وبرامج الوعي بين الثقافات.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:handshake.svg?color=%23602234',
                    'title_en' => 'Placement & Integration Support',
                    'title_ar' => 'دعم التوظيف والاندماج',
                    'desc_en' => 'Smooth onboarding process, team integration support, and ongoing assistance during the transition period.',
                    'desc_ar' => 'عملية انضمام سلسة، دعم اندماج الفريق، والمساعدة المستمرة خلال فترة الانتقال.'
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

<!-- Staff Categories Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'فئات الموظفين' : 'Staff Categories'; ?></h2>
            <p class="section-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'نوظف جميع أنواع الموظفين المنزليين والمهنيين' 
                    : 'We recruit all types of household and professional staff'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $categories = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account-tie.svg?color=%23602234',
                    'title_en' => 'Household Managers',
                    'title_ar' => 'مديرو المنازل',
                    'desc_en' => 'Experienced managers to oversee household operations, staff coordination, and daily management.',
                    'desc_ar' => 'مديرون ذوو خبرة للإشراف على عمليات المنزل وتنسيق الموظفين والإدارة اليومية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:chef-hat.svg?color=%23602234',
                    'title_en' => 'Chefs & Kitchen Staff',
                    'title_ar' => 'الطهاة وطاقم المطبخ',
                    'desc_en' => 'Professional chefs, sous chefs, and kitchen assistants with culinary expertise.',
                    'desc_ar' => 'طهاة محترفون، مساعدو طهاة، ومساعدو مطبخ بخبرة طهوية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:broom.svg?color=%23602234',
                    'title_en' => 'Housekeeping Staff',
                    'title_ar' => 'طاقم التنظيف',
                    'desc_en' => 'Housekeepers, cleaners, and maintenance staff trained in luxury home care standards.',
                    'desc_ar' => 'عمال منازل، عمال نظافة، وطاقم صيانة مدربون على معايير رعاية المنازل الفاخرة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:baby-face-outline.svg?color=%23602234',
                    'title_en' => 'Nannies & Childcare',
                    'title_ar' => 'المربيات ورعاية الأطفال',
                    'desc_en' => 'Qualified nannies, tutors, and childcare professionals with proper certifications.',
                    'desc_ar' => 'مربيات مؤهلات، مدرسون، ومهنيو رعاية أطفال بشهادات مناسبة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:shield-account.svg?color=%23602234',
                    'title_en' => 'Security Personnel',
                    'title_ar' => 'الأفراد الأمنيون',
                    'desc_en' => 'Trained security guards, bodyguards, and security coordinators with proper licensing.',
                    'desc_ar' => 'حراس أمن مدربون، حراس شخصيون، ومنسقو أمن بتراخيص مناسبة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:car.svg?color=%23602234',
                    'title_en' => 'Drivers & Transportation',
                    'title_ar' => 'السائقون والنقل',
                    'desc_en' => 'Professional drivers, chauffeurs, and transportation coordinators with clean records.',
                    'desc_ar' => 'سائقون محترفون، سائقو سيارات خاصة، ومنسقو نقل بسجلات نظيفة.'
                ]
            ];

            foreach ($categories as $index => $category) :
            ?>
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="staff-category-card">
                    <div class="staff-category-icon">
                        <img src="<?php echo isset($category['icon_url']) ? $category['icon_url'] : 'https://cdn.iconscout.com/icon/premium/png-256-thumb/default-icon-1560000-1322000.png?f=webp&w=128'; ?>" alt="<?php echo $lang === 'ar' ? $category['title_ar'] : $category['title_en']; ?>" class="staff-category-icon-img">
                    </div>
                    <h4><?php echo $lang === 'ar' ? $category['title_ar'] : $category['title_en']; ?></h4>
                    <p><?php echo $lang === 'ar' ? $category['desc_ar'] : $category['desc_en']; ?></p>
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
                    ? 'عملية توظيف صارمة لضمان أفضل النتائج' 
                    : 'Rigorous recruitment process to ensure the best results'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $steps = [
                [
                    'number' => '01',
                    'title_en' => 'Needs Assessment',
                    'title_ar' => 'تقييم الاحتياجات',
                    'desc_en' => 'Understanding your specific requirements, household structure, and ideal candidate profile.',
                    'desc_ar' => 'فهم متطلباتك المحددة وهيكل المنزل وملف المرشح المثالي.'
                ],
                [
                    'number' => '02',
                    'title_en' => 'Candidate Sourcing',
                    'title_ar' => 'استقطاب المرشحين',
                    'desc_en' => 'Extensive search through our network and databases to identify qualified candidates.',
                    'desc_ar' => 'بحث شامل عبر شبكتنا وقواعد البيانات لتحديد المرشحين المؤهلين.'
                ],
                [
                    'number' => '03',
                    'title_en' => 'Rigorous Screening',
                    'title_ar' => 'الفحص الصارم',
                    'desc_en' => 'Multi-stage vetting including interviews, background checks, skills testing, and reference verification.',
                    'desc_ar' => 'فحص متعدد المراحل يشمل المقابلات، فحوصات الخلفية، اختبارات المهارات، والتحقق من المراجع.'
                ],
                [
                    'number' => '04',
                    'title_en' => 'Training & Placement',
                    'title_ar' => 'التدريب والتوظيف',
                    'desc_en' => 'Customized training, smooth onboarding, and ongoing support to ensure successful integration.',
                    'desc_ar' => 'تدريب مخصص، انضمام سلس، ودعم مستمر لضمان الاندماج الناجح.'
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
                    <img src="<?= url('assets/images/service-4.jpg') ?>" alt="<?php echo $lang === 'ar' ? 'فوائد الخدمة' : 'Service Benefits'; ?>" class="img-fluid service-detail-img">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title"><?php echo $lang === 'ar' ? 'لماذا تختار خدماتنا' : 'Why Choose Our Services'; ?></h2>
                <div class="benefits-list">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'فحص صارم' : 'Rigorous Vetting'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'عملية فحص متعددة المراحل تضمن أن كل موظف يلبي أعلى المعايير' : 'Multi-stage vetting process ensures every employee meets the highest standards'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'تدريب شامل' : 'Comprehensive Training'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'برامج تدريب مخصصة تغطي جميع جوانب الوظيفة والمعايير المطلوبة' : 'Customized training programs covering all aspects of the role and required standards'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'ضمان الجودة' : 'Quality Assurance'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'تقييم مستمر للأداء وضمان أن الموظفين يحافظون على المعايير العالية' : 'Continuous performance evaluation and assurance that staff maintain high standards'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'دعم مستمر' : 'Ongoing Support'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'متابعة مستمرة ودعم للموظفين وأصحاب العمل لضمان النجاح' : 'Continuous follow-up and support for both employees and employers to ensure success'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'سرية وخصوصية' : 'Confidentiality & Privacy'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'جميع العمليات تتم بسرية تامة واحترام للخصوصية' : 'All processes are conducted with complete discretion and respect for privacy'; ?></p>
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
                    'q_en' => 'What is included in your vetting process?',
                    'q_ar' => 'ما المدرج في عملية الفحص لديكم؟',
                    'a_en' => 'Our vetting process includes comprehensive background checks, reference verification, skills assessment, medical screening, security clearance, and character evaluation. We verify all credentials and conduct in-depth interviews.',
                    'a_ar' => 'تشمل عملية الفحص لدينا فحوصات الخلفية الشاملة، التحقق من المراجع، تقييم المهارات، الفحص الطبي، التصريح الأمني، وتقييم الشخصية. نتحقق من جميع الاعتمادات ونجري مقابلات متعمقة.'
                ],
                [
                    'q_en' => 'How long does the recruitment process take?',
                    'q_ar' => 'كم تستغرق عملية التوظيف؟',
                    'a_en' => 'The recruitment timeline varies based on position and requirements. Typically, the process takes 4-8 weeks from initial request to placement, including vetting, training, and onboarding.',
                    'a_ar' => 'يختلف الجدول الزمني للتوظيف حسب الوظيفة والمتطلبات. عادة، تستغرق العملية 4-8 أسابيع من الطلب الأولي إلى التوظيف، بما في ذلك الفحص والتدريب والانضمام.'
                ],
                [
                    'q_en' => 'Do you provide training for existing staff?',
                    'q_ar' => 'هل تقدمون التدريب للموظفين الحاليين؟',
                    'a_en' => 'Yes, we offer comprehensive training programs for existing staff members. This includes skill enhancement, protocol training, and professional development to ensure they meet current standards.',
                    'a_ar' => 'نعم، نقدم برامج تدريبية شاملة لأعضاء الفريق الحاليين. يشمل ذلك تعزيز المهارات، تدريب البروتوكول، والتطوير المهني لضمان تلبية المعايير الحالية.'
                ],
                [
                    'q_en' => 'What happens if a placed employee doesn\'t work out?',
                    'q_ar' => 'ماذا يحدث إذا لم ينجح الموظف الموظف؟',
                    'a_en' => 'We offer a replacement guarantee within the first 90 days. If an employee doesn\'t meet expectations, we will find a suitable replacement at no additional cost, after understanding the specific issues.',
                    'a_ar' => 'نقدم ضمان الاستبدال خلال أول 90 يوماً. إذا لم يلبي الموظف التوقعات، سنجد بديلاً مناسباً دون تكلفة إضافية، بعد فهم المشاكل المحددة.'
                ],
                [
                    'q_en' => 'Do you handle all legal and administrative requirements?',
                    'q_ar' => 'هل تتعاملون مع جميع المتطلبات القانونية والإدارية؟',
                    'a_en' => 'Yes, we assist with work permits, visa processing, contract preparation, and all necessary legal documentation. We ensure full compliance with local regulations and requirements.',
                    'a_ar' => 'نعم، نساعد في تصاريح العمل، معالجة التأشيرات، إعداد العقود، وجميع الوثائق القانونية اللازمة. نضمن الامتثال الكامل للوائح والمتطلبات المحلية.'
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
                <h2><?php echo $lang === 'ar' ? 'هل أنت مستعد لتوظيف فريق استثنائي؟' : 'Ready to Recruit an Exceptional Team?'; ?></h2>
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

