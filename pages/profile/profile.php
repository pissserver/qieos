<?php
include '../../sessions/session.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/profile.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/profile.css'); ?>">

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Profile - Qieos</title>

    <?php include '../../script/headscript.php'; ?>
</head>

<body class="profile-body">

    <?php include '../components/sidebar.php'; ?>

    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <div class="container-fluid px-0 mt-4 profile-page">

            <?php
            if (isset($_SESSION['flash'])) {
                $flashType = (strpos($_SESSION['flash'], 'Gagal') === 0) ? 'error' : 'success';
                $flashMsg = $_SESSION['flash'];
                unset($_SESSION['flash']);
            }

            // Ringkasan kelengkapan profil
            $pfChecks = [
                'Nama lengkap terisi'  => !empty($user['fullname']),
                'Foto profil diunggah' => !empty($user['photo']),
                'Peran akun ditetapkan' => !empty($user['role']),
                'Username terdaftar'    => !empty($_SESSION['username']),
            ];
            $pfDone  = count(array_filter($pfChecks));
            $pfTotal = count($pfChecks);
            $pfPct   = $pfTotal ? (int)round($pfDone / $pfTotal * 100) : 0;
            ?>

            <!-- HERO -->
            <section class="profile-hero">
                <!-- ===== galaksi di dalam kartu ===== -->
                <canvas class="hero-galaxy-canvas" id="galaxyCanvas" aria-hidden="true"></canvas>

                <div class="hero-galaxy-layer" aria-hidden="true">
                    <span class="galaxy-nebula nebula-a"></span>
                    <span class="galaxy-nebula nebula-b"></span>
                    <span class="galaxy-nebula nebula-c"></span>

                    <span class="galaxy-planet planet-a"></span>
                    <span class="galaxy-planet planet-b"></span>
                    <span class="galaxy-planet planet-c"></span>
                </div>

                <div class="hero-inner">

                    <div class="hero-avatar-wrap">
                        <span class="hero-avatar-ring"></span>
                        <img
                            src="<?php echo $user['photo'] ? BASE_URL . '/assets/img/uploads/' . $user['photo'] : BASE_URL . '/assets/img/default-avatar.jpg'; ?>"
                            class="hero-avatar"
                            alt="Foto profil <?= htmlspecialchars($user['fullname']) ?>">
                        <span class="hero-status-dot" title="Aktif"></span>

                        <?php if (!empty($user['photo'])): ?>
                        <form action="delete-photo.php" method="POST" class="delete-photo-form" id="deletePhotoForm">
                            <button type="submit" class="delete-photo-btn" title="Hapus foto profil">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <div class="hero-info">
                        <span class="hero-eyebrow">Profil Pengguna</span>

                        <div class="hero-name-row">
                            <h1 class="hero-name">
                                <?= $user['fullname'] != '' ? ucwords(strtolower($user['fullname'])) : 'Unknown User'; ?>
                            </h1>
                        </div>

                        <div class="hero-tags">
                            <span class="hero-tag hero-tag-user">
                                <i class="fas fa-at"></i>
                                <span><?= strtoupper($user['username']) ?></span>
                            </span>

                            <span class="hero-tag hero-tag-role">
                                <i class="fas fa-shield-halved"></i>
                                <span><?= strtoupper($user['role']) ?></span>
                            </span>
                        </div>
                    </div>

                    <!-- RINGKASAN AKUN -->
                    <aside class="hero-aside">
                        <div class="ha-top">
                            <div class="ha-ring" style="--pct:<?= $pfPct ?>">
                                <span class="ha-ring-num"><?= $pfPct ?>%</span>
                            </div>

                            <div class="ha-top-text">
                                <strong>Ringkasan Akun</strong>
                                <span>Kelengkapan profil Anda</span>
                            </div>
                        </div>

                        <ul class="ha-list">
                            <?php foreach($pfChecks as $pfLabel => $pfOk): ?>
                            <li class="<?= $pfOk ? 'ok' : 'off' ?>">
                                <i class="fas <?= $pfOk ? 'fa-circle-check' : 'fa-circle-minus' ?>"></i>
                                <?= htmlspecialchars($pfLabel) ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="ha-status">
                            <span class="ha-status-dot"></span>
                            Akun aktif
                        </div>
                    </aside>

                </div>
            </section>

            <div class="row g-4 mb-3">

                <!-- FORM -->
                <div class="col-12 col-xl-7">
                    <div class="panel-card">

                        <div class="card-head">
                            <div class="card-head-icon icon-indigo">
                                <i class="fas fa-user-pen"></i>
                            </div>

                            <div class="card-head-text">
                                <span class="card-eyebrow">Setelan Akun</span>
                                <h2>Informasi Profil</h2>
                                <p>Kelola informasi profil dan pengaturan keamanan Anda.</p>
                            </div>

                            <div class="status-badge">
                                <span></span>
                                Aktif
                            </div>
                        </div>

                        <form action="profile-action.php" method="POST">

                            <div class="field">
                                <label>Nama Lengkap</label>
                                <div class="input-wrap">
                                    <span class="input-ico icon-indigo"><i class="fas fa-user"></i></span>
                                    <input
                                        type="text"
                                        name="fullname"
                                        value="<?= htmlspecialchars($user['fullname']) ?>"
                                        placeholder="Masukkan nama lengkap"
                                        required>
                                </div>
                            </div>

                            <div class="field">
                                <label>Username</label>
                                <div class="input-wrap">
                                    <span class="input-ico icon-blue"><i class="fas fa-at"></i></span>
                                    <input
                                        type="text"
                                        value="<?= htmlspecialchars($_SESSION['username']); ?>"
                                        readonly
                                        name="username">
                                </div>
                            </div>

                            <div class="field">
                                <label>Role</label>
                                <div class="input-wrap">
                                    <span class="input-ico icon-violet"><i class="fas fa-shield-halved"></i></span>
                                    <input
                                        type="text"
                                        value="<?= ucwords(strtolower($user['role'])) ?>"
                                        readonly
                                        name="role">
                                </div>
                            </div>

                            <div class="security-section">

                                <div class="security-header">
                                    <div class="security-head-icon icon-emerald">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div>
                                        <h3>Keamanan</h3>
                                        <span>Ubah kata sandi jika diperlukan</span>
                                    </div>
                                </div>

                                <div class="password-grid">

                                    <div class="field">
                                        <label>Password Baru</label>
                                        <div class="input-wrap has-eye">
                                            <span class="input-ico icon-emerald"><i class="fas fa-key"></i></span>
                                            <input
                                                type="password"
                                                id="passwordInput"
                                                name="password"
                                                autocomplete="new-password"
                                                placeholder="Password baru">
                                            <button type="button" class="eye-btn" data-target="passwordInput" aria-label="Tampilkan password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="field">
                                        <label>Konfirmasi Password</label>
                                        <div class="input-wrap has-eye">
                                            <span class="input-ico icon-amber"><i class="fas fa-check-double"></i></span>
                                            <input
                                                type="password"
                                                id="passwordConfirm"
                                                name="password_confirm"
                                                autocomplete="new-password"
                                                placeholder="Ulangi password">
                                            <button type="button" class="eye-btn" data-target="passwordConfirm" aria-label="Tampilkan konfirmasi password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>

                                        <div class="field-error" id="passwordMatchError" hidden>
                                            <i class="fas fa-circle-exclamation"></i>
                                            <span>Password tidak sama.</span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <button class="save-button" type="submit">
                                <i class="fas fa-save"></i>
                                Simpan Perubahan
                            </button>

                        </form>

                    </div>
                </div>

                <!-- UPLOAD -->
                <div class="col-12 col-xl-5">
                    <div class="panel-card upload-card">

                        <span class="upload-orb"></span>

                        <div class="card-head card-head-compact">
                            <div class="card-head-icon icon-rose">
                                <i class="fas fa-image"></i>
                            </div>

                            <div class="card-head-text">
                                <span class="card-eyebrow">Foto Profil</span>
                                <h2>Gambar Profil</h2>
                                <p>Unggah gambar profil baru untuk akun Anda.</p>
                            </div>
                        </div>

                        <div class="upload-note">
                            <i class="fas fa-circle-info"></i>
                            <span><strong>Maksimal 2 MB</strong> &middot; Format JPG, PNG, GIF</span>
                        </div>

                        <form action="upload-profile.php" method="POST" enctype="multipart/form-data">

                            <div
                                class="upload-area"
                                onclick="document.getElementById('fileInput').click();">

                                <div id="uploadText">
                                    <div class="upload-ico">
                                        <i class="fas fa-cloud-arrow-up"></i>
                                    </div>
                                    <h4>Pilih Gambar</h4>
                                    <p>Klik di sini untuk memilih gambar</p>
                                </div>

                                <img
                                    id="preview"
                                    src=""
                                    alt=""
                                    onclick="document.getElementById('fileInput').click();">

                                <input
                                    type="file"
                                    id="fileInput"
                                    name="photo"
                                    accept="image/*"
                                    onchange="previewImage(event)"
                                    hidden>

                            </div>

                            <button
                                id="saveBtn"
                                class="upload-btn"
                                type="submit">
                                <i class="fas fa-upload"></i>
                                Upload Gambar
                            </button>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php include '../../script/footscript.php'; ?>

    <script>
        // Flash message -> QToast (warna otomatis sesuai tipe)
        <?php if (isset($flashMsg)): ?>
        document.addEventListener('DOMContentLoaded', function () {
            QToast({
                type: '<?= $flashType ?>',
                title: '<?= $flashType === 'error' ? 'Gagal' : 'Berhasil' ?>',
                message: '<?= addslashes($flashMsg) ?>',
            });
        });
        <?php endif; ?>
    </script>

    <script>
        // Konfirmasi hapus foto profil
        const deleteForm = document.getElementById('deletePhotoForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function (e) {
                e.preventDefault();

                QConfirm('Hapus Foto Profil?', 'Foto akan dihapus permanen dan diganti dengan avatar default.', {
                    confirmText: 'Hapus',
                    icon: 'fa-trash-can',
                    confirmClass: 'q-confirm-btn-danger',
                    iconClass: 'q-confirm-icon-danger'
                }).then(function (ok) {
                    if (ok) {
                        deleteForm.submit();
                    }
                });
            });
        }
    </script>

    <script>
        const MAX_SIZE = 2 * 1024 * 1024; // 2 MB

        function previewImage(event) {
            const file = event.target.files[0];
            const input = event.target;

            if (file) {
                if (file.size > MAX_SIZE) {
                    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    QToast({
                        type: 'error',
                        title: 'File Terlalu Besar!',
                        message: `Ukuran file ${sizeMB} MB melebihi batas maksimal 2 MB.`,
                    });

                    // reset input supaya user bisa pilih file lain
                    input.value = '';
                    document.getElementById("preview").style.display = "none";
                    document.getElementById("uploadText").style.display = "block";
                    document.getElementById("saveBtn").style.display = "none";
                    return;
                }

                const preview = document.getElementById("preview");
                preview.src = URL.createObjectURL(file); // langsung pakai object URL
                preview.style.display = "block";

                document.getElementById("uploadText").style.display = "none";
                document.getElementById("saveBtn").style.display = "inline-flex";
            }
        }
    </script>

    <!-- Password: eye toggle + validasi konfirmasi langsung -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pw = document.getElementById('passwordInput');
            const cf = document.getElementById('passwordConfirm');
            const err = document.getElementById('passwordMatchError');

            // Toggle tampilkan / sembunyikan password
            document.querySelectorAll('.eye-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(this.dataset.target);
                    if (!input) return;

                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';

                    this.classList.toggle('is-on', show);
                    this.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');

                    const icon = this.querySelector('i');
                    if (icon) icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';

                    input.focus();
                });
            });

            if (!pw || !cf) return;

            function checkMatch() {
                const filled = cf.value !== '';
                const match = pw.value === cf.value;
                const show = filled && !match;

                if (err) err.hidden = !show;
                cf.classList.toggle('is-invalid', show);
                const wrap = cf.closest('.input-wrap');
                if (wrap) wrap.classList.toggle('is-invalid', show);
            }

            pw.addEventListener('input', checkMatch);
            cf.addEventListener('input', checkMatch);

            const form = cf.closest('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (pw.value !== '' && pw.value !== cf.value) {
                        e.preventDefault();
                        checkMatch();
                        if (err) {
                            err.hidden = false;
                            err.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        cf.focus();
                    }
                });
            }
        });
    </script>

    <!-- Sweetalert -->
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const msg = urlParams.get('msg');

        if (status && msg) {
            QToast({
                type: status === 'success' ? 'success' : 'error',
                title: status === 'success' ? 'Berhasil!' : 'Error',
                message: msg,
            });

            // 🔥 hapus parameter dari URL setelah tampil
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>

    <!-- Galaksi di dalam kartu hero: starfield warp (bintang meluncur dari tengah) -->
    <script>
        (function () {
            const canvas = document.getElementById('galaxyCanvas');
            if (!canvas) return;

            const host = canvas.parentElement; // kartu .profile-hero
            const ctx = canvas.getContext('2d');
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            let W = 0, H = 0, CX = 0, CY = 0, DPR = 1;
            let stars = [];
            let raf = null;
            let last = 0;
            let bgGrad = null;   // gradasi langit (seperti sidebar)

            const SPEED = 0.6;   // kecepatan luncur (z per detik)

            function rand(a, b) { return a + Math.random() * (b - a); }

            function newStar(deep) {
                const angle = Math.random() * Math.PI * 2;
                const radius = Math.sqrt(Math.random()); // sebaran merata di piringan
                return {
                    x: Math.cos(angle) * radius,
                    y: Math.sin(angle) * radius,
                    z: deep ? rand(0.05, 1) : 1,
                    hue: Math.random() < 0.55 ? rand(190, 230) : rand(250, 295),
                    base: rand(1.1, 2.2)
                };
            }

            function seed() {
                const count = Math.max(140, Math.min(420, Math.round((W * H) / 2600)));
                stars = [];
                for (let i = 0; i < count; i++) stars.push(newStar(true));
            }

            function resize() {
                DPR = Math.min(window.devicePixelRatio || 1, 2);
                W = Math.max(1, Math.round(host.clientWidth));
                H = Math.max(1, Math.round(host.clientHeight));
                canvas.width = Math.floor(W * DPR);
                canvas.height = Math.floor(H * DPR);
                ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
                CX = W / 2;
                CY = H / 2;

                // langit senada sidebar/navbar (#0f172a -> #1e293b)
                bgGrad = ctx.createLinearGradient(0, 0, 0, H);
                bgGrad.addColorStop(0, 'rgba(15,23,42,0.30)');
                bgGrad.addColorStop(1, 'rgba(30,41,59,0.30)');
            }

            function projectAt(s, z) {
                return {
                    x: CX + (s.x / z) * CX,
                    y: CY + (s.y / z) * CY
                };
            }

            function paintStatic() {
                ctx.clearRect(0, 0, W, H);
                for (const s of stars) {
                    const p = projectAt(s, s.z);
                    if (p.x < -20 || p.x > W + 20 || p.y < -20 || p.y > H + 20) continue;
                    const size = Math.max(0.4, (1 - s.z) * s.base);
                    ctx.fillStyle = 'hsla(' + s.hue + ',100%,80%,' + (0.35 + (1 - s.z) * 0.6) + ')';
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, size, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            function frame(t) {
                const dt = last ? Math.min((t - last) / 1000, 0.05) : 0.016;
                last = t;

                // jejak / motion blur (langit senada sidebar)
                ctx.fillStyle = bgGrad || 'rgba(15,23,42,0.28)';
                ctx.fillRect(0, 0, W, H);

                for (const s of stars) {
                    const pz = s.z;
                    s.z -= SPEED * dt;

                    if (s.z <= 0.04) {
                        Object.assign(s, newStar(false));
                        continue;
                    }

                    const cur = projectAt(s, s.z);
                    const prev = projectAt(s, pz);

                    if ((cur.x < -40 || cur.x > W + 40 || cur.y < -40 || cur.y > H + 40) &&
                        (prev.x < -40 || prev.x > W + 40 || prev.y < -40 || prev.y > H + 40)) {
                        continue;
                    }

                    const alpha = Math.min(1, (1 - s.z) * 1.3);
                    const size = Math.max(0.5, (1 - s.z) * s.base);

                    ctx.strokeStyle = 'hsla(' + s.hue + ',100%,82%,' + alpha + ')';
                    ctx.lineWidth = size;
                    ctx.beginPath();
                    ctx.moveTo(prev.x, prev.y);
                    ctx.lineTo(cur.x, cur.y);
                    ctx.stroke();
                }

                raf = requestAnimationFrame(frame);
            }

            resize();
            seed();

            if (reduce) {
                paintStatic();
            } else {
                const g0 = ctx.createLinearGradient(0, 0, 0, H);
                g0.addColorStop(0, '#0f172a');
                g0.addColorStop(1, '#1e293b');
                ctx.fillStyle = g0;
                ctx.fillRect(0, 0, W, H);
                raf = requestAnimationFrame(frame);
            }

            let rt;
            function scheduleResize() {
                clearTimeout(rt);
                rt = setTimeout(function () {
                    resize();
                    seed();
                    if (reduce) paintStatic();
                }, 120);
            }

            if ('ResizeObserver' in window) {
                new ResizeObserver(scheduleResize).observe(host);
            } else {
                window.addEventListener('resize', scheduleResize);
            }

            document.addEventListener('visibilitychange', function () {
                if (reduce) return;
                if (document.hidden) {
                    if (raf) { cancelAnimationFrame(raf); raf = null; }
                } else if (!raf) {
                    last = 0;
                    raf = requestAnimationFrame(frame);
                }
            });
        })();
    </script>
</body>

</html>
