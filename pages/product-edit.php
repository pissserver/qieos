<?php
include '../sessions/session.php';
$query = mysqli_query($conn, "SELECT * FROM products WHERE id='" . $_GET['id'] . "'");
$row = mysqli_fetch_assoc($query);
?>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Edit Produk - Cartify</title>

    <?php include '../script/headscript.php'; ?>
</head>

<body>
    <?php include '../components/sidebar.php'; ?>

    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <a href="product-add.php"
            class=" mt-5 btn btn-gray-800 d-inline-flex align-items-center me-2 dropdown-toggle"
            aria-haspopup="true"
            aria-expanded="false">
            <svg
                class="icon icon-xs me-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Produk
        </a>

        <div class="row mt-3">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow">

                    <div class="card-header">
                        <h5 class="mb-0">Edit Produk</h5>
                        <small class="text-muted">Edit informasi produk yang sudah ada dalam sistem</small>
                    </div>

                    <div class="card-body">

                        <form action="product-action.php?action=edit&id=<?php echo $_GET['id']; ?>" method="POST" enctype="multipart/form-data">

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
                                            <option value="makanan" <?php echo ($row['category'] == 'makanan') ? 'selected' : ''; ?>>Makanan</option>
                                            <option value="minuman" <?php echo ($row['category'] == 'minuman') ? 'selected' : ''; ?>>Minuman</option>
                                            <option value="jajanan" <?php echo ($row['category'] == 'jajanan') ? 'selected' : ''; ?>>Jajanan</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Nama Produk -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Nama Produk</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-box"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="product_name"
                                            class="form-control"
                                            value="<?php echo $row['product_name']; ?>"
                                            placeholder="Masukkan nama produk"
                                            required>
                                    </div>
                                </div>

                                <!-- Harga -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </span>
                                        <input
                                            type="number"
                                            name="price"
                                            class="form-control"
                                            value="<?php echo $row['price']; ?>"
                                            placeholder="Masukkan harga produk"
                                            required>
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
                                            onchange="previewImage(event)">
                                    </div>
                                </div>

                                <!-- Preview Gambar -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Preview Gambar</label>

                                    <div class="border rounded p-3 text-center">
                                        <img
                                            id="preview-product"
                                            src="../assets/img/uploads/<?php echo $row['photo']; ?>"
                                            style="max-width:100%; height:auto;">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <!-- Tombol -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" name="edit_product" class="btn btn-primary">
                                    Edit Produk
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