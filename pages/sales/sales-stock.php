<?php include '../../sessions/session.php'; ?>

<?php
/* 🔥 Next REQ code generator */
$last    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT MAX(id) AS max_id FROM stock_requests"));
$next_id = $last && $last['max_id'] ? ((int)$last['max_id'] + 1) : 1;
$next_code = 'REQ-' . str_pad($next_id, 7, '0', STR_PAD_LEFT);

/* 🔥 Produk + stok gudang untuk isi dropdown */
$pq = mysqli_query($conn,"
    SELECT
        p.id,
        p.name,
        p.code,
        COALESCE(SUM(pi.remaining_qty),0) AS stock
    FROM products p
    LEFT JOIN purchase_items pi
        ON pi.product_id = p.id
        AND pi.deleted_at IS NULL
    WHERE p.category != 'additional'
    GROUP BY p.id, p.name, p.code
    ORDER BY p.name ASC
");

$products = [];
while($prod = mysqli_fetch_assoc($pq)){
    $products[] = [
        'id'    => (int)$prod['id'],
        'name'  => $prod['name'],
        'code'  => $prod['code'],
        'stock' => (int)$prod['stock']
    ];
}
?>

<!doctype html>
<html>
    <head>
        <title>Stok Kantin - Qieos</title>
        <?php include '../../script/headscript.php'; ?>

        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/sales-stock.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/sales-stock.css'); ?>">
    </head>

    <body>
        <?php include '../components/sidebar.php'; ?>

        <main class="content">
            <?php include '../components/navbar.php'; ?>

            <div class="container-fluid px-0 mt-4 mb-5">
                <!-- REQUEST / HISTORY -->
                <div class="section-card mb-4 mt-5">
                    <div class="panel-header panel-primary">
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

                        <div class="panel-toggle-wrap">
                            <span class="toggle-label active" id="labelForm">Request</span>

                            <label class="switch-toggle">
                                <input type="checkbox" id="panelToggle">
                                <span class="slider-toggle"></span>
                            </label>

                            <span class="toggle-label" id="labelEdit">Riwayat</span>
                        </div>
                    </div>

                    <div class="my-4 px-4">
                        <div id="formMode" class="panel-mode active">
                            <form id="form-request" autocomplete="off">
                                <!-- 🎫 Nomor Request -->
                                <div class="req-hero">
                                    <div class="req-hero-left">
                                        <div class="req-hero-label">
                                            <i class="fas fa-file-invoice me-2"></i>
                                            No. Request
                                        </div>
                                        <div class="req-hero-number">
                                            <span id="nextReqCode"><?= $next_code ?></span>
                                            <span class="req-hero-status">
                                                <span class="status-dot"></span>
                                                Draft
                                            </span>
                                        </div>
                                    </div>

                                    <div class="req-hero-right">
                                        <div class="req-hero-date">
                                            <i class="far fa-calendar-alt"></i>
                                            <?= date('d F Y') ?>
                                        </div>
                                        <div class="req-hero-hint">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Qty request mengikuti stok gudang
                                        </div>
                                    </div>
                                </div>

                                <!-- Daftar item -->
                                <div id="itemsContainer" class="mt-4"></div>

                                <!-- Tombol tambah -->
                                <button type="button" id="addItemBtn" class="btn-add-item">
                                    <i class="fas fa-plus me-2"></i>
                                    Tambah Produk
                                </button>

                                <!-- Ringkasan + submit -->
                                <div class="req-summary">
                                    <div class="req-summary-stats">
                                        <span class="summary-pill">
                                            <i class="fas fa-boxes-stacked"></i>
                                            <b id="sumItems">0</b> item
                                        </span>
                                        <span class="summary-pill">
                                            <i class="fas fa-cubes"></i>
                                            <b id="sumQty">0</b> pcs
                                        </span>
                                    </div>

                                    <button type="submit" id="submitBtn" class="btn btn-request">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Kirim Request
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div id="editMode" class="panel-mode">
                            <div id="history-table"></div>
                        </div>
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

                    <div class="mt-4 px-4">
                        <div id="sales-table"></div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../../script/footscript.php'; ?>

        <script>
            const PRODUCTS = <?= json_encode($products) ?>;

            /* ---------- helpers ---------- */
            let nextCode = document.getElementById('nextReqCode').textContent.trim();
            var editingRequestId = null;

            function fmt(n){
                return String(n || 0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function getProduct(id){
                for(let p of PRODUCTS){
                    if(p.id === id) return p;
                }
                return null;
            }

            function bumpReqCode(){
                const num = parseInt(nextCode.replace('REQ-',''));
                nextCode = 'REQ-' + String(num + 1).padStart(7, '0');
                document.getElementById('nextReqCode').textContent = nextCode;
            }

            function setHeroStatus(mode){
                const pill = document.querySelector('.req-hero-status');
                if(mode === 'edit'){
                    pill.classList.add('status-pending');
                    pill.innerHTML = '<span class="status-dot"></span> Pending';
                } else {
                    pill.classList.remove('status-pending');
                    pill.innerHTML = '<span class="status-dot"></span> Draft';
                }
            }

            function setFormMode(mode){
                editingRequestId = (mode === 'edit') ? editingRequestId : null;
                const btn = document.getElementById('submitBtn');
                if(mode === 'edit'){
                    btn.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Perubahan';
                } else {
                    btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Kirim Request';
                    setHeroStatus('draft');
                    document.getElementById('nextReqCode').textContent = nextCode;
                }
            }

            /* ---------- render row ---------- */
            function rowTemplate(){
                let opts = '<option value="">Pilih Produk</option>';
                opts += PRODUCTS.map(p =>
                    `<option value="${p.id}" data-stock="${p.stock}">${p.name} (${p.code})</option>`
                ).join('');

                return `
                    <div class="req-item">
                        <div class="req-item-index"></div>

                        <div class="req-item-body">
                            <div class="req-f input-produk">
                                <label><i class="fas fa-box-open"></i> Produk</label>
                                <select class="item-select">
                                    ${opts}
                                </select>
                            </div>

                            <div class="req-f input-qty">
                                <label><i class="fas fa-cubes"></i> Qty</label>
                                <input type="number" class="item-qty" min="1" placeholder="0">
                                <small class="maks-hint"></small>
                            </div>

                            <div class="req-f input-stok">
                                <label><i class="fas fa-warehouse"></i> Stok Gudang</label>
                                <span class="stock-chip">
                                    <i class="fas fa-warehouse"></i>
                                    <span class="stock-amt">-</span> tersedia
                                </span>
                            </div>
                        </div>

                        <button type="button" class="item-remove" title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
            }

            function reindexItems(){
                document.querySelectorAll("#itemsContainer .req-item").forEach(function(row, i){
                    row.querySelector(".req-item-index").textContent =
                        String(i + 1).padStart(2, "0");
                });
            }

            function refreshRow(row){
                const sel = row.querySelector(".item-select");
                const qty = row.querySelector(".item-qty");
                const chip = row.querySelector(".stock-chip");
                const stockAmt = row.querySelector(".stock-amt");
                const hint = row.querySelector(".maks-hint");

                const pid = parseInt(sel.value || 0);
                const prod = getProduct(pid);

                row.classList.remove("no-stock", "over-stock");

                if(!prod){
                    chip.className = "stock-chip";
                    stockAmt.textContent = "-";
                    hint.textContent = "";
                    qty.max = 0;
                    updateSummary();
                    return;
                }

                const stock = prod.stock;
                const val = parseInt(qty.value || 0);

                stockAmt.textContent = fmt(stock);
                hint.textContent = stock > 0 ? "Maks: " + fmt(stock) : "Stok habis";

                chip.className = "stock-chip";
                if(stock === 0){
                    chip.classList.add("chip-empty");
                    row.classList.add("no-stock");
                }

                qty.max = stock;

                if(val > stock){
                    row.classList.add("over-stock");
                }

                updateSummary();
            }

            function rowEvents(row){
                const qty = row.querySelector(".item-qty");

                row.querySelector(".item-select").addEventListener("change", function(){
                    this.closest(".req-item").querySelector(".item-qty").value = "";
                    refreshRow(this.closest(".req-item"));
                });

                qty.addEventListener("input", function(){
                    refreshRow(this.closest(".req-item"));
                });

                row.querySelector(".item-remove").addEventListener("click", function(){
                    const item = this.closest(".req-item");
                    item.style.height = item.offsetHeight + "px";
                    item.classList.add("removing");

                    item.style.height = "0";
                    item.style.margin = "0";
                    item.style.padding = "0";

                    setTimeout(function(){
                        item.remove();
                        reindexItems();
                        updateSummary();
                    }, 300);
                });
            }

            function addItem(){
                const container = document.getElementById("itemsContainer");
                const tpl = document.createElement("div");
                tpl.innerHTML = rowTemplate().trim();
                const row = tpl.firstElementChild;
                row.style.animation = "fadeSlide .35s ease";

                container.appendChild(row);
                rowEvents(row);
                reindexItems();
                updateSummary();

                row.querySelector(".item-select").focus();

                return row;
            }

            /* ---------- summary ---------- */
            function updateSummary(){
                let items = 0, qty = 0, invalid = 0;

                document.querySelectorAll("#itemsContainer .req-item").forEach(function(row){
                    const pid = parseInt(row.querySelector(".item-select").value || 0);
                    const val = parseInt(row.querySelector(".item-qty").value || 0);

                    if(pid > 0){
                        items++;
                        qty += val || 0;

                        const prod = getProduct(pid);
                        if(prod && val > prod.stock){
                            invalid++;
                        }
                    }
                });

                document.getElementById("sumItems").textContent = items;
                document.getElementById("sumQty").textContent = fmt(qty);

                const btn = document.getElementById("submitBtn");
                btn.classList.toggle("has-error", invalid > 0);
            }

            /* ---------- submit ---------- */
            const form = document.getElementById("form-request");

            form.addEventListener("submit", async function(e){
                e.preventDefault();

                const rows = document.querySelectorAll("#itemsContainer .req-item");
                const items = [];

                for(const row of rows){
                    const pid = parseInt(row.querySelector(".item-select").value || 0);
                    const qty = parseInt(row.querySelector(".item-qty").value || 0);

                    if(pid <= 0) continue;
                    items.push({product_id: pid, qty});
                }

                if(items.length === 0){
                    QToast("Error", "Silakan pilih minimal 1 produk", "error");
                    return;
                }

                for(const it of items){
                    const prod = getProduct(it.product_id);
                    if(it.qty > prod.stock){
                        QToast("Error", prod.name + " melebihi stok gudang (" + fmt(prod.stock) + ")", "error");
                        return;
                    }
                }

                const btn = document.getElementById("submitBtn");
                const oldTxt = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

                try {
                    const payload = editingRequestId
                        ? { action: "update", request_id: editingRequestId, items: items }
                        : { items: items };

                    const res = await fetch("sales-action.php", {
                        method: "POST",
                        headers: {"Content-Type": "application/json"},
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();

                    QToast(data.status, data.msg, data.status);

                    if(data.status === "success"){
                        document.getElementById("itemsContainer").innerHTML = "";
                        addItem();

                        if(editingRequestId){
                            editingRequestId = null;
                            setFormMode("create");
                        } else {
                            bumpReqCode();
                        }

                        loadTable();
                        loadHistory();
                    }
                } catch {
                    QToast("error", "Server error", "error");
                }

                btn.disabled = false;
                btn.innerHTML = oldTxt;
            });

            /* ---------- edit / delete request ---------- */
            function editRequest(btn){
                const tr = btn.closest('tr');
                const id = parseInt(tr.dataset.id);
                const code = tr.dataset.code;

                let items = [];
                try {
                    items = JSON.parse(tr.dataset.items || '[]');
                } catch(e){ items = []; }

                editingRequestId = id;

                const container = document.getElementById('itemsContainer');
                container.innerHTML = '';

                if(items.length === 0){
                    addItem();
                } else {
                    items.forEach(function(it){
                        const row = addItem();
                        row.querySelector('.item-select').value = it.product_id;
                        row.querySelector('.item-qty').value = it.qty;
                        refreshRow(row);
                    });
                    reindexItems();
                }

                document.getElementById('nextReqCode').textContent = code;
                setHeroStatus('pending');
                setFormMode('edit');

                const toggle = document.getElementById('panelToggle');
                if(toggle.checked){
                    toggle.checked = false;
                    toggle.dispatchEvent(new Event('change'));
                }

                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function deleteRequest(btn){
                const tr = btn.closest('tr');
                const id   = parseInt(tr.dataset.id);
                const code = tr.dataset.code;

                QConfirm(
                    'Hapus Request?',
                    code + ' akan dihapus permanen.',
                    {
                        confirmText: 'Hapus',
                        icon: 'fa-trash',
                        confirmClass: 'q-confirm-btn-danger',
                        iconClass: 'q-confirm-icon-danger'
                    }
                ).then(function(ok){
                    if(!ok) return;

                    fetch('sales-action.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ action: 'delete', request_id: id })
                    })
                    .then(function(res){ return res.json(); })
                    .then(function(data){
                        QToast(data.status, data.msg, data.status);
                        if(data.status === 'success') loadHistory();
                    });
                });
            }

            /* ---------- init ---------- */
            document.getElementById("addItemBtn").addEventListener("click", addItem);
            addItem();

            /* 🔥 LOAD TABLE */
            function loadTable() {
                fetch("sales-table.php")
                    .then((res) => res.text())
                    .then((html) => {
                        document.getElementById("sales-table").innerHTML = html;

                        if ($.fn.DataTable.isDataTable("#stockTable")) {
                            $("#stockTable").DataTable().destroy();
                        }

                        $("#stockTable").DataTable({
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

                        if ($.fn.DataTable.isDataTable('#stockTable')) {
                            $('#stockTable').DataTable().columns.adjust();
                        }
                    });
            }

            /* 🔥 LOAD REQUEST */
            function loadHistory(){
                fetch('../components/tables/history-request-table.php?view=sales')
                .then(res=>res.text())
                .then(html=>{
                    document.getElementById("history-table").innerHTML = html;

                    setTimeout(() => {

                        // 🔥 DESTROY DULU
                        if ($.fn.DataTable.isDataTable('#requestHistory')) {
                            $('#requestHistory').DataTable().destroy();
                        }

                        // 🔥 INIT ULANG
                        let ht = $('#requestHistory').DataTable({
                            pageLength: 5,
                            lengthMenu:[[5,10,25,50],[5,10,25,50]],
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

                        ht.columns.adjust();

                        // 🔥 EXPAND / COLLAPSE DETAIL ITEM
                        attachHistoryExpand();

                    }, 100);
                });
            }

            function attachHistoryExpand(){
                const table = document.getElementById('requestHistory');

                if(!table) return;

                table.querySelectorAll('.btn-expand').forEach(function(btn){
                    if(btn.dataset.bound) return;
                    btn.dataset.bound = '1';

                    btn.addEventListener('click', function(){
                        const tr = this.closest('tr');
                        const row = $('#requestHistory').DataTable().row(tr);

                        if(tr.classList.contains('shown')){
                            row.child.hide();
                            tr.classList.remove('shown');
                            this.querySelector('i').className = 'fas fa-chevron-right';
                            return;
                        }

                        row.child(detailHtml(tr.dataset.items || '[]')).show();

                        tr.classList.add('shown');
                        this.querySelector('i').className = 'fas fa-chevron-down';
                    });
                });
            }

            function detailHtml(itemsJson){
                let items;
                try {
                    items = JSON.parse(itemsJson);
                } catch(e){
                    items = [];
                }

                if(!items.length){
                    return '<div class="history-detail">Tidak ada item</div>';
                }

                const rows = items.map(function(it){
                    const img = it.photo
                        ? `<img class="product-img" src="${escapeHtml(it.photo)}" alt="">`
                        : `<div class="product-icon"><i class="fas fa-box-open"></i></div>`;

                    return `
                        <div class="detail-item">
                            <div class="detail-prod">
                                ${img}
                                <div>
                                    <div class="detail-name">${escapeHtml(it.name)}</div>
                                    <div class="detail-code">${escapeHtml(it.code)}</div>
                                </div>
                            </div>
                            <div class="detail-qty">
                                <i class="fas fa-cubes me-1"></i>
                                ${fmt(it.qty)} pcs
                            </div>
                        </div>
                    `;
                }).join('');

                return `
                    <div class="history-detail">
                        <div class="detail-head">Detail Item</div>
                        ${rows}
                    </div>
                `;
            }

            function escapeHtml(s){
                return String(s == null ? '' : s)
                    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            loadTable();

            /* 🔥 TOGGLE REQUEST / RIWAYAT */
            const panelToggle = document.getElementById('panelToggle');
            let historyLoaded = false;

            panelToggle.addEventListener('change', function () {
                const formMode = document.getElementById('formMode');
                const editMode = document.getElementById('editMode');
                const labelForm = document.getElementById('labelForm');
                const labelEdit = document.getElementById('labelEdit');

                if (this.checked) {
                    formMode.classList.remove('active');
                    editMode.classList.add('active');
                    labelForm.classList.remove('active');
                    labelEdit.classList.add('active');

                    if (!historyLoaded) {
                        historyLoaded = true;
                        loadHistory();
                    }
                } else {
                    editMode.classList.remove('active');
                    formMode.classList.add('active');
                    labelEdit.classList.remove('active');
                    labelForm.classList.add('active');
                }
            });

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