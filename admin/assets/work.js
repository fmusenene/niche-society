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
  const translateBtns = modalEl.querySelectorAll('.work-translate-btn');
  let dirty = false;
  let saving = false;
  let translating = false;

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

  function setTranslateLoading(loading) {
    translating = loading;
    translateBtns.forEach(function (btn) {
      btn.disabled = loading;
      btn.classList.toggle('is-loading', loading);
    });
    modalEl.querySelector('.work-editor-wrap')?.classList.toggle('is-translating', loading);
  }

  function collectTranslatableBlocks() {
    if (!editorEl) return [];
    const blocks = [];
    editorEl.querySelectorAll('p,h1,h2,h3,h4,li,blockquote,div').forEach(function (el) {
      if (el.tagName === 'DIV' && el.querySelector('p,h1,h2,h3,h4,li,blockquote,div')) {
        return;
      }
      const text = String(el.textContent || '').trim();
      if (text) {
        blocks.push({
          el: el,
          text: text,
          hasInline: !!el.querySelector('b,strong,i,em,u,s,strike'),
        });
      }
    });
    if (!blocks.length) {
      const text = String(editorEl.textContent || '').trim();
      if (text) blocks.push({ el: editorEl, text: text, isRoot: true });
    }
    return blocks;
  }

  function collectTextNodesInElement(root) {
    const nodes = [];
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
      acceptNode: function (node) {
        if (!node.textContent || !String(node.textContent).trim()) {
          return NodeFilter.FILTER_REJECT;
        }
        return NodeFilter.FILTER_ACCEPT;
      },
    });
    let node;
    while ((node = walker.nextNode())) {
      nodes.push(node);
    }
    return nodes;
  }

  function preserveWhitespace(original, translated) {
    const source = String(original || '');
    const lead = source.match(/^\s*/)[0];
    const trail = source.match(/\s*$/)[0];
    return lead + String(translated || '').trim() + trail;
  }

  function chunkArray(items, size) {
    const chunks = [];
    for (let i = 0; i < items.length; i += size) {
      chunks.push(items.slice(i, i + size));
    }
    return chunks;
  }

  async function translateManyTexts(texts, sourceLang, targetLang) {
    const results = texts.slice();
    const batches = chunkArray(texts, 40);
    let offset = 0;

    for (let b = 0; b < batches.length; b += 1) {
      const batch = batches[b];
      const res = await fetch('api/translate-batch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          texts: batch,
          from: sourceLang,
          to: targetLang,
        }),
        credentials: 'same-origin',
      });
      const data = await res.json();
      if (!res.ok || !data.ok) {
        throw new Error(data.error || 'Translation failed');
      }
      const translations = data.translations || [];
      translations.forEach(function (value, index) {
        results[offset + index] = value;
      });
      offset += batch.length;
    }

    return results;
  }

  async function translateEditorContent(targetLang) {
    if (!editorEl || translating) return;

    const lang = targetLang === 'ar' ? 'ar' : 'en';
    const sourceLang = 'auto';
    const blocks = collectTranslatableBlocks();

    if (!blocks.length) {
      showError('Nothing to translate. Type some content first.');
      return;
    }

    showError('');
    setTranslateLoading(true);

    try {
      const payloads = blocks.map(function (block) { return block.text; });
      const translations = await translateManyTexts(payloads, sourceLang, lang);

      blocks.forEach(function (block, index) {
        const translated = translations[index] ?? block.text;
        if (block.isRoot) {
          setEditorHtml(plainTextToHtml(translated));
          return;
        }
        if (block.hasInline) {
          const nodes = collectTextNodesInElement(block.el);
          if (nodes.length) {
            nodes[0].textContent = translated;
            for (let i = 1; i < nodes.length; i += 1) {
              nodes[i].textContent = '';
            }
          }
          return;
        }
        block.el.textContent = translated;
      });

      const radio = modalEl.querySelector('input[name="workFormLanguage"][value="' + lang + '"]');
      if (radio) radio.checked = true;
      applyEditorDirection(lang);
      markDirty();

      if (window.adminNotify) {
        window.adminNotify.success(lang === 'ar' ? 'Translated to Arabic' : 'Translated to English');
      }
    } catch (err) {
      showError(err.message || 'Translation failed');
    } finally {
      setTranslateLoading(false);
    }
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
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creatingâ€¦';
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

  translateBtns.forEach(function (btn) {
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      const target = btn.getAttribute('data-target') === 'ar' ? 'ar' : 'en';
      translateEditorContent(target);
    });
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
