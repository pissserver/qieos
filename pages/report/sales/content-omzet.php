<div id="omzetContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-coins"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Omzet Harian
                </div>

                <div class="panel-subtitle">
                    Rekap omzet penjualan per hari dari pesanan yang sudah dibayar.
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
                    id="first_date_omzet">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_omzet">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('omzet')">
                    <i class="fas fa-file-excel me-2"></i>
                    Export Excel
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('omzet')">
                    <i class="fas fa-file-pdf me-2"></i>
                    Print PDF
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableOmzet">
            <thead>
                <tr>
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Jumlah Pesanan</th>
                    <th class="text-center">Terbayar</th>
                    <th class="text-center">Waiting</th>
                    <th class="text-center">Omzet</th>
                </tr>
            </thead>

            <tbody id="reportOmzetBody">
                <tr>
                    <td colspan="6">
                        <div class="loading-box">
                            <i class="fas fa-coins"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan omzet harian.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportOmzetSummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Omzet</div>
                    <div style="font-size:12px; color:#94a3b8;">Akumulasi omzet penjualan terbayar</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalOmzetAmountLabel">
                Rp 0
            </div>
        </div>
    </div>

</div>
