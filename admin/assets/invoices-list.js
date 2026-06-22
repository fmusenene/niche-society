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
  const paginationEl = document.getElementById('invoicePagination');
  const paginationInfoEl = document.getElementById('invoicePaginationInfo');
  const paginationListEl = document.getElementById('invoicePaginationList');
  const pageSizeSelectEl = document.getElementById('invoicePageSize');
  const tableWrapEl = document.getElementById('invoiceTableWrap');
  const listCardEl = document.querySelector('.invoice-list-card');
  const modal = new bootstrap.Modal(modalEl);
  const MANAGEMENT_FEE = 0.15;
  const AUTO_SAVE_DELAY_MS = 2000;
  const PAGE_SIZE_STORAGE_KEY = 'invoice_list_page_size';
  const PAGE_SIZE_OPTIONS = [10, 25, 50, 100];
  let invoicePageSize = parseInt(localStorage.getItem(PAGE_SIZE_STORAGE_KEY) || '10', 10);
  if (PAGE_SIZE_OPTIONS.indexOf(invoicePageSize) === -1) {
    invoicePageSize = 10;
  }
  let invoiceCurrentPage = 1;
  let scrollHighlightFrame = null;
  let scrollIdleTimer = null;
  let isListScrolling = false;
  let refreshListOnModalClose = false;
  let suppressListRefreshOnClose = false;
  let formDirty = false;
  let sessionTouched = false;
  let autoSaveTimer = null;
  let autoSaveInFlight = null;

  function cleanInvoicesListUrl() {
    if (location.search.match(/[?&](add|edit)=/)) {
      history.replaceState(null, '', '?section=invoices');
    }
  }

  function markFormDirty() {
    if (!modalEl.classList.contains('show')) return;
    formDirty = true;
    sessionTouched = true;
    scheduleAutoSave();
  }

  function resetFormTracking() {
    formDirty = false;
    sessionTouched = false;
    clearTimeout(autoSaveTimer);
    window.setTimeout(function () {
      lastSavedJson = JSON.stringify(prepareState(buildState()));
    }, 0);
  }

  let lastSavedJson = '';

  function scheduleAutoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = window.setTimeout(function () {
      if (!modalEl.classList.contains('show') || !formDirty) return;
      autoSaveInvoice({ silent: true }).catch(function () {});
    }, AUTO_SAVE_DELAY_MS);
  }

  function autoSaveInvoice(options) {
    const opts = options || {};
    const id = parseInt(fieldValue('invoiceFormId') || '0', 10);
    if (!id) return Promise.resolve(null);

    const state = prepareState(buildState());
    if (!isProposalMode() && !state.fields.clientName && opts.requireClientName) {
      return Promise.reject(new Error('Enter a client name (or a subject / title).'));
    }

    if (opts.skipIfClean && !formDirty) {
      return Promise.resolve({ skipped: true });
    }

    const payload = {
      id: id,
      admin_csrf: csrf,
      state: state,
    };

    const runSave = function () {
      return fetch('api/invoice-save.php', {
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
            throw new Error((result.data && result.data.error) || 'Save failed');
          }
          formDirty = false;
          lastSavedJson = JSON.stringify(state);
          return result.data;
        });
    };

    if (autoSaveInFlight) {
      return autoSaveInFlight.then(function () {
        if (opts.skipIfClean && !formDirty) {
          return { skipped: true };
        }
        return runSave();
      });
    }

    autoSaveInFlight = runSave().finally(function () {
      autoSaveInFlight = null;
    });
    return autoSaveInFlight;
  }

  function flushAutoSaveOnClose() {
    clearTimeout(autoSaveTimer);
    const id = parseInt(fieldValue('invoiceFormId') || '0', 10);
    const shouldReload = refreshListOnModalClose || sessionTouched;

    if (!id) {
      refreshListOnModalClose = false;
      sessionTouched = false;
      cleanInvoicesListUrl();
      return;
    }

    if (!sessionTouched && !refreshListOnModalClose) {
      cleanInvoicesListUrl();
      return;
    }

    autoSaveInvoice({ silent: true })
      .catch(function () {})
      .finally(function () {
        refreshListOnModalClose = false;
        formDirty = false;
        sessionTouched = false;
        if (shouldReload) {
          window.location.href = '?section=invoices';
        } else {
          cleanInvoicesListUrl();
        }
      });
  }

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

  const DEFAULT_PAYMENT_TERMS_AR = [
    'الدفعة الأولى: 30% من إجمالي المبلغ عند توقيع العقد',
    'الدفعة الثانية: 40% من إجمالي المبلغ قبل الفعالية',
    'الدفعة الثالثة: 30% من إجمالي المبلغ في يوم الفعالية',
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

  const PROPOSAL_DEFAULTS_AR = {
    intro1: 'يسعدنا أن تتاح لنا الفرصة لتقديم عرضنا لإدارة فعاليتكم القادمة.',
    intro2: 'نقدم لكم ملخصًا كاملاً للعرض الفني والمالي الذي توفره نيش سوسايتي.',
    intro3: 'شكرًا لاختياركم نيش سوسايتي كأحد المرشحين للشراكة في فعاليتكم القادمة.',
    cancellationPolicy: [
      'الدفعة الأولى غير مستردة.',
      'لا يتم رد أي دفعة إذا تم الإلغاء قبل أقل من 60 يومًا من الفعالية.',
      'عروض التصوير والفيديو لها أسعار خاصة مع خدمات تنظيم حفلات الزفاف.',
    ].join('\n'),
    closing1: 'نأمل أن يكون ما ورد أعلاه مناسبًا لكم ونتطلع إلى تأكيدكم قريبًا،',
    closing2: 'يرجى إرسال بريد إلكتروني يفيد بالموافقة وتأكيد هذه الترتيبات.',
    closing3: 'لأي استفسار إضافي يرجى التواصل مباشرة على +966 54 694 7915، ويسعدنا تقديم المساعدة.',
    closingRegards: 'مع أطيب التحيات،',
    socialIntro: 'يمكن للعميل الموافقة أو عدم الموافقة على نشر محتوى الفعالية (صور، فيديو، وتغطية) على المنصات أدناه. ضع علامة على غير موافق إذا لم تكن نيش سوسايتي تنشر محتوى الفعالية على تلك المنصة دون موافقة خطية منفصلة.',
    socialSnapchat: '',
    socialInstagram: '',
    socialFacebook: '',
  };

  const FORM_I18N = {
    en: {
      sectionProposal: 'Proposal',
      sectionInvoice: 'Invoice',
      sectionClient: 'Client details',
      sectionCategories: 'Categories & line items',
      sectionTotals: 'Totals',
      offerDate: 'Offer date',
      invoiceDate: 'Invoice date',
      dueDate: 'Due date',
      currency: 'Currency',
      language: 'Language',
      eventDate: 'Event date',
      eventLocation: 'Event location',
      preparedBy: 'Prepared by',
      subject: 'Subject / title',
      tel: 'Tel',
      clientName: 'Client name',
      clientEmail: 'Email',
      clientPhone: 'Phone',
      dateSigned: 'Date signed',
      clientAddress: 'Address',
      addCategory: 'Add category',
      linesHint: 'Group services by category. Use <strong>Add item</strong> inside each category for more rows.',
      discount: 'Discount (%)',
      notes: 'Notes',
      subtotal: 'Subtotal (line amounts)',
      fees: 'Event management fees (15%)',
      discountAmt: 'Discount',
      amountDue: 'Amount due',
      paymentSchedule: 'Payment schedule:',
      colNum: '#',
      colDesc: 'Description',
      colQty: 'Qty',
      colUnit: 'Unit price',
      colAmount: 'Amount',
      colTotal: 'Total',
      addItem: 'Add item',
      deleteCategory: 'Delete',
      catPlaceholder: 'Category name (e.g. Entertainment)',
      descPlaceholder: 'Description',
      saveProposal: 'Save proposal',
      saveInvoice: 'Save invoice',
      printProposal: 'Print proposal',
      printInvoice: 'Print invoice',
      makeInvoice: 'Make invoice',
      cancel: 'Cancel',
      newProposal: 'New proposal',
      docProposalSuffix: ' — Technical & Financial Proposal',
      printInstallmentTitle: 'Print invoice',
      printInstallmentDesc: 'Select which payment this invoice is for — one installment or the full contract amount.',
      installmentFull: 'Full payment (100%)',
      installment1: '1st payment (30%)',
      installment2: '2nd payment (40%)',
      installment3: '3rd payment (30%)',
      printLinkedInvoice: 'Print invoice',
    },
    ar: {
      sectionProposal: 'العرض',
      sectionInvoice: 'الفاتورة',
      sectionClient: 'بيانات العميل',
      sectionCategories: 'الأقسام والبنود',
      sectionTotals: 'الإجماليات',
      offerDate: 'تاريخ العرض',
      invoiceDate: 'تاريخ الفاتورة',
      dueDate: 'تاريخ الاستحقاق',
      currency: 'العملة',
      language: 'اللغة',
      eventDate: 'تاريخ الفعالية',
      eventLocation: 'موقع الفعالية',
      preparedBy: 'إعداد',
      subject: 'الموضوع / العنوان',
      tel: 'هاتف',
      clientName: 'اسم العميل',
      clientEmail: 'البريد الإلكتروني',
      clientPhone: 'الهاتف',
      dateSigned: 'تاريخ التوقيع',
      clientAddress: 'العنوان',
      addCategory: 'إضافة قسم',
      linesHint: 'جمّع الخدمات حسب القسم. استخدم <strong>إضافة بند</strong> داخل كل قسم لصفوف إضافية.',
      discount: 'الخصم (%)',
      notes: 'ملاحظات',
      subtotal: 'إجمالي البنود',
      fees: 'رسوم إدارة الفعالية (15%)',
      discountAmt: 'الخصم',
      amountDue: 'المبلغ المستحق',
      paymentSchedule: 'جدول الدفع:',
      colNum: '#',
      colDesc: 'التفاصيل',
      colQty: 'الكمية',
      colUnit: 'سعر الوحدة',
      colAmount: 'المبلغ',
      colTotal: 'الإجمالي',
      addItem: 'إضافة بند',
      deleteCategory: 'حذف',
      catPlaceholder: 'اسم القسم (مثال: ترفيه)',
      descPlaceholder: 'الوصف',
      saveProposal: 'حفظ العرض',
      saveInvoice: 'حفظ الفاتورة',
      printProposal: 'طباعة العرض',
      printInvoice: 'طباعة الفاتورة',
      makeInvoice: 'إنشاء فاتورة',
      cancel: 'إلغاء',
      newProposal: 'عرض جديد',
      docProposalSuffix: ' — العرض الفني والمالي',
      printInstallmentTitle: 'طباعة الفاتورة',
      printInstallmentDesc: 'اختر الدفعة التي تخص هذه الفاتورة — قسط واحد أو المبلغ الكامل للعقد.',
      installmentFull: 'دفع كامل (100%)',
      installment1: 'الدفعة الأولى (30%)',
      installment2: 'الدفعة الثانية (40%)',
      installment3: 'الدفعة الثالثة (30%)',
      printLinkedInvoice: 'طباعة الفاتورة',
    },
  };

  let languageSwitchBusy = false;
  let lastSavedFormLanguage = 'en';
  const translationCache = new Map();

  function getFormLanguage() {
    const checked = modalEl.querySelector('input[name="invoiceFormLanguage"]:checked');
    return checked && checked.value === 'ar' ? 'ar' : 'en';
  }

  function setFormLanguageRadio(lang) {
    const value = lang === 'ar' ? 'ar' : 'en';
    const radio = modalEl.querySelector('input[name="invoiceFormLanguage"][value="' + value + '"]');
    if (radio) radio.checked = true;
  }

  function formTr(key) {
    const lang = getFormLanguage();
    return FORM_I18N[lang][key] || FORM_I18N.en[key] || key;
  }

  function applyFormLanguage() {
    const lang = getFormLanguage();
    const proposal = isProposalMode();
    const modalContent = modalEl.querySelector('.invoice-modal');
    if (modalContent) {
      modalContent.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
      modalContent.setAttribute('lang', lang);
    }

    const setText = function (id, key) {
      const el = document.getElementById(id);
      if (el) el.textContent = formTr(key);
    };
    const setHtml = function (id, key) {
      const el = document.getElementById(id);
      if (el) el.innerHTML = formTr(key);
    };

    const sectionProposal = document.getElementById('invoiceSectionProposalTitle');
    if (sectionProposal) {
      sectionProposal.innerHTML = proposal
        ? '<i class="bi bi-file-earmark-text" aria-hidden="true"></i> ' + formTr('sectionProposal')
        : '<i class="bi bi-receipt" aria-hidden="true"></i> ' + formTr('sectionInvoice');
    }
    const sectionClient = document.getElementById('invoiceSectionClientTitle');
    if (sectionClient) {
      sectionClient.innerHTML = '<i class="bi bi-person-lines-fill" aria-hidden="true"></i> ' + formTr('sectionClient');
    }
    const sectionCategories = document.getElementById('invoiceSectionCategoriesTitle');
    if (sectionCategories) {
      sectionCategories.innerHTML = '<i class="bi bi-list-ul" aria-hidden="true"></i> ' + formTr('sectionCategories');
    }
    const sectionTotals = document.getElementById('invoiceSectionTotalsTitle');
    if (sectionTotals) {
      sectionTotals.innerHTML = '<i class="bi bi-calculator" aria-hidden="true"></i> ' + formTr('sectionTotals');
    }

    setText('labelOfferDate', proposal ? 'offerDate' : 'invoiceDate');
    setText('labelInvoiceCurrency', 'currency');
    setText('labelInvoiceLanguage', 'language');
    setText('labelEventDate', 'eventDate');
    setText('labelEventLocation', 'eventLocation');
    setText('labelPreparedBy', 'preparedBy');
    setText('labelSubject', 'subject');
    setText('labelTel', 'tel');
    setText('labelClientName', 'clientName');
    setText('labelClientEmail', 'clientEmail');
    setText('labelClientPhone', 'clientPhone');
    setText('labelDateSigned', 'dateSigned');
    setText('labelClientAddress', 'clientAddress');
    setText('labelAddCategory', 'addCategory');
    setHtml('invoiceLinesHint', 'linesHint');
    setText('labelDiscount', 'discount');
    setText('labelNotes', 'notes');
    setText('labelSubtotal', 'subtotal');
    setText('labelFees', 'fees');
    setText('labelDiscountAmt', 'discountAmt');
    setText('labelAmountDue', 'amountDue');
    setText('labelPaymentSchedule', 'paymentSchedule');
    setText('labelMakeInvoice', 'makeInvoice');
    setText('labelCancel', 'cancel');

    if (fields.saveLabel) {
      fields.saveLabel.textContent = proposal ? formTr('saveProposal') : formTr('saveInvoice');
    }
    if (fields.printLabel) {
      fields.printLabel.textContent = proposal ? formTr('printProposal') : formTr('printInvoice');
    }

    linesBody?.querySelectorAll('.invoice-cat-items-table thead th').forEach(function (th, index) {
      const keys = ['colNum', 'colDesc', 'colQty', 'colUnit', 'colAmount'];
      if (keys[index]) th.textContent = formTr(keys[index]);
    });
    linesBody?.querySelectorAll('.invoice-cat-total-label').forEach(function (el) {
      el.textContent = formTr('colTotal');
    });
    linesBody?.querySelectorAll('.js-add-item').forEach(function (btn) {
      btn.innerHTML = '<i class="bi bi-plus-lg"></i> ' + formTr('addItem');
    });
    linesBody?.querySelectorAll('.invoice-cat-remove-label').forEach(function (el) {
      el.textContent = formTr('deleteCategory');
    });
    linesBody?.querySelectorAll('.js-cat-name').forEach(function (input) {
      input.placeholder = formTr('catPlaceholder');
    });
    linesBody?.querySelectorAll('.js-item-desc').forEach(function (input) {
      input.placeholder = formTr('descPlaceholder');
      input.setAttribute('aria-label', formTr('descPlaceholder'));
    });

    const instTitle = document.getElementById('modalPrintInstallmentTitle');
    if (instTitle) instTitle.textContent = formTr('printInstallmentTitle');
    const instDesc = document.getElementById('modalPrintInstallmentDesc');
    if (instDesc) instDesc.textContent = formTr('printInstallmentDesc');
    setText('labelInstallment1', 'installment1');
    setText('labelInstallment2', 'installment2');
    setText('labelInstallment3', 'installment3');
    setText('labelInstallmentFull', 'installmentFull');
    const viewLinkedLabel = document.getElementById('labelPrintLinkedInvoice');
    if (viewLinkedLabel) viewLinkedLabel.textContent = formTr('printLinkedInvoice');
  }

  function updateInstallmentPickerAmounts() {
    const cur = currency();
    const fullEl = document.getElementById('labelInstallmentFull');
    const grandEl = document.getElementById('invoiceGrandTotal');
    if (fullEl) {
      const grand = grandEl ? grandEl.textContent.trim() : '';
      const base = formTr('installmentFull');
      fullEl.textContent = grand ? base + ' — ' + grand + ' ' + cur : base;
    }
    const amounts = ['invoicePay1', 'invoicePay2', 'invoicePay3'];
    const keys = ['installment1', 'installment2', 'installment3'];
    const labelIds = ['labelInstallment1', 'labelInstallment2', 'labelInstallment3'];
    amounts.forEach(function (amountId, index) {
      const amountEl = document.getElementById(amountId);
      const labelEl = document.getElementById(labelIds[index]);
      if (!labelEl) return;
      const amount = amountEl ? amountEl.textContent.trim() : '';
      const base = formTr(keys[index]);
      labelEl.textContent = amount ? base + ' — ' + amount + ' ' + cur : base;
    });
  }

  let pendingInvoicePrintId = 0;

  function translationCacheKey(text, fromLang, toLang) {
    return fromLang + ':' + toLang + ':' + text;
  }

  async function translateManyViaApi(texts, targetLang) {
    const sourceLang = targetLang === 'ar' ? 'en' : 'ar';
    const inputs = texts.map(function (t) { return String(t || '').trim(); });
    const results = inputs.slice();
    const pendingIndexes = [];
    const pendingTexts = [];

    inputs.forEach(function (text, index) {
      if (!text) return;
      const cached = translationCache.get(translationCacheKey(text, sourceLang, targetLang));
      if (cached !== undefined) {
        results[index] = cached;
        return;
      }
      pendingIndexes.push(index);
      pendingTexts.push(text);
    });

    if (pendingTexts.length) {
      const res = await fetch('api/translate-batch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ texts: pendingTexts, from: sourceLang, to: targetLang }),
        credentials: 'same-origin',
      });
      const data = await res.json();
      if (!data.ok) throw new Error(data.error || 'Translation failed');
      const translations = data.translations || [];
      pendingIndexes.forEach(function (originalIndex, i) {
        const translated = translations[i] ?? results[originalIndex];
        results[originalIndex] = translated;
        const sourceText = pendingTexts[i];
        translationCache.set(translationCacheKey(sourceText, sourceLang, targetLang), translated);
      });
    }

    return results;
  }

  function collectTranslatableFields() {
    const jobs = [];
    const fieldIds = [
      'invoiceFormLocation',
      'invoiceFormSubject',
      'invoiceFormPrepared',
      'invoiceFormProposalTel',
      'invoiceFormClientName',
      'invoiceFormClientAddress',
      'invoiceFormClientEmail',
      'invoiceFormClientPhone',
      'invoiceFormNotes',
    ];

    fieldIds.forEach(function (id) {
      const el = fieldEl(id);
      if (!el || el.closest('[hidden]')) return;
      const value = String(el.value || '').trim();
      if (value) jobs.push({ el: el, value: value });
    });

    linesBody?.querySelectorAll('.js-cat-name').forEach(function (el) {
      const value = String(el.value || '').trim();
      if (value) jobs.push({ el: el, value: value });
    });

    linesBody?.querySelectorAll('.js-item-desc').forEach(function (el) {
      const value = String(el.value || '').trim();
      if (value) jobs.push({ el: el, value: value });
    });

    return jobs;
  }

  async function translateFormContent(targetLang) {
    const jobs = collectTranslatableFields();
    if (!jobs.length) return;

    const translations = await translateManyViaApi(jobs.map(function (job) { return job.value; }), targetLang);
    jobs.forEach(function (job, index) {
      job.el.value = translations[index] ?? job.value;
    });
  }

  function setLanguageSwitchLoading(loading) {
    const pills = modalEl.querySelector('.invoice-language-pills');
    if (pills) pills.classList.toggle('is-translating', loading);
    modalEl.querySelectorAll('input[name="invoiceFormLanguage"]').forEach(function (r) {
      r.disabled = loading;
    });
  }

  async function switchFormLanguage(targetLang, sourceLang) {
    if (languageSwitchBusy) return;
    const from = sourceLang || lastSavedFormLanguage;
    if (from === targetLang) return;

    languageSwitchBusy = true;
    setLanguageSwitchLoading(true);

    const previousLang = from;
    setFormLanguageRadio(targetLang);
    lastSavedFormLanguage = targetLang;
    applyFormLanguage();
    applyRecordTypeUI();

    try {
      await translateFormContent(targetLang);
      markFormDirty();
    } catch (err) {
      notifyError(err.message || 'Translation failed', { title: 'Language switch' });
      lastSavedFormLanguage = previousLang;
      setFormLanguageRadio(previousLang);
      applyFormLanguage();
      applyRecordTypeUI();
    } finally {
      setLanguageSwitchLoading(false);
      languageSwitchBusy = false;
    }
  }

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
    const lang = getFormLanguage();
    const defaults = lang === 'ar' ? PROPOSAL_DEFAULTS_AR : PROPOSAL_DEFAULTS;
    const paymentTerms = lang === 'ar' ? DEFAULT_PAYMENT_TERMS_AR : DEFAULT_PAYMENT_TERMS;
    return {
      paymentTerms: paymentTerms,
      intro1: defaults.intro1,
      intro2: defaults.intro2,
      intro3: defaults.intro3,
      cancellationPolicy: defaults.cancellationPolicy,
      closing1: defaults.closing1,
      closing2: defaults.closing2,
      closing3: defaults.closing3,
      closingRegards: defaults.closingRegards,
      socialIntro: defaults.socialIntro,
      socialSnapchat: '',
      socialInstagram: '',
      socialFacebook: '',
      language: lang,
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
    modalEl.querySelectorAll('.proposal-only-field').forEach(function (el) {
      el.hidden = !proposal;
    });
    const offerLabel = document.getElementById('labelOfferDate');
    if (offerLabel) {
      offerLabel.textContent = proposal ? 'Offer date' : 'Invoice date';
    }
    const sectionProposalTitle = document.getElementById('invoiceSectionProposalTitle');
    if (sectionProposalTitle) {
      sectionProposalTitle.innerHTML = proposal
        ? '<i class="bi bi-file-earmark-text" aria-hidden="true"></i> Proposal'
        : '<i class="bi bi-receipt" aria-hidden="true"></i> Invoice';
    }
    const clientSection = document.getElementById('invoiceSectionClient');
    if (clientSection) {
      clientSection.hidden = false;
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
      fields.saveBtn.innerHTML = '<i class="bi bi-check-lg"></i> <span id="btnInvoiceSaveLabel">' + (proposal ? formTr('saveProposal') : formTr('saveInvoice')) + '</span>';
      fields.saveLabel = document.getElementById('btnInvoiceSaveLabel');
    }
    applyFormLanguage();
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
      name: name ?? '',
      items: [blankItem()],
    };
  }

  function defaultState() {
    return {
      categories: [blankCategory('')],
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
    const descPh = formTr('descPlaceholder');
    return (
      '<tr>' +
      '<td class="col-num"><span class="js-row-num text-muted">—</span></td>' +
      '<td class="col-desc"><input type="text" class="form-control form-control-sm js-item-desc" value="' + escapeHtml(item.description || '') + '" placeholder="' + escapeHtml(descPh) + '" aria-label="' + escapeHtml(descPh) + '"></td>' +
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
    const catPh = escapeHtml(formTr('catPlaceholder'));
    block.innerHTML =
      '<div class="invoice-cat-header">' +
      '<input type="text" class="form-control form-control-sm js-cat-name" value="' + escapeHtml(cat.name || '') + '" placeholder="' + catPh + '" aria-label="' + catPh + '">' +
      '<button type="button" class="btn btn-sm btn-outline-danger js-remove-cat" title="Delete category"><i class="bi bi-trash" aria-hidden="true"></i><span class="invoice-cat-remove-label">' + escapeHtml(formTr('deleteCategory')) + '</span></button>' +
      '</div>' +
      '<div class="table-responsive">' +
      '<table class="table table-sm invoice-lines-table invoice-cat-items-table mb-0">' +
      '<thead><tr>' +
      '<th class="col-num">' + escapeHtml(formTr('colNum')) + '</th>' +
      '<th>' + escapeHtml(formTr('colDesc')) + '</th>' +
      '<th class="text-end col-qty">' + escapeHtml(formTr('colQty')) + '</th>' +
      '<th class="text-end col-unit">' + escapeHtml(formTr('colUnit')) + '</th>' +
      '<th class="text-end col-amount">' + escapeHtml(formTr('colAmount')) + '</th>' +
      '<th class="col-actions"></th>' +
      '</tr></thead>' +
      '<tbody class="invoice-cat-items">' + itemsHtml +
      '<tr class="invoice-cat-total-row">' +
      '<td colspan="2" class="text-end invoice-cat-total-label">' + escapeHtml(formTr('colTotal')) + '</td>' +
      '<td class="text-end"><span class="js-cat-qty-total">0</span></td>' +
      '<td></td>' +
      '<td class="text-end"><span class="js-cat-total fw-semibold" data-amount="0">0</span></td>' +
      '<td></td>' +
      '</tr></tbody>' +
      '</table></div>' +
      '<div class="invoice-cat-footer">' +
      '<button type="button" class="btn btn-outline-secondary btn-sm js-add-item"><i class="bi bi-plus-lg"></i> ' + escapeHtml(formTr('addItem')) + '</button>' +
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
      block.querySelectorAll('.invoice-cat-items tr:not(.invoice-cat-total-row)').forEach(function (row) {
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
    linesBody.querySelectorAll('.invoice-cat-items').forEach(function (tbody) {
      let num = 0;
      tbody.querySelectorAll('tr:not(.invoice-cat-total-row)').forEach(function (row) {
        num += 1;
        const el = row.querySelector('.js-row-num');
        if (el) el.textContent = String(num);
      });
    });
  }

  function updateRowAmounts() {
    linesBody?.querySelectorAll('.invoice-cat-items tr:not(.invoice-cat-total-row)').forEach(function (row) {
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
        markFormDirty();
      };
    });

    linesBody.querySelectorAll('.js-add-item').forEach(function (btn) {
      btn.onclick = function () {
        const tbody = btn.closest('.invoice-cat-block')?.querySelector('.invoice-cat-items');
        if (!tbody) return;
        const totalRow = tbody.querySelector('.invoice-cat-total-row');
        const rowHtml = buildItemRowHtml(blankItem());
        if (totalRow) {
          totalRow.insertAdjacentHTML('beforebegin', rowHtml);
        } else {
          tbody.insertAdjacentHTML('beforeend', rowHtml);
        }
        bindCategoryEvents();
        updateRowNumbers();
        const itemRows = tbody.querySelectorAll('tr:not(.invoice-cat-total-row)');
        itemRows[itemRows.length - 1]?.querySelector('.js-item-desc')?.focus();
        markFormDirty();
      };
    });

    linesBody.querySelectorAll('.js-delete-item').forEach(function (btn) {
      btn.onclick = function () {
        const tbody = btn.closest('.invoice-cat-items');
        const block = btn.closest('.invoice-cat-block');
        if (!tbody || !block) return;
        const itemRows = tbody.querySelectorAll('tr:not(.invoice-cat-total-row)');
        if (itemRows.length <= 1) {
          const row = itemRows[0];
          row.querySelector('.js-item-desc').value = '';
          row.querySelector('.js-item-qty').value = '1';
          row.querySelector('.js-item-price').value = '0';
        } else {
          btn.closest('tr')?.remove();
        }
        updateRowNumbers();
        updateRowAmounts();
        updateTotals();
        markFormDirty();
      };
    });

    linesBody.querySelectorAll('input').forEach(function (input) {
      input.oninput = function () {
        markFormDirty();
        updateRowAmounts();
        updateTotals();
      };
    });
  }

  function updateCategoryTotals() {
    if (!linesBody) return;
    linesBody.querySelectorAll('.invoice-cat-block').forEach(function (block) {
      let subtotal = 0;
      let qtyTotal = 0;
      block.querySelectorAll('.invoice-cat-items tr:not(.invoice-cat-total-row)').forEach(function (row) {
        const qty = Math.max(0, parseInt(row.querySelector('.js-item-qty')?.value || '0', 10) || 0);
        const unit = Math.max(0, parseInt(row.querySelector('.js-item-price')?.value || '0', 10) || 0);
        subtotal += lineAmount(qty, unit);
        qtyTotal += qty;
      });
      const totalEl = block.querySelector('.js-cat-total');
      const qtyEl = block.querySelector('.js-cat-qty-total');
      if (totalEl) {
        totalEl.dataset.amount = String(subtotal);
        totalEl.textContent = subtotal.toLocaleString();
      }
      if (qtyEl) qtyEl.textContent = String(qtyTotal);
    });
  }

  function updateTotals() {
    updateRowAmounts();
    updateCategoryTotals();
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
        language: getFormLanguage(),
      },
      staticInvoiceFields()
    );

    INVOICE_FORM_FIELDS.forEach(function (spec) {
      stateFields[spec.key] = readFormField(spec.id, spec.key);
    });

    if (isProposalMode()) {
      stateFields.clientPhone = readFormField('invoiceFormProposalTel', 'clientPhone');
    }

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
    setFieldValue('invoiceFormProposalTel', f.clientPhone || '');
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

    setFormLanguageRadio(f.language === 'ar' ? 'ar' : 'en');
    lastSavedFormLanguage = f.language === 'ar' ? 'ar' : 'en';

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
        fields.title.textContent = formTr('newProposal');
      } else if (proposal) {
        fields.title.textContent = (f.subject || formTr('sectionProposal')) + formTr('docProposalSuffix');
      } else {
        fields.title.textContent = (f.subject || formTr('sectionInvoice')) + (data.invoice_number ? ' — ' + data.invoice_number : '');
      }
    }
    if (fields.printBtn) {
      fields.printBtn.hidden = options?.hidePrint === true;
    }
    resetFormTracking();
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
        refreshListOnModalClose = true;
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

    showError('');
    if (fields.saveBtn) {
      fields.saveBtn.disabled = true;
      fields.saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';
    }

    autoSaveInvoice({ requireClientName: !isProposalMode() })
      .then(function (data) {
        if (!data || data.skipped) {
          return data;
        }
        if (fields.printBtn) fields.printBtn.hidden = false;
        if (isProposalMode() && data.linked_invoice_updated) {
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
        refreshListOnModalClose = false;
        suppressListRefreshOnClose = true;
        modal.hide();
        window.setTimeout(function () {
          window.location.href = '?section=invoices';
        }, isProposalMode() && data.linked_invoice_updated ? 900 : 500);
        return data;
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

  function getFilteredInvoiceRows() {
    if (!tableBody) return [];
    return Array.from(tableBody.querySelectorAll('tr[data-invoice-row]')).filter(function (row) {
      return !row.hidden;
    });
  }

  function getDisplayedInvoiceRows() {
    if (!tableBody) return [];
    return Array.from(tableBody.querySelectorAll('tr[data-invoice-row]')).filter(function (row) {
      return !row.hidden && !row.classList.contains('invoice-page-hidden');
    });
  }

  function updateScrollActiveRow() {
    if (!tableBody) return;
    const rows = getDisplayedInvoiceRows();
    rows.forEach(function (row) {
      row.classList.remove('is-scroll-active', 'is-in-viewport');
    });
    if (!rows.length) return;

    const viewTop = 0;
    const viewBottom = window.innerHeight;
    const centerY = window.innerHeight / 2;
    let bestRow = null;
    let bestDist = Infinity;

    rows.forEach(function (row) {
      const rect = row.getBoundingClientRect();
      if (rect.bottom < viewTop || rect.top > viewBottom) return;
      if (isListScrolling) {
        row.classList.add('is-in-viewport');
      }
      const rowCenter = rect.top + rect.height / 2;
      const dist = Math.abs(rowCenter - centerY);
      if (dist < bestDist) {
        bestDist = dist;
        bestRow = row;
      }
    });

    if (bestRow && isListScrolling) {
      bestRow.classList.add('is-scroll-active');
    }
  }

  function scheduleScrollActiveRow() {
    if (scrollHighlightFrame) return;
    scrollHighlightFrame = window.requestAnimationFrame(function () {
      scrollHighlightFrame = null;
      updateScrollActiveRow();
    });
  }

  function onInvoiceListScroll() {
    if (!tableBody) return;
    isListScrolling = true;
    listCardEl?.classList.add('is-scrolling');
    scheduleScrollActiveRow();
    if (scrollIdleTimer) {
      clearTimeout(scrollIdleTimer);
    }
    scrollIdleTimer = setTimeout(function () {
      isListScrolling = false;
      listCardEl?.classList.remove('is-scrolling');
      scheduleScrollActiveRow();
    }, 140);
  }

  function scrollInvoiceListToTop() {
    if (tableWrapEl) {
      tableWrapEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function revealVisibleInvoiceRows() {
    const rows = getDisplayedInvoiceRows();
    rows.forEach(function (row, index) {
      row.classList.remove('invoice-row-enter');
      row.style.setProperty('--row-enter-delay', String(index * 40) + 'ms');
      void row.offsetWidth;
      row.classList.add('invoice-row-enter');
    });
  }

  function renderInvoicePagination(totalItems, totalPages) {
    if (!paginationEl || !paginationListEl) return;

    if (totalItems === 0) {
      paginationEl.hidden = true;
      paginationListEl.innerHTML = '';
      if (paginationInfoEl) paginationInfoEl.textContent = '';
      return;
    }

    paginationEl.hidden = false;
    const start = (invoiceCurrentPage - 1) * invoicePageSize + 1;
    const end = Math.min(invoiceCurrentPage * invoicePageSize, totalItems);
    if (paginationInfoEl) {
      paginationInfoEl.textContent = 'Showing ' + start + '–' + end + ' of ' + totalItems;
    }

    if (totalPages <= 1) {
      paginationListEl.innerHTML = '';
      paginationListEl.hidden = true;
      return;
    }

    paginationListEl.hidden = false;
    const items = [];
    items.push(
      '<li class="page-item' + (invoiceCurrentPage <= 1 ? ' disabled' : '') + '">' +
      '<button type="button" class="page-link" data-page="prev" aria-label="Previous page">&lsaquo;</button></li>'
    );

    const maxButtons = 5;
    let pageStart = Math.max(1, invoiceCurrentPage - Math.floor(maxButtons / 2));
    let pageEnd = Math.min(totalPages, pageStart + maxButtons - 1);
    pageStart = Math.max(1, pageEnd - maxButtons + 1);

    for (let page = pageStart; page <= pageEnd; page += 1) {
      items.push(
        '<li class="page-item' + (page === invoiceCurrentPage ? ' active' : '') + '">' +
        '<button type="button" class="page-link" data-page="' + page + '"' +
        (page === invoiceCurrentPage ? ' aria-current="page"' : '') + '>' + page + '</button></li>'
      );
    }

    items.push(
      '<li class="page-item' + (invoiceCurrentPage >= totalPages ? ' disabled' : '') + '">' +
      '<button type="button" class="page-link" data-page="next" aria-label="Next page">&rsaquo;</button></li>'
    );

    paginationListEl.innerHTML = items.join('');
  }

  function applyInvoicePagination() {
    if (!tableBody) return;

    const filteredRows = getFilteredInvoiceRows();
    const totalItems = filteredRows.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / invoicePageSize));

    if (invoiceCurrentPage > totalPages) {
      invoiceCurrentPage = totalPages;
    }
    if (invoiceCurrentPage < 1) {
      invoiceCurrentPage = 1;
    }

    const start = (invoiceCurrentPage - 1) * invoicePageSize;
    const end = start + invoicePageSize;

    tableBody.querySelectorAll('tr[data-invoice-row]').forEach(function (row) {
      row.classList.remove('invoice-page-hidden', 'is-scroll-active', 'is-in-viewport', 'invoice-row-enter');
      if (row.hidden) return;
      const index = filteredRows.indexOf(row);
      if (index < 0 || index < start || index >= end) {
        row.classList.add('invoice-page-hidden');
      }
    });

    renderInvoicePagination(totalItems, totalPages);
    revealVisibleInvoiceRows();
    scheduleScrollActiveRow();
  }

  function goToInvoicePage(page) {
    if (!tableBody) return;
    const totalPages = Math.max(1, Math.ceil(getFilteredInvoiceRows().length / invoicePageSize));
    if (page === 'prev') {
      invoiceCurrentPage = Math.max(1, invoiceCurrentPage - 1);
    } else if (page === 'next') {
      invoiceCurrentPage = Math.min(totalPages, invoiceCurrentPage + 1);
    } else {
      const num = parseInt(page, 10);
      if (!num || num < 1 || num > totalPages) return;
      invoiceCurrentPage = num;
    }
    applyInvoicePagination();
    scrollInvoiceListToTop();
  }

  function filterRows() {
    if (!tableBody || !searchInput) return;
    const q = searchInput.value.trim().toLowerCase();
    let visible = 0;
    tableBody.querySelectorAll('tr[data-invoice-row]').forEach(function (row) {
      const hay = row.getAttribute('data-search') || '';
      const show = q === '' || hay.indexOf(q) !== -1;
      row.hidden = !show;
      row.classList.remove('invoice-page-hidden');
      if (show) visible++;
    });
    if (emptyRow) emptyRow.hidden = visible > 0 || q === '';
    if (visibleCountEl) visibleCountEl.textContent = String(visible);
    if (searchClearBtn) searchClearBtn.hidden = q === '';
    if (filterStatCard) filterStatCard.classList.toggle('is-filtered', q !== '');
    invoiceCurrentPage = 1;
    applyInvoicePagination();
  }

  function openPrint(id, options) {
    options = options || {};
    if (!id) return;
    let url = 'invoice.php?id=' + encodeURIComponent(id);
    if (options.installment === 'full') {
      url += '&installment=full';
    } else {
      const installment = parseInt(options.installment, 10);
      if (installment >= 1 && installment <= 3) {
        url += '&installment=' + installment;
      }
    }
    window.open(url, '_blank', 'noopener');
  }

  function openInvoicePrintPicker(id) {
    if (!id) return;
    pendingInvoicePrintId = id;
    updateInstallmentPickerAmounts();
    const pickerEl = document.getElementById('modalPrintInstallment');
    if (!pickerEl) {
      openPrint(id, { installment: 'full' });
      return;
    }
    bootstrap.Modal.getOrCreateInstance(pickerEl).show();
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
        if (isProposalMode()) {
          openPrint(id);
        } else {
          openInvoicePrintPicker(id);
        }
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
      openInvoicePrintPicker(linkedId);
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
      openInvoicePrintPicker(Number(btn.dataset.id));
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
    markFormDirty();
    const blocks = linesBody.querySelectorAll('.invoice-cat-block');
    const last = blocks[blocks.length - 1];
    last?.querySelector('.js-cat-name')?.focus();
  });
  form?.addEventListener('input', markFormDirty);
  form?.addEventListener('change', markFormDirty);
  form?.addEventListener('submit', saveInvoice);
  searchInput?.addEventListener('input', filterRows);

  paginationListEl?.addEventListener('click', function (event) {
    const btn = event.target.closest('[data-page]');
    if (!btn || btn.closest('.page-item')?.classList.contains('disabled')) return;
    goToInvoicePage(btn.getAttribute('data-page'));
  });

  pageSizeSelectEl?.addEventListener('change', function () {
    const nextSize = parseInt(pageSizeSelectEl.value, 10);
    if (PAGE_SIZE_OPTIONS.indexOf(nextSize) === -1) return;
    invoicePageSize = nextSize;
    localStorage.setItem(PAGE_SIZE_STORAGE_KEY, String(invoicePageSize));
    invoiceCurrentPage = 1;
    applyInvoicePagination();
    scrollInvoiceListToTop();
  });

  window.addEventListener('scroll', onInvoiceListScroll, { passive: true });
  window.addEventListener('resize', scheduleScrollActiveRow);

  if (pageSizeSelectEl) {
    pageSizeSelectEl.value = String(invoicePageSize);
  }

  if (tableBody) {
    filterRows();
  }

  modalEl.querySelectorAll('input[name="invoiceFormCurrency"]').forEach(function (radio) {
    radio.addEventListener('change', setCurrencyLabels);
  });
  modalEl.querySelectorAll('input[name="invoiceFormLanguage"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
      if (languageSwitchBusy) return;
      const target = radio.value === 'ar' ? 'ar' : 'en';
      if (target === lastSavedFormLanguage) return;
      switchFormLanguage(target, lastSavedFormLanguage);
    });
  });
  fields.discount?.addEventListener('input', updateTotals);

  document.querySelectorAll('.btn-installment-print').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const raw = btn.dataset.installment || '';
      const id = pendingInvoicePrintId;
      const pickerEl = document.getElementById('modalPrintInstallment');
      if (pickerEl) {
        const instance = bootstrap.Modal.getInstance(pickerEl);
        if (instance) instance.hide();
      }
      if (id > 0) {
        if (raw === 'full') {
          openPrint(id, { installment: 'full' });
        } else {
          const installment = parseInt(raw, 10);
          if (installment >= 1 && installment <= 3) {
            openPrint(id, { installment: installment });
          }
        }
      }
      pendingInvoicePrintId = 0;
    });
  });

  fields.printBtn?.addEventListener('click', function () {
    saveThenPrint();
  });

  modalEl.addEventListener('hidden.bs.modal', function () {
    if (suppressListRefreshOnClose) {
      suppressListRefreshOnClose = false;
      cleanInvoicesListUrl();
      return;
    }

    flushAutoSaveOnClose();
  });

  window.addEventListener('beforeunload', function () {
    if (!sessionTouched || !modalEl.classList.contains('show')) return;
    const id = parseInt(fieldValue('invoiceFormId') || '0', 10);
    if (!id) return;
    clearTimeout(autoSaveTimer);
    const state = prepareState(buildState());
    const payload = JSON.stringify({ id: id, admin_csrf: csrf, state: state });
    try {
      fetch('api/invoice-save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: payload,
        credentials: 'same-origin',
        keepalive: true,
      });
    } catch (err) {
      // Best-effort save when leaving the page.
    }
  });

  const boot = window.INVOICE_LIST_BOOT;
  if (boot && boot.open === 'new') {
    openNew();
  } else if (boot && boot.open === 'edit' && boot.id) {
    openEdit(boot.id);
  }
})();
