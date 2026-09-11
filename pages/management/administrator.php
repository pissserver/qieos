<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Administrator - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/administrator.css">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <!-- HEADER -->
    <!-- <div class="stock-header mt-5">
        <div>
            <h3>Stok Gudang</h3>
            <p>Monitoring stok produk dan FIFO layer secara realtime</p>
        </div>

        <div class="header-icon">
            <i class="fas fa-warehouse"></i>
        </div>
    </div> -->

    <div class="row">
        <div class="col-md-12">
            <!-- Main Table -->
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-users"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Administrator
                            </div>
                            <div class="panel-subtitle">
                                Ubah informasi administrator atau hapus dari daftar administrator
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <!-- Button Add -->
                    <div id="btnContainer" style="display:none;">
                        <button
                            type="button"
                            class="btn btn-primary"
                            id="btnAddAdministrator">
                            <i class="fas fa-user-plus me-2"></i>
                            Tambah Administrator
                        </button>
                    </div>
                    
                    <!-- TABLE -->
                    <div class="table-responsive-wrap" id="adminTableContainer">
                        <!-- Loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addAdministratorModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fas fa-user-plus"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Tambah Administrator 
                            </div>
                            <div class="panel-subtitle">
                                Tambah nama, username, dan password
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="addAdministratorContent"></div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editAdministratorModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fas fa-user"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Edit Administrator 
                            </div>
                            <div class="panel-subtitle">
                                Edit nama, username, dan password
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="editAdministratorContent"></div>
            </div>
        </div>
    </div>
</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
    function loadAdminTable(){
        $('#btnContainer').hide().insertBefore('#adminTableContainer');
        fetch('administrator-table.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('adminTableContainer').innerHTML = html;

            // Destroy old DataTable first
            if($.fn.DataTable.isDataTable('#stockTable')){
                $('#stockTable').DataTable().destroy();
            }

            // Reinit DataTable
            setTimeout(()=>{
            $('#stockTable').DataTable({
                pageLength: 5,
                lengthMenu:[[5,10,25,50],[5,10,25,50]],
                responsive: true,
                autoWidth: false,
                language:{
                    search:"",
                    searchPlaceholder:"Cari administrator...",

                    zeroRecords: `
                        <div class="empty-search">
                            <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                            <div class="empty-title">Administrator tidak ditemukan</div>
                            <div class="empty-sub">
                                Coba gunakan kata kunci lain
                            </div>
                        </div>
                    `,

                    emptyTable: `
                        <div class="empty-search">
                            <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                            <div class="empty-title">Belum ada data administrator</div>
                            <div class="empty-sub">
                                Silakan tambahkan administrator terlebih dahulu
                            </div>
                        </div>
                    `
                }
            });

            // Buat wrapper untuk search + button
            $('#stockTable_filter')
                .wrap('<div class="table-action-wrapper"></div>');

            // Pindahkan tombol ke wrapper
            $('#btnContainer')
                .show()
                .appendTo('.table-action-wrapper');
            },100);
        });
    }

    $(document).ready(function(){
        loadAdminTable();
    });
</script>

<!-- Script Add -->
<script>
    // Add Modal
    $(document).on('click','#btnAddAdministrator',function(){

        $('#addAdministratorModal').modal('show');

        document.getElementById('addAdministratorContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('administrator-add.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('addAdministratorContent').innerHTML = html;
        });

    });

    // Add Action
    $(document).on('submit','#addAdministratorForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch('administrator-action.php?action=store',{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil ditambahkan', 'success');

                $('#addAdministratorModal').modal('hide');

                loadAdminTable();

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses tambah data', 'error');
        });
    });
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editAdministratorBtn',function(){

        let id = $(this).data('id');

        $('#editAdministratorModal').modal('show');

        document.getElementById('editAdministratorContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('administrator-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editAdministratorContent').innerHTML = html;
        });

    });

    // Edit Action
    $(document).on('submit','#editAdministratorForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('administrator-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil diperbarui', 'success');

                $('#editAdministratorModal').modal('hide');

                loadAdminTable();

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

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
    $(document).on('click','.deleteAdministratorBtn',function(){

        let id = $(this).data('id');
        let fullname = $(this).data('fullname');

        QConfirm('Hapus Administrator?', 'Data ' + fullname + ' akan dihapus secara permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('administrator-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        loadAdminTable();
                    }
                });
            }
        });
    });
</script>

</body>
</html>