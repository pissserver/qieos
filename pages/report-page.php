<?php
    include '../sessions/session.php';
?>

<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Primary Meta Tags -->
        <title>Laporan - Cartify</title>
        
        <?php include '../script/headscript.php'; ?>
    </head>

    <body>
        <?php include '../components/sidebar.php'; ?>

        <main class="content">
            <?php include '../components/navbar.php'; ?>

            <div class="row mt-5">
                <div class="col-12 col-md-6 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-header" style="background-color: #1F2937;">
                            <h5 class="mb-0" style="color: white;">Laporan Omzet Harian</h5>
                            <small class="text-muted">Laporan omzet harian berdasarkan data penjualan</small>
                        </div>

                        <div class="card-body">
                            <form action="report/report-omzet-daily.php" method="POST" enctype="multipart/form-data" target="_blank">
                                <div class="row">
                                    <!-- Tanggal -->
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">Tanggal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input 
                                                type="date"
                                                name="date"
                                                class="form-control"
                                                placeholder="Masukkan tanggal"
                                                required
                                            >
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <!-- Tombol -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" name="save_product" class="btn btn-primary">
                                        <i class="fas fa-chart-bar"></i>&nbsp; Lihat Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-header" style="background-color: #1F2937;">
                            <h5 class="mb-0" style="color: white;">Laporan Omzet Bulanan</h5>
                            <small class="text-muted">Laporan omzet bulanan berdasarkan data penjualan</small>
                        </div>

                        <div class="card-body">
                            <form action="report/report-omzet-monthly.php" method="POST" enctype="multipart/form-data" target="_blank">
                                <div class="row">
                                    <!-- Tanggal Awal -->
                                    <div class="col-6 col-md-6 mb-4">
                                        <label class="form-label">Tanggal Awal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input 
                                                type="date"
                                                name="start_date"
                                                class="form-control"
                                                placeholder="Masukkan tanggal"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <!-- Tanggal Akhir -->
                                    <div class="col-6 col-md-6 mb-4">
                                        <label class="form-label">Tanggal Akhir</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input 
                                                type="date"
                                                name="end_date"
                                                class="form-control"
                                                placeholder="Masukkan tanggal"
                                                required
                                            >
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <!-- Tombol -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" name="save_product" class="btn btn-primary">
                                        <i class="fas fa-chart-bar"></i>&nbsp; Lihat Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12 col-md-6 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-header" style="background-color: #1F2937;">
                            <h5 class="mb-0" style="color: white;">Laporan Penjualan</h5>
                            <small class="text-muted">Laporan penjualan berdasarkan data pelanggan</small>
                        </div>

                        <div class="card-body">
                            <form action="report/report-sales-customer.php" method="POST" enctype="multipart/form-data" target="_blank">
                                <div class="row">
                                    <!-- Tanggal Awal -->
                                    <div class="col-6 col-md-6 mb-4">
                                        <label class="form-label">Tanggal Awal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input 
                                                type="date"
                                                name="start_date"
                                                class="form-control"
                                                placeholder="Masukkan tanggal"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <!-- Tanggal Akhir -->
                                    <div class="col-6 col-md-6 mb-4">
                                        <label class="form-label">Tanggal Akhir</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input 
                                                type="date"
                                                name="end_date"
                                                class="form-control"
                                                placeholder="Masukkan tanggal"
                                                required
                                            >
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <!-- Tombol -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" name="save_product" class="btn btn-primary">
                                        <i class="fas fa-chart-bar"></i>&nbsp; Lihat Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-header" style="background-color: #1F2937;">
                            <h5 class="mb-0" style="color: white;">Laporan Produk Bulanan</h5>
                            <small class="text-muted">Laporan produk terlaris berdasarkan data penjualan bulanan</small>
                        </div>

                        <div class="card-body">
                            <form action="report/report-products.php" method="POST" enctype="multipart/form-data" target="_blank">
                                <div class="row">
                                    <!-- Kategori -->
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">Kategori</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-box"></i>
                                            </span>
                                            <select name="category" id="category" class="form-control" required>
                                                <option value="">Pilih Kategori</option>
                                                <option value="semua">Semua</option>
                                                <option value="makanan">Makanan</option>
                                                <option value="minuman">Minuman</option>
                                                <option value="jajanan">Jajanan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Tanggal Awal -->
                                    <div class="col-6 col-md-6 mb-4">
                                        <label class="form-label">Tanggal Awal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input 
                                                type="date"
                                                name="start_date"
                                                class="form-control"
                                                placeholder="Masukkan tanggal"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <!-- Tanggal Akhir -->
                                    <div class="col-6 col-md-6 mb-4">
                                        <label class="form-label">Tanggal Akhir</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input 
                                                type="date"
                                                name="end_date"
                                                class="form-control"
                                                placeholder="Masukkan tanggal"
                                                required
                                            >
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <!-- Tombol -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" name="save_product" class="btn btn-primary">
                                        <i class="fas fa-chart-bar"></i>&nbsp; Lihat Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../script/footscript.php'; ?>
    </body>
</html>
