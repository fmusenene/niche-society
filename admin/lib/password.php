<?php
/**
 * Admin password reset & change helpers
 */

function adminGetRecoveryEmail(array $siteSettings = []): string
{
    $email = trim($siteSettings['admin_email'] ?? '');
    if ($email === '' && defined('CONTACT_EMAIL')) {
        $email = CONTACT_EMAIL;
    }
    return $email;
}

function adminEnsureResetTable(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_password_resets (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        token_hash CHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_token_hash (token_hash),
        INDEX idx_expires (expires_at),
        INDEX idx_email_created (email, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

function adminPublicCsrfToken(): string
{
    if (empty($_SESSION['admin_public_csrf'])) {
        $_SESSION['admin_public_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_public_csrf'];
}

function adminVerifyPublicCsrf(?string $token): bool
{
    return $token !== null && $token !== ''
        && !empty($_SESSION['admin_public_csrf'])
        && hash_equals($_SESSION['admin_public_csrf'], $token);
}

function adminPasswordRequirementLabels(): array
{
    return [
        'length' => 'At least 8 characters',
        'upper' => 'One uppercase letter (A–Z)',
        'lower' => 'One lowercase letter (a–z)',
        'number' => 'One number (0–9)',
        'special' => 'One special character (!@#$…)',
    ];
}

function adminPasswordRequirementChecks(string $password): array
{
    return [
        'length' => strlen($password) >= 8,
        'upper' => (bool) preg_match('/[A-Z]/', $password),
        'lower' => (bool) preg_match('/[a-z]/', $password),
        'number' => (bool) preg_match('/[0-9]/', $password),
        'special' => (bool) preg_match('/[^A-Za-z0-9]/', $password),
    ];
}

function adminValidateNewPassword(string $password): ?string
{
    $messages = [
        'length' => 'Password must be at least 8 characters.',
        'upper' => 'Password must include at least one uppercase letter.',
        'lower' => 'Password must include at least one lowercase letter.',
        'number' => 'Password must include at least one number.',
        'special' => 'Password must include at least one special character.',
    ];

    foreach (adminPasswordRequirementChecks($password) as $rule => $passed) {
        if (!$passed) {
            return $messages[$rule];
        }
    }

    return null;
}

function adminIsPasswordHashed(string $stored): bool
{
    if ($stored === '') {
        return false;
    }
    return password_get_info($stored)['algo'] !== 0;
}

function adminSetPassword(string $newPassword, array &$maintenanceSettings, array &$siteSettings, array &$adminCredentials): bool
{
    $adminCredentials['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
    return adminWriteSettingsFile($maintenanceSettings, $siteSettings, $adminCredentials);
}

function adminVerifyPassword(string $password, array $adminCredentials): bool
{
    $stored = (string) ($adminCredentials['password'] ?? '');
    if ($stored === '') {
        return false;
    }
    if (adminIsPasswordHashed($stored)) {
        return password_verify($password, $stored);
    }
    return hash_equals($stored, $password);
}

function adminSendSystemEmail(string $to, string $subject, string $bodyHtml): bool
{
    $emailConfig = dirname(__DIR__, 2) . '/config/email.php';
    if (file_exists($emailConfig)) {
        require_once $emailConfig;
    }
    $smtpFile = dirname(__DIR__, 2) . '/functions/mail-smtp.php';
    if (file_exists($smtpFile)) {
        require_once $smtpFile;
    }

    $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : (defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'noreply@localhost');
    $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : (defined('SITE_NAME') ? SITE_NAME : 'Site Admin');

    if (defined('SMTP_ENABLED') && SMTP_ENABLED && function_exists('sendMailSMTP')) {
        return (bool) sendMailSMTP($to, $subject, $bodyHtml, $fromEmail, $fromName, null);
    }

    if (function_exists('sendEmail')) {
        return (bool) sendEmail($to, $subject, $bodyHtml, $fromEmail);
    }

    $headers = "From: {$fromName} <{$fromEmail}>\r\nContent-Type: text/html; charset=UTF-8\r\n";
    return @mail($to, $subject, $bodyHtml, $headers);
}

function adminRequestPasswordReset(PDO $pdo, string $email, array $siteSettings): array
{
    adminEnsureResetTable($pdo);
    $pdo->exec('DELETE FROM admin_password_resets WHERE expires_at < NOW()');

    $recoveryEmail = adminGetRecoveryEmail($siteSettings);
    $email = strtolower(trim($email));

    if ($recoveryEmail === '' || !filter_var($recoveryEmail, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Admin recovery email is not configured. Contact your developer once to set it in Contact settings.'];
    }

    // Always show generic success when email doesn't match (don't reveal registered email)
    $genericSuccess = 'If that email is registered for admin access, we sent a reset link. Check your inbox and spam folder. The link expires in 1 hour.';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => true, 'message' => $genericSuccess];
    }

    if (strtolower($recoveryEmail) !== $email) {
        return ['ok' => true, 'message' => $genericSuccess];
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM admin_password_resets WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)');
    $stmt->execute([$email]);
    if ((int) $stmt->fetchColumn() >= 3) {
        return ['ok' => true, 'message' => $genericSuccess];
    }

    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);
    $expires = date('Y-m-d H:i:s', time() + 3600);

    $insert = $pdo->prepare('INSERT INTO admin_password_resets (email, token_hash, expires_at) VALUES (?, ?, ?)');
    $insert->execute([$email, $hash, $expires]);

    $resetUrl = rtrim(SITE_URL, '/') . '/admin/index.php?view=reset&token=' . urlencode($token);
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Website';
    $subject = $siteName . ' — Admin password reset';
    $body = '<div style="font-family:Arial,sans-serif;max-width:520px;margin:0 auto;padding:24px;">'
        . '<h2 style="color:#602234;">Reset your admin password</h2>'
        . '<p>You requested a password reset for the <strong>' . htmlspecialchars($siteName) . '</strong> admin panel.</p>'
        . '<p><a href="' . htmlspecialchars($resetUrl) . '" style="display:inline-block;background:#602234;color:#fffaf3;padding:12px 24px;text-decoration:none;border-radius:8px;">Set new password</a></p>'
        . '<p style="font-size:13px;color:#666;">Or copy this link:<br>' . htmlspecialchars($resetUrl) . '</p>'
        . '<p style="font-size:13px;color:#666;">This link expires in 1 hour. If you did not request this, ignore this email.</p>'
        . '</div>';

    $sent = adminSendSystemEmail($recoveryEmail, $subject, $body);

    if (defined('IS_LOCAL') && IS_LOCAL) {
        $_SESSION['admin_dev_reset_link'] = $resetUrl;
        error_log('Admin password reset link (local): ' . $resetUrl);
    }

    if (!$sent && !(defined('IS_LOCAL') && IS_LOCAL)) {
        error_log('Admin password reset email failed to send to: ' . $recoveryEmail);
    }

    return ['ok' => true, 'message' => $genericSuccess, 'dev_link' => $_SESSION['admin_dev_reset_link'] ?? null];
}

function adminFindValidReset(PDO $pdo, string $token): ?array
{
    if ($token === '' || strlen($token) < 32) {
        return null;
    }
    adminEnsureResetTable($pdo);
    $hash = hash('sha256', $token);
    $stmt = $pdo->prepare('SELECT * FROM admin_password_resets WHERE token_hash = ? AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
    $stmt->execute([$hash]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function adminCompletePasswordReset(
    PDO $pdo,
    string $token,
    string $newPassword,
    string $confirmPassword,
    array &$maintenanceSettings,
    array &$siteSettings,
    array &$adminCredentials
): array {
    if ($error = adminValidateNewPassword($newPassword)) {
        return ['ok' => false, 'error' => $error];
    }
    if ($newPassword !== $confirmPassword) {
        return ['ok' => false, 'error' => 'Passwords do not match.'];
    }

    $row = adminFindValidReset($pdo, $token);
    if (!$row) {
        return ['ok' => false, 'error' => 'This reset link is invalid or has expired. Request a new one.'];
    }

    if (!adminSetPassword($newPassword, $maintenanceSettings, $siteSettings, $adminCredentials)) {
        return ['ok' => false, 'error' => 'Could not save the new password. Check file permissions on config/admin-settings.php.'];
    }

    $pdo->prepare('DELETE FROM admin_password_resets WHERE token_hash = ?')->execute([$row['token_hash']]);
    unset($_SESSION['admin_dev_reset_link']);

    return ['ok' => true, 'message' => 'Your password has been updated. You can log in now.'];
}

function adminChangePassword(
    string $currentPassword,
    string $newPassword,
    string $confirmPassword,
    array &$maintenanceSettings,
    array &$siteSettings,
    array &$adminCredentials
): array {
    if (!adminVerifyPassword($currentPassword, $adminCredentials)) {
        return ['ok' => false, 'error' => 'Current password is incorrect.'];
    }
    if ($error = adminValidateNewPassword($newPassword)) {
        return ['ok' => false, 'error' => $error];
    }
    if ($newPassword !== $confirmPassword) {
        return ['ok' => false, 'error' => 'Passwords do not match.'];
    }
    if (adminVerifyPassword($newPassword, $adminCredentials)) {
        return ['ok' => false, 'error' => 'Choose a password different from your current one.'];
    }
    if (!adminSetPassword($newPassword, $maintenanceSettings, $siteSettings, $adminCredentials)) {
        return ['ok' => false, 'error' => 'Could not save the new password. Check file permissions on config/admin-settings.php.'];
    }
    return ['ok' => true, 'message' => 'Password updated successfully.'];
}
