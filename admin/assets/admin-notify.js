(function (global) {
  'use strict';

  const ICONS = {
    success: 'bi-check-circle-fill',
    danger: 'bi-x-circle-fill',
    error: 'bi-x-circle-fill',
    warning: 'bi-exclamation-triangle-fill',
    info: 'bi-info-circle-fill',
  };

  function escapeHtml(text) {
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function ensureToastStack() {
    let stack = document.getElementById('adminToastStack');
    if (!stack) {
      stack = document.createElement('div');
      stack.id = 'adminToastStack';
      stack.className = 'admin-toast-stack';
      stack.setAttribute('aria-live', 'polite');
      stack.setAttribute('aria-atomic', 'false');
      document.body.appendChild(stack);
    }
    return stack;
  }

  function toast(message, type, options) {
    const opts = options || {};
    const variant = type === 'error' ? 'danger' : (type || 'info');
    const duration = opts.duration !== undefined ? opts.duration : 6000;
    const stack = ensureToastStack();
    const el = document.createElement('div');
    el.className = 'admin-toast admin-toast--' + variant;
    el.setAttribute('role', 'alert');

    const icon = ICONS[variant] || ICONS.info;
    el.innerHTML =
      '<div class="admin-toast__accent" aria-hidden="true"></div>' +
      '<div class="admin-toast__icon"><i class="bi ' + icon + '" aria-hidden="true"></i></div>' +
      '<div class="admin-toast__content">' +
        (opts.title ? '<div class="admin-toast__title">' + escapeHtml(opts.title) + '</div>' : '') +
        '<div class="admin-toast__message">' + escapeHtml(message) + '</div>' +
      '</div>' +
      '<button type="button" class="admin-toast__close" aria-label="Dismiss">' +
        '<i class="bi bi-x-lg" aria-hidden="true"></i>' +
      '</button>' +
      (duration > 0 ? '<span class="admin-toast__progress" style="animation-duration:' + duration + 'ms"></span>' : '');

    stack.appendChild(el);
    requestAnimationFrame(function () {
      el.classList.add('is-visible');
    });

    function dismiss() {
      if (el.classList.contains('is-hiding')) return;
      el.classList.remove('is-visible');
      el.classList.add('is-hiding');
      window.setTimeout(function () {
        el.remove();
      }, 320);
    }

    el.querySelector('.admin-toast__close')?.addEventListener('click', dismiss);
    if (duration > 0) {
      window.setTimeout(dismiss, duration);
    }

    return { dismiss: dismiss };
  }

  function ensureConfirmRoot() {
    let root = document.getElementById('adminConfirmRoot');
    if (!root) {
      root = document.createElement('div');
      root.id = 'adminConfirmRoot';
      document.body.appendChild(root);
    }
    return root;
  }

  function confirmDialog(options) {
    const opts = Object.assign({
      title: 'Confirm',
      message: '',
      confirmLabel: 'Confirm',
      cancelLabel: 'Cancel',
      variant: 'primary',
    }, options || {});

    return new Promise(function (resolve) {
      const root = ensureConfirmRoot();
      const overlay = document.createElement('div');
      overlay.className = 'admin-confirm-overlay';
      overlay.setAttribute('role', 'presentation');

      const variantClass = 'admin-confirm--' + (opts.variant || 'primary');
      overlay.innerHTML =
        '<div class="admin-confirm ' + variantClass + '" role="dialog" aria-modal="true" aria-labelledby="adminConfirmTitle">' +
          '<div class="admin-confirm__header">' +
            '<div class="admin-confirm__icon"><i class="bi bi-receipt-cutoff" aria-hidden="true"></i></div>' +
            '<h2 class="admin-confirm__title" id="adminConfirmTitle">' + escapeHtml(opts.title) + '</h2>' +
          '</div>' +
          '<div class="admin-confirm__body">' + escapeHtml(opts.message) + '</div>' +
          '<div class="admin-confirm__actions">' +
            '<button type="button" class="btn btn-light admin-confirm__cancel">' + escapeHtml(opts.cancelLabel) + '</button>' +
            '<button type="button" class="btn btn-primary admin-confirm__ok">' + escapeHtml(opts.confirmLabel) + '</button>' +
          '</div>' +
        '</div>';

      root.appendChild(overlay);
      requestAnimationFrame(function () {
        overlay.classList.add('is-visible');
      });

      const dialog = overlay.querySelector('.admin-confirm');
      const btnOk = overlay.querySelector('.admin-confirm__ok');
      const btnCancel = overlay.querySelector('.admin-confirm__cancel');

      function close(result) {
        overlay.classList.remove('is-visible');
        window.setTimeout(function () {
          overlay.remove();
        }, 220);
        resolve(result);
      }

      btnOk.addEventListener('click', function () { close(true); });
      btnCancel.addEventListener('click', function () { close(false); });
      overlay.addEventListener('click', function (event) {
        if (event.target === overlay) close(false);
      });

      document.addEventListener('keydown', function onKey(event) {
        if (event.key === 'Escape') {
          document.removeEventListener('keydown', onKey);
          close(false);
        }
      });

      btnOk.focus();
    });
  }

  global.AdminNotify = {
    toast: toast,
    success: function (message, options) { return toast(message, 'success', options); },
    error: function (message, options) { return toast(message, 'danger', options); },
    info: function (message, options) { return toast(message, 'info', options); },
    warning: function (message, options) { return toast(message, 'warning', options); },
    confirm: confirmDialog,
  };
})(window);
