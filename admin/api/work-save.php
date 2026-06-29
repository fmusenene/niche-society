<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../functions/work-documents.php';

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
$payload = json_decode($raw ?: '', true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON body']);
    exit;
}

if (!adminVerifyCsrf($payload['admin_csrf'] ?? null)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Security token expired. Refresh the page and try again.']);
    exit;
}

$id = (int) ($payload['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Document id is required']);
    exit;
}

$data = $payload['document'] ?? null;
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Document data is required']);
    exit;
}

try {
    if (!cmsSaveWorkDocument($pdo, $id, $data)) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Document not found']);
        exit;
    }
    $doc = cmsGetWorkDocument($pdo, $id);
    echo json_encode(['ok' => true, 'id' => $id, 'document' => $doc], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
