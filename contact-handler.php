<?php
/**
 * Contact Form Handler - Niche Society
 * PRODUCTION SAFE (SMTP preferred, mail() fallback)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =====================================================
   ONLY ACCEPT POST
===================================================== */
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    $_SESSION['contact_error'] = 'Invalid request method.';
    header('Location: /contact.php');
    exit;
}

/* =====================================================
   BASIC POST CHECK
===================================================== */
if (empty($_POST)) {
    $_SESSION['contact_error'] = 'Form data was not received.';
    header('Location: /contact.php');
    exit;
}

/* =====================================================
   HONEYPOT SPAM CHECK
===================================================== */
if (!empty($_POST['website'] ?? '')) {
    // Silently pretend it worked (prevents bots learning)
    $_SESSION['contact_success'] = 'Thank you! Your message has been received.';
    header('Location: /contact.php');
    exit;
}

/* =====================================================
   CSRF CHECK
===================================================== */
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], (string)$_POST['csrf_token'])
) {
    $_SESSION['contact_error'] = 'Security verification failed.';
    $_SESSION['form_data'] = sanitize($_POST);
    header('Location: /contact.php');
    exit;
}

/* =====================================================
   SANITIZE INPUT
===================================================== */
$data = [
    'name'    => trim((string)($_POST['name'] ?? '')),
    'email'   => trim((string)($_POST['email'] ?? '')),
    'phone'   => trim((string)($_POST['phone'] ?? '')),
    'service' => trim((string)($_POST['service'] ?? '')),
    'message' => trim((string)($_POST['message'] ?? '')),
    'lang'    => (($_POST['lang'] ?? 'en') === 'ar') ? 'ar' : 'en',
];

/* =====================================================
   VALIDATION
===================================================== */
$errors = [];

if (mb_strlen($data['name']) < 2 || mb_strlen($data['name']) > 100) {
    $errors[] = ($data['lang'] === 'ar') ? 'الاسم غير صالح' : 'Invalid name';
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = ($data['lang'] === 'ar') ? 'البريد الإلكتروني غير صالح' : 'Invalid email';
}

if (mb_strlen($data['phone']) < 6 || mb_strlen($data['phone']) > 20) {
    $errors[] = ($data['lang'] === 'ar') ? 'رقم الهاتف غير صالح' : 'Invalid phone number';
}

if (mb_strlen($data['message']) < 10 || mb_strlen($data['message']) > 1000) {
    $errors[] = ($data['lang'] === 'ar') ? 'الرسالة قصيرة جداً' : 'Message too short';
}

if (!empty($errors)) {
    $_SESSION['contact_error'] = implode(' - ', $errors);
    $_SESSION['form_data'] = $data;
    header('Location: /contact.php');
    exit;
}

/* =====================================================
   DATABASE SAVE (do not break the flow if DB fails)
===================================================== */
try {
    // Ensure $pdo exists (database.php returns it)
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        $pdo = require __DIR__ . '/config/database.php';
    }

    $stmt = $pdo->prepare("
        INSERT INTO contact_forms
        (name, email, phone, service_interest, message, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['service'],
        $data['message'],
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
} catch (Throwable $e) {
    error_log("Contact form DB insert failed: " . $e->getMessage());
    // Continue to email sending even if DB fails
}

/* =====================================================
   BUILD EMAIL CONTENT
===================================================== */
$adminTo  = CONTACT_EMAIL;
$subject  = 'New Contact Form Submission - Niche Society';

$bodyText = "New Contact Form Submission\n\n"
          . "Name: {$data['name']}\n"
          . "Email: {$data['email']}\n"
          . "Phone: {$data['phone']}\n"
          . "Service: {$data['service']}\n\n"
          . "Message:\n{$data['message']}\n\n"
          . "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n"
          . "UA: " . ($_SERVER['HTTP_USER_AGENT'] ?? '') . "\n";

$autoSubject = ($data['lang'] === 'ar')
    ? 'تم استلام رسالتك - Niche Society'
    : 'We received your message - Niche Society';

$autoBodyText = ($data['lang'] === 'ar')
    ? "مرحباً {$data['name']},\n\nشكراً لتواصلك مع Niche Society.\nتم استلام رسالتك وسنقوم بالرد عليك قريباً.\n\nمع التحية,\nNiche Society\n" . SITE_URL
    : "Dear {$data['name']},\n\nThank you for contacting Niche Society.\nWe have received your message and will get back to you shortly.\n\nBest regards,\nNiche Society\n" . SITE_URL;

/* =====================================================
   SEND EMAILS (SMTP preferred, mail() fallback)
===================================================== */
$adminSent = sendEmailSmart([
    'to'        => $adminTo,
    'to_name'   => 'Niche Society',
    'subject'   => $subject,
    'body_text' => $bodyText,
    'reply_to'  => $data['email'], // visitor email
    'from'      => CONTACT_EMAIL,
    'from_name' => 'Niche Society Website'
]);

$userSent = sendEmailSmart([
    'to'        => $data['email'],
    'to_name'   => $data['name'],
    'subject'   => $autoSubject,
    'body_text' => $autoBodyText,
    'reply_to'  => CONTACT_EMAIL,
    'from'      => CONTACT_EMAIL,
    'from_name' => 'Niche Society'
]);

/* =====================================================
   SET USER MESSAGE
===================================================== */
if ($adminSent) {
    $_SESSION['contact_success'] = ($data['lang'] === 'ar')
        ? 'شكراً لتواصلك معنا! تم إرسال رسالتك بنجاح وسنرد عليك قريباً.'
        : 'Thank you for contacting us! Your message has been sent successfully. We will get back to you soon.';
    unset($_SESSION['form_data']);
} else {
    // If admin email failed, show a helpful message
    $_SESSION['contact_error'] = ($data['lang'] === 'ar')
        ? 'تعذر إرسال الرسالة حالياً. يرجى المحاولة لاحقاً أو مراسلتنا مباشرة على info@niche-society.com'
        : 'We could not send your message at the moment. Please try again later or email us directly at info@niche-society.com';
    $_SESSION['form_data'] = $data;
}

// Rotate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

header('Location: /contact.php');
exit;
