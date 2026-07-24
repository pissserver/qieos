<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Update - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <style>
        :root{
            --primary:#4f46e5;
            --dark:#0f172a;
            --muted:#64748b;
            --border:#e2e8f0;
        }

        /* HEADER */

        .stock-header{
            background:linear-gradient(135deg,#334155,#0f172a);
            color:#fff;
            border-radius:24px;
            padding:30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:25px;
            box-shadow:0 12px 30px rgba(15,23,42,.15);
        }

        .stock-header h3{
            color:#fff;
            font-weight:800;
            margin-top:5px;
        }

        .stock-header p{
            margin:0;
            opacity:.85;
        }

        .header-icon{
            width:90px;
            height:90px;
            border-radius:22px;
            background:rgba(255,255,255,.08);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:36px;
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

        /* TABLE */

        #stockTable{
            border-collapse:separate;
            border-spacing:0 14px;
        }

        #stockTable thead th{
            border:none !important;
            color:#64748b;
            font-size:12px;
            text-transform:uppercase;
        }

        .stock-row{
            background:#fff;
            box-shadow:0 8px 18px rgba(15,23,42,.05);
            transition:.25s;
            border-radius:14px;
        }

        .stock-row:hover{
            transform:translateY(-2px);
            box-shadow:0 14px 28px rgba(15,23,42,.10);
        }

        .stock-row td:first-child{
            border-radius:14px 0 0 14px;
        }

        .stock-row td:last-child{
            border-radius:0 14px 14px 0;
        }

        #stockTable tbody td{
            border:none !important;
            padding:18px;
            vertical-align:middle;
        }

        .product-wrap{
            display:flex;
            align-items:center;
            gap:15px;
        }

        .avatar{
            width:50px;
            height:50px;
            border-radius:14px;
            background:linear-gradient(135deg,#334155,#0f172a);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .avatar-photo{
            width:50px;
            height:50px;
            border-radius:14px;
            object-fit:cover;
            display:block;
        }

        .stock-badge{
            padding:8px 14px;
            border-radius:10px;
            font-weight:700;
        }

        .stock-success{
            background:#dcfce7;
            color:#166534;
        }

        .stock-danger{
            background:#fee2e2;
            color:#991b1b;
        }

        .stock-empty{
            background:#e2e8f0;
            color:#475569;
        }

        .unit-badge{
            padding:8px 14px;
            border-radius:10px;
            background:#f8fafc;
            border:1px solid #e2e8f0;
            font-weight:600;
        }

        .table-card{
            background:#fff;
            border-radius:20px;
            padding:20px;
            box-shadow:0 8px 20px rgba(15,23,42,.05);
        }

        /* ACTION */
        .action-btn{
            width:38px;
            height:38px;
            border:none;
            border-radius:10px;
            margin:0 4px;
            color:#fff;
            transition:.25s;
        }

        .btn-show{
            background: #0284c7;
        }
        
        .btn-edit{
            background:#f59e0b;
        }

        .btn-delete{
            background:#ef4444;
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

        /* FIFO CONTAINER */

        .fifo-container{
            background:#fff;
            border-radius:20px;
            padding:25px;
            box-shadow:0 8px 20px rgba(15,23,42,.05);
        }

        .fifo-title{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:15px;
        }

        /* Modal Edit */
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

        .modal-backdrop.show{
            opacity:.55 !important;
        }

        #editAdministratorModal .modal-content, #addUpdateModal .modal-content{
            background:#fff !important;
            border-radius:16px !important;
            overflow:hidden;
            box-shadow:0 20px 40px rgba(15,23,42,.25);
        }

        #editAdministratorModal .stock-body, #addUpdateModal .stock-body{
            background:#fff !important;
        }

        #editAdministratorModal .modal-dialog, #addUpdateModal .modal-dialog{
            max-width:1200px;
        }

        #editAdministratorModal .btn-close, #addUpdateModal .btn-close{
            filter:brightness(0) invert(1);
            opacity:.85;
        }

        #editAdministratorModal .modal-content *, #addUpdateModal .modal-content *{
            opacity:1 !important;
        }

        .table-action-wrapper{
            display:flex;
            justify-content:flex-end;
            align-items:center;
            gap:12px;
            width:100%;
        }

        .table-action-wrapper #stockTable_filter{
            margin:0;
        }

        .table-action-wrapper #stockTable_filter label{
            margin:0;
        }

        .table-action-wrapper #stockTable_filter input{
            height:42px;
        }

        #btnAddUpdate{
            height:42px;
            display:flex;
            align-items:center;
            white-space:nowrap;
        }

        /* Show Details */
        .update-overlay{
            position:fixed;
            inset:0;

            display:none;

            justify-content:center;
            align-items:center;

            background:rgba(7,15,30,.45);
            backdrop-filter:blur(10px);

            z-index:999999;
        }

        .update-modal{

            width:650px;
            max-width:92%;

            height:700px; 
            max-height:90vh;

            background:#fff;

            border-radius:24px;

            overflow:hidden;

            box-shadow:
                0 30px 80px rgba(0,0,0,.30);

            animation:showUpdate .35s ease;
        }

        .update-header{

            padding:35px;

            text-align:center;

            color:white;

            background:
                linear-gradient(135deg,#4338ca,#7c3aed);

            position:relative;

        }

        .update-icon{

            width:72px;
            height:72px;

            margin:auto;

            border-radius:50%;

            display:flex;
            justify-content:center;
            align-items:center;

            font-size:34px;

            background:rgba(255,255,255,.18);

            margin-bottom:15px;

        }

        .update-header h4{

            font-size:28px;

            font-weight:700;

            margin-bottom:15px;

        }

        .update-meta{

            display:flex;

            justify-content:center;

            gap:12px;

            flex-wrap:wrap;

        }

        .update-meta span{

            padding:8px 16px;

            border-radius:999px;

            font-weight:600;

            font-size:13px;

        }

        #updateTitle {
            color: #fff;
        }

        .update-body{

            padding:30px;

            max-height:60vh;

            overflow:auto;

        }

        .update-item{

            display:flex;

            gap:18px;

            align-items:flex-start;

            padding:15px 18px;

            border-radius:14px;

            background:#f8fafc;

            margin-bottom:12px;

            transition:.25s;

        }

        .update-item:hover{

            transform:translateX(5px);

            background:#eef4ff;

        }

        .update-item:last-child{

            border:none;

        }

        .update-item i{

            color:#10b981;

            font-size:20px;

            margin-top:2px;

        }

        .closeUpdate{

            position:absolute;

            right:20px;
            top:20px;

            width:42px;
            height:42px;

            border:none;

            border-radius:50%;

            background:rgba(255,255,255,.18);

            color:white;

            transition:.25s;

        }

        .closeUpdate:hover{

            background:white;

            color:#4f46e5;

            transform:rotate(90deg);

        }

        @keyframes showUpdate{

            0%{

                opacity:0;

                transform:scale(.85);

            }

            100%{

                opacity:1;

                transform:scale(1);

            }

        }
    </style>
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
                    <div id="btnContainer" style="display:none;">
                        <button
                            type="button"
                            class="btn btn-primary"
                            id="btnAddUpdate">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Log Update
                        </button>
                    </div>
                    
                    <!-- TABLE -->
                    <div>
                        <table class="table table-hover align-middle" id="stockTable">
                            <thead>
                                <tr style="font-size:13px;color:#64748b;">
                                    <th>Nama Update</th>
                                    <th class="text-center">Version</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Tanggal Update</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php
                            $q = mysqli_query($conn,"
                            SELECT
                                *
                            FROM updates
                            WHERE deleted_at IS NULL
                            ORDER BY update_date DESC
                            ");
                            while($d=mysqli_fetch_assoc($q)): ?>

                            <tr class="stock-row">

                                <td>
                                    <div class="product-wrap">

                                        <div class="avatar">
                                            <i class="fas fa-rocket"></i>
                                        </div>

                                        <div>
                                            <div class="fw-bold">
                                                <?= htmlspecialchars($d['update_name']) ?>
                                            </div>

                                            <small class="text-muted text-capitalize">
                                                Update Log <?=htmlspecialchars($d['update_type']); ?>
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                <td class="text-center">

                                    <span class="stock-badge 
                                        <?= $d['update_type'] === 'major' ? 'stock-danger' : ($d['update_type'] === 'minor' ? 'stock-success' : 'unit-badge'); ?>
                                        text-capitalize">

                                        <i class="fas fa-code-branch me-1"></i>
                                        <?= htmlspecialchars($d['update_version']) ?>

                                    </span>

                                </td>

                                <td class="text-center">

                                    <span class="stock-badge 
                                        <?= $d['update_type'] === 'major' ? 'stock-danger' : ($d['update_type'] === 'minor' ? 'stock-success' : 'unit-badge'); ?>
                                        text-capitalize">

                                        <i class="fas fa-layer-group me-1"></i>
                                        <?= htmlspecialchars($d['update_type']) ?>
                                    </span>

                                </td>

                                <td class="text-center">

                                    <span class="unit-badge">
                                        <i class="fas fa-cubes me-1"></i>
                                        <?php
                                        $bulan = [
                                            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                                            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
                                        ];

                                        $tgl = strtotime($d['update_date']);
                                        echo date('d', $tgl) . ' ' . $bulan[(int)date('n', $tgl)] . ' ' . date('Y', $tgl) . ' | ' . date('h:i', $tgl);
                                        ?>
                                    </span>

                                </td>

                                <td class="text-center">
                                    <button class="action-btn btn-show showUpdateBtn" data-id="<?= $d['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button class="action-btn btn-edit editUpdateBtn" data-id="<?= $d['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="action-btn btn-delete deleteUpdateBtn"
                                        data-id="<?= $d['id'] ?>"
                                        data-name="<?= $d['update_name'] ?>"
                                        data-version="<?= $d['update_version'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <?php endwhile; ?>

                            </tbody>
                        </table>
                    </div>
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
    $(document).ready(function(){
        $('#stockTable').DataTable({
            pageLength: 5,
            lengthMenu:[[5,10,25,50],[5,10,25,50]],
            responsive: true,
            autoWidth: false,
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

        // Buat wrapper untuk search + button
        $('#stockTable_filter')
            .wrap('<div class="table-action-wrapper"></div>');

        // Pindahkan tombol ke wrapper
        $('#btnContainer')
            .show()
            .appendTo('.table-action-wrapper');
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

                Swal.fire({
                    icon:'success',
                    title:'Berhasil',
                    text:'Data berhasil ditambahkan',
                    showConfirmButton:false
                });

                $('#addUpdateModal').modal('hide');

                setTimeout(() => {
                    location.reload();
                }, 1000);

            }else{

                Swal.fire({
                    icon:'error',
                    title:'Gagal',
                    text:res.message || 'Terjadi kesalahan'
                });

            }

        })
        .catch(() => {
            Swal.fire(
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

                Swal.fire({
                    icon:'success',
                    title:'Berhasil',
                    text:'Data berhasil diperbarui',
                    showConfirmButton:false
                });

                $('#editUpdateModal').modal('hide');

                setTimeout(() => {
                    location.reload();
                }, 1000);

            }else{

                Swal.fire({
                    icon:'error',
                    title:'Gagal',
                    text:res.message || 'Terjadi kesalahan'
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
</script>

<!-- Script Delete -->
<script>
    // Delete Action
    $(document).on('click','.deleteUpdateBtn',function(){

        let id = $(this).data('id');
        let name = $(this).data('name');
        let version = $(this).data('version');

        Swal.fire({
            title:'Hapus Update Log?',
            html:`
                <div style="text-align:center">
                    <small style="color:#94a3b8">Update Log:</small><br>
                    ${name} - ${version}
                </div>
            `,
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal',
            confirmButtonColor:'#dc2626'
        }).then((result)=>{

            if(result.isConfirmed){

                fetch('update-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
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

                        setTimeout(() => {
                            location.reload();
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
                Swal.fire("Error", res.message, "error");
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