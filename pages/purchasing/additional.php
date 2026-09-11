<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Input Pembelian - Qieos</title>
<?php include '../../script/headscript.php'; ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/additional.css">

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
                        Produk Tambahan
                    </div>
                    <div class="panel-subtitle">
                        Tambahkan beberapa produk tambahan untuk penjualan
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 px-4">
            <div class="stock-body">

                <div id="formMode" class="panel-mode active">
                    <form id="form-stock" action="additional-action.php?action=store" method="POST" enctype="multipart/form-data">

                        <!-- INFORMASI PRODUK -->
                        <div class="section-title">Informasi Produk</div>

                        <div class="row">

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

                                    <input type="text"
                                        name="category"
                                        id="category"
                                        class="form-control"
                                        placeholder="Kategori Additional"
                                        required
                                        readonly>
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

                        <!-- ACTION -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save me-1"></i>
                                Simpan
                            </button>
                        </div>

                    </form>
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
                        Daftar Produk Tambahan 
                    </div>
                    <div class="panel-subtitle">
                        List produk tambahan yang akan ditampilkan pada katalog penjualan
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 px-4">
            <div id="additional-table"></div>
        </div>
    </div>
</div>
</main>

<!-- EDIT MODAL -->
<div class="modal fade" id="editAdditionalModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content stock-panel border-0">

            <div class="panel-header panel-dark my-3 mx-3">
                <div class="panel-left">
                    <div class="panel-icon">
                        <i class="fas fas fa-file-alt"></i>
                    </div>

                    <div>
                        <div class="panel-title">
                            Edit Produk Tambahan 
                        </div>
                        <div class="panel-subtitle">
                            Edit informasi produk tambahan
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="mt-2 px-5" id="editAdditionalContent"></div>
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
                loadTable();
            }else{
                QToast("Error", res.msg, "error");
            }
        });
    });

    function loadTable(){
        fetch('additional-table.php')
        .then(res=>res.text())
        .then(html=>{
            document.getElementById("additional-table").innerHTML=html;

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
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editAdditionalBtn',function(){

        let id = $(this).data('id');

        $('#editAdditionalModal').modal('show');

        document.getElementById('editAdditionalContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('additional-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editAdditionalContent').innerHTML = html;
        });

    });

    // Edit Action
    $(document).on('submit','#editAdditionalForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('additional-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil diperbarui', 'success');

                $('#editAdditionalModal').modal('hide');

                loadTable();

            }else{

                QToast('Gagal', res.msg || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
        });
    });
</script>

<!-- Script Delete -->
<script>
    // Delete Action
    $(document).on('click','.deleteAdditionalBtn',function(){

        let id = $(this).data('id');
        let name = $(this).data('name');

        QConfirm('Hapus Produk?', 'Data "' + name + '" akan dihapus permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('additional-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        setTimeout(() => {
                            loadTable();
                        }, 1000);
                    }
                });
            }
        });
    });
</script>

</body>
</html>