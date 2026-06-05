<?php
/**
 * reCAPTCHA v2 verification helpers
 */

function recaptchaIsEnabled(): bool
{
    return defined('RECAPTCHA_SITE_KEY') && RECAPTCHA_SITE_KEY !== ''
        && defined('RECAPTCHA_SECRET_KEY') && RECAPTCHA_SECRET_KEY !== '';
}

function recaptchaVerify(?string $response, ?string $remoteIp = null): bool
{
    if (!recaptchaIsEnabled()) {
        return true;
    }

    $response = trim((string) $response);
    if ($response === '') {
        return false;
    }

    $payload = http_build_query([
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $response,
        'remoteip' => $remoteIp ?? ($_SERVER['REMOTE_ADDR'] ?? ''),
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 8,
        ],
    ]);

    $result = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    if ($result === false) {
        error_log('reCAPTCHA verification request failed');
        return false;
    }

    $data = json_decode($result, true);
    return !empty($data['success']);
}

function recaptchaRequireOnProduction(): bool
{
    if (recaptchaIsEnabled()) {
        return true;
    }
    return defined('IS_LOCAL') && IS_LOCAL;
}
