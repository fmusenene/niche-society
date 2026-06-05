<?php
/**
 * Admin login security — lockout, session hardening
 */

function adminEnsureLoginAttemptsTable(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_login_attempts (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        username VARCHAR(100) NOT NULL DEFAULT '',
        attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip_time (ip_address, attempted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

function adminClientIp(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']);
        $candidate = trim($parts[0]);
        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            $ip = $candidate;
        }
    }
    return $ip;
}

function adminMaxLoginAttempts(): int
{
    return defined('MAX_LOGIN_ATTEMPTS') ? max(1, (int) MAX_LOGIN_ATTEMPTS) : 5;
}

function adminLoginLockoutSeconds(): int
{
    return defined('LOGIN_TIMEOUT') ? max(60, (int) LOGIN_TIMEOUT) : 900;
}

function adminPurgeOldLoginAttempts(PDO $pdo): void
{
    $seconds = adminLoginLockoutSeconds();
    $pdo->prepare('DELETE FROM admin_login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL ? SECOND)')
        ->execute([$seconds * 2]);
}

function adminFailedLoginCount(PDO $pdo, string $ip): int
{
    adminEnsureLoginAttemptsTable($pdo);
    adminPurgeOldLoginAttempts($pdo);
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM admin_login_attempts
         WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL ? SECOND)'
    );
    $stmt->execute([$ip, adminLoginLockoutSeconds()]);
    return (int) $stmt->fetchColumn();
}

function adminIsLoginLockedOut(PDO $pdo, ?string $ip = null): bool
{
    $ip = $ip ?? adminClientIp();
    return adminFailedLoginCount($pdo, $ip) >= adminMaxLoginAttempts();
}

function adminLoginLockoutMinutesRemaining(PDO $pdo, ?string $ip = null): int
{
    $ip = $ip ?? adminClientIp();
    adminEnsureLoginAttemptsTable($pdo);
    $stmt = $pdo->prepare(
        'SELECT attempted_at FROM admin_login_attempts
         WHERE ip_address = ?
         ORDER BY attempted_at DESC LIMIT 1'
    );
    $stmt->execute([$ip]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return 0;
    }
    $elapsed = time() - strtotime($row['attempted_at']);
    $remaining = adminLoginLockoutSeconds() - $elapsed;
    return max(1, (int) ceil($remaining / 60));
}

function adminRecordLoginFailure(PDO $pdo, string $username, ?string $ip = null): void
{
    adminEnsureLoginAttemptsTable($pdo);
    $pdo->prepare('INSERT INTO admin_login_attempts (ip_address, username) VALUES (?, ?)')
        ->execute([$ip ?? adminClientIp(), mb_substr($username, 0, 100)]);
}

function adminClearLoginFailures(PDO $pdo, ?string $ip = null): void
{
    adminEnsureLoginAttemptsTable($pdo);
    $pdo->prepare('DELETE FROM admin_login_attempts WHERE ip_address = ?')
        ->execute([$ip ?? adminClientIp()]);
}

function adminEstablishSession(): void
{
    session_regenerate_id(true);
    $_SESSION['admin_authenticated'] = true;
    $_SESSION['admin_login_at'] = time();
    $_SESSION['admin_last_activity'] = time();
}

function adminAttemptLogin(
    PDO $pdo,
    string $username,
    string $password,
    array &$maintenanceSettings,
    array &$siteSettings,
    array &$adminCredentials
): array {
    $ip = adminClientIp();

    if (adminIsLoginLockedOut($pdo, $ip)) {
        $mins = adminLoginLockoutMinutesRemaining($pdo, $ip);
        return [
            'ok' => false,
            'error' => "Too many failed login attempts. Please wait {$mins} minute(s) and try again.",
        ];
    }

    $expectedUser = (string) ($adminCredentials['username'] ?? 'admin');
    if (!hash_equals($expectedUser, $username)) {
        adminRecordLoginFailure($pdo, $username, $ip);
        return ['ok' => false, 'error' => 'Invalid username or password.'];
    }

    if (!adminVerifyPassword($password, $adminCredentials)) {
        adminRecordLoginFailure($pdo, $username, $ip);
        return ['ok' => false, 'error' => 'Invalid username or password.'];
    }

    if (!adminIsPasswordHashed((string) ($adminCredentials['password'] ?? ''))) {
        adminSetPassword($password, $maintenanceSettings, $siteSettings, $adminCredentials);
    }

    adminClearLoginFailures($pdo, $ip);
    adminEstablishSession();

    return ['ok' => true];
}
