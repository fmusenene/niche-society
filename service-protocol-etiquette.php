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
$pageTitle = $lang === 'ar' ? 'البروتوكول والإتيكيت - نيتش سوسايتي' : 'Protocol & Etiquette Training - Niche Society';
$pageDescription = $lang === 'ar' ? 'برامج تدريبية مخصصة لتعزيز التواصل والسلوك في المواقف الرسمية واليومية بأعلى معايير الاحترافية.' : 'Tailored training programs to enhance communication and behavior in formal and everyday settings with the highest standards of professionalism.';

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
<a href="<?= url('services.php') ?>#service-4" class="back-button back-button-sticky">
    <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
    <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
</a>

<!-- Back Button & Page Header -->
<section class="service-detail-header">
    <div class="container">
        <div class="service-detail-nav">
            <a href="<?= url('services.php') ?>#service-4" class="back-button">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
            </a>
        </div>
        <div class="service-detail-title-section">
            <div class="service-badge-header"><?php echo formatNumber('04'); ?></div>
            <h1 class="service-detail-title"><?php echo $lang === 'ar' ? 'البروتوكول والإتيكيت' : 'Protocol & Etiquette Training'; ?></h1>
            <p class="service-detail-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'برامج تدريبية مخصصة لتعزيز التواصل والسلوك في المواقف الرسمية واليومية' 
                    : 'Tailored training programs to enhance communication and behavior in formal and everyday settings'; ?>
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
                        ? 'نقدم برامج تدريبية شاملة في البروتوكول والإتيكيت مصممة خصيصاً للأفراد والعائلات والمؤسسات. خبرتنا تمتد لأكثر من 25 عاماً في خدمة الشخصيات الرفيعة.' 
                        : 'We offer comprehensive training programs in protocol and etiquette specifically designed for individuals, families, and institutions. Our experience spans over 25 years serving distinguished personalities.'; ?>
                </p>
                <p>
                    <?php echo $lang === 'ar' 
                        ? 'برامجنا تغطي جميع جوانب السلوك الرسمي والاجتماعي، من البروتوكول الملكي إلى إتيكيت المائدة والضيافة، مع التركيز على التطبيق العملي والثقة في المواقف المختلفة.' 
                        : 'Our programs cover all aspects of formal and social behavior, from royal protocol to table etiquette and hospitality, with focus on practical application and confidence in various situations.'; ?>
                </p>
                <div class="service-stats mt-4">
                    <div class="stat-box">
                        <h3><?php echo formatNumber('500'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'متدرب ناجح' : 'Successful Trainees'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo formatNumber('25'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'عاماً من الخبرة' : 'Years Experience'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>100%</h3>
                        <p><?php echo $lang === 'ar' ? 'معدل الرضا' : 'Satisfaction Rate'; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="service-image-wrapper">
                    <img src="<?= url('assets/images/service-6.jpg') ?>" alt="<?php echo $lang === 'ar' ? 'البروتوكول والإتيكيت' : 'Protocol & Etiquette'; ?>" class="img-fluid service-detail-img">
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
                    ? 'برامج تدريبية شاملة تغطي جميع جوانب البروتوكول والإتيكيت' 
                    : 'Comprehensive training programs covering all aspects of protocol and etiquette'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $features = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:crown.svg?color=%23602234',
                    'title_en' => 'Royal & Official Protocol',
                    'title_ar' => 'البروتوكول الملكي والرسمي',
                    'desc_en' => 'Training in royal protocol, official ceremonies, diplomatic etiquette, and formal event conduct.',
                    'desc_ar' => 'التدريب على البروتوكول الملكي، المراسم الرسمية، الإتيكيت الدبلوماسي، وسلوك الفعاليات الرسمية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:silverware-fork-knife.svg?color=%23602234',
                    'title_en' => 'Table Etiquette & Dining',
                    'title_ar' => 'إتيكيت المائدة والطعام',
                    'desc_en' => 'Comprehensive dining etiquette, table settings, and formal meal conduct.',
                    'desc_ar' => 'إتيكيت الطعام الشامل، ترتيبات المائدة، وسلوك الوجبات الرسمية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:handshake.svg?color=%23602234',
                    'title_en' => 'Social Etiquette',
                    'title_ar' => 'الإتيكيت الاجتماعي',
                    'desc_en' => 'Personal introductions, conversation skills, networking, and social interaction protocols.',
                    'desc_ar' => 'التعارف الشخصي، مهارات المحادثة، التواصل، وبروتوكولات التفاعل الاجتماعي.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account-tie.svg?color=%23602234',
                    'title_en' => 'Business Protocol',
                    'title_ar' => 'البروتوكول التجاري',
                    'desc_en' => 'Corporate etiquette, meeting conduct, business correspondence, and professional presentation.',
                    'desc_ar' => 'الإتيكيت التجاري، سلوك الاجتماعات، المراسلات التجارية، والعرض المهني.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:school.svg?color=%23602234',
                    'title_en' => 'School Etiquette Programs',
                    'title_ar' => 'برامج إتيكيت المدارس',
                    'desc_en' => 'Age-appropriate etiquette training for students, building confidence and social skills.',
                    'desc_ar' => 'تدريب إتيكيت مناسب للعمر للطلاب، بناء الثقة والمهارات الاجتماعية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:home-heart.svg?color=%23602234',
                    'title_en' => 'Hospitality & Hosting',
                    'title_ar' => 'الضيافة والاستضافة',
                    'desc_en' => 'Hosting etiquette, guest reception, event hosting, and creating welcoming environments.',
                    'desc_ar' => 'إتيكيت الاستضافة، استقبال الضيوف، استضافة الفعاليات، وخلق بيئات ترحيبية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:tshirt-crew.svg?color=%23602234',
                    'title_en' => 'Dress Code & Appearance',
                    'title_ar' => 'قواعد اللباس والمظهر',
                    'desc_en' => 'Appropriate attire for various occasions, grooming standards, and professional presentation.',
                    'desc_ar' => 'اللباس المناسب للمناسبات المختلفة، معايير العناية الشخصية، والعرض المهني.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:message-text.svg?color=%23602234',
                    'title_en' => 'Communication Skills',
                    'title_ar' => 'مهارات التواصل',
                    'desc_en' => 'Verbal and non-verbal communication, public speaking, and confident expression.',
                    'desc_ar' => 'التواصل اللفظي وغير اللفظي، الخطابة العامة، والتعبير الواثق.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:earth.svg?color=%23602234',
                    'title_en' => 'Cultural Awareness',
                    'title_ar' => 'الوعي الثقافي',
                    'desc_en' => 'Cross-cultural etiquette, international protocol, and respectful cultural interaction.',
                    'desc_ar' => 'الإتيكيت بين الثقافات، البروتوكول الدولي، والتفاعل الثقافي المحترم.'
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

<!-- Training Programs Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'برامجنا التدريبية' : 'Our Training Programs'; ?></h2>
            <p class="section-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'برامج مخصصة لجميع المستويات والأعمار' 
                    : 'Customized programs for all levels and ages'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $programs = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account.svg?color=%23602234',
                    'title_en' => 'Individual Coaching',
                    'title_ar' => 'التدريب الفردي',
                    'desc_en' => 'One-on-one personalized training sessions tailored to your specific needs and goals.',
                    'desc_ar' => 'جلسات تدريبية شخصية فردية مصممة خصيصاً لاحتياجاتك وأهدافك المحددة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account-group.svg?color=%23602234',
                    'title_en' => 'Family Programs',
                    'title_ar' => 'برامج العائلات',
                    'desc_en' => 'Comprehensive family etiquette training for all members, including children and teenagers.',
                    'desc_ar' => 'تدريب إتيكيت عائلي شامل لجميع الأعضاء، بما في ذلك الأطفال والمراهقين.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:office-building.svg?color=%23602234',
                    'title_en' => 'Corporate Training',
                    'title_ar' => 'التدريب المؤسسي',
                    'desc_en' => 'Group training programs for businesses, focusing on professional protocol and business etiquette.',
                    'desc_ar' => 'برامج تدريبية جماعية للشركات، تركز على البروتوكول المهني وإتيكيت الأعمال.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:school.svg?color=%23602234',
                    'title_en' => 'Educational Institutions',
                    'title_ar' => 'المؤسسات التعليمية',
                    'desc_en' => 'Age-appropriate etiquette programs for schools, building confidence and social skills in students.',
                    'desc_ar' => 'برامج إتيكيت مناسبة للعمر للمدارس، بناء الثقة والمهارات الاجتماعية لدى الطلاب.'
                ]
            ];

            foreach ($programs as $index => $program) :
            ?>
            <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="program-card">
                    <div class="program-icon">
                        <?php if (isset($program['icon_url'])): ?>
                            <img src="<?php echo $program['icon_url']; ?>" alt="<?php echo $lang === 'ar' ? $program['title_ar'] : $program['title_en']; ?>" class="program-icon-img">
                        <?php elseif (isset($program['icon'])): ?>
                            <i class="fas <?php echo $program['icon']; ?>"></i>
                        <?php endif; ?>
                    </div>
                    <h4><?php echo $lang === 'ar' ? $program['title_ar'] : $program['title_en']; ?></h4>
                    <p><?php echo $lang === 'ar' ? $program['desc_ar'] : $program['desc_en']; ?></p>
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
                    ? 'منهجية تدريب احترافية لضمان أفضل النتائج' 
                    : 'Professional training methodology to ensure the best results'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $steps = [
                [
                    'number' => '01',
                    'title_en' => 'Assessment & Consultation',
                    'title_ar' => 'التقييم والاستشارة',
                    'desc_en' => 'Understanding your needs, current level, and specific goals to design a customized program.',
                    'desc_ar' => 'فهم احتياجاتك ومستواك الحالي والأهداف المحددة لتصميم برنامج مخصص.'
                ],
                [
                    'number' => '02',
                    'title_en' => 'Program Design',
                    'title_ar' => 'تصميم البرنامج',
                    'desc_en' => 'Creating a tailored curriculum that addresses your specific requirements and learning objectives.',
                    'desc_ar' => 'إنشاء منهج مخصص يتناول متطلباتك وأهداف التعلم المحددة.'
                ],
                [
                    'number' => '03',
                    'title_en' => 'Interactive Training',
                    'title_ar' => 'التدريب التفاعلي',
                    'desc_en' => 'Hands-on training sessions with practical exercises, role-playing, and real-world scenarios.',
                    'desc_ar' => 'جلسات تدريبية عملية مع تمارين عملية وتمثيل الأدوار وسيناريوهات من العالم الحقيقي.'
                ],
                [
                    'number' => '04',
                    'title_en' => 'Ongoing Support',
                    'title_ar' => 'الدعم المستمر',
                    'desc_en' => 'Follow-up sessions, progress evaluation, and continuous guidance to ensure lasting improvement.',
                    'desc_ar' => 'جلسات المتابعة، تقييم التقدم، والإرشاد المستمر لضمان التحسين الدائم.'
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
                    <img src="<?= url('assets/images/service-6.jpg') ?>" alt="<?php echo $lang === 'ar' ? 'فوائد الخدمة' : 'Service Benefits'; ?>" class="img-fluid service-detail-img">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title"><?php echo $lang === 'ar' ? 'لماذا تختار خدماتنا' : 'Why Choose Our Services'; ?></h2>
                <div class="benefits-list">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'خبرة ملكية' : 'Royal Experience'; ?></h4>
                            <p><?php echo $lang === 'ar' ? '25 عاماً من الخبرة في خدمة الشخصيات الرفيعة' : '25 years of experience serving distinguished personalities'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'برامج مخصصة' : 'Customized Programs'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'كل برنامج مصمم خصيصاً لاحتياجاتك وأهدافك الفريدة' : 'Every program designed specifically for your unique needs and goals'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'تدريب عملي' : 'Practical Training'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'تركيز على التطبيق العملي والثقة في المواقف الحقيقية' : 'Focus on practical application and confidence in real situations'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'بيئة مريحة' : 'Comfortable Environment'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'تدريب في بيئة مريحة وخاصة تحترم خصوصيتك' : 'Training in a comfortable and private environment that respects your privacy'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'نتائج مضمونة' : 'Guaranteed Results'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'تحسين ملحوظ في الثقة والمهارات الاجتماعية والمهنية' : 'Noticeable improvement in confidence and social and professional skills'; ?></p>
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
                    'q_en' => 'Who can benefit from protocol and etiquette training?',
                    'q_ar' => 'من يمكنه الاستفادة من تدريب البروتوكول والإتيكيت؟',
                    'a_en' => 'Our programs benefit individuals, families, corporate executives, students, and anyone seeking to enhance their social and professional presence. We customize programs for all ages and backgrounds.',
                    'a_ar' => 'تستفيد برامجنا الأفراد والعائلات والتنفيذيين التجاريين والطلاب وأي شخص يسعى لتعزيز حضوره الاجتماعي والمهني. نخصص البرامج لجميع الأعمار والخلفيات.'
                ],
                [
                    'q_en' => 'How long does a typical training program last?',
                    'q_ar' => 'كم تستغرق برنامج التدريب النموذجي؟',
                    'a_en' => 'Program duration varies based on individual needs and goals. We offer intensive workshops (1-3 days), comprehensive courses (4-12 weeks), and ongoing coaching programs. We design the timeline to fit your schedule.',
                    'a_ar' => 'تختلف مدة البرنامج حسب الاحتياجات والأهداف الفردية. نقدم ورش عمل مكثفة (1-3 أيام)، دورات شاملة (4-12 أسبوعاً)، وبرامج تدريب مستمرة. نصمم الجدول الزمني ليناسب جدولك.'
                ],
                [
                    'q_en' => 'Do you offer training in Arabic and English?',
                    'q_ar' => 'هل تقدمون التدريب بالعربية والإنجليزية؟',
                    'a_en' => 'Yes, we offer training in both Arabic and English, and can accommodate bilingual programs. Our trainers are fluent in both languages and understand cultural nuances.',
                    'a_ar' => 'نعم، نقدم التدريب بالعربية والإنجليزية، ويمكننا استيعاب البرامج ثنائية اللغة. مدربونا يجيدون كلا اللغتين ويفهمون الفروق الثقافية.'
                ],
                [
                    'q_en' => 'Can training be conducted at our location?',
                    'q_ar' => 'هل يمكن إجراء التدريب في موقعنا؟',
                    'a_en' => 'Absolutely. We offer both on-site training at your location and sessions at our facilities. We can also arrange training at private venues for maximum comfort and discretion.',
                    'a_ar' => 'بالتأكيد. نقدم التدريب في موقعك والجلسات في مرافقنا. يمكننا أيضاً ترتيب التدريب في أماكن خاصة لأقصى راحة وخصوصية.'
                ],
                [
                    'q_en' => 'What makes your training different from others?',
                    'q_ar' => 'ما الذي يجعل تدريبكم مختلفاً عن الآخرين؟',
                    'a_en' => 'Our 25-year experience serving distinguished clients gives us unique insight into the highest standards of protocol and etiquette. We combine traditional values with modern practices in a practical, personalized approach.',
                    'a_ar' => 'خبرتنا لمدة 25 عاماً في خدمة العملاء المميزين تمنحنا رؤية فريدة لأعلى معايير البروتوكول والإتيكيت. نجمع بين القيم التقليدية والممارسات الحديثة في نهج عملي وشخصي.'
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
                <h2><?php echo $lang === 'ar' ? 'هل أنت مستعد لتعزيز مهاراتك في البروتوكول والإتيكيت؟' : 'Ready to Enhance Your Protocol & Etiquette Skills?'; ?></h2>
                <p class="lead mb-4">
                    <?php echo $lang === 'ar' 
                        ? 'احصل على استشارة مجانية لمناقشة احتياجاتك التدريبية' 
                        : 'Get a free consultation to discuss your training needs'; ?>
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

