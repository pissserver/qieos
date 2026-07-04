<?php
include '../../sessions/session.php';
?>

<style>
    .profile-panel{
        margin-bottom:50px;
        background:#ffffff;
        border-radius:32px;
        padding:40px;
        box-shadow:
        0 10px 30px rgba(15,23,42,.04),
        0 20px 80px rgba(15,23,42,.08);
        position:relative;
        overflow:hidden;
    }

    .profile-panel::before{
        content:"";
        position:absolute;
        top:-120px;
        right:-120px;
        width:250px;
        height:250px;
        background:radial-gradient(
            circle,
            rgba(99,102,241,.15),
            transparent
        );
    }

    .panel-subtitle{
        font-size:.75rem;
        letter-spacing:2px;
        color:#6366f1;
        font-weight:700;
    }

    .panel-header{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        margin-bottom:40px;
    }

    .panel-header h2{
        font-size:2rem;
        font-weight:800;
        color:#0f172a;
        margin-top:8px;
        margin-bottom:8px;
    }

    .panel-header p{
        color:#64748b;
        margin:0;
    }

    .status-badge{
        display:flex;
        align-items:center;
        gap:10px;
        padding:10px 18px;
        border-radius:100px;
        background:#f8fafc;
        border:1px solid #e2e8f0;
        font-weight:600;
    }

    .status-badge span{
        width:10px;
        height:10px;
        background:#22c55e;
        border-radius:50%;
    }

    .field{
        margin-bottom:24px;
    }

    .field label{
        display:block;
        margin-bottom:10px;
        font-size:.85rem;
        font-weight:600;
        color:#64748b;
    }

    .field input{
        width:100%;
        height:62px;
        border:none;
        outline:none;
        border-radius:18px;
        background:#f8fafc;
        padding:0 22px;
        font-size:15px;
        transition:.25s;
    }

    .field input:focus{
        background:white;
        box-shadow:
        0 0 0 4px rgba(99,102,241,.10);
    }

    .field input[readonly]{
        color:#64748b;
    }

    .security-section{
        margin-top:35px;
        padding-top:30px;
        border-top:1px solid #e2e8f0;
    }

    .security-header{
        margin-bottom:25px;
    }

    .security-header h3{
        font-size:1.1rem;
        font-weight:700;
        margin-bottom:4px;
    }

    .security-header span{
        color:#64748b;
    }

    .password-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:20px;
    }

    .save-button{
        margin-top:20px;
        height:60px;
        border:none;
        width:100%;
        border-radius:18px;
        font-weight:700;
        font-size:15px;
        color:white;
        background:
        linear-gradient(
            135deg,
            #6366f1,
            #2563eb
        );
        transition:.3s;
    }

    .save-button:hover{
        transform:translateY(-2px);
        box-shadow:
        0 20px 40px rgba(37,99,235,.25);
    }

    .success-box{
        padding:16px 20px;
        border-radius:16px;
        background:#ecfdf5;
        color:#065f46;
        margin-bottom:25px;
        font-weight:600;
    }

    @media(max-width:768px){

        .profile-panel{
            padding:24px;
        }

        .panel-header{
            flex-direction:column;
            gap:15px;
        }

        .password-grid{
            grid-template-columns:1fr;
        }
    }

    /* Profile Preview */
    .profile-card{

        background:white;
        border-radius:32px;
        overflow:hidden;
        box-shadow:
        0 15px 50px rgba(15,23,42,.06);

        margin-bottom:30px;

    }

    .profile-cover{

        height:120px;

        background:
        linear-gradient(135deg,#6366f1,#2563eb,#0ea5e9);

        position:relative;

    }

    .profile-overlay{

        position:absolute;
        inset:0;

        background:
        radial-gradient(circle at top right,
        rgba(255,255,255,.35),
        transparent 45%);

    }

    .profile-avatar{

        width:145px;
        height:145px;

        border-radius:50%;

        object-fit:cover;

        border:6px solid white;

        position:absolute;

        left:50%;
        bottom:-72px;

        transform:translateX(-50%);

        box-shadow:
        0 20px 40px rgba(15,23,42,.18);

    }

    .profile-content{

        padding:95px 35px 35px;
        text-align:center;

    }

    .role-badge{

        display:inline-block;

        padding:8px 16px;

        background:#eef2ff;

        color:#4f46e5;

        border-radius:100px;

        font-size:12px;

        font-weight:700;

        letter-spacing:1px;

        margin-bottom:18px;

    }

    .profile-content h2{

        font-size:30px;

        font-weight:800;

        color:#0f172a;

        margin-bottom:8px;

    }

    .profile-content p{

        color:#64748b;

        margin-bottom:20px;

    }

    .profile-divider{

        height:1px;

        background:#e2e8f0;

        margin-bottom:15px;

    }

    .profile-info{

        display:flex;

        justify-content:space-around;

    }

    .profile-info span{

        display:block;

        color:#94a3b8;

        font-size:13px;

        margin-bottom:6px;

    }

    .profile-info strong{

        font-size:16px;

        color:#0f172a;

    }

    .upload-card{

        background:white;

        border-radius:32px;

        padding:35px;

        box-shadow:
        0 15px 50px rgba(15,23,42,.06);

    }

    .upload-title{

        margin-bottom:25px;

    }

    .upload-title h3{

        font-size:20px;

        font-weight:700;

        margin-bottom:5px;

    }

    .upload-title span{

        color:#64748b;

    }

    .upload-area{

        border:2px dashed #cbd5e1;

        border-radius:26px;

        height:245px;

        display:flex;

        justify-content:center;

        align-items:center;

        text-align:center;

        cursor:pointer;

        transition:.35s;

        background:#fafafa;

        position:relative;

    }

    .upload-area:hover{

        border-color:#6366f1;

        background:#f5f7ff;

    }

    .upload-area i{

        font-size:55px;

        color:#6366f1;

        margin-bottom:15px;

    }

    .upload-area h4{

        font-size:22px;

        margin-bottom:5px;

    }

    .upload-area p{

        color:#64748b;

    }

    #preview{

        display:none;

        width:100%;
        height:100%;

        object-fit:cover;

        border-radius:24px;

    }

    .upload-btn{

        display:none;

        width:100%;

        margin-top:25px;

        height:58px;

        border:none;

        border-radius:18px;

        background:
        linear-gradient(135deg,#6366f1,#2563eb);

        color:white;

        font-size:15px;

        font-weight:700;

        transition:.3s;

    }

    .upload-btn:hover{

        transform:translateY(-2px);

        box-shadow:
        0 20px 40px rgba(37,99,235,.25);

    }

    @media(max-width:768px){

    .profile-avatar{

    width:120px;
    height:120px;
    bottom:-60px;

    }

    .profile-content{

    padding:80px 25px 25px;

    }

    .profile-info{

    flex-direction:column;
    gap:20px;

    }

    }
</style>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Profile - Cartify</title>

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
                            <label>Alamat Email</label>
                            <input
                                type="email"
                                value="<?= $_SESSION['email']; ?>"
                                readonly
                                name="email">
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
                            src="<?php echo $user['photo'] ? '/qieos/assets/img/uploads/' . $user['photo'] : '/qieos/assets/img/default-avatar.jpg'; ?>"
                            class="profile-avatar">

                    </div>

                    <div class="profile-content">

                        <span class="role-badge">
                            <?= strtoupper($user['role']) ?>
                        </span>

                        <h2>
                            <?= $user['fullname'] != '' ? ucwords(strtolower($user['fullname'])) : 'Unknown User'; ?>
                        </h2>

                        <p>
                            <?= $_SESSION['email']; ?>
                        </p>

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
            Swal.fire({
                icon: status === 'success' ? 'success' : 'error',
                title: status === 'success' ? 'Berhasil!' : 'Error',
                text: msg,
            });

            // 🔥 hapus parameter dari URL setelah tampil
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</body>

</html>