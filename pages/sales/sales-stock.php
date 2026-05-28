<?php include '../../sessions/session.php'; ?>

<!doctype html>
<html>
    <head>
        <title>Stok Penjualan | Qieos</title>
        <?php include '../../script/headscript.php'; ?>

        <style>
            :root {
                --primary: #4f46e5;
                --soft: #eef2ff;
                --border: #e2e8f0;
            }

            .card-modern {
                border: none;
                border-radius: 16px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            }

            .form-control {
                border-radius: 10px;
            }

            .badge-soft {
                background: var(--soft);
                color: var(--primary);
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 10px;
            }

            .dataTables_length select {
                min-width: 70px;
                padding-right: 20px;
            }
        </style>
    </head>

    <body>
        <?php include '../components/sidebar.php'; ?>

        <main class="content">
            <?php include '../components/navbar.php'; ?>

            <div class="container-fluid mt-4">
                <h5 class="mb-3">Stok Penjualan</h5>

                <!-- REQUEST -->
                <div class="card card-modern p-4 mb-4">
                    <form id="form-request">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Produk</label>
                                <select
                                    name="product_id"
                                    class="form-control"
                                    required
                                >
                                    <option value="">Pilih</option>

                                    <?php 
                                        $q = mysqli_query($conn,"SELECT id,name,code FROM products"); 

                                        function getStock($conn,$product_id){ 
                                            $q2 =
                                                mysqli_query($conn," SELECT
                                                COALESCE(SUM(remaining_qty),0) as stock FROM
                                                purchase_items WHERE product_id =
                                                $product_id "); $d =
                                        
                                            mysqli_fetch_assoc($q2); return $d['stock'];
                                        } while($p=mysqli_fetch_assoc($q)): ?>
                                    <option
                                        value="<?= $p['id'] ?>"
                                        data-stock="<?= getStock($conn,$p['id']) ?>"
                                    >
                                        <?= $p['name'] ?> (<?= $p['code'] ?>)
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Qty</label>
                                <input
                                    type="number"
                                    name="qty"
                                    class="form-control"
                                    placeholder="0"
                                    required
                                />
                                <small
                                    id="stock-info"
                                    class="text-muted"
                                ></small>
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100">
                                    Request Stok
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- STOK -->
                <div class="card card-modern p-3 mb-4">
                    <h6>Stok Penjualan</h6>
                    <div id="sales-table"></div>
                </div>

                <!-- HISTORY -->
                <div class="card card-modern p-3 mb-5">
                    <h6>Riwayat Request</h6>
                    <div id="history-table"></div>
                </div>
            </div>
        </main>

        <?php include '../../script/footscript.php'; ?>

        <script>
            const form = document.getElementById("form-request");
            const productSelect = form.querySelector(
                'select[name="product_id"]',
            );
            const qtyInput = form.querySelector('input[name="qty"]');
            const stockInfo = document.getElementById("stock-info");

            /* 🔥 SET MAX QTY */
            productSelect.addEventListener("change", function () {
                let stock = parseInt(
                    this.options[this.selectedIndex].dataset.stock || 0,
                );

                qtyInput.max = stock;
                qtyInput.value = "";
                qtyInput.placeholder = stock != 0 ? "Max: " + stock : "Stok kosong";
            });

            /* 🔥 VALIDASI */
            form.addEventListener("submit", async function (e) {
                e.preventDefault();

                let max = parseInt(qtyInput.max || 0);
                let val = parseInt(qtyInput.value || 0);

                if (val > max) {
                    Swal.fire(
                        "Error",
                        "Qty melebihi stok (" + max + ")",
                        "error",
                    );
                    return;
                }

                let btn = form.querySelector("button");
                btn.disabled = true;
                btn.innerText = "Loading...";

                try {
                    let res = await fetch("sales-action.php", {
                        method: "POST",
                        body: new FormData(form),
                    });

                    let data = await res.json();

                    Swal.fire(data.status, data.msg, data.status);

                    form.reset();
                    stockInfo.innerHTML = "";

                    loadTable();
                    loadHistory();
                } catch {
                    Swal.fire("error", "Server error", "error");
                }

                btn.disabled = false;
                btn.innerText = "Request Stok";
            });

            /* 🔥 LOAD TABLE */
            function loadTable() {
                fetch("sales-table.php")
                    .then((res) => res.text())
                    .then((html) => {
                        document.getElementById("sales-table").innerHTML = html;

                        if ($.fn.DataTable.isDataTable("#salesTable")) {
                            $("#salesTable").DataTable().destroy();
                        }

                        $("#salesTable").DataTable({
                            pageLength: 5,
                            lengthMenu: [5, 10, 25, 50],
                            responsive: true,
                            autoWidth: false,
                            order: [],
                        });
                    });
            }

            /* 🔥 LOAD REQUEST */
            function loadHistory(){
                fetch('../components/tables/history-request-table.php')
                .then(res=>res.text())
                .then(html=>{
                    document.getElementById("history-table").innerHTML = html;

                    setTimeout(() => {

                        // 🔥 DESTROY DULU
                        if ($.fn.DataTable.isDataTable('#requestHistory')) {
                            $('#requestHistory').DataTable().destroy();
                        }

                        // 🔥 INIT ULANG
                        $('#requestHistory').DataTable({
                            pageLength: 5,
                            lengthMenu: [5, 10, 25, 50],
                            responsive: true,
                            autoWidth: false,

                            // 🔥 PENTING: IKUTIN SORT SQL
                            order: [] 
                        });

                    }, 100);
                });
            }

            loadTable();
            loadHistory();
        </script>
    </body>
</html>
