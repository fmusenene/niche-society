<?php
/**
 * Helper Functions
 * Niche Society Website
 */

function getCurrentLang() {
    return $_SESSION['lang'] ?? DEFAULT_LANG;
}
function getCurrentLanguage() {
    return getCurrentLang();
}
function getTextDirection($lang = null) {
    $lang = $lang ?? getCurrentLang();
    return $lang === 'ar' ? 'rtl' : 'ltr';
}
function getTranslations($lang = null) {
    return loadTranslations($lang);
}
function setLanguage($lang) {
    if (in_array($lang, SUPPORTED_LANGUAGES, true)) {
        $_SESSION['lang'] = $lang;
        return true;
    }
    return false;
}
function loadTranslations($lang = null) {
    $lang = $lang ?? getCurrentLang();
    $file = __DIR__ . "/../lang/{$lang}.json";
    if (file_exists($file)) {
        $json = file_get_contents($file);
        return json_decode($json, true) ?: [];
    }
    return [];
}
function t($key, $default = '') {
    static $translations = null;
    if ($translations === null) {
        $translations = loadTranslations();
    }
    $text = $translations[$key] ?? $default;

    if (getCurrentLang() === 'ar' && !empty($text)) {
        $text = preg_replace_callback('/\d+/', function($matches) {
            return formatNumber($matches[0], 'ar');
        }, $text);
    }
    return $text;
}
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            $data[$k] = sanitize($v);
        }
        return $data;
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}
function isValidEmail($email) {
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}
function redirect($url) {
    header("Location: $url");
    exit;
}
function handleLanguageSwitch() {
    if (isset($_GET['lang'])) {
        setLanguage($_GET['lang']);
        $scriptName = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');

        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        $queryParams = [];
        if (!empty($queryString)) {
            parse_str($queryString, $queryParams);
        }
        unset($queryParams['lang']);

        $finalUrl = SITE_URL . '/' . $scriptName;
        if (!empty($queryParams)) {
            $finalUrl .= '?' . http_build_query($queryParams);
        }
        redirect($finalUrl);
    }
}
function getCurrentUrl() {
    // Keep as path (REQUEST_URI). Absolute URL is built elsewhere using SITE_URL + getCurrentUrl()
    return $_SERVER['REQUEST_URI'] ?? '/';
}
function formatNumber($number, $lang = null) {
    $lang = $lang ?? getCurrentLang();

    if ($lang === 'ar') {
        $arabicNumerals = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $numberStr = (string)$number;

        $hasPlus = false;
        if (strpos($numberStr, '+') !== false) {
            $hasPlus = true;
            $numberStr = str_replace('+', '', $numberStr);
        }

        $result = '';
        $len = strlen($numberStr);
        for ($i = 0; $i < $len; $i++) {
            $char = $numberStr[$i];
            if (ctype_digit($char)) {
                $result .= $arabicNumerals[(int)$char];
            } else {
                $result .= $char;
            }
        }
        if ($hasPlus) {
            $result = '+' . $result;
        }
        return $result;
    }

    return (string)$number;
}
function url($path = '') {
    if ($path === '') return SITE_URL;
    $path = ltrim((string)$path, '/');
    return SITE_URL . '/' . $path;
}
function isActive($page) {
    $current = basename($_SERVER['PHP_SELF'] ?? '');
    if ($page === 'index.php') {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $requestPath = parse_url($requestUri, PHP_URL_PATH) ?: '';
        $requestPath = rtrim($requestPath, '/');
        $basePath = rtrim(parse_url(SITE_URL, PHP_URL_PATH) ?: '', '/');

        if ($current === 'index.php' || $requestPath === $basePath || $requestPath === $basePath . '/index.php') {
            return 'active';
        }
        return '';
    }
    return ($current === $page) ? 'active' : '';
}
function formatDate($date, $format = 'Y-m-d') {
    $lang = getCurrentLang();
    $timestamp = strtotime((string)$date);

    if ($lang === 'ar') {
        $months_ar = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        $day = date('d', $timestamp);
        $month = $months_ar[(int)date('n', $timestamp)];
        $year = date('Y', $timestamp);
        return "$day $month $year";
    }

    return date($format, $timestamp);
}

/**
 * Production email sender:
 * - Uses PHPMailer SMTP if configured + installed
 * - Falls back to PHP mail()
 *
 * Expected $args keys:
 * to, subject, body_text, from, from_name, reply_to, to_name
 */
function sendEmailSmart(array $args): bool
{
    $to        = (string)($args['to'] ?? '');
    $toName    = (string)($args['to_name'] ?? '');
    $subject   = (string)($args['subject'] ?? '');
    $bodyText  = (string)($args['body_text'] ?? '');
    $from      = (string)($args['from'] ?? CONTACT_EMAIL);
    $fromName  = (string)($args['from_name'] ?? SITE_NAME);
    $replyTo   = (string)($args['reply_to'] ?? $from);

    if ($to === '' || $subject === '' || $bodyText === '') {
        error_log("sendEmailSmart: missing required fields");
        return false;
    }

    // Try SMTP via PHPMailer if enabled and available
    $phpMailerBase = __DIR__ . '/../vendor/PHPMailer/src/';
    $phpMailerOk = file_exists($phpMailerBase . 'PHPMailer.php')
                && file_exists($phpMailerBase . 'SMTP.php')
                && file_exists($phpMailerBase . 'Exception.php');

    $smtpReady = defined('SMTP_ENABLED') && SMTP_ENABLED
        && defined('SMTP_HOST') && SMTP_HOST !== ''
        && defined('SMTP_USERNAME') && SMTP_USERNAME !== ''
        && defined('SMTP_PASSWORD') && SMTP_PASSWORD !== '';

    if ($smtpReady && $phpMailerOk) {
        try {
            require_once $phpMailerBase . 'Exception.php';
            require_once $phpMailerBase . 'PHPMailer.php';
            require_once $phpMailerBase . 'SMTP.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->Port = (int)(defined('SMTP_PORT') ? SMTP_PORT : 587);

            // Auto choose encryption
            if ($mail->Port === 465) {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to, $toName ?: $to);
            $mail->addReplyTo($replyTo);

            // Send as plain text (reliable)
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $bodyText;

            $mail->send();
            return true;
        } catch (Throwable $e) {
            error_log("SMTP send failed: " . $e->getMessage());
            // fall through to mail() fallback
        }
    }

    // Fallback to PHP mail()
    $headers = [];
    $headers[] = 'From: ' . $fromName . ' <' . $from . '>';
    $headers[] = 'Reply-To: ' . $replyTo;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    $ok = mail($to, $subject, $bodyText, implode("\r\n", $headers));
    if (!$ok) {
        error_log("mail() failed sending to {$to}");
    }
    return $ok;
}

function generateMetaTags($title, $description, $keywords = '', $image = '') {
    $title = sanitize($title);
    $description = sanitize($description);

    echo "<title>{$title} - " . SITE_NAME . "</title>\n";
    echo "<meta name='description' content='{$description}'>\n";

    if ($keywords) {
        echo "<meta name='keywords' content='" . sanitize($keywords) . "'>\n";
    }

    $logoUrl = ASSETS_URL . "/images/" . urlencode('Logo 1.jpg');

    echo "<meta property='og:title' content='{$title}'>\n";
    echo "<meta property='og:description' content='{$description}'>\n";
    echo "<meta property='og:type' content='website'>\n";
    echo "<meta property='og:url' content='" . SITE_URL . getCurrentUrl() . "'>\n";
    echo "<meta property='og:image' content='" . ($image ? ASSETS_URL . "/images/{$image}" : $logoUrl) . "'>\n";
    echo "<meta property='og:image:width' content='1200'>\n";
    echo "<meta property='og:image:height' content='630'>\n";
    echo "<meta property='og:image:alt' content='" . SITE_NAME . " Logo'>\n";
    echo "<meta property='og:site_name' content='" . SITE_NAME . "'>\n";

    echo "<meta name='twitter:card' content='summary_large_image'>\n";
    echo "<meta name='twitter:title' content='{$title}'>\n";
    echo "<meta name='twitter:description' content='{$description}'>\n";
    echo "<meta name='twitter:image' content='" . ($image ? ASSETS_URL . "/images/{$image}" : $logoUrl) . "'>\n";
    echo "<meta name='twitter:image:alt' content='" . SITE_NAME . " Logo'>\n";
}
function getMetaTags($title, $description, $url = '', $image = '') {
    generateMetaTags($title, $description, '', $image);
}
function humanFileSize($bytes, $decimals = 2) {
    $size = ['B','KB','MB','GB','TB'];
    $bytes = (float)$bytes;
    $factor = $bytes > 0 ? floor((strlen((string)(int)$bytes) - 1) / 3) : 0;
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[(int)$factor];
}
function truncate($text, $length = 150, $suffix = '...') {
    $text = (string)$text;
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
function requireAdmin() {
    if (!isAdmin()) {
        redirect(SITE_URL . '/login.php');
    }
}

if (file_exists(__DIR__ . '/helpers-extended.php')) {
    require_once __DIR__ . '/helpers-extended.php';
}
