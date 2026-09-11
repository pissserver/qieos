<?php
include '../../sessions/session.php';

$qLast = mysqli_query($conn,"SELECT form FROM purchases ORDER BY id DESC LIMIT 1");
$dLast = mysqli_fetch_assoc($qLast);

$lastNum = 0;
if ($dLast && !empty($dLast['form'])) {
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
<title>Pembelian Stok - Qieos</title>
<?php include '../../script/headscript.php'; ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/list.css">

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

                        <div id="itemsContainer">

                            <div class="item-row row mb-3">

                                <div class="col-md-4">
                                    <input type="text"
                                        name="product_name[]"
                                        class="form-control"
                                        placeholder="Nama Produk"
                                        required>
                                </div>

                                <div class="col-md-4">
                                    <input type="number"
                                        name="qty[]"
                                        class="form-control"
                                        placeholder="Qty"
                                        required>
                                </div>

                                <div class="col-md-4">
                                    <input type="text"
                                        name="unit[]"
                                        class="form-control"
                                        placeholder="Satuan"
                                        required>
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

        let formData = new FormData(this);

        fetch(this.action,{
            method:"POST",
            body:formData
        })
        .then(res=>res.json())
        .then(res=>{
            if(res.status==="success"){
                QToast("Berhasil", res.msg, "success");
                this.reset();
            }else{
                QToast("Error", res.msg, "error");
            }
        });
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
                    <input type="text"
                        name="product_name[]"
                        class="form-control"
                        placeholder="Nama Produk"
                        required>
                </div>

                <div class="col-md-4">
                    <input type="number"
                        name="qty[]"
                        class="form-control"
                        placeholder="Qty"
                        required>
                </div>

                <div class="col-md-4 d-flex">

                    <input type="text"
                        name="unit[]"
                        class="form-control"
                        placeholder="Satuan"
                        required>

                    <button type="button"
                            class="btn btn-danger ms-2"
                            onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>

                </div>

            </div>
        `;

        document
            .getElementById('itemsContainer')
            .insertAdjacentHTML('beforeend', html);
    }

    function addItemEdit()
    {
        let html = `
            <div class="item-row row mb-3">

                <input type="hidden"
                    name="item_id[]"
                    value="">

                <div class="col-md-4">
                    <input type="text"
                        name="product_name[]"
                        class="form-control"
                        placeholder="Nama Produk"
                        required>
                </div>

                <div class="col-md-2">
                    <input type="number"
                        name="qty[]"
                        class="form-control"
                        placeholder="Qty"
                        min="0"
                        required>
                </div>

                <div class="col-md-2">
                    <input type="text"
                        name="unit[]"
                        class="form-control"
                        placeholder="Satuan"
                        required>
                </div>

                <div class="col-md-3">
                    <input type="number"
                        name="price[]"
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