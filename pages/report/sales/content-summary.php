<div id="summaryContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-wallet"></i>
            </div>

            <div>
                <div class="panel-title">
                    Ringkasan Keuangan
                </div>

                <div class="panel-subtitle">
                    Omzet, pengeluaran, dan laba bersih pada periode yang dipilih.
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal Awal
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="first_date_summary">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_summary">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <input type="checkbox" id="summary_detail" class="detail-toggle-input" onchange="toggleReportDetail('summary')">
                <label class="detail-toggle" for="summary_detail" title="Tampilkan rincian omzet, pengeluaran & laba per tanggal di file Excel (khusus Excel).">
                    <span class="detail-toggle-icon"><i class="fas fa-list-ul"></i></span>
                    <span class="detail-toggle-text">Detail</span>
                    <span class="detail-toggle-switch"></span>
                </label>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button id="summaryExcelBtn" class="btn btn-success me-2 w-100" type="button" onclick="printExcel('summary')">
                    <i class="fas fa-file-excel"></i>
                    <span class="excel-label">Export Excel</span>
                </button>

                <button id="summaryPdfBtn" class="btn btn-danger w-100" type="button" onclick="printPDF('summary')">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="money-grid row g-3 mb-4">
        <div class="col-md-4">
            <div class="money-stat money-stat-in">
                <div class="money-stat-icon"><i class="fas fa-arrow-trend-up"></i></div>
                <div>
                    <small>Omzet Penjualan</small>
                    <h5 id="summaryOmzetLabel">Rp 0</h5>
                    <span>Dari pesanan yang terbayar</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="money-stat money-stat-out">
                <div class="money-stat-icon"><i class="fas fa-arrow-trend-down"></i></div>
                <div>
                    <small>Pengeluaran</small>
                    <h5 id="summaryExpenseLabel">Rp 0</h5>
                    <span>Dari daftar belanja stok</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="money-stat money-stat-profit">
                <div class="money-stat-icon"><i class="fas fa-scale-balanced"></i></div>
                <div>
                    <small>Laba Bersih</small>
                    <h5 id="summaryProfitLabel">Rp 0</h5>
                    <span>Omzet dikurangi pengeluaran</span>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableSummary">
            <thead>
                <tr>
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Komponen</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Jumlah</th>
                </tr>
            </thead>

            <tbody id="reportSummaryBody">
                <tr>
                    <td colspan="4">
                        <div class="loading-box">
                            <i class="fas fa-wallet"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan ringkasan keuangan.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportSummarySummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Laba Bersih</div>
                    <div style="font-size:12px; color:#94a3b8;">Omzet penjualan dikurangi pengeluaran belanja</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalSummaryAmountLabel">
                Rp 0
            </div>
        </div>
    </div>

</div>
