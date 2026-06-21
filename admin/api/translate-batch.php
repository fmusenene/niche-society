<?php
/**
 * AJAX: translate multiple texts between English and Arabic in one request.
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

$texts = $payload['texts'] ?? [];
if (!is_array($texts)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'texts must be an array']);
    exit;
}

if (count($texts) > 80) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Too many texts (max 80).']);
    exit;
}

$from = strtolower(trim((string) ($payload['from'] ?? 'en')));
$to = strtolower(trim((string) ($payload['to'] ?? ($payload['target'] ?? 'ar'))));
if (!in_array($from, ['en', 'ar'], true)) {
    $from = 'en';
}
if (!in_array($to, ['en', 'ar'], true)) {
    $to = $from === 'en' ? 'ar' : 'en';
}

$normalized = [];
foreach ($texts as $text) {
    $text = trim((string) $text);
    if (mb_strlen($text) > 8000) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'One or more texts exceed 8000 characters.']);
        exit;
    }
    $normalized[] = $text;
}

if ($normalized === []) {
    echo json_encode(['ok' => true, 'translations' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $translations = cmsTranslateMany($normalized, $from, $to);
    echo json_encode(['ok' => true, 'translations' => $translations], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
