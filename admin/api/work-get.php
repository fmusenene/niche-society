<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../functions/work-documents.php';

if (!adminIsAuthenticated()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Document id is required']);
    exit;
}

$doc = cmsGetWorkDocument($pdo, $id);
if (!$doc) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Document not found']);
    exit;
}

echo json_encode(['ok' => true, 'document' => $doc], JSON_UNESCAPED_UNICODE);
