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

