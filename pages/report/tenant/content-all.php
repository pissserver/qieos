<div id="allTenantContent" class="section-card">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-chart-line"></i>
            </div>

            <div>
                <div id="titleReport" class="panel-title">
                    Laporan Pembayaran Tenant
                </div>

                <div id="subtitleReport" class="panel-subtitle">
                    Menampilkan seluruh riwayat pembayaran seluruh tenant.
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="report-filter">

        <div class="row">

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Tanggal Awal
                </label>

                <input
                    type="date"
                    class="form-control"
                    id="first_date_all">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>

                <input
                    type="date"
                    class="form-control"
                    id="last_date_all">
            </div>

            <div class="col-md-4 d-flex align-items-end">

                <button class="btn btn-success me-2 w-100">
                    <i class="fas fa-file-excel me-2"></i>
                    Export Excel
                </button>

                <button class="btn btn-danger w-100" onclick="printPDF('all')">
                    <i class="fas fa-file-pdf me-2"></i>
                    Print PDF
                </button>

            </div>

        </div>

    </div>

    <hr>

    <div class="table-responsive">

        <table class="table align-middle" id="reportTableAll">

            <thead>

                <tr>

                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Nama Tenant</th>
                    <th class="text-center">Tanggal Pembayaran</th>
                    <th class="text-center">Jumlah Pembayaran</th>
                    <th class="text-center">Status</th>

                </tr>

            </thead>

            <tbody id="reportAllBody">

                <tr>

                    <td colspan="5">

                        <div class="loading-box">

                            <i class="fas fa-file-invoice-dollar"></i>

                            <h5>Belum Ada Data</h5>

                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan laporan.
                            </span>

                        </div>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>