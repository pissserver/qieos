<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/connection.php';
}
?>
<!-- Meta -->
<meta
    name="viewport"
    content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no, interactive-widget=resizes-content" />
<meta name="title" content="Volt Free Bootstrap Dashboard - Transactions" />
<meta name="author" content="Themesberg" />
<meta
    name="description"
    content="Volt Pro is a Premium Bootstrap 5 Admin Dashboard featuring over 800 components, 10+ plugins and 20 example pages using Vanilla JS." />
<meta
    name="keywords"
    content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, themesberg, themesberg dashboard, themesberg admin dashboard" />
<link
    rel="canonical"
    href="https://themesberg.com/product/admin-dashboard/volt-premium-bootstrap-5-dashboard" />

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website" />
<meta property="og:url" content="https://demo.themesberg.com/volt-pro" />
<meta
    property="og:title"
    content="Volt Free Bootstrap Dashboard - Transactions" />
<meta
    property="og:description"
    content="Volt Pro is a Premium Bootstrap 5 Admin Dashboard featuring over 800 components, 10+ plugins and 20 example pages using Vanilla JS." />
<meta
    property="og:image"
    content="https://themesberg.s3.us-east-2.amazonaws.com/public/products/volt-pro-bootstrap-5-dashboard/volt-pro-preview.jpg" />

<!-- Favicon -->
<link
    rel="icon"
    sizes="120x120"
    href="<?php echo BASE_URL; ?>/assets/img/brand/qieos2.png" />

<meta name="msapplication-TileColor" content="#4f46e5" />

<!-- Sweet Alert -->
<link
    type="text/css"
    href="<?php echo BASE_URL; ?>/assets/vendor/sweetalert2/sweetalert2.min.css"
    rel="stylesheet" />

<!-- Notyf -->
<link type="text/css" href="<?php echo BASE_URL; ?>/assets/vendor/notyf/notyf.min.css" rel="stylesheet" />

<!-- Volt CSS -->
<link type="text/css" href="<?php echo BASE_URL; ?>/css/volt.css?v=<?php echo filemtime(__DIR__ . '/../css/volt.css'); ?>" rel="stylesheet" />

<!-- Qieos Toast -->
<link type="text/css" href="<?php echo BASE_URL; ?>/css/components/toast.css?v=<?php echo filemtime(__DIR__ . '/../css/components/toast.css'); ?>" rel="stylesheet" />

<!-- Qieos Shared Buttons -->
<link type="text/css" href="<?php echo BASE_URL; ?>/css/components/buttons.css?v=<?php echo filemtime(__DIR__ . '/../css/components/buttons.css'); ?>" rel="stylesheet" />

<!-- Font Awesome -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/vendor/fontawesome/css/all.min.css?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/fontawesome/css/all.min.css'); ?>" />
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/vendor/inter/inter.css?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/inter/inter.css'); ?>" />

<!-- SweetAlert2 -->
<script src="<?php echo BASE_URL; ?>/assets/vendor/sweetalert2/sweetalert2.all.min.js?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/sweetalert2/sweetalert2.all.min.js'); ?>"></script>

<!-- Datatables -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/vendor/datatables/dataTables.bootstrap5.min.css?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/datatables/dataTables.bootstrap5.min.css'); ?>" />
<script src="<?php echo BASE_URL; ?>/assets/vendor/jquery/jquery.min.js?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo BASE_URL; ?>/assets/vendor/datatables/jquery.dataTables.min.js?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo BASE_URL; ?>/assets/vendor/datatables/dataTables.bootstrap5.min.js?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/datatables/dataTables.bootstrap5.min.js'); ?>"></script>

<!-- Select2 (dropdown searchable) -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/vendor/select2/select2.min.css?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/select2/select2.min.css'); ?>" />
<script src="<?php echo BASE_URL; ?>/assets/vendor/select2/select2.min.js?v=<?php echo filemtime(__DIR__ . '/../assets/vendor/select2/select2.min.js'); ?>"></script>

<!-- Select2: halaman ini di-scroll lewat .content (overflow-y: auto), bukan <body>.
     Bawaan select2 MEMBEKUKAN scroll semua ancestor yang bisa di-scroll selama
     dropdown terbuka, sementara .content memakai scroll-behavior: smooth.
     Akibatnya tiap putaran scroll lalu ditarik balik ke posisi awal -> halaman
     bergetar. Patch di bawah melepas pembekuan itu, mengikuti posisi select
     selama scroll, lalu menutup dropdown bila select-nya sudah keluar viewport. -->
<script>
    // select2 4.1.0-rc.0 menyimpan instance-nya di cache internal
    // (Utils.__cache, dikunci lewat atribut data-select2-id), BUKAN di jQuery
    // data. Jadi $(el).data('select2') selalu undefined -> dipakai Utils.GetData
    // lewat escape hatch $.fn.select2.amd.require('select2/utils').
    window.qieosSelect2Instance = function (el) {
        if (!el || typeof jQuery === 'undefined' || !jQuery.fn.select2) return null;
        var inst = null;
        try {
            var amd = jQuery.fn.select2.amd;
            if (amd && typeof amd.require === 'function') {
                var Utils = amd.require('select2/utils');
                if (Utils && typeof Utils.GetData === 'function') inst = Utils.GetData(el, 'select2');
            }
        } catch (err) {}
        if (!inst) inst = jQuery(el).data('select2') || null;
        return inst;
    };

    // Container dropdown yang ditempel select2 ke dropdownParent (anak <body>
    // atau modal). Dipakai untuk membersihkan dropdown yatim.
    window.qieosSelect2Container = function (el) {
        var inst = window.qieosSelect2Instance(el);
        if (!inst) return null;
        if (inst.dropdown && inst.dropdown.$dropdownContainer && inst.dropdown.$dropdownContainer[0]) {
            return inst.dropdown.$dropdownContainer[0];
        }
        if (inst.$dropdown && inst.$dropdown[0]) return inst.$dropdown[0];
        return null;
    };

    (function () {
        if (typeof jQuery === 'undefined' || !jQuery.fn.select2) return;

        var stopFollow = null;

        // Jalan dari select ke <html>, lewati <html> (scroll utama halaman).
        function walkAncestors(el, fn) {
            while (el && el !== document.documentElement) {
                fn(el);
                el = el.parentElement;
            }
        }

        jQuery(document).on('select2:open', function (e) {
            var $sel = jQuery(e.target);
            var inst = window.qieosSelect2Instance(e.target);
            if (!inst || !inst.dropdown || typeof inst.dropdown._positionDropdown !== 'function') return;

            // select2:open dipancarkan SEBELUM attachBody memasang pembekuan
            // scroll-nya, jadi pemasangan patch-nya ditunda satu tiket.
            setTimeout(function () {
                if (stopFollow) { stopFollow(); stopFollow = null; }
                if (window.qieosSelect2Instance(e.target) !== inst) return;
                if (typeof inst.isOpen === 'function' && !inst.isOpen()) return;

                var id = inst.id;
                var host = inst.$container && inst.$container[0];
                if (!host) return;

                // lepas pembekuan bawaan di semua ancestor select-nya
                walkAncestors(host, function (node) {
                    jQuery(node).off('scroll.select2.' + id);
                });

                // ...tapi hanya pada ancestor yang benar-benar bisa di-scroll
                // yang dipasang pengikut posisi dropdown-nya
                var nodes = [];
                walkAncestors(host, function (node) {
                    var cs = window.getComputedStyle(node);
                    if (cs.overflowY === 'auto' || cs.overflowY === 'scroll') nodes.push(node);
                });
                if (!nodes.length) return;

                var raf = 0;

                function position() {
                    if (raf) return;
                    raf = requestAnimationFrame(function () {
                        raf = 0;
                        if (!host.isConnected) { stop(); return; }

                        var rect = host.getBoundingClientRect();
                        var view = nodes[0].getBoundingClientRect();

                        // select sudah keluar dari area yang terlihat: tutup
                        // dropdown supaya tidak melayang sendirian di luar scroller
                        if (rect.bottom < view.top + 4 || rect.top > view.bottom - 4) {
                            closeQuietly(view);
                            return;
                        }

                        try {
                            inst.dropdown._positionDropdown();
                            if (typeof inst.dropdown._resizeDropdown === 'function') inst.dropdown._resizeDropdown();
                        } catch (err) {}
                    });
                }

                // Menutup dropdown memindahkan fokus ke select-nya, dan browser
                // langsung men-scroll select itu ke dalam layar — padahal select
                // tadi sengaja keluar dari layar. Matikan smooth scroll sementara
                // dan kembalikan posisinya, supaya halaman tidak ikut tertarik.
                function closeQuietly(view) {
                    var box = nodes[0];
                    var y = box.scrollTop;
                    var prev = box.style.scrollBehavior;

                    box.style.scrollBehavior = 'auto';
                    stop();
                    if (document.activeElement && document.activeElement.blur) {
                        document.activeElement.blur();
                    }
                    if (typeof inst.close === 'function') {
                        try { inst.close(); } catch (err) {}
                    }
                    box.scrollTop = y;

                    requestAnimationFrame(function () {
                        if (box.scrollTop !== y) box.scrollTop = y;
                        box.style.scrollBehavior = prev;
                    });
                }

                function stop() {
                    nodes.forEach(function (node) {
                        jQuery(node).off('scroll.select2.' + id);
                        node.removeEventListener('scroll', position);
                    });
                    raf = 0;
                    if (stopFollow === stop) stopFollow = null;
                }

                nodes.forEach(function (node) {
                    node.addEventListener('scroll', position, { passive: true });
                });

                $sel.off('select2:close.qieosScrollFollow')
                    .on('select2:close.qieosScrollFollow', stop);
                stopFollow = stop;
            }, 0);
        });
    })();
</script>

<!-- PWA -->
<link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.php">
<meta name="theme-color" content="#0f172a">

<!-- PWA: iOS / iPadOS standalone support -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Qieos">
<link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/img/brand/icon-192.png">
<link rel="apple-touch-icon" sizes="192x192" href="<?php echo BASE_URL; ?>/assets/img/brand/icon-192.png">
<link rel="apple-touch-icon" sizes="512x512" href="<?php echo BASE_URL; ?>/assets/img/brand/icon-512.png">

<!-- PWA: fullscreen/standalone display + safe-area handling -->
<style>
    :root{
        --safe-top:env(safe-area-inset-top, 0px);
        --safe-bottom:env(safe-area-inset-bottom, 0px);
        --safe-left:env(safe-area-inset-left, 0px);
        --safe-right:env(safe-area-inset-right, 0px);
    }

    /* Saat berjalan sebagai aplikasi terinstall (standalone/fullscreen) */
    @media (display-mode: standalone), (display-mode: fullscreen), (display-mode: window-controls-overlay){
        html, body{
            overscroll-behavior-y:none;
            background:#0f172a;
        }

        body{
            padding-bottom:var(--safe-bottom);
            padding-left:var(--safe-left);
            padding-right:var(--safe-right);
            -webkit-user-select:none;
            user-select:none;
        }

        /* Izinkan seleksi teks di area input/konten */
        input, textarea, select, [contenteditable="true"], .allow-select{
            -webkit-user-select:text;
            user-select:text;
        }
    }

    /* ===== Warna dasar halaman — seragam di semua halaman ===== */
    :root{
        --q-page-bg:#DFE0E2;
        --bs-body-bg:#DFE0E2;
        --bs-body-bg-rgb:223, 224, 226;
    }

    html, body{
        background-color:#DFE0E2 !important;
    }
</style>

<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
    (function () {
        var overlayId = "qieos-offline-overlay";
        var previousFocus = null;

        function showOfflineOverlay() {
            if (document.getElementById(overlayId)) return;
            if (!document.body) {
                document.addEventListener("DOMContentLoaded", showOfflineOverlay, { once: true });
                return;
            }

            var overlay = document.createElement("div");
            overlay.id = overlayId;
            overlay.tabIndex = -1;
            overlay.setAttribute("role", "alertdialog");
            overlay.setAttribute("aria-modal", "true");
            overlay.setAttribute("aria-labelledby", "qieos-offline-title");
            overlay.setAttribute("aria-describedby", "qieos-offline-message");
            overlay.innerHTML = `
                <style>
                    #qieos-offline-overlay {
                        position: fixed;
                        z-index: 2147483647;
                        inset: 0;
                        display: grid;
                        place-items: center;
                        overflow-y: auto;
                        padding: max(16px, env(safe-area-inset-top, 0px)) max(16px, env(safe-area-inset-right, 0px)) max(16px, env(safe-area-inset-bottom, 0px)) max(16px, env(safe-area-inset-left, 0px));
                        background: radial-gradient(ellipse at 18% 18%, rgba(99, 102, 241, .2), transparent 38%), radial-gradient(ellipse at 88% 82%, rgba(14, 165, 233, .12), transparent 35%), #0b1120;
                        color: #f8fafc;
                        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                    }
                    #qieos-offline-overlay *,
                    #qieos-offline-overlay *::before,
                    #qieos-offline-overlay *::after { box-sizing: border-box; }
                    #qieos-offline-overlay .qieos-offline-card {
                        width: min(900px, 100%);
                        display: grid;
                        grid-template-columns: .9fr 1.1fr;
                        overflow: hidden;
                        border: 1px solid rgba(148, 163, 184, .18);
                        border-radius: 30px;
                        background: linear-gradient(140deg, rgba(30, 41, 59, .96), rgba(15, 23, 42, .98));
                        box-shadow: 0 32px 90px rgba(0, 0, 0, .42), inset 0 1px 0 rgba(255, 255, 255, .06);
                    }
                    #qieos-offline-overlay .qieos-offline-art {
                        min-height: 300px;
                        display: grid;
                        place-items: center;
                        background: radial-gradient(circle at center, rgba(99, 102, 241, .22), transparent 62%);
                    }
                    #qieos-offline-overlay .qieos-offline-art svg {
                        width: min(230px, 66%);
                        height: auto;
                        filter: drop-shadow(0 22px 30px rgba(0, 0, 0, .35));
                    }
                    #qieos-offline-overlay .qieos-offline-copy {
                        align-self: center;
                        padding: 44px 48px 40px 20px;
                    }
                    #qieos-offline-overlay .qieos-offline-status {
                        width: fit-content;
                        display: flex;
                        align-items: center;
                        gap: 9px;
                        margin-bottom: 20px;
                        padding: 8px 12px;
                        border: 1px solid rgba(251, 191, 36, .24);
                        border-radius: 999px;
                        background: rgba(245, 158, 11, .1);
                        color: #fde68a;
                        font-size: 12px;
                        font-weight: 700;
                    }
                    #qieos-offline-overlay .qieos-offline-dot {
                        width: 8px;
                        height: 8px;
                        border-radius: 50%;
                        background: #fbbf24;
                        box-shadow: 0 0 12px rgba(251, 191, 36, .7);
                    }
                    #qieos-offline-overlay h1 {
                        margin: 0;
                        color: #f8fafc;
                        font-size: clamp(30px, 4vw, 44px);
                        line-height: 1.08;
                        letter-spacing: -.04em;
                    }
                    #qieos-offline-overlay .qieos-offline-message {
                        margin: 18px 0 24px;
                        color: #aab8cc;
                        font-size: 16px;
                        line-height: 1.7;
                    }
                    @media (max-width: 680px) {
                        #qieos-offline-overlay .qieos-offline-card {
                            grid-template-columns: 1fr;
                            border-radius: 24px;
                        }
                        #qieos-offline-overlay .qieos-offline-art { min-height: 180px; }
                        #qieos-offline-overlay .qieos-offline-art svg { width: 145px; }
                        #qieos-offline-overlay .qieos-offline-copy {
                            padding: 4px 24px 28px;
                            text-align: center;
                        }
                        #qieos-offline-overlay .qieos-offline-status { margin: 0 auto 16px; }
                        #qieos-offline-overlay .qieos-offline-message { font-size: 15px; }
                    }
                </style>
                <main class="qieos-offline-card">
                    <div class="qieos-offline-art" aria-hidden="true">
                        <svg viewBox="0 0 320 280" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="153" cy="252" rx="96" ry="13" fill="#020617" opacity=".38"/>
                            <path d="M91 207c-34 3-49 27-35 42 10 11 31 8 34-5 2-8-4-14-12-12" fill="none" stroke="#e87932" stroke-width="17" stroke-linecap="round"/>
                            <path d="M87 238c0-46 22-79 67-79s74 33 74 79v10H87z" fill="#e99142"/>
                            <ellipse cx="156" cy="216" rx="38" ry="47" fill="#ffe5bc"/>
                            <ellipse cx="112" cy="243" rx="25" ry="12" fill="#f7b65f"/>
                            <ellipse cx="191" cy="243" rx="25" ry="12" fill="#f7b65f"/>
                            <path d="M94 111 88 61c-1-10 10-15 17-8l32 30m50 0 30-30c7-7 18-2 17 8l-6 50" fill="#f4aa53" stroke="#d96c2d" stroke-width="4" stroke-linejoin="round"/>
                            <path d="m105 83-3-17 20 18m76 0 19-18-2 18" fill="#ef9a65"/>
                            <ellipse cx="156" cy="116" rx="69" ry="59" fill="#f4aa53"/>
                            <path d="M126 120q10 11 20 0m20 0q10 11 20 0" fill="none" stroke="#713f2c" stroke-width="5" stroke-linecap="round"/>
                            <path d="m150 135 6 5 6-5c0-5-12-5-12 0Zm6 6q-9 10-18 2m18-2q9 10 18 2" fill="#a94d52" stroke="#713f2c" stroke-width="3" stroke-linecap="round"/>
                            <rect x="225" y="34" width="70" height="66" rx="22" fill="#312e81" stroke="#818cf8" stroke-width="2"/>
                            <path d="M241 56q19-16 38 0m-30 9q11-9 22 0m-31 19 39-35" fill="none" stroke="#c4b5fd" stroke-width="5" stroke-linecap="round"/>
                            <circle cx="260" cy="75" r="3.5" fill="#c4b5fd"/>
                        </svg>
                    </div>
                    <section class="qieos-offline-copy">
                        <div class="qieos-offline-status" role="status">
                            <span class="qieos-offline-dot" aria-hidden="true"></span>
                            <span>Koneksi terputus</span>
                        </div>
                        <h1 id="qieos-offline-title">Sepertinya internet sedang bersembunyi</h1>
                        <p id="qieos-offline-message" class="qieos-offline-message">Qieos belum bisa terhubung. Periksa Wi-Fi atau data seluler.</p>
                    </section>
                </main>`;

            previousFocus = document.activeElement;
            document.body.appendChild(overlay);
            overlay.focus();
        }

        function hideOfflineOverlay() {
            var overlay = document.getElementById(overlayId);
            if (!overlay) return;

            overlay.remove();
            if (previousFocus && typeof previousFocus.focus === "function") {
                previousFocus.focus({ preventScroll: true });
            }
            previousFocus = null;
        }

        window.addEventListener("offline", showOfflineOverlay);
        window.addEventListener("online", hideOfflineOverlay);
        if (!navigator.onLine) showOfflineOverlay();
    })();

    if ("serviceWorker" in navigator) {
        window.addEventListener("load", function () {
            navigator.serviceWorker.register(BASE_URL + "/sw.js?v=12").catch(function (err) {
                console.warn("SW registration failed:", err);
            });
            // Unregister old service workers
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                registrations.forEach(function(registration) {
                    if (registration.scope.indexOf(BASE_URL + '/') !== -1) {
                        registration.update();
                    }
                });
            });
        });
    }
</script>
