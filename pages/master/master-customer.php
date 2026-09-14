<?php include '../../sessions/session.php'; ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Master Customer - Qieos</title>
    <?php include '../../script/headscript.php'; ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product.css">
</head>
<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">
    <div class="row">
        <div class="col-md-12 mb-5">
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="panel-title">Master Customer</div>
                            <div class="panel-subtitle">Kelola data customer</div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 px-4">
                    <div id="btnContainer" style="display:none;">
                        <button type="button" class="btn btn-primary" id="btnAddCustomer">
                            <i class="fas fa-plus me-2"></i>Tambah Customer
                        </button>
                    </div>
                    <div class="table-responsive-wrap" id="customerTableContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-plus"></i></div>
                        <div>
                            <div class="panel-title">Tambah Customer</div>
                            <div class="panel-subtitle">Tambah nama dan nomor telepon customer</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5" id="addCustomerContent"></div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-edit"></i></div>
                        <div>
                            <div class="panel-title">Edit Customer</div>
                            <div class="panel-subtitle">Ubah informasi customer</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5" id="editCustomerContent"></div>
            </div>
        </div>
    </div>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
function loadCustomerTable(){
    $('#btnContainer').hide().insertBefore('#customerTableContainer');
    fetch('master-customer-table.php')
    .then(res=>res.text())
    .then(html=>{
        document.getElementById('customerTableContainer').innerHTML = html;
        if($.fn.DataTable.isDataTable('#stockTable')){
            $('#stockTable').DataTable().destroy();
        }
        setTimeout(()=>{
            $('#stockTable').DataTable({
                pageLength:5,
                lengthMenu:[[5,10,25,50],[5,10,25,50]],
                responsive:true,
                autoWidth:false,
                language:{
                    search:"",
                    searchPlaceholder:"Cari customer...",
                    zeroRecords:'<div class="empty-search"><img src="../../assets/img/illustrations/empty-data.png" class="empty-img"><div class="empty-title">Customer tidak ditemukan</div><div class="empty-sub">Coba gunakan kata kunci lain</div></div>',
                    emptyTable:'<div class="empty-search"><img src="../../assets/img/illustrations/empty-data.png" class="empty-img"><div class="empty-title">Belum ada data customer</div><div class="empty-sub">Silakan tambahkan customer terlebih dahulu</div></div>'
                }
            });
            $('#stockTable_filter').wrap('<div class="table-action-wrapper"></div>');
            $('#btnContainer').show().appendTo('.table-action-wrapper');
        },100);
    });
}
$(document).ready(function(){ loadCustomerTable(); });

// ADD
$(document).on('click','#btnAddCustomer',function(){
    $('#addCustomerModal').modal('show');
    document.getElementById('addCustomerContent').innerHTML='<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch('master-customer-add.php').then(res=>res.text()).then(html=>{ document.getElementById('addCustomerContent').innerHTML=html; });
});
$(document).on('submit','#addCustomerForm',function(e){
    e.preventDefault();
    let formData=new FormData(this);
    fetch('master-customer-action.php?action=store',{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        if(res.status==='success'){ QToast('Berhasil','Customer berhasil ditambahkan','success'); $('#addCustomerModal').modal('hide'); loadCustomerTable(); }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan','error'); }
    }).catch(()=>{ QToast('Error','Gagal memproses','error'); });
});

// EDIT
$(document).on('click','.editCustomerBtn',function(){
    let id=$(this).data('id');
    $('#editCustomerModal').modal('show');
    document.getElementById('editCustomerContent').innerHTML='<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch('master-customer-edit.php?id='+id).then(res=>res.text()).then(html=>{ document.getElementById('editCustomerContent').innerHTML=html; });
});
$(document).on('submit','#editCustomerForm',function(e){
    e.preventDefault();
    let formData=new FormData(this);
    let id=formData.get('id');
    fetch('master-customer-action.php?action=update&id='+id,{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        if(res.status==='success'){ QToast('Berhasil','Customer berhasil diperbarui','success'); $('#editCustomerModal').modal('hide'); loadCustomerTable(); }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan','error'); }
    }).catch(()=>{ QToast('Error','Gagal memproses update','error'); });
});

// DELETE
$(document).on('click','.deleteCustomerBtn',function(){
    let id=$(this).data('id');
    let name=$(this).data('name');
    QConfirm('Hapus Customer?','Data customer '+name+' akan dihapus permanen.',{confirmText:'Hapus',icon:'fa-trash-can',confirmClass:'q-confirm-btn-danger',iconClass:'q-confirm-icon-danger'}).then(function(ok){
        if(ok){
            fetch('master-customer-action.php?action=destroy',{method:'POST',body:new URLSearchParams({id:id})})
            .then(res=>res.json()).then(res=>{ if(res.status==='success'){ QToast('Terhapus','Data berhasil dihapus','success'); loadCustomerTable(); } });
        }
    });
});
</script>
</body>
</html>