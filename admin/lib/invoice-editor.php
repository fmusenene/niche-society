<?php
/**
 * Render the event proposal template for admin (standalone or iframe embed).
 */

function adminRenderInvoiceEditor(PDO $pdo, int $invoiceId, array $options = []): string
{
    $embed = !empty($options['embed']);
    $invoice = cmsGetInvoice($pdo, $invoiceId);
    if (!$invoice) {
        return '<!DOCTYPE html><html><body><p>Invoice not found.</p></body></html>';
    }

    $templatePath = dirname(__DIR__) . '/Niche_Society_Event_Proposal_Template.html';
    if (!is_readable($templatePath)) {
        return '<!DOCTYPE html><html><body><p>Invoice template file is missing.</p></body></html>';
    }

    $html = file_get_contents($templatePath);
    $adminBase = rtrim(SITE_URL, '/') . '/admin';
    $saveUrl = $adminBase . '/api/invoice-save.php';
    $listUrl = $adminBase . '/index.php?section=invoices';
    $csrf = adminCsrfToken();
    $initialState = $invoice['state'];
    $invoiceNumber = htmlspecialchars($invoice['invoice_number'] ?? '', ENT_QUOTES, 'UTF-8');
    $subject = $invoice['subject'] ?: 'New invoice';

    $configJson = json_encode([
        'id' => $invoiceId,
        'csrf' => $csrf,
        'saveUrl' => $saveUrl,
        'listUrl' => $listUrl,
        'initialState' => $initialState,
        'invoiceNumber' => $invoiceNumber,
        'embed' => $embed,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    if ($embed) {
        $adminToolbar = <<<HTML
<div class="invoice-admin-bar invoice-admin-bar--embed no-print">
  <span class="invoice-admin-number"><strong id="invoice-bar-number">{$invoiceNumber}</strong></span>
  <span id="invoice-save-status" class="invoice-save-status" aria-live="polite"></span>
</div>
<style>
  .invoice-admin-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    background: #f0ebe4;
    border-bottom: 1px solid #e2d6c8;
    font-size: 8.5pt;
  }
  .invoice-admin-bar--embed .invoice-admin-number { color: #602234; font-weight: 600; }
  .invoice-save-status { margin-left: auto; color: #602234; opacity: 0.85; }
  .invoice-save-status.is-pending { opacity: 1; }
  .invoice-save-status.is-success { color: #1f6b3a; }
  .invoice-save-status.is-error { color: #8b1c1c; }
  body.invoice-embed { padding: 0; background: #ece8e4; }
  body.invoice-embed .page { margin: 0 auto; box-shadow: none; }
</style>
HTML;
    } else {
        $adminToolbar = <<<HTML
<div class="invoice-admin-bar no-print">
  <a href="{$listUrl}" class="invoice-admin-back">&larr; All invoices</a>
  <span class="invoice-admin-number">Invoice <strong id="invoice-bar-number">{$invoiceNumber}</strong></span>
  <span id="invoice-save-status" class="invoice-save-status" aria-live="polite"></span>
  <button type="button" class="primary" id="btn-save-invoice">Save to server</button>
</div>
<style>
  .invoice-admin-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    background: #f0ebe4;
    border-bottom: 1px solid #e2d6c8;
    font-size: 8.5pt;
  }
  .invoice-admin-back {
    color: #602234;
    text-decoration: none;
    font-weight: 600;
  }
  .invoice-admin-back:hover { text-decoration: underline; }
  .invoice-admin-number { color: #602234; }
  .invoice-save-status { margin-left: auto; color: #602234; opacity: 0.85; }
  .invoice-save-status.is-pending { opacity: 1; }
  .invoice-save-status.is-success { color: #1f6b3a; }
  .invoice-save-status.is-error { color: #8b1c1c; }
</style>
HTML;
    }

    $toolbarPatch = '<div class="toolbar no-print">';
    $html = str_replace($toolbarPatch, $adminToolbar . $toolbarPatch, $html, $count);
    if ($count === 0) {
        $html = $adminToolbar . $html;
    }

    $invoiceNumberField = <<<HTML
        <div class="field">
          <span class="field-label" id="label-invoice-number">Invoice No.</span>
          <input class="field-input" id="field-invoice-number" type="text" readonly tabindex="-1" value="{$invoiceNumber}" aria-readonly="true">
        </div>

HTML;
    $html = str_replace('<div class="meta-grid">', '<div class="meta-grid">' . $invoiceNumberField, $html, $fieldCount);
    if ($fieldCount === 0) {
        $html = str_replace('<div class="doc-title"', $invoiceNumberField . '<div class="doc-title"', $html);
    }

    $html = preg_replace(
        '/<title>.*?<\/title>/',
        '<title>' . htmlspecialchars($subject) . ' — ' . htmlspecialchars($invoiceNumber) . ' — Niche Society</title>',
        $html,
        1
    );

    if ($embed) {
        $html = str_replace('<body>', '<body class="invoice-embed">', $html);
    }

    $configScript = '<script>window.INVOICE_CONFIG = ' . $configJson . ';</script>';
    $html = str_replace('</head>', $configScript . "\n</head>", $html);

    $html = str_replace(
        'Changes are saved in this browser.',
        'Changes auto-save to the server. Use Print / Save as PDF when ready.',
        $html
    );

    $loadPatch = <<<'JS'
    function applyServerInvoiceState(state) {
      categories = Array.isArray(state.categories) && state.categories.length
        ? normalizeCategories(state.categories)
        : cloneCategories(DEFAULT_CATEGORIES);

      if (state.fields) {
        document.getElementById("field-offer-date").value =
          state.fields.offerDate || state.fields.date || "";
        document.getElementById("field-event-date").value = state.fields.eventDate || "";
        document.getElementById("field-location").value = state.fields.location || "";
        document.getElementById("field-subject").value = state.fields.subject || "";
        document.getElementById("field-prepared").value = state.fields.prepared || "";
        document.getElementById("field-discount").value = state.fields.discount || "0";
        document.getElementById("field-client-name").value = state.fields.clientName || "";
        document.getElementById("field-signature-date").value = state.fields.signatureDate || "";
        setLanguage(state.fields.language || "en");
        const currency = state.fields.currency === "JOD" ? "JOD" : "SAR";
        document.querySelectorAll('input[name="currency"]').forEach(input => {
          input.checked = input.value === currency;
        });
      }
      syncSignature();
    }

    window.getInvoiceEditorState = function () {
      return {
        categories: categories,
        fields: {
          offerDate: document.getElementById("field-offer-date").value,
          eventDate: document.getElementById("field-event-date").value,
          location: document.getElementById("field-location").value,
          subject: document.getElementById("field-subject").value,
          prepared: document.getElementById("field-prepared").value,
          currency: getCurrency(),
          discount: document.getElementById("field-discount").value,
          clientName: document.getElementById("field-client-name").value,
          signatureDate: document.getElementById("field-signature-date").value,
          language: currentLanguage
        }
      };
    };

    if (window.INVOICE_CONFIG && window.INVOICE_CONFIG.invoiceNumber) {
      const numEl = document.getElementById("field-invoice-number");
      const barEl = document.getElementById("invoice-bar-number");
      if (numEl) numEl.value = window.INVOICE_CONFIG.invoiceNumber;
      if (barEl) barEl.textContent = window.INVOICE_CONFIG.invoiceNumber;
    }

    if (window.INVOICE_CONFIG && window.INVOICE_CONFIG.initialState) {
      applyServerInvoiceState(window.INVOICE_CONFIG.initialState);
    } else {
      loadState();
    }
    renderTable();

    const _invoicePersistSaveState = saveState;
    saveState = function () {
      _invoicePersistSaveState();
      if (typeof window.invoiceAdminQueueSave === "function") {
        window.invoiceAdminQueueSave(getInvoiceEditorState(), false);
      }
    };

    if (window.INVOICE_CONFIG && window.INVOICE_CONFIG.embed && window.parent !== window) {
      window.addEventListener("message", function (event) {
        if (!event.data || event.data.type !== "invoice-print") return;
        handlePrint(event);
      });
    }
JS;

    $html = str_replace("    loadState();\n    renderTable();", $loadPatch, $html, $replaceCount);
    if ($replaceCount === 0) {
        return '<!DOCTYPE html><html><body><p>Could not patch invoice template.</p></body></html>';
    }

    $tail = '<script src="assets/invoice-admin.js"></script>' . "\n" . '</body>';
    $html = str_replace('</body>', $tail, $html);

    return $html;
}
