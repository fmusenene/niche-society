<?php
/**
 * Professional printable invoice document
 */

function adminInvoiceCompanyInfo(PDO $pdo): array
{
    $contact = array_merge(
        cmsGetSettingsByCategory($pdo, 'contact'),
        cmsGetSettingsByCategory($pdo, 'company')
    );

    return [
        'name' => $contact['site_name_en'] ?? (defined('SITE_NAME') ? SITE_NAME : 'Niche Society'),
        'email' => $contact['site_email'] ?? (defined('CONTACT_EMAIL') ? CONTACT_EMAIL : ''),
        'phone' => $contact['site_phone'] ?? (defined('CONTACT_PHONE') ? CONTACT_PHONE : ''),
        'address' => $contact['site_address_en'] ?? (defined('CONTACT_ADDRESS_EN') ? CONTACT_ADDRESS_EN : ''),
        'website' => 'niche-society.com',
        'logo' => url('assets/images/logo.png'),
    ];
}

function adminInvoiceWatermarkUrl(): string
{
    $relative = 'assets/images/Icon 1.png';
    $root = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
    if (!is_file($root . '/' . $relative)) {
        return '';
    }

    return str_replace(' ', '%20', url($relative));
}

function adminInvoiceSocialDisclaimer(string $lang = 'en'): string
{
    if ($lang === 'ar') {
        return 'يمكن للعميل الموافقة أو عدم الموافقة على نشر محتوى الفعالية (صور، فيديو، وتغطية) على المنصات أدناه. ضع علامة على غير موافق إذا لم تكن نيش سوسايتي تنشر محتوى الفعالية على تلك المنصة دون موافقة خطية منفصلة.';
    }

    return 'The client may authorize or decline publication of event-related content (photos, videos, and coverage) on the platforms below. Tick Not Approved if Niche Society should not publish event-related content on that platform without separate written consent.';
}

/** @return array<string, string> */
function adminInvoicePrintLabels(string $lang = 'en'): array
{
    $en = [
        'docProposal' => 'Technical & Financial Proposal',
        'docInvoice' => 'Invoice',
        'offerDate' => 'Offer Date',
        'invoiceDate' => 'Invoice date',
        'eventDate' => 'Event Date',
        'eventLocation' => 'Event Location',
        'subject' => 'Subject',
        'preparedBy' => 'Prepared by',
        'tel' => 'Tel',
        'billFrom' => 'Bill from',
        'billTo' => 'Bill to',
        'email' => 'Email:',
        'phone' => 'Phone:',
        'invoiceNumber' => 'Invoice number',
        'dueDate' => 'Due date',
        'description' => 'Description',
        'qty' => 'Qty',
        'unitPrice' => 'Unit price',
        'amount' => 'Amount',
        'total' => 'Total',
        'noLineItems' => 'No line items',
        'subtotal' => 'Subtotal',
        'fees' => 'Event management fees (15%)',
        'discount' => 'Discount',
        'amountDue' => 'Amount due',
        'pay1' => 'First payment (30%)',
        'pay2' => 'Second payment (40%)',
        'pay3' => 'Third payment (30%)',
        'paymentsTitle' => 'Method of payments',
        'cancellationTitle' => 'Cancelation Policy',
        'socialTitle' => 'Social Media Coverage Authorization',
        'approved' => 'Approved',
        'notApproved' => 'Not approved',
        'authorization' => 'Authorization',
        'clientSignature' => 'Client signature',
        'date' => 'Date:',
        'notes' => 'Notes',
        'footerThanks' => 'Thank you for your business.',
        'print' => 'Print / Save PDF',
        'close' => 'Close',
        'platformSnapchat' => 'Snapchat',
        'platformInstagram' => 'Instagram',
        'platformFacebook' => 'Facebook',
        'installment1Due' => 'Amount due — 1st payment (30%)',
        'installment2Due' => 'Amount due — 2nd payment (40%)',
        'installment3Due' => 'Amount due — 3rd payment (30%)',
        'contractTotal' => 'Contract total',
        'installmentOf' => 'Installment',
        'installment1Of' => '1 of 3 — First payment (30%)',
        'installment2Of' => '2 of 3 — Second payment (40%)',
        'installment3Of' => '3 of 3 — Third payment (30%)',
        'installmentFullDue' => 'Amount due — Full payment',
        'installmentFullOf' => 'Full payment (100%)',
    ];

    $ar = [
        'docProposal' => 'العرض الفني والمالي',
        'docInvoice' => 'فاتورة',
        'offerDate' => 'تاريخ العرض',
        'invoiceDate' => 'تاريخ الفاتورة',
        'eventDate' => 'تاريخ الفعالية',
        'eventLocation' => 'موقع الفعالية',
        'subject' => 'الموضوع',
        'preparedBy' => 'إعداد',
        'tel' => 'هاتف',
        'billFrom' => 'من',
        'billTo' => 'إلى',
        'email' => 'البريد:',
        'phone' => 'الهاتف:',
        'invoiceNumber' => 'رقم الفاتورة',
        'dueDate' => 'تاريخ الاستحقاق',
        'description' => 'التفاصيل',
        'qty' => 'الكمية',
        'unitPrice' => 'سعر الوحدة',
        'amount' => 'المبلغ',
        'total' => 'الإجمالي',
        'noLineItems' => 'لا توجد بنود',
        'subtotal' => 'إجمالي الخدمات',
        'fees' => 'رسوم إدارة الفعالية (15%)',
        'discount' => 'الخصم',
        'amountDue' => 'المبلغ المستحق',
        'pay1' => 'الدفعة الأولى (30%)',
        'pay2' => 'الدفعة الثانية (40%)',
        'pay3' => 'الدفعة الثالثة (30%)',
        'paymentsTitle' => 'طريقة الدفع',
        'cancellationTitle' => 'سياسة الإلغاء',
        'socialTitle' => 'تفويض النشر عبر وسائل التواصل الاجتماعي',
        'approved' => 'موافق',
        'notApproved' => 'غير موافق',
        'authorization' => 'التفويض',
        'clientSignature' => 'توقيع العميل',
        'date' => 'التاريخ:',
        'notes' => 'ملاحظات',
        'footerThanks' => 'شكرًا لتعاملكم معنا.',
        'print' => 'طباعة / حفظ PDF',
        'close' => 'إغلاق',
        'platformSnapchat' => 'سناب شات',
        'platformInstagram' => 'إنستغرام',
        'platformFacebook' => 'فيسبوك',
        'installment1Due' => 'المبلغ المستحق — الدفعة الأولى (30%)',
        'installment2Due' => 'المبلغ المستحق — الدفعة الثانية (40%)',
        'installment3Due' => 'المبلغ المستحق — الدفعة الثالثة (30%)',
        'contractTotal' => 'إجمالي العقد',
        'installmentOf' => 'الدفعة',
        'installment1Of' => '1 من 3 — الدفعة الأولى (30%)',
        'installment2Of' => '2 من 3 — الدفعة الثانية (40%)',
        'installment3Of' => '3 من 3 — الدفعة الثالثة (30%)',
        'installmentFullDue' => 'المبلغ المستحق — الدفع الكامل',
        'installmentFullOf' => 'دفع كامل (100%)',
    ];

    return $lang === 'ar' ? $ar : $en;
}

function adminRenderInvoicePrint(PDO $pdo, int $invoiceId, bool $autoPrint = false, int $installment = 0, bool $fullPayment = false): string
{
    $invoice = cmsGetInvoice($pdo, $invoiceId);
    if (!$invoice) {
        return '<!DOCTYPE html><html><body><p>Invoice not found.</p></body></html>';
    }

    $company = adminInvoiceCompanyInfo($pdo);
    $watermarkUrl = adminInvoiceWatermarkUrl();
    $state = $invoice['state'];
    $fields = $state['fields'];
    $lang = ($fields['language'] ?? 'en') === 'ar' ? 'ar' : 'en';
    $L = adminInvoicePrintLabels($lang);
    $dir = $lang === 'ar' ? 'rtl' : 'ltr';
    $currency = $fields['currency'] ?? 'SAR';
    $discountPct = (float) ($fields['discount'] ?? 0);
    $totals = cmsInvoiceComputeBreakdown($state['categories'], $discountPct);
    $installment = max(0, min(3, $installment));

    $h = static fn (?string $v): string => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
    $money = static fn (int $n): string => htmlspecialchars(cmsInvoiceFormatMoney($n, $currency), ENT_QUOTES, 'UTF-8');

    $lineRows = '';
    foreach ($state['categories'] as $cat) {
        $rowNum = 0;
        $catSubtotal = 0;
        $catQty = 0;
        $catHasRows = false;
        $catName = trim($cat['name'] ?? '');
        if ($catName !== '') {
            $lineRows .= '<tr class="cat-row"><td colspan="5">' . $h($catName) . '</td></tr>';
        }
        foreach ($cat['items'] ?? [] as $item) {
            $rowNum++;
            $qty = max(0, (int) ($item['quantity'] ?? 0));
            $unit = max(0, (int) ($item['price'] ?? 0));
            $amount = $qty * $unit;
            $desc = trim($item['description'] ?? '');
            $details = $item['details'] ?? [];
            if (!is_array($details)) {
                $details = [];
            }
            $descHtml = '';
            if ($desc !== '') {
                $descHtml .= $h($desc);
            }
            foreach ($details as $detailLine) {
                $detailLine = trim((string) $detailLine);
                if ($detailLine === '') {
                    continue;
                }
                $descHtml .= ($descHtml !== '' ? '<br>' : '') . '<span class="desc-detail">' . $h($detailLine) . '</span>';
            }
            if ($desc === '' && $descHtml === '' && $qty === 0 && $unit === 0) {
                continue;
            }
            $catHasRows = true;
            $catSubtotal += $amount;
            $catQty += $qty;
            $lineRows .= '<tr>'
                . '<td class="num">' . $rowNum . '</td>'
                . '<td class="desc">' . ($descHtml !== '' ? $descHtml : '—') . '</td>'
                . '<td class="qty">' . $qty . '</td>'
                . '<td class="unit">' . $money($unit) . '</td>'
                . '<td class="amount">' . $money($amount) . '</td>'
                . '</tr>';
        }
        if ($catName !== '' || $catHasRows) {
            $lineRows .= '<tr class="cat-total-row">'
                . '<td colspan="2" class="cat-total-label">' . $h($L['total']) . '</td>'
                . '<td class="qty">' . $catQty . '</td>'
                . '<td></td>'
                . '<td class="amount">' . $money($catSubtotal) . '</td>'
                . '</tr>';
        }
    }
    if ($lineRows === '') {
        $lineRows = '<tr><td colspan="5" class="empty">' . $h($L['noLineItems']) . '</td></tr>';
    }

    $paymentTerms = $fields['paymentTerms'] ?? '';
    $paymentTermsHtml = '';
    if ($paymentTerms !== '') {
        foreach (preg_split('/\r\n|\r|\n/', $paymentTerms) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $paymentTermsHtml .= '<li>' . $h($line) . '</li>';
            }
        }
    }

    $clientName = trim($fields['clientName'] ?? $invoice['client_name'] ?? '');
    if ($clientName === '') {
        $clientName = trim($fields['subject'] ?? $invoice['subject'] ?? '');
    }
    $clientAddress = trim($fields['clientAddress'] ?? $invoice['client_address'] ?? '');
    $clientEmail = trim($fields['clientEmail'] ?? $invoice['client_email'] ?? '');
    $clientPhone = trim($fields['clientPhone'] ?? $invoice['client_phone'] ?? '');
    $hasBillTo = $clientName !== '' || $clientAddress !== '' || $clientEmail !== '' || $clientPhone !== '';

    $notes = trim($fields['notes'] ?? '');

    $isProposal = cmsInvoiceIsProposal($invoice);
    if ($isProposal) {
        $installment = 0;
        $fullPayment = false;
    }
    $installmentDue = ($installment >= 1 && $installment <= 3) ? (int) $totals['pay' . $installment] : 0;
    $amountDueLabel = $L['amountDue'];
    $amountDueValue = $totals['grand'];
    $installmentMeta = '';
    if ($fullPayment) {
        $amountDueLabel = $L['installmentFullDue'];
        $amountDueValue = $totals['grand'];
        $installmentMeta = $L['installmentFullOf'];
    } elseif ($installmentDue > 0) {
        $amountDueLabel = $L['installment' . $installment . 'Due'];
        $amountDueValue = $installmentDue;
        $installmentMeta = $L['installment' . $installment . 'Of'];
    }
    $docTitle = $isProposal ? $L['docProposal'] : $L['docInvoice'];
    $invoiceNumber = trim((string) ($invoice['invoice_number'] ?? ''));
    $offerDate = cmsInvoiceFormatDisplayDate($fields['offerDate'] ?: ($invoice['offer_date'] ?? ''), true);
    $dueDate = cmsInvoiceFormatDisplayDate($fields['dueDate'] ?: ($invoice['due_date'] ?? ''), true);
    $eventDate = cmsInvoiceFormatDisplayDate($fields['eventDate'] ?? '', true);
    $location = trim($fields['location'] ?? '') ?: '—';
    $prepared = trim($fields['prepared'] ?? '') ?: '—';
    $subject = trim($fields['subject'] ?? $invoice['subject'] ?? '') ?: '—';
    $proposalTel = $clientPhone !== '' ? $clientPhone : trim($company['phone']);
    $proposalTelDisplay = $proposalTel !== '' ? $proposalTel : '—';
    $autoPrintJs = $autoPrint ? 'window.addEventListener("load",function(){window.print();});' : '';

    $proposalDefaults = cmsInvoiceDefaultProposalFields($lang);
    $intro1 = trim($fields['intro1'] ?? $proposalDefaults['intro1']);
    $intro2 = trim($fields['intro2'] ?? $proposalDefaults['intro2']);
    $intro3 = trim($fields['intro3'] ?? $proposalDefaults['intro3']);
    $closing1 = trim($fields['closing1'] ?? $proposalDefaults['closing1']);
    $closing2 = trim($fields['closing2'] ?? $proposalDefaults['closing2']);
    $closing3 = trim($fields['closing3'] ?? $proposalDefaults['closing3']);
    $closingRegards = trim($fields['closingRegards'] ?? $proposalDefaults['closingRegards']);
    $socialIntro = trim($fields['socialIntro'] ?? '');
    if ($socialIntro === '') {
        $socialIntro = adminInvoiceSocialDisclaimer($lang);
    }
    $cancellationText = trim($fields['cancellationPolicy'] ?? $proposalDefaults['cancellationPolicy']);

    $cancellationHtml = '';
    if ($cancellationText !== '') {
        foreach (preg_split('/\r\n|\r|\n/', $cancellationText) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $cancellationHtml .= '<li>' . $h($line) . '</li>';
            }
        }
    }

    $socialPlatforms = [
        $L['platformSnapchat'],
        $L['platformInstagram'],
        $L['platformFacebook'],
    ];
    $socialRowsHtml = '';
    foreach ($socialPlatforms as $platformName) {
        $socialRowsHtml .= '<div class="social-row">'
            . '<span class="social-platform">' . $h($platformName) . '</span>'
            . '<span class="social-choice">'
            . '<span class="social-checkbox" aria-hidden="true"></span>'
            . '<span>' . $h($L['approved']) . '</span>'
            . '</span>'
            . '<span class="social-choice">'
            . '<span class="social-checkbox" aria-hidden="true"></span>'
            . '<span>' . $h($L['notApproved']) . '</span>'
            . '</span>'
            . '</div>';
    }

    ob_start();
    ?>
<!DOCTYPE html>
<html lang="<?= $h($lang) ?>" dir="<?= $h($dir) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $h($isProposal ? 'Proposal' : 'Invoice') ?><?= $installmentDue > 0 ? ' — ' . $h($installmentMeta) : '' ?> <?= $h($invoiceNumber) ?> — <?= $h($company['name']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Domine:wght@500;600;700&family=Arimo:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --burgundy: #602234;
      --cream: #fff6e7;
      --border: #e2d6c8;
      --muted: #6b4f58;
    }
    @page { size: A4; margin: 14mm; }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      padding: 24px;
      font-family: "Arimo", system-ui, sans-serif;
      font-size: 10pt;
      color: var(--burgundy);
      background: #ece8e4;
      line-height: 1.45;
    }
    html[dir="rtl"] body { text-align: right; }
    html[dir="rtl"] .invoice-top { flex-direction: row-reverse; }
    html[dir="rtl"] .invoice-top--proposal .header-url { direction: ltr; }
    html[dir="rtl"] .proposal-meta-grid,
    html[dir="rtl"] .meta-grid,
    html[dir="rtl"] .invoice-facts,
    html[dir="rtl"] .payments-box,
    html[dir="rtl"] .footer-grid { direction: rtl; }
    html[dir="rtl"] .proposal-field { flex-direction: row-reverse; }
    html[dir="rtl"] table.items th,
    html[dir="rtl"] table.items td.desc { text-align: right; }
    html[dir="rtl"] table.items th.qty,
    html[dir="rtl"] table.items th.unit,
    html[dir="rtl"] table.items th.amount,
    html[dir="rtl"] table.items td.qty,
    html[dir="rtl"] table.items td.unit,
    html[dir="rtl"] table.items td.amount { text-align: left; }
    html[dir="rtl"] table.items tr.cat-total-row td.cat-total-label { text-align: left; }
    html[dir="rtl"] .summary-wrap { justify-content: flex-start; }
    html[dir="rtl"] .summary-row { flex-direction: row-reverse; }
    html[dir="rtl"] .intro-block,
    html[dir="rtl"] .terms ul { border-left: 0; border-right: 3px solid var(--burgundy); padding-right: 18px; padding-left: 0; }
    html[dir="rtl"] .proposal-meta-grid { border-left: 0; border-right: 3px solid var(--burgundy); }
    html[dir="rtl"] .social-row { flex-direction: row-reverse; }
    .invoice-page {
      max-width: 210mm;
      margin: 0 auto;
      background: #fff;
      box-shadow: 0 8px 28px rgba(0,0,0,.1);
      position: relative;
    }
    .doc-watermark {
      position: fixed;
      top: 52%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 68%;
      max-width: 5.25in;
      z-index: 9999;
      pointer-events: none;
    }
    .doc-watermark img {
      width: 100%;
      height: auto;
      display: block;
      opacity: 0.11;
      mix-blend-mode: screen;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    .invoice-top,
    .invoice-body {
      position: relative;
      z-index: 1;
    }
    .invoice-top {
      background: var(--burgundy);
      color: var(--cream);
      padding: 22px 28px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
    }
    .invoice-top img { height: 52px; width: auto; }
    .invoice-top--proposal {
      height: 1.45in;
      min-height: 140px;
      padding: 10px 32px;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    .invoice-top--proposal img { height: 66px; max-width: 340px; }
    .invoice-top--proposal .header-url {
      font-family: "Domine", Georgia, serif;
      font-size: 12pt;
      font-weight: 500;
      letter-spacing: .04em;
      color: var(--cream);
      white-space: nowrap;
    }
    .invoice-top .brand h1 {
      margin: 0;
      font-family: "Domine", Georgia, serif;
      font-size: 18pt;
      font-weight: 600;
    }
    .invoice-top .brand p { margin: 4px 0 0; opacity: .9; font-size: 9pt; }
    .invoice-top .doc-type {
      text-align: right;
      font-size: 11pt;
      opacity: .95;
      letter-spacing: .02em;
    }
    .invoice-top .doc-type .doc-title {
      display: block;
      font-family: "Domine", Georgia, serif;
      font-size: 13pt;
      font-weight: 600;
      letter-spacing: .04em;
      text-transform: uppercase;
      margin-bottom: 4px;
      opacity: 1;
    }
    .invoice-body { padding: 24px 28px 28px; }
    .invoice-body--proposal { padding: 22px 28px 28px; }
    .proposal-doc-title {
      margin: 0 0 16px;
      font-family: "Domine", Georgia, serif;
      font-size: 17pt;
      font-weight: 600;
      color: var(--burgundy);
      text-align: center;
      letter-spacing: .06em;
      text-transform: uppercase;
    }
    .proposal-meta-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px 24px;
      margin-bottom: 16px;
      padding: 12px 14px;
      background: #fdfbf8;
      border: 1px solid var(--border);
      border-left: 3px solid var(--burgundy);
    }
    .proposal-field {
      display: flex;
      gap: 8px;
      align-items: baseline;
      min-width: 0;
    }
    .proposal-label {
      font-family: "Domine", Georgia, serif;
      font-size: 7.5pt;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: var(--burgundy);
      min-width: 88px;
      flex-shrink: 0;
    }
    .proposal-value {
      flex: 1;
      font-size: 8.5pt;
      color: var(--burgundy);
      word-break: break-word;
    }
    .proposal-intro {
      margin: 0 0 20px;
    }
    .proposal-intro p {
      margin: 0 0 6px;
      font-size: 8.5pt;
      color: var(--burgundy);
      text-align: justify;
    }
    .proposal-intro p:last-child { margin-bottom: 0; }
    .meta-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 22px;
    }
    .meta-box {
      border: 1px solid var(--border);
      border-radius: 6px;
      padding: 14px 16px;
      background: #faf7f3;
    }
    .meta-box h3 {
      margin: 0 0 10px;
      font-family: "Domine", Georgia, serif;
      font-size: 10pt;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: var(--muted);
    }
    .meta-box p { margin: 0 0 6px; }
    .meta-box .muted { color: var(--muted); font-size: 9.5pt; }
    .bill-to-box .bill-to-name { margin-bottom: 8px; }
    .bill-to-box .bill-to-address { white-space: pre-line; }
    .bill-to-box .bill-to-label { font-weight: 600; color: var(--burgundy); }
    .invoice-facts {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 22px;
      padding: 14px 16px;
      background: #faf7f3;
      border: 1px solid var(--border);
      border-radius: 6px;
    }
    .invoice-facts div span {
      display: block;
      font-size: 8pt;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--muted);
      margin-bottom: 2px;
    }
    .invoice-facts div strong { font-size: 10pt; }
    table.items {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 18px;
    }
    table.items th {
      background: var(--burgundy);
      color: var(--cream);
      font-size: 8.5pt;
      text-transform: uppercase;
      letter-spacing: .05em;
      padding: 8px 10px;
      text-align: left;
    }
    table.items th.qty, table.items th.unit, table.items th.amount { text-align: right; }
    table.items td {
      border-bottom: 1px solid var(--border);
      padding: 8px 10px;
      vertical-align: top;
    }
    table.items tr.cat-row td {
      background: #f3ebe3;
      font-weight: 700;
      font-family: "Domine", Georgia, serif;
      border-bottom: 1px solid #d9c8bc;
    }
    table.items tr.cat-total-row td {
      background: #f3ebe3;
      border-bottom: 2px solid #d9c8bc;
      font-weight: 700;
      color: var(--burgundy);
    }
    table.items tr.cat-total-row td.cat-total-label {
      text-align: right;
      font-family: "Domine", Georgia, serif;
      text-transform: uppercase;
      font-size: 8pt;
      letter-spacing: .06em;
    }
    table.items td.desc .desc-detail {
      color: var(--muted);
      font-size: 0.92em;
    }

    table.items td.num { width: 36px; color: var(--muted); }
    table.items td.qty, table.items td.unit, table.items td.amount { text-align: right; white-space: nowrap; }
    table.items td.amount { font-weight: 600; }
    table.items td.empty { text-align: center; color: var(--muted); font-style: italic; }
    .summary-wrap {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 22px;
    }
    .summary {
      width: min(100%, 320px);
      border: 1px solid var(--border);
      border-radius: 6px;
      overflow: hidden;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding: 8px 14px;
      border-bottom: 1px solid var(--border);
    }
    .summary-row:last-child { border-bottom: 0; }
    .summary-row.total {
      background: var(--burgundy);
      color: var(--cream);
      font-size: 11pt;
      font-weight: 700;
    }
    .summary-row.discount { color: #8b1c1c; }
    .payments-box {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      margin-bottom: 22px;
    }
    .payments-box div {
      text-align: center;
      padding: 10px;
      border: 1px solid var(--border);
      border-radius: 6px;
      background: #faf7f3;
      font-size: 9pt;
    }
    .payments-box strong { display: block; font-size: 11pt; margin-top: 4px; }
    .payments-box div.is-active {
      border-color: var(--burgundy);
      background: #f3ebe3;
      box-shadow: inset 0 0 0 1px rgba(96, 34, 52, 0.15);
      font-weight: 600;
    }
    .payments-box div.is-active strong { color: var(--burgundy); }
    .payments-box.is-full-payment div {
      border-color: var(--burgundy);
      background: #f3ebe3;
    }
    .summary-row.contract-total {
      font-size: 9.5pt;
      color: var(--muted);
    }
    .section-title {
      font-family: "Domine", Georgia, serif;
      font-size: 11pt;
      margin: 0 0 8px;
      color: var(--burgundy);
    }
    .intro-block {
      margin: 0 0 20px;
      padding: 14px 16px;
      background: #faf7f3;
      border-left: 3px solid var(--burgundy);
      border-radius: 0 6px 6px 0;
    }
    .intro-block p { margin: 0 0 10px; }
    .intro-block p:last-child { margin-bottom: 0; }
    .prose-block { margin-bottom: 20px; }
    .prose-block p { margin: 0 0 8px; }
    .terms ul, .notes p { margin: 0; padding-left: 18px; color: var(--muted); }
    .notes p { padding-left: 0; }
    .footer-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      margin-top: 24px;
      padding-top: 18px;
      border-top: 1px solid var(--border);
    }
    .signature-line {
      margin-top: 36px;
      border-top: 1px solid var(--burgundy);
      padding-top: 6px;
      font-size: 9pt;
      color: var(--muted);
    }
    .social-block {
      margin-top: 20px;
      padding: 14px 16px;
      border: 1px solid var(--border);
      border-radius: 6px;
      background: #faf7f3;
    }
    .social-block .social-intro { margin: 0 0 12px; color: var(--muted); font-size: 9.5pt; }
    .social-row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 12px 24px;
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
    }
    .social-row:last-child { border-bottom: 0; }
    .social-platform { font-weight: 600; min-width: 100px; }
    .social-choice {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 9.5pt;
      color: var(--burgundy);
    }
    .social-checkbox {
      display: inline-block;
      width: 15px;
      height: 15px;
      border: 1.5px solid var(--burgundy);
      border-radius: 2px;
      background: #fff;
      flex-shrink: 0;
      box-sizing: border-box;
    }
    .closing-block { margin-bottom: 20px; }
    .closing-block .regards { margin-top: 12px; font-weight: 600; }
    .closing-block .prepared-name { margin-top: 4px; font-style: italic; }
    .toolbar {
      max-width: 210mm;
      margin: 0 auto 12px;
      display: flex;
      gap: 8px;
      justify-content: flex-end;
    }
    .toolbar button {
      font: inherit;
      padding: 8px 16px;
      border: 1px solid var(--border);
      border-radius: 4px;
      background: #fff;
      cursor: pointer;
      color: var(--burgundy);
    }
    .toolbar button.primary { background: var(--burgundy); color: var(--cream); border-color: var(--burgundy); }
    @media print {
      body { background: #fff; padding: 0; }
      .invoice-page { box-shadow: none; }
      .toolbar { display: none !important; }
      .doc-watermark {
        position: fixed;
        top: 52%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 68%;
        max-width: 5.25in;
        z-index: 9999;
        pointer-events: none;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .doc-watermark img {
        opacity: 0.11 !important;
        mix-blend-mode: screen !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .social-checkbox {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        border-color: #602234 !important;
      }
    }
  </style>
</head>
<body>
  <div class="toolbar no-print">
    <button type="button" onclick="window.print()"><?= $h($L['print']) ?></button>
    <button type="button" class="primary" onclick="window.close()"><?= $h($L['close']) ?></button>
  </div>

  <div class="invoice-page">
    <header class="invoice-top<?= $isProposal ? ' invoice-top--proposal' : '' ?>">
      <div class="brand">
        <img src="<?= $h($company['logo']) ?>" alt="<?= $h($company['name']) ?>">
      </div>
      <?php if ($isProposal): ?>
      <span class="header-url"><?= $h($company['website']) ?></span>
      <?php else: ?>
      <div class="doc-type">
        <span class="doc-title"><?= $h($docTitle) ?></span>
        <span><?= $h($company['website']) ?></span>
      </div>
      <?php endif; ?>
    </header>

    <div class="invoice-body<?= $isProposal ? ' invoice-body--proposal' : '' ?>">
      <?php if ($isProposal): ?>
      <h1 class="proposal-doc-title"><?= $h($docTitle) ?></h1>

      <div class="proposal-meta-grid">
        <div class="proposal-field">
          <span class="proposal-label"><?= $h($L['offerDate']) ?></span>
          <span class="proposal-value"><?= $h($offerDate) ?></span>
        </div>
        <div class="proposal-field">
          <span class="proposal-label"><?= $h($L['eventDate']) ?></span>
          <span class="proposal-value"><?= $h($eventDate) ?></span>
        </div>
        <div class="proposal-field">
          <span class="proposal-label"><?= $h($L['eventLocation']) ?></span>
          <span class="proposal-value"><?= $h($location) ?></span>
        </div>
        <div class="proposal-field">
          <span class="proposal-label"><?= $h($L['subject']) ?></span>
          <span class="proposal-value"><?= $h($subject) ?></span>
        </div>
        <div class="proposal-field">
          <span class="proposal-label"><?= $h($L['preparedBy']) ?></span>
          <span class="proposal-value"><?= $h($prepared) ?></span>
        </div>
        <div class="proposal-field">
          <span class="proposal-label"><?= $h($L['tel']) ?></span>
          <span class="proposal-value"><?= $h($proposalTelDisplay) ?></span>
        </div>
      </div>

      <?php if ($intro1 || $intro2 || $intro3): ?>
      <div class="proposal-intro">
        <?php if ($intro1): ?><p><?= $h($intro1) ?></p><?php endif; ?>
        <?php if ($intro2): ?><p><?= $h($intro2) ?></p><?php endif; ?>
        <?php if ($intro3): ?><p><?= $h($intro3) ?></p><?php endif; ?>
      </div>
      <?php endif; ?>

      <?php else: ?>

      <div class="meta-grid">
        <div class="meta-box">
          <h3><?= $h($L['billFrom']) ?></h3>
          <p><strong><?= $h($company['name']) ?></strong></p>
          <p class="muted"><?= $h($company['address']) ?></p>
          <p class="muted"><?= $h($company['phone']) ?></p>
          <p class="muted"><?= $h($company['email']) ?></p>
        </div>
        <div class="meta-box bill-to-box">
          <h3><?= $h($L['billTo']) ?></h3>
          <?php if (!$hasBillTo): ?>
          <p class="muted">—</p>
          <?php else: ?>
          <?php if ($clientName !== ''): ?>
          <p class="bill-to-name"><strong><?= $h($clientName) ?></strong></p>
          <?php endif; ?>
          <?php if ($clientAddress !== ''): ?>
          <p class="muted bill-to-address"><?= nl2br($h($clientAddress)) ?></p>
          <?php endif; ?>
          <?php if ($clientEmail !== ''): ?>
          <p class="muted"><span class="bill-to-label"><?= $h($L['email']) ?></span> <?= $h($clientEmail) ?></p>
          <?php endif; ?>
          <?php if ($clientPhone !== ''): ?>
          <p class="muted"><span class="bill-to-label"><?= $h($L['phone']) ?></span> <?= $h($clientPhone) ?></p>
          <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="invoice-facts">
        <div><span><?= $h($L['invoiceNumber']) ?></span><strong><?= $h($invoiceNumber !== '' ? $invoiceNumber : '—') ?></strong></div>
        <?php if ($installmentMeta !== ''): ?>
        <div><span><?= $h($L['installmentOf']) ?></span><strong><?= $h($installmentMeta) ?></strong></div>
        <?php endif; ?>
        <div><span><?= $h($L['invoiceDate']) ?></span><strong><?= $h($offerDate) ?></strong></div>
        <div><span><?= $h($L['dueDate']) ?></span><strong><?= $h($dueDate) ?></strong></div>
        <div><span><?= $h($L['eventDate']) ?></span><strong><?= $h($eventDate) ?></strong></div>
        <div><span><?= $h($L['eventLocation']) ?></span><strong><?= $h($location) ?></strong></div>
        <div><span><?= $h($L['preparedBy']) ?></span><strong><?= $h($prepared) ?></strong></div>
      </div>

      <?php endif; ?>

      <table class="items">
        <thead>
          <tr>
            <th class="num">#</th>
            <th><?= $h($L['description']) ?></th>
            <th class="qty"><?= $h($L['qty']) ?></th>
            <th class="unit"><?= $h($L['unitPrice']) ?></th>
            <th class="amount"><?= $h($L['amount']) ?></th>
          </tr>
        </thead>
        <tbody><?= $lineRows ?></tbody>
      </table>

      <div class="summary-wrap">
        <div class="summary">
          <div class="summary-row"><span><?= $h($L['subtotal']) ?></span><strong><?= $money($totals['subtotal']) ?></strong></div>
          <div class="summary-row"><span><?= $h($L['fees']) ?></span><strong><?= $money($totals['fees']) ?></strong></div>
          <?php if ($totals['discount'] > 0): ?>
          <div class="summary-row discount"><span><?= $h($L['discount']) ?> (<?= (int) $discountPct ?>%)</span><strong>-<?= $money($totals['discount']) ?></strong></div>
          <?php endif; ?>
          <?php if ($installmentDue > 0): ?>
          <div class="summary-row contract-total"><span><?= $h($L['contractTotal']) ?></span><strong><?= $money($totals['grand']) ?></strong></div>
          <?php endif; ?>
          <div class="summary-row total"><span><?= $h($amountDueLabel) ?></span><strong><?= $money($amountDueValue) ?></strong></div>
        </div>
      </div>

      <div class="payments-box<?= $fullPayment ? ' is-full-payment' : '' ?>">
        <div class="<?= $installment === 1 ? 'is-active' : '' ?>"><?= $h($L['pay1']) ?><strong><?= $money($totals['pay1']) ?></strong></div>
        <div class="<?= $installment === 2 ? 'is-active' : '' ?>"><?= $h($L['pay2']) ?><strong><?= $money($totals['pay2']) ?></strong></div>
        <div class="<?= $installment === 3 ? 'is-active' : '' ?>"><?= $h($L['pay3']) ?><strong><?= $money($totals['pay3']) ?></strong></div>
      </div>

      <?php if ($isProposal && $paymentTermsHtml !== ''): ?>
      <div class="terms prose-block">
        <h4 class="section-title"><?= $h($L['paymentsTitle']) ?></h4>
        <ul><?= $paymentTermsHtml ?></ul>
      </div>
      <?php endif; ?>

      <?php if ($isProposal && $cancellationHtml !== ''): ?>
      <div class="terms prose-block">
        <h4 class="section-title"><?= $h($L['cancellationTitle']) ?></h4>
        <ul><?= $cancellationHtml ?></ul>
      </div>
      <?php endif; ?>

      <?php if ($isProposal && ($closing1 || $closing2 || $closing3)): ?>
      <div class="closing-block prose-block">
        <?php if ($closing1): ?><p><?= $h($closing1) ?></p><?php endif; ?>
        <?php if ($closing2): ?><p><?= $h($closing2) ?></p><?php endif; ?>
        <?php if ($closing3): ?><p><?= $h($closing3) ?></p><?php endif; ?>
        <?php if ($closingRegards): ?><p class="regards"><?= $h($closingRegards) ?></p><?php endif; ?>
        <?php if (trim($fields['prepared'] ?? '') !== ''): ?>
        <p class="prepared-name"><?= $h(trim($fields['prepared'])) ?></p>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if ($isProposal): ?>
      <div class="social-block">
        <h4 class="section-title"><?= $h($L['socialTitle']) ?></h4>
        <p class="social-intro"><?= $h($socialIntro) ?></p>
        <?= $socialRowsHtml ?>
      </div>
      <?php endif; ?>

      <div class="footer-grid">
        <div></div>
        <div>
          <h4 class="section-title"><?= $h($L['authorization']) ?></h4>
          <p class="muted" style="margin:0 0 8px;"><?= $h($L['clientSignature']) ?></p>
          <div class="signature-line">
            &nbsp;
            <?php if (!empty($fields['signatureDate'])): ?>
            <br><span><?= $h($L['date']) ?> <?= $h(cmsInvoiceFormatDisplayDate($fields['signatureDate'])) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <?php if ($notes !== ''): ?>
      <div class="notes" style="margin-top:18px;">
        <h4 class="section-title"><?= $h($L['notes']) ?></h4>
        <p><?= nl2br($h($notes)) ?></p>
      </div>
      <?php endif; ?>

      <p class="muted" style="margin-top:24px;font-size:8.5pt;text-align:center;">
        <?= $h($L['footerThanks']) ?> <?= $h($company['name']) ?> · <?= $h($company['website']) ?>
      </p>
    </div>
  </div>
  <?php if ($watermarkUrl !== ''): ?>
  <div class="doc-watermark" aria-hidden="true">
    <img src="<?= $h($watermarkUrl) ?>" alt="">
  </div>
  <?php endif; ?>
  <script><?= $autoPrintJs ?></script>
</body>
</html>
    <?php
    return (string) ob_get_clean();
}
