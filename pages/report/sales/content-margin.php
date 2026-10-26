<div id="marginContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-percent"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Margin Produk
                </div>

                <div class="panel-subtitle">
                    Selisih harga jual dan harga beli FIFO untuk mengukur keuntungan per produk.
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
                    id="first_date_margin">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_margin">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('margin')">
                    <i class="fas fa-file-excel me-2"></i>
                    Export Excel
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('margin')">
                    <i class="fas fa-file-pdf me-2"></i>
                    Print PDF
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableMargin">
            <thead>
                <tr>
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Produk</th>
                    <th class="text-center">Qty Terjual</th>
                    <th class="text-center">Harga Beli</th>
                    <th class="text-center">Harga Jual</th>
                    <th class="text-center">Margin</th>
                    <th class="text-center">Keuntungan</th>
                </tr>
            </thead>

            <tbody id="reportMarginBody">
                <tr>
                    <td colspan="7">
                        <div class="loading-box">
                            <i class="fas fa-percent"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan margin produk.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportMarginSummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Keuntungan</div>
                    <div style="font-size:12px; color:#94a3b8;">Akumulasi margin seluruh produk terjual</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalMarginAmountLabel">
                Rp 0
            </div>
        </div>
    </div>

</div>
