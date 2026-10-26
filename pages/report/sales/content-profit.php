<div id="profitContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-chart-pie"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Laba & Rugi
                </div>

                <div class="panel-subtitle">
                    Perbandingan omzet penjualan dengan pengeluaran belanja per hari.
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Tanggal Awal
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="first_date_profit">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_profit">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('profit')">
                    <i class="fas fa-file-excel me-2"></i>
                    Export Excel
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('profit')">
                    <i class="fas fa-file-pdf me-2"></i>
                    Print PDF
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableProfit">
            <thead>
                <tr>
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Omzet</th>
                    <th class="text-center">Pengeluaran</th>
                    <th class="text-center">Laba / Rugi</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>

            <tbody id="reportProfitBody">
                <tr>
                    <td colspan="6">
                        <div class="loading-box">
                            <i class="fas fa-chart-pie"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan laba dan rugi.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportProfitSummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Laba / Rugi</div>
                    <div style="font-size:12px; color:#94a3b8;">Omzet dikurangi pengeluaran pada periode ini</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalProfitAmountLabel">
                Rp 0
            </div>
        </div>
    </div>

</div>
