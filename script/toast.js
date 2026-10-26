/* ============================================
   QIEOS TOAST — JS Engine
   Drop-in replacement for Swal.fire()
   ============================================ */

(function () {
    // Prevent double-init
    if (window.QToast) return;

    // Inject container once
    function ensureContainer() {
        let c = document.getElementById('q-toast-container');
        if (!c) {
            c = document.createElement('div');
            c.id = 'q-toast-container';
            c.className = 'q-toast-container';
            document.body.appendChild(c);
        }
        return c;
    }

    // Icon map
    const ICONS = {
        success: 'fa-check',
        error: 'fa-xmark',
        warning: 'fa-exclamation',
        info: 'fa-info',
    };

    const DURATIONS = {
        success: 3000,
        error: 4500,
        warning: 4000,
        info: 3500,
    };

    /**
     * QToast(title, message?, type?, duration?)
     * or
     * QToast({ title, message, type, duration, onClose })
     */
    function QToast(a, b, c, d) {
        let opts = {};
        if (typeof a === 'object' && a !== null) {
            opts = a;
        } else {
            opts.title = a || '';
            opts.message = b || '';
            opts.type = c || 'info';
            opts.duration = d;
        }

        const title = opts.title || '';
        const message = opts.message || '';
        let type = opts.type || 'info';

        // Normalize: "error" string becomes type "error", etc.
        if (['success', 'error', 'warning', 'info'].indexOf(type) === -1) {
            type = 'info';
        }

        const duration = opts.duration || DURATIONS[type] || 3500;
        const onClose = opts.onClose || null;

        const container = ensureContainer();

        // Create toast element
        const el = document.createElement('div');
        el.className = 'q-toast toast-' + type;

        el.innerHTML =
            '<div class="q-toast-icon"><i class="fas ' + (ICONS[type] || ICONS.info) + '"></i></div>' +
            '<div class="q-toast-body">' +
                (title ? '<div class="q-toast-title">' + escapeHtml(title) + '</div>' : '') +
                (message ? '<div class="q-toast-msg">' + escapeHtml(message) + '</div>' : '') +
            '</div>' +
            '<button class="q-toast-close" aria-label="Close"><i class="fas fa-xmark"></i></button>' +
            '<div class="q-toast-progress" style="animation-duration:' + duration + 'ms"></div>';

        container.appendChild(el);

        // Trigger show animation
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                el.classList.add('show');
            });
        });

        // Auto-dismiss
        let timer = setTimeout(function () { dismiss(); }, duration);

        // Close button
        el.querySelector('.q-toast-close').addEventListener('click', function () {
            clearTimeout(timer);
            dismiss();
        });

        function dismiss() {
            if (el.classList.contains('hide')) return;
            el.classList.remove('show');
            el.classList.add('hide');
            setTimeout(function () {
                if (el.parentNode) el.parentNode.removeChild(el);
                if (onClose) onClose();
            }, 400);
        }

        return { dismiss: dismiss };
    }

    // Expose globally
    window.QToast = QToast;

    function escapeHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }
})();

/* ============================================
   QIEOS CONFIRM DIALOG
   QConfirm(title, message, opts?).then(ok => {...})
   opts: { confirmText, cancelText, icon, confirmClass, iconClass }
   ============================================ */
(function () {
    if (window.QConfirm) return;

    function QConfirm(title, message, opts) {
        opts = opts || {};
        var confirmText = opts.confirmText || 'Ya';
        var cancelText = opts.cancelText || 'Batal';
        var icon = opts.icon || 'fa-circle-question';
        var confirmClass = opts.confirmClass || 'q-confirm-btn-success';
        var iconClass = opts.iconClass || 'q-confirm-icon-info';

        return new Promise(function (resolve) {
            var overlay = document.createElement('div');
            overlay.className = 'q-confirm-overlay';
            overlay.innerHTML =
                '<div class="q-confirm-card">' +
                    '<div class="q-confirm-icon ' + iconClass + '"><i class="fas ' + icon + '"></i></div>' +
                    '<div class="q-confirm-title">' + escapeHtml(title) + '</div>' +
                    '<div class="q-confirm-msg">' + escapeHtml(message) + '</div>' +
                    '<div class="q-confirm-actions">' +
                        '<button class="q-confirm-btn q-confirm-btn-cancel" data-action="cancel">' + escapeHtml(cancelText) + '</button>' +
                        '<button class="q-confirm-btn ' + confirmClass + '" data-action="confirm">' + escapeHtml(confirmText) + '</button>' +
                    '</div>' +
                '</div>';

            document.body.appendChild(overlay);

            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    overlay.classList.add('show');
                });
            });

            function close(result) {
                overlay.classList.remove('show');
                setTimeout(function () {
                    if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
                    resolve(result);
                }, 300);
            }

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) close(false);
            });

            overlay.querySelector('[data-action="cancel"]').addEventListener('click', function () {
                close(false);
            });

            overlay.querySelector('[data-action="confirm"]').addEventListener('click', function () {
                close(true);
            });

            function escapeHtml(str) {
                var d = document.createElement('div');
                d.appendChild(document.createTextNode(str || ''));
                return d.innerHTML;
            }
        });
    }

    window.QConfirm = QConfirm;
})();
