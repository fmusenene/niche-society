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
$pageTitle = $lang === 'ar' ? 'تنظيم الفعاليات - نيتش سوسايتي' : 'Event Management - Niche Society';
$pageDescription = $lang === 'ar' ? 'تخطيط وتنفيذ فعاليات استثنائية من الألف إلى الياء بأعلى معايير الاحترافية والأناقة.' : 'Planning and executing exceptional events from A to Z with the highest standards of professionalism and elegance.';

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
<a href="<?= url('services.php') ?>#service-3" class="back-button back-button-sticky">
    <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
    <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
</a>

<!-- Back Button & Page Header -->
<section class="service-detail-header">
    <div class="container">
        <div class="service-detail-nav">
            <a href="<?= url('services.php') ?>#service-3" class="back-button">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
            </a>
        </div>
        <div class="service-detail-title-section">
            <div class="service-badge-header"><?php echo formatNumber('03'); ?></div>
            <h1 class="service-detail-title"><?php echo $lang === 'ar' ? 'تنظيم الفعاليات' : 'Event Management'; ?></h1>
            <p class="service-detail-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'تخطيط وتنفيذ فعاليات استثنائية من الألف إلى الياء بأعلى معايير الاحترافية' 
                    : 'Planning and executing exceptional events from A to Z with the highest standards of professionalism'; ?>
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
                        ? 'نحول رؤيتك إلى واقع استثنائي. من الحفلات الملكية والمناسبات الرسمية إلى الفعاليات التجارية والاحتفالات الخاصة، نضمن تنفيذاً مثالياً يليق بمكانتك.' 
                        : 'We transform your vision into exceptional reality. From royal galas and official ceremonies to corporate events and private celebrations, we ensure flawless execution worthy of your stature.'; ?>
                </p>
                <p>
                    <?php echo $lang === 'ar' 
                        ? 'فريقنا المتخصص يتولى كل التفاصيل من التخطيط الاستراتيجي إلى التنفيذ المثالي، مع الحفاظ على أعلى معايير الأناقة والبروتوكول والسرية.' 
                        : 'Our specialized team handles every detail from strategic planning to flawless execution, while maintaining the highest standards of elegance, protocol, and discretion.'; ?>
                </p>
                <div class="service-stats mt-4">
                    <div class="stat-box">
                        <h3><?php echo formatNumber('1000'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'فعالية ناجحة' : 'Successful Events'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo formatNumber('25'); ?>+</h3>
                        <p><?php echo $lang === 'ar' ? 'عاماً من الخبرة' : 'Years Experience'; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>100%</h3>
                        <p><?php echo $lang === 'ar' ? 'رضا العملاء' : 'Client Satisfaction'; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="service-image-wrapper">
                    <img src="<?= url('assets/images/service-5.jpg') ?>" alt="<?php echo $lang === 'ar' ? 'تنظيم الفعاليات' : 'Event Management'; ?>" class="img-fluid service-detail-img">
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
                    ? 'خدمات شاملة لتجربة فعالية لا تُنسى' 
                    : 'Comprehensive services for an unforgettable event experience'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $features = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:calendar-multiple-check.svg?color=%23602234',
                    'title_en' => 'Strategic Planning',
                    'title_ar' => 'التخطيط الاستراتيجي',
                    'desc_en' => 'Comprehensive event planning including timeline development, budget management, and contingency planning.',
                    'desc_ar' => 'تخطيط شامل للفعاليات يشمل تطوير الجدول الزمني وإدارة الميزانية والتخطيط للطوارئ.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:palette.svg?color=%23602234',
                    'title_en' => 'Creative Design',
                    'title_ar' => 'التصميم الإبداعي',
                    'desc_en' => 'Custom theme development, décor design, floral arrangements, and visual aesthetics that reflect your style.',
                    'desc_ar' => 'تطوير موضوع مخصص، تصميم الديكور، ترتيبات الزهور، والجماليات البصرية التي تعكس أسلوبك.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:silverware-fork-knife.svg?color=%23602234',
                    'title_en' => 'Culinary Excellence',
                    'title_ar' => 'التميز في المأكولات',
                    'desc_en' => 'World-class catering services, custom menus, dietary accommodations, and presentation that exceeds expectations.',
                    'desc_ar' => 'خدمات تقديم طعام عالمية المستوى، قوائم مخصصة، استيعابات غذائية، وعرض يتجاوز التوقعات.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account-network.svg?color=%23602234',
                    'title_en' => 'Vendor Management',
                    'title_ar' => 'إدارة الموردين',
                    'desc_en' => 'Coordination with trusted vendors, suppliers, and service providers to ensure seamless execution.',
                    'desc_ar' => 'التنسيق مع الموردين والمزودين ومقدمي الخدمات الموثوقين لضمان تنفيذ سلس.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:microphone.svg?color=%23602234',
                    'title_en' => 'Audio-Visual Production',
                    'title_ar' => 'الإنتاج السمعي البصري',
                    'desc_en' => 'State-of-the-art sound systems, lighting design, video production, and technical support.',
                    'desc_ar' => 'أنظمة صوتية حديثة، تصميم الإضاءة، إنتاج الفيديو، والدعم الفني.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:account-tie.svg?color=%23602234',
                    'title_en' => 'Protocol & Etiquette',
                    'title_ar' => 'البروتوكول والإتيكيت',
                    'desc_en' => 'Royal protocol adherence, guest management, seating arrangements, and formal ceremony coordination.',
                    'desc_ar' => 'الالتزام بالبروتوكول الملكي، إدارة الضيوف، ترتيبات الجلوس، وتنسيق المراسم الرسمية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:shield-check.svg?color=%23602234',
                    'title_en' => 'Security & Safety',
                    'title_ar' => 'الأمن والسلامة',
                    'desc_en' => 'Comprehensive security planning, access control, emergency protocols, and guest safety measures.',
                    'desc_ar' => 'تخطيط أمني شامل، التحكم في الوصول، بروتوكولات الطوارئ، وإجراءات سلامة الضيوف.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:handshake.svg?color=%23602234',
                    'title_en' => 'Guest Services',
                    'title_ar' => 'خدمات الضيوف',
                    'desc_en' => 'Personalized guest assistance, VIP treatment, transportation coordination, and hospitality management.',
                    'desc_ar' => 'مساعدة شخصية للضيوف، معاملة VIP، تنسيق النقل، وإدارة الضيافة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:clipboard-check.svg?color=%23602234',
                    'title_en' => 'Day-of Coordination',
                    'title_ar' => 'التنسيق يوم الفعالية',
                    'desc_en' => 'On-site event management, real-time problem solving, and seamless execution oversight.',
                    'desc_ar' => 'إدارة الفعالية في الموقع، حل المشاكل في الوقت الفعلي، والإشراف على التنفيذ السلس.'
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

<!-- Event Types Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title"><?php echo $lang === 'ar' ? 'أنواع الفعاليات' : 'Event Types'; ?></h2>
            <p class="section-subtitle">
                <?php echo $lang === 'ar' 
                    ? 'ننظم جميع أنواع الفعاليات باحترافية عالية' 
                    : 'We organize all types of events with high professionalism'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $eventTypes = [
                [
                    'icon_url' => 'https://api.iconify.design/mdi:crown.svg?color=%23602234',
                    'title_en' => 'Royal & Official Events',
                    'title_ar' => 'الفعاليات الملكية والرسمية',
                    'desc_en' => 'State ceremonies, royal galas, diplomatic receptions, and official celebrations.',
                    'desc_ar' => 'المراسم الرسمية، الحفلات الملكية، الاستقبالات الدبلوماسية، والاحتفالات الرسمية.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:heart.svg?color=%23602234',
                    'title_en' => 'Private Celebrations',
                    'title_ar' => 'الاحتفالات الخاصة',
                    'desc_en' => 'Weddings, anniversaries, birthdays, and intimate family gatherings with complete privacy.',
                    'desc_ar' => 'الأعراس، الذكريات، أعياد الميلاد، والتجمعات العائلية الحميمة بخصوصية تامة.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:briefcase.svg?color=%23602234',
                    'title_en' => 'Corporate Events',
                    'title_ar' => 'الفعاليات التجارية',
                    'desc_en' => 'Product launches, conferences, corporate galas, and business networking events.',
                    'desc_ar' => 'إطلاق المنتجات، المؤتمرات، الحفلات التجارية، وفعاليات التواصل التجاري.'
                ],
                [
                    'icon_url' => 'https://api.iconify.design/mdi:gift.svg?color=%23602234',
                    'title_en' => 'Charity & Fundraising',
                    'title_ar' => 'الفعاليات الخيرية وجمع التبرعات',
                    'desc_en' => 'Charity galas, fundraising dinners, and philanthropic events with maximum impact.',
                    'desc_ar' => 'الحفلات الخيرية، عشاء جمع التبرعات، والفعاليات الخيرية بأقصى تأثير.'
                ]
            ];

            foreach ($eventTypes as $index => $type) :
            ?>
            <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="event-type-card">
                    <div class="event-type-icon">
                        <?php if (isset($type['icon_url'])): ?>
                            <img src="<?php echo $type['icon_url']; ?>" alt="<?php echo $lang === 'ar' ? $type['title_ar'] : $type['title_en']; ?>" class="event-type-icon-img">
                        <?php elseif (isset($type['icon'])): ?>
                            <i class="fas <?php echo $type['icon']; ?>"></i>
                        <?php endif; ?>
                    </div>
                    <h4><?php echo $lang === 'ar' ? $type['title_ar'] : $type['title_en']; ?></h4>
                    <p><?php echo $lang === 'ar' ? $type['desc_ar'] : $type['desc_en']; ?></p>
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
                    ? 'منهجية عمل احترافية لضمان فعالية ناجحة' 
                    : 'Professional methodology to ensure a successful event'; ?>
            </p>
        </div>

        <div class="row">
            <?php
            $steps = [
                [
                    'number' => '01',
                    'title_en' => 'Consultation & Vision',
                    'title_ar' => 'الاستشارة والرؤية',
                    'desc_en' => 'Understanding your vision, requirements, and expectations to create a customized event concept.',
                    'desc_ar' => 'فهم رؤيتك ومتطلباتك وتوقعاتك لإنشاء مفهوم فعالية مخصص.'
                ],
                [
                    'number' => '02',
                    'title_en' => 'Design & Planning',
                    'title_ar' => 'التصميم والتخطيط',
                    'desc_en' => 'Developing detailed event design, timeline, budget, and comprehensive execution plan.',
                    'desc_ar' => 'تطوير تصميم الفعالية التفصيلي، الجدول الزمني، الميزانية، وخطة التنفيذ الشاملة.'
                ],
                [
                    'number' => '03',
                    'title_en' => 'Coordination & Preparation',
                    'title_ar' => 'التنسيق والتحضير',
                    'desc_en' => 'Vendor coordination, venue preparation, logistics management, and finalizing all details.',
                    'desc_ar' => 'تنسيق الموردين، تحضير المكان، إدارة اللوجستيات، وإتمام جميع التفاصيل.'
                ],
                [
                    'number' => '04',
                    'title_en' => 'Flawless Execution',
                    'title_ar' => 'التنفيذ المثالي',
                    'desc_en' => 'On-site management, real-time coordination, and ensuring every moment exceeds expectations.',
                    'desc_ar' => 'الإدارة في الموقع، التنسيق في الوقت الفعلي، وضمان تجاوز كل لحظة للتوقعات.'
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
                    <img src="<?= url('assets/images/service-5.jpg') ?>" alt="<?php echo $lang === 'ar' ? 'فوائد الخدمة' : 'Service Benefits'; ?>" class="img-fluid service-detail-img">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title"><?php echo $lang === 'ar' ? 'لماذا تختار خدماتنا' : 'Why Choose Our Services'; ?></h2>
                <div class="benefits-list">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'تجربة لا تُنسى' : 'Unforgettable Experience'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'فعاليات مصممة خصيصاً تترك انطباعاً دائماً على ضيوفك' : 'Custom-designed events that leave a lasting impression on your guests'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'اهتمام بكل التفاصيل' : 'Attention to Every Detail'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'لا نترك أي تفصيل للصدفة، كل شيء مخطط ومنسق بدقة' : 'We leave nothing to chance, everything is planned and coordinated precisely'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'بروتوكول واحترافية' : 'Protocol & Professionalism'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'الالتزام الكامل بالبروتوكولات الرسمية وأعلى معايير الاحترافية' : 'Complete adherence to official protocols and highest standards of professionalism'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'راحة البال' : 'Peace of Mind'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'استمتع بفعاليتك بينما نتولى جميع التفاصيل' : 'Enjoy your event while we handle all the details'; ?></p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $lang === 'ar' ? 'شبكة موثوقة من الموردين' : 'Trusted Vendor Network'; ?></h4>
                            <p><?php echo $lang === 'ar' ? 'علاقات طويلة الأمد مع أفضل الموردين ومقدمي الخدمات' : 'Long-standing relationships with the best vendors and service providers'; ?></p>
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
                    'q_en' => 'How far in advance should I book your services?',
                    'q_ar' => 'كم مقدماً يجب أن أحجز خدماتكم؟',
                    'a_en' => 'We recommend booking at least 3-6 months in advance for large events, though we can accommodate shorter timelines for smaller gatherings. Early planning ensures optimal vendor availability and pricing.',
                    'a_ar' => 'نوصي بالحجز قبل 3-6 أشهر على الأقل للفعاليات الكبيرة، رغم أننا يمكننا استيعاب جداول زمنية أقصر للتجمعات الأصغر. التخطيط المبكر يضمن توفر الموردين الأمثل والأسعار.'
                ],
                [
                    'q_en' => 'Do you handle events outside Saudi Arabia?',
                    'q_ar' => 'هل تتعاملون مع فعاليات خارج المملكة العربية السعودية؟',
                    'a_en' => 'Yes, we organize events internationally. Our team has experience managing events in the Gulf region, Europe, and other destinations. We coordinate all logistics including travel and accommodation.',
                    'a_ar' => 'نعم، نحن ننظم الفعاليات دولياً. فريقنا لديه خبرة في إدارة الفعاليات في منطقة الخليج وأوروبا ووجهات أخرى. نتولى تنسيق جميع اللوجستيات بما في ذلك السفر والإقامة.'
                ],
                [
                    'q_en' => 'What is included in your event management package?',
                    'q_ar' => 'ما المدرج في حزمة إدارة الفعاليات؟',
                    'a_en' => 'Our packages include comprehensive planning, design, vendor coordination, day-of management, and post-event follow-up. Specific inclusions vary based on event type and requirements. We provide detailed proposals for each event.',
                    'a_ar' => 'تشمل حزمنا التخطيط الشامل، التصميم، تنسيق الموردين، الإدارة يوم الفعالية، والمتابعة بعد الفعالية. التضمينات المحددة تختلف حسب نوع الفعالية والمتطلبات. نقدم عروضاً تفصيلية لكل فعالية.'
                ],
                [
                    'q_en' => 'How do you ensure privacy and discretion?',
                    'q_ar' => 'كيف تضمنون الخصوصية والسرية؟',
                    'a_en' => 'All our staff sign strict NDAs and are trained in discretion protocols. We have a 25-year track record of maintaining complete confidentiality. We never share client information or event details publicly.',
                    'a_ar' => 'جميع موظفينا يوقعون اتفاقيات سرية صارمة ويتدربون على بروتوكولات السرية. لدينا سجل حافل لمدة 25 عاماً في الحفاظ على السرية التامة. لا نشارك معلومات العملاء أو تفاصيل الفعاليات علناً أبداً.'
                ],
                [
                    'q_en' => 'Can you work with our existing vendors?',
                    'q_ar' => 'هل يمكنكم العمل مع موردينا الحاليين؟',
                    'a_en' => 'Absolutely. We can integrate your preferred vendors into our management structure. We also have an extensive network of trusted partners we can recommend if needed.',
                    'a_ar' => 'بالتأكيد. يمكننا دمج مورديك المفضلين في هيكل إدارتنا. لدينا أيضاً شبكة واسعة من الشركاء الموثوقين يمكننا التوصية بهم إذا لزم الأمر.'
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
                <h2><?php echo $lang === 'ar' ? 'هل أنت مستعد لفعالية استثنائية؟' : 'Ready for an Exceptional Event?'; ?></h2>
                <p class="lead mb-4">
                    <?php echo $lang === 'ar' 
                        ? 'احصل على استشارة مجانية لمناقشة فعاليتك' 
                        : 'Get a free consultation to discuss your event'; ?>
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

