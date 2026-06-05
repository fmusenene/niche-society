/**
 * Service image upload in admin modal
 */
(function () {
    const UPLOAD_API = 'api/upload-image.php';

    function getSiteBase() {
        const el = document.getElementById('adminSiteUrl');
        return el ? el.getAttribute('data-base') || '' : '';
    }

    function imageUrl(path) {
        if (!path) return '';
        if (/^https?:\/\//i.test(path)) return path;
        const base = getSiteBase().replace(/\/$/, '');
        const p = path.replace(/^\//, '');
        return base + '/' + p;
    }

    function initServiceImageUpload(modal) {
        if (modal.dataset.uploadBound === '1') {
            const pathInput = modal.querySelector('#serviceImagePath');
            const preview = modal.querySelector('#serviceImagePreview');
            if (pathInput && preview) {
                const path = pathInput.value.trim();
                if (path) {
                    preview.src = imageUrl(path);
                    preview.hidden = false;
                }
            }
            return;
        }
        modal.dataset.uploadBound = '1';

        const fileInput = modal.querySelector('#serviceImageFile');
        const pathInput = modal.querySelector('#serviceImagePath');
        const preview = modal.querySelector('#serviceImagePreview');
        const previewWrap = modal.querySelector('#serviceImagePreviewWrap');
        const statusEl = modal.querySelector('#serviceImageUploadStatus');
        const clearBtn = modal.querySelector('#serviceImageClear');
        const slugInput = modal.querySelector('[name="slug"]');

        if (!fileInput || !pathInput || !preview) return;

        function setPreview(path) {
            if (path) {
                preview.src = imageUrl(path);
                preview.hidden = false;
                if (previewWrap) previewWrap.hidden = false;
                if (clearBtn) clearBtn.hidden = false;
            } else {
                preview.removeAttribute('src');
                preview.hidden = true;
                if (previewWrap) previewWrap.hidden = true;
                if (clearBtn) clearBtn.hidden = true;
            }
        }

        function setStatus(msg, type) {
            if (!statusEl) return;
            statusEl.textContent = msg || '';
            statusEl.className = 'hint mt-1 service-upload-status' + (type ? ' text-' + type : '');
        }

        setPreview(pathInput.value.trim());

        pathInput.addEventListener('change', function () {
            setPreview(pathInput.value.trim());
        });

        const manualInput = modal.querySelector('#serviceImagePathManual');
        const applyBtn = modal.querySelector('#serviceImagePathApply');

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                pathInput.value = '';
                fileInput.value = '';
                if (manualInput) manualInput.value = '';
                setPreview('');
                setStatus('');
            });
        }

        if (applyBtn && manualInput) {
            applyBtn.addEventListener('click', function () {
                pathInput.value = manualInput.value.trim();
                setPreview(pathInput.value);
                setStatus(pathInput.value ? 'Path applied.' : '', 'success');
            });
        }

        fileInput.addEventListener('change', async function () {
            const file = fileInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);
            const csrfEl = document.getElementById('adminCsrfToken');
            if (csrfEl && csrfEl.getAttribute('data-token')) {
                formData.append('admin_csrf', csrfEl.getAttribute('data-token'));
            }
            if (slugInput && slugInput.value.trim()) {
                formData.append('slug', slugInput.value.trim());
            }

            setStatus('Uploading…', 'secondary');
            fileInput.disabled = true;

            try {
                const res = await fetch(UPLOAD_API, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData,
                });
                const data = await res.json();
                if (!data.ok) {
                    throw new Error(data.error || 'Upload failed');
                }
                pathInput.value = data.path;
                if (manualInput) manualInput.value = data.path;
                setPreview(data.path);
                setStatus('Image uploaded. Save the service to keep it.', 'success');
            } catch (err) {
                setStatus(err.message || 'Upload failed', 'danger');
                fileInput.value = '';
            } finally {
                fileInput.disabled = false;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalService');
        if (modal) {
            initServiceImageUpload(modal);
            modal.addEventListener('shown.bs.modal', function () {
                initServiceImageUpload(modal);
            });
        }
    });
})();
