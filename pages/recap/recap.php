<?php include '../../sessions/session.php'; ?>

<!doctype html>
<html>
    <head>
        <title>Rekap Penjualan - Qieos</title>
        <?php include '../../script/headscript.php'; ?>

        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/recap.css">
    </head>

    <body>
        <?php include '../components/sidebar.php'; ?>

        <main class="content">
            <?php include '../components/navbar.php'; ?>

            <div class="container-fluid px-0 mt-4">
                <!-- Header -->
                <!-- <div class="sales-header mt-5">
                    <div>
                        <h3>Stok Penjualan</h3>
                        <p class="mb-0">
                            Monitoring stok produk yang tersedia untuk penjualan
                        </p>
                    </div>

                    <div class="sales-icon">
                        <i class="fas fa-store"></i>
                    </div>
                </div> -->

                <!-- Hapus kondisi ketika sudah launching -->
                <?php if ($user['role'] == 'developer') { ?>
                <div class="section-card mb-4 mt-5">
                    <div class="panel-header panel-primary">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Rekap Penjualan
                                </div>

                                <div class="panel-subtitle">
                                    Rekap data penjualan produk yang telah dilakukan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="my-4 px-4">
                        <form id="form-request">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label-modern">
                                        Tanggal Awal
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>

                                        <input
                                            type="date"
                                            name="first_date"
                                            class="form-control "
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label-modern">
                                        Tanggal Akhir
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>

                                        <input
                                            type="date"
                                            name="last_date"
                                            class="form-control"
                                            required>
                                    </div>

                                    <small id="stock-info" class="text-muted"></small>
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-primary btn-request w-100">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Rekap
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php } ?>

                <div class="section-card mb-5 mt-2">
                    <div class="panel-header panel-primary">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Rekap Tenant
                                </div>

                                <div class="panel-subtitle">
                                    Rekap data pembayaran tenant & air listrik yang telah dilakukan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="my-4 px-4">
                        <form id="form-request-tenant">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label-modern">
                                        Tanggal Awal
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>

                                        <input
                                            type="date"
                                            name="first_date"
                                            class="form-control "
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-modern">
                                        Tanggal Akhir
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>

                                        <input
                                            type="date"
                                            name="last_date"
                                            class="form-control"
                                            required>
                                    </div>

                                    <small id="stock-info-tenant" class="text-muted"></small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-modern">
                                        Jenis Pembayaran
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-tags"></i>
                                        </span>

                                        <select name="type" id="type" class="form-control">
                                            <option value="">Pilih Pembayaran</option>
                                            <option value="tenant">Tenant</option>
                                            <option value="utility">Air & Listrik</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary btn-request w-100">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Rekap
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../../script/footscript.php'; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const formRequest = document.getElementById('form-request');

                formRequest.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const firstDate = formRequest.first_date.value;
                    const lastDate = formRequest.last_date.value;

                    if (firstDate && lastDate) {
                        const url = `recap-print.php?first_date=${firstDate}&last_date=${lastDate}`;
                        window.open(url, '_blank');
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const formRequestTenant = document.getElementById('form-request-tenant');

                formRequestTenant.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const firstDate = formRequestTenant.first_date.value;
                    const lastDate = formRequestTenant.last_date.value;
                    const type = formRequestTenant.type.value;

                    if (firstDate && lastDate && type) {
                        const url = `recap-print-tenant.php?first_date=${firstDate}&last_date=${lastDate}&type=${type}`;
                        window.open(url, '_blank');
                    }
                });
            });
        </script>
    </body>
</html>
