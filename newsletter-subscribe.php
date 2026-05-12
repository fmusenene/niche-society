<?php
/**
 * Newsletter Subscribe Handler - Niche Society
 *
 * Saves email to newsletter_subscribers table and sends a welcome email
 * to the subscriber. Uses same SMTP/mail setup as contact form.
 */

ob_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

if (file_exists(__DIR__ . '/config/email.php')) {
    require_once __DIR__ . '/config/email.php';
}
if (defined('SMTP_ENABLED') && SMTP_ENABLED && file_exists(__DIR__ . '/functions/mail-smtp.php')) {
    require_once __DIR__ . '/functions/mail-smtp.php';
}
if (file_exists(__DIR__ . '/config/mailchimp.php')) {
    require_once __DIR__ . '/config/mailchimp.php';
}

$lang = getCurrentLanguage();

/**
 * Add subscriber to Mailchimp audience (optional). Does not block signup if it fails.
 * Requires config/mailchimp.php with MAILCHIMP_ENABLED, MAILCHIMP_API_KEY, MAILCHIMP_LIST_ID.
 */
function addSubscriberToMailchimp($email, $name) {
    if (!defined('MAILCHIMP_ENABLED') || !MAILCHIMP_ENABLED || !defined('MAILCHIMP_API_KEY') || !defined('MAILCHIMP_LIST_ID')) {
        return true;
    }
    $key = MAILCHIMP_API_KEY;
    $listId = MAILCHIMP_LIST_ID;
    if ($key === '' || $key === 'your-api-key-here' || $listId === '' || $listId === 'your-list-id-here') {
        return true;
    }
    // Data center is the part after the last hyphen (e.g. us20)
    $dc = 'us20';
    if (preg_match('/-([a-z0-9]+)$/i', $key, $m)) {
        $dc = $m[1];
    }
    $url = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . md5(strtolower($email));
    $payload = [
        'email_address' => $email,
        'status' => 'subscribed',
        'merge_fields' => []
    ];
    if ($name !== null && $name !== '') {
        $payload['merge_fields']['FNAME'] = substr($name, 0, 255);
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $key
        ],
        CURLOPT_TIMEOUT => 10
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    }
    // 400 = already subscribed or invalid; don't treat as fatal
    if ($httpCode === 400) {
        return true;
    }
    error_log("Mailchimp add subscriber failed: HTTP " . $httpCode . " " . substr($response, 0, 200));
    return false;
}

/**
 * Send welcome/confirmation email to new subscriber (same transport as contact form).
 */
function sendWelcomeNewsletterEmail($toEmail, $name, $lang) {
    $subject = $lang === 'ar'
        ? 'تم اشتراكك في النشرة الإخبارية - نيش سوسيتي'
        : 'You\'re Subscribed to Our Newsletter - Niche Society';

    $displayName = $name !== null && $name !== '' ? htmlspecialchars($name) : ($lang === 'ar' ? 'المشترك' : 'Subscriber');
    $blogUrl = SITE_URL . '/blog.php';

    $message = "
    <html>
    <head>
        <meta charset='utf-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #602234; color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; background: #fffaf3; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            a { color: #602234; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Niche Society</h1>
            </div>
            <div class='content'>
                " . ($lang === 'ar'
                    ? "<p>مرحباً <strong>" . $displayName . "</strong>،</p>
                       <p>شكراً لاشتراكك في نشرتنا الإخبارية. سنرسل لك آخر الأخبار والمقالات مباشرة إلى بريدك الإلكتروني.</p>
                       <p>يمكنك زيارة <a href='" . $blogUrl . "'>المدونة</a> في أي وقت لقراءة أحدث المحتوى.</p>
                       <p>مع أطيب التحيات،<br>فريق نيش سوسيتي</p>"
                    : "<p>Hello <strong>" . $displayName . "</strong>,</p>
                       <p>Thank you for subscribing to our newsletter. We'll send you the latest news and articles directly to your inbox.</p>
                       <p>You can visit our <a href='" . $blogUrl . "'>blog</a> anytime to read the latest content.</p>
                       <p>Best regards,<br>Niche Society Team</p>"
                ) . "
            </div>
            <div class='footer'>
                <p><strong>Niche Society</strong></p>
                <p>" . (defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@niche-society.com') . " | " . (defined('CONTACT_PHONE') ? CONTACT_PHONE : '') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : (defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@niche-society.com');
    $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Niche Society';

    if (defined('SMTP_ENABLED') && SMTP_ENABLED && function_exists('sendMailSMTP')) {
        $result = sendMailSMTP($toEmail, $subject, $message, $fromEmail, $fromName, null);
    } else {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
            'X-Mailer: PHP/' . phpversion()
        ];
        $result = @mail($toEmail, $subject, $message, implode("\r\n", $headers), "-f " . $fromEmail);
    }

    if (!$result) {
        error_log("Newsletter welcome email failed to send to: " . $toEmail);
    }
    return $result;
}

// Redirect back to referer or blog with message
$redirect = function ($param, $code = null) {
    session_write_close();
    if (ob_get_level()) ob_end_clean();
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $host = parse_url(SITE_URL, PHP_URL_HOST);
    $url = ($referer !== '' && $host !== false && strpos($referer, $host) !== false)
        ? $referer
        : url('blog.php');
    $sep = strpos($url, '?') !== false ? '&' : '?';
    $url .= $sep . 'newsletter=' . $param;
    if ($param === 'error' && $code !== null && $code !== '') {
        $url .= '&code=' . rawurlencode($code);
    }
    header('Location: ' . $url, true, 302);
    exit;
};

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $redirect('error', 'invalid');
}

// Create table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(191) NOT NULL,
        name VARCHAR(100) DEFAULT NULL,
        status ENUM('active', 'unsubscribed') DEFAULT 'active',
        source VARCHAR(50) DEFAULT 'blog',
        ip_address VARCHAR(45) DEFAULT NULL,
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uk_email (email),
        INDEX idx_status (status),
        INDEX idx_subscribed_at (subscribed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (PDOException $e) {
    error_log("Newsletter table error: " . $e->getMessage());
    $redirect('error', 'database');
}

// CSRF: accept session or cookie token
if (!isset($_SESSION['csrf_newsletter'])) {
    $_SESSION['csrf_newsletter'] = bin2hex(random_bytes(32));
}
$postToken = $_POST['csrf_token'] ?? '';
$sessionValid = isset($_SESSION['csrf_newsletter']) && $postToken !== '' && hash_equals((string) $_SESSION['csrf_newsletter'], $postToken);
$cookieValid = isset($_COOKIE['csrf_newsletter']) && $postToken !== '' && hash_equals((string) $_COOKIE['csrf_newsletter'], $postToken);
if (!$sessionValid && !$cookieValid) {
    $redirect('error', 'csrf');
}
if (PHP_VERSION_ID >= 70300) {
    setcookie('csrf_newsletter', '', ['expires' => time() - 3600, 'path' => '/', 'secure' => !empty($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax']);
} else {
    @setcookie('csrf_newsletter', '', time() - 3600, '/');
}

// Validate email
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $redirect('error', 'invalid_email');
}
$name = isset($_POST['name']) ? trim(substr($_POST['name'], 0, 100)) : null;
$source = isset($_POST['source']) ? trim(substr($_POST['source'], 0, 50)) : 'blog';
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
if (strlen($ip) > 45) $ip = substr($ip, 0, 45);

try {
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, name, source, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $name ?: null, $source, $ip]);
} catch (PDOException $e) {
    // Duplicate email = already subscribed
    if ($e->getCode() == 23000 || strpos($e->getMessage(), 'Duplicate') !== false) {
        $redirect('already');
        return;
    }
    error_log("Newsletter subscribe error: " . $e->getMessage());
    $redirect('error', 'database');
}

// Sync to Mailchimp if configured (optional; signup still succeeds if this fails)
addSubscriberToMailchimp($email, $name);

// Send welcome email to subscriber (same SMTP/mail as contact form)
sendWelcomeNewsletterEmail($email, $name, $lang);

// Rotate CSRF token
unset($_SESSION['csrf_newsletter']);
$_SESSION['csrf_newsletter'] = bin2hex(random_bytes(32));

$redirect('success');
