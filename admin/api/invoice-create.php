<?php
/**
 * AJAX: create a new draft invoice
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
    $payload = $_POST;
}

if (!adminVerifyCsrf($payload['admin_csrf'] ?? null)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Security token expired. Refresh the page and try again.']);
    exit;
}

try {
    $id = cmsCreateInvoice($pdo);
    $invoice = cmsGetInvoice($pdo, $id);
    if (!$invoice) {
        throw new RuntimeException('Could not load new invoice.');
    }

    echo json_encode([
        'ok' => true,
        'id' => $id,
        'invoice_number' => $invoice['invoice_number'] ?? '',
        'subject' => $invoice['subject'] ?? '',
        'embed_url' => rtrim(SITE_URL, '/') . '/admin/invoice.php?id=' . $id . '&embed=1',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
