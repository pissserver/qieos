(function () {
    var log = [];
    function L(s) { log.push(s); }
    function flush() {
        var pre = document.getElementById('s2out');
        if (!pre) {
            pre = document.createElement('pre');
            pre.id = 's2out';
            pre.style.cssText = 'position:fixed;left:0;top:0;z-index:99999;background:#000;color:#0f0;font-size:12px;padding:8px;max-width:100%;white-space:pre-wrap';
            document.body.appendChild(pre);
        }
        pre.textContent = log.join('\n');
    }
    function wait(ms) { return new Promise(function (r) { setTimeout(r, ms); }); }
    window.addEventListener('error', function (e) { L('!! ERROR ' + e.message + ' @' + e.lineno); flush(); });
    window.addEventListener('unhandledrejection', function (e) { L('!! REJECT ' + (e.reason && e.reason.message ? e.reason.message : e.reason)); flush(); });
    window.addEventListener('scroll', function () {
        L('   [scroll] win=' + Math.round(window.pageYOffset) + ' modal=' +
            Math.round((document.getElementById('combineModal') || { scrollTop: 0 }).scrollTop || 0));
    }, true);

    function dd() { return document.querySelector('.select2-dropdown'); }
    function ddOpen() {
        var d = dd();
        if (!d) return false;
        var cs = window.getComputedStyle(d);
        return cs.display !== 'none' && cs.visibility !== 'hidden' && d.offsetParent !== null;
    }
    function hitTest(el) {
        var r = el.getBoundingClientRect();
        var top = document.elementFromPoint(Math.round(r.left + r.width / 2), Math.round(r.top + r.height / 2));
        return (el === top) ? 'OK' : 'BLOCKED by ' + (top ? (top.className || top.tagName) : 'null');
    }

    (async function main() {
        L('select2=' + !!(jQuery.fn && jQuery.fn.select2) + ' bootstrap.modal awal=' + !!(jQuery.fn && jQuery.fn.modal));
        for (var w = 0; w < 60 && !(jQuery.fn && jQuery.fn.modal); w++) await wait(200);
        L('bootstrap.modal=' + !!(jQuery.fn && jQuery.fn.modal) + ' (setelah ' + (w * 200) + 'ms)');
        // 1) buka Tambah Racikan
        var variant = new URLSearchParams(location.search).get('v') || '';
        if (variant === 'nofocus') {
            L('>> UJI VARIAN: openCombineModal() diganti, show({focus:false})');
            window.openCombineModal = function (id) {
                var titleEl = document.getElementById('combineModalTitle');
                if (titleEl) titleEl.textContent = id ? 'Edit Racikan' : 'Tambah Racikan';
                jQuery('#combineModal').modal({ focus: false, backdrop: true, keyboard: true });
                document.getElementById('combineFormContent').innerHTML = '<div class="spinner"></div>';
                fetch(id ? 'master-combine-form.php?id=' + id : 'master-combine-form.php')
                    .then(function (r) { return r.text(); })
                    .then(function (h) {
                        document.getElementById('combineFormContent').innerHTML = h;
                        window.initCombineForm();
                    });
            };
        }
        document.querySelector('.js-add-combine').click();
        for (var i = 0; i < 40 && !document.querySelector('#combineItems .combine-item-row select.select2-hidden-accessible'); i++) {
            await wait(200);
        }
        var sel = document.querySelector('#combineItems .combine-item-row select');
        L('row select found=' + !!sel + ' select2=' + jQuery(sel).hasClass('select2-hidden-accessible'));

        // 2) login
        var box = sel.nextElementSibling;
        var hit = box.querySelector('.select2-selection--single') || box.querySelector('.select2-selection');
        ['mousedown', 'mouseup', 'click'].forEach(function (t) {
            hit.dispatchEvent(new MouseEvent(t, { bubbles: true, cancelable: true, view: window }));
        });
        await wait(600);
        L('dropdown open after click = ' + ddOpen());

        // 3) cek posisi dropdown relatif viewport
        var d = dd();
        var dr = d.getBoundingClientRect();
        var dtop = document.elementFromPoint(Math.round(dr.left + dr.width / 2), Math.round(dr.top + 10));
        var dcls = dtop ? (dtop.className || dtop.tagName) : 'null';
        L('dropdown rect top=' + Math.round(dr.top) + ' h=' + Math.round(dr.height) +
            ' hitTest(top area)=' + dcls +
            ' isDropdown=' + (dtop === d || (dtop && dtop.closest && dtop.closest('.select2-dropdown') === d)));

        // 4) FOKUS kolom search - ini yang memicu scroll browser
        var search = document.querySelector('.select2-search__field');
        L('search found=' + !!search + ' scrollY before focus=' + Math.round(window.pageYOffset) +
            ' docEl scrollH=' + document.documentElement.scrollHeight + ' innerH=' + window.innerHeight);
        if (search) {
            var scs = window.getComputedStyle(search);
            L('search attrs: readonly=' + search.readOnly + ' disabled=' + search.disabled +
                ' tabIndex=' + search.tabIndex + ' type=' + search.type +
                ' pointerEvents=' + scs.pointerEvents + ' visibility=' + scs.visibility +
                ' display=' + scs.display + ' zIndex=' + scs.zIndex);
            L('search in DOM=' + document.contains(search) +
                ' parents=' + (function () {
                    var p = [], n = search.parentElement;
                    while (n && p.length < 6) { p.push(n.tagName + (n.id ? '#' + n.id : '') + (n.className ? '.' + String(n.className).split(' ').join('.') : '')); n = n.parentElement; }
                    return p.join(' < ');
                })());

            ['focusin', 'focusout', 'blur', 'focus'].forEach(function (t) {
                document.addEventListener(t, function (e) {
                    L('   [ev ' + t + '] target=' + (e.target.className || e.target.tagName || e.target.nodeName) +
                        ' related=' + (e.relatedTarget ? (e.relatedTarget.className || e.relatedTarget.nodeName) : 'null'));
                }, true);
            });
            search.focus();
            L('SYNC after focus: activeElement=' + (document.activeElement ? (document.activeElement.className || document.activeElement.nodeName) : 'null') +
                ' isSearch=' + (document.activeElement === search) + ' scrollY=' + Math.round(window.pageYOffset));
            await wait(300);
            L('after 300ms: activeElement=' + (document.activeElement ? (document.activeElement.className || document.activeElement.nodeName) : 'null') +
                ' scrollY=' + Math.round(window.pageYOffset) + ' dropdown open=' + ddOpen());
        }

        // 5) ketik
        if (search) {
            search.value = 'Good';
            search.dispatchEvent(new Event('input', { bubbles: true }));
            await wait(300);
            var opts = [].map.call(document.querySelectorAll('.select2-results__option[role="option"]'),
                function (o) { return o.textContent.replace(/\s+/g, ' ').trim().slice(0, 40); });
            L('after typing "Good": results=' + opts.length + ' ' + JSON.stringify(opts.slice(0, 2)) +
                ' dropdown open=' + ddOpen() + ' scrollY=' + Math.round(window.pageYOffset));
        }
        L('DONE');
        flush();
    })();
})();
