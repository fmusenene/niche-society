<?php
/**
 * AJAX: translate text between English and Arabic
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../functions/translate.php';

if (!adminIsAuthenticated()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST required']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload)) {
    $payload = $_POST;
}

$text = trim((string) ($payload['text'] ?? ''));
if ($text === '') {
    echo json_encode(['ok' => true, 'translated' => '']);
    exit;
}

$from = strtolower(trim((string) ($payload['from'] ?? 'en')));
$to = strtolower(trim((string) ($payload['to'] ?? ($payload['target'] ?? 'ar'))));
if (!in_array($from, ['en', 'ar', 'auto'], true)) {
    $from = 'auto';
}
if (!in_array($to, ['en', 'ar'], true)) {
    $to = $from === 'en' ? 'ar' : 'en';
}

if (mb_strlen($text) > 8000) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Text too long (max 8000 characters).']);
    exit;
}

try {
    $translated = cmsTranslateBetween($text, $from, $to);
    echo json_encode(['ok' => true, 'translated' => $translated], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
