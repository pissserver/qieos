<?php include '../../sessions/session.php'; ?>

<!doctype html>
<html>
    <head>
        <title>Rekap Penjualan - Qieos</title>
        <?php include '../../script/headscript.php'; ?>

        <style>
            /* HERO */
            .sales-header{
                background:linear-gradient(135deg,#334155,#0f172a);
                color:#fff;
                border-radius:24px;
                padding:30px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                margin-bottom:25px;
                box-shadow:0 12px 30px rgba(15,23,42,.15);
            }

            .sales-header h3{
                font-weight:800;
                margin:5px 0;
                color:#fff;
            }

            .sales-header small{
                text-transform:uppercase;
                letter-spacing:1px;
                opacity:.8;
            }

            .sales-icon{
                width:90px;
                height:90px;
                border-radius:22px;
                background:rgba(255,255,255,.08);
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:35px;
            }

            /* SECTION */

            .section-card{
                background:#fff;
                border-radius:24px;
                padding:24px;
                box-shadow:0 8px 24px rgba(15,23,42,.05);
            }

            /* PANEL */
            
            .panel-header{
                display:flex;
                justify-content:space-between;
                align-items:center;
                padding:18px 22px;
                border-radius:18px;
            }

            .panel-left{
                display:flex;
                align-items:center;
                gap:16px;
            }

            .panel-icon{
                width:58px;
                height:58px;
                border-radius:16px;
                background:rgba(255,255,255,.12);
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:22px;
            }

            .panel-title{
                font-size:17px;
                font-weight:700;
            }

            .panel-subtitle{
                font-size:13px;
                opacity:.85;
            }

            .panel-primary{
                background:linear-gradient(
                    135deg,
                    #4f46e5,
                    #4338ca
                );
                color:#fff;
            }

            .panel-success{
                background:linear-gradient(
                    135deg,
                    #16a34a,
                    #15803d
                );
                color:#fff;
            }

            .panel-dark{
                background:linear-gradient(
                    135deg,
                    #334155,
                    #0f172a
                );
                color:#fff;
            }

            /* FORM */

            .form-label-modern{
                font-weight:600;
                margin-bottom:8px;
                color:#334155;
            }

            .input-group-text{
                border-radius:12px 0 0 12px;
            }

            .form-product,
            .form-qty{
                border-radius:12px;
                min-height:45px;
            }

            .btn-request{
                height:46px;
                border-radius:12px;
                font-weight:600;
            }
        </style>
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

                <!-- REQUEST -->
                <div class="section-card mb-4 mt-5">
                    <div class="panel-header panel-success">
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
                                <div class="col-md-6">
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

                                    <small id="stock-info" class="text-muted"></small>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary btn-request w-100">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Rekap Penjualan
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
        </script>
    </body>
</html>
