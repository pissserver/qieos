<?php
include '../../sessions/session.php';
?>

<style>
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }

    .dataTables_length select {
        min-width: 70px;
        padding-right: 20px;
    }
</style>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pembelian Stok - Qieos</title>
    <?php include '../../script/headscript.php'; ?>
</head>

<body>
<?php include '../../components/sidebar.php'; ?>

<main class="content">
<?php include '../../components/navbar.php'; ?>

<div class="row mt-5">
<div class="col-12">
<div class="card border-0 shadow">

    <div class="card-header">
        <h5 class="mb-0">Pembelian Stok</h5>
        <small class="text-muted">Barang masuk ke Gudang Stok (FIFO)</small>
    </div>

    <div class="card-body">
        <form id="form-stock" action="stock-action.php?action=stock_in" method="POST" enctype="multipart/form-data">

            <div class="row">

                <!-- Nama Produk -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="product_name" placeholder="Masukkan nama produk" class="form-control" required>
                </div>

                <!-- Kode Produk -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Kode Produk</label>
                    <input type="text" name="code" placeholder="Masukkan kode produk" class="form-control" required>
                </div>

                <!-- Kategori -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                        <option value="jajanan">Jajanan</option>
                    </select>
                </div>

                <!-- Qty -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Jumlah (Qty)</label>
                    <input type="number" name="qty" placeholder="Masukkan jumlah" class="form-control" min="1" required>
                </div>

                <!-- Satuan -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="unit" placeholder="Masukkan satuan (misal: pcs, kg)" class="form-control" required>
                </div>

                <!-- Harga Beli -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Harga Beli</label>
                    <input type="number" name="buy_price" placeholder="Masukkan harga beli" class="form-control" min="0" required>
                </div>

                <!-- Harga Jual -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Harga Jual</label>
                    <input type="number" name="sell_price" placeholder="Masukkan harga jual" class="form-control" min="0" required>
                </div>

                <!-- Gambar -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Gambar</label>
                    <input type="file" name="photo" placeholder="Pilih gambar" class="form-control" accept="image/*">
                </div>

                <!-- Catatan -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="note" placeholder="Masukkan catatan" class="form-control"></textarea>
                </div>

            </div>

            <hr>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    Simpan Pembelian
                </button>
            </div>

        </form>
    </div>

</div>
</div>
</div>

<!-- LIST STOK GUDANG (rekap) -->
<div class="card border-0 shadow mt-4 mb-5">
    <div class="card-header">
        <h6 class="mb-0">Stok Gudang (Rekap FIFO)</h6>
    </div>

    <div class="card-body table-responsive">
        <div class="card-body table-responsive">
            <div id="table-stock"></div>
        </div>
    </div>
</div>

</main>

    <?php include '../../script/footscript.php'; ?>

    <!-- Ajax action -->
    <script>
        document.getElementById("form-stock").addEventListener("submit", function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch(this.action, {
                method: "POST",
                body: formData
            })
            .then(res => res.text()) // 🔥 ubah dulu ke text
            .then(data => {
                console.log("RESPONSE:", data); // 🔥 lihat di console

                try {
                    let json = JSON.parse(data);

                    if (json.status === "success") {
                        Swal.fire("Berhasil", json.msg, "success");
                        this.reset();
                        loadTable();
                    } else {
                        Swal.fire("Error", json.msg, "error");
                    }

                } catch (e) {
                    console.error("Bukan JSON:", data);
                    Swal.fire("Error", "Response bukan JSON", "error");
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire("Error", "Fetch gagal", "error");
            });
        });
    </script>

    <!-- Reload table -->
    <script>
        function loadTable() {
            fetch('stock-table.php')
            .then(res => res.text())
            .then(html => {
                document.getElementById("table-stock").innerHTML = html;

                setTimeout(() => {
                    if ($.fn.DataTable.isDataTable('#stockTable')) {
                        $('#stockTable').DataTable().destroy();
                    }

                    $('#stockTable').DataTable({
                        pageLength: 5,
                        lengthMenu: [5, 10, 25, 50],
                        responsive: true,
                        autoWidth: false
                    });

                }, 100);
            });
        }

        loadTable();
    </script>
</body>
</html>