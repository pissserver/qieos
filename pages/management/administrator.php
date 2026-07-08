<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Administrator - Qieos</title>
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

        .dev-badge{
            position:relative;
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:8px 16px;
            border-radius:12px;

            background:linear-gradient(
                135deg,
                #fff8dc 0%,
                #ffe082 35%,
                #ffd54f 60%,
                #ffca28 100%
            );

            color:#7c5700;
            border:1px solid #f4c430;
            font-weight:700;
            overflow:hidden;

            box-shadow:
                0 4px 12px rgba(255,193,7,.25),
                inset 0 1px 0 rgba(255,255,255,.6);

            transition:.25s ease;
        }

        .dev-badge:hover{
            transform:translateY(-2px);
            box-shadow:
                0 8px 20px rgba(255,193,7,.35),
                inset 0 1px 0 rgba(255,255,255,.8);
        }

        .dev-badge::before{
            content:'';
            position:absolute;
            top:0;
            left:-150%;
            width:60%;
            height:100%;

            background:linear-gradient(
                120deg,
                transparent,
                rgba(255,255,255,.7),
                transparent
            );

            transform:skewX(-25deg);
            animation:goldShine 2.8s infinite;
        }

        @keyframes goldShine{
            0%{
                left:-150%;
            }
            100%{
                left:180%;
            }
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

        #editAdministratorModal .modal-content, #addAdministratorModal .modal-content{
            background:#fff !important;
            border-radius:16px !important;
            overflow:hidden;
            box-shadow:0 20px 40px rgba(15,23,42,.25);
        }

        #editAdministratorModal .stock-body, #addAdministratorModal .stock-body{
            background:#fff !important;
        }

        #editAdministratorModal .modal-dialog, #addAdministratorModal .modal-dialog{
            max-width:1200px;
        }

        #editAdministratorModal .btn-close, #addAdministratorModal .btn-close{
            filter:brightness(0) invert(1);
            opacity:.85;
        }

        #editAdministratorModal .modal-content *, #addAdministratorModal .modal-content *{
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

        #btnAddAdministrator{
            height:42px;
            display:flex;
            align-items:center;
            white-space:nowrap;
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
                    <div>
                        <table class="table table-hover align-middle" id="stockTable">
                            <thead>
                                <tr style="font-size:13px;color:#64748b;">
                                    <th>Nama</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Terbuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php
                            $q = mysqli_query($conn,"
                            SELECT
                                *
                            FROM users
                            WHERE role IN ('administrator', 'developer') 
                            ORDER BY fullname ASC
                            ");
                            while($d=mysqli_fetch_assoc($q)): ?>

                            <tr class="stock-row">

                                <td>
                                    <div class="product-wrap">

                                        <?php if(!empty($d['photo'])): ?>
                                            <img class="avatar-photo"
                                                src="/qieos/assets/img/uploads/<?= htmlspecialchars($d['photo']) ?>"
                                                alt="<?= htmlspecialchars($d['fullname']) ?>">
                                        <?php else: ?>
                                            <div class="avatar">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div>
                                            <div class="fw-bold">
                                                <?= htmlspecialchars($d['fullname']) ?>
                                            </div>

                                            <small class="text-muted text-capitalize">
                                                <?= htmlspecialchars($d['username']) ?>
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                <td class="text-center">

                                    <span class="stock-badge <?php echo $d['role'] === 'developer' ? 'dev-badge' : 'unit-badge'; ?> text-capitalize">
                                        <?php if($d['role'] === 'developer'): ?>
                                            <i class="fas fa-crown me-1"></i>
                                        <?php else: ?>
                                            <i class="fas fa-user-shield me-1"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($d['role']) ?>
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

                                        $tgl = strtotime($d['created_at']);
                                        echo date('d', $tgl) . ' ' . $bulan[(int)date('n', $tgl)] . ' ' . date('Y', $tgl);
                                        ?>
                                    </span>

                                </td>

                                <td class="text-center">
                                    <?php if($d['role'] === 'developer'): ?>
                                        <span class="text-muted" style="font-size:13px;">
                                            <i class="fas fa-lock me-1"></i>
                                            Tidak dapat diubah
                                        </span>
                                    <?php elseif($d['username'] === $_SESSION['username']): ?>
                                        <span class="text-muted" style="font-size:13px;">
                                            <i class="fas fa-lock me-1"></i>
                                            Anda
                                        </span>
                                    <?php else: ?>
                                        <button class="action-btn btn-edit editAdministratorBtn" data-id="<?= $d['id'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="action-btn btn-delete deleteAdministratorBtn"
                                            data-id="<?= $d['id'] ?>"
                                            data-fullname="<?= $d['fullname'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
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
    $(document).ready(function(){
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

                Swal.fire({
                    icon:'success',
                    title:'Berhasil',
                    text:'Data berhasil ditambahkan',
                    showConfirmButton:false
                });

                $('#addAdministratorModal').modal('hide');

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

                Swal.fire({
                    icon:'success',
                    title:'Berhasil',
                    text:'Data berhasil diperbarui',
                    showConfirmButton:false
                });

                $('#editAdministratorModal').modal('hide');

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
    $(document).on('click','.deleteAdministratorBtn',function(){

        let id = $(this).data('id');
        let fullname = $(this).data('fullname');

        Swal.fire({
            title:'Hapus Administrator?',
            html:`
                <div style="text-align:center">
                    <small style="color:#94a3b8">Nama administrator:</small><br>
                    ${fullname}
                </div>
            `,
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal',
            confirmButtonColor:'#dc2626'
        }).then((result)=>{

            if(result.isConfirmed){

                fetch('administrator-action.php?action=destroy', {
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

</body>
</html>