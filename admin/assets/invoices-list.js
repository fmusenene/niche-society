(function () {
  const csrfEl = document.getElementById('adminCsrfToken');
  const modalEl = document.getElementById('modalInvoiceForm');
  if (!modalEl || !csrfEl) return;

  const csrf = csrfEl.dataset.token || '';
  const form = document.getElementById('formInvoice');
  const linesBody = document.getElementById('invoiceLinesBody');
  const searchInput = document.getElementById('invoiceSearch');
  const searchClearBtn = document.getElementById('invoiceSearchClear');
  const searchClearEmptyBtn = document.getElementById('invoiceSearchClearEmpty');
  const visibleCountEl = document.getElementById('invoiceVisibleCount');
  const filterStatCard = document.querySelector('.invoice-stat-card--filter');
  const tableBody = document.getElementById('invoiceTableBody');
  const emptyRow = document.getElementById('invoiceSearchEmpty');
  const modal = new bootstrap.Modal(modalEl);
  const MANAGEMENT_FEE = 0.15;

  function notifySuccess(message, options) {
    if (window.AdminNotify) {
      window.AdminNotify.success(message, options);
      return;
    }
    alert(message);
  }

  function notifyError(message, options) {
    if (window.AdminNotify) {
      window.AdminNotify.error(message, options);
      return;
    }
    alert(message);
  }

  function notifyConfirm(options) {
    if (window.AdminNotify) {
      return window.AdminNotify.confirm(options);
    }
    const message = options?.message || 'Are you sure?';
    return Promise.resolve(window.confirm(message));
  }

  const DEFAULT_PAYMENT_TERMS = [
    'First payment: 30% of total amount is required upon signing the contract',
    'Second payment: 40% of the total amount is required before the event',
    'Third payment: 30% of the total amount is required on the day of the event',
  ].join('\n');

  const PROPOSAL_DEFAULTS = {
    intro1: "It's our pleasure for giving us the opportunity to present our proposal regarding the management of your upcoming event.",
    intro2: 'We are writing you to summarize the complete technical and financial proposal that Niche Society has to offer.',
    intro3: 'Thank you for supporting Niche Society to be one of the candidates that you considered as a partner for your upcoming event.',
    cancellationPolicy: [
      'First payment is non-refundable.',
      'No refund of any payment will be done if cancellation will occur less than 60 days before the event.',
      'Photography and video offer have special rates with wedding planning services.',
    ].join('\n'),
    closing1: 'We hope that the above meets your approval and we look forward to receive your confirmation soon,',
    closing2: 'Kindly send us an email indicating your approval and confirmation of these arrangements.',
    closing3: 'However, should you require any further assistance, please feel free to contact us directly at +966 54 694 7915, we will be more than happy to provide you with any assistance you require.',
    closingRegards: 'Best Regards,',
    socialIntro: 'The client may authorize or decline publication of event-related content (photos, videos, and coverage) on the platforms below. Tick Not Approved if Niche Society should not publish event-related content on that platform without separate written consent.',
    socialSnapchat: '',
    socialInstagram: '',
    socialFacebook: '',
  };

  function fieldEl(id) {
    return modalEl.querySelector('#' + id) || document.getElementById(id);
  }

  function readFormField(id, name) {
    const el = fieldEl(id);
    if (el) {
      return String(el.value ?? '').trim();
    }
    if (form && name) {
      const fd = new FormData(form);
      if (fd.has(name)) {
        return String(fd.get(name) ?? '').trim();
      }
    }
    return '';
  }

  const INVOICE_FORM_FIELDS = [
    { key: 'offerDate', id: 'invoiceFormOfferDate' },
    { key: 'dueDate', id: 'invoiceFormDueDate' },
    { key: 'eventDate', id: 'invoiceFormEventDate' },
    { key: 'location', id: 'invoiceFormLocation' },
    { key: 'subject', id: 'invoiceFormSubject' },
    { key: 'prepared', id: 'invoiceFormPrepared' },
    { key: 'discount', id: 'invoiceFormDiscount' },
    { key: 'clientName', id: 'invoiceFormClientName' },
    { key: 'clientAddress', id: 'invoiceFormClientAddress' },
    { key: 'clientEmail', id: 'invoiceFormClientEmail' },
    { key: 'clientPhone', id: 'invoiceFormClientPhone' },
    { key: 'signatureDate', id: 'invoiceFormSignatureDate' },
    { key: 'notes', id: 'invoiceFormNotes' },
  ];

  function staticInvoiceFields() {
    return {
      paymentTerms: DEFAULT_PAYMENT_TERMS,
      intro1: PROPOSAL_DEFAULTS.intro1,
      intro2: PROPOSAL_DEFAULTS.intro2,
      intro3: PROPOSAL_DEFAULTS.intro3,
      cancellationPolicy: PROPOSAL_DEFAULTS.cancellationPolicy,
      closing1: PROPOSAL_DEFAULTS.closing1,
      closing2: PROPOSAL_DEFAULTS.closing2,
      closing3: PROPOSAL_DEFAULTS.closing3,
      closingRegards: PROPOSAL_DEFAULTS.closingRegards,
      socialIntro: PROPOSAL_DEFAULTS.socialIntro,
      socialSnapchat: '',
      socialInstagram: '',
      socialFacebook: '',
    };
  }

  function fieldValue(id) {
    return readFormField(id, '');
  }

  function setFieldValue(id, value) {
    const el = fieldEl(id);
    if (el) el.value = value ?? '';
  }

  const fields = {
    id: document.getElementById('invoiceFormId'),
    recordType: document.getElementById('invoiceFormRecordType'),
    linkedInvoiceId: document.getElementById('invoiceFormLinkedInvoiceId'),
    linkedInvoiceNumber: document.getElementById('invoiceFormLinkedInvoiceNumber'),
    number: document.getElementById('invoiceFormNumber'),
    offerDate: document.getElementById('invoiceFormOfferDate'),
    dueDate: document.getElementById('invoiceFormDueDate'),
    eventDate: document.getElementById('invoiceFormEventDate'),
    location: document.getElementById('invoiceFormLocation'),
    subject: document.getElementById('invoiceFormSubject'),
    prepared: document.getElementById('invoiceFormPrepared'),
    discount: document.getElementById('invoiceFormDiscount'),
    clientName: document.getElementById('invoiceFormClientName'),
    clientAddress: document.getElementById('invoiceFormClientAddress'),
    clientEmail: document.getElementById('invoiceFormClientEmail'),
    clientPhone: document.getElementById('invoiceFormClientPhone'),
    signatureDate: document.getElementById('invoiceFormSignatureDate'),
    notes: document.getElementById('invoiceFormNotes'),
    title: document.getElementById('modalInvoiceFormTitle'),
    error: document.getElementById('invoiceFormError'),
    printBtn: document.getElementById('btnInvoicePrint'),
    printLabel: document.getElementById('btnInvoicePrintLabel'),
    makeInvoiceBtn: document.getElementById('btnInvoiceMakeInvoice'),
    viewLinkedBtn: document.getElementById('btnInvoiceViewLinked'),
    linkedNote: document.getElementById('invoiceProposalLinkedNote'),
    linkedNoteNumber: document.getElementById('invoiceProposalLinkedNumber'),
    saveBtn: document.getElementById('btnInvoiceSave'),
    saveLabel: document.getElementById('btnInvoiceSaveLabel'),
  };

  function todayIso() {
    const d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }

  function isProposalMode() {
    return (fields.recordType?.value || 'proposal') === 'proposal';
  }

  function applyRecordTypeUI() {
    const proposal = isProposalMode();
    const recordId = parseInt(fieldValue('invoiceFormId') || '0', 10);
    const linkedId = parseInt(fieldValue('invoiceFormLinkedInvoiceId') || '0', 10);
    const linkedNumber = fieldValue('invoiceFormLinkedInvoiceNumber') || '';

    modalEl.querySelectorAll('.invoice-only-field').forEach(function (el) {
      el.hidden = proposal;
    });
    const offerLabel = document.getElementById('labelOfferDate');
    if (offerLabel) {
      offerLabel.textContent = proposal ? 'Offer date' : 'Invoice date';
    }
    if (fields.makeInvoiceBtn) {
      fields.makeInvoiceBtn.hidden = !proposal || !recordId || linkedId > 0;
    }
    if (fields.viewLinkedBtn) {
      fields.viewLinkedBtn.hidden = !proposal || linkedId <= 0;
    }
    if (fields.linkedNote) {
      if (proposal && linkedId > 0) {
        fields.linkedNote.classList.remove('d-none');
        if (fields.linkedNoteNumber) {
          fields.linkedNoteNumber.textContent = linkedNumber || ('#' + linkedId);
        }
      } else {
        fields.linkedNote.classList.add('d-none');
      }
    }
    if (fields.saveLabel) {
      fields.saveLabel.textContent = proposal ? 'Save proposal' : 'Save invoice';
    }
    if (fields.printLabel) {
      fields.printLabel.textContent = proposal ? 'Print proposal' : 'Print invoice';
    }
    if (fields.saveBtn && !fields.saveBtn.disabled) {
      fields.saveBtn.innerHTML = '<i class="bi bi-check-lg"></i> <span id="btnInvoiceSaveLabel">' + (proposal ? 'Save proposal' : 'Save invoice') + '</span>';
      fields.saveLabel = document.getElementById('btnInvoiceSaveLabel');
    }
  }

  function currency() {
    const checked = modalEl.querySelector('input[name="invoiceFormCurrency"]:checked');
    return checked && checked.value === 'JOD' ? 'JOD' : 'SAR';
  }

  function formatMoney(n) {
    return Number(n || 0).toLocaleString() + ' ' + currency();
  }

  function setCurrencyLabels() {
    const cur = currency();
    document.querySelectorAll('.invoice-currency-label').forEach(function (el) {
      el.textContent = cur;
    });
    linesBody?.querySelectorAll('.js-item-amount').forEach(function (el) {
      const raw = el.dataset.amount || '0';
      el.textContent = Number(raw).toLocaleString();
    });
  }

  function blankItem() {
    return { description: '', quantity: 1, price: 0 };
  }

  function blankCategory(name) {
    return {
      name: name || 'New category',
      items: [blankItem()],
    };
  }

  function defaultState() {
    return {
      categories: [
        blankCategory('Entertainment'),
        blankCategory('Furniture & decorations'),
      ],
      fields: {
        offerDate: '',
        dueDate: '',
        eventDate: '',
        location: '',
        subject: '',
        prepared: '',
        currency: 'SAR',
        discount: '0',
        clientName: '',
        clientAddress: '',
        clientEmail: '',
        clientPhone: '',
        signatureDate: '',
        paymentTerms: DEFAULT_PAYMENT_TERMS,
        notes: '',
        ...PROPOSAL_DEFAULTS,
        language: 'en',
      },
    };
  }

  function showError(msg) {
    if (!fields.error) return;
    if (!msg) {
      fields.error.classList.add('d-none');
      fields.error.textContent = '';
      return;
    }
    fields.error.textContent = msg;
    fields.error.classList.remove('d-none');
  }

  function escapeHtml(text) {
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function lineAmount(qty, unit) {
    return Math.max(0, Number(qty) || 0) * Math.max(0, Number(unit) || 0);
  }

  /** Convert stored date strings to YYYY-MM-DD for <input type="date">. */
  function toDateInputValue(value) {
    const s = String(value || '').trim();
    if (!s) return '';
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
    let m = s.match(/^(\d{1,2})[-/.](\d{1,2})[-/.](\d{4})$/);
    if (m) {
      return m[3] + '-' + m[2].padStart(2, '0') + '-' + m[1].padStart(2, '0');
    }
    m = s.match(/^(\d{4})[-/.](\d{1,2})[-/.](\d{1,2})$/);
    if (m) {
      return m[1] + '-' + m[2].padStart(2, '0') + '-' + m[3].padStart(2, '0');
    }
    return '';
  }

  function setDateFieldValue(id, value) {
    setFieldValue(id, toDateInputValue(value));
  }

  function normalizeCategoriesList(categories) {
    const list = (categories || []).map(function (cat) {
      const items = (cat.items || []).map(function (item) {
        let description = String(item.description || '').trim();
        const details = Array.isArray(item.details) ? item.details : [];
        const extra = details.map(function (d) { return String(d || '').trim(); }).filter(Boolean);
        if (extra.length) {
          description = description ? description + '\n' + extra.join('\n') : extra.join('\n');
        }
        return {
          description: description,
          quantity: Math.max(0, Number(item.quantity) || 0),
          price: Math.max(0, Number(item.price) || 0),
        };
      });
      if (!items.length) {
        items.push(blankItem());
      }
      return {
        name: String(cat.name || '').trim(),
        items: items,
      };
    });
    return list.length ? list : defaultState().categories;
  }

  function buildItemRowHtml(item) {
    const qty = Math.max(0, Number(item.quantity) || 0);
    const unit = Math.max(0, Number(item.price) || 0);
    const amount = lineAmount(qty, unit);
    return (
      '<tr>' +
      '<td class="col-num"><span class="js-row-num text-muted">—</span></td>' +
      '<td class="col-desc"><input type="text" class="form-control form-control-sm js-item-desc" value="' + escapeHtml(item.description || '') + '" placeholder="Description" aria-label="Description"></td>' +
      '<td class="col-qty"><input type="number" class="form-control form-control-sm js-item-qty text-end" min="0" step="1" value="' + qty + '" aria-label="Quantity"></td>' +
      '<td class="col-unit"><input type="number" class="form-control form-control-sm js-item-price text-end" min="0" step="1" value="' + unit + '" aria-label="Unit price"></td>' +
      '<td class="col-amount text-end"><span class="js-item-amount" data-amount="' + amount + '">' + amount.toLocaleString() + '</span></td>' +
      '<td class="text-end col-actions">' +
      '<button type="button" class="btn btn-sm btn-outline-danger js-delete-item" title="Remove item"><i class="bi bi-trash"></i></button>' +
      '</td></tr>'
    );
  }

  function buildCategoryBlock(cat) {
    const block = document.createElement('div');
    block.className = 'invoice-cat-block';
    const itemsHtml = (cat.items || [blankItem()]).map(buildItemRowHtml).join('');
    block.innerHTML =
      '<div class="invoice-cat-header">' +
      '<input type="text" class="form-control form-control-sm js-cat-name" value="' + escapeHtml(cat.name || '') + '" placeholder="Category name (e.g. Entertainment)" aria-label="Category name">' +
      '<button type="button" class="btn btn-sm btn-outline-danger js-remove-cat" title="Remove category"><i class="bi bi-trash"></i><span class="invoice-cat-remove-label"> Remove</span></button>' +
      '</div>' +
      '<div class="table-responsive">' +
      '<table class="table table-sm invoice-lines-table invoice-cat-items-table mb-0">' +
      '<thead><tr>' +
      '<th class="col-num">#</th>' +
      '<th>Description</th>' +
      '<th class="text-end col-qty">Qty</th>' +
      '<th class="text-end col-unit">Unit price</th>' +
      '<th class="text-end col-amount">Amount</th>' +
      '<th class="col-actions"></th>' +
      '</tr></thead>' +
      '<tbody class="invoice-cat-items">' + itemsHtml + '</tbody>' +
      '</table></div>' +
      '<div class="invoice-cat-footer">' +
      '<button type="button" class="btn btn-outline-secondary btn-sm js-add-item"><i class="bi bi-plus-lg"></i> Add item</button>' +
      '</div>';
    return block;
  }

  function readCategories() {
    if (!linesBody) {
      return defaultState().categories;
    }
    const categories = [];
    linesBody.querySelectorAll('.invoice-cat-block').forEach(function (block) {
      const name = String(block.querySelector('.js-cat-name')?.value ?? '').trim();
      const items = [];
      block.querySelectorAll('.invoice-cat-items tr').forEach(function (row) {
        items.push({
          description: String(row.querySelector('.js-item-desc')?.value ?? '').trim(),
          quantity: Math.max(0, parseInt(row.querySelector('.js-item-qty')?.value || '0', 10) || 0),
          price: Math.max(0, parseInt(row.querySelector('.js-item-price')?.value || '0', 10) || 0),
        });
      });
      categories.push({
        name: name,
        items: items.length ? items : [blankItem()],
      });
    });
    return categories.length ? categories : defaultState().categories;
  }

  function renderCategories(categories) {
    if (!linesBody) return;
    const cats = normalizeCategoriesList(categories);
    linesBody.innerHTML = '';
    cats.forEach(function (cat) {
      linesBody.appendChild(buildCategoryBlock(cat));
    });
    bindCategoryEvents();
    updateRowNumbers();
    updateRowAmounts();
    updateTotals();
  }

  function updateRowNumbers() {
    if (!linesBody) return;
    let num = 0;
    linesBody.querySelectorAll('.invoice-cat-items tr').forEach(function (row) {
      num += 1;
      const el = row.querySelector('.js-row-num');
      if (el) el.textContent = String(num);
    });
  }

  function updateRowAmounts() {
    linesBody?.querySelectorAll('.invoice-cat-items tr').forEach(function (row) {
      const qty = Math.max(0, parseInt(row.querySelector('.js-item-qty')?.value || '0', 10) || 0);
      const unit = Math.max(0, parseInt(row.querySelector('.js-item-price')?.value || '0', 10) || 0);
      const amount = lineAmount(qty, unit);
      const span = row.querySelector('.js-item-amount');
      if (span) {
        span.dataset.amount = String(amount);
        span.textContent = amount.toLocaleString();
      }
    });
  }

  function bindCategoryEvents() {
    if (!linesBody) return;

    linesBody.querySelectorAll('.js-remove-cat').forEach(function (btn) {
      btn.onclick = function () {
        const blocks = linesBody.querySelectorAll('.invoice-cat-block');
        const block = btn.closest('.invoice-cat-block');
        if (!block) return;
        if (blocks.length <= 1) {
          block.querySelector('.js-cat-name').value = '';
          const tbody = block.querySelector('.invoice-cat-items');
          if (tbody) {
            tbody.innerHTML = buildItemRowHtml(blankItem());
            bindCategoryEvents();
          }
        } else {
          block.remove();
        }
        updateRowNumbers();
        updateTotals();
      };
    });

    linesBody.querySelectorAll('.js-add-item').forEach(function (btn) {
      btn.onclick = function () {
        const tbody = btn.closest('.invoice-cat-block')?.querySelector('.invoice-cat-items');
        if (!tbody) return;
        tbody.insertAdjacentHTML('beforeend', buildItemRowHtml(blankItem()));
        bindCategoryEvents();
        updateRowNumbers();
        const rows = tbody.querySelectorAll('tr');
        rows[rows.length - 1]?.querySelector('.js-item-desc')?.focus();
      };
    });

    linesBody.querySelectorAll('.js-delete-item').forEach(function (btn) {
      btn.onclick = function () {
        const tbody = btn.closest('.invoice-cat-items');
        const block = btn.closest('.invoice-cat-block');
        if (!tbody || !block) return;
        const rows = tbody.querySelectorAll('tr');
        if (rows.length <= 1) {
          const row = rows[0];
          row.querySelector('.js-item-desc').value = '';
          row.querySelector('.js-item-qty').value = '1';
          row.querySelector('.js-item-price').value = '0';
        } else {
          btn.closest('tr')?.remove();
        }
        updateRowNumbers();
        updateRowAmounts();
        updateTotals();
      };
    });

    linesBody.querySelectorAll('input').forEach(function (input) {
      input.oninput = function () {
        updateRowAmounts();
        updateTotals();
      };
    });
  }

  function updateTotals() {
    updateRowAmounts();
    let subtotal = 0;
    readCategories().forEach(function (cat) {
      cat.items.forEach(function (item) {
        subtotal += lineAmount(item.quantity, item.price);
      });
    });

    let discountPct = Number(fields.discount?.value) || 0;
    discountPct = Math.max(0, Math.min(100, discountPct));
    if (fields.discount) fields.discount.value = String(discountPct);

    const fees = Math.round(subtotal * MANAGEMENT_FEE);
    const discountAmt = Math.round(subtotal * (discountPct / 100));
    const grand = Math.max(0, subtotal + fees - discountAmt);

    const set = function (id, val) {
      const el = document.getElementById(id);
      if (el) el.textContent = Number(val).toLocaleString();
    };

    set('invoiceSubtotal', subtotal);
    set('invoiceFees', fees);
    set('invoiceDiscountAmt', discountAmt);
    set('invoiceGrandTotal', grand);
    set('invoicePay1', Math.round(grand * 0.3));
    set('invoicePay2', Math.round(grand * 0.4));
    set('invoicePay3', Math.round(grand * 0.3));

    const discountRow = document.getElementById('invoiceDiscountRow');
    if (discountRow) discountRow.hidden = discountPct <= 0;
    setCurrencyLabels();
  }

  function buildState() {
    const stateFields = Object.assign(
      {
        currency: currency(),
        language: 'en',
      },
      staticInvoiceFields()
    );

    INVOICE_FORM_FIELDS.forEach(function (spec) {
      stateFields[spec.key] = readFormField(spec.id, spec.key);
    });

    if (stateFields.discount === '') {
      stateFields.discount = '0';
    }

    return {
      categories: readCategories(),
      fields: stateFields,
    };
  }

  function populateForm(data, options) {
    const state = data.state || defaultState();
    const f = state.fields || {};
    const recordType = data.record_type || (data.invoice_number ? 'invoice' : 'proposal');

    setFieldValue('invoiceFormId', data.id ? String(data.id) : '');
    setFieldValue('invoiceFormRecordType', recordType);
    setFieldValue('invoiceFormLinkedInvoiceId', data.linked_invoice_id ? String(data.linked_invoice_id) : '');
    setFieldValue('invoiceFormLinkedInvoiceNumber', data.linked_invoice_number || '');
    setFieldValue('invoiceFormNumber', data.invoice_number || '');
    setDateFieldValue('invoiceFormOfferDate', f.offerDate || '');
    setDateFieldValue('invoiceFormDueDate', f.dueDate || '');
    setDateFieldValue('invoiceFormEventDate', f.eventDate || '');
    setFieldValue('invoiceFormLocation', f.location || '');
    setFieldValue('invoiceFormSubject', f.subject || '');
    setFieldValue('invoiceFormPrepared', f.prepared || '');
    setFieldValue('invoiceFormDiscount', f.discount || '0');
    setFieldValue('invoiceFormClientName', f.clientName || '');
    setFieldValue('invoiceFormClientAddress', f.clientAddress || '');
    setFieldValue('invoiceFormClientEmail', f.clientEmail || '');
    setFieldValue('invoiceFormClientPhone', f.clientPhone || '');
    setDateFieldValue('invoiceFormSignatureDate', f.signatureDate || '');
    setFieldValue('invoiceFormNotes', f.notes || '');

    const cur = f.currency === 'JOD' ? 'JOD' : 'SAR';
    const radio = modalEl.querySelector('input[name="invoiceFormCurrency"][value="' + cur + '"]');
    if (radio) radio.checked = true;

    const cats = Array.isArray(state.categories) && state.categories.length
      ? state.categories
      : defaultState().categories;
    renderCategories(cats);
    setCurrencyLabels();
    showError('');
    applyRecordTypeUI();

    const isNew = !data.id;
    const proposal = recordType === 'proposal';
    if (fields.title) {
      if (isNew) {
        fields.title.textContent = 'New proposal';
      } else if (proposal) {
        fields.title.textContent = (f.subject || 'Proposal') + ' — Technical & Financial Proposal';
      } else {
        fields.title.textContent = (f.subject || 'Invoice') + (data.invoice_number ? ' — ' + data.invoice_number : '');
      }
    }
    if (fields.printBtn) {
      fields.printBtn.hidden = options?.hidePrint === true;
    }
  }

  function openNew() {
    showError('');
    if (fields.saveBtn) {
      fields.saveBtn.disabled = true;
      fields.saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Preparing…';
    }

    fetch('api/invoice-create.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ admin_csrf: csrf }),
      credentials: 'same-origin',
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.data.ok) {
          throw new Error((result.data && result.data.error) || 'Could not create proposal');
        }
        const initialState = defaultState();
        initialState.fields.offerDate = todayIso();
        populateForm({
          id: result.data.id,
          invoice_number: '',
          record_type: result.data.record_type || 'proposal',
          state: initialState,
        }, { hidePrint: true });
        modal.show();
      })
      .catch(function (err) {
        notifyError(err.message || 'Could not create proposal', { title: 'Could not create proposal' });
      })
      .finally(function () {
        if (fields.saveBtn) {
          fields.saveBtn.disabled = false;
          applyRecordTypeUI();
        }
      });
  }

  function openEdit(id) {
    showError('');
    if (fields.saveBtn) {
      fields.saveBtn.disabled = true;
      fields.saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading…';
    }

    fetch('api/invoice-get.php?id=' + encodeURIComponent(id), { credentials: 'same-origin' })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.data.ok) {
          throw new Error((result.data && result.data.error) || 'Could not load invoice');
        }
        const data = result.data;
        if (
          data.record_type === 'invoice'
          && Number(data.source_proposal_id || 0) > 0
          && Number(data.id) !== Number(data.source_proposal_id)
        ) {
          openEdit(Number(data.source_proposal_id));
          return;
        }
        populateForm(data);
        modal.show();
      })
      .catch(function (err) {
        notifyError(err.message || 'Could not load proposal', { title: 'Could not load' });
      })
      .finally(function () {
        if (fields.saveBtn) {
          fields.saveBtn.disabled = false;
          applyRecordTypeUI();
        }
      });
  }

  function makeInvoice(options) {
    const opts = options || {};
    const id = parseInt(String(opts.id || fieldValue('invoiceFormId') || '0'), 10);
    if (!id) return;

    const useModalState = opts.withState !== false && modalEl.classList.contains('show');

    function runMakeInvoice() {
      const payload = {
        id: id,
        admin_csrf: csrf,
      };
      if (useModalState) {
        const state = prepareState(buildState());
        if (!state.fields.clientName) {
          showError('Enter a client name (or a subject / title) before creating an invoice.');
          return;
        }
        payload.state = state;
      }

      showError('');
      if (fields.makeInvoiceBtn) {
        fields.makeInvoiceBtn.disabled = true;
      }
      if (fields.saveBtn) {
        fields.saveBtn.disabled = true;
      }

      fetch('api/invoice-convert.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        credentials: 'same-origin',
      })
        .then(function (res) {
          return res.json().then(function (data) {
            return { ok: res.ok, data: data };
          });
        })
        .then(function (result) {
          if (!result.ok || !result.data.ok) {
            throw new Error((result.data && result.data.error) || 'Could not create invoice');
          }
          const invoiceId = result.data.id;
          const invoiceNumber = result.data.invoice_number || '';
          if (useModalState) {
            setFieldValue('invoiceFormLinkedInvoiceId', String(invoiceId));
            setFieldValue('invoiceFormLinkedInvoiceNumber', invoiceNumber);
            applyRecordTypeUI();
            if (fields.printBtn) fields.printBtn.hidden = false;
            notifySuccess('Invoice ' + invoiceNumber + ' is ready. Saving this proposal keeps the invoice in sync automatically.', {
              title: 'Invoice created',
              duration: 8000,
            });
            return;
          }
          notifySuccess('Invoice ' + invoiceNumber + ' was created successfully.', {
            title: 'Invoice created',
            duration: 5000,
          });
          window.setTimeout(function () {
            window.location.reload();
          }, 1200);
        })
        .catch(function (err) {
          if (useModalState) {
            showError(err.message || 'Could not create invoice');
          } else {
            notifyError(err.message || 'Could not create invoice', { title: 'Invoice not created' });
          }
        })
        .finally(function () {
          if (fields.makeInvoiceBtn) {
            fields.makeInvoiceBtn.disabled = false;
          }
          if (fields.saveBtn) {
            fields.saveBtn.disabled = false;
            applyRecordTypeUI();
          }
        });
    }

    if (!useModalState) {
      const subject = opts.subject || 'this proposal';
      notifyConfirm({
        title: 'Create invoice',
        message: 'Convert "' + subject + '" to an invoice? An invoice number will be assigned.',
        confirmLabel: 'Create invoice',
        cancelLabel: 'Cancel',
        variant: 'success',
      }).then(function (ok) {
        if (ok) runMakeInvoice();
      });
      return;
    }

    runMakeInvoice();
  }

  function syncInvoiceFromProposal(options) {
    const opts = options || {};
    const proposalId = parseInt(String(opts.id || fieldValue('invoiceFormId') || '0'), 10);
    if (!proposalId) return;

    const useModalState = opts.withState !== false && modalEl.classList.contains('show');
    if (!useModalState) {
      const subject = opts.subject || 'this proposal';
      if (!confirm('Copy the latest proposal content into the linked invoice for "' + subject + '"?')) {
        return;
      }
    }

    const payload = {
      proposal_id: proposalId,
      admin_csrf: csrf,
    };
    if (useModalState) {
      payload.state = prepareState(buildState());
    }

    if (fields.syncLinkedBtn) fields.syncLinkedBtn.disabled = true;

    fetch('api/invoice-sync.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      credentials: 'same-origin',
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.data.ok) {
          throw new Error((result.data && result.data.error) || 'Could not update invoice');
        }
        if (useModalState) {
          notifySuccess('The linked invoice was updated from this proposal.', {
            title: 'Invoice updated',
          });
          return;
        }
        const invoiceId = result.data.id;
        window.location.href = '?section=invoices&edit=' + encodeURIComponent(String(invoiceId));
      })
      .catch(function (err) {
        if (useModalState) {
          showError(err.message || 'Could not update invoice');
        } else {
          notifyError(err.message || 'Could not update invoice', { title: 'Update failed' });
        }
      })
      .finally(function () {
        if (fields.syncLinkedBtn) fields.syncLinkedBtn.disabled = false;
      });
  }

  function prepareState(state) {
    if (!state.fields.clientName && state.fields.subject) {
      state.fields.clientName = state.fields.subject;
    }
    return state;
  }

  function saveInvoice(event) {
    event.preventDefault();
    const id = parseInt(fieldValue('invoiceFormId') || '0', 10);
    if (!id) return;

    const state = prepareState(buildState());
    if (!isProposalMode() && !state.fields.clientName) {
      showError('Enter a client name (or a subject / title).');
      return;
    }

    showError('');
    if (fields.saveBtn) {
      fields.saveBtn.disabled = true;
      fields.saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';
    }

    fetch('api/invoice-save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: id,
        admin_csrf: csrf,
        state: state,
      }),
      credentials: 'same-origin',
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.data.ok) {
          throw new Error((result.data && result.data.error) || 'Save failed');
        }
        if (fields.printBtn) fields.printBtn.hidden = false;
        if (isProposalMode() && result.data.linked_invoice_updated) {
          notifySuccess('Proposal saved and linked invoice updated.', {
            title: 'Saved',
            duration: 4500,
          });
        } else if (isProposalMode()) {
          notifySuccess('Proposal saved successfully.', {
            title: 'Saved',
            duration: 3500,
          });
        } else {
          notifySuccess('Invoice saved successfully.', {
            title: 'Saved',
            duration: 3500,
          });
        }
        modal.hide();
        window.setTimeout(function () {
          window.location.href = '?section=invoices';
        }, isProposalMode() && result.data.linked_invoice_updated ? 900 : 500);
      })
      .catch(function (err) {
        showError(err.message || 'Save failed');
      })
      .finally(function () {
        if (fields.saveBtn) {
          fields.saveBtn.disabled = false;
          applyRecordTypeUI();
        }
      });
  }

  function clearInvoiceSearch() {
    if (!searchInput) return;
    searchInput.value = '';
    filterRows();
    searchInput.focus();
  }

  function filterRows() {
    if (!tableBody || !searchInput) return;
    const q = searchInput.value.trim().toLowerCase();
    let visible = 0;
    tableBody.querySelectorAll('tr[data-invoice-row]').forEach(function (row) {
      const hay = row.getAttribute('data-search') || '';
      const show = q === '' || hay.indexOf(q) !== -1;
      row.hidden = !show;
      if (show) visible++;
    });
    if (emptyRow) emptyRow.hidden = visible > 0 || q === '';
    if (visibleCountEl) visibleCountEl.textContent = String(visible);
    if (searchClearBtn) searchClearBtn.hidden = q === '';
    if (filterStatCard) filterStatCard.classList.toggle('is-filtered', q !== '');
  }

  function openPrint(id) {
    window.open('invoice.php?id=' + encodeURIComponent(id), '_blank', 'noopener');
  }

  function saveThenPrint() {
    const id = parseInt(fieldValue('invoiceFormId') || '0', 10);
    if (!id) return;

    const state = prepareState(buildState());
    if (!isProposalMode() && !state.fields.clientName) {
      showError('Enter a client name (or a subject / title) before printing the invoice.');
      return;
    }

    if (fields.saveBtn) {
      fields.saveBtn.disabled = true;
    }

    fetch('api/invoice-save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: id,
        admin_csrf: csrf,
        state: state,
      }),
      credentials: 'same-origin',
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.data.ok) {
          throw new Error((result.data && result.data.error) || 'Save failed');
        }
        if (fields.printBtn) fields.printBtn.hidden = false;
        openPrint(id);
      })
      .catch(function (err) {
        notifyError(err.message || 'Could not save before printing', { title: 'Print failed' });
      })
      .finally(function () {
        if (fields.saveBtn) {
          fields.saveBtn.disabled = false;
        }
      });
  }

  fields.makeInvoiceBtn?.addEventListener('click', function () {
    makeInvoice({ withState: true });
  });

  fields.viewLinkedBtn?.addEventListener('click', function () {
    const linkedId = parseInt(fieldValue('invoiceFormLinkedInvoiceId') || '0', 10);
    if (linkedId > 0) {
      openPrint(linkedId);
    }
  });

  document.querySelectorAll('.btn-make-invoice').forEach(function (btn) {
    btn.addEventListener('click', function () {
      makeInvoice({
        id: Number(btn.dataset.id),
        subject: btn.dataset.subject || '',
        withState: false,
      });
    });
  });

  document.querySelectorAll('.btn-edit-proposal').forEach(function (btn) {
    btn.addEventListener('click', function () {
      openEdit(Number(btn.dataset.id));
    });
  });

  document.querySelectorAll('.btn-print-linked-invoice').forEach(function (btn) {
    btn.addEventListener('click', function () {
      openPrint(Number(btn.dataset.id));
    });
  });

  document.querySelectorAll('.btn-print-invoice').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const id = Number(btn.dataset.id);
      const editingId = parseInt(fieldValue('invoiceFormId') || '0', 10);
      if (modalEl.classList.contains('show') && editingId === id) {
        saveThenPrint();
        return;
      }
      openPrint(id);
    });
  });

  document.getElementById('btnNewInvoice')?.addEventListener('click', openNew);
  document.getElementById('btnNewInvoiceEmpty')?.addEventListener('click', openNew);
  searchClearBtn?.addEventListener('click', clearInvoiceSearch);
  searchClearEmptyBtn?.addEventListener('click', clearInvoiceSearch);
  document.getElementById('btnInvoiceAddCategory')?.addEventListener('click', function () {
    if (!linesBody) return;
    const cats = readCategories();
    cats.push(blankCategory(''));
    renderCategories(cats);
    const blocks = linesBody.querySelectorAll('.invoice-cat-block');
    const last = blocks[blocks.length - 1];
    last?.querySelector('.js-cat-name')?.focus();
  });
  form?.addEventListener('submit', saveInvoice);
  searchInput?.addEventListener('input', filterRows);

  modalEl.querySelectorAll('input[name="invoiceFormCurrency"]').forEach(function (radio) {
    radio.addEventListener('change', setCurrencyLabels);
  });
  fields.discount?.addEventListener('input', updateTotals);

  fields.printBtn?.addEventListener('click', function () {
    saveThenPrint();
  });

  modalEl.addEventListener('hidden.bs.modal', function () {
    if (location.search.match(/[?&](add|edit)=/)) {
      history.replaceState(null, '', '?section=invoices');
    }
  });

  const boot = window.INVOICE_LIST_BOOT;
  if (boot && boot.open === 'new') {
    openNew();
  } else if (boot && boot.open === 'edit' && boot.id) {
    openEdit(boot.id);
  }
})();
