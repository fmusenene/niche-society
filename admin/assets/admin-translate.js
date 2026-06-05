/**
 * Auto-translate English CMS fields to Arabic while typing.
 */
(function () {
    const API = 'api/translate.php';
    const DEBOUNCE_MS = 900;
    const STORAGE_KEY = 'cms_auto_translate';

    let enabled = localStorage.getItem(STORAGE_KEY) !== '0';
    const timers = new WeakMap();

    function arFieldFor(enField) {
        const name = enField.getAttribute('name') || '';
        if (!name.endsWith('_en')) return null;
        const form = enField.closest('form');
        if (!form) return null;
        return form.querySelector('[name="' + name.replace(/_en$/, '_ar') + '"]');
    }

    async function translateText(text) {
        const res = await fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ text }),
        });
        const data = await res.json();
        if (!data.ok) {
            throw new Error(data.error || 'Translation failed');
        }
        return data.translated;
    }

    function setTranslating(arField, on) {
        if (!arField) return;
        arField.classList.toggle('field-translating', on);
        arField.closest('.mb-3')?.classList.toggle('is-translating', on);
    }

    async function runTranslate(enField) {
        const arField = arFieldFor(enField);
        if (!arField || arField.dataset.manualEdit === '1') return;

        const text = enField.value.trim();
        if (!text) {
            if (!arField.dataset.manualEdit) arField.value = '';
            return;
        }

        setTranslating(arField, true);
        try {
            const translated = await translateText(text);
            if (arField.dataset.manualEdit !== '1') {
                arField.value = translated;
                arField.dataset.autoFilled = '1';
            }
        } catch (err) {
            showTranslateToast(err.message || 'Translation failed', 'danger');
        } finally {
            setTranslating(arField, false);
        }
    }

    function scheduleTranslate(enField) {
        clearTimeout(timers.get(enField));
        timers.set(
            enField,
            setTimeout(() => runTranslate(enField), DEBOUNCE_MS)
        );
    }

    function showTranslateToast(message, type) {
        let el = document.getElementById('translateToast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'translateToast';
            el.className = 'translate-toast';
            document.body.appendChild(el);
        }
        el.className = 'translate-toast translate-toast-' + (type || 'warning');
        el.textContent = message;
        el.hidden = false;
        clearTimeout(el._hideTimer);
        el._hideTimer = setTimeout(() => { el.hidden = true; }, 5000);
    }

    function bindArField(arField) {
        if (arField.dataset.translateBound) return;
        arField.dataset.translateBound = '1';
        arField.addEventListener('input', function () {
            arField.dataset.manualEdit = '1';
            delete arField.dataset.autoFilled;
        });
    }

    function bindEnField(enField) {
        if (enField.dataset.translateBound) return;
        enField.dataset.translateBound = '1';
        const arField = arFieldFor(enField);
        if (arField) bindArField(arField);

        enField.addEventListener('input', function () {
            if (!enabled) return;
            scheduleTranslate(enField);
        });

        enField.addEventListener('blur', function () {
            if (!enabled) return;
            const text = enField.value.trim();
            const ar = arFieldFor(enField);
            if (text && ar && ar.dataset.manualEdit !== '1' && !ar.value.trim()) {
                runTranslate(enField);
            }
        });
    }

    function scanForms(root) {
        root.querySelectorAll('input[name$="_en"], textarea[name$="_en"]').forEach(bindEnField);
    }

    async function translateAllInForm(form) {
        const pairs = [];
        form.querySelectorAll('input[name$="_en"], textarea[name$="_en"]').forEach(function (en) {
            const ar = arFieldFor(en);
            if (ar) {
                delete ar.dataset.manualEdit;
                pairs.push({ en, ar });
            }
        });

        for (const { en, ar } of pairs) {
            const text = en.value.trim();
            if (!text) continue;
            setTranslating(ar, true);
            try {
                ar.value = await translateText(text);
                ar.dataset.autoFilled = '1';
            } catch (err) {
                showTranslateToast(err.message, 'danger');
            } finally {
                setTranslating(ar, false);
            }
        }
        showTranslateToast('Arabic fields updated from English.', 'success');
    }

    function initToggle() {
        const toggle = document.getElementById('autoTranslateToggle');
        if (!toggle) return;

        toggle.checked = enabled;
        toggle.addEventListener('change', function () {
            enabled = toggle.checked;
            localStorage.setItem(STORAGE_KEY, enabled ? '1' : '0');
        });

        document.querySelectorAll('[data-translate-all]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const modal = btn.closest('.modal');
                const form = modal ? modal.querySelector('form') : document.querySelector('form');
                if (form) translateAllInForm(form);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        scanForms(document);
        initToggle();

        document.querySelectorAll('.modal').forEach(function (modal) {
            modal.addEventListener('shown.bs.modal', function () {
                scanForms(modal);
            });
        });
    });

    window.cmsTranslateAll = translateAllInForm;
})();
