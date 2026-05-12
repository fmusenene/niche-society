<?php
/**
 * Success Story Submission Handler - Niche Society
 *
 * Saves client-submitted success stories to success_stories with status = 'active'.
 * Stories appear on the site automatically after submission.
 */

ob_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

$lang = getCurrentLanguage();

$redirect = function ($url, $param = null) {
    session_write_close();
    if (ob_get_level()) ob_end_clean();
    if ($param) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . $param;
    }
    header('Location: ' . $url, true, 302);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $redirect(url('success-story-submit.php'), 'error=invalid');
}

// CSRF
if (!isset($_SESSION['csrf_success_story']) || empty($_POST['csrf_token']) || !hash_equals((string)$_SESSION['csrf_success_story'], (string)$_POST['csrf_token'])) {
    $redirect(url('success-story-submit.php'), 'error=csrf');
}

$clientName = trim($_POST['client_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$clientType = trim($_POST['client_type'] ?? '');
$serviceCategory = trim($_POST['service_category'] ?? '');
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$content = trim($_POST['content'] ?? '');

$errors = [];
if (strlen($clientName) < 2) $errors[] = 'name';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'email';
$allowedTypes = ['royal', 'government', 'corporate', 'individual'];
if (!in_array($clientType, $allowedTypes)) $errors[] = 'client_type';
if (strlen($title) < 5) $errors[] = 'title';
if (strlen($description) < 20) $errors[] = 'description';
if (strlen($content) < 50) $errors[] = 'content';

if (!empty($errors)) {
    $_SESSION['success_story_form_data'] = $_POST;
    $_SESSION['success_story_errors'] = $errors;
    $redirect(url('success-story-submit.php'), 'error=validation');
}

// Build unique slug from title
$baseSlug = preg_replace('/[^a-z0-9]+/i', '-', $title);
$baseSlug = trim(strtolower($baseSlug), '-');
if ($baseSlug === '') $baseSlug = 'story-' . time();
$slug = $baseSlug;
$n = 0;
while (true) {
    $stmt = $pdo->prepare("SELECT id FROM success_stories WHERE slug = ?");
    $stmt->execute([$slug]);
    if (!$stmt->fetch()) break;
    $n++;
    $slug = $baseSlug . '-' . $n;
}

// Store same text in both EN and AR (admin can translate later)
$stmt = $pdo->prepare("
    INSERT INTO success_stories
    (slug, client_name_en, client_name_ar, client_type, title_en, title_ar, description_en, description_ar, content_en, content_ar, service_category, project_date, featured, display_order, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 0, 999, 'active')
");
$stmt->execute([
    $slug,
    $clientName,
    $clientName,
    $clientType,
    $title,
    $title,
    $description,
    $description,
    $content,
    $content,
    $serviceCategory ?: null
]);

// Optional: notify admin (if you have mail configured)
if (defined('ADMIN_EMAIL') && ADMIN_EMAIL && function_exists('mail')) {
    $subj = 'New Success Story Submission - ' . SITE_NAME;
    $body = "A new success story was submitted.\n\n";
    $body .= "Client: $clientName\nEmail: $email\nType: $clientType\nService: $serviceCategory\nTitle: $title\n\n";
    $body .= "Description: $description\n\nContent: $content\n\n";
    $body .= "The story is live on the site. Slug: $slug";
    @mail(ADMIN_EMAIL, $subj, $body, 'From: ' . (defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost')));
}

unset($_SESSION['csrf_success_story'], $_SESSION['success_story_form_data'], $_SESSION['success_story_errors']);
$redirect(url('success-stories.php'), 'submitted=1');
