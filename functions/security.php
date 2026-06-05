<?php
/**
 * Application security helpers
 */

function appEncryptionKey(): string
{
    if (!defined('ENCRYPTION_KEY') || ENCRYPTION_KEY === '' || ENCRYPTION_KEY === 'change-this-to-a-random-32-char-key') {
        throw new RuntimeException('ENCRYPTION_KEY is not configured.');
    }
    return hash('sha256', ENCRYPTION_KEY, true);
}

function appEncrypt(string $plaintext): string
{
    $key = appEncryptionKey();
    $iv = random_bytes(16);
    $tag = '';
    $cipher = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($cipher === false) {
        throw new RuntimeException('Encryption failed.');
    }
    return base64_encode($iv . $tag . $cipher);
}

function appDecrypt(string $payload): string
{
    $raw = base64_decode($payload, true);
    if ($raw === false || strlen($raw) < 33) {
        throw new RuntimeException('Invalid encrypted payload.');
    }
    $key = appEncryptionKey();
    $iv = substr($raw, 0, 16);
    $tag = substr($raw, 16, 16);
    $cipher = substr($raw, 32);
    $plain = openssl_decrypt($cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($plain === false) {
        throw new RuntimeException('Decryption failed.');
    }
    return $plain;
}

function appClientIp(): string
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
