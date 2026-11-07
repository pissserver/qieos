<div id="stockContent" class="section-card report-panel">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Stok
                </div>

                <div class="panel-subtitle">
                    Mutasi stok gudang dan kantin per produk pada periode terpilih.
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Lokasi Stok
                </label>
                <select class="form-control" id="stock_location">
                    <option value="gudang">Stok Gudang</option>
                    <option value="kantin">Stok Kantin</option>
                    <option value="gabungan">Gabungan (Gudang + Kantin)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal Awal
                </label>
                <input type="date" class="form-control" id="first_date_stock">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input type="date" class="form-control" id="last_date_stock">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('stock')">
                    <i class="fas fa-file-excel me-2"></i>
                    Excel
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('stock')">
                    <i class="fas fa-file-pdf me-2"></i>
                    PDF
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableStock">
            <thead>
                <tr id="stockHeadRow">
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Nama Produk</th>
                    <th class="text-center">Qty Awal</th>
                    <th class="text-center">Qty Masuk</th>
                    <th class="text-center">Qty Keluar</th>
                    <th class="text-center">Adjustment +</th>
                    <th class="text-center">Adjustment −</th>
                    <th class="text-center">Saldo Akhir</th>
                </tr>
            </thead>

            <tbody id="reportStockBody">
                <tr>
                    <td colspan="8">
                        <div class="loading-box">
                            <i class="fas fa-boxes-stacked"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan laporan stok.
                            </span>
                        </div>
                        </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
