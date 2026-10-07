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
                    <?php
                        $qTenant = mysqli_query($conn,"
                            SELECT *
                            FROM tenants
                            WHERE status = 'active'
                            ORDER BY tenant_name ASC
                        ");

                        while($tenant = mysqli_fetch_assoc($qTenant)){
                            echo '<option value="'.htmlspecialchars($tenant['id']).'">'.htmlspecialchars($tenant['tenant_name']).'</option>';
                        }
                    ?>

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

                <button class="btn btn-success me-2 w-100" onclick="printExcel('single')">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </button>

                <button class="btn btn-danger w-100" onclick="printPDF('single')">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>

            </div>

        </div>

    </div>

    <hr>

    <div class="table-responsive">

        <table class="table align-middle" id="reportTableSingle">

            <thead>

                <tr>

                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Tanggal Pembayaran</th>
                    <th class="text-center">Jumlah Pembayaran</th>
                    <th class="text-center">Status</th>

                </tr>

            </thead>

            <tbody id="reportSingleBody">

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

            <tfoot id="reportSingleFoot">
            </tfoot>

        </table>

    </div>

    <!-- SUMMARY BANNER CARD -->
    <div id="reportSingleSummaryCard" class="mt-4" style="display:none; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #fff; border: 1.5px solid rgba(99,102,241,0.4); border-radius: 16px; box-shadow: 0 8px 24px rgba(30, 27, 75, 0.15); padding: 18px 24px;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Pembayaran</div>
                    <div style="font-size:12px; color:#94a3b8;">Akumulasi pembayaran tenant yang dipilih</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalSingleAmountLabel">
                Rp 0
            </div>
        </div>
    </div>

</div>
