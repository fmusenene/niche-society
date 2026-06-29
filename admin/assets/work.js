(function () {
  const csrfEl = document.getElementById('adminCsrfToken');
  if (!csrfEl) return;

  const csrf = csrfEl.dataset.token || '';
  const modalEl = document.getElementById('modalWorkForm');
  if (!modalEl) return;

  const modal = new bootstrap.Modal(modalEl);
  const deleteModalEl = document.getElementById('modalDeleteWork');
  const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
  const form = document.getElementById('formWork');
  const errorEl = document.getElementById('workFormError');
  const saveBtn = document.getElementById('btnWorkSave');
  const printBtn = document.getElementById('btnWorkPrint');
  const editorEl = document.getElementById('workFormBody');
  const headingSelect = document.getElementById('workHeadingSelect');
  let dirty = false;
  let saving = false;

  function field(id) {
    return document.getElementById(id);
  }

  function fieldValue(id) {
    const el = field(id);
    return el ? String(el.value ?? '').trim() : '';
  }

  function setField(id, value) {
    const el = field(id);
    if (el) el.value = value ?? '';
  }

  function showError(msg) {
    if (!errorEl) return;
    if (!msg) {
      errorEl.classList.add('d-none');
      errorEl.textContent = '';
      return;
    }
    errorEl.textContent = msg;
    errorEl.classList.remove('d-none');
  }

  function getLanguage() {
    const radio = modalEl.querySelector('input[name="workFormLanguage"]:checked');
    return radio && radio.value === 'ar' ? 'ar' : 'en';
  }

  function applyEditorDirection(lang) {
    if (!editorEl) return;
    const useAr = lang === 'ar';
    editorEl.dir = useAr ? 'rtl' : 'ltr';
    editorEl.style.textAlign = useAr ? 'right' : 'left';
  }

  function markDirty() {
    dirty = true;
    if (saveBtn) saveBtn.disabled = false;
    if (printBtn) printBtn.disabled = !fieldValue('workFormId');
  }

  function editorHtml() {
    return editorEl ? editorEl.innerHTML.trim() : '';
  }

  function setEditorHtml(html) {
    if (!editorEl) return;
    editorEl.innerHTML = html || '';
    if (!editorEl.textContent.trim() && !editorEl.querySelector('img,ul,ol,h1,h2,h3,h4')) {
      editorEl.innerHTML = '';
    }
  }

  function plainTextToHtml(text) {
    if (!text || text.indexOf('<') !== -1) {
      return text || '';
    }
    return text.split(/\r\n|\r|\n/).map(function (line) {
      const trimmed = line.trim();
      if (trimmed === '') {
        return '<p><br></p>';
      }
      return '<p>' + escapeHtml(trimmed) + '</p>';
    }).join('');
  }

  function escapeHtml(text) {
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function readDocument() {
    return {
      body: editorHtml(),
      language: getLanguage(),
    };
  }

  function populateDocument(doc) {
    setField('workFormId', doc.id ? String(doc.id) : '');
    const lang = doc.language === 'ar' ? 'ar' : 'en';
    const radio = modalEl.querySelector('input[name="workFormLanguage"][value="' + lang + '"]');
    if (radio) radio.checked = true;
    applyEditorDirection(lang);
    setEditorHtml(plainTextToHtml(doc.body || ''));
    dirty = false;
    showError('');
    if (saveBtn) saveBtn.disabled = !doc.id;
    if (printBtn) printBtn.disabled = !doc.id;
  }

  function openPrint(id) {
    if (!id) return;
    window.open('work.php?id=' + encodeURIComponent(String(id)) + '&print=1', '_blank', 'noopener');
  }

  function saveDocument(options) {
    const opts = options || {};
    const id = parseInt(fieldValue('workFormId') || '0', 10);
    if (!id || saving) {
      return Promise.reject(new Error('Save the document first.'));
    }

    saving = true;
    if (saveBtn) saveBtn.disabled = true;

    return fetch('api/work-save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        admin_csrf: csrf,
        id: id,
        document: readDocument(),
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
        dirty = false;
        if (opts.reload) {
          window.location.href = '?section=work&edit=' + id;
        }
        return result.data;
      })
      .finally(function () {
        saving = false;
        if (saveBtn) saveBtn.disabled = !dirty && !!id;
      });
  }

  function createDocument() {
    showError('');
    if (saveBtn) {
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating…';
    }

    fetch('api/work-create.php', {
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
          throw new Error((result.data && result.data.error) || 'Could not create document');
        }
        const doc = result.data.document || {};
        doc.id = result.data.id;
        populateDocument(doc);
        markDirty();
        modal.show();
        editorEl?.focus();
      })
      .catch(function (err) {
        if (window.adminNotify) {
          window.adminNotify.error(err.message || 'Could not create document');
        } else {
          alert(err.message || 'Could not create document');
        }
      })
      .finally(function () {
        if (saveBtn) {
          saveBtn.innerHTML = '<i class="bi bi-check-lg"></i> Save';
          saveBtn.disabled = !dirty;
        }
      });
  }

  function loadDocument(id) {
    fetch('api/work-get.php?id=' + encodeURIComponent(String(id)), { credentials: 'same-origin' })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.data.ok) {
          throw new Error((result.data && result.data.error) || 'Document not found');
        }
        populateDocument(result.data.document || {});
        modal.show();
      })
      .catch(function (err) {
        if (window.adminNotify) {
          window.adminNotify.error(err.message || 'Could not load document');
        } else {
          alert(err.message || 'Could not load document');
        }
      });
  }

  function execFormat(cmd, value) {
    if (!editorEl) return;
    editorEl.focus();
    try {
      document.execCommand(cmd, false, value || null);
    } catch (e) {
      /* ignore unsupported commands */
    }
    markDirty();
  }

  modalEl.querySelectorAll('.work-cmd-btn').forEach(function (btn) {
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      const cmd = btn.getAttribute('data-cmd');
      if (cmd) execFormat(cmd);
    });
  });

  headingSelect?.addEventListener('change', function () {
    const tag = headingSelect.value || 'p';
    execFormat('formatBlock', tag === 'p' ? 'p' : tag);
    headingSelect.value = 'p';
  });

  modalEl.querySelectorAll('input[name="workFormLanguage"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
      applyEditorDirection(getLanguage());
      markDirty();
    });
  });

  editorEl?.addEventListener('input', markDirty);
  editorEl?.addEventListener('blur', markDirty);

  document.getElementById('btnNewWork')?.addEventListener('click', createDocument);
  document.getElementById('btnNewWorkEmpty')?.addEventListener('click', createDocument);

  document.querySelectorAll('.btn-edit-work').forEach(function (btn) {
    btn.addEventListener('click', function () {
      loadDocument(parseInt(btn.dataset.id || '0', 10));
    });
  });

  document.querySelectorAll('.btn-print-work').forEach(function (btn) {
    btn.addEventListener('click', function () {
      openPrint(parseInt(btn.dataset.id || '0', 10));
    });
  });

  document.querySelectorAll('.btn-delete-work').forEach(function (btn) {
    btn.addEventListener('click', function () {
      setField('deleteWorkId', btn.dataset.id || '');
      const titleEl = document.getElementById('deleteWorkTitle');
      if (titleEl) titleEl.textContent = btn.dataset.title || 'this document';
      deleteModal?.show();
    });
  });

  document.getElementById('btnConfirmDeleteWork')?.addEventListener('click', function () {
    const id = parseInt(fieldValue('deleteWorkId') || '0', 10);
    if (!id) return;
    fetch('api/work-delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ admin_csrf: csrf, id: id }),
      credentials: 'same-origin',
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.data.ok) {
          throw new Error((result.data && result.data.error) || 'Delete failed');
        }
        deleteModal?.hide();
        window.location.href = '?section=work';
      })
      .catch(function (err) {
        if (window.adminNotify) {
          window.adminNotify.error(err.message || 'Delete failed');
        } else {
          alert(err.message || 'Delete failed');
        }
      });
  });

  form?.addEventListener('submit', function (event) {
    event.preventDefault();
    saveDocument({ reload: true })
      .then(function () {
        if (window.adminNotify) {
          window.adminNotify.success('Document saved');
        }
      })
      .catch(function (err) {
        showError(err.message || 'Save failed');
      });
  });

  printBtn?.addEventListener('click', function () {
    const id = parseInt(fieldValue('workFormId') || '0', 10);
    if (!id) return;
    const runPrint = function () {
      openPrint(id);
    };
    if (dirty) {
      saveDocument().then(runPrint).catch(function (err) {
        showError(err.message || 'Save before printing failed');
      });
    } else {
      runPrint();
    }
  });

  modalEl.addEventListener('hidden.bs.modal', function () {
    if (location.search.match(/[?&]edit=/)) {
      history.replaceState(null, '', '?section=work');
    }
  });

  const boot = window.WORK_BOOT || {};
  if (boot.editId) {
    loadDocument(parseInt(String(boot.editId), 10));
  }
})();


// Work Documents JavaScript functionality
(function() {
    'use strict';

    const WORK_ENDPOINT = 'ajax/work-documents.php';
    let currentWorkId = 0;
    let workEditor = null;

    // Initialize
    function initWorkModule() {
        // Get the work editor element
        workEditor = document.getElementById('workFormBody');
        if (!workEditor) return;

        // Set up event listeners
        setupNewDocumentButtons();
        setupEditButtons();
        setupDeleteButtons();
        setupPrintButtons();
        setupToolbarCommands();
        setupHeadingSelect();
        setupLanguageToggle();
        setupFormSubmit();
        setupDeleteConfirm();

        // Auto-open edit if specified
        if (window.WORK_BOOT && window.WORK_BOOT.editId > 0) {
            loadAndOpenDocument(window.WORK_BOOT.editId);
        }
    }

    function setupNewDocumentButtons() {
        const buttons = document.querySelectorAll('#btnNewWork, #btnNewWorkEmpty');
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                createNewDocument();
            });
        });
    }

    function setupEditButtons() {
        document.querySelectorAll('.btn-edit-work').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.dataset.id);
                loadAndOpenDocument(id);
            });
        });
    }

    function setupDeleteButtons() {
        document.querySelectorAll('.btn-delete-work').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.dataset.id);
                const title = this.dataset.title || 'Untitled';
                document.getElementById('deleteWorkId').value = id;
                document.getElementById('deleteWorkTitle').textContent = title;
                const modal = new bootstrap.Modal(document.getElementById('modalDeleteWork'));
                modal.show();
            });
        });
    }

    function setupPrintButtons() {
        document.querySelectorAll('.btn-print-work').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.dataset.id);
                window.open(`work.php?id=${id}&print=1`, '_blank');
            });
        });
    }

    function setupToolbarCommands() {
        document.querySelectorAll('.work-cmd-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!workEditor) return;
                const cmd = this.dataset.cmd;
                document.execCommand(cmd, false, null);
                workEditor.focus();
                checkContent();
            });
        });
    }

    function setupHeadingSelect() {
        const select = document.getElementById('workHeadingSelect');
        if (!select) return;
        select.addEventListener('change', function() {
            if (!workEditor) return;
            const value = this.value;
            if (value === 'p') {
                document.execCommand('formatBlock', false, '<p>');
            } else {
                document.execCommand('formatBlock', false, '<' + value + '>');
            }
            workEditor.focus();
            checkContent();
        });
    }

    function setupLanguageToggle() {
        const radios = document.querySelectorAll('input[name="workFormLanguage"]');
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (!workEditor) return;
                const dir = this.value === 'ar' ? 'rtl' : 'ltr';
                workEditor.dir = dir;
            });
        });
    }

    function setupFormSubmit() {
        const form = document.getElementById('formWork');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveDocument();
        });
    }

    function setupDeleteConfirm() {
        const btn = document.getElementById('btnConfirmDeleteWork');
        if (!btn) return;
        btn.addEventListener('click', function() {
            const id = parseInt(document.getElementById('deleteWorkId').value);
            if (id > 0) {
                deleteDocument(id);
            }
        });
    }

    function createNewDocument() {
        fetch(WORK_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=create'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.id) {
                currentWorkId = data.id;
                openModalWithData({ id: data.id, body: '', language: 'en' });
            } else {
                showError(data.error || 'Failed to create document');
            }
        })
        .catch(error => {
            showError('Network error: ' + error.message);
        });
    }

    function loadAndOpenDocument(id) {
        fetch(`${WORK_ENDPOINT}?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.document) {
                currentWorkId = id;
                openModalWithData(data.document);
            } else {
                showError(data.error || 'Failed to load document');
            }
        })
        .catch(error => {
            showError('Network error: ' + error.message);
        });
    }

    function openModalWithData(data) {
        const modal = new bootstrap.Modal(document.getElementById('modalWorkForm'));
        
        // Set document ID
        document.getElementById('workFormId').value = data.id || 0;
        currentWorkId = data.id || 0;

        // Set metadata fields
        document.getElementById('workFormTitle').value = data.title || '';
        document.getElementById('workFormSubject').value = data.subject || '';
        document.getElementById('workFormOfferDate').value = data.offer_date || '';
        document.getElementById('workFormPreparedBy').value = data.prepared_by || '';
        document.getElementById('workFormTel').value = data.tel || '';

        // Set body content
        if (workEditor) {
            workEditor.innerHTML = data.body || '';
        }

        // Set language
        const lang = data.language || 'en';
        const radio = document.getElementById(`workLang${lang === 'ar' ? 'Ar' : 'En'}`);
        if (radio) {
            radio.checked = true;
            if (workEditor) {
                workEditor.dir = lang === 'ar' ? 'rtl' : 'ltr';
            }
        }

        // Enable save/print buttons
        document.getElementById('btnWorkSave').disabled = false;
        document.getElementById('btnWorkPrint').disabled = false;

        // Hide any previous errors
        document.getElementById('workFormError').classList.add('d-none');

        modal.show();
        if (workEditor) {
            setTimeout(() => workEditor.focus(), 300);
        }
        checkContent();
    }

    function saveDocument() {
        const id = parseInt(document.getElementById('workFormId').value);
        if (!id) {
            showError('No document ID found');
            return;
        }

        const title = document.getElementById('workFormTitle').value;
        const subject = document.getElementById('workFormSubject').value;
        const offerDate = document.getElementById('workFormOfferDate').value;
        const preparedBy = document.getElementById('workFormPreparedBy').value;
        const tel = document.getElementById('workFormTel').value;
        const body = workEditor ? workEditor.innerHTML : '';
        const language = document.querySelector('input[name="workFormLanguage"]:checked')?.value || 'en';

        const data = new URLSearchParams({
            action: 'save',
            id: id,
            title: title,
            subject: subject,
            offer_date: offerDate,
            prepared_by: preparedBy,
            tel: tel,
            body: body,
            language: language
        });

        fetch(WORK_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Close modal and reload page to show changes
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalWorkForm'));
                if (modal) modal.hide();
                location.reload();
            } else {
                showError(result.error || 'Failed to save document');
            }
        })
        .catch(error => {
            showError('Network error: ' + error.message);
        });
    }

    function deleteDocument(id) {
        const data = new URLSearchParams({
            action: 'delete',
            id: id
        });

        fetch(WORK_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalDeleteWork'));
                if (modal) modal.hide();
                location.reload();
            } else {
                alert('Failed to delete document: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Network error: ' + error.message);
        });
    }

    function checkContent() {
        // Enable/disable save button based on content
        const saveBtn = document.getElementById('btnWorkSave');
        if (saveBtn) {
            const hasContent = workEditor && workEditor.textContent.trim() !== '';
            saveBtn.disabled = !hasContent;
        }
    }

    function showError(message) {
        const errorEl = document.getElementById('workFormError');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('d-none');
        }
    }

    // Check content on input
    if (document.getElementById('workFormBody')) {
        document.getElementById('workFormBody').addEventListener('input', checkContent);
        document.getElementById('workFormBody').addEventListener('keyup', checkContent);
        document.getElementById('workFormBody').addEventListener('paste', function() {
            setTimeout(checkContent, 100);
        });
    }

    // Print button
    document.getElementById('btnWorkPrint')?.addEventListener('click', function() {
        const id = currentWorkId || parseInt(document.getElementById('workFormId').value);
        if (id) {
            window.open(`work.php?id=${id}&print=1`, '_blank');
        }
    });

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWorkModule);
    } else {
        initWorkModule();
    }

})();