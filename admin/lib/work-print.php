<?php
/**
 * Printable work document — proposal header/footer shell, content only in body
 */

require_once __DIR__ . '/invoice-print.php';
require_once dirname(__DIR__, 2) . '/functions/work-documents.php';

function adminRenderWorkPrint(PDO $pdo, int $workId, bool $autoPrint = false): string
{
    $doc = cmsGetWorkDocument($pdo, $workId);
    if (!$doc) {
        return '<!DOCTYPE html><html><body><p>Document not found.</p></body></html>';
    }

    $company = adminInvoiceCompanyInfo($pdo);
    $watermarkUrl = adminInvoiceWatermarkUrl();
    $lang = ($doc['language'] ?? 'en') === 'ar' ? 'ar' : 'en';
    $dir = $lang === 'ar' ? 'rtl' : 'ltr';
    $L = adminInvoicePrintLabels($lang);

    $h = static fn (?string $v): string => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
    $bodyHtml = cmsWorkBodyToPrintHtml(trim($doc['body'] ?? ''));
    $autoPrintJs = $autoPrint ? 'window.addEventListener("load",function(){window.print();});' : '';

    ob_start();
    ?>
<!DOCTYPE html>
<html lang="<?= $h($lang) ?>" dir="<?= $h($dir) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $h($lang === 'ar' ? 'مستند' : 'Document') ?> — <?= $h($company['name']) ?></title>
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
    .invoice-page {
      max-width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      background: #fff;
      box-shadow: 0 8px 28px rgba(0,0,0,.1);
      position: relative;
      display: flex;
      flex-direction: column;
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
    .invoice-body { position: relative; z-index: 1; }
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
    .invoice-top { flex-shrink: 0; }
    .invoice-body--proposal {
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
      padding: 22px 28px 16px;
    }
    .work-body {
      flex: 1 1 auto;
      margin: 0;
      font-size: 10pt;
      line-height: 1.65;
      color: #2c1a23;
      word-break: break-word;
    }
    .work-body p,
    .work-body div {
      margin: 0 0 10px;
    }
    .work-body p:last-child,
    .work-body div:last-child { margin-bottom: 0; }
    .work-body h1,
    .work-body h2,
    .work-body h3,
    .work-body h4 {
      font-family: "Domine", Georgia, serif;
      color: var(--burgundy);
      margin: 0 0 10px;
      line-height: 1.3;
    }
    .work-body h1 { font-size: 16pt; }
    .work-body h2 { font-size: 13pt; }
    .work-body h3 { font-size: 11.5pt; }
    .work-body h4 { font-size: 10.5pt; }
    .work-body ul,
    .work-body ol {
      margin: 0 0 10px;
      padding-left: 1.4rem;
    }
    html[dir="rtl"] .work-body ul,
    html[dir="rtl"] .work-body ol {
      padding-left: 0;
      padding-right: 1.4rem;
    }
    .work-body li { margin-bottom: 4px; }
    .work-body strong,
    .work-body b { font-weight: 700; }
    .work-body blockquote {
      margin: 0 0 10px;
      padding: 8px 12px;
      border-left: 3px solid var(--burgundy);
      background: #fdfbf8;
    }
    html[dir="rtl"] .work-body blockquote {
      border-left: 0;
      border-right: 3px solid var(--burgundy);
    }
    .work-body-spacer { height: 6px; margin: 0 !important; line-height: 6px; }
    .work-body-empty {
      color: var(--muted);
      font-style: italic;
      text-align: center;
      padding: 24px 0;
    }
    .invoice-page-footer {
      flex-shrink: 0;
      margin-top: auto;
      width: 100%;
      padding: 14px 28px 22px;
      font-size: 8.5pt;
      line-height: 1.4;
      text-align: center;
      color: var(--muted);
      border-top: 1px solid var(--border);
      background: #fff;
      position: relative;
      z-index: 2;
    }
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
      .invoice-page {
        box-shadow: none;
        min-height: 0;
      }
      .invoice-page-footer {
        position: relative;
        margin-top: auto;
        page-break-inside: avoid;
        break-inside: avoid;
      }
      .invoice-body--proposal {
        padding-bottom: 16px;
      }
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
    <header class="invoice-top invoice-top--proposal">
      <div class="brand">
        <img src="<?= $h($company['logo']) ?>" alt="<?= $h($company['name']) ?>">
      </div>
      <span class="header-url"><?= $h($company['website']) ?></span>
    </header>

    <div class="invoice-body invoice-body--proposal">
      <div class="work-body">
        <?php if ($bodyHtml !== ''): ?>
        <?= $bodyHtml ?>
        <?php else: ?>
        <p class="work-body-empty">—</p>
        <?php endif; ?>
      </div>
    </div>

    <footer class="invoice-page-footer">
      <?= $h($L['footerThanks']) ?> <?= $h($company['name']) ?> · <?= $h($company['website']) ?>
    </footer>
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
