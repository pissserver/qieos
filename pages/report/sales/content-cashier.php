<div id="cashierContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-cash-register"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Penjualan Per Kasir
                </div>

                <div class="panel-subtitle">
                    Menampilkan transaksi berdasarkan kasir yang bertugas.
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Kasir
                </label>
                <select class="form-control" id="cashier_id">
                    <option value="">
                        -- Pilih Kasir --
                    </option>
                    <?php
                        $qCashier = mysqli_query($conn, "
                            SELECT id, fullname, username, role
                            FROM users
                            WHERE role IN ('staff kasir', 'administrator', 'developer')
                            ORDER BY fullname ASC, username ASC
                        ");

                        if ($qCashier) {
                            while ($cashier = mysqli_fetch_assoc($qCashier)) {
                                $name = !empty($cashier['fullname']) ? $cashier['fullname'] : $cashier['username'];
                                echo '<option value="'.$cashier['id'].'">'.htmlspecialchars($name).'</option>';
                            }
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
                    id="first_date_cashier">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_cashier">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('cashier')">
                    <i class="fas fa-file-excel"></i>
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('cashier')">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableCashier">
            <thead>
                <tr>
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Kode Pesanan</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>

            <tbody id="reportCashierBody">
                <tr>
                    <td colspan="5">
                        <div class="loading-box">
                            <i class="fas fa-cash-register"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Pilih kasir terlebih dahulu untuk melihat laporan penjualan.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportCashierSummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Omzet Kasir</div>
                    <div style="font-size:12px; color:#94a3b8;">Hanya dari transaksi kasir yang sudah terbayar</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalCashierAmountLabel">
                Rp 0
            </div>
        </div>
    </div>

</div>
