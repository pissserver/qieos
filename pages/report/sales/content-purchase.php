<div id="purchaseContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Pembelian
                </div>

                <div class="panel-subtitle">
                    Detail pembelian per form belanja pada periode yang dipilih.
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Form Belanja
                </label>
                <select
                    class="form-control"
                    id="purchase_form_id"
                    style="width: 100%;">
                    <option value="">Pilih Form Belanja</option>
                </select>
            </div>

            <div class="col-md-8 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('purchase')">
                    <i class="fas fa-file-excel me-2"></i>
                    Export Excel
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('purchase')">
                    <i class="fas fa-file-pdf me-2"></i>
                    Print PDF
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTablePurchase">
            <thead>
                <tr>
                    <th class="text-center" width="40">No</th>
                    <th class="text-center">Tgl Beli</th>
                    <th class="text-center">No Penerimaan Barang</th>
                    <th class="text-center">No Referensi</th>
                    <th class="text-center">Nama Supplier</th>
                    <th class="text-center">Kode Barang</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Barang Masuk/Qty</th>
                    <th class="text-center">Total Pembayaran</th>
                    <th class="text-center">Harga/Satuan</th>
                </tr>
            </thead>

            <tbody id="reportPurchaseBody">
                <tr>
                    <td colspan="11">
                        <div class="loading-box">
                            <i class="fas fa-file-invoice"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Pilih form belanja untuk menampilkan detail pembelian.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportPurchaseSummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Pembayaran</div>
                    <div style="font-size:12px; color:#94a3b8;">Akumulasi total pembayaran pada form ini</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalPurchaseAmountLabel">
                Rp 0
            </div>
        </div>
    </div>

</div>
