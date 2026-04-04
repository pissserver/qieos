<?php
    include '../sessions/session.php';
?>

<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Primary Meta Tags -->
        <title>Tambah Produk - Cartify</title>
        
        <?php include '../script/headscript.php'; ?>
    </head>

    <body>
        <?php include '../components/sidebar.php'; ?>

        <main class="content">
            <?php include '../components/navbar.php'; ?>

            <div class="row mt-5">
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow">

                        <div class="card-header">
                            <h5 class="mb-0">Tambah Produk</h5>
                            <small class="text-muted">Masukkan data produk baru</small>
                        </div>

                        <div class="card-body">

                            <form action="product-action.php?action=add" method="POST" enctype="multipart/form-data">

                                <div class="row">
                                    <!-- Kategori -->
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">Kategori</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-box"></i>
                                            </span>
                                            <select name="category" id="category" class="form-control" required>
                                                <option value="">Pilih Kategori</option>
                                                <option value="makanan">Makanan</option>
                                                <option value="minuman">Minuman</option>
                                                <option value="jajanan">Jajanan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Nama Produk -->
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">Nama Produk</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-box"></i>
                                            </span>
                                            <input 
                                                type="text"
                                                name="product_name"
                                                class="form-control"
                                                placeholder="Masukkan nama produk"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <!-- Harga -->
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">Harga</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </span>
                                            <input 
                                                type="number"
                                                name="price"
                                                class="form-control"
                                                placeholder="Masukkan harga produk"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <!-- Gambar Produk -->
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Gambar Produk</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-image"></i>
                                            </span>

                                            <input
                                            type="file"
                                            name="photo"
                                            class="form-control"
                                            accept="image/*"
                                            onchange="previewImage(event)"
                                            >
                                        </div>
                                    </div>

                                    <!-- Preview Gambar -->
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Preview Gambar</label>

                                        <div class="border rounded p-3 text-center">
                                            <img
                                            id="preview-product"
                                            style="max-width:100%; height:auto;"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <!-- Tombol -->
                                <div class="d-flex justify-content-end">

                                    <button type="reset" class="btn btn-light me-2">
                                        Reset
                                    </button>

                                    <button type="submit" name="save_product" class="btn btn-primary">
                                        Simpan Produk
                                    </button>

                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Produk -->
            <div class="mb-5">
                <?php include '../components/tables/product-table.php'; ?>
            </div>
        </main>

        <?php include '../script/footscript.php'; ?>

        <!-- Preview Gambar -->
        <script>
            function previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const preview = document.getElementById("preview-product");
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = "block";
                }
            }
        </script>

        <!-- Sweetalert -->
        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const msg = urlParams.get('msg');

            if(status && msg){
                Swal.fire({
                    icon: status === 'success' ? 'success' : 'error',
                    title: status === 'success' ? 'Sukses' : 'Error',
                    text: msg,
                });

                // 🔥 hapus parameter dari URL setelah tampil
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        </script>
    </body>
</html>
