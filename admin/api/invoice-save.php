<?php
/**
 * AJAX: save invoice / proposal state
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../functions/invoices.php';

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
    echo json_encode(['ok' => false, 'error' => 'Invoice id is required']);
    exit;
}

$state = $payload['state'] ?? null;
if (!is_array($state)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invoice state is required']);
    exit;
}

$status = isset($payload['status']) ? trim((string) $payload['status']) : null;
if ($status === '') {
    $status = null;
}

try {
    $existing = cmsGetInvoice($pdo, $id);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Invoice not found']);
        exit;
    }

    cmsSaveInvoice($pdo, $id, $state, $status);
    $updated = cmsGetInvoice($pdo, $id);

    echo json_encode([
        'ok' => true,
        'id' => $id,
        'subject' => $updated['subject'] ?? '',
        'invoice_number' => $updated['invoice_number'] ?? '',
        'grand_total' => (int) ($updated['grand_total'] ?? 0),
        'currency' => $updated['currency'] ?? 'SAR',
        'status' => $updated['status'] ?? 'draft',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
