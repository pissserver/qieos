<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/connection.php';
}
?>
<!-- Core -->
<script src="<?php echo BASE_URL; ?>/vendor/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="<?php echo BASE_URL; ?>/vendor/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Vendor JS -->
<script src="<?php echo BASE_URL; ?>/vendor/onscreen/dist/on-screen.umd.min.js"></script>

<!-- Slider -->
<!-- <script src="<?php echo BASE_URL; ?>/vendor/nouislider/distribute/nouislider.min.js"></script> -->

<!-- Smooth scroll -->
<script src="<?php echo BASE_URL; ?>/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<!-- Charts -->
<script src="<?php echo BASE_URL; ?>/vendor/chartist/dist/chartist.min.js"></script>
<script src="<?php echo BASE_URL; ?>/vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>

<!-- Datepicker -->
<script src="<?php echo BASE_URL; ?>/vendor/vanillajs-datepicker/dist/js/datepicker.min.js"></script>

<!-- Sweet Alerts 2 -->
<script src="<?php echo BASE_URL; ?>/vendor/sweetalert2/dist/sweetalert2.all.min.js"></script>

<!-- Moment JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

<!-- Vanilla JS Datepicker -->
<script src="<?php echo BASE_URL; ?>/vendor/vanillajs-datepicker/dist/js/datepicker.min.js"></script>

<!-- Notyf -->
<script src="<?php echo BASE_URL; ?>/vendor/notyf/notyf.min.js"></script>

<!-- Simplebar -->
<script src="<?php echo BASE_URL; ?>/vendor/simplebar/dist/simplebar.min.js"></script>

<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<!-- Volt JS -->
<script src="<?php echo BASE_URL; ?>/assets/js/volt.js"></script>

<!-- Qieos Toast -->
<script src="<?php echo BASE_URL; ?>/script/toast.js?v=<?php echo filemtime(__DIR__ . '/toast.js'); ?>"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Logout Animation Overlay -->
<style>
    .logout-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 35%, #311042 70%, #0f172a 100%);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }
    .logout-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .logout-icon-wrap {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 50px rgba(239, 68, 68, 0.4), 0 0 100px rgba(239, 68, 68, 0.15);
        transform: scale(0);
        opacity: 0;
        margin-bottom: 2rem;
        position: relative;
    }
    .logout-overlay.active .logout-icon-wrap {
        animation: logoutBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s forwards;
    }
    .logout-icon-wrap svg {
        width: 36px;
        height: 36px;
        color: #fff;
    }
    .logout-ripple {
        position: absolute;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 2px solid rgba(239, 68, 68, 0.35);
        opacity: 0;
    }
    .logout-overlay.active .logout-ripple:nth-child(1) {
        animation: logoutRipple 1s ease 0.5s forwards;
    }
    .logout-overlay.active .logout-ripple:nth-child(2) {
        animation: logoutRipple 1s ease 0.7s forwards;
    }
    .logout-text {
        color: #f8fafc;
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        opacity: 0;
        transform: translateY(16px);
        text-align: center;
    }
    .logout-subtext {
        color: #94a3b8;
        font-size: 0.9rem;
        font-weight: 500;
        margin-top: 0.4rem;
        opacity: 0;
        transform: translateY(16px);
    }
    .logout-overlay.active .logout-text {
        animation: logoutFadeUp 0.45s ease 0.9s forwards;
    }
    .logout-overlay.active .logout-subtext {
        animation: logoutFadeUp 0.45s ease 1.1s forwards;
    }
    .logout-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, #ef4444, #f87171, #fca5a5);
        border-radius: 0 4px 4px 0;
        width: 0;
    }
    .logout-overlay.active .logout-progress {
        animation: logoutProgress 1.6s ease 0.3s forwards;
    }
    @keyframes logoutBounce {
        0% { transform: scale(0); opacity: 0; }
        50% { opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes logoutRipple {
        0% { transform: scale(1); opacity: 0.5; }
        100% { transform: scale(2.8); opacity: 0; }
    }
    @keyframes logoutFadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes logoutProgress {
        to { width: 100%; }
    }
</style>

<div class="logout-overlay" id="logoutOverlay">
    <div class="logout-ripple"></div>
    <div class="logout-ripple"></div>
    <div class="logout-icon-wrap">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
    </div>
    <div class="logout-text">Sampai Jumpa!</div>
    <div class="logout-subtext">Anda telah berhasil keluar dari sistem</div>
    <div class="logout-progress"></div>
</div>

<script>
(function() {
    var overlay = document.getElementById('logoutOverlay');
    if (!overlay) return;

    document.addEventListener('click', function(e) {
        var link = e.target.closest('a[href*="logout.php"]');
        if (!link) return;

        e.preventDefault();
        var href = link.getAttribute('href');

        overlay.classList.add('active');

        setTimeout(function() {
            window.location.href = href;
        }, 2200);
    });
})();
</script>

<!-- PWA Install Prompt -->
<style>
    #pwaInstallBtn{
        position:fixed;
        right:calc(18px + env(safe-area-inset-right, 0px));
        bottom:calc(18px + env(safe-area-inset-bottom, 0px));
        z-index:99990;
        display:none;
        align-items:center;
        gap:8px;
        padding:12px 18px;
        border:none;
        border-radius:999px;
        color:#fff;
        font-weight:700;
        font-size:14px;
        cursor:pointer;
        background:linear-gradient(90deg,#4f46e5,#7c3aed);
        box-shadow:0 12px 30px rgba(79,70,229,.45);
        transition:.25s;
    }
    #pwaInstallBtn:hover{
        transform:translateY(-2px);
        box-shadow:0 16px 36px rgba(79,70,229,.55);
    }
    #pwaInstallBtn i{font-size:15px;}
</style>

<button id="pwaInstallBtn" type="button">
    <i class="fas fa-download"></i>
    Install Aplikasi
</button>

<script>
(function () {
    var deferredPrompt = null;
    var installBtn = document.getElementById('pwaInstallBtn');
    if (!installBtn) return;

    // Deteksi apakah sudah berjalan sebagai aplikasi terinstall
    function isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.matchMedia('(display-mode: fullscreen)').matches ||
               window.navigator.standalone === true;
    }

    // Tangkap event install (Chrome/Edge/Android)
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        if (!isStandalone()) {
            installBtn.style.display = 'inline-flex';
        }
    });

    installBtn.addEventListener('click', function () {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function () {
            deferredPrompt = null;
            installBtn.style.display = 'none';
        });
    });

    // Sembunyikan tombol setelah terinstall
    window.addEventListener('appinstalled', function () {
        installBtn.style.display = 'none';
        deferredPrompt = null;
    });

    // Kalau sudah standalone, pastikan tombol tidak muncul
    if (isStandalone()) {
        installBtn.style.display = 'none';
    }
})();
</script>
