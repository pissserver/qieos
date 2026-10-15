<div id="bestContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-trophy"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Produk Terlaris
                </div>

                <div class="panel-subtitle">
                    Peringkat produk dengan penjualan terbanyak pada periode yang dipilih.
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-2">
                <label class="form-label fw-bold">
                    Tanggal Awal
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="first_date_best">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_best">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">
                    Tampilkan
                </label>
                <select class="form-control" id="best_limit">
                    <option value="10">Top 10</option>
                    <option value="20">Top 20</option>
                    <option value="50">Top 50</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <input type="checkbox" id="best_detail" class="detail-toggle-input" onchange="toggleReportDetail('best')">
                <label class="detail-toggle" for="best_detail" title="Tampilkan rincian qty & omzet per tanggal di file Excel (khusus Excel).">
                    <span class="detail-toggle-icon"><i class="fas fa-list-ul"></i></span>
                    <span class="detail-toggle-text">Detail</span>
                    <span class="detail-toggle-switch"></span>
                </label>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button id="bestExcelBtn" class="btn btn-success me-2 w-100" type="button" onclick="printExcel('best')">
                    <i class="fas fa-file-excel"></i>
                    <span class="excel-label">Export Excel</span>
                </button>

                <button id="bestPdfBtn" class="btn btn-danger w-100" type="button" onclick="printPDF('best')">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableBest">
            <thead>
                <tr>
                    <th class="text-center" width="80">Peringkat</th>
                    <th class="text-center">Produk</th>
                    <th class="text-center">Kategori</th>
                    <th class="text-center">Qty Terjual</th>
                    <th class="text-center">Omzet</th>
                </tr>
            </thead>

            <tbody id="reportBestBody">
                <tr>
                    <td colspan="5">
                        <div class="loading-box">
                            <i class="fas fa-trophy"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan produk terlaris.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportBestSummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-fire"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Produk Terlaris</div>
                    <div style="font-size:12px; color:#94a3b8;">Akumulasi jumlah unit terjual dan nilai omzet</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); color: #c7d2fe; font-size: 14px; font-weight: 700; padding: 6px 16px; border-radius: 12px;" id="totalBestQtyLabel">
                    0 Unit
                </div>
                <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalBestAmountLabel">
                    Rp 0
                </div>
            </div>
        </div>
    </div>

</div>
