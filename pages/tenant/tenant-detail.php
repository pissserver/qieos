<?php
    include '../../sessions/session.php';

    $id = $_GET['id'];
    $query = "SELECT * FROM tenants WHERE id = '$id' ORDER BY tenant_name ASC";
    $result = mysqli_query($conn, $query);
    $tenant = mysqli_fetch_assoc($result);

    $query2 = "SELECT * FROM tenants ORDER BY tenant_name ASC";
    $result2 = mysqli_query($conn, $query2);
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
            cursor:pointer;
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

        .detail-container{
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

        .payment-tabs{
            display:flex;
            gap:12px;
            background:#f4f6fb;
            padding:8px;
            border-radius:18px;
            margin:20px 0 25px;
        }

        .payment-tab{
            flex:1;
            border:none;
            background:transparent;
            padding:14px 20px;
            border-radius:14px;
            font-weight:700;
            color:#64748b;
            transition:.3s;
            cursor:pointer;
        }

        .payment-tab i{
            margin-right:8px;
        }

        .payment-tab:hover{
            background:#fff;
            color:#4338ca;
        }

        .payment-tab.active{

            background:linear-gradient(
                135deg,
                #5b4cf4,
                #4038d7
            );
            color:#fff;
            box-shadow:
                0 10px 25px rgba(91,76,244,.25);
        }

        .loading-box{
            height:280px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-direction:column;
            color:#94a3b8;
            font-size:15px;
        }

        .loading-box i{
            font-size:28px;
            margin-bottom:15px;
        }

        /* Tenant Dropdown */
        .tenant-dropdown{
            position:relative;
        }

        .tenant-toggle{
            display:flex;
            align-items:center;
            gap:10px;
            background:transparent;
            border:none;
            color:#fff;
            font-size:20px;
            font-weight:700;
            padding:0;
            cursor:pointer;
        }

        .tenant-toggle i{
            font-size:15px;
            transition:.3s;
        }

        .tenant-toggle.active i{
            transform:rotate(180deg);
        }

        .tenant-header{
            padding:14px 18px;
            margin-bottom:8px;

            border-radius:14px;

            background:linear-gradient(135deg,#4f46e5,#7c3aed);
            color:#fff;
        }

        .tenant-header small{
            display:block;
            opacity:.8;
            letter-spacing:1px;
            text-transform:uppercase;
            font-size:11px;
        }

        .tenant-header h6{
            margin:4px 0 0;
            font-size:18px;
            font-weight:700;
            color:#fff;
        }

        .tenant-menu{
            position:absolute;
            top:115%;
            left:-125px;
            min-width:340px;
            padding:10px;

            background:linear-gradient(160deg,#ffffff,#f8fbff);
            border:1px solid rgba(99,102,241,.15);
            border-radius:22px;

            box-shadow:
                0 30px 60px rgba(15,23,42,.18),
                0 10px 25px rgba(99,102,241,.12);

            backdrop-filter:blur(20px);

            z-index:9999;

            /* Animasi */
            opacity:0;
            visibility:hidden;
            pointer-events:none;

            transform:
                translateY(-10px)
                scale(.96);

            transform-origin:top center;

            transition:
                opacity .28s ease,
                transform .32s cubic-bezier(.22,1,.36,1),
                visibility .28s;
        }

        .tenant-menu.show{
            opacity:1;
            visibility:visible;
            pointer-events:auto;

            transform:
                translateY(0)
                scale(1);
        }

        .tenant-item{
            display:flex;
            align-items:center;
            justify-content:space-between;

            padding:14px 18px;
            margin-bottom:8px;

            border-radius:14px;

            color:#334155;
            text-decoration:none;
            font-weight:600;
            transition:.3s;
        }

        .tenant-item{
            opacity:0;
            transform:translateX(-15px);

            transition:
                opacity .35s ease,
                transform .35s ease,
                background .25s;
        }

        .tenant-menu.show .tenant-item{
            opacity:1;
            transform:translateX(0);
        }

        .tenant-menu.show .tenant-item:nth-child(2){
            transition-delay:.03s;
        }

        .tenant-menu.show .tenant-item:nth-child(3){
            transition-delay:.06s;
        }

        .tenant-menu.show .tenant-item:nth-child(4){
            transition-delay:.09s;
        }

        .tenant-menu.show .tenant-item:nth-child(5){
            transition-delay:.12s;
        }

        .tenant-menu.show .tenant-item:nth-child(6){
            transition-delay:.15s;
        }

        .tenant-menu.show .tenant-item:nth-child(7){
            transition-delay:.18s;
        }

        .tenant-menu.show .tenant-item:nth-child(8){
            transition-delay:.21s;
        }

        .tenant-item:last-child{
            margin-bottom:0;
        }

        .tenant-item span{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .tenant-item span i{
            width:42px;
            height:42px;

            display:flex;
            align-items:center;
            justify-content:center;

            border-radius:12px;

            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            color:#fff;
            font-size:16px;

            box-shadow:0 8px 18px rgba(99,102,241,.25);
        }

        .tenant-item:hover{
            transform:translateX(6px);

            background:linear-gradient(90deg,#eef4ff,#f7f1ff);
            color:#312e81;
        }

        .tenant-item:hover span i{
            transform:rotate(-10deg) scale(1.08);
        }

        .tenant-item.active{
            background:linear-gradient(135deg,#4f46e5,#7c3aed);
            color:#fff;

            box-shadow:
                0 12px 25px rgba(79,70,229,.35);
        }

        .tenant-item.active span i{
            background:rgba(255,255,255,.18);
            color:#fff;
            box-shadow:none;
        }

        .tenant-item .fa-check{
            width:34px;
            height:34px;

            display:flex;
            align-items:center;
            justify-content:center;

            border-radius:50%;

            background:rgba(255,255,255,.2);
            color:#fff;
        }

        .tenant-item:not(.active) .fa-check{
            color:#22c55e;
            background:#dcfce7;
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
                            <i class="fas fa-store"></i>
                        </div>

                        <div>
                            <div class="tenant-dropdown">
                                <button
                                    type="button"
                                    class="tenant-toggle"
                                    id="tenantToggle">
                                    <span><?= htmlspecialchars(strtoupper($tenant['tenant_name'])) ?></span>
                                    <i class="fas fa-chevron-down" id="tenantArrow"></i>
                                </button>

                                <div class="tenant-menu" id="tenantMenu">
                                    <div class="tenant-header">
                                        <small>Pilih Tenant</small>
                                        <h6>Daftar Tenant</h6>
                                    </div>

                                    <?php while($allTenant=mysqli_fetch_assoc($result2)): ?>
                                        <a
                                            href="?id=<?= $allTenant['id'] ?>"
                                            class="tenant-item <?= $allTenant['id']==$tenant['id'] ? 'active' : '' ?>">

                                            <span>
                                                <i class="fas fa-store"></i>
                                                <?= htmlspecialchars(strtoupper($allTenant['tenant_name'])) ?>
                                            </span>

                                            <?php if($allTenant['id']==$tenant['id']){ ?>
                                                <i class="fas fa-check"></i>
                                            <?php } ?>
                                        </a>
                                    <?php endwhile; ?>
                                </div>
                            </div>

                            <div class="panel-subtitle">
                                <?= $tenant['tenant_owner'] ?> | <?= ucwords(strtolower($tenant['status'])) ?> &nbsp;<i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="detail-container mb-5">

                <!-- <div class="fifo-title">
                    <i class="fas fa-wallet text-primary"></i>
                    <h5 class="mb-0">Detail Pembayaran</h5>
                </div> -->

                <div class="payment-tabs">

                    <button
                        class="payment-tab active"
                        onclick="switchPaymentTab('tenant', this)">
                        <i class="fas fa-store"></i>
                        Pembayaran Tenant
                    </button>

                    <button
                        class="payment-tab"
                        onclick="switchPaymentTab('utility', this)">
                        <i class="fas fa-bolt"></i>
                        Air & Listrik
                    </button>

                </div>

                <div id="payment-content">

                    <div class="loading-box">
                        <i class="fas fa-spinner fa-spin"></i>
                        Memuat data...
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fas fa-user-plus"></i>
                        </div>

                        <div>
                            <div id="addPaymentTitle" class="panel-title">

                            </div>
                            <div class="panel-subtitle">
                                Tambah pembayaran baru untuk tenant ini
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="addPaymentContent"></div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fas fa-money-bill-wave"></i>
                        </div>

                        <div>
                            <div id="editPaymentTitle" class="panel-title">
                                
                            </div>
                            <div class="panel-subtitle">
                                Edit nama tenant dan tanggal pembayaran
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="editPaymentContent"></div>
            </div>
        </div>
    </div>
</main>

<?php include '../../script/footscript.php'; ?>

<!-- Tenant Dropdown -->
<script>
    const tenantToggle=document.getElementById("tenantToggle");
    const tenantMenu=document.getElementById("tenantMenu");
    const tenantArrow=document.getElementById("tenantArrow");

    tenantToggle.addEventListener("click",function(e){

        e.stopPropagation();

        tenantMenu.classList.toggle("show");

        tenantToggle.classList.toggle("active");

    });

    document.addEventListener("click",function(){

        tenantMenu.classList.remove("show");

        tenantToggle.classList.remove("active");

    });
</script>

<!-- Switch Payment Tab -->
<script>
    document.addEventListener("DOMContentLoaded", function(){
        loadPayment("tenant");
    });

    function switchPaymentTab(type, el){
        document.querySelectorAll(".payment-tab").forEach(btn=>{
            btn.classList.remove("active");
        });

        el.classList.add("active");

        loadPayment(type);
    }

    function loadPayment(type){
        document.getElementById("payment-content").innerHTML=`
            <div class="loading-box">
                <i class="fas fa-spinner fa-spin"></i>
                Memuat data...
            </div>
        `;

        fetch("tenant-detail-table.php?type="+type+"&tenant=<?= $tenant['id']; ?>")
        .then(res=>res.text())
        .then(html=>{

            document.getElementById("payment-content").innerHTML=html;

            if($.fn.DataTable.isDataTable("#tablePayment")){
                $("#tablePayment").DataTable().destroy();
            }

            $("#tablePayment").DataTable({
                pageLength:10,
                responsive:true,
                ordering:false,
                autoWidth:false,

                language:{
                    search:"",
                    searchPlaceholder:"Cari pembayaran..."
                }
            });
        });
    }
</script>

<!-- Script Add -->
<script>
    // OPEN ADD MODAL
    $(document).on('click','.addPaymentBtn',function(){

        let type = $(this).data('type');
        let tenant_id = $(this).data('tenant-id');

        $('#addPaymentModal').modal('show');

        const paymentType = type === 'tenant' ? 'Pembayaran Tenant' : 'Pembayaran Air & Listrik';
        $('#addPaymentTitle').text('Tambah ' + paymentType);

        document.getElementById('addPaymentContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('tenant-payment-add.php?type=' + type + '&tenant_id=' + tenant_id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('addPaymentContent').innerHTML = html;
        });

    });

    // Add Action
    $(document).on('submit','#addPaymentForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let tenant_id = formData.get('tenant_id');
        let type = formData.get('type');

        fetch('tenant-payment-action.php?action=store',{
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

                $('#addPaymentModal').modal('hide');

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

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editPaymentBtn',function(){

        let id = $(this).data('id');
        let type = $(this).data('type');

        $('#editPaymentModal').modal('show');

        const paymentType = type === 'tenant' ? 'Pembayaran Tenant' : 'Pembayaran Air & Listrik';
        $('#editPaymentTitle').text('Edit ' + paymentType);

        document.getElementById('editPaymentContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('tenant-payment-edit.php?id=' + id + '&type=' + type)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editPaymentContent').innerHTML = html;
        });

    });

    // Edit Action
    $(document).on('submit','#editPaymentForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');
        let type = formData.get('type');

        fetch('tenant-payment-action.php?action=update',{
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

                $('#editPaymentModal').modal('hide');

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
    $(document).on('click','.deletePaymentBtn',function(){

        let id = $(this).data('id');
        let date = $(this).data('date');
        let type = $(this).data('type');

        if(type==="tenant"){
            typeName = "Pembayaran Tenant";
        }else if(type==="utility"){
            typeName = "Pembayaran Air & Listrik";
        }

        const formattedDate = new Date(date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });

        Swal.fire({
            title:'Hapus Pembayaran?',
            html:`
                <div style="text-align:center">
                    <small style="color:#94a3b8">${typeName}:</small><br>
                    ${formattedDate}
                </div>
            `,
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal',
            confirmButtonColor:'#dc2626'
        }).then((result)=>{

            if(result.isConfirmed){

                fetch('tenant-payment-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id, type: type })
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