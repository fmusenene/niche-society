<?php
/**
 * Services Page - Niche Society
 * 
 * Complete overview of all services offered with detailed descriptions
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/helpers.php';

// Handle language switch
handleLanguageSwitch();

$lang = getCurrentLanguage();
$t = getTranslations($lang);
$dir = getTextDirection($lang);

$pageTitle = $lang === 'ar' ? 'خدماتنا - نيتش سوسيتي' : 'Our Services - Niche Society';
$pageDescription = $lang === 'ar' 
    ? 'خدمات متكاملة في إدارة المنازل، تنظيم الفعاليات، البروتوكول والإتيكيت، خدمات VIP والاستشارات' 
    : 'Comprehensive services in household management, event management, protocol & etiquette, VIP services and consultations';
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
    <!-- Hero background updated to new Our Services image -->
    <section class="page-hero" style="background-image: url('<?= url('assets/images/our-services-hero.jpg') ?>');">
        <div class="hero-burgundy-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-text-animated">
                    <h1 class="hero-title" data-aos="fade-up">
                        <?= $lang === 'ar' ? 'خدماتنا' : 'Our Services' ?>
                    </h1>
                    <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
                        <?= $lang === 'ar' 
                            ? 'حلول إدارية متكاملة ومعتمدة بشهادة ISO 9001:2015 لتلبية جميع احتياجاتكم'
                            : 'Comprehensive management solutions certified with ISO 9001:2015 to meet all your needs'
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Introduction -->
    <section class="services-intro-section">
        <div class="container">
            <div class="services-intro-wrapper">
                <!-- Header Section -->
                <div class="text-center mb-5" data-aos="fade-up">
                    <span class="services-intro-badge">
                        <i class="bi bi-award-fill"></i>
                        <?= $lang === 'ar' ? 'خدمات معتمدة' : 'Certified Services' ?>
                    </span>
                    <h2 class="services-intro-title">
                        <?= $lang === 'ar' ? 'خدمات متخصصة للعملاء المميزين' : 'Specialized Services for Distinguished Clients' ?>
                    </h2>
                    <div class="services-intro-divider">
                        <span class="divider-line"></span>
                        <span class="divider-icon"><i class="bi bi-diamond-fill"></i></span>
                        <span class="divider-line"></span>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="row justify-content-center">
                    <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
                        <div class="services-intro-content">
                            <p class="services-intro-lead">
                                <?= $lang === 'ar'
                                    ? 'نقدم مجموعة شاملة من خدمات الإدارة الفاخرة المصممة خصيصاً لتلبية احتياجات الشخصيات البارزة. كل خدمة نقدمها تجمع بين الخبرة العميقة، التقنيات المبتكرة، والاهتمام الدقيق بالتفاصيل لضمان تجربة استثنائية لعملائنا.'
                                    : 'We offer a comprehensive range of luxury management services specifically designed to meet the needs of distinguished personalities. Each service we provide combines deep expertise, innovative technologies, and meticulous attention to detail to ensure an exceptional experience for our clients.'
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Stats Section -->
                <div class="services-stats-enhanced" data-aos="fade-up" data-aos-delay="200">
                    <div class="row justify-content-center g-2 g-md-4">
                        <div class="col-lg-3 col-md-6 col-3">
                            <div class="stat-card-enhanced">
                                <div class="stat-icon-wrapper">
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                </div>
                                <div class="stat-number-enhanced"><?php echo formatNumber('6'); ?></div>
                                <div class="stat-label-enhanced"><?= $lang === 'ar' ? 'خدمات رئيسية' : 'Core Services' ?></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-3">
                            <div class="stat-card-enhanced">
                                <div class="stat-icon-wrapper">
                                    <i class="bi bi-calendar-check-fill"></i>
                                </div>
                                <div class="stat-number-enhanced"><?php echo formatNumber('25'); ?>+</div>
                                <div class="stat-label-enhanced"><?= $lang === 'ar' ? 'عاماً من الخبرة' : 'Years Experience' ?></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-3">
                            <div class="stat-card-enhanced">
                                <div class="stat-icon-wrapper">
                                    <i class="bi bi-shield-check-fill"></i>
                                </div>
                                <div class="stat-number-enhanced">ISO</div>
                                <div class="stat-label-enhanced"><?= $lang === 'ar' ? 'معتمد دولياً' : 'Internationally Certified' ?></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-3">
                            <div class="stat-card-enhanced">
                                <div class="stat-icon-wrapper">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="stat-number-enhanced"><?php echo formatNumber('100'); ?>%</div>
                                <div class="stat-label-enhanced"><?= $lang === 'ar' ? 'رضا العملاء' : 'Client Satisfaction' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service 1: Smart Household Management -->
    <section id="service-1" class="service-detail-section bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2" data-aos="fade-left">
                    <div class="service-image">
                        <img src="<?= url('assets/images/service.png') ?>" alt="<?= $lang === 'ar' ? 'إدارة المنزل الذكية' : 'Smart Household Management' ?>" class="img-fluid">
                        <div class="service-badge"><?php echo formatNumber('01'); ?></div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1" data-aos="fade-right">
                    <div class="service-header-mobile">
                        <div class="service-icon">
                            <i class="bi bi-house-door"></i>
                        </div>
                        <h2 class="service-title">
                            <?= $lang === 'ar' ? 'إدارة المنزل الذكية' : 'Smart Household Management' ?>
                        </h2>
                    </div>
                    <div class="divider"></div>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'نحول كل عقار إلى نظام متكامل يدار عبر أنظمة ذكية ويتم تنسيقه من خلال تواصل فعال.'
                            : 'We transform every property into an integrated system managed through smart technologies and coordinated through effective communication.'
                        ?>
                    </p>
                    <ul class="service-features-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'إنشاء نظام داخلي شامل للعمليات' : 'Comprehensive internal system establishment' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'إدارة وتقييم العمليات اليومية' : 'Daily operations management and evaluation' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تقييم شامل للعقار والموظفين' : 'Comprehensive property and staff assessment' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'لوحات تحكم رقمية وسجلات خدمية' : 'Digital control panels and service records' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تنسيق الاستجابة الفورية للطوارئ' : 'Rapid emergency response coordination' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'مدير علاقات مخصص على مدار الساعة' : 'Dedicated relationship manager 24/7' ?></span>
                        </li>
                    </ul>
                    <a href="<?= url('service-household-management.php') ?>#service-1" class="btn btn-primary mt-3">
                        <?= $lang === 'ar' ? 'تفاصيل الخدمة' : 'Service Details' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service 2: Property Management -->
    <section id="service-2" class="service-detail-section bg-cream">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="service-image">
                        <img src="<?= url('assets/images/service-3.jpg') ?>" alt="<?= $lang === 'ar' ? 'إدارة الممتلكات' : 'Property Management' ?>" class="img-fluid">
                        <div class="service-badge"><?php echo formatNumber('02'); ?></div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="service-header-mobile">
                        <div class="service-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h2 class="service-title">
                            <?= $lang === 'ar' ? 'إدارة الممتلكات' : 'Property Management' ?>
                        </h2>
                    </div>
                    <div class="divider"></div>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'إدارة شاملة لممتلكاتكم العقارية مع دمج الأنظمة الذكية والتقنيات الحديثة.'
                            : 'Comprehensive management of your properties with integration of smart systems and modern technologies.'
                        ?>
                    </p>
                    <ul class="service-features-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تكامل الأنظمة الذكية للمباني' : 'Smart building systems integration' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'جدولة وإدارة الصيانة الوقائية' : 'Preventive maintenance scheduling' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'إدارة الموظفين والمقاولين' : 'Staff and contractor management' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تقارير وتحليلات أداء العقار' : 'Property performance reports and analytics' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'إدارة الأمن والسلامة' : 'Security and safety management' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تحسين كفاءة الطاقة' : 'Energy efficiency optimization' ?></span>
                        </li>
                    </ul>
                    <a href="<?= url('service-property-management.php') ?>#service-2" class="btn btn-primary mt-3">
                        <?= $lang === 'ar' ? 'تفاصيل الخدمة' : 'Service Details' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service 3: Event Management -->
    <section id="service-3" class="service-detail-section bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2" data-aos="fade-left">
                    <div class="service-image">
                        <img src="<?= url('assets/images/service-5.jpg') ?>" alt="<?= $lang === 'ar' ? 'تنظيم الفعاليات' : 'Event Management' ?>" class="img-fluid">
                        <div class="service-badge"><?php echo formatNumber('03'); ?></div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1" data-aos="fade-right">
                    <div class="service-header-mobile">
                        <div class="service-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h2 class="service-title">
                            <?= $lang === 'ar' ? 'تنظيم الفعاليات' : 'Event Management' ?>
                        </h2>
                    </div>
                    <div class="divider"></div>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'تخطيط وتنفيذ فعاليات استثنائية من الألف إلى الياء بأعلى معايير الاحترافية.'
                            : 'Planning and executing exceptional events from A to Z with the highest standards of professionalism.'
                        ?>
                    </p>
                    <ul class="service-features-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'التخطيط الاستراتيجي والتصميم الإبداعي' : 'Strategic planning and creative design' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'إدارة الموردين والتنسيق الكامل' : 'Vendor management and full coordination' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'التنفيذ المثالي يوم الفعالية' : 'Flawless execution on event day' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'إدارة الضيوف والبروتوكول' : 'Guest management and protocol' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تقنيات الصوت والصورة المتقدمة' : 'Advanced audio-visual technology' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تقييم ما بعد الفعالية' : 'Post-event analysis and reporting' ?></span>
                        </li>
                    </ul>
                    <a href="<?= url('service-event-management.php') ?>#service-3" class="btn btn-primary mt-3">
                        <?= $lang === 'ar' ? 'تفاصيل الخدمة' : 'Service Details' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service 4: Protocol & Etiquette -->
    <section id="service-4" class="service-detail-section bg-cream">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="service-image">
                        <img src="<?= url('assets/images/service-6.jpg') ?>" alt="<?= $lang === 'ar' ? 'البروتوكول والإتيكيت' : 'Protocol & Etiquette' ?>" class="img-fluid">
                        <div class="service-badge"><?php echo formatNumber('04'); ?></div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="service-header-mobile">
                        <div class="service-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <h2 class="service-title">
                            <?= $lang === 'ar' ? 'البروتوكول والإتيكيت' : 'Protocol & Etiquette Training' ?>
                        </h2>
                    </div>
                    <div class="divider"></div>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'برامج تدريبية مخصصة لتعزيز التواصل والسلوك في المواقف الرسمية واليومية.'
                            : 'Tailored training programs to enhance communication and behavior in formal and everyday settings.'
                        ?>
                    </p>
                    <ul class="service-features-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'ورش البروتوكول الملكي والرسمي' : 'Royal and official protocol workshops' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'برامج إتيكيت الضيافة المتقدمة' : 'Advanced hospitality etiquette programs' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'استراتيجيات التواصل الشخصية' : 'Personalized communication strategies' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تدريب على الحضور والتعبير' : 'Presence and articulation training' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'برامج الإتيكيت للمدارس' : 'Foundational etiquette for schools' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'التدريب الشخصي والاستشارات' : 'Private coaching and consultations' ?></span>
                        </li>
                    </ul>
                    <a href="<?= url('service-protocol-etiquette.php') ?>#service-4" class="btn btn-primary mt-3">
                        <?= $lang === 'ar' ? 'تفاصيل الخدمة' : 'Service Details' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service 5: VIP Concierge -->
    <section id="service-5" class="service-detail-section bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2" data-aos="fade-left">
                    <div class="service-image">
                        <img src="<?= url('assets/images/service-2-914x1024.png') ?>" alt="<?= $lang === 'ar' ? 'خدمة الكونسيرج VIP' : 'VIP Logistics & Consulting Service' ?>" class="img-fluid">
                        <div class="service-badge"><?php echo formatNumber('05'); ?></div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1" data-aos="fade-right">
                    <div class="service-header-mobile">
                        <div class="service-icon">
                            <i class="bi bi-star"></i>
                        </div>
                        <h2 class="service-title">
                            <?= $lang === 'ar' ? 'خدمة الكونسيرج VIP' : 'VIP Logistics & Consulting Service' ?>
                        </h2>
                    </div>
                    <div class="divider"></div>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'مساعدة شخصية حصرية على مدار الساعة لتلبية جميع احتياجاتكم ورغباتكم.'
                            : 'Exclusive 24/7 personal assistance to meet all your needs and desires.'
                        ?>
                    </p>
                    <ul class="service-features-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'مساعد شخصي متاح على مدار الساعة' : '24/7 available personal assistant' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تنسيق السفر والإقامة الفاخرة' : 'Luxury travel and accommodation coordination' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'حجوزات المطاعم والفعاليات الحصرية' : 'Exclusive restaurant and event reservations' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'إدارة نمط الحياة الشخصي' : 'Personal lifestyle management' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'خدمات التسوق والهدايا الفاخرة' : 'Luxury shopping and gift services' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'طلبات خاصة ومتطلبات فريدة' : 'Special requests and unique requirements' ?></span>
                        </li>
                    </ul>
                    <a href="<?= url('service-vip-concierge.php') ?>#service-5" class="btn btn-primary mt-3">
                        <?= $lang === 'ar' ? 'تفاصيل الخدمة' : 'Service Details' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service 6: Staff Recruitment & Training -->
    <section id="service-6" class="service-detail-section bg-cream">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="service-image">
                        <img src="<?= url('assets/images/service-4.jpg') ?>" alt="<?= $lang === 'ar' ? 'توظيف وتدريب الموظفين' : 'Staff Recruitment & Training' ?>" class="img-fluid">
                        <div class="service-badge"><?php echo formatNumber('06'); ?></div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="service-header-mobile">
                        <div class="service-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h2 class="service-title">
                            <?= $lang === 'ar' ? 'توظيف وتدريب الموظفين' : 'Staff Recruitment & Training' ?>
                        </h2>
                    </div>
                    <div class="divider"></div>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'اختيار وتطوير أفضل الكفاءات لخدمتكم بأعلى معايير الاحترافية.'
                            : 'Selecting and developing the best talents to serve you with the highest standards of professionalism.'
                        ?>
                    </p>
                    <ul class="service-features-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'عملية فحص دقيقة ومتعددة المراحل' : 'Rigorous multi-stage vetting process' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تقييم المهارات والكفاءات' : 'Skills and competency assessment' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'برامج تدريب مخصصة ومستمرة' : 'Customized ongoing training programs' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تدريب على البروتوكول والإتيكيت' : 'Protocol and etiquette training' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'تقييم الأداء المستمر' : 'Continuous performance evaluation' ?></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $lang === 'ar' ? 'برامج التطوير المهني' : 'Professional development programs' ?></span>
                        </li>
                    </ul>
                    <a href="<?= url('service-staff-recruitment.php') ?>#service-6" class="btn btn-primary mt-3">
                        <?= $lang === 'ar' ? 'تفاصيل الخدمة' : 'Service Details' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Process -->
    <section class="section process-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title"><?= $lang === 'ar' ? 'عملية العمل' : 'Our Process' ?></h2>
                <div class="divider mx-auto"></div>
                <p class="lead-text">
                    <?= $lang === 'ar'
                        ? 'منهجية عمل منظمة تضمن تحقيق أفضل النتائج'
                        : 'A structured methodology ensuring optimal results'
                    ?>
                </p>
            </div>
            <div class="row">
                <div class="col-md-3 col-6 mb-3" data-aos="zoom-in" data-aos-delay="100">
                    <div class="process-step">
                        <div class="step-number"><?php echo formatNumber('1'); ?></div>
                        <h4><?= $lang === 'ar' ? 'الاستشارة الأولية' : 'Initial Consultation' ?></h4>
                        <p><?= $lang === 'ar' ? 'فهم احتياجاتكم وأهدافكم بدقة' : 'Understanding your needs and goals precisely' ?></p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3" data-aos="zoom-in" data-aos-delay="200">
                    <div class="process-step">
                        <div class="step-number"><?php echo formatNumber('2'); ?></div>
                        <h4><?= $lang === 'ar' ? 'التخطيط المخصص' : 'Custom Planning' ?></h4>
                        <p><?= $lang === 'ar' ? 'تصميم حل مخصص يناسب متطلباتكم' : 'Designing a custom solution for your requirements' ?></p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3" data-aos="zoom-in" data-aos-delay="300">
                    <div class="process-step">
                        <div class="step-number"><?php echo formatNumber('3'); ?></div>
                        <h4><?= $lang === 'ar' ? 'التنفيذ المتقن' : 'Precise Execution' ?></h4>
                        <p><?= $lang === 'ar' ? 'تنفيذ الخطة بأعلى مستويات الاحترافية' : 'Implementing the plan with highest professionalism' ?></p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3" data-aos="zoom-in" data-aos-delay="400">
                    <div class="process-step">
                        <div class="step-number"><?php echo formatNumber('4'); ?></div>
                        <h4><?= $lang === 'ar' ? 'المتابعة المستمرة' : 'Ongoing Follow-up' ?></h4>
                        <p><?= $lang === 'ar' ? 'ضمان الجودة والتحسين المستمر' : 'Ensuring quality and continuous improvement' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="section bg-burgundy text-white">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title text-white"><?= $lang === 'ar' ? 'لماذا تختار خدماتنا؟' : 'Why Choose Our Services?' ?></h2>
                <div class="divider divider-light mx-auto"></div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-3 mb-2" data-aos="fade-up" data-aos-delay="100">
                    <div class="why-choose-card">
                        <div class="icon-circle">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4><?= $lang === 'ar' ? 'معتمد ISO' : 'ISO Certified' ?></h4>
                        <p><?= $lang === 'ar' ? 'خدمات معتمدة بشهادة ISO 9001:2015' : 'Services certified with ISO 9001:2015' ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-3 mb-2" data-aos="fade-up" data-aos-delay="200">
                    <div class="why-choose-card">
                        <div class="icon-circle">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4><?= $lang === 'ar' ? 'متاح 24/7' : 'Available 24/7' ?></h4>
                        <p><?= $lang === 'ar' ? 'دعم ومساعدة على مدار الساعة' : 'Around-the-clock support and assistance' ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-3 mb-2" data-aos="fade-up" data-aos-delay="300">
                    <div class="why-choose-card">
                        <div class="icon-circle">
                            <i class="bi bi-incognito"></i>
                        </div>
                        <h4><?= $lang === 'ar' ? 'سرية تامة' : 'Complete Discretion' ?></h4>
                        <p><?= $lang === 'ar' ? 'أعلى معايير الخصوصية والسرية' : 'Highest standards of privacy and confidentiality' ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-3 mb-2" data-aos="fade-up" data-aos-delay="400">
                    <div class="why-choose-card">
                        <div class="icon-circle">
                            <i class="bi bi-gem"></i>
                        </div>
                        <h4><?= $lang === 'ar' ? 'جودة فائقة' : 'Superior Quality' ?></h4>
                        <p><?= $lang === 'ar' ? 'تميز في كل جانب من جوانب الخدمة' : 'Excellence in every aspect of service' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title"><?= $lang === 'ar' ? 'الأسئلة الشائعة' : 'Frequently Asked Questions' ?></h2>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-accordion" data-aos="fade-up">
                        <div class="faq-item">
                            <h4 class="faq-question">
                                <?= $lang === 'ar' ? 'كيف يمكنني البدء بالاستفادة من خدماتكم؟' : 'How can I start using your services?' ?>
                            </h4>
                            <div class="faq-answer">
                                <p>
                                    <?= $lang === 'ar'
                                        ? 'ببساطة تواصل معنا عبر نموذج الاتصال أو الهاتف، وسيقوم فريقنا بترتيب استشارة مجانية لفهم احتياجاتك وتقديم الحل المناسب.'
                                        : 'Simply contact us through our contact form or phone, and our team will arrange a free consultation to understand your needs and provide the appropriate solution.'
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="faq-item">
                            <h4 class="faq-question">
                                <?= $lang === 'ar' ? 'هل يمكن تخصيص الخدمات حسب احتياجاتي؟' : 'Can services be customized to my needs?' ?>
                            </h4>
                            <div class="faq-answer">
                                <p>
                                    <?= $lang === 'ar'
                                        ? 'بالتأكيد، جميع خدماتنا قابلة للتخصيص الكامل. نحن نصمم كل خدمة وفقاً لمتطلباتك وتفضيلاتك الخاصة.'
                                        : 'Absolutely, all our services are fully customizable. We design each service according to your specific requirements and preferences.'
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="faq-item">
                            <h4 class="faq-question">
                                <?= $lang === 'ar' ? 'ما مدى سرية المعلومات التي أشاركها معكم؟' : 'How confidential is the information I share with you?' ?>
                            </h4>
                            <div class="faq-answer">
                                <p>
                                    <?= $lang === 'ar'
                                        ? 'السرية هي أولويتنا القصوى. لدينا بروتوكولات صارمة لحماية جميع المعلومات، ونوقع اتفاقيات سرية شاملة مع جميع عملائنا.'
                                        : 'Confidentiality is our top priority. We have strict protocols to protect all information and sign comprehensive confidentiality agreements with all our clients.'
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="faq-item">
                            <h4 class="faq-question">
                                <?= $lang === 'ar' ? 'هل تقدمون خدماتكم خارج المملكة العربية السعودية؟' : 'Do you provide services outside Saudi Arabia?' ?>
                            </h4>
                            <div class="faq-answer">
                                <p>
                                    <?= $lang === 'ar'
                                        ? 'نعم، نقدم خدماتنا للعملاء في المملكة العربية السعودية ودول الخليج، مع إمكانية التوسع حسب احتياجات العميل.'
                                        : 'Yes, we provide our services to clients in Saudi Arabia and Gulf countries, with the possibility of expansion based on client needs.'
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section cta-section bg-cream">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0" data-aos="fade-right">
                    <h2><?= $lang === 'ar' ? 'هل أنتم مستعدون للارتقاء بخدماتكم؟' : 'Ready to Elevate Your Services?' ?></h2>
                    <p class="lead-text">
                        <?= $lang === 'ar'
                            ? 'تواصلوا معنا اليوم واحصلوا على استشارة مجانية'
                            : 'Contact us today and get a free consultation'
                        ?>
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                    <a href="<?= url('contact.php') ?>" class="btn btn-primary btn-lg">
                        <?= $lang === 'ar' ? 'احصل على استشارة' : 'Get Consultation' ?>
                        <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?>"></i>
                    </a>
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

        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', function() {
                const item = this.closest('.faq-item');
                const wasActive = item.classList.contains('active');
                
                // Close all FAQ items
                document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
                
                // Open clicked item if it wasn't active
                if (!wasActive) {
                    item.classList.add('active');
                }
            });
        });

        // Smooth scroll to service section when coming from detail page
        if (window.location.hash) {
            const hash = window.location.hash;
            const targetElement = document.querySelector(hash);
            if (targetElement) {
                setTimeout(() => {
                    const offset = 100; // Offset for fixed navbar
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Highlight the section briefly
                    targetElement.style.transition = 'box-shadow 0.3s ease';
                    targetElement.style.boxShadow = '0 0 30px rgba(96, 34, 52, 0.3)';
                    setTimeout(() => {
                        targetElement.style.boxShadow = '';
                    }, 2000);
                }, 100);
            }
        }
    </script>
    <script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
