<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/connection.php';
}
?>
<!-- Core -->
<script src="<?php echo BASE_URL; ?>/vendor/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="<?php echo BASE_URL; ?>/vendor/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Smooth scroll -->
<script src="<?php echo BASE_URL; ?>/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<?php if (basename($_SERVER['PHP_SELF']) === 'dashboard.php') { ?>
<!-- Charts -->
<script src="<?php echo BASE_URL; ?>/vendor/chartist/dist/chartist.min.js"></script>
<script src="<?php echo BASE_URL; ?>/vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
<?php } ?>

<!-- Volt JS -->
<script src="<?php echo BASE_URL; ?>/assets/js/volt.js"></script>

<!-- Qieos Toast -->
<script src="<?php echo BASE_URL; ?>/script/toast.js?v=<?php echo filemtime(__DIR__ . '/toast.js'); ?>"></script>

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
        background:
            radial-gradient(circle at 25% 20%, rgba(99, 102, 241, 0.16) 0%, transparent 42%),
            radial-gradient(circle at 78% 68%, rgba(139, 92, 246, 0.14) 0%, transparent 42%),
            radial-gradient(circle at 50% 90%, rgba(236, 72, 153, 0.07) 0%, transparent 40%),
            linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }
    .logout-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .logout-overlay::before,
    .logout-overlay::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        opacity: 0;
        will-change: transform;
    }
    .logout-overlay::before {
        width: 440px;
        height: 440px;
        top: -150px;
        left: -130px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.30), transparent 65%);
    }
    .logout-overlay::after {
        width: 400px;
        height: 400px;
        bottom: -150px;
        right: -130px;
        background: radial-gradient(circle, rgba(236, 72, 153, 0.16), transparent 65%);
    }
    .logout-overlay.active::before {
        animation: loAurora 7s ease-in-out infinite alternate;
    }
    .logout-overlay.active::after {
        animation: loAurora 9s ease-in-out infinite alternate-reverse;
    }
    @keyframes loAurora {
        from { transform: translate3d(0, 0, 0) scale(1); opacity: 1; }
        to { transform: translate3d(56px, -44px, 0) scale(1.15); opacity: .85; }
    }
    .logout-stars {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }
    .logout-stars i {
        position: absolute;
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 0 8px rgba(165, 180, 252, 0.9);
        opacity: 0;
        animation-play-state: paused;
    }
    .logout-overlay.active .logout-stars i {
        animation: loStar 3.4s ease-in-out infinite;
    }
    .logout-stars i:nth-child(1) { top: 20%; left: 14%; animation-delay: .2s; }
    .logout-stars i:nth-child(2) { top: 34%; left: 84%; width: 2px; height: 2px; animation-delay: 1.1s; }
    .logout-stars i:nth-child(3) { top: 12%; left: 55%; width: 2px; height: 2px; animation-delay: 2s; }
    .logout-stars i:nth-child(4) { top: 68%; left: 8%; animation-delay: .7s; }
    .logout-stars i:nth-child(5) { top: 78%; left: 66%; width: 2px; height: 2px; animation-delay: 1.6s; }
    .logout-stars i:nth-child(6) { top: 82%; left: 30%; animation-delay: 2.5s; }
    @keyframes loStar {
        0%, 100% { opacity: 0; transform: scale(.6); }
        50% { opacity: .95; transform: scale(1.2); }
    }
    .logout-overlay.active .logout-icon-wrap svg {
        animation: logoutIconFloat 2.6s ease-in-out 1.4s infinite;
    }
    @keyframes logoutIconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .logout-warp {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 0;
    }
    .logout-warp i {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 2px;
        height: 58px;
        margin-left: -1px;
        background: linear-gradient(to bottom, rgba(165, 180, 252, 0.9), rgba(165, 180, 252, 0));
        transform-origin: center top;
        transform: rotate(var(--a)) translateY(-170px) scaleY(.2);
        opacity: 0;
    }
    .logout-overlay.active .logout-warp i {
        animation: loWarp 1.05s cubic-bezier(.16, 1, .3, 1) var(--dl) forwards;
    }
    @keyframes loWarp {
        0% { transform: rotate(var(--a)) translateY(-170px) scaleY(.2); opacity: 0; }
        28% { opacity: .8; }
        100% { transform: rotate(var(--a)) translateY(-170px) scaleY(1); opacity: 0; }
    }
    .lo-burst {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }
    .lo-burst i {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 5px;
        height: 5px;
        margin: -2.5px 0 0 -2.5px;
        border-radius: 50%;
        background: #e0e7ff;
        box-shadow: 0 0 10px rgba(165, 180, 252, .95);
        opacity: 0;
        z-index: 2;
    }
    .logout-overlay.active .lo-burst i {
        animation: loBurst .8s cubic-bezier(.16, 1, .3, 1) var(--bd) forwards;
    }
    @keyframes loBurst {
        0% { transform: translate(0, 0) scale(.4); opacity: 0; }
        22% { opacity: 1; }
        100% { transform: translate(var(--bx), var(--by)) scale(.15); opacity: 0; }
    }
    .logout-icon-wrap {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background:
            radial-gradient(circle at 30% 25%, rgba(255, 255, 255, 0.35) 0%, transparent 45%),
            linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow:
            0 0 50px rgba(99, 102, 241, 0.45),
            0 0 110px rgba(139, 92, 246, 0.22),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.18);
        transform: scale(0);
        opacity: 0;
        margin-bottom: 2rem;
        position: relative;
    }
    .logout-overlay.active .logout-icon-wrap {
        animation: logoutBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s forwards;
    }
    .logout-icon-wrap::before {
        content: '';
        position: absolute;
        inset: -7px;
        border-radius: 50%;
        padding: 2px;
        background: linear-gradient(135deg, rgba(165, 180, 252, 0.7), rgba(192, 132, 252, 0.15), rgba(165, 180, 252, 0.7));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
    }
    .logout-overlay.active .logout-icon-wrap::before {
        animation: logoutRing 0.9s ease 0.35s forwards;
    }
    .logout-icon-wrap svg {
        width: 36px;
        height: 36px;
        color: #fff;
        filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.25));
    }
    .logout-ripple {
        position: absolute;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 2px solid rgba(129, 140, 248, 0.5);
        opacity: 0;
    }
    .logout-overlay.active .logout-ripple:nth-child(2) {
        animation: logoutRipple 1s ease 0.5s forwards;
    }
    .logout-overlay.active .logout-ripple:nth-child(3) {
        animation: logoutRipple 1s ease 0.7s forwards;
    }
    .logout-text {
        background: linear-gradient(120deg, #ffffff 0%, #c7d2fe 50%, #e9d5ff 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        opacity: 0;
        transform: translateY(16px);
        text-align: center;
        filter: drop-shadow(0 2px 14px rgba(139, 92, 246, 0.35));
    }
    .logout-subtext {
        color: #a5b4d6;
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
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899);
        border-radius: 0 4px 4px 0;
        box-shadow: 0 0 14px rgba(139, 92, 246, 0.6);
        width: 0;
        overflow: hidden;
    }
    .logout-progress::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.55), transparent);
        transform: translateX(-120%);
    }
    .logout-overlay.active .logout-progress::after {
        animation: loProgShimmer 0.9s linear 0.9s 2;
    }
    @keyframes loProgShimmer {
        to { transform: translateX(130%); }
    }
    .logout-overlay.active .logout-progress {
        animation: logoutProgress 1.6s ease 0.3s forwards;
    }
    @keyframes logoutBounce {
        0% { transform: scale(0); opacity: 0; }
        50% { opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes logoutRing {
        from { opacity: 0; transform: scale(0.82); }
        to { opacity: 1; transform: scale(1); }
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
    @media (prefers-reduced-motion: reduce) {
        .logout-overlay.active::before,
        .logout-overlay.active::after,
        .logout-stars i,
        .logout-overlay.active .logout-icon-wrap svg,
        .logout-overlay.active .logout-warp i,
        .logout-overlay.active .lo-burst i,
        .logout-overlay.active .logout-progress::after {
            animation: none !important;
        }
    }
</style>

<div class="logout-overlay" id="logoutOverlay">
    <div class="logout-stars" aria-hidden="true">
        <i></i><i></i><i></i><i></i><i></i><i></i>
    </div>
    <div class="logout-warp" aria-hidden="true">
        <i style="--a:32deg;--dl:1.45s"></i>
        <i style="--a:77deg;--dl:1.6s"></i>
        <i style="--a:122deg;--dl:1.5s"></i>
        <i style="--a:167deg;--dl:1.75s"></i>
        <i style="--a:212deg;--dl:1.5s"></i>
        <i style="--a:257deg;--dl:1.65s"></i>
        <i style="--a:302deg;--dl:1.55s"></i>
        <i style="--a:347deg;--dl:1.7s"></i>
    </div>
    <div class="logout-ripple"></div>
    <div class="logout-ripple"></div>
    <div class="logout-icon-wrap">
        <div class="lo-burst" aria-hidden="true">
            <i style="--bx:40px;--by:-14px;--bd:.38s"></i>
            <i style="--bx:28px;--by:30px;--bd:.44s"></i>
            <i style="--bx:-32px;--by:26px;--bd:.4s"></i>
            <i style="--bx:-38px;--by:-18px;--bd:.48s"></i>
            <i style="--bx:6px;--by:-42px;--bd:.42s"></i>
            <i style="--bx:-12px;--by:40px;--bd:.46s"></i>
        </div>
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
        }, 2600);
    });
})();
</script>

<!-- Lazy load gambar konten (hemat bandwidth & memori di mobile) -->
<script>
(function(){
    if (!('loading' in HTMLImageElement.prototype)) return;
    var imgs = document.querySelectorAll('img:not([loading])');
    for (var i = 0; i < imgs.length; i++) {
        var img = imgs[i];
        if (img.closest('nav') || img.closest('.sidebar-header') || img.closest('.mobile-user-card')) continue;
        img.loading = 'lazy';
        img.decoding = 'async';
    }
})();
</script>

<!-- Chat: badge unread + notifikasi device + notifikasi suara (polling ringan) -->
<script>
(function () {
    if (window.__chatActive) return;

    var badges = document.querySelectorAll('.chat-unread-badge');
    if (!badges.length) return;

    var lastTotal = -1;
    var lastNotifId = 0;
    var audioCtx = null;
    var swReg = null;

    // Notifikasi butuh konteks aman (HTTPS / localhost). Lewat IP LAN tidak didukung browser.
    var notifSupported = ('Notification' in window) && window.isSecureContext;

    // Ambil service worker aktif (untuk showNotification + klik notifikasi)
    if ('serviceWorker' in navigator && window.isSecureContext) {
        navigator.serviceWorker.ready.then(function (reg) { swReg = reg; }).catch(function () {});
    }

    function updateBadges(total) {
        for (var i = 0; i < badges.length; i++) {
            if (total > 0) {
                badges[i].textContent = total > 9 ? '9+' : total;
                badges[i].classList.remove('d-none');
            } else {
                badges[i].classList.add('d-none');
            }
        }
    }

    function ensureAudio() {
        if (!audioCtx) {
            try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch (e) { return; }
        }
        if (audioCtx.state === 'suspended') audioCtx.resume();
    }

    function askNotifyPermission() {
        if (!notifSupported) { renderNotifyUI(); return; }
        if (Notification.permission !== 'default') { renderNotifyUI(); return; }
        try {
            var p = Notification.requestPermission(function () { renderNotifyUI(); });
            if (p && p.then) p.then(renderNotifyUI);
        } catch (e) {}
    }

    // ---- Tombol/diagnosa notifikasi ----
    var notifBtn = null;

    function notifPillDismissed() {
        try { return localStorage.getItem('qieos_notif_pill_dismissed') === '1'; } catch (e) { return false; }
    }
    function dismissNotifPill() {
        try { localStorage.setItem('qieos_notif_pill_dismissed', '1'); } catch (e) {}
        if (notifBtn) { notifBtn.remove(); notifBtn = null; }
    }
    function hideNotifPill() {
        if (notifBtn) { notifBtn.remove(); notifBtn = null; }
    }

    function renderNotifyUI() {
        // Lewat IP / non-secure: notifikasi memang tidak didukung, jangan tampilkan peringatan.
        if (!notifSupported || Notification.permission === 'granted') { hideNotifPill(); return; }
        if (notifPillDismissed()) return;
        if (Notification.permission === 'denied') {
            showNotifPill('Notifikasi diblokir \u2014 izinkan lewat setelan situs browser', true);
            return;
        }
        showNotifPill('Aktifkan notifikasi pesan', false);
    }

    function showNotifPill(text, disabled) {
        if (!notifBtn) {
            notifBtn = document.createElement('div');
            notifBtn.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:2147483000;display:flex;align-items:center;gap:8px;border-radius:999px;padding:9px 8px 9px 16px;font-family:inherit;font-size:12.5px;font-weight:700;line-height:1;color:#fff;background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 10px 24px rgba(99,102,241,.4);';

            var lbl = document.createElement('span');
            lbl.id = '__notifPillLabel';
            var x = document.createElement('button');
            x.type = 'button';
            x.setAttribute('aria-label', 'Tutup');
            x.textContent = '\u00d7';
            x.style.cssText = 'border:0;background:transparent;color:#fff;font-size:17px;line-height:1;cursor:pointer;padding:0 4px;opacity:.85;';
            x.onclick = dismissNotifPill;

            notifBtn.appendChild(lbl);
            notifBtn.appendChild(x);
            document.body.appendChild(notifBtn);
        }

        var lbl = document.getElementById('__notifPillLabel');
        lbl.textContent = text;
        if (disabled) {
            lbl.style.cursor = 'default';
            lbl.onclick = null;
            notifBtn.style.background = 'rgba(30,41,59,.94)';
        } else {
            lbl.style.cursor = 'pointer';
            lbl.onclick = function () { ensureAudio(); askNotifyPermission(); };
            notifBtn.style.background = 'linear-gradient(135deg,#6366f1,#8b5cf6)';
        }
    }

    // Minta izin notifikasi saat interaksi pertama (harus dari user gesture)
    document.addEventListener('pointerdown', function () {
        ensureAudio();
        askNotifyPermission();
    }, { passive: true });

    function beep() {
        ensureAudio();
        if (!audioCtx) return;
        var t = audioCtx.currentTime;
        var seq = [659.25, 880];
        for (var j = 0; j < seq.length; j++) {
            var o = audioCtx.createOscillator();
            var g = audioCtx.createGain();
            var st = t + j * 0.09;
            o.type = 'sine';
            o.frequency.value = seq[j];
            g.gain.setValueAtTime(0.0001, st);
            g.gain.exponentialRampToValueAtTime(0.15, st + 0.02);
            g.gain.exponentialRampToValueAtTime(0.0001, st + 0.4);
            o.connect(g);
            g.connect(audioCtx.destination);
            o.start(st);
            o.stop(st + 0.45);
        }
    }

    function notify(latest) {
        if (!latest) return;
        if (!notifSupported || Notification.permission !== 'granted') return;
        if (latest.id <= lastNotifId) return;
        lastNotifId = latest.id;

        var title = 'Pesan baru dari ' + (latest.sender_name || 'Pengguna');
        var body = latest.message || '';
        if (body.length > 120) body = body.slice(0, 120) + '\u2026';

        var url = BASE_URL + '/pages/chat/chat.php?with=' + latest.sender_id;
        var opts = {
            body: body,
            icon: BASE_URL + '/assets/img/brand/qieos2.png',
            badge: BASE_URL + '/assets/img/brand/qieos2.png',
            tag: 'chat-' + latest.sender_id,
            renotify: true,
            data: { url: url }
        };

        if (swReg && swReg.showNotification) {
            try { swReg.showNotification(title, opts).catch(function () {}); } catch (e) {}
        } else {
            try {
                var n = new Notification(title, opts);
                n.onclick = function () {
                    window.focus();
                    location.href = url;
                    n.close();
                };
            } catch (e) {}
        }
    }

    function poll() {
        fetch(BASE_URL + '/pages/chat/chat-api.php?action=unread&_=' + Date.now())
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var total = data.total || 0;
                var increased = (lastTotal >= 0 && total > lastTotal);
                if (increased) {
                    if (document.visibilityState !== 'hidden') beep();
                    notify(data.latest);
                } else if (lastNotifId === 0 && data.latest) {
                    // Baseline awal: jangan munculkan notif untuk pesan lama
                    lastNotifId = data.latest.id;
                }
                lastTotal = total;
                updateBadges(total);
            })
            .catch(function () {});
    }

    poll();
    renderNotifyUI();
    var timer = setInterval(poll, 3000);
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') { poll(); renderNotifyUI(); }
    });
    window.addEventListener('pagehide', function () { clearInterval(timer); });
})();
</script>
