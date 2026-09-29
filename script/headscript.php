<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/connection.php';
}
?>
<!-- Meta -->
<meta
    name="viewport"
    content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no" />
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
    href="<?php echo BASE_URL; ?>/vendor/sweetalert2/dist/sweetalert2.min.css"
    rel="stylesheet" />

<!-- Notyf -->
<link type="text/css" href="<?php echo BASE_URL; ?>/vendor/notyf/notyf.min.css" rel="stylesheet" />

<!-- Volt CSS -->
<link type="text/css" href="<?php echo BASE_URL; ?>/css/volt.css?v=<?php echo filemtime(__DIR__ . '/../css/volt.css'); ?>" rel="stylesheet" />

<!-- Qieos Toast -->
<link type="text/css" href="<?php echo BASE_URL; ?>/css/components/toast.css?v=<?php echo filemtime(__DIR__ . '/../css/components/toast.css'); ?>" rel="stylesheet" />

<!-- Qieos Shared Buttons -->
<link type="text/css" href="<?php echo BASE_URL; ?>/css/components/buttons.css?v=<?php echo filemtime(__DIR__ . '/../css/components/buttons.css'); ?>" rel="stylesheet" />

<!-- Font Awesome -->
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Datatables -->
<link
    rel="stylesheet"
    href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Select2 (dropdown searchable) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

        /* Area status bar ditutup navbar mobile (hindari strip putih di atas) */
        .navbar-theme-primary{
            padding-top:calc(.75rem + var(--safe-top, 0px));
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
    if ("serviceWorker" in navigator) {
        window.addEventListener("load", function () {
            navigator.serviceWorker.register(BASE_URL + "/sw.js?v=6").catch(function (err) {
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

