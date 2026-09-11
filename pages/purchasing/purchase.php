<?php
include '../../sessions/session.php';

$qLast = mysqli_query($conn,"SELECT form FROM purchases WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 1");
$dLast = mysqli_fetch_assoc($qLast);

$lastNum = 0;
if ($dLast && !empty($dLast['form'])) {
    // Ambil angka dari format FORM-0000002 -> 2
    $lastNum = (int) preg_replace('/[^0-9]/', '', $dLast['form']);
}

if(!isset($_SESSION['current_form_id'])){
    $_SESSION['current_form_id'] = $lastNum > 0 ? $lastNum : 1;
}

$currentFormId = $_SESSION['current_form_id'];
$formNumber = 'FORM-' . str_pad($currentFormId,7,'0',STR_PAD_LEFT);

$hasPrevious = $currentFormId > 1;
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Input Pembelian - Qieos</title>
<?php include '../../script/headscript.php'; ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/purchase.css">

</head>

<body>

<?php include '../components/sidebar.php'; ?>

<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-5">
    <!-- FORM -->
    <div class="section-card mb-4">
        <div class="panel-header panel-dark">
            <div class="panel-left">
                <div class="panel-icon">
                    <i class="fas fas fa-file-alt"></i>
                </div>

                <div>
                    <div class="panel-title">
                        Form Pembelian 
                    </div>
                    <div class="panel-subtitle">
                        Tambah stok produk yang dibeli
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

        <div class="mt-4 px-4">
            <div class="stock-body">

                <div id="formMode" class="panel-mode active">
                    <form id="form-stock" action="purchase-action.php?action=store" method="POST" enctype="multipart/form-data">

                        <!-- FORM NUMBER -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-hashtag"></i>
                                    </div>

                                    <input type="text"
                                        name="form_number"
                                        class="form-control fw-bold"
                                        value="<?= $formNumber ?>"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <!-- INFORMASI PRODUK -->
                        <div class="section-title">Informasi Produk</div>

                        <div class="row">

                            <!-- KODE -->
                            <div class="col-md-3">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-barcode"></i>
                                    </div>

                                    <input type="text"
                                        name="code"
                                        id="productCode"
                                        class="form-control"
                                        list="productCodeList"
                                        placeholder="Pilih / ketik kode produk baru"
                                        autocomplete="off"
                                        required>

                                    <datalist id="productCodeList"></datalist>
                                </div>
                            </div>

                            <!-- NAMA -->
                            <div class="col-md-3">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-box"></i>
                                    </div>

                                    <input type="text"
                                        name="product_name"
                                        id="productName"
                                        class="form-control"
                                        placeholder="Nama Produk"
                                        required>
                                </div>
                            </div>

                            <!-- KATEGORI -->
                            <div class="col-md-3">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-tags"></i>
                                    </div>

                                    <select name="category"
                                            id="productCategory"
                                            class="form-control"
                                            required>
                                        <option value="">Kategori</option>
                                        <option value="makanan">Makanan</option>
                                        <option value="minuman">Minuman</option>
                                        <option value="jajanan">Jajanan</option>
                                        <option value="pelengkap">Pelengkap</option>
                                    </select>
                                </div>
                            </div>

                            <!-- FOTO -->
                            <div class="col-md-3">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-image"></i>
                                    </div>

                                    <input type="file"
                                        name="photo"
                                        class="form-control">
                                </div>
                            </div>

                        </div>

                        <!-- DETAIL STOK -->
                        <div class="section-title mt-3">Detail Stok</div>

                        <div class="row">

                            <!-- QTY -->
                            <div class="col-md-3">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-cubes"></i>
                                    </div>

                                    <input type="number"
                                        name="qty"
                                        class="form-control"
                                        placeholder="Qty"
                                        required>
                                </div>
                            </div>

                            <!-- SATUAN -->
                            <div class="col-md-3">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-balance-scale"></i>
                                    </div>

                                    <input type="text"
                                        name="unit"
                                        class="form-control"
                                        placeholder="Satuan"
                                        required>
                                </div>
                            </div>

                            <!-- HARGA BELI -->
                            <div class="col-md-3">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-wallet"></i>
                                    </div>

                                    <input type="number"
                                        name="buy_price"
                                        class="form-control"
                                        placeholder="Harga Beli"
                                        required>
                                </div>
                            </div>

                            <!-- HARGA JUAL -->
                            <div class="col-md-3">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>

                                    <input type="number"
                                        name="sell_price"
                                        class="form-control"
                                        placeholder="Harga Jual"
                                        required>
                                </div>
                            </div>

                        </div>

                        <!-- NOTE -->
                        <div class="input-group-modern mt-3">
                            <div class="input-icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>

                            <textarea name="note"
                                    class="form-control"
                                    placeholder="Catatan transaksi"></textarea>
                        </div>

                        <!-- ACTION -->
                        <div class="d-flex justify-content-end gap-2 mt-4">

                            <?php if($hasPrevious): ?>
                            <button type="button" id="prevFormBtn" class="btn-prev-form">
                                <i class="fas fa-arrow-left me-1"></i>
                                Form Sebelumnya
                            </button>
                            <?php endif; ?>

                            <button type="button" id="nextFormBtn" class="btn-change-form">
                                <i class="fas fa-sync-alt me-1"></i>
                                Ganti Form
                            </button>

                            <button type="submit" class="btn-save">
                                <i class="fas fa-save me-1"></i>
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

    <!-- TABLE -->
    <div class="section-card mb-5">
        <div class="panel-header panel-primary">
            <div class="panel-left">
                <div class="panel-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>

                <div>
                    <div class="panel-title">
                        Pembelian Produk 
                    </div>
                    <div class="panel-subtitle">
                        List produk yang sudah dibeli
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 px-4">
            <div id="table-stock"></div>
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
                            Edit Form Pembelian 
                        </div>
                        <div class="panel-subtitle">
                            Edit informasi pembelian dan detail stok produk
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

        let formData = new FormData(this);

        fetch(this.action,{
            method:"POST",
            body:formData
        })
        .then(res=>res.json())
        .then(res=>{
            if(res.status==="success"){
                QToast('Berhasil', res.msg, 'success');
                this.reset();
                document.getElementById('productCode').focus();
                loadTable();
            }else{
                QToast("Error", res.msg, "error");
            }
        });
    });

    function padFormNumber(n){
        return 'FORM-' + String(n).padStart(7,'0');
    }

    function updateFormNumber(res){
        if(res.form_number){
            document.querySelector('input[name="form_number"]').value = res.form_number;
        }
        loadTable();
        loadPurchaseTable();
    }

    // Next form button
    document.getElementById('nextFormBtn')?.addEventListener('click',function(){

        fetch('purchase-action.php?action=next_form')
        .then(res=>res.json())
        .then(res=>{
            if(res.status==='success'){
                updateFormNumber(res);
                QToast('Berhasil', 'Beralih ke ' + res.form_number, 'success');
            }
        });

    });

    // Previous form button
    document.getElementById('prevFormBtn')?.addEventListener('click',function(){

        fetch('purchase-action.php?action=prev_form')
        .then(res=>res.json())
        .then(res=>{
            if(res.status==='success'){
                updateFormNumber(res);
                QToast('Berhasil', 'Beralih ke ' + res.form_number, 'success');
            }
        });

    });

    function loadTable(){
        fetch('purchase-stock-table.php')
        .then(res=>res.text())
        .then(html=>{
            document.getElementById("table-stock").innerHTML=html;

            setTimeout(()=>{
                $('#stockTable').DataTable({
                    pageLength:5,
                    lengthMenu:[[5,10,25,50],[5,10,25,50]],
                    responsive:true,
                    autoWidth:false,
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
                    }
                });
            },100);
        });
    }

    loadTable();

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
        fetch('purchase-table.php')
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
                        searchPlaceholder:"Cari purchase...",
                        
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
                                <div class="empty-title">Belum ada data purchase</div>
                                <div class="empty-sub">
                                    Silakan tambahkan purchase terlebih dahulu
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

    // LOAD DATA KODE PRODUK
    function loadProductCodes(){
        fetch('purchase-product-code.php')
        .then(res => res.json())
        .then(data => {

            const list = document.getElementById('productCodeList');
            list.innerHTML = '';

            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.code;
                option.dataset.name = item.name;
                option.dataset.category = item.category;
                option.dataset.sellPrice = item.sell_price;
                option.dataset.unit = item.unit;
                list.appendChild(option);
            });

        });
    }

    document.getElementById('productCode').addEventListener('input', function(){

        const code = this.value;
        const options = document.querySelectorAll('#productCodeList option');

        let found = false;

        options.forEach(opt => {
            if(opt.value === code){
                document.getElementById('productName').value = opt.dataset.name;
                document.getElementById('productCategory').value = opt.dataset.category;
                document.querySelector('input[name="sell_price"]').value = opt.dataset.sellPrice;
                document.querySelector('input[name="unit"]').value = opt.dataset.unit;
                found = true;
            }
        });

        // kalau kode baru
        if(!found){
            document.getElementById('productName').value = '';
            document.getElementById('productCategory').value = '';
            document.querySelector('input[name="sell_price"]').value = '';
        }

    });

    loadProductCodes();
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

        fetch('purchase-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editPurchaseContent').innerHTML = html;
        });

    });

    // Edit Action
    $(document).on('submit','#editPurchaseForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('purchase-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil diperbarui', 'success');

                $('#editPurchaseModal').modal('hide');

                loadPurchaseTable();
                loadTable();

            }else{

                QToast('Gagal', res.msg || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
        });
    });

    $(document).on('click','.deletePurchaseBtn',function(){

        let id = $(this).data('id');
        let form = $(this).data('form');
        let products = $(this).data('products');
        let qty = $(this).data('qty');
        let unit = $(this).data('unit');

        QConfirm('Hapus Data Pembelian?', 'Data pembelian form ' + form + ' akan dihapus permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('purchase-action.php?action=destroy&id='+id)
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        loadPurchaseTable();
                        loadTable();

                    }

                });
            }
        });

    });
</script>

</body>
</html>