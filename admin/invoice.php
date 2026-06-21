<?php
/**
 * Printable professional invoice
 */
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../functions/invoices.php';
require_once __DIR__ . '/lib/invoice-print.php';

if (!adminIsAuthenticated()) {
    adminRedirect();
}

$invoiceId = !empty($_GET['id']) ? (int) $_GET['id'] : 0;
$autoPrint = !empty($_GET['print']);
$installmentParam = isset($_GET['installment']) ? trim((string) $_GET['installment']) : '';
$fullPayment = $installmentParam === 'full';
$installment = 0;
if (!$fullPayment && $installmentParam !== '' && ctype_digit($installmentParam)) {
    $installment = (int) $installmentParam;
    if ($installment < 1 || $installment > 3) {
        $installment = 0;
    }
}

if ($invoiceId <= 0) {
    adminFlash('danger', 'Invoice not found.');
    adminRedirect('section=invoices');
}

$invoice = cmsGetInvoice($pdo, $invoiceId);
if (!$invoice) {
    adminFlash('danger', 'Invoice not found.');
    adminRedirect('section=invoices');
}

header('Content-Type: text/html; charset=UTF-8');
echo adminRenderInvoicePrint($pdo, $invoiceId, $autoPrint, $installment, $fullPayment);
