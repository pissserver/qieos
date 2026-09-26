<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Master User - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-user.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/master-user.css'); ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">
    <div class="row">
        <div class="col-md-12">

            <!-- Main Panel -->
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Master User
                            </div>
                            <div class="panel-subtitle">
                                Kelola administrator dan staff kasir dalam satu panel
                            </div>
                        </div>
                    </div>

                    <div class="panel-header-right">
                        <div class="mu-hero-chip">
                            <i class="fas fa-shield-halved"></i>
                            <span>Terproteksi</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <!-- TABS + ADD BUTTON -->
                    <div class="mu-toolbar">
                        <div class="mu-tabs" id="userTabs">
                            <span class="mu-tab-indicator" id="tabIndicator"></span>

                            <button type="button" class="mu-tab active" data-role="administrator">
                                <span class="mu-tab-icon"><i class="fas fa-user-shield"></i></span>
                                <span>Administrator</span>
                                <span class="mu-count" id="countAdmin">0</span>
                            </button>

                            <button type="button" class="mu-tab" data-role="cashier">
                                <span class="mu-tab-icon"><i class="fas fa-user-tie"></i></span>
                                <span>Staff Kasir</span>
                                <span class="mu-count" id="countCashier">0</span>
                            </button>
                        </div>

                        <button type="button" class="btn mu-add-btn" id="btnAddUser">
                            <i class="fas fa-user-plus me-2"></i>
                            Tambah User
                        </button>
                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive-wrap" id="userTableContainer">
                        <!-- Loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content stock-panel border-0">

            <div class="panel-header panel-dark my-3 mx-3">
                <div class="panel-left">
                    <div class="panel-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>

                    <div>
                        <div class="panel-title">
                            Tambah User
                        </div>
                        <div class="panel-subtitle">
                            Pilih role, lalu lengkapi informasi user
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="mt-2 px-5" id="addUserContent"></div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content stock-panel border-0">

            <div class="panel-header panel-dark my-3 mx-3">
                <div class="panel-left">
                    <div class="panel-icon">
                        <i class="fas fa-user-pen"></i>
                    </div>

                    <div>
                        <div class="panel-title">
                            Edit User
                        </div>
                        <div class="panel-subtitle">
                            Ubah role atau informasi user
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="mt-2 px-5" id="editUserContent"></div>
        </div>
    </div>
</div>

</main>

<?php include '../../script/footscript.php'; ?>

<script>
    let activeRole = 'administrator';

    // ----- TAB INDICATOR -----
    function positionIndicator(){
        const tabs = document.querySelectorAll('.mu-tab');
        const indicator = document.getElementById('tabIndicator');
        if(!indicator || !tabs.length) return;

        const active = document.querySelector('.mu-tab.active');
        if(!active) return;

        indicator.style.left = active.offsetLeft + 'px';
        indicator.style.width = active.offsetWidth + 'px';
    }

    window.addEventListener('load', positionIndicator);
    window.addEventListener('resize', positionIndicator);

    // ----- LOAD TABLE -----
    function loadUserTable(role){
        activeRole = role;

        fetch('master-user-table.php?role=' + encodeURIComponent(role))
        .then(res => res.text())
        .then(html => {
            const container = document.getElementById('userTableContainer');
            container.innerHTML = html;

            // Update count badges
            const table = document.getElementById('userTable');
            if(table){
                const ca = table.getAttribute('data-count-admin');
                const cc = table.getAttribute('data-count-cashier');
                if(ca !== null) document.getElementById('countAdmin').textContent = ca;
                if(cc !== null) document.getElementById('countCashier').textContent = cc;
            }

            // Destroy old DataTable
            if($.fn.DataTable.isDataTable('#userTable')){
                $('#userTable').DataTable().destroy();
            }

            const isCashier = role === 'cashier';

            setTimeout(()=>{
                $('#userTable').DataTable({
                    pageLength: 5,
                    lengthMenu: [[5,10,25,50],[5,10,25,50]],
                    responsive: true,
                    autoWidth: false,
                    language:{
                        search:"",
                        searchPlaceholder: isCashier ? "Cari staff kasir..." : "Cari administrator...",

                        zeroRecords: `
                            <div class="empty-search">
                                <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                <div class="empty-title">${isCashier ? 'Staff kasir' : 'Administrator'} tidak ditemukan</div>
                                <div class="empty-sub">
                                    Coba gunakan kata kunci lain
                                </div>
                            </div>
                        `,

                        emptyTable: `
                            <div class="empty-search">
                                <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                <div class="empty-title">Belum ada data ${isCashier ? 'staff kasir' : 'administrator'}</div>
                                <div class="empty-sub">
                                    Silakan tambahkan user terlebih dahulu
                                </div>
                            </div>
                        `
                    }
                });

                // Buat wrapper untuk search + button
                $('#userTable_filter')
                    .wrap('<div class="table-action-wrapper"></div>');
            },100);
        });
    }

    // ----- TABS -----
    $(document).on('click','.mu-tab',function(){
        if($(this).hasClass('active')) return;

        $('.mu-tab').removeClass('active');
        $(this).addClass('active');

        positionIndicator();
        loadUserTable($(this).data('role'));
    });

    // ----- ADD -----
    $(document).on('click','#btnAddUser',function(){
        $('#addUserModal').modal('show');

        document.getElementById('addUserContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('master-user-add.php?role=' + encodeURIComponent(activeRole))
        .then(res => res.text())
        .then(html => {
            document.getElementById('addUserContent').innerHTML = html;
        });
    });

    $(document).on('submit','#addUserForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch('master-user-action.php?action=store',{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil ditambahkan', 'success');

                $('#addUserModal').modal('hide');

                // Switch ke tab sesuai role yang dipilih
                const role = res.role === 'staff kasir' ? 'cashier' : 'administrator';
                $('.mu-tab').removeClass('active');
                $('.mu-tab[data-role="'+role+'"]').addClass('active');
                positionIndicator();
                loadUserTable(role);

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

            }
        })
        .catch(() => {
            QToast('Error', 'Gagal memproses tambah data', 'error');
        });
    });

    // ----- EDIT -----
    $(document).on('click','.editUserBtn',function(){
        let id = $(this).data('id');

        $('#editUserModal').modal('show');

        document.getElementById('editUserContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('master-user-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editUserContent').innerHTML = html;
        });
    });

    $(document).on('submit','#editUserForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('master-user-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil diperbarui', 'success');

                $('#editUserModal').modal('hide');

                const role = res.role === 'staff kasir' ? 'cashier' : 'administrator';
                $('.mu-tab').removeClass('active');
                $('.mu-tab[data-role="'+role+'"]').addClass('active');
                positionIndicator();
                loadUserTable(role);

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

            }
        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
        });
    });

    // ----- DELETE -----
    $(document).on('click','.deleteUserBtn',function(){
        let id = $(this).data('id');
        let fullname = $(this).data('fullname');

        QConfirm('Hapus User?', 'Data ' + fullname + ' akan dihapus secara permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('master-user-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        loadUserTable(activeRole);
                    }else{
                        QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });

    // ----- INIT -----
    $(document).ready(function(){
        loadUserTable('administrator');
    });
</script>

</body>
</html>