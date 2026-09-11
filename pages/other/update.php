<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Update - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/update.css">
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

    <div class="row mb-4">
        <div class="col-md-12">
            <!-- Main Table -->
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-rocket"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Update
                            </div>
                            <div class="panel-subtitle">
                                Menampilkan semua log history update sistem
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <!-- Button Add -->
                    <?php if($user['role'] == 'developer') : ?>
                    <div id="btnContainer" style="display:none;">
                        <button
                            type="button"
                            class="btn btn-primary"
                            id="btnAddUpdate">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Log Update
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- TABLE -->
                    <div id="updateTableContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addUpdateModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fas fa-rocket"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Tambah Log Update 
                            </div>
                            <div class="panel-subtitle">
                                Tambah nama update, tanggal, tipe, version dan deskripsi log update
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="addUpdateContent"></div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editUpdateModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fas fa-rocket"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Edit Update Log 
                            </div>
                            <div class="panel-subtitle">
                                Edit nama update, tanggal, tipe, version dan deskripsi log update
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="editUpdateContent"></div>
            </div>
        </div>
    </div>

    <!-- Show Details -->
    <div class="update-overlay" id="updateOverlay">

        <div class="update-modal">

            <div class="update-header">

                <div>
                    <div class="update-icon">
                        <i class="fas fa-rocket"></i>
                    </div>

                    <h4 id="updateTitle"></h4>

                    <div class="update-meta">

                        <span id="updateVersion"></span>

                        <span id="updateType" style="color: #000;"></span>

                        <span id="updateDate"></span>

                    </div>

                </div>

                <button class="closeUpdate">
                    <i class="fas fa-times"></i>
                </button>

            </div>

            <div class="update-body">

                <h6 class="mb-3">
                    <i class="fas fa-sparkles me-2"></i>
                    What's New
                </h6>

                <div id="updateDetailList" class="mb-5"></div>

            </div>

        </div>

    </div>
</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
    function loadUpdateTable() {
        $('#btnContainer').hide().insertBefore('#updateTableContainer');
        fetch('update-table.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('updateTableContainer').innerHTML = html;

            if($.fn.DataTable.isDataTable('#stockTable')){
                $('#stockTable').DataTable().destroy();
            }

            setTimeout(()=>{
            $('#stockTable').DataTable({
                pageLength: 5,
                lengthMenu:[[5,10,25,50],[5,10,25,50]],
                responsive: true,
                autoWidth: false,
                order: [],
                language:{
                    search:"",
                    searchPlaceholder:"Cari log update...",
                    zeroRecords: `
                        <div class="empty-search">
                            <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                            <div class="empty-title">Log update tidak ditemukan</div>
                            <div class="empty-sub">
                                Coba gunakan kata kunci lain
                            </div>
                        </div>
                    `,
                    emptyTable: `
                        <div class="empty-search">
                            <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                            <div class="empty-title">Belum ada data log update</div>
                            <div class="empty-sub">
                                Silakan tambahkan log update terlebih dahulu
                            </div>
                        </div>
                    `
                }
            });
            if ($('#stockTable_filter').parent('.table-action-wrapper').length === 0) {
                $('#stockTable_filter').wrap('<div class="table-action-wrapper"></div>');
            }
            $('#btnContainer').show().appendTo('.table-action-wrapper');
            },100);
        });
    }

    $(document).ready(function(){
        loadUpdateTable();
    });
</script>

<!-- Script Add -->
<script>
    // Add Modal
    $(document).on('click','#btnAddUpdate',function(){

        $('#addUpdateModal').modal('show');

        document.getElementById('addUpdateContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('update-add.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('addUpdateContent').innerHTML = html;
        });

    });

    // Add Details Update Description
    $(document).ready(function () {

        $(document).on("click", "#addDescription", function () {

            let html = `
                <div class="row description-row mb-3">

                    <div class="col-md-11">

                        <div class="input-group-modern">

                            <div class="input-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <input
                                type="text"
                                name="description[]"
                                class="form-control"
                                placeholder="Masukkan deskripsi update"
                                required>

                        </div>

                    </div>

                    <div class="col-md-1">

                        <button
                            type="button"
                            class="btn btn-danger w-100 removeDescription">

                            <i class="fas fa-trash"></i>

                        </button>

                    </div>

                </div>
            `;

            $("#descriptionContainer").append(html);

        });

        $(document).on("click", ".removeDescription", function () {

            if ($(".description-row").length <= 1) {
                return;
            }

            $(this).closest(".description-row").remove();

        });

    });

    // Add Action
    $(document).on('submit','#addUpdateForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch('update-action.php?action=store',{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast({
                    title:'Berhasil',
                    message:'Data berhasil ditambahkan',
                    type:'success'
                });

                $('#addUpdateModal').modal('hide');

                setTimeout(() => {
                    loadUpdateTable();
                }, 1000);

            }else{

                QToast({
                    title:'Gagal',
                    message:res.message || 'Terjadi kesalahan',
                    type:'error'
                });

            }

        })
        .catch(() => {
            QToast(
                'Error',
                'Gagal memproses tambah data',
                'error'
            );
        });
    });
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editUpdateBtn',function(){

        let id = $(this).data('id');

        $('#editUpdateModal').modal('show');

        document.getElementById('editUpdateContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('update-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editUpdateContent').innerHTML = html;
        });

    });

    // Add Details Update Description
    $(document).on("click", "#addDescriptionEdit", function () {

        $("#descriptionContainer").append(`
            <div class="row description-row mb-3">

                <div class="col-md-11">

                    <div class="input-group-modern">

                        <div class="input-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>

                        <input
                            type="text"
                            name="description[]"
                            class="form-control"
                            placeholder="Masukkan deskripsi update"
                            required>

                    </div>

                </div>

                <div class="col-md-1">

                    <button
                        type="button"
                        class="btn btn-danger w-100 removeDescription">

                        <i class="fas fa-trash"></i>

                    </button>

                </div>

            </div>
        `);

    });

    $(document).on("click", ".removeDescriptionEdit", function () {

        if ($(".description-row").length > 1) {
            $(this).closest(".description-row").remove();
        }

    });

    // Edit Action
    $(document).on('submit','#editUpdateForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('update-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast({
                    title:'Berhasil',
                    message:'Data berhasil diperbarui',
                    type:'success'
                });

                $('#editUpdateModal').modal('hide');

                setTimeout(() => {
                    loadUpdateTable();
                }, 1000);

            }else{

                QToast({
                    title:'Gagal',
                    message:res.message || 'Terjadi kesalahan',
                    type:'error'
                });

            }

        })
        .catch(() => {
            QToast(
                'Error',
                'Gagal memproses update',
                'error'
            );
        });
    });
</script>

<!-- Script Delete -->
<script>
    // Delete Action
    $(document).on('click','.deleteUpdateBtn',function(){

        let id = $(this).data('id');
        let name = $(this).data('name');
        let version = $(this).data('version');

        QConfirm('Hapus Log Update?', 'Update "' + name + ' v' + version + '" akan dihapus permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('update-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast({
                            title:'Terhapus',
                            message:'Data berhasil dihapus',
                            type:'success'
                        });

                        setTimeout(() => {
                            loadUpdateTable();
                        }, 1000);
                    }
                });
            }
        });
    });
</script>


<!-- Script Show Details -->
<script>
    $(document).on("click", ".showUpdateBtn", function () {

        let id = $(this).data("id");

        $.get("update-detail.php", { id: id }, function (res) {

            if (res.status != "success") {
                QToast("Error", res.message, "error");
                return;
            }

            $("#updateTitle").text(res.update_name);

            $("#updateVersion").html(`
                <span class="stock-badge">${res.update_version}</span>
            `);

            $("#updateType").html(res.badge);
            $("#updateDate").text(res.update_date);

            let html = "";

            res.details.forEach(function (item) {

                html += `
                    <div class="update-item">
                        <i class="fas fa-check-circle"></i>
                        <div>${item.description}</div>
                    </div>
                `;

            });

            $("#updateDetailList").html(html);

            $("#updateOverlay")
            .css("display","flex")
            .hide()
            .fadeIn(200);

        }, "json");

    });

    $(document).on("click", ".closeUpdate", function () {
        $("#updateOverlay").fadeOut(150);
    });

    $(document).on("click", "#updateOverlay", function (e) {

        if (e.target === this) {
            $(this).fadeOut(150);
        }

    });
</script>


</body>
</html>