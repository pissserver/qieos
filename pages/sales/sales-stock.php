<?php include '../../sessions/session.php'; ?>

<!doctype html>
<html>
    <head>
        <title>Stok Kantin - Qieos</title>
        <?php include '../../script/headscript.php'; ?>

        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/sales-stock.css">
    </head>

    <body>
        <?php include '../components/sidebar.php'; ?>

        <main class="content">
            <?php include '../components/navbar.php'; ?>

            <div class="container-fluid px-0 mt-4">
                <!-- Header -->
                <!-- <div class="sales-header mt-5">
                    <div>
                        <h3>Stok Kantin</h3>
                        <p class="mb-0">
                            Monitoring stok produk yang tersedia untuk kantin
                        </p>
                    </div>

                    <div class="sales-icon">
                        <i class="fas fa-store"></i>
                    </div>
                </div> -->

                <!-- REQUEST -->
                <div class="section-card mb-4 mt-5">
                    <div class="panel-header panel-success">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-paper-plane"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Request Stok Gudang
                                </div>

                                <div class="panel-subtitle">
                                    Ajukan permintaan stok kantin ke gudang
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="my-4 px-4">
                        <form id="form-request">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-modern">
                                        Produk
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-box-open"></i>
                                        </span>

                                        <select
                                            name="product_id"
                                            class="form-select form-product"
                                            required>

                                            <option value="">
                                                Pilih Produk
                                            </option>

                                            <?php
                                            $q = mysqli_query($conn,"
                                                SELECT id,name,code
                                                FROM products
                                                WHERE category != 'additional'
                                                ORDER BY name ASC
                                            ");

                                            function getStock($conn,$product_id){
                                                $q2=mysqli_query($conn,"
                                                    SELECT
                                                    COALESCE(SUM(remaining_qty),0) stock
                                                    FROM purchase_items
                                                    WHERE product_id=$product_id
                                                    AND deleted_at IS NULL
                                                ");

                                                return mysqli_fetch_assoc($q2)['stock'];
                                            }

                                            while($p=mysqli_fetch_assoc($q)):
                                            ?>

                                            <option
                                                value="<?= $p['id'] ?>"
                                                data-stock="<?= getStock($conn,$p['id']) ?>">
                                                <?= $p['name'] ?>
                                                (<?= $p['code'] ?>)
                                            </option>

                                            <?php endwhile; ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-modern">
                                        Qty
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-cubes"></i>
                                        </span>

                                        <input
                                            type="number"
                                            name="qty"
                                            class="form-control form-qty"
                                            placeholder="0"
                                            min="1"
                                            required>
                                    </div>

                                    <small id="stock-info" class="text-muted"></small>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary btn-request w-100">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Request
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- STOK Kantin -->
                <div class="section-card mb-4">
                    <div class="panel-header panel-primary">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-boxes-stacked"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Stok Kantin
                                </div>
                                <div class="panel-subtitle">
                                    List stok produk yang siap dijual
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 px-4 table-responsive">
                        <div id="sales-table"></div>
                    </div>
                </div>

                <!-- HISTORY -->
                <div class="section-card mb-5">
                    <div class="panel-header panel-dark">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-history"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Riwayat Request Stok
                                </div>
                                <div class="panel-subtitle">
                                    Histori seluruh permintaan stok gudang
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 px-4 table-responsive">
                        <div id="history-table"></div>
                    </div>
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

                qtyInput.dataset.maxStock = stock;
                qtyInput.value = "";
                qtyInput.placeholder = stock != 0 ? "Max: " + stock : "Stok kosong";
            });

            /* 🔥 VALIDASI */
            form.addEventListener("submit", async function (e) {
                e.preventDefault();

                let max = parseInt(qtyInput.dataset.maxStock || 0);
                let val = parseInt(qtyInput.value || 0);

                if (val > max) {
                    QToast(
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

                    QToast(data.status, data.msg, data.status);

                    form.reset();
                    stockInfo.innerHTML = "";

                    loadTable();
                    loadHistory();
                } catch {
                    QToast("error", "Server error", "error");
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
                            lengthMenu:[[5,10,25,50],[5,10,25,50]],
                            responsive: true,
                            autoWidth: false,
                            language:{
                                search:"",
                                searchPlaceholder:"Cari produk...",

                                zeroRecords: `
                                    <div class="empty-search">
                                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                        <div class="empty-title">Produk tidak ditemukan</div>
                                        <div class="empty-sub">
                                            Coba gunakan kata kunci lain
                                        </div>
                                    </div>
                                `,

                                emptyTable: `
                                    <div class="empty-search">
                                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                        <div class="empty-title">Belum ada data produk</div>
                                        <div class="empty-sub">
                                            Silakan tambahkan stok terlebih dahulu
                                        </div>
                                    </div>
                                `
                            },
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
                            lengthMenu:[[5,10,25,50],[5,10,25,50]],
                            responsive: true,
                            autoWidth: false,
                            language:{
                                search:"",
                                searchPlaceholder:"Cari request...",

                                zeroRecords: `
                                    <div class="empty-search">
                                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                        <div class="empty-title">Request tidak ditemukan</div>
                                        <div class="empty-sub">
                                            Coba gunakan kata kunci lain
                                        </div>
                                    </div>
                                `,

                                emptyTable: `
                                    <div class="empty-search">
                                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                        <div class="empty-title">Belum ada data request</div>
                                        <div class="empty-sub">
                                            Silakan tambahkan stok terlebih dahulu
                                        </div>
                                    </div>
                                `
                            },

                            // 🔥 PENTING: IKUTIN SORT SQL
                            order: [] 
                        });

                    }, 100);
                });
            }

            loadTable();
            loadHistory();

            function toggleCatalog(id, el){

                let status = el.checked ? 'active' : 'nonactive';
                let row = document.getElementById('row-' + id);

                // UI langsung berubah (smooth UX)
                if(row){
                    row.classList.toggle('catalog-active', el.checked);
                }

                fetch('sales-toggle-catalog.php', {
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body:JSON.stringify({
                        id:id,
                        status:status
                    })
                })
                .then(res=>res.json())
                .then(res=>{
                    console.log(res);

                    if(!res.success){
                        alert('Gagal update catalog');
                        el.checked = !el.checked;

                        if(row){
                            row.classList.toggle('catalog-active', el.checked);
                        }
                    }

                })
                .catch(()=>{
                    alert('Server error');
                    el.checked = !el.checked;

                    if(row){
                        row.classList.toggle('catalog-active', el.checked);
                    }
                });

            }
        </script>
    </body>
</html>
