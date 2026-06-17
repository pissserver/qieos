<?php
include '../../sessions/session.php';

$qLast = mysqli_query($conn,"SELECT MAX(id) as last_id FROM purchases");
$dLast = mysqli_fetch_assoc($qLast);

$lastId = $dLast['last_id'] ? (int)$dLast['last_id'] : 0;

if(!isset($_SESSION['current_form_id'])){
    $_SESSION['current_form_id'] = $lastId > 0 ? $lastId : 1;
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

<style>
    :root{
        --primary:#1e293b;
        --primary-dark:#0f172a;
        --accent:#334155;
        --soft:#f8fafc;
        --border:#cbd5e1;
        --text:#0f172a;
        --muted:#64748b;
        --badge:#e2e8f0;
    }

    /* SECTION */

    .section-card{
        background:#fff;
        border-radius:24px;
        padding:24px;
        box-shadow:0 8px 24px rgba(15,23,42,.05);
    }

    /* PANEL */
            
    .panel-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:18px 22px;
        border-radius:18px;
    }

    .panel-left{
        display:flex;
        align-items:center;
        gap:16px;
    }

    .panel-icon{
        width:58px;
        height:58px;
        border-radius:16px;
        background:rgba(255,255,255,.12);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:22px;
    }

    .panel-title{
        font-size:17px;
        font-weight:700;
    }

    .panel-subtitle{
        font-size:13px;
        opacity:.85;
    }

    .panel-primary{
        background:linear-gradient(
            135deg,
            #4f46e5,
            #4338ca
        );
        color:#fff;
    }


    .panel-dark{
        background:linear-gradient(
            135deg,
            #334155,
            #0f172a
        );
        color:#fff;
    }

    .panel-toggle-wrap{
        display:flex;
        align-items:center;
        gap:10px;
    }

    .toggle-label{
        font-size:13px;
        color:#cbd5e1;
        font-weight:500;
        transition:.25s;
    }

    .toggle-label.active{
        color:#fff;
        font-weight:700;
    }

    .switch-toggle{
        position:relative;
        display:inline-block;
        width:54px;
        height:28px;
    }

    .switch-toggle input{
        opacity:0;
        width:0;
        height:0;
    }

    .slider-toggle{
        position:absolute;
        cursor:pointer;
        inset:0;
        background:#475569;
        border-radius:50px;
        transition:.3s;
    }

    .slider-toggle:before{
        content:"";
        position:absolute;
        width:22px;
        height:22px;
        left:3px;
        top:3px;
        background:#fff;
        border-radius:50%;
        transition:.3s;
    }

    .switch-toggle input:checked + .slider-toggle{
        background:#22c55e;
    }

    .switch-toggle input:checked + .slider-toggle:before{
        transform:translateX(26px);
    }

    .panel-mode{
        display:none;
        animation:fadeSlide .3s ease;
    }

    .panel-mode.active{
        display:block;
    }

    @keyframes fadeSlide{
        from{
            opacity:0;
            transform:translateY(8px);
        }
        to{
            opacity:1;
            transform:translateY(0);
        }
    }

    /* SECTION */
    .section-title{
        display:inline-block;
        background:#edf4ff;
        color:var(--primary-dark);
        padding:5px 12px;
        border-radius:7px;
        font-size:12px;
        font-weight:600;
        margin-bottom:14px;
    }

    /* FORM */
    .input-group-modern{
        display:flex;
        align-items:center;
        margin-bottom:14px;
    }

    .input-icon{
        width:42px;
        height:42px;
        border-radius:10px;
        background:linear-gradient(135deg,#334155,#1e293b);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        margin-right:10px;
        box-shadow:0 4px 10px rgba(15,23,42,.18);
    }

    .form-control{
        height:42px;
        border-radius:8px;
        border:1px solid var(--border);
        font-size:14px;
    }

    textarea.form-control{
        height:65px;
    }

    .form-control:focus{
        border-color:var(--primary);
        box-shadow:0 0 0 3px rgba(63,130,196,.15);
    }

    /* BUTTON */
    .btn-save{
        background:linear-gradient(90deg,#1e293b,#334155);
        border:none;
        border-radius:8px;
        color:#fff;
        padding:10px 24px;
        font-weight:600;
    }

    .btn-prev-form{
        background:#334155;
        color:#fff;
        border:none;
        padding:11px 18px;
        border-radius:10px;
        font-weight:600;
    }

    .btn-change-form{
        background:#0f172a;
        color:#fff;
        border:none;
        padding:11px 18px;
        border-radius:10px;
        font-weight:600;
    }

    .btn-prev-form:hover,
    .btn-change-form:hover{
        opacity:.9;
    }

    /* DATATABLE */
    .dataTables_filter input{
        border-radius:8px !important;
        border:1px solid #cbd5e1 !important;
        padding:8px 14px !important;
        width:180px !important;
    }

    .dataTables_length select{
        min-width:75px !important;
        height:38px !important;
        padding:0 30px 0 12px !important;
        border-radius:8px !important;
        border:1px solid #cbd5e1 !important;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter{
        margin-bottom:18px;
    }

    .empty-search{
        padding:50px 20px;
        text-align:center;
    }

    .empty-img{
        width:370px;
        opacity:.9;
        margin-bottom:18px;
    }

    .empty-title{
        font-size:18px;
        font-weight:700;
        color:#0f172a;
        margin-bottom:6px;
    }

    .empty-sub{
        font-size:14px;
        color:#64748b;
    }

    .modal-backdrop.show{
        opacity:.55 !important;
    }

    #editPurchaseModal .modal-content{
        background:#fff !important;
        border-radius:16px !important;
        overflow:hidden;
        box-shadow:0 20px 40px rgba(15,23,42,.25);
    }

    #editPurchaseModal .stock-body{
        background:#fff !important;
    }

    #editPurchaseModal .modal-dialog{
        max-width:1200px;
    }

    #editPurchaseModal .btn-close{
        filter:brightness(0) invert(1);
        opacity:.85;
    }

    #editPurchaseModal .modal-content *{
        opacity:1 !important;
    }
</style>

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
                        action="list-action.php"
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
                Swal.fire("Berhasil",res.msg,"success");
                this.reset();
            }else{
                Swal.fire("Error",res.msg,"error");
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

                Swal.fire({
                    icon:'success',
                    title:'Berhasil',
                    text:'Data berhasil diperbarui',
                    showConfirmButton:false
                });

                $('#editPurchaseModal').modal('hide');

                loadPurchaseTable();

            }else{

                Swal.fire({
                    icon:'error',
                    title:'Gagal',
                    text:res.msg || 'Terjadi kesalahan'
                });

            }

        })
        .catch(() => {
            Swal.fire(
                'Error',
                'Gagal memproses update',
                'error'
            );
        });
    });

    $(document).on('click','.deletePurchaseBtn',function(){

        let id = $(this).data('id');
        let form = $(this).data('form');
        let products = $(this).data('products');
        let qty = $(this).data('qty');
        let unit = $(this).data('unit');

        Swal.fire({
            title:'Hapus Purchase?',
            html:`
                <div style="text-align:center">
                    <b>${form}</b><br><br>
                    <small style="color:#94a3b8">Produk:</small><br>
                    ${products} (${qty} ${unit})
                </div>
            `,
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal',
            confirmButtonColor:'#dc2626'
        }).then((result)=>{

            if(result.isConfirmed){

                fetch('purchase-action.php?action=destroy&id='+id)
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        Swal.fire({
                            icon:'success',
                            title:'Terhapus',
                            text:'Data berhasil dihapus',
                            timer:1500,
                            showConfirmButton:false
                        });

                        loadPurchaseTable();

                    }

                });

            }

        });

    });
</script>

</body>
</html>