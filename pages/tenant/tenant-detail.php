<?php
    include '../../sessions/session.php';

    $id = $_GET['id'];
    $query = "SELECT * FROM tenants WHERE id = '$id' ORDER BY tenant_name ASC";
    $result = mysqli_query($conn, $query);
    $tenant = mysqli_fetch_assoc($result);

    $query2 = "SELECT * FROM tenants WHERE status = 'active' ORDER BY tenant_name ASC";
    $result2 = mysqli_query($conn, $query2);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Detail Tenant - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/tenant-detail.css">

    <style>
        /* ===== PANEL ACTION BUTTONS ===== */
        .panel-right{
            display:flex;align-items:center;gap:10px;flex-shrink:0;
        }
        .tenant-act-btn{
            display:inline-flex;align-items:center;gap:8px;
            padding:10px 18px;border:none;border-radius:12px;
            font-size:13px;font-weight:700;cursor:pointer;
            transition:all 0.25s cubic-bezier(0.4,0,0.2,1);
        }
        .tenant-act-btn i{font-size:14px;transition:transform 0.25s}
        .tenant-act-edit{
            background:rgba(255,255,255,.15);color:#fff;
            backdrop-filter:blur(4px);
        }
        .tenant-act-edit:hover{
            background:rgba(255,255,255,.28);
            transform:translateY(-2px);
            box-shadow:0 6px 20px rgba(99,102,241,.3);
        }
        .tenant-act-edit:hover i{transform:rotate(-12deg) scale(1.1)}
        .tenant-act-delete{
            background:rgba(239,68,68,.85);color:#fff;
            backdrop-filter:blur(4px);
        }
        .tenant-act-delete:hover{
            background:#ef4444;color:#fff;
            transform:translateY(-2px);
            box-shadow:0 6px 20px rgba(239,68,68,.4);
        }
        .tenant-act-delete:hover i{transform:rotate(-12deg) scale(1.1)}

        /* ===== EDIT TENANT MODAL ===== */
        .etd-modal-header{
            display:flex;align-items:center;justify-content:space-between;
            padding:20px 24px;
            background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);
            position:relative;overflow:hidden;
        }
        .etd-modal-header::after{
            content:'';position:absolute;top:-50%;right:-5%;
            width:160px;height:160px;
            background:radial-gradient(circle,rgba(99,102,241,.18) 0%,transparent 70%);
            pointer-events:none;
        }
        .etd-modal-header-left{
            display:flex;align-items:center;gap:14px;position:relative;z-index:2;
        }
        .etd-modal-icon{
            width:44px;height:44px;border-radius:12px;
            display:flex;align-items:center;justify-content:center;
            font-size:18px;color:#fff;flex-shrink:0;
        }
        .etd-modal-icon-edit{
            background:linear-gradient(135deg,#f59e0b,#d97706);
            box-shadow:0 6px 16px rgba(245,158,11,.4);
        }
        .etd-modal-title{color:#fff;font-size:17px;font-weight:700;margin:0 0 2px}
        .etd-modal-subtitle{color:#94a3b8;font-size:12px;margin:0}
        .etd-modal-close{
            width:36px;height:36px;border-radius:10px;border:none;
            background:rgba(255,255,255,.1);color:#94a3b8;font-size:16px;
            display:flex;align-items:center;justify-content:center;
            cursor:pointer;transition:all 0.2s;position:relative;z-index:2;
        }
        .etd-modal-close:hover{background:rgba(239,68,68,.25);color:#fca5a5}

        .etd-modal-body{padding:24px;background:#fff}

        .etd-form-group{margin-bottom:18px}
        .etd-label{
            display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:8px;
        }
        .etd-label i{color:#6366f1;margin-right:4px;font-size:12px}

        .etd-input-wrap{position:relative;display:flex;align-items:center}
        .etd-input-icon{
            position:absolute;left:16px;color:#94a3b8;font-size:14px;
            transition:color 0.2s;z-index:5;pointer-events:none;
        }
        .etd-input{
            width:100%;height:48px;padding:0 16px 0 44px;
            font-size:14px;font-weight:500;color:#0f172a;
            background:#f8fafc;border:1.5px solid #e2e8f0;
            border-radius:12px;outline:none;
            transition:all 0.25s cubic-bezier(0.4,0,0.2,1);
        }
        .etd-input::placeholder{color:#94a3b8;font-weight:400}
        .etd-input:focus{
            background:#fff;border-color:#f59e0b;
            box-shadow:0 0 0 4px rgba(245,158,11,.12);
        }
        .etd-input-wrap:focus-within .etd-input-icon{color:#f59e0b}

        .etd-form-footer{
            display:flex;justify-content:flex-end;gap:10px;
            margin-top:24px;padding-top:18px;border-top:1px solid #f1f5f9;
        }
        .etd-btn{
            display:inline-flex;align-items:center;gap:8px;
            padding:10px 22px;border:none;border-radius:12px;
            font-size:14px;font-weight:600;cursor:pointer;
            transition:all 0.25s;
        }
        .etd-btn-cancel{
            background:#f1f5f9;color:#64748b;
        }
        .etd-btn-cancel:hover{background:#e2e8f0;color:#334155}
        .etd-btn-save{
            background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;
            box-shadow:0 4px 14px rgba(245,158,11,.35);
        }
        .etd-btn-save:hover{
            background:linear-gradient(135deg,#d97706,#b45309);
            transform:translateY(-1px);
            box-shadow:0 6px 20px rgba(245,158,11,.45);
        }

        /* ===== RESPONSIVE ===== */
        @media(max-width:991px){
            .panel-header{
                flex-direction:column;align-items:flex-start;gap:14px;
                padding:16px 18px;
            }
            .panel-right{
                width:100%;justify-content:flex-end;
            }
            .tenant-act-btn span{display:inline}
        }
        @media(max-width:575px){
            .panel-right{gap:6px}
            .tenant-act-btn{
                padding:8px 12px;font-size:11px;border-radius:10px;
            }
            .tenant-act-btn span{display:none}
            .tenant-act-btn i{font-size:15px}
            .etd-modal-body{padding:18px}
            .etd-input{height:44px;font-size:13px;padding-left:40px;border-radius:10px}
            .etd-input-icon{left:12px;font-size:13px}
            .etd-btn{padding:9px 16px;font-size:13px;border-radius:10px}
            .etd-form-footer{flex-wrap:wrap}
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

                    <!-- ACTION BUTTONS -->
                    <div class="panel-right">
                        <button
                            type="button"
                            class="tenant-act-btn tenant-act-edit"
                            data-id="<?= $tenant['id'] ?>"
                            data-name="<?= htmlspecialchars($tenant['tenant_name']) ?>"
                            data-owner="<?= htmlspecialchars($tenant['tenant_owner']) ?>"
                            id="btnEditTenant">
                            <i class="fas fa-pen-to-square"></i>
                            <span>Edit</span>
                        </button>

                        <button
                            type="button"
                            class="tenant-act-btn tenant-act-delete"
                            data-id="<?= $tenant['id'] ?>"
                            data-name="<?= htmlspecialchars($tenant['tenant_name']) ?>"
                            id="btnDeleteTenant">
                            <i class="fas fa-trash-can"></i>
                            <span>Hapus</span>
                        </button>
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

    <!-- EDIT TENANT MODAL -->
    <div class="modal fade" id="editTenantModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:20px;overflow:hidden;box-shadow:0 25px 60px rgba(15,23,42,.25)">

                <div class="etd-modal-header">
                    <div class="etd-modal-header-left">
                        <div class="etd-modal-icon etd-modal-icon-edit">
                            <i class="fas fa-pen-to-square"></i>
                        </div>
                        <div>
                            <div class="etd-modal-title">Edit Tenant</div>
                            <div class="etd-modal-subtitle">Perbarui informasi tenant</div>
                        </div>
                    </div>
                    <button class="etd-modal-close" data-bs-dismiss="modal">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <div class="etd-modal-body">
                    <form id="editTenantForm">
                        <input type="hidden" name="id" id="editTenantId" value="">

                        <div class="etd-form-group">
                            <label class="etd-label">
                                <i class="fas fa-store"></i> Nama Tenant
                            </label>
                            <div class="etd-input-wrap">
                                <span class="etd-input-icon"><i class="fas fa-shop"></i></span>
                                <input
                                    type="text"
                                    name="tenant_name"
                                    id="editTenantName"
                                    class="etd-input"
                                    placeholder="Masukkan nama tenant"
                                    required>
                            </div>
                        </div>

                        <div class="etd-form-group">
                            <label class="etd-label">
                                <i class="fas fa-user-tie"></i> Nama Pemilik
                            </label>
                            <div class="etd-input-wrap">
                                <span class="etd-input-icon"><i class="fas fa-user"></i></span>
                                <input
                                    type="text"
                                    name="tenant_owner"
                                    id="editTenantOwner"
                                    class="etd-input"
                                    placeholder="Masukkan nama pemilik"
                                    required>
                            </div>
                        </div>

                        <div class="etd-form-footer">
                            <button type="button" class="etd-btn etd-btn-cancel" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="etd-btn etd-btn-save">
                                <i class="fas fa-check-circle"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
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

                const receiptUrl = `../receipt-tenant.php?payment_id=${res.payment_id}&type=${res.type}`;
                    window.open(receiptUrl, '_blank');

                if (navigator.share) {
                    navigator.share({
                        title: 'Struk Pembayaran',
                        text: 'Berikut struk pembayaran' + type,
                        url: receiptUrl
                    });
                }

                QToast('Berhasil', 'Data berhasil ditambahkan', 'success');

                $('#addPaymentModal').modal('hide');

                setTimeout(() => {
                    loadPayment(type);
                }, 1000);

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
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

                QToast('Berhasil', 'Data berhasil diperbarui', 'success');

                $('#editPaymentModal').modal('hide');

                setTimeout(() => {
                    loadPayment(type);
                }, 1000);

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

        QConfirm('Hapus Pembayaran?', 'Pembayaran "' + typeName + ' (' + formattedDate + ')" akan dihapus permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('tenant-payment-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id, type: type })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        setTimeout(() => {
                            loadPayment(type);
                        }, 1000);
                    }
                });
            }
        });
    });
</script>

<!-- Print Receipt -->
<script>
    function printReceipt(id, type) {
        const receiptUrl = `../receipt-tenant.php?payment_id=${id}&type=${type}`;

        // buka struk di tab baru
        const newWindow = window.open(receiptUrl, '_blank');

        // OPTIONAL: auto focus
        if (newWindow) {
            newWindow.focus();
        }

        if ($type == 'tenant') {
            $tittle = 'Pembayaran Tenant';
        } else {
            $tittle = 'Pembayaran Air & Listrik';
        }

        // SHARE (jika user klik manual)
        if (navigator.share) {
            navigator.share({
                title: 'Struk ' + $tittle,
                text: 'Berikut struk ' + $tittle,
                url: receiptUrl
            }).catch(err => console.log(err));
        }
    }
</script>

<!-- Script Edit Tenant -->
<script>
    document.getElementById('btnEditTenant').addEventListener('click', function(){
        document.getElementById('editTenantId').value = this.dataset.id;
        document.getElementById('editTenantName').value = this.dataset.name;
        document.getElementById('editTenantOwner').value = this.dataset.owner;
        new bootstrap.Modal(document.getElementById('editTenantModal')).show();
    });

    document.getElementById('editTenantForm').addEventListener('submit', async function(e){
        e.preventDefault();

        let btn = this.querySelector('.etd-btn-save');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

        try {
            let res = await fetch('registration-action.php?action=update&id=' + this.editTenantId.value, {
                method: 'POST',
                body: new FormData(this)
            });

            let data = await res.json();

            if(data.status === 'success'){
                QToast('Berhasil', 'Data tenant berhasil diperbarui', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editTenantModal')).hide();

                // Update UI tanpa refresh
                let newName = document.getElementById('editTenantName').value;
                let newOwner = document.getElementById('editTenantOwner').value;

                // Update nama tenant di header
                document.querySelector('.tenant-toggle span').textContent = newName.toUpperCase();
                // Update subtitle owner
                let subtitleEl = document.querySelector('.panel-subtitle');
                subtitleEl.innerHTML = newOwner + ' | ' + subtitleEl.textContent.split('|')[1].trim();

                // Update data attributes tombol edit
                let editBtn = document.getElementById('btnEditTenant');
                editBtn.dataset.name = newName;
                editBtn.dataset.owner = newOwner;
                let deleteBtn = document.getElementById('btnDeleteTenant');
                deleteBtn.dataset.name = newName;

            } else {
                QToast('Gagal', data.message || 'Terjadi kesalahan', 'error');
            }
        } catch {
            QToast('Error', 'Gagal memproses update', 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Simpan Perubahan';
    });
</script>

<!-- Script Hapus Tenant -->
<script>
    document.getElementById('btnDeleteTenant').addEventListener('click', function(){
        let id = this.dataset.id;
        let name = this.dataset.name;

        QConfirm(
            'Hapus Tenant?',
            'Tenant "' + name + '" akan dihapus permanen dari sistem.',
            {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}
        ).then(function(ok){
            if(ok){
                fetch('registration-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res => res.json())
                .then(res => {
                    QToast('Terhapus', 'Tenant berhasil dihapus', 'success');
                    // Redirect ke daftar tenant tanpa hard refresh
                    setTimeout(() => {
                        window.location.href = 'tenant.php';
                    }, 1000);
                });
            }
        });
    });
</script>

</body>
</html>