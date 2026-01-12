<?php
/**
 * About Us Page - Niche Society
 * Professional and Beautiful Redesign
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/helpers.php';

// Handle language switch
handleLanguageSwitch();

$lang = getCurrentLang();
$dir = getTextDirection($lang);

$pageTitle = $lang === 'ar' ? 'من نحن - نيش سوسيتي' : 'About Us - Niche Society';
$pageDescription = $lang === 'ar'
    ? 'شركة نيش سوسيتي - خبرة في إدارة الممتلكات والفعاليات والخدمات الفاخرة'
    : 'Niche Society - Excellence in estate management, events, and luxury services';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
    <?= getMetaTags($pageTitle, $pageDescription, getCurrentUrl()) ?>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <?php if ($lang === 'ar'): ?>
        <link rel="stylesheet" href="<?= url('assets/css/rtl.css') ?>">
    <?php endif; ?>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-premium">
    <!-- Background Image -->
    <div class="hero-bg-container">
        <div class="hero-bg-image" style="background-image: url('<?= url('assets/images/TEAM-scaled.jpg') ?>');"></div>
        <div class="hero-black-overlay"></div>
    </div>
    
    <!-- Hero Content -->
    <div class="hero-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <!-- Title -->
                    <div class="hero-text-animated">
                        <h1 class="hero-main-title">
                            <?= $lang === 'ar' ? 'من نحن' : 'About Us' ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Company Overview - Enhanced -->
<section class="section about-overview-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <div class="overview-content-wrapper">
                    <span class="section-badge-professional">
                        <i class="bi bi-award-fill"></i>
                        <?= $lang === 'ar' ? 'من نحن' : 'About Us' ?>
                    </span>
                    <h2 class="section-title-professional">
                        <?= $lang === 'ar' ? 'من هي نيش سوسيتي؟' : 'Who is Niche Society?' ?>
                    </h2>
                    <div class="divider-professional"></div>
                    <p class="lead-text-professional">
                        <?= $lang === 'ar'
                            ? 'نيش سوسيتي شركة متخصصة في تقديم حلول إدارية وتنظيمية بمعايير تعيد تعريف معنى التميز، تشمل إدارة الممتلكات الخاصة، العقارات، البروتوكول والإتيكيت الرسمي، اللوجستيات، العلاقات العامة، والخدمات التشغيلية الفاخرة.'
                            : 'Niche Society is a company specialized in providing administrative and organizational solutions with standards that redefine the meaning of excellence, including private property management, real estate, official etiquette and protocols, logistics, public relations, and high-end operational services.' ?>
                    </p>
                    <p class="text-professional">
                        <?= $lang === 'ar'
                            ? 'مع أكثر من 25 عاماً من الخبرة في خدمة الشخصيات البارزة والعملاء الدوليين، ندير العمليات وننسق التفاصيل بأسلوب يجمع بين الدقة والخصوصية والأناقة.'
                            : 'With over 25 years of experience serving high-profile individuals and international clients, we manage operations and coordinate details in a style that combines precision, privacy, and sophistication.' ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="overview-features-grid">
                    <div class="feature-item-professional">
                        <div class="feature-icon-professional">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="feature-content-professional">
                            <h4><?= $lang === 'ar' ? 'معتمد ISO 9001:2015' : 'ISO 9001:2015 Certified' ?></h4>
                            <p><?= $lang === 'ar' ? 'معايير جودة عالمية' : 'International quality standards' ?></p>
                        </div>
                    </div>
                    <div class="feature-item-professional">
                        <div class="feature-icon-professional">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="feature-content-professional">
                            <h4><?= $lang === 'ar' ? '25+ عاماً من الخبرة' : '25+ Years Experience' ?></h4>
                            <p><?= $lang === 'ar' ? 'خبرة متراكمة' : 'Accumulated expertise' ?></p>
                        </div>
                    </div>
                    <div class="feature-item-professional">
                        <div class="feature-icon-professional">
                            <i class="bi bi-incognito"></i>
                        </div>
                        <div class="feature-content-professional">
                            <h4><?= $lang === 'ar' ? 'سرية تامة' : 'Complete Discretion' ?></h4>
                            <p><?= $lang === 'ar' ? 'خصوصية مطلقة' : 'Absolute privacy' ?></p>
                        </div>
                    </div>
                    <div class="feature-item-professional">
                        <div class="feature-icon-professional">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="feature-content-professional">
                            <h4><?= $lang === 'ar' ? 'تميز في الخدمة' : 'Service Excellence' ?></h4>
                            <p><?= $lang === 'ar' ? 'معايير عالية' : 'High standards' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission, Vision, Values - Redesigned -->
<section class="section bg-cream about-mvv-section-professional">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge-professional">
                <i class="bi bi-heart-fill"></i>
                <?= $lang === 'ar' ? 'قيمنا الأساسية' : 'Our Core Values' ?>
            </span>
            <h2 class="section-title-professional"><?= $lang === 'ar' ? 'رؤيتنا ورسالتنا وقيمنا' : 'Our Mission, Vision & Values' ?></h2>
            <div class="divider-professional mx-auto"></div>
            <p class="lead-text-professional">
                <?= $lang === 'ar'
                    ? 'الأساس الذي نبني عليه تميزنا'
                    : 'The foundation upon which we build our excellence' ?>
            </p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <!-- Mission -->
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="mvv-card-professional">
                    <div class="mvv-card-number"><?php echo formatNumber('01'); ?></div>
                    <div class="mvv-card-corner top-left"></div>
                    <div class="mvv-card-corner bottom-right"></div>
                    <div class="mvv-card-icon">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h3 class="mvv-card-title"><?= $lang === 'ar' ? 'الرسالة' : 'Our Mission' ?></h3>
                    <div class="mvv-card-underline"></div>
                    <p class="mvv-card-text">
                        <?= $lang === 'ar'
                            ? 'تقديم حلول إدارية وتنظيمية استثنائية تجمع بين الكفاءة التشغيلية والأناقة، مع الحفاظ على أعلى معايير الخصوصية والسرية للعملاء المميزين.'
                            : 'To deliver exceptional administrative and organizational solutions that combine operational efficiency with elegance, while maintaining the highest standards of privacy and confidentiality for distinguished clients.' ?>
                    </p>
                </div>
            </div>
            
            <!-- Vision -->
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="mvv-card-professional">
                    <div class="mvv-card-number"><?php echo formatNumber('02'); ?></div>
                    <div class="mvv-card-corner top-left"></div>
                    <div class="mvv-card-corner bottom-right"></div>
                    <div class="mvv-card-icon">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <h3 class="mvv-card-title"><?= $lang === 'ar' ? 'الرؤية' : 'Our Vision' ?></h3>
                    <div class="mvv-card-underline"></div>
                    <p class="mvv-card-text">
                        <?= $lang === 'ar'
                            ? 'أن نكون المرجع الأول في مجال الخدمات الإدارية الفاخرة، معترف بنا عالمياً لتميزنا في خدمة الشخصيات الرفيعة.'
                            : 'To be the leading reference in luxury administrative services, globally recognized for our excellence in serving distinguished personalities.' ?>
                    </p>
                </div>
            </div>
            
            <!-- Values -->
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="mvv-card-professional">
                    <div class="mvv-card-number"><?php echo formatNumber('03'); ?></div>
                    <div class="mvv-card-corner top-left"></div>
                    <div class="mvv-card-corner bottom-right"></div>
                    <div class="mvv-card-icon">
                        <i class="bi bi-gem"></i>
                    </div>
                    <h3 class="mvv-card-title"><?= $lang === 'ar' ? 'قيمنا' : 'Our Values' ?></h3>
                    <div class="mvv-card-underline"></div>
                    <p class="mvv-card-text">
                        <?= $lang === 'ar'
                            ? 'التميز، الخصوصية، الإتقان، والأناقة الهادئة. نؤمن أن الرقي الحقيقي يُشعر به قبل أن يُرى.'
                            : 'Excellence, Privacy, Mastery, and Quiet Elegance. We believe that true sophistication is felt before it is seen.' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story - Redesigned -->
<section class="section about-story-section-professional">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0" data-aos="fade-right">
                <div class="story-visual-wrapper">
                    <div class="story-badge-visual">
                        <span class="story-year"><?php echo formatNumber('25'); ?></span>
                        <span class="story-year-label"><?= $lang === 'ar' ? 'عاماً' : 'Years' ?></span>
                    </div>
                    <div class="story-accent-line"></div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="story-content-professional">
                    <span class="section-badge-professional">
                        <i class="bi bi-book-fill"></i>
                        <?= $lang === 'ar' ? 'قصتنا' : 'Our Story' ?>
                    </span>
                    <h2 class="section-title-professional">
                        <?= $lang === 'ar' ? '25 عاماً من التميز' : '25 Years of Excellence' ?>
                    </h2>
                    <div class="divider-professional"></div>
                    <p class="lead-text-professional">
                        <?= $lang === 'ar'
                            ? 'بدأت نيش سوسيتي من شغف عميق بالتحدي ورغبة لا تتزعزع في خلق حلول حديثة تترجم أعلى معايير الجودة والدقة.'
                            : 'Niche Society was born from a deep passion for challenge and an unwavering desire to create modern solutions that translate the highest standards of quality and accuracy.' ?>
                    </p>
                    <p class="text-professional">
                        <?= $lang === 'ar'
                            ? 'بناء الأنظمة، متابعة التفاصيل، وتحقيق نتائج ملموسة كان دائماً ما يلهمنا ويدفعنا للمضي قدماً. المستحيل لم يكن موجوداً في قاموسنا، بل كان دافعاً لمزيد من الإصرار على تطوير المهارات، مواكبة أحدث التقنيات، وتقديم خدمات تلبي تطلعات العملاء وتجسد طموحنا.'
                            : 'Building systems, following details, and achieving tangible results has always been what inspires and motivates us to keep going. The impossible had no place in our dictionary, but it was a motivation to further insist on developing skills, keeping up with the latest technologies, and providing services that meet customer aspirations and embody our ambition.' ?>
                    </p>
                    <p class="text-professional mb-0">
                        <?= $lang === 'ar'
                            ? 'نيش سوسيتي ليست مجرد مشروع، بل نتيجة لسنوات من الخبرة، التنوع الثقافي، والتحديات التي واجهناها خلال مسيرتنا المهنية. تأسست لتقديم خدمات تعزز الخصوصية، تعزز الإنتاجية، وتُنفذ بأعلى مستويات الحرفية، بهدوء وسلاسة، وتعزز الأناقة التي لا تُخطئها العين ولا يغفلها الحس.'
                            : 'Niche Society is not just a project, but the result of years of experience, cultural diversity, and the challenges we faced during our professional career. It was founded to provide services that enhance privacy, enhance productivity, and are executed with the highest levels of craftsmanship, with calmness and smoothness, and enhance elegance that is unmistakable to the eye and not overlooked by the senses.' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us - Redesigned -->
<section class="section bg-cream about-why-section-professional">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge-professional">
                <i class="bi bi-star-fill"></i>
                <?= $lang === 'ar' ? 'ما يميزنا' : 'What Sets Us Apart' ?>
            </span>
            <h2 class="section-title-professional"><?= $lang === 'ar' ? 'لماذا تختار نيش سوسيتي؟' : 'Why Choose Niche Society?' ?></h2>
            <div class="divider-professional mx-auto"></div>
            <p class="lead-text-professional">
                <?= $lang === 'ar'
                    ? 'ما يميزنا عن الآخرين'
                    : 'What sets us apart' ?>
            </p>
        </div>
        
        <div class="row g-4">
            <?php
            $advantages = [
                [
                    'icon' => 'bi-shield-check-fill',
                    'title_en' => 'ISO 9001:2015 Certified',
                    'title_ar' => 'معتمد ISO 9001:2015',
                    'desc_en' => 'Internationally recognized quality management system ensuring consistent excellence in all our services.',
                    'desc_ar' => 'نظام إدارة الجودة المعترف به دولياً يضمن التميز المستمر في جميع خدماتنا.'
                ],
                [
                    'icon' => 'bi-incognito',
                    'title_en' => 'Complete Discretion',
                    'title_ar' => 'سرية تامة',
                    'desc_en' => 'Uncompromising commitment to privacy and confidentiality. Your information and lifestyle remain completely protected.',
                    'desc_ar' => 'التزام لا يتزعزع بالخصوصية والسرية. معلوماتك ونمط حياتك يبقى محمياً تماماً.'
                ],
                [
                    'icon' => 'bi-clock-history',
                    'title_en' => '25+ Years Experience',
                    'title_ar' => 'أكثر من 25 عاماً من الخبرة',
                    'desc_en' => 'A quarter-century of serving distinguished clients has refined our expertise to perfection.',
                    'desc_ar' => 'ربع قرن من خدمة العملاء المميزين صقل خبرتنا إلى الكمال.'
                ],
                [
                    'icon' => 'bi-gem',
                    'title_en' => 'Quiet Luxury',
                    'title_ar' => 'الرقي الهادئ',
                    'desc_en' => 'We believe sophistication is felt before it is seen. Every detail is crafted with understated elegance.',
                    'desc_ar' => 'نؤمن أن الأناقة تُشعر بها قبل أن تُرى. كل تفصيلة مصنوعة بأناقة هادئة.'
                ],
                [
                    'icon' => 'bi-person-check-fill',
                    'title_en' => 'Dedicated Team',
                    'title_ar' => 'فريق مخصص',
                    'desc_en' => 'Highly trained professionals who understand the nuances of luxury service and protocol.',
                    'desc_ar' => 'محترفون مدربون تدريباً عالياً يفهمون دقائق الخدمة الفاخرة والبروتوكول.'
                ],
                [
                    'icon' => 'bi-stars',
                    'title_en' => 'Tailored Solutions',
                    'title_ar' => 'حلول مخصصة',
                    'desc_en' => 'Every service is re-engineered from scratch to meet your unique requirements and exceed expectations.',
                    'desc_ar' => 'كل خدمة تُعاد هندستها من الصفر لتلبية متطلباتك الفريدة وتتجاوز التوقعات.'
                ]
            ];
            
            foreach ($advantages as $index => $advantage) :
            ?>
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                <div class="advantage-card-professional">
                    <div class="advantage-icon-professional">
                        <i class="bi <?= $advantage['icon'] ?>"></i>
                    </div>
                    <h4 class="advantage-title-professional"><?= $lang === 'ar' ? $advantage['title_ar'] : $advantage['title_en'] ?></h4>
                    <p class="advantage-text-professional"><?= $lang === 'ar' ? $advantage['desc_ar'] : $advantage['desc_en'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Statistics & Achievements - Redesigned -->
<section class="section bg-burgundy text-white about-stats-section-professional">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge-professional badge-light">
                <i class="bi bi-trophy-fill"></i>
                <?= $lang === 'ar' ? 'إنجازاتنا' : 'Our Achievements' ?>
            </span>
            <h2 class="section-title-professional text-white"><?= $lang === 'ar' ? 'إنجازاتنا بالأرقام' : 'Our Achievements in Numbers' ?></h2>
            <div class="divider-professional divider-light mx-auto"></div>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="stat-card-professional">
                    <div class="stat-number-professional"><?php echo formatNumber('25'); ?>+</div>
                    <div class="stat-label-professional"><?= $lang === 'ar' ? 'عاماً من الخبرة' : 'Years of Experience' ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="stat-card-professional">
                    <div class="stat-number-professional"><?php echo formatNumber('500'); ?>+</div>
                    <div class="stat-label-professional"><?= $lang === 'ar' ? 'عميل راضٍ' : 'Satisfied Clients' ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="stat-card-professional">
                    <div class="stat-number-professional"><?php echo formatNumber('1000'); ?>+</div>
                    <div class="stat-label-professional"><?= $lang === 'ar' ? 'مشروع ناجح' : 'Successful Projects' ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="400">
                <div class="stat-card-professional">
                    <div class="stat-number-professional">ISO</div>
                    <div class="stat-label-professional"><?= $lang === 'ar' ? 'معتمد دولياً' : 'Internationally Certified' ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ISO Certification - Redesigned -->
<section class="section about-certification-section-professional">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <div class="certificate-wrapper-professional">
                    <div class="certificate-frame">
                        <img src="<?= url('assets/images/Niche-Society-Arabic-CP2.png') ?>" alt="<?= $lang === 'ar' ? 'شهادة ISO' : 'ISO Certificate' ?>" class="certificate-image-professional">
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="certification-content-professional">
                    <span class="section-badge-professional">
                        <i class="bi bi-award-fill"></i>
                        <?= $lang === 'ar' ? 'التصديق' : 'Certification' ?>
                    </span>
                    <h2 class="section-title-professional">
                        <?= $lang === 'ar' ? 'معتمدون ISO 9001:2015' : 'ISO 9001:2015 Certified' ?>
                    </h2>
                    <div class="divider-professional"></div>
                    <p class="lead-text-professional">
                        <?= $lang === 'ar'
                            ? 'نفتخر بحصولنا على شهادة ISO 9001:2015، التي تؤكد التزامنا بأعلى معايير الجودة في جميع خدماتنا.'
                            : 'We are proud to be ISO 9001:2015 certified, confirming our commitment to the highest quality standards in all our services.' ?>
                    </p>
                    <p class="text-professional">
                        <?= $lang === 'ar'
                            ? 'هذه الشهادة تعكس التزامنا المستمر بالتحسين المستمر، إدارة الجودة، ورضا العملاء. كل عملية ننفذها تخضع لمعايير صارمة لضمان التميز في كل تفصيلة.'
                            : 'This certification reflects our ongoing commitment to continuous improvement, quality management, and customer satisfaction. Every process we execute is subject to strict standards to ensure excellence in every detail.' ?>
                    </p>
                    <div class="certificate-details-professional mt-4">
                        <div class="cert-detail-item-professional">
                            <div class="cert-icon-wrapper">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="cert-detail-content">
                                <strong><?= $lang === 'ar' ? 'رقم الشهادة:' : 'Certificate Number:' ?></strong>
                                <span><?php echo formatNumber('25'); ?>EQQN<?php echo formatNumber('01'); ?></span>
                            </div>
                        </div>
                        <div class="cert-detail-item-professional">
                            <div class="cert-icon-wrapper">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <div class="cert-detail-content">
                                <strong><?= $lang === 'ar' ? 'صالحة حتى:' : 'Valid Until:' ?></strong>
                                <span><?php echo formatNumber('2028'); ?>-<?php echo formatNumber('11'); ?>-<?php echo formatNumber('04'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Approach - Redesigned -->
<section class="section bg-cream about-approach-section-professional">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge-professional">
                <i class="bi bi-gear-fill"></i>
                <?= $lang === 'ar' ? 'كيف نعمل' : 'How We Work' ?>
            </span>
            <h2 class="section-title-professional"><?= $lang === 'ar' ? 'نهجنا في العمل' : 'Our Approach' ?></h2>
            <div class="divider-professional mx-auto"></div>
            <p class="lead-text-professional">
                <?= $lang === 'ar'
                    ? 'كيف نقدم التميز في كل مشروع'
                    : 'How we deliver excellence in every project' ?>
            </p>
        </div>
        
        <div class="row g-4">
            <?php
            $approaches = [
                [
                    'icon' => 'bi-search',
                    'title_en' => 'Understanding First',
                    'title_ar' => 'الفهم أولاً',
                    'desc_en' => 'We begin by deeply understanding your unique needs, lifestyle, and expectations to create truly personalized solutions.',
                    'desc_ar' => 'نبدأ بفهم عميق لاحتياجاتك الفريدة ونمط حياتك وتوقعاتك لإنشاء حلول مخصصة حقاً.'
                ],
                [
                    'icon' => 'bi-gear',
                    'title_en' => 'Re-engineering Excellence',
                    'title_ar' => 'إعادة هندسة التميز',
                    'desc_en' => 'We don\'t offer ready-made solutions. Each experience is re-engineered from scratch to meet your highest standards.',
                    'desc_ar' => 'لا نقدم حلولاً جاهزة. كل تجربة تُعاد هندستها من الصفر لتلبية أعلى معاييرك.'
                ],
                [
                    'icon' => 'bi-eye',
                    'title_en' => 'Attention to Detail',
                    'title_ar' => 'الاهتمام بالتفاصيل',
                    'desc_en' => 'From foundation to finishing touches, we coordinate everything visible and manage everything behind the scenes.',
                    'desc_ar' => 'من الأساس إلى اللمسات الأخيرة، ننسق كل ما هو مرئي وندير كل ما هو خلف الكواليس.'
                ],
                [
                    'icon' => 'bi-heart',
                    'title_en' => 'Building Relationships',
                    'title_ar' => 'بناء العلاقات',
                    'desc_en' => 'We don\'t just provide services; we build lasting relationships based on trust, understanding, and mutual respect.',
                    'desc_ar' => 'لا نقدم خدمات فقط؛ بل نبني علاقات دائمة مبنية على الثقة والفهم والاحترام المتبادل.'
                ]
            ];
            
            foreach ($approaches as $index => $approach) :
            ?>
            <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                <div class="approach-card-professional">
                    <div class="approach-step-number"><?php echo formatNumber(str_pad($index + 1, 2, '0', STR_PAD_LEFT)); ?></div>
                    <div class="approach-icon-professional">
                        <i class="bi <?= $approach['icon'] ?>"></i>
                    </div>
                    <h4 class="approach-title-professional"><?= $lang === 'ar' ? $approach['title_ar'] : $approach['title_en'] ?></h4>
                    <p class="approach-text-professional"><?= $lang === 'ar' ? $approach['desc_ar'] : $approach['desc_en'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action - Enhanced -->
<section class="section about-cta-section-professional">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="cta-wrapper-professional" data-aos="fade-up">
                    <div class="cta-content-professional">
                        <h2 class="cta-title-professional"><?= $lang === 'ar' ? 'هل أنت مستعد لتجربة التميز؟' : 'Ready to Experience Excellence?' ?></h2>
                        <p class="cta-text-professional">
                            <?= $lang === 'ar'
                                ? 'تواصل معنا اليوم واحصل على استشارة مجانية'
                                : 'Contact us today and get a free consultation' ?>
                        </p>
                        <a href="<?= url('contact.php') ?>" class="btn-cta-professional">
                            <span><?= $lang === 'ar' ? 'تواصل معنا' : 'Contact Us' ?></span>
                            <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });
</script>
<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
