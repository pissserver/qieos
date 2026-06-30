<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mutasi Stok - Qieos</title>
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

        .product-icon{
            width:50px;
            height:50px;
            border-radius:14px;
            background:linear-gradient(135deg,#334155,#0f172a);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
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

        #editStaffModal .modal-content{
            background:#fff !important;
            border-radius:16px !important;
            overflow:hidden;
            box-shadow:0 20px 40px rgba(15,23,42,.25);
        }

        #editStaffModal .stock-body{
            background:#fff !important;
        }

        #editStaffModal .modal-dialog{
            max-width:1200px;
        }

        #editStaffModal .btn-close{
            filter:brightness(0) invert(1);
            opacity:.85;
        }

        #editStaffModal .modal-content *{
            opacity:1 !important;
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
                                Staff Kasir
                            </div>
                            <div class="panel-subtitle">
                                Ubah informasi staff kasir atau hapus dari daftar staff kasir
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
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
                            WHERE role = 'staff kasir'
                            ORDER BY fullname ASC
                            ");
                            while($d=mysqli_fetch_assoc($q)): ?>

                            <tr class="stock-row"
                                onclick="loadDetail(<?= $d['id'] ?>)">

                                <td>
                                    <div class="product-wrap">

                                        <div class="product-icon">
                                            <i class="fas fa-user"></i>
                                        </div>

                                        <div>
                                            <div class="fw-bold">
                                                <?= htmlspecialchars($d['fullname']) ?>
                                            </div>

                                            <small class="text-muted">
                                                <?= htmlspecialchars($d['email']) ?>
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                <td class="text-center">

                                    <span class="stock-badge unit-badge text-capitalize">
                                        <i class="fas fa-id-badge me-1"></i>
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
                                    <button class="action-btn btn-edit editStaffBtn" data-id="<?= $d['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="action-btn btn-delete deletePurchaseBtn"
                                        data-id="<?= $d['id'] ?>"
                                        data-date="<?= $d['date_list'] ?>">
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

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editStaffModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fas fa-file-alt"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Edit Staff Kasir 
                            </div>
                            <div class="panel-subtitle">
                                Edit nama, email, dan password
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="editStaffContent"></div>
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
    });
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editStaffBtn',function(){

        let id = $(this).data('id');

        $('#editStaffModal').modal('show');

        document.getElementById('editStaffContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('cashier-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editStaffContent').innerHTML = html;
        });

    });

    // Edit Action
    $(document).on('submit','#editStaffForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('cashier-action.php?action=update&id='+id,{
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

                $('#editStaffModal').modal('hide');

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

</body>
</html>