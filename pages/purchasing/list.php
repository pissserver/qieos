<?php
include '../../sessions/session.php';

$qLast = mysqli_query($conn,"SELECT MAX(CAST(REPLACE(form, 'FORM-', '') AS UNSIGNED)) AS last_num FROM purchases");
$dLast = mysqli_fetch_assoc($qLast);
$nextNum = ($dLast && $dLast['last_num'] ? (int)$dLast['last_num'] : 0) + 1;
$formNumber = 'FORM-' . str_pad($nextNum,7,'0',STR_PAD_LEFT);

// Produk untuk dropdown (semua kategori kecuali Additional)
$qProd = mysqli_query($conn, "SELECT id, name, unit FROM products WHERE deleted_at IS NULL AND category <> 'Additional' ORDER BY name ASC");
$products = [];
while($p = mysqli_fetch_assoc($qProd)){
    $products[] = ['id' => (int)$p['id'], 'name' => $p['name'], 'unit' => $p['unit']];
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Pembelian Stok - Qieos</title>
<?php include '../../script/headscript.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/list.css?v=<?= filemtime(__DIR__ . '/../../css/pages/list.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>

<body>

<?php include '../components/sidebar.php'; ?>

<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-5">
    <!-- FORM -->
    <div class="section-card mb-5">
        <div class="panel-header panel-dark">
            <div class="panel-left">
                <div class="panel-icon">
                    <i class="fas fas fa-file-alt"></i>
                </div>

                <div>
                    <div class="panel-title">
                        Daftar Belanja 
                    </div>
                    <div class="panel-subtitle">
                        Buat daftar produk yang akan dibelanja
                    </div>
                </div>
            </div>

            <div class="panel-toggle-wrap">
                <span class="toggle-label active" id="labelForm">Form</span>

                <label class="switch-toggle">
                    <input type="checkbox" id="panelToggle">
                    <span class="slider-toggle"></span>
                </label>

                <span class="toggle-label" id="labelEdit">Edit</span>
            </div>
        </div>

        <div class="mt-5 px-4">
            <div class="stock-body">

                <div id="formMode" class="panel-mode active">
<form id="form-stock"
                            action="list-action.php?action=store"
                            method="POST">

                        <input type="hidden" name="form_number" value="<?= $formNumber ?>">

                        <div id="itemsContainer">

                            <div class="item-row row mb-3">

                                <div class="col-md-4">
                                    <select
                                        name="product_id[]"
                                        class="form-control product-select"
                                        placeholder="Nama Produk"
                                        required>
                                        <option value=""></option>
                                        <?php foreach($products as $p): ?>
                                        <option value="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-unit="<?= htmlspecialchars($p['unit']) ?>"><?= htmlspecialchars($p['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <input type="number"
                                        name="qty_buy[]"
                                        class="form-control"
                                        placeholder="Qty"
                                        required>
                                </div>

                                <div class="col-md-2">
                                    <input type="text"
                                        name="unit_buy[]"
                                        class="form-control"
                                        placeholder="Satuan"
                                        required>
                                </div>

                                <div class="col-md-4">
                                    <input type="number"
                                        name="price_buy[]"
                                        class="form-control"
                                        placeholder="Total Harga (Rp)"
                                        min="0">
                                </div>

                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">

                            <button type="button"
                                    class="btn-save"
                                    onclick="addItem()">

                                <i class="fas fa-plus"></i>
                                Tambah Item

                            </button>

                            <button type="submit"
                                    class="btn-save">

                                <i class="fas fa-save"></i>
                                Simpan

                            </button>

                        </div>

                    </form>
                </div>

                <div id="editMode" class="panel-mode">
                    <div id="purchase-table"></div>
                </div>
            </div>
        </div>
    </div>   
</div>
</main>

<!-- EDIT MODAL -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content stock-panel border-0">

            <div class="panel-header panel-dark my-3 mx-3">
                <div class="panel-left">
                    <div class="panel-icon">
                        <i class="fas fas fa-file-alt"></i>
                    </div>

                    <div>
                        <div class="panel-title">
                            Edit Daftar Belanja 
                        </div>
                        <div class="panel-subtitle">
                            Edit barang, qty, dan satuan daftar belanja
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="mt-2 px-5" id="editPurchaseContent"></div>
        </div>
    </div>
</div>

<?php include '../../script/footscript.php'; ?>

<script>
    document.getElementById("form-stock").addEventListener("submit", function(e){
        e.preventDefault();

        // Validasi manual (required Select2 tidak tervalidasi otomatis)
        let rows = document.querySelectorAll('#itemsContainer .item-row');
        let valid = true;
        rows.forEach(function(r){
            let sel = r.querySelector('select[name="product_id[]"]');
            let qty = r.querySelector('input[name="qty_buy[]"]');
            let unit = r.querySelector('input[name="unit_buy[]"]');
            if(!sel || !sel.value){
                QToast('Error', 'Nama produk wajib dipilih', 'error');
                valid = false;
                return;
            }
            if(!qty || !qty.value.trim()){
                QToast('Error', 'Qty wajib diisi', 'error');
                valid = false;
                return;
            }
            if(!unit || !unit.value.trim()){
                QToast('Error', 'Satuan wajib diisi', 'error');
                valid = false;
                return;
            }
        });
        if(!valid) return;

        let formData = new FormData(this);

        fetch(this.action,{
            method:"POST",
            body:formData
        })
        .then(res=>res.json())
        .then(res=>{
            if(res.status==="success"){
                QToast("Berhasil", res.msg, "success");
                resetItemRows();
            }else{
                QToast("Error", res.msg, "error");
            }
        });
    });

    // Reset form item ke kondisi awal (satu baris kosong seperti halaman baru)
    function resetItemRows(){
        let container = document.getElementById('itemsContainer');
        container.querySelectorAll('.product-select').forEach(function(sel){
            let $s = $(sel);
            if($s.hasClass('select2-hidden-accessible')) $s.select2('destroy');
        });
        container.innerHTML = '';

        let row = document.createElement('div');
        row.className = 'item-row row mb-3';
        row.innerHTML = `
            <div class="col-md-4">
                <select name="product_id[]" class="form-control product-select" placeholder="Nama Produk" required>
                    ${buildProductOptions('')}
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="qty_buy[]" class="form-control" placeholder="Qty" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="unit_buy[]" class="form-control" placeholder="Satuan" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="price_buy[]" class="form-control" placeholder="Harga" min="0">
            </div>
        `;
        container.appendChild(row);
        initProductSelect(row.querySelector('.product-select'));
    }

    // Daftar produk untuk dropdown dinamis (semua kecuali Additional)
    var PRODUCT_LIST = <?= json_encode(array_values($products), JSON_UNESCAPED_UNICODE) ?>;

    function escAttr(s){
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function buildProductOptions(selected){
        let html = '<option value=""></option>';
        PRODUCT_LIST.forEach(function(p){
            let sel = (selected !== '' && String(selected) === String(p.id)) ? ' selected' : '';
            html += '<option value="' + p.id + '" data-name="' + escAttr(p.name) + '" data-unit="' + escAttr(p.unit || '') + '"' + sel + '>' + escAttr(p.name) + '</option>';
        });
        return html;
    }

    function applyItemSelect2($sel){
        if(!$sel || !$sel.length) return;
        if($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
        let opts = {
            width: '100%',
            placeholder: 'Cari / Pilih Produk...',
            allowClear: true
        };
        if($sel.closest('#editPurchaseModal').length){
            opts.dropdownParent = $('#editPurchaseModal');
        }
        $sel.select2(opts);
        $sel.on('change', function(){
            let unitInput = $(this).closest('.item-row').find('input[name="unit_buy[]"]');
            if(unitInput.length){
                let opt = this.options[this.selectedIndex];
                let u = opt ? opt.getAttribute('data-unit') : '';
                if(u) unitInput.val(u);
            }
        });
    }

    function initProductSelect(sel){
        if(!sel || typeof $ === 'undefined') return;
        applyItemSelect2($(sel));
    }

    function initEditSelects(){
        document.querySelectorAll('#itemsContainerEdit .product-select').forEach(function(sel){
            applyItemSelect2($(sel));
        });
    }

    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('#itemsContainer .product-select').forEach(initProductSelect);
    });

    const panelToggle = document.getElementById('panelToggle');

    panelToggle.addEventListener('change', function(){

        const formMode = document.getElementById('formMode');
        const editMode = document.getElementById('editMode');

        const labelForm = document.getElementById('labelForm');
        const labelEdit = document.getElementById('labelEdit');

        if(this.checked){
            formMode.classList.remove('active');
            editMode.classList.add('active');

            labelForm.classList.remove('active');
            labelEdit.classList.add('active');

            loadPurchaseTable();
        }else{
            editMode.classList.remove('active');
            formMode.classList.add('active');

            labelEdit.classList.remove('active');
            labelForm.classList.add('active');
        }

    });

    function loadPurchaseTable(){
        fetch('list-table.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('purchase-table').innerHTML = html;

            setTimeout(() => {

                if ($.fn.DataTable.isDataTable('#purchaseTable')) {
                    $('#purchaseTable').DataTable().destroy();
                }

                let table = $('#purchaseTable').DataTable({
                    pageLength:5,
                    lengthMenu:[[5,10,25,50],[5,10,25,50]],
                    responsive:true,
                    autoWidth:false,
                    order: [[0, 'desc']],
                    ordering: true,
                    language:{
                        search:"",
                        searchPlaceholder:"Cari daftar belanja...",
                        
                        zeroRecords: `
                            <div class="empty-search">
                                <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                <div class="empty-title">Purchase tidak ditemukan</div>
                                <div class="empty-sub">
                                    Coba gunakan kata kunci lain
                                </div>
                            </div>
                        `,

                        emptyTable: `
                            <div class="empty-search">
                                <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                <div class="empty-title">Belum ada data list belanja</div>
                                <div class="empty-sub">
                                    Silakan buat list belanja terlebih dahulu
                                </div>
                            </div>
                        `
                    }
                });

                table.columns.adjust();

                if(table.responsive){
                    table.responsive.recalc();
                }

            },250);
        });
    }

    function addItem()
    {
        let html = `
            <div class="item-row row mb-3">

                <div class="col-md-4">
                    <select
                        name="product_id[]"
                        class="form-control product-select"
                        placeholder="Nama Produk"
                        required>
                        ${buildProductOptions('')}
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="number"
                        name="qty_buy[]"
                        class="form-control"
                        placeholder="Qty"
                        required>
                </div>

                <div class="col-md-2">
                    <input type="text"
                        name="unit_buy[]"
                        class="form-control"
                        placeholder="Satuan"
                        required>
                </div>

                <div class="col-md-3">
                    <input type="number"
                        name="price_buy[]"
                        class="form-control"
                        placeholder="Harga"
                        min="0">
                </div>

                <div class="col-md-1">
                    <button type="button"
                            class="btn btn-danger w-100"
                            onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

            </div>
        `;

        document
            .getElementById('itemsContainer')
            .insertAdjacentHTML('beforeend', html);

        let last = document.querySelector('#itemsContainer .item-row:last-child select.product-select');
        initProductSelect(last);
    }

    function addItemEdit()
    {
        let html = `
            <div class="item-row row mb-3">

                <input type="hidden"
                    name="item_id[]"
                    value="">

                <div class="col-md-4">
                    <select
                        name="product_id[]"
                        class="form-control product-select"
                        placeholder="Nama Produk"
                        required>
                        ${buildProductOptions('')}
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="number"
                        name="qty_buy[]"
                        class="form-control"
                        placeholder="Qty"
                        min="0"
                        required>
                </div>

                <div class="col-md-2">
                    <input type="text"
                        name="unit_buy[]"
                        class="form-control"
                        placeholder="Satuan"
                        required>
                </div>

                <div class="col-md-3">
                    <input type="number"
                        name="price_buy[]"
                        class="form-control"
                        placeholder="Harga"
                        min="0"
                        value="">
                </div>

                <div class="col-md-1">
                    <button type="button"
                            class="btn btn-danger w-100"
                            onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

            </div>
        `;

        document
            .querySelector('#itemsContainerEdit')
            .insertAdjacentHTML('beforeend', html);

        let last = document.querySelector('#itemsContainerEdit .item-row:last-child select.product-select');
        applyItemSelect2($(last));
    }

    function removeItem(button)
    {
        button.closest('.item-row').remove();
    }
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editPurchaseBtn',function(){

        let id = $(this).data('id');

        $('#editPurchaseModal').modal('show');

        document.getElementById('editPurchaseContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('list-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editPurchaseContent').innerHTML = html;
            initEditSelects();
        });

    });

    // Edit Action
    $(document).on('submit','#editPurchaseForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('list-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil diperbarui', 'success');

                $('#editPurchaseModal').modal('hide');

                loadPurchaseTable();

            }else{

                QToast('Gagal', res.msg || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
        });
    });

    // Delete Action
    $(document).on('click','.deletePurchaseBtn',function(){

        let id = $(this).data('id');
        let date = $(this).data('date');
        let formattedDate = new Date(date).toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        QConfirm('Hapus Daftar Belanja?', 'Daftar belanja tanggal ' + formattedDate + ' akan dihapus permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('list-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        loadPurchaseTable();

                    }

                });
            }
        });

    });

    // Print Action
    $(document).on('click', '.printPurchaseBtn', function () {

        let id = $(this).data('id');
        let date = $(this).data('date');
        let formattedDate = new Date(date).toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        fetch('list-action.php?action=get_print&id=' + id)
            .then(res => res.json())
            .then(res => {

                let items = res.data;

                let rows = '';

                items.forEach((item, i) => {
                    rows += `
                        <tr>
                            <td style="text-align:center">${i + 1}</td>
                            <td>${item.name}</td>
                            <td style="text-align:center">${item.qty}</td>
                            <td style="text-align:center">${item.unit}</td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center">☐</td>
                        </tr>
                    `;
                });

                let win = window.open('', '', 'width=900,height=700');

                win.document.write(`
                    <html>
                    <head>
                        <title>Print Daftar Belanja</title>
                        <style>
                            body { font-family: Poppins; padding: 20px; }
                            h2 { margin-bottom: 5px; text-align: center; text-transform: uppercase;}
                            .sub { color: #666; margin-bottom: 50px; text-align: center;}

                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }

                            table, th, td {
                                border: 1px solid #000;
                            }

                            th, td {
                                padding: 8px;
                            }

                            th {
                                background: #f2f2f2;
                            }
                        </style>
                    </head>
                    <body>

                        <h2>Daftar Belanja (${formattedDate})</h2>
                        <div class="sub">by QIEOS</div>

                        <table>
                            <colgroup>
                                <col style="width:6%">
                                <col style="width:44%">
                                <col style="width:10%">
                                <col style="width:12%">
                                <col style="width:18%">
                                <col style="width:10%">
                            </colgroup>
                            
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang / Produk</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows}
                            </tbody>
                        </table>

                    </body>
                    </html>
                `);

                win.document.close();

                // 🔥 FIX IMPORTANT
                setTimeout(() => {
                    win.print();
                }, 300);

            });

    });
</script>

</body>
</html>