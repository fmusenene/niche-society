<?php
/**
 * AJAX: convert a proposal to an invoice (assigns invoice number)
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

$id = (int) ($payload['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Proposal id is required']);
    exit;
}

try {
    $state = isset($payload['state']) && is_array($payload['state']) ? $payload['state'] : null;
    $proposalId = (int) ($payload['proposal_id'] ?? $payload['id'] ?? 0);
    if ($proposalId <= 0) {
        throw new RuntimeException('Proposal id is required.');
    }

    $hadInvoice = cmsGetLinkedInvoiceForProposal($pdo, $proposalId) !== null;
    $invoice = cmsConvertProposalToInvoice($pdo, $proposalId, $state);

    echo json_encode([
        'ok' => true,
        'proposal_id' => $proposalId,
        'id' => (int) $invoice['id'],
        'invoice_number' => $invoice['invoice_number'] ?? '',
        'record_type' => cmsInvoiceRecordType($invoice),
        'subject' => $invoice['subject'] ?? '',
        'state' => $invoice['state'],
        'created' => !$hadInvoice,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
