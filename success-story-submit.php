<?php
/**
 * Share Your Success Story - Niche Society
 *
 * Clients submit their success story here. Submissions are saved with status = 'active'
 * and appear on the website automatically.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

handleLanguageSwitch();

$lang = getCurrentLanguage();
$dir = getTextDirection($lang);

if (!isset($_SESSION['csrf_success_story'])) {
    $_SESSION['csrf_success_story'] = bin2hex(random_bytes(32));
}

// Service categories: from DB or default list
$servicesStmt = $pdo->prepare("SELECT DISTINCT service_category FROM success_stories WHERE status = 'active' AND service_category != '' AND service_category IS NOT NULL ORDER BY service_category");
$servicesStmt->execute();
$serviceCategories = $servicesStmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($serviceCategories)) {
    $serviceCategories = ['Estate Management', 'Household Management', 'Event Management', 'Protocol and Etiquette', 'Property Management', 'VIP Concierge'];
}

$formData = $_SESSION['success_story_form_data'] ?? [];
$formErrors = $_SESSION['success_story_errors'] ?? [];
// Map handler error keys to input names for is-invalid
$hasError = function ($key) use ($formErrors) {
    return in_array($key, $formErrors, true);
};
$msg = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $msg = $lang === 'ar' ? 'انتهت صلاحية الجلسة. يرجى المحاولة مرة أخرى.' : 'Session expired. Please try again.';
    elseif ($_GET['error'] === 'validation') $msg = $lang === 'ar' ? 'يرجى تعبئة جميع الحقول المطلوبة بشكل صحيح.' : 'Please fill all required fields correctly.';
    else $msg = $lang === 'ar' ? 'حدث خطأ. يرجى المحاولة لاحقاً.' : 'Something went wrong. Please try again.';
}

$pageTitle = $lang === 'ar' ? 'شارك قصتك - نيش سوسيتي' : 'Share Your Story - Niche Society';
$pageDescription = $lang === 'ar'
    ? 'شاركنا قصة نجاحك مع نيش سوسيتي'
    : 'Share your success story with Niche Society';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
    <?= getMetaTags($pageTitle, $pageDescription, getCurrentUrl()) ?>
    <link rel="icon" type="image/png" href="<?= url('assets/images/favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= url('assets/images/favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= url('assets/images/favicon.png') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <?php if ($lang === 'ar'): ?>
    <link rel="stylesheet" href="<?= url('assets/css/rtl.css') ?>">
    <?php endif; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <section class="page-hero" style="background-image: url('<?= url('assets/images/sunlit-library-escape-701x1024.jpg') ?>');">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title" data-aos="fade-up">
                    <?= $lang === 'ar' ? 'شارك قصتك' : 'Share Your Story' ?>
                </h1>
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: #000F2B !important; opacity: 1 !important;">
                    <?= $lang === 'ar' ? 'شاركنا قصة نجاحك مع خدمات نيش سوسيتي' : 'Share your success story with Niche Society services' ?>
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if ($msg): ?>
                    <div class="alert alert-danger mb-4"><?= htmlspecialchars($msg) ?></div>
                    <?php endif; ?>

                    <p class="mb-4">
                        <?= $lang === 'ar'
                            ? 'املأ النموذج أدناه. بعد المراجعة، قد ننشر قصتك على صفحة قصص النجاح.'
                            : 'Fill in the form below. After review, we may publish your story on our Success Stories page.'
                        ?>
                    </p>

                    <form action="<?= url('success-story-handler.php') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_success_story']) ?>">

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="client_name" class="form-label"><?= $lang === 'ar' ? 'الاسم أو اسم العميل' : 'Your name or client name' ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= $hasError('name') ? 'is-invalid' : '' ?>" id="client_name" name="client_name" required maxlength="200"
                                    value="<?= htmlspecialchars($formData['client_name'] ?? '') ?>">
                                <?php if ($hasError('name')): ?>
                                <div class="invalid-feedback"><?= $lang === 'ar' ? 'الاسم مطلوب (حرفان على الأقل)' : 'Required (at least 2 characters)' ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label"><?= $lang === 'ar' ? 'البريد الإلكتروني' : 'Email' ?> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?= $hasError('email') ? 'is-invalid' : '' ?>" id="email" name="email" required maxlength="100"
                                    value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
                                <?php if ($hasError('email')): ?>
                                <div class="invalid-feedback"><?= $lang === 'ar' ? 'بريد إلكتروني صالح مطلوب' : 'Please enter a valid email address' ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="client_type" class="form-label"><?= $lang === 'ar' ? 'نوع العميل' : 'Client type' ?> <span class="text-danger">*</span></label>
                                <select class="form-control <?= $hasError('client_type') ? 'is-invalid' : '' ?>" id="client_type" name="client_type" required>
                                    <option value=""><?= $lang === 'ar' ? 'اختر' : 'Select' ?></option>
                                    <option value="individual" <?= ($formData['client_type'] ?? '') === 'individual' ? 'selected' : '' ?>><?= $lang === 'ar' ? 'فرد' : 'Individual' ?></option>
                                    <option value="corporate" <?= ($formData['client_type'] ?? '') === 'corporate' ? 'selected' : '' ?>><?= $lang === 'ar' ? 'شركة' : 'Corporate' ?></option>
                                    <option value="government" <?= ($formData['client_type'] ?? '') === 'government' ? 'selected' : '' ?>><?= $lang === 'ar' ? 'حكومي' : 'Government' ?></option>
                                    <option value="royal" <?= ($formData['client_type'] ?? '') === 'royal' ? 'selected' : '' ?>><?= $lang === 'ar' ? 'عائلة ملكية' : 'Royal' ?></option>
                                </select>
                                <?php if ($hasError('client_type')): ?>
                                <div class="invalid-feedback"><?= $lang === 'ar' ? 'يرجى اختيار نوع العميل' : 'Please select a client type' ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="service_category" class="form-label"><?= $lang === 'ar' ? 'نوع الخدمة' : 'Service category' ?></label>
                                <select class="form-control" id="service_category" name="service_category">
                                    <option value=""><?= $lang === 'ar' ? 'اختر' : 'Select' ?></option>
                                    <?php foreach ($serviceCategories as $s): ?>
                                    <option value="<?= htmlspecialchars($s) ?>" <?= ($formData['service_category'] ?? '') === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="title" class="form-label"><?= $lang === 'ar' ? 'عنوان القصة' : 'Story title' ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= $hasError('title') ? 'is-invalid' : '' ?>" id="title" name="title" required maxlength="255"
                                value="<?= htmlspecialchars($formData['title'] ?? '') ?>"
                                placeholder="<?= $lang === 'ar' ? 'مثال: إدارة عقار فاخر في الرياض' : 'e.g. Luxury estate management in Riyadh' ?>">
                            <small class="text-muted"><?= $lang === 'ar' ? '5 أحرف على الأقل' : 'At least 5 characters' ?></small>
                            <?php if ($hasError('title')): ?>
                            <div class="invalid-feedback"><?= $lang === 'ar' ? 'العنوان مطلوب (5 أحرف على الأقل)' : 'Required (at least 5 characters)' ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label"><?= $lang === 'ar' ? 'ملخص قصير (جملة أو اثنتان)' : 'Short summary (1–2 sentences)' ?> <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= $hasError('description') ? 'is-invalid' : '' ?>" id="description" name="description" required rows="2" maxlength="500"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                            <small class="text-muted"><?= $lang === 'ar' ? '20 حرف على الأقل' : 'At least 20 characters' ?></small>
                            <?php if ($hasError('description')): ?>
                            <div class="invalid-feedback"><?= $lang === 'ar' ? 'الملخص مطلوب (20 حرف على الأقل)' : 'Required (at least 20 characters)' ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label"><?= $lang === 'ar' ? 'القصة الكاملة' : 'Full story' ?> <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= $hasError('content') ? 'is-invalid' : '' ?>" id="content" name="content" required rows="8" minlength="50"><?= htmlspecialchars($formData['content'] ?? '') ?></textarea>
                            <small class="text-muted"><?= $lang === 'ar' ? '50 حرف على الأقل' : 'At least 50 characters' ?></small>
                            <?php if ($hasError('content')): ?>
                            <div class="invalid-feedback"><?= $lang === 'ar' ? 'القصة مطلوبة (50 حرف على الأقل)' : 'Required (at least 50 characters)' ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <?= $lang === 'ar' ? 'إرسال القصة' : 'Submit story' ?>
                            <i class="bi bi-<?= $dir === 'rtl' ? 'arrow-left' : 'arrow-right' ?> ms-1"></i>
                        </button>
                        <a href="<?= url('success-stories.php') ?>" class="btn btn-outline-secondary btn-lg ms-2"><?= $lang === 'ar' ? 'إلغاء' : 'Cancel' ?></a>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
