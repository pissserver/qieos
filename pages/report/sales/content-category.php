<div id="categoryContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-tags"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Penjualan Per Kategori
                </div>

                <div class="panel-subtitle">
                    Menampilkan omzet produk berdasarkan kategori.
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Kategori
                </label>
                <select class="form-control" id="category_id">
                    <option value="">
                        -- Pilih Kategori --
                    </option>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                    <option value="jajanan">Jajanan</option>
                    <option value="pelengkap">Pelengkap</option>
                    <option value="additional">Additional</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal Awal
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="first_date_category">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_category">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('category')">
                    <i class="fas fa-file-excel"></i>
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('category')">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableCategory">
            <thead>
                <tr>
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Produk</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Qty Terjual</th>
                    <th class="text-center">Omzet</th>
                </tr>
            </thead>

            <tbody id="reportCategoryBody">
                <tr>
                    <td colspan="5">
                        <div class="loading-box">
                            <i class="fas fa-tags"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Pilih kategori terlebih dahulu untuk melihat laporan penjualan.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportCategorySummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Kategori</div>
                    <div style="font-size:12px; color:#94a3b8;">Akumulasi jumlah unit terjual dan nilai omzet</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); color: #c7d2fe; font-size: 14px; font-weight: 700; padding: 6px 16px; border-radius: 12px;" id="totalCategoryQtyLabel">
                    0 Unit
                </div>
                <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalCategoryAmountLabel">
                    Rp 0
                </div>
            </div>
        </div>
    </div>

</div>
