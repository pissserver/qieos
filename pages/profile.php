<?php
include '../sessions/session.php';

$currentState = $user['state'];
?>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Profile - Cartify</title>

    <?php include '../script/headscript.php'; ?>
</head>

<body>
    <?php include 'components/sidebar.php'; ?>

    <main class="content">
        <?php include 'components/navbar.php'; ?>

        <div class="row mt-5">
            <div class="col-12 col-xl-7">
                <div class="card card-body border-0 shadow mb-4">
                    <?php
                    if (isset($_SESSION['flash'])) {
                        echo '<div class="alert alert-success mt-3">' . $_SESSION['flash'] . '</div>';
                        unset($_SESSION['flash']); // hapus setelah ditampilkan
                    }
                    ?>

                    <h2 class="h5 mb-4">Informasi Akun</h2>
                    <form action="profile-action.php" method="POST">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div>
                                    <label for="first_name">Nama Lengkap</label>
                                    <input
                                        class="form-control"
                                        id="fullname"
                                        type="text"
                                        placeholder="Nama lengkap"
                                        value="<?= $user['fullname'] ?>"
                                        name="fullname"
                                        required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input
                                        class="form-control"
                                        id="email"
                                        type="email"
                                        value="<?php echo $_SESSION['email']; ?>"
                                        name="email"
                                        required
                                        readonly />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Pilih Status Kepemilikan</option>
                                        <option value="owner" <?= $user['status'] === 'owner' ? 'selected' : '' ?>>Owner</option>
                                        <option value="staff" <?= $user['status'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="city">Kota</label>
                                    <input
                                        class="form-control"
                                        id="city"
                                        type="text"
                                        placeholder="Kota tempat tinggal"
                                        value="<?= $user['city'] ?>"
                                        name="city"
                                        required />
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="state">Provinsi</label>
                                <select class="form-select w-100 mb-0" id="state" name="state" aria-label="State select example">
                                    <option selected>Pilih Provinsi</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button
                                class="btn btn-gray-800 animate-up-2"
                                type="submit">
                                Simpan &nbsp;<i class="fas fa-save"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-xl-5">
                <div class="row">
                    <!-- Kartu profil -->
                    <div class="col-12 mb-4">
                        <div class="card shadow border-0 text-center p-0">
                            <div class="profile-cover rounded-top"
                                data-background="../assets/img/profile-cover.jpg"></div>
                            <div class="card-body text-center pb-4"
                                style="background: linear-gradient(135deg, #f9f9f9, #ffffff); 
                                            border-radius: 16px; 
                                            box-shadow: 0 8px 20px rgba(0,0,0,0.08); 
                                            padding: 40px 20px;">

                                <!-- Foto profil -->
                                <img src="<?php echo $user['photo'] ? $user['photo'] : '../assets/img/default-avatar.jpg'; ?>"
                                    class="mt-n7 mb-4"
                                    style="object-fit: cover; 
                                                width: 120px; 
                                                height: 120px; 
                                                border-radius: 50%; 
                                                border: 4px solid #fff; 
                                                box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
                                    alt="User Portrait" />

                                <!-- Nama -->
                                <h4 style="font-size: 1.6rem; 
                                            font-weight: 700; 
                                            color: #333; 
                                            margin-bottom: 6px;">
                                    <?php echo $user['fullname'] != '' ? ucwords(strtolower($user['fullname'])) : 'Nama tidak tersedia'; ?>
                                </h4>

                                <!-- Status -->
                                <h5 style="font-size: 1rem; 
                                            font-weight: 400; 
                                            color: #777; 
                                            margin-bottom: 12px;">
                                    <?php echo $user['status'] != '' ? ucwords(strtolower($user['status'])) : 'Status tidak tersedia'; ?>
                                </h5>

                                <?php
                                // Ambil data provinsi dari API
                                $provinces = json_decode(file_get_contents("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json"), true);

                                // Cari nama provinsi berdasarkan ID di database
                                $stateName = "";
                                foreach ($provinces as $prov) {
                                    if ($prov['id'] == $user['state']) {
                                        $stateName = $prov['name'];
                                        break;
                                    }
                                }
                                ?>

                                <!-- Lokasi -->
                                <p style="font-size: 0.95rem; 
                                            color: #555; 
                                            background: #f1f1f1; 
                                            display: inline-block; 
                                            padding: 6px 14px; 
                                            border-radius: 20px; 
                                            font-weight: 500;">
                                    <?php echo $user['city'] != '' ? ucwords(strtolower($user['city'])) : 'Kota tidak tersedia'; ?>,
                                    <?php echo $user['state'] != '' ? ucwords(strtolower($stateName)) : 'Provinsi tidak tersedia'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form upload foto -->
                    <div class="col-12">
                        <div class="card card-body border-0 shadow mb-4">
                            <h2 class="h5 mb-4">Upload Foto</h2>
                            <form action="upload-profile.php" method="POST" enctype="multipart/form-data">
                                <div class="d-flex">

                                    <!-- Box upload -->
                                    <div class="upload-box rounded position-relative me-3"
                                        style="cursor:pointer; width:150px; height:150px; border:2px dashed #000;"
                                        onclick="document.getElementById('fileInput').click();">

                                        <!-- Default tampilan -->
                                        <div id="uploadText" class="d-flex flex-column align-items-center justify-content-center h-100">
                                            <span style="font-size:24px; color:#0d6efd;">+</span>
                                        </div>

                                        <!-- Preview gambar -->
                                        <img id="preview" src="" alt="Preview"
                                            style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; border-radius:6px; cursor:pointer; padding: 3px;"
                                            onclick="document.getElementById('fileInput').click();" />

                                        <!-- Input file -->
                                        <input type="file" id="fileInput" name="photo" accept="image/*"
                                            onchange="previewImage(event)" style="display:none;" />
                                    </div>

                                    <!-- Sisi kanan biar tidak kosong -->
                                    <div class="flex-grow-1 d-flex flex-column justify-content-center">
                                        <p class="text-muted mb-2">Pilih foto sebagai profile.</p>
                                        <small class="text-secondary">Format: JPG, PNG, max 2MB</small>
                                        <!-- Tombol simpan di bawah -->
                                        <button type="submit" id="saveBtn" class="btn btn-primary mt-3" style="display:none;">Simpan &nbsp;<i class="fas fa-save"></i></button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../script/footscript.php'; ?>

    <!-- Ambil data provinsi dari API -->
    <script>
        // Ambil nilai state dari PHP
        const currentState = "<?php echo $currentState; ?>";

        // Ambil data provinsi dari API
        fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json")
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById("state");
                data.forEach(prov => {
                    const opt = document.createElement("option");
                    opt.value = prov.id; // simpan nama provinsi
                    opt.textContent = prov.name; // tampilkan nama provinsi
                    if (prov.id === currentState) {
                        opt.selected = true; // tandai sebagai selected
                    }
                    select.appendChild(opt);
                });
            })
            .catch(error => console.error("Gagal memuat data provinsi:", error));
    </script>

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