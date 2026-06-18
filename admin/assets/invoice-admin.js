(function () {
  const cfg = window.INVOICE_CONFIG;
  if (!cfg || !cfg.id) return;

  const saveUrl = cfg.saveUrl;
  const csrf = cfg.csrf;
  const statusEl = document.getElementById('invoice-save-status');
  let saveTimer = null;
  let saving = false;
  let queuedState = null;

  function setStatus(text, kind) {
    if (!statusEl) return;
    statusEl.textContent = text;
    statusEl.classList.remove('is-error', 'is-success', 'is-pending');
    if (kind) statusEl.classList.add(kind);
  }

  function notifyParent(data) {
    if (!cfg.embed || window.parent === window) return;
    try {
      window.parent.postMessage({ type: 'invoice-saved', data: data || {} }, window.location.origin);
    } catch (err) {
      /* ignore */
    }
  }

  function buildPayload(state) {
    return {
      id: cfg.id,
      admin_csrf: csrf,
      state: state,
    };
  }

  function persist(state, manual) {
    if (!state) return;
    queuedState = state;
    if (saveTimer) clearTimeout(saveTimer);

    const run = function () {
      if (saving) return;
      const payloadState = queuedState;
      queuedState = null;
      if (!payloadState) return;

      saving = true;
      setStatus('Saving…', 'is-pending');

      fetch(saveUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(buildPayload(payloadState)),
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
          setStatus('Saved', 'is-success');
          if (result.data.subject) {
            document.title = result.data.subject + ' — ' + (result.data.invoice_number || cfg.invoiceNumber || '') + ' — Niche Society';
          }
          notifyParent(result.data);
        })
        .catch(function (err) {
          setStatus(err.message || 'Save failed', 'is-error');
        })
        .finally(function () {
          saving = false;
          if (queuedState) {
            run();
          }
        });
    };

    if (manual) {
      run();
      return;
    }

    saveTimer = setTimeout(run, 800);
  }

  window.invoiceAdminQueueSave = persist;

  const saveBtn = document.getElementById('btn-save-invoice');
  if (saveBtn) {
    saveBtn.addEventListener('click', function (event) {
      event.preventDefault();
      if (typeof window.getInvoiceEditorState === 'function') {
        persist(window.getInvoiceEditorState(), true);
      }
    });
  }

  window.addEventListener('beforeunload', function (event) {
    if (queuedState || saving) {
      event.preventDefault();
      event.returnValue = '';
    }
  });
})();
