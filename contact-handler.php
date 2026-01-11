<?php
/**
 * Contact Form Handler - Niche Society
 * 
 * Processes contact form submissions with:
 * - CSRF protection
 * - Input validation and sanitization
 * - Rate limiting
 * - Spam prevention
 * - Email notifications
 * - Database storage
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// Ensure required tables exist
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_forms (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        service_interest VARCHAR(100),
        message TEXT NOT NULL,
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_email (email),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS security_logs (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        event_type VARCHAR(50) NOT NULL,
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_event_type (event_type),
        INDEX idx_ip_address (ip_address),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (PDOException $e) {
    error_log("Table creation error (non-fatal): " . $e->getMessage());
}

// Set JSON header for AJAX responses - REMOVED because we're using redirects, not JSON
// header('Content-Type: application/json');

/**
 * Send JSON response and exit
 */
function sendResponse($success, $message, $redirect = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($redirect) {
        $response['redirect'] = $redirect;
    }
    echo json_encode($response);
    exit;
}

/**
 * Validate and sanitize input
 */
function validateInput($data) {
    $errors = [];
    
    // Name validation
    if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
        $errors[] = 'Name must be at least 2 characters long';
    }
    
    // Email validation
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address';
    }
    
    // Phone validation
    if (empty($data['phone']) || !preg_match('/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/', $data['phone'])) {
        $errors[] = 'Please provide a valid phone number';
    }
    
    // Message validation
    if (empty($data['message']) || strlen(trim($data['message'])) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }
    
    // Privacy agreement
    if (!isset($data['privacy']) || $data['privacy'] !== 'on') {
        $errors[] = 'You must agree to the privacy policy';
    }
    
    return $errors;
}

/**
 * Check rate limiting
 */
function checkRateLimit($pdo, $ipAddress) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM contact_forms 
        WHERE ip_address = ? 
        AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute([$ipAddress]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['count'] < 3; // Max 3 submissions per hour
}

/**
 * Log security event
 */
function logSecurityEvent($pdo, $eventType, $ipAddress, $details) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO security_logs (event_type, ip_address, user_agent, details, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $eventType,
            $ipAddress,
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            $details
        ]);
    } catch (Exception $e) {
        // Silent fail for logging
        error_log("Security log failed: " . $e->getMessage());
    }
}

/**
 * Send email notification
 */
function sendEmailNotification($data, $lang) {
    $to = CONTACT_EMAIL; // info@niche-society.com
    $subject = $lang === 'ar' 
        ? 'رسالة جديدة من موقع نيتش سوسيتي' 
        : 'New Contact Form Submission - Niche Society';
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #602234; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .field { margin-bottom: 15px; }
            .field-label { font-weight: bold; color: #602234; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>" . ($lang === 'ar' ? 'رسالة جديدة' : 'New Contact Message') . "</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='field-label'>" . ($lang === 'ar' ? 'الاسم:' : 'Name:') . "</span>
                    " . htmlspecialchars($data['name']) . "
                </div>
                <div class='field'>
                    <span class='field-label'>" . ($lang === 'ar' ? 'البريد الإلكتروني:' : 'Email:') . "</span>
                    " . htmlspecialchars($data['email']) . "
                </div>
                <div class='field'>
                    <span class='field-label'>" . ($lang === 'ar' ? 'الهاتف:' : 'Phone:') . "</span>
                    " . htmlspecialchars($data['phone']) . "
                </div>
                <div class='field'>
                    <span class='field-label'>" . ($lang === 'ar' ? 'الخدمة المهتم بها:' : 'Service of Interest:') . "</span>
                    " . htmlspecialchars($data['service'] ?? 'Not specified') . "
                </div>
                <div class='field'>
                    <span class='field-label'>" . ($lang === 'ar' ? 'الرسالة:' : 'Message:') . "</span>
                    <p>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                </div>
            </div>
            <div class='footer'>
                <p>" . ($lang === 'ar' ? 'تم الإرسال من موقع نيتش سوسيتي' : 'Sent from Niche Society website') . "</p>
                <p>" . date('Y-m-d H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: Niche Society Website <noreply@niche-society.com>',
        'Reply-To: ' . $data['email'],
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $result = @mail($to, $subject, $message, implode("\r\n", $headers));
    
    // Log email sending result
    if (!$result) {
        $error = error_get_last();
        error_log("Email notification failed to send to: $to");
        error_log("Error details: " . ($error ? $error['message'] : 'Unknown error'));
        error_log("Note: On localhost/XAMPP, PHP mail() requires SMTP configuration. Emails will work on production server.");
    } else {
        error_log("Email notification sent successfully to: $to");
    }
    
    return $result;
}

/**
 * Send auto-reply to customer
 */
function sendAutoReply($email, $name, $lang) {
    $subject = $lang === 'ar' 
        ? 'شكراً لتواصلك معنا - نيتش سوسيتي' 
        : 'Thank You for Contacting Us - Niche Society';
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #602234; color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; background: white; }
            .footer { text-align: center; padding: 20px; background: #FFF7E7; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Niche Society</h1>
            </div>
            <div class='content'>
                " . ($lang === 'ar' 
                    ? "<p>عزيزي/عزيزتي <strong>" . htmlspecialchars($name) . "</strong>،</p>
                       <p>شكراً لتواصلك معنا. لقد استلمنا رسالتك وسيقوم فريقنا بالرد عليك في أقرب وقت ممكن.</p>
                       <p>نحن نقدر اهتمامك بخدماتنا ونتطلع إلى خدمتك.</p>
                       <p>مع أطيب التحيات،<br>فريق نيتش سوسيتي</p>"
                    : "<p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
                       <p>Thank you for contacting us. We have received your message and our team will respond to you as soon as possible.</p>
                       <p>We appreciate your interest in our services and look forward to serving you.</p>
                       <p>Best regards,<br>Niche Society Team</p>"
                ) . "
            </div>
            <div class='footer'>
                <p><strong>" . ($lang === 'ar' ? 'نيتش سوسيتي' : 'Niche Society') . "</strong></p>
                <p>info@niche-society.com | +966 532 447 976</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: Niche Society <info@niche-society.com>',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $result = @mail($email, $subject, $message, implode("\r\n", $headers));
    
    // Log email sending result
    if (!$result) {
        $error = error_get_last();
        error_log("Auto-reply email failed to send to: $email");
        error_log("Error details: " . ($error ? $error['message'] : 'Unknown error'));
        error_log("Note: On localhost/XAMPP, PHP mail() requires SMTP configuration. Emails will work on production server.");
    } else {
        error_log("Auto-reply email sent successfully to: $email");
    }
    
    return $result;
}

// ============================================
// MAIN PROCESSING LOGIC
// ============================================

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['contact_error'] = 'Invalid request method';
        header('Location: contact.php');
        exit;
    }

    $lang = $_POST['lang'] ?? 'en';
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // 1. CSRF Token Validation
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        logSecurityEvent($pdo, 'csrf_violation', $ipAddress, 'Invalid CSRF token');
        $_SESSION['contact_error'] = $lang === 'ar' 
            ? 'فشل التحقق الأمني. يرجى المحاولة مرة أخرى' 
            : 'Security verification failed. Please try again';
        header('Location: contact.php');
        exit;
    }

    // 2. Honeypot Check (spam prevention)
    if (!empty($_POST['website'])) {
        logSecurityEvent($pdo, 'spam_detected', $ipAddress, 'Honeypot triggered');
        $_SESSION['contact_error'] = $lang === 'ar' 
            ? 'تم اكتشاف محاولة غير صحيحة' 
            : 'Invalid submission detected';
        header('Location: contact.php');
        exit;
    }

    // 3. Rate Limiting Check
    if (!checkRateLimit($pdo, $ipAddress)) {
        logSecurityEvent($pdo, 'rate_limit_exceeded', $ipAddress, 'Too many submissions');
        $_SESSION['contact_error'] = $lang === 'ar' 
            ? 'لقد تجاوزت الحد المسموح من المحاولات. يرجى المحاولة بعد ساعة' 
            : 'You have exceeded the maximum number of submissions. Please try again in an hour';
        header('Location: contact.php');
        exit;
    }

    // 4. Sanitize Input
    $data = [
        'name' => trim(htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8')),
        'email' => trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL)),
        'phone' => trim(htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8')),
        'service' => trim(htmlspecialchars($_POST['service'] ?? '', ENT_QUOTES, 'UTF-8')),
        'message' => trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8')),
        'privacy' => $_POST['privacy'] ?? ''
    ];

    // 5. Validate Input
    $errors = validateInput($data);
    if (!empty($errors)) {
        logSecurityEvent($pdo, 'validation_failed', $ipAddress, implode(', ', $errors));
        $_SESSION['contact_error'] = $lang === 'ar' 
            ? 'يرجى التحقق من البيانات المدخلة: ' . implode(', ', $errors)
            : 'Please check your input: ' . implode(', ', $errors);
        header('Location: contact.php');
        exit;
    }

    // 6. Save to Database
    $stmt = $pdo->prepare("
        INSERT INTO contact_forms 
        (name, email, phone, service_interest, message, ip_address, user_agent, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'new', NOW())
    ");
    
    $success = $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['service'],
        $data['message'],
        $ipAddress,
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ]);

    if (!$success) {
        throw new Exception('Database insertion failed');
    }

    // 7. Send Email Notifications
    $emailSent = sendEmailNotification($data, $lang);
    $autoReplySent = sendAutoReply($data['email'], $data['name'], $lang);
    
    // Log email sending results (for debugging on localhost)
    if (!$emailSent || !$autoReplySent) {
        error_log("Warning: Some emails failed to send. This is normal on localhost/XAMPP without SMTP configuration.");
        error_log("Email notification sent: " . ($emailSent ? 'Yes' : 'No'));
        error_log("Auto-reply sent: " . ($autoReplySent ? 'Yes' : 'No'));
    }

    // 8. Log Success
    logSecurityEvent($pdo, 'contact_form_success', $ipAddress, 'Form submitted successfully');

    // 9. Clear CSRF token and generate new one
    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // 10. Set Success Message
    $_SESSION['contact_success'] = $lang === 'ar' 
        ? 'شكراً لتواصلك معنا! سنرد عليك في أقرب وقت ممكن.'
        : 'Thank you for contacting us! We will get back to you as soon as possible.';

    // Redirect back to contact page
    header('Location: contact.php');
    exit;

} catch (PDOException $e) {
    // Database error - log detailed error
    $errorDetails = "Contact form database error: " . $e->getMessage();
    $errorDetails .= "\nSQL State: " . $e->getCode();
    $errorDetails .= "\nFile: " . $e->getFile() . " Line: " . $e->getLine();
    error_log($errorDetails);
    
    // Preserve form data in session for user convenience
    if (isset($_POST)) {
        $_SESSION['form_data'] = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'service' => $_POST['service'] ?? '',
            'message' => $_POST['message'] ?? ''
        ];
    }
    
    $_SESSION['contact_error'] = $lang === 'ar' 
        ? 'حدث خطأ أثناء معالجة طلبك. يرجى المحاولة لاحقاً'
        : 'An error occurred while processing your request. Please try again later';
    header('Location: contact.php');
    exit;

} catch (Exception $e) {
    // General error - log detailed error
    $errorDetails = "Contact form error: " . $e->getMessage();
    $errorDetails .= "\nFile: " . $e->getFile() . " Line: " . $e->getLine();
    $errorDetails .= "\nTrace: " . $e->getTraceAsString();
    error_log($errorDetails);
    
    // Preserve form data in session for user convenience
    if (isset($_POST)) {
        $_SESSION['form_data'] = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'service' => $_POST['service'] ?? '',
            'message' => $_POST['message'] ?? ''
        ];
    }
    
    $_SESSION['contact_error'] = $lang === 'ar' 
        ? 'حدث خطأ غير متوقع. يرجى المحاولة لاحقاً'
        : 'An unexpected error occurred. Please try again later';
    header('Location: contact.php');
    exit;
}
