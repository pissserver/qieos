<div id="tenantContent" class="section-card" style="display:none;">

    <div class="panel-header panel-primary mb-4">

        <div class="panel-left">

            <div class="panel-icon">
                <i class="fas fa-store"></i>
            </div>

            <div>

                <div id="titleReportSingle" class="panel-title">
                    Laporan Pembayaran Per Tenant
                </div>

                <div id="subtitleReportSingle" class="panel-subtitle">
                    Menampilkan riwayat pembayaran berdasarkan tenant yang dipilih.
                </div>

            </div>

        </div>

    </div>

    <!-- FILTER -->
    <div class="report-filter">

        <div class="row">

            <div class="col-md-4">

                <label class="form-label fw-bold">
                    Tenant
                </label>

                <select class="form-control" id="tenant_id">

                    <option value="">
                        -- Pilih Tenant --
                    </option>
                    <?php
                        $qTenant = mysqli_query($conn,"
                            SELECT *
                            FROM tenants
                            WHERE id != '$d[tenant_id]'
                            ORDER BY tenant_name ASC
                        ");

                        while($tenant = mysqli_fetch_assoc($qTenant)){
                            echo '<option value="'.$tenant['id'].'">'.$tenant['tenant_name'].'</option>';
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
                    id="first_date_single">

            </div>

            <div class="col-md-3">

                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>

                <input
                    type="date"
                    class="form-control"
                    id="last_date_single">

            </div>

            <div class="col-md-2 d-flex align-items-end">

                <button class="btn btn-success me-2 w-100">
                    <i class="fas fa-file-excel"></i>
                </button>

                <button class="btn btn-danger w-100" onclick="printPDF('single')">
                    <i class="fas fa-file-pdf"></i>
                </button>

            </div>

        </div>

    </div>

    <hr>

    <div class="table-responsive">

        <table class="table align-middle" id="reportTableSingle">

            <thead>

                <tr>

                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Tanggal Pembayaran</th>
                    <th class="text-center">Jumlah Pembayaran</th>
                    <th class="text-center">Status</th>

                </tr>

            </thead>

            <tbody id="reportSingleBody">

                <tr>

                    <td colspan="4">

                        <div class="loading-box">

                            <i class="fas fa-store"></i>

                            <h5>Belum Ada Data</h5>

                            <span>
                                Pilih tenant terlebih dahulu untuk melihat laporan pembayaran.
                            </span>

                        </div>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>