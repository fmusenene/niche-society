<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// Handle language switch
handleLanguageSwitch();

// Get current language
$lang = getCurrentLanguage();
$dir = getTextDirection($lang);

// Page settings
$currentPage = 'services';
$pageTitle = $lang === 'ar' ? 'أعمال البناء والإنشاءات - نيش سوسايتي' : 'Construction Services - Niche Society';
$pageDescription = $lang === 'ar' ? 'خدمات بناء وإنشاءات شاملة في البناء المدني، التشطيبات، أنظمة MEP، إدارة المرافق، وأكثر.' : 'Comprehensive construction services including civil construction, finishes, MEP systems, facility management, and more.';

// CSRF token for contact form
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'includes/header.php';
?>

<!-- Construction Page Custom Styles -->
<style>
.construction-page-wrapper {
    background: #F8F7F0 !important;
}

.construction-page-wrapper * {
    color: #2D6A6A !important;
}

.construction-page-wrapper .section,
.construction-page-wrapper section {
    background: #F8F7F0 !important;
    padding: 60px 0;
}

.construction-page-wrapper h1,
.construction-page-wrapper h2,
.construction-page-wrapper h3,
.construction-page-wrapper h4 {
    color: #2D6A6A !important;
    font-weight: 700;
}

.construction-page-wrapper .construction-title {
    font-size: 28px;
    font-weight: 700;
    color: #2D6A6A;
    margin-bottom: 20px;
}

.construction-page-wrapper .construction-intro {
    font-size: 16px;
    line-height: 1.8;
    color: #2D6A6A;
    margin-bottom: 25px;
}

.construction-page-wrapper .construction-subtitle {
    font-size: 18px;
    font-weight: 600;
    color: #2D6A6A;
    margin-top: 25px;
    margin-bottom: 15px;
}

.construction-page-wrapper .construction-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.construction-page-wrapper .construction-list li {
    padding: 8px 0;
    padding-left: 25px;
    position: relative;
    font-size: 15px;
    line-height: 1.7;
    color: #2D6A6A;
}

.construction-page-wrapper .construction-list li::before {
    content: '•';
    position: absolute;
    left: 0;
    color: #2D6A6A;
    font-size: 20px;
    line-height: 1;
}

[dir="rtl"] .construction-page-wrapper .construction-list li {
    padding-left: 0;
    padding-right: 25px;
}

[dir="rtl"] .construction-page-wrapper .construction-list li::before {
    left: auto;
    right: 0;
}

.construction-page-wrapper .construction-image {
    width: 100%;
    height: auto;
    border: 1px solid rgba(45, 106, 106, 0.1);
}

.construction-page-wrapper .btn-primary {
    background-color: #2D6A6A !important;
    border-color: #2D6A6A !important;
    color: #F8F7F0 !important;
}

.construction-page-wrapper .btn-primary:hover {
    background-color: #245555 !important;
    border-color: #245555 !important;
}

.construction-page-wrapper .back-button {
    color: #2D6A6A !important;
}

.construction-page-wrapper .back-button:hover {
    color: #245555 !important;
}
</style>

<div class="construction-page-wrapper">
<!-- Sticky Back Button -->
<a href="<?= url('services.php') ?>#service-construction" class="back-button back-button-sticky">
    <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
    <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
</a>

<!-- Page Header - Matching Company Profile Design -->
<section style="background: #F8F7F0; padding: 40px 0;">
    <div class="container">
        <div class="service-detail-nav">
            <a href="<?= url('services.php') ?>#service-construction" class="back-button">
                <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-right' : 'arrow-left' ?>"></i>
                <span><?php echo $lang === 'ar' ? 'العودة إلى الخدمات' : 'Back to Services'; ?></span>
            </a>
        </div>
        <div class="text-center">
            <h1 style="color: #2D6A6A; font-size: 36px; font-weight: 700; margin-bottom: 10px;"><?php echo $lang === 'ar' ? 'خدماتنا' : 'Our Services'; ?></h1>
        </div>
    </div>
</section>

<!-- Services Introduction Section - Matching Page 6 Design -->
<section style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?= url('assets/images/construction/construction.png') ?>" 
                     alt="<?php echo $lang === 'ar' ? 'خدمات البناء' : 'Construction Services'; ?>" 
                     class="construction-image">
            </div>
            <div class="col-lg-6">
                <h2 style="color: #2D6A6A; font-size: 28px; font-weight: 700; margin-bottom: 20px;"><?php echo $lang === 'ar' ? 'ماذا نقدم؟' : 'What do we offer?'; ?></h2>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'في نيش سوسايتي، نتعامل مع كل مشروع بعمق تقني ورؤية استراتيجية واضحة، ونلتزم بالتنفيذ السلس والدقيق من المفهوم إلى التسليم. تجمع فرقنا بين التصميم المبتكر، المعدات المتطورة، والتنفيذ الاحترافي لتقديم حلول مخصصة تلبي أعلى معايير الأداء والجودة.' 
                        : 'At Niche Society, we approach every project with technical depth and a clear strategic vision, committed to smooth and precise execution from concept to delivery. Our teams combine innovative design, advanced equipment, and professional execution to deliver customized solutions that meet the highest standards of performance and quality.'; ?>
                </p>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'نقدم مجموعة شاملة من الخدمات تشمل أعمال البناء المدني، أعمال الخرسانة، التشطيبات الداخلية والخارجية، إدارة المرافق، إدارة المخاطر، تنسيق المواقع، وأنظمة الميكانيكا والكهرباء والسباكة. كل خدمة مصممة لتلبية احتياجات العميل وتبقى لفترة طويلة.' 
                        : 'We offer a comprehensive range of services including civil construction works, concrete works, interior and exterior finishes, facility management, risk management, site coordination, and Mechanical, Electrical, and Plumbing (MEP) systems. Each service is designed to meet client needs and built to last.'; ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 1. Civil Construction Works - Image Left, Text Right -->
<section id="service-construction" style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?= url('assets/images/construction/Civil construction works.png') ?>" 
                     alt="<?php echo $lang === 'ar' ? 'أعمال البناء المدني' : 'Civil Construction Works'; ?>" 
                     class="construction-image">
            </div>
            <div class="col-lg-6">
                <h2 class="construction-title"><?php echo $lang === 'ar' ? 'أعمال البناء المدني:' : 'Civil Construction Works:'; ?></h2>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'تنفذ مشاريع البناء من الأساس إلى التسليم، وفق منهجية دقيقة تضمن الجودة، السلامة، والالتزام بالجدول الزمني. تدير كل موقع كمنظومة متكاملة، وتنفّذ كل مرحلة بثقة، وضوح، وتحكم كامل.' 
                        : 'Construction projects are executed from foundation to handover, according to a precise methodology that ensures quality, safety, and adherence to the timeline. Each site is managed as an integrated system, and every stage is executed with confidence, clarity, and complete control.'; ?>
                </p>
                <h4 class="construction-subtitle"><?php echo $lang === 'ar' ? 'وتشمل:' : 'And includes:'; ?></h4>
                <ul class="construction-list">
                    <li><?php echo $lang === 'ar' ? 'أعمال الحفر والردم.' : 'Excavation and backfilling works.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'الأساسات والخرسانة المسلحة.' : 'Foundations and reinforced concrete.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'الهياكل الإنشائية والمباني.' : 'Structural frameworks and buildings.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'العزل المائي والحراري.' : 'Waterproofing and thermal insulation.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'أعمال التشطيب الهيكلي.' : 'Structural finishing works.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'التنسيق مع الجهات التنظيمية والمكاتب الهندسية.' : 'Coordination with regulatory bodies and engineering offices.'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 2. Interior Finishes - Image Right, Text Left -->
<section style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <img src="<?= url('assets/images/construction/Interior finishes.png') ?>" 
                     alt="<?php echo $lang === 'ar' ? 'التشطيبات الداخلية' : 'Interior Finishes'; ?>" 
                     class="construction-image">
            </div>
            <div class="col-lg-6 order-lg-1">
                <h2 class="construction-title"><?php echo $lang === 'ar' ? 'التشطيبات الداخلية:' : 'Interior Finishes:'; ?></h2>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'نقدم حلول تشطيب متكاملة تجمع بين الذوق المميز، التنفيذ الدقيق، وتصميم المساحات الوظيفية والأنيقة التي تعكس هوية المشروع، مع الالتزام بالجودة والتسليم في الوقت المحدد.' 
                        : 'We offer integrated finishing solutions that combine refined taste, precise execution, designing functional and elegant spaces that reflect the project identity, with a commitment to quality and timely delivery.'; ?>
                </p>
                <h4 class="construction-subtitle"><?php echo $lang === 'ar' ? 'وتشمل:' : 'And includes:'; ?></h4>
                <ul class="construction-list">
                    <li><?php echo $lang === 'ar' ? 'التصميم الداخلي والتوزيع المعماري.' : 'Interior design and architectural distribution.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'تنفيذ الأرضيات والجدران والأسقف.' : 'Execution of floors, walls, and ceilings.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'أعمال النجارة والجبس والدهانات.' : 'Carpentry, gypsum, and painting works.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'التمديدات الكهربائية والميكانيكية الداخلية.' : 'Internal electrical and mechanical installations.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'تنسيق الإضاءة والأثاث حسب الطلب.' : 'Coordination of lighting and furnishings as per request.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'إدارة المواد والتوريد حسب أعلى المواصفات.' : 'Material management and supply according to the highest specifications.'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 3. MEP Systems - Image Left, Text Right -->
<section style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?= url('assets/images/construction/Mechanical Electrical and Plumbing.png') ?>" 
                     alt="<?php echo $lang === 'ar' ? 'أنظمة الميكانيكا والكهرباء والسباكة' : 'MEP Systems'; ?>" 
                     class="construction-image">
            </div>
            <div class="col-lg-6">
                <h2 class="construction-title"><?php echo $lang === 'ar' ? 'أنظمة الميكانيكا والكهرباء والسباكة (MEP):' : 'Mechanical, Electrical, and Plumbing (MEP) Systems:'; ?></h2>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'نُصمّم أنظمة MEP بهندسة عالية لضمان عمل المباني بكفاءة، أمان، واستدامة، وننفّذ حلول MEP وفق أعلى المعايير لتعمل بسلاسة خلف الكواليس وتدعم راحة المستخدمين وجودة التشغيل.' 
                        : 'We engineer MEP systems to maintain buildings efficiently, safely, and sustainably, implementing MEP solutions according to the highest standards to operate smoothly behind the scenes and support user comfort and operational quality.'; ?>
                </p>
                <h4 class="construction-subtitle"><?php echo $lang === 'ar' ? 'وتشمل:' : 'And includes:'; ?></h4>
                <ul class="construction-list">
                    <li><?php echo $lang === 'ar' ? 'أنظمة التكييف والتهوية (HVAC).' : 'Air conditioning and ventilation systems (HVAC).'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'التمديدات الكهربائية وتوزيع الطاقة.' : 'Electrical installations and power distribution.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'أنظمة الإضاءة والتحكم الذكي.' : 'Lighting and smart control systems.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'شبكات المياه والصرف الصحي.' : 'Water and sanitation networks.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'أنظمة الحماية من الحريق.' : 'Fire protection systems.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'اختبار الأنظمة وتشغيلها وصيانتها.' : 'System testing, operation, and maintenance.'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 4. Facilities Management - Image Right, Text Left -->
<section style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <img src="<?= url('assets/images/construction/Facilities Management.png') ?>" 
                     alt="<?php echo $lang === 'ar' ? 'إدارة المرافق' : 'Facilities Management'; ?>" 
                     class="construction-image">
            </div>
            <div class="col-lg-6 order-lg-1">
                <h2 class="construction-title"><?php echo $lang === 'ar' ? 'إدارة المرافق:' : 'Facilities Management:'; ?></h2>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'تدير المرافق بأسلوب احترافي يضمن الاستمرارية، الكفاءة، والسلامة، وتعالج التفاصيل اليومية دون أن تغيب عنا الصورة الكاملة، ونُصمّم حلولاً تشغيلية تحافظ على الجودة.' 
                        : 'We manage facilities in a professional manner that ensures continuity, efficiency, and safety, addresses daily details without losing sight of the big picture, and designs operational solutions that maintain quality.'; ?>
                </p>
                <h4 class="construction-subtitle"><?php echo $lang === 'ar' ? 'وتشمل:' : 'And includes:'; ?></h4>
                <ul class="construction-list">
                    <li><?php echo $lang === 'ar' ? 'تشغيل وصيانة الأنظمة الحيوية.' : 'Operation and maintenance of vital systems.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'إدارة الطاقة والمياه بكفاءة.' : 'Efficient management of energy and water.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'التنظيف الأمن، والخدمات المساندة.' : 'Safe cleaning and support services.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'مراقبة الأداء وتحسين العمليات.' : 'Performance monitoring and process improvement.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'إدارة العقود والموردين.' : 'Contract and supplier management.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'تقارير دورية وتحليل بيانات التشغيل.' : 'Periodic reports and analysis of operational data.'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 5. Maintenance - Image Left, Text Right -->
<section style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?= url('assets/images/construction/Maintenance.png') ?>" 
                     alt="<?php echo $lang === 'ar' ? 'الصيانة' : 'Maintenance'; ?>" 
                     class="construction-image">
            </div>
            <div class="col-lg-6">
                <h2 class="construction-title"><?php echo $lang === 'ar' ? 'الصيانة:' : 'Maintenance:'; ?></h2>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'تُدار الصيانة كنظام وقائي يهدف إلى الحفاظ على الأداء، إطالة عمر الأصول، ومنع الأعطال قبل حدوثها؛ حيث تُصمّم خطط تشغيلية دقيقة تناسب كل موقع وتُنفّذ بكفاءة وبانتباه للتفاصيل.' 
                        : 'Maintenance is managed as a preventive system, aiming to preserve performance, extend asset life, and prevent failures before they occur; where precise operational plans are designed to suit each site and are executed efficiently and with attention to detail.'; ?>
                </p>
                <h4 class="construction-subtitle"><?php echo $lang === 'ar' ? 'وتشمل:' : 'And includes:'; ?></h4>
                <ul class="construction-list">
                    <li><?php echo $lang === 'ar' ? 'الصيانة التصحيحية (بعد حدوث العطل).' : 'Corrective maintenance (after a breakdown occurs).'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'الصيانة الوقائية (مجدولة لتقليل التوقف).' : 'Preventive maintenance (scheduled to reduce downtime).'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'الصيانة التنبؤية (استباقية بناءً على البيانات).' : 'Predictive maintenance (proactive based on data).'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'الصيانة حسب الحالة (وفق أداء الأصل الفعلي).' : 'Condition-based maintenance (according to actual asset performance).'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 6. Site Coordination / Landscaping - Image Right, Text Left -->
<section style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <img src="<?= url('assets/images/construction/Site Coordination.png') ?>" 
                     alt="<?php echo $lang === 'ar' ? 'تنسيق المواقع' : 'Site Coordination'; ?>" 
                     class="construction-image">
            </div>
            <div class="col-lg-6 order-lg-1">
                <h2 class="construction-title"><?php echo $lang === 'ar' ? 'تنسيق المواقع:' : 'Site Coordination / Landscaping:'; ?></h2>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'نحول المساحات الخارجية إلى بيئات تنبض بالجمال والوظيفة، وتصمم وتنفذ حلول تنسيق المواقع بعناية، تجمع بين الذوق، الاستدامة، والدقة الهندسية، لتكمل هوية المشروع وتعزز تجربة العميل.' 
                        : 'We transform outdoor spaces into environments that pulsate with beauty and functionality, and we design and implement site coordination solutions with care, combining taste, sustainability, and engineering precision, to complete the project identity and enhance the client experience.'; ?>
                </p>
                <h4 class="construction-subtitle"><?php echo $lang === 'ar' ? 'وتشمل:' : 'And includes:'; ?></h4>
                <ul class="construction-list">
                    <li><?php echo $lang === 'ar' ? 'تصميم وتخطيط المساحات الخارجية.' : 'Design and planning of outdoor spaces.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'تنفيذ أعمال الزراعة والتشجير.' : 'Execution of planting and landscaping works.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'تركيب أنظمة الري والتحكم بالمياه.' : 'Installation of irrigation and water control systems.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'أعمال الأرضيات والممرات الخارجية.' : 'Outdoor flooring and pathway works.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'الإضاءة الخارجية وتنسيق الأجواء.' : 'Outdoor lighting and ambiance coordination.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'صيانة موسمية وتحديثات حسب الحاجة.' : 'Seasonal maintenance and updates as needed.'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 7. Risk Management - Image Left, Text Right -->
<section style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?= url('assets/images/construction/Risk Management .png') ?>" 
                     alt="<?php echo $lang === 'ar' ? 'إدارة المخاطر' : 'Risk Management'; ?>" 
                     class="construction-image">
            </div>
            <div class="col-lg-6">
                <h2 class="construction-title"><?php echo $lang === 'ar' ? 'إدارة المخاطر:' : 'Risk Management:'; ?></h2>
                <p class="construction-intro">
                    <?php echo $lang === 'ar' 
                        ? 'تحصن المشاريع من المفاجآت، وتخطط لما قد يحدث قبل أن يحدث، كما تطبق منهجيات دقيقة لرصد المخاطر، تقييمها، والتعامل معها بذكاء، لضمان استمرارية التشغيل وسلامة الأصول.' 
                        : 'It protects projects from surprises, plans for what might happen before it happens, and applies precise methodologies for monitoring, evaluating, and intelligently dealing with risks, to ensure operational continuity and asset safety.'; ?>
                </p>
                <h4 class="construction-subtitle"><?php echo $lang === 'ar' ? 'وتشمل:' : 'And includes:'; ?></h4>
                <ul class="construction-list">
                    <li><?php echo $lang === 'ar' ? 'تحليل المخاطر ووضع خطط التخفيف.' : 'Risk analysis and mitigation planning.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'تطبيق بروتوكولات الصحة والسلامة والبيئة.' : 'Application of health, safety, and environmental protocols.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'أنظمة الاستجابة للطوارئ.' : 'Emergency response systems.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'التدقيق الميداني وتحديد نقاط الضعف.' : 'Field auditing and identification of weaknesses.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'التنسيق التأميني وتوثيق الإجراءات.' : 'Insurance coordination and documentation of procedures.'; ?></li>
                    <li><?php echo $lang === 'ar' ? 'الالتزام بالأنظمة والتقارير التنظيمية.' : 'Compliance with regulations and organizational reports.'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section style="background: #F8F7F0; padding: 60px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 style="color: #2D6A6A; margin-bottom: 20px; font-size: 28px;"><?php echo $lang === 'ar' ? 'هل أنت مستعد لبدء مشروعك الإنشائي؟' : 'Ready to Start Your Construction Project?'; ?></h2>
                <p style="color: #2D6A6A; margin-bottom: 30px; font-size: 16px;">
                    <?php echo $lang === 'ar' 
                        ? 'تواصل معنا اليوم واحصل على استشارة مجانية لمشروعك' 
                        : 'Contact us today and get a free consultation for your project'; ?>
                </p>
                <a href="contact.php" class="btn btn-primary btn-lg"><?php echo $lang === 'ar' ? 'احصل على استشارة مجانية' : 'Get Free Consultation'; ?></a>
            </div>
        </div>
    </div>
</section>
</div>

<script>
// Sticky Back Button
(function() {
    const stickyButton = document.querySelector('.back-button-sticky');
    const header = document.querySelector('section');
    
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
        handleScroll();
    }
})();
</script>

<?php require_once 'includes/footer.php'; ?>
