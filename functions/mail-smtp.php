<?php
/**
 * Simple SMTP mail sender (no external dependencies)
 * Used when config/email.php defines SMTP_ENABLED and SMTP_* constants.
 */

if (!defined('SMTP_ENABLED') || !SMTP_ENABLED) {
    return;
}

/**
 * Send an email via SMTP (TLS/SSL).
 * Returns true on success, false on failure.
 *
 * @param string $to       Recipient email
 * @param string $subject  Subject
 * @param string $bodyHtml HTML body
 * @param string $fromEmail From address
 * @param string $fromName  From name
 * @param string|null $replyTo Reply-To address (optional)
 * @return bool
 */
function sendMailSMTP($to, $subject, $bodyHtml, $fromEmail, $fromName, $replyTo = null) {
    $host = defined('SMTP_HOST') ? SMTP_HOST : '';
    $port = defined('SMTP_PORT') ? (int) SMTP_PORT : 587;
    $secure = defined('SMTP_SECURE') ? strtolower(SMTP_SECURE) : 'tls';
    $user = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
    $pass = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';

    if (empty($host) || empty($user) || empty($pass)) {
        error_log('SMTP send failed: SMTP_HOST, SMTP_USERNAME or SMTP_PASSWORD not set in config/email.php');
        return false;
    }

    $replyTo = $replyTo ?: $fromEmail;
    $subject = trim($subject);
    $to = trim($to);
    $fromEmail = trim($fromEmail);

    if ($port === 465) {
        $context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $sock = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            15,
            STREAM_CLIENT_CONNECT,
            $context
        );
    } else {
        $sock = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            15,
            STREAM_CLIENT_CONNECT
        );
    }

    if (!$sock) {
        error_log("SMTP connection failed: {$errstr} ({$errno})");
        return false;
    }

    $read = function () use ($sock) {
        $line = @fgets($sock, 515);
        return $line !== false ? trim($line) : '';
    };

    $send = function ($cmd) use ($sock) {
        return @fwrite($sock, $cmd . "\r\n") !== false;
    };

    $expect = function ($code) use ($read, $send) {
        $line = $read();
        $ok = (int) substr($line, 0, 3) === (int) $code;
        if (!$ok) {
            error_log("SMTP unexpected: {$line}");
        }
        return $ok;
    };

    if (!$expect(220)) {
        fclose($sock);
        return false;
    }

    $send('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    if (!$expect(250)) {
        fclose($sock);
        return false;
    }

    if ($port === 587 && $secure === 'tls') {
        $send('STARTTLS');
        if (!$expect(220)) {
            fclose($sock);
            return false;
        }
        if (!@stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log('SMTP STARTTLS failed');
            fclose($sock);
            return false;
        }
        $send('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
        if (!$expect(250)) {
            fclose($sock);
            return false;
        }
    }

    $send('AUTH LOGIN');
    if (!$expect(334)) {
        fclose($sock);
        return false;
    }
    $send(base64_encode($user));
    if (!$expect(334)) {
        fclose($sock);
        return false;
    }
    $send(base64_encode($pass));
    if (!$expect(235)) {
        fclose($sock);
        return false;
    }

    $send('MAIL FROM:<' . $fromEmail . '>');
    if (!$expect(250)) {
        fclose($sock);
        return false;
    }
    $send('RCPT TO:<' . $to . '>');
    if (!$expect(250)) {
        fclose($sock);
        return false;
    }
    $send('DATA');
    if (!$expect(354)) {
        fclose($sock);
        return false;
    }

    $messageId = '<' . bin2hex(random_bytes(16)) . '@' . (defined('SMTP_HOST') ? SMTP_HOST : 'localhost') . '>';
    $headers = [
        'Date: ' . date('r'),
        'To: ' . $to,
        'From: ' . ($fromName ? "\"{$fromName}\" <{$fromEmail}>" : $fromEmail),
        'Reply-To: ' . $replyTo,
        'Subject: ' . $subject,
        'Message-ID: ' . $messageId,
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=utf-8',
        'X-Mailer: PHP/' . phpversion(),
    ];
    $data = implode("\r\n", $headers) . "\r\n\r\n" . $bodyHtml . "\r\n.";
    $send($data);
    if (!$expect(250)) {
        fclose($sock);
        return false;
    }

    $send('QUIT');
    fclose($sock);
    return true;
}
