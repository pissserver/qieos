<?php
include '../../sessions/session.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/profile.css">

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Profile - Qieos</title>

    <?php include '../../script/headscript.php'; ?>
</head>

<body>
    <?php include '../components/sidebar.php'; ?>

    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <div class="row mt-5">
            <div class="col-12 col-xl-7">
                <div class="profile-panel">

                    <div class="panel-header">
                        <div>
                            <span class="panel-subtitle">SETELAN AKUN</span>
                            <h2>Informasi Profil</h2>
                            <p>Kelola informasi profil dan pengaturan keamanan Anda.</p>
                        </div>

                        <div class="status-badge">
                            <span></span>
                            Aktif
                        </div>
                    </div>

                    <?php
                    if (isset($_SESSION['flash'])) {
                        echo '<div class="success-box">' . $_SESSION['flash'] . '</div>';
                        unset($_SESSION['flash']);
                    }
                    ?>

                    <form action="profile-action.php" method="POST">

                        <div class="field">
                            <label>Nama Lengkap</label>
                            <input
                                type="text"
                                name="fullname"
                                value="<?= $user['fullname'] ?>"
                                placeholder="Enter your full name"
                                required>
                        </div>

                        <div class="field">
                            <label>Username</label>
                            <input
                                type="text"
                                value="<?= $_SESSION['username']; ?>"
                                readonly
                                name="username">
                        </div>

                        <div class="field">
                            <label>Role</label>
                            <input
                                type="text"
                                value="<?= ucwords(strtolower($user['role'])) ?>"
                                readonly
                                name="role">
                        </div>

                        <div class="security-section">

                            <div class="security-header">
                                <h3>Keamanan</h3>
                                <span>Ubah kata sandi jika diperlukan</span>
                            </div>

                            <div class="password-grid">

                                <div class="field">
                                    <label>Password Baru</label>
                                    <input
                                        type="password"
                                        name="password"
                                        autocomplete="new-password"
                                        placeholder="New password">
                                </div>

                                <div class="field">
                                    <label>Konfirmasi Password</label>
                                    <input
                                        type="password"
                                        name="password_confirm"
                                        autocomplete="new-password"
                                        placeholder="Confirm password">
                                </div>

                            </div>

                        </div>

                        <button class="save-button" type="submit">
                            Simpan Perubahan &nbsp;<i class="fas fa-save"></i>
                        </button>

                    </form>

                </div>
            </div>
            
            <div class="col-12 col-xl-5 mb-5">

                <div class="profile-card">

                    <div class="profile-cover">

                        <div class="profile-overlay"></div>

                        <img
                            src="<?php echo $user['photo'] ? BASE_URL . '/assets/img/uploads/' . $user['photo'] : BASE_URL . '/assets/img/default-avatar.jpg'; ?>"
                            class="profile-avatar">

                    </div>

                    <div class="profile-content">

                        <span class="role-badge">
                            <?= strtoupper($user['role']) ?>
                        </span>

                        <h2>
                            <?= $user['fullname'] != '' ? ucwords(strtolower($user['fullname'])) : 'Unknown User'; ?>
                        </h2>

                        <span class="username-badge">
                            <?= strtoupper($user['username']) ?>
                        </span>

                        <div class="profile-divider"></div>

                        <div class="profile-info">

                            <div>
                                <span>Akun</span>
                                <strong>Terverifikasi</strong>
                            </div>

                            <div>
                                <span>Status</span>
                                <strong style="color:#22c55e;">Aktif</strong>
                            </div>

                        </div>

                    </div>

                </div>



                <div class="upload-card">

                    <div class="upload-title">
                        <h3>Gambar Profil</h3>
                        <span>Upload gambar profil baru</span>
                    </div>

                    <form action="upload-profile.php" method="POST" enctype="multipart/form-data">

                        <div
                            class="upload-area"
                            onclick="document.getElementById('fileInput').click();">

                            <div id="uploadText">

                                <i class="fas fa-cloud-upload-alt"></i>

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

                            Upload Gambar &nbsp;<i class="fas fa-upload"></i>

                        </button>

                    </form>

                </div>

            </div>
        </div>
    </main>

    <?php include '../../script/footscript.php'; ?>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const preview = document.getElementById("preview");
                preview.src = URL.createObjectURL(file); // langsung pakai object URL
                preview.style.display = "block";

                document.getElementById("uploadText").style.display = "none";
                document.getElementById("saveBtn").style.display = "inline-block";
            }
        }
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
</body>

</html>