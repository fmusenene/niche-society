<?php
/**
 * AJAX: upload service image
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../functions/media.php';

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

if (!adminVerifyCsrf($_POST['admin_csrf'] ?? null)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Security token expired. Refresh the page and try again.']);
    exit;
}

if (empty($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No image file received.']);
    exit;
}

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower(trim($_POST['slug'] ?? '')));

try {
    $result = cmsUploadServiceImage($_FILES['image'], $slug);
    echo json_encode([
        'ok' => true,
        'path' => $result['path'],
        'url' => $result['url'],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
