<div id="tenantContent" class="section-card" style="display:none;">

    <div class="panel-header panel-primary mb-4">

        <div class="panel-left">

            <div class="panel-icon">
                <i class="fas fa-store"></i>
            </div>

            <div>

                <div id="titleReportSingle" class="panel-title">
                    Laporan Pembayaran Per Tenant
                </div>

                <div id="subtitleReportSingle" class="panel-subtitle">
                    Menampilkan riwayat pembayaran berdasarkan tenant yang dipilih.
                </div>

            </div>

        </div>

    </div>

    <!-- FILTER -->
    <div class="report-filter">

        <div class="row">

            <div class="col-md-4">

                <label class="form-label fw-bold">
                    Tenant
                </label>

                <select class="form-control" id="tenant_id">

                    <option value="">
                        -- Pilih Tenant --
                    </option>

                </select>

            </div>

            <div class="col-md-3">

                <label class="form-label fw-bold">
                    Tanggal Awal
                </label>

                <input
                    type="date"
                    class="form-control"
                    id="first_date_single">

            </div>

            <div class="col-md-3">

                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>

                <input
                    type="date"
                    class="form-control"
                    id="last_date_single">

            </div>

            <div class="col-md-2 d-flex align-items-end">

                <button class="btn btn-success me-2 w-100">
                    <i class="fas fa-file-excel"></i>
                </button>

                <button class="btn btn-danger w-100">
                    <i class="fas fa-file-pdf"></i>
                </button>

            </div>

        </div>

    </div>

    <hr>

    <div class="table-responsive">

        <table class="table align-middle" id="reportTableSingle">

            <thead>

                <tr>

                    <th width="60">No</th>
                    <th>Tanggal Pembayaran</th>
                    <th>Jumlah Pembayaran</th>
                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td colspan="4">

                        <div class="loading-box">

                            <i class="fas fa-store"></i>

                            <h5>Belum Ada Data</h5>

                            <span>
                                Pilih tenant terlebih dahulu untuk melihat laporan pembayaran.
                            </span>

                        </div>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>