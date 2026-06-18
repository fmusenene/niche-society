<?php
/**
 * AJAX: fetch invoice for editing
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../functions/invoices.php';

if (!adminIsAuthenticated()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invoice id is required']);
    exit;
}

$invoice = cmsGetInvoice($pdo, $id);
if (!$invoice) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Invoice not found']);
    exit;
}

echo json_encode([
    'ok' => true,
    'id' => (int) $invoice['id'],
    'invoice_number' => $invoice['invoice_number'] ?? '',
    'status' => $invoice['status'] ?? 'draft',
    'state' => $invoice['state'],
], JSON_UNESCAPED_UNICODE);
