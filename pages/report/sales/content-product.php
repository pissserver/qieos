<div id="productContent" class="section-card report-panel" style="display:none;">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-box"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Penjualan Per Produk
                </div>

                <div class="panel-subtitle">
                    Menampilkan riwayat penjualan berdasarkan produk yang dipilih.
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Produk
                </label>
                <select class="form-control" id="product_id">
                    <option value="">
                        -- Pilih Produk --
                    </option>
                    <?php
                        $qProduct = mysqli_query($conn, "
                            SELECT id, name, code
                            FROM products
                            ORDER BY name ASC
                        ");

                        if ($qProduct) {
                            while ($product = mysqli_fetch_assoc($qProduct)) {
                                $label = $product['name'];
                                if (!empty($product['code'])) {
                                    $label .= ' (' . $product['code'] . ')';
                                }
                                echo '<option value="'.$product['id'].'">'.htmlspecialchars($label).'</option>';
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
                    id="first_date_product">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_product">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('product')">
                    <i class="fas fa-file-excel"></i>
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('product')">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableProduct">
            <thead>
                <tr>
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Kode Pesanan</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Subtotal</th>
                </tr>
            </thead>

            <tbody id="reportProductBody">
                <tr>
                    <td colspan="6">
                        <div class="loading-box">
                            <i class="fas fa-box"></i>
                            <h5>Belum Ada Data</h5>
                            <span>
                                Pilih produk terlebih dahulu untuk melihat laporan penjualan.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportProductSummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#c7d2fe; font-weight:700;">Total Penjualan Produk</div>
                    <div style="font-size:12px; color:#94a3b8;">Akumulasi jumlah unit terjual dan nilai omzet</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); color: #c7d2fe; font-size: 14px; font-weight: 700; padding: 6px 16px; border-radius: 12px;" id="totalProductQtyLabel">
                    0 Unit
                </div>
                <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fbbf24; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalProductAmountLabel">
                    Rp 0
                </div>
            </div>
        </div>
    </div>

</div>
