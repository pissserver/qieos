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

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/tenant-detail.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/tenant-detail.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/tenant-detail-table.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/tenant-detail-table.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/datatable-premium.css?v=<?php echo filemtime(__DIR__ . '/../../css/components/datatable-premium.css'); ?>">

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

        .etd-modal-body{padding:20px;background:#fff}

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
            background:linear-gradient(90deg,#1e293b,#334155);color:#fff;
            min-width:132px;height:44px;
            padding:0 22px;
            line-height:1;
            font-size:13.5px;font-weight:700;letter-spacing:.01em;
            box-shadow:0 10px 20px -12px rgba(15,23,42,.8);
            transition:transform .18s ease, box-shadow .18s ease, background .2s;
        }
        .etd-btn-save i{font-size:13px;line-height:1}
        .etd-btn-save:hover{
            background:linear-gradient(90deg,#334155,#475569);color:#fff;
            transform:translateY(-1px);
            box-shadow:0 14px 24px -12px rgba(15,23,42,.85);
        }
        .etd-btn-save:active{transform:translateY(0)}

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

            /* Modal edit pembayaran & edit tenant: judul & tombol close tetap sebaris */
            #addPaymentModal .panel-header,
            #editPaymentModal .panel-header,
            #editTenantModal .panel-header{
                flex-direction:row;
                align-items:center;
                gap:12px;
            }
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

            /* ===== MODAL EDIT PEMBAYARAN (mobile) ===== */

            /* Konten: tanpa padding kiri-kanan px-5 */
            #addPaymentContent,
            #editPaymentContent{
                padding-left:12px !important;
                padding-right:12px !important;
                margin-top:0 !important;
            }

            #addPaymentContent .section-title,
            #editPaymentContent .section-title{
                margin-top:8px;
                padding-left:10px;
            }

            #addPaymentContent .row,
            #editPaymentContent .row{
                margin-left:0;
                margin-right:0;
            }

            #addPaymentContent .etp-field,
            #editPaymentContent .etp-field{
                margin-bottom:16px;
            }

            #addPaymentContent .etp-field:last-child,
            #editPaymentContent .etp-field:last-child{
                margin-bottom:4px;
            }

            #addPaymentContent .mp-add-footer,
            #editPaymentContent .mp-add-footer{
                flex-direction:column;
                align-items:stretch;
                margin-top:16px;
                padding-top:14px;
            }

            #addPaymentContent .btn-save,
            #editPaymentContent .btn-save{
                width:100%;
                min-width:0;
                height:46px;
                border-radius:12px;
                font-size:13.5px;
            }

            /* ===== MODAL EDIT TENANT (mobile) ===== */
                        #editTenantModal .etd-modal-body{
                padding:6px 20px 16px;
            }

            #editTenantModal .etd-form-footer .etd-btn-save{
                display:inline-flex;
                align-items:center;
                justify-content:center;
                gap:8px;

                width:100%;
                min-width:0;
                height:46px;
                padding:0 18px;
                border-radius:12px;
                font-size:13.5px;
            }

            #editTenantModal .etd-form-footer .etd-btn-save i{
                font-size:13px;
            }
        }
    /* ===== MODAL EDIT PEMBAYARAN & EDIT TENANT (desktop) ===== */
        #addPaymentModal .modal-content,
        #editPaymentModal .modal-content,
        #editTenantModal .modal-content{
            border-radius:24px;
            overflow:hidden;
        }

        /* Jarak atas judul "Informasi Pembayaran" di modal edit payment */
        #editPaymentContent .section-title{
            margin-top:18px;
        }

        @media(min-width:576px){
            /* Jarak header ke konten tetap rapat, konten di dalam (ada padding) */
            #addPaymentModal .panel-header,
            #editPaymentModal .panel-header{
                margin:18px 16px 8px !important;
            }

            #editTenantModal .panel-header{
                margin:12px 16px 8px !important;
            }

            #addPaymentContent,
            #editPaymentContent{
                padding-left:32px !important;
                padding-right:32px !important;
                margin-top:0 !important;
            }

                        #editTenantModal .etd-modal-body{
                padding:4px 24px 16px;
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

                <div id="payment-content" class="dt-premium">

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
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-pen-to-square"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Edit Tenant
                            </div>
                            <div class="panel-subtitle">
                                Perbarui informasi tenant
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="etd-modal-body mt-2">
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
                            <button type="submit" class="etd-btn btn-save">
                                <i class="fas fa-save"></i>
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../script/footscript.php'; ?>

<script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<!-- Tenant Dropdown -->
<script>
    const tenantToggle=document.getElementById("tenantToggle");
    const tenantMenu=document.getElementById("tenantMenu");
    const tenantArrow=document.getElementById("tenantArrow");

    const tenantPanelHeader = document.querySelector('.panel-header');

    function positionTenantMenu(){
        if(window.innerWidth > 575) return;

        const rect = tenantPanelHeader.getBoundingClientRect();

        tenantMenu.style.setProperty('--tm-left', rect.left + 'px');
        tenantMenu.style.setProperty('--tm-width', rect.width + 'px');
        tenantMenu.style.setProperty('--tm-top', (rect.bottom + 10) + 'px');
    }

    tenantToggle.addEventListener("click",function(e){

        e.stopPropagation();

        positionTenantMenu();

        tenantMenu.classList.toggle("show");

        tenantToggle.classList.toggle("active");

    });

    document.addEventListener("click",function(){

        tenantMenu.classList.remove("show");

        tenantToggle.classList.remove("active");

    });

    window.addEventListener("resize",positionTenantMenu);
    window.addEventListener("scroll",positionTenantMenu,true);
</script>

<!-- Switch Payment Tab -->
<script>
    const USER_ROLE = '<?= $user['role'] ?>';

    function renderMobileCards(){
        // Tidak pakai card lagi, pakai tabel responsive biasa
        return;
    }

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

    function formatRupiah(value){
        const num = Number(String(value).replace(/[^0-9.-]/g,"")) || 0;
        return "Rp " + num.toLocaleString("id-ID");
    }

    function rowAttr(row, name){
        if(!row || !row.getAttribute) return "";

        const val = row.getAttribute("data-" + name);

        return val === null ? "" : String(val).trim();
    }

    function paymentCardBadge(status){
        return String(status).trim().toLowerCase() === "paid"
            ? '<span class="paym-card-badge is-paid"><i class="fas fa-check"></i>Lunas</span>'
            : '<span class="paym-card-badge is-pending"><i class="fas fa-clock"></i>Belum Lunas</span>';
    }

    function paymentCardActions(row){
        const id = rowAttr(row, "id");
        const type = rowAttr(row, "type");
        const paymentDate = rowAttr(row, "payment-date");

        if(USER_ROLE !== "staff kasir"){
            return '' +
                '<button class="paym-card-btn is-edit editPaymentBtn" data-id="'+id+'" data-type="'+type+'">' +
                    '<i class="fas fa-pen"></i>' +
                '</button>' +
                '<button class="paym-card-btn is-delete deletePaymentBtn" data-id="'+id+'" data-date="'+paymentDate+'" data-type="'+type+'">' +
                    '<i class="fas fa-trash"></i>' +
                '</button>';
        }

        return '<button class="paym-card-btn is-print" onclick="printReceipt(\''+id+'\', \''+type+'\')">' +
            '<i class="fas fa-print"></i>' +
        '</button>';
    }

    function renderPaymentCards(api, box){
        let rows = [];

        // nodes() selalu mengembalikan elemen <tr> asli, sehingga
        // atribut data-* pasti terbaca (data() tidak selalu begitu)
        try{
            rows = api.rows({page:"current"}).nodes().toArray();
        }catch(err){
            rows = [];
        }

        // Hanya baris pembayaran asli (DataTables menyisipkan bar
        // "no data" sendiri saat search tidak menemukan hasil)
        let cards = rows.filter(function(row){
            return row && row.getAttribute && row.getAttribute("data-id");
        });

        if(!cards.length){
            const tbody = document.querySelector("#tablePayment tbody");

            if(tbody){
                cards = Array.prototype.slice.call(tbody.querySelectorAll("tr[data-id]"));
            }
        }

        if(!cards.length){
            let noMatch = false;

            try{
                const info = api.page.info();
                noMatch = info.recordsTotal > 0 && info.recordsDisplay === 0;
            }catch(err){
                noMatch = false;
            }

            box.innerHTML =
                '<div class="empty-search">' +
                    '<img src="../../assets/img/illustrations/empty-data.png" class="empty-img" alt="">' +
                    '<div class="empty-title">'+ (noMatch ? "Pembayaran tidak ditemukan" : "Belum ada data pembayaran") +'</div>' +
                    '<div class="empty-sub">'+ (noMatch ? "Coba gunakan kata kunci lain" : "Silakan tambahkan pembayaran terlebih dahulu") +'</div>' +
                '</div>';
            return;
        }

        box.innerHTML = cards.map(function(row){
            const cells = row.cells || [];

            const date = rowAttr(row, "date")
                || (cells[0] ? cells[0].textContent.replace(/\s+/g," ").trim() : "")
                || "-";

            const amount = rowAttr(row, "amount")
                || (cells[1] ? cells[1].textContent.replace(/[^0-9]/g,"") : "");

            const status = rowAttr(row, "status");

            return '' +
            '<article class="paym-card">' +
                '<div class="paym-card-icon">' +
                    '<i class="fa-solid fa-wallet"></i>' +
                '</div>' +
                '<div class="paym-card-main">' +
                    '<div class="paym-card-date">'+ date +'</div>' +
                    '<div class="paym-card-row">' +
                        '<span class="paym-card-amount">'+ (amount ? formatRupiah(amount) : "-") +'</span>' +
                        paymentCardBadge(status) +
                    '</div>' +
                '</div>' +
                '<div class="paym-card-actions">'+ paymentCardActions(row) +'</div>' +
            '</article>';
        }).join("");
    }

    /* ---------- DATA TABLE PEMBAYARAN ----------

       Breakpoint, pager, dan chip info sekarang milik helper bersama
       script/datatable-compact.js, sama seperti halaman Master, Purchasing,
       Stok Gudang, Mutasi, Transfer, dan Sales. Angka 575.98 tidak lagi
       ditulis ulang di file ini. */
    function isPaymentMobile(){
        return MP_TABLE.isMobile();
    }

    let paymentIsMobile = isPaymentMobile();

    /* State per tab. "tenant" dan "utility" memakai tabel yang sama
       (#tablePayment), jadi kalau state-nya disimpan di satu variabel global,
       pindah tab akan ikut membawa kata kunci pencarian dari tab lain.
       Dipisah per tab: masing-masing ingat halaman/pencarian/jumlah baris
       sendiri, danberganti bolak-balik tidak kehilangan posisi. */
    const paymentTableState = {
        tenant:{ page:0, keyword:"", length:null },
        utility:{ page:0, keyword:"", length:null }
    };

    /* Tab yang sedang ditampilkan. Dipakai untuk membedakan "reload tab yang
       sama" dari "pindah tab" - lihat catatan di loadPayment(). */
    let currentPaymentType = null;

    function paymentDataTableOptions(){
        const mobile = isPaymentMobile();

        const options = {
            pageLength:5,

            /* Opsi 100 yang tadinya ada dihapus supaya daftar "Show n
               entries" sama persis dengan halaman lain. */
            lengthMenu:[[5,10,25,50],[5,10,25,50]],

            /* Pager kustom dari script/datatable-compact.js: 3 nomor di
               mobile, 5 di tablet/desktop, halaman pertama & terakhir dikunci
               di dua ujung, dan "..." hanya muncul kalau memang ada nomor
               yang disembunyikan.

               DULU mobile memakai "simple_numbers" dengan dom "ftp". */
            pagingType:"mp_compact",

            /* Chip "Menampilkan 1-5 dari 137 pembayaran". */
            infoCallback: MP_TABLE.infoCallback('pembayaran'),

            /* Tabel ini tidak pernah diurutkan - urutan bawaan PHP sudah
               "ORDER BY payment_date DESC". */
            ordering:false,

            /* Opsi "responsive" DIHAPUS. Opsi itu hanya dibaca oleh extension
               Responsive (responsive.dataTables.min.js) yang TIDAK dimuat di
               script/headscript.php - yang ada hanya jquery.dataTables.min.js +
               dataTables.bootstrap5.min.js, jadi nilainya diam-diam diabaikan.
               Tampilan mobile ditangani manual: tukar "dom" saat breakpoint
               (lihat bawah) + kartu .paym-card dari drawCallback. */
            autoWidth:false,

            language:{
                search:"",
                searchPlaceholder:"Cari pembayaran...",

                /* Panah Previous/Next memakai glyph supaya tidak memakan
                   ruang di baris pager yang sempit. Aksesibilitas tetap
                   terjaga lewat aria-label bahasa Indonesia di oAria. */
                paginate:{
                    previous:"&#8249;",  // ‹
                    next:"&#8250;"       // ›
                },

                oAria:{
                    paginate:{
                        pageLabel:"Halaman {page}",
                        previous:"Halaman sebelumnya",
                        next:"Halaman berikutnya"
                    }
                },

                zeroRecords:
                    '<div class="empty-search">' +
                        '<img src="../../assets/img/illustrations/empty-data.png" class="empty-img" alt="">' +
                        '<div class="empty-title">Pembayaran tidak ditemukan</div>' +
                        '<div class="empty-sub">Coba gunakan kata kunci lain</div>' +
                    '</div>',

                emptyTable:
                    '<div class="empty-search">' +
                        '<img src="../../assets/img/illustrations/empty-data.png" class="empty-img" alt="">' +
                        '<div class="empty-title">Belum ada data pembayaran</div>' +
                        '<div class="empty-sub">Silakan tambahkan pembayaran terlebih dahulu</div>' +
                    '</div>'
            },

            drawCallback:function(){
                /* Kartu mobile hanya dibuat saat breakpoint mobile. Di
                   desktop/tablet tabelnya tetap jadi tabel. */
                if(!isPaymentMobile()) return;

                const wrapper = document.getElementById("tablePayment_wrapper");
                if(!wrapper) return;

                let box = document.getElementById("paymentCardsMobile");

                if(!box){
                    box = document.createElement("div");
                    box.id = "paymentCardsMobile";
                    box.className = "paym-cards";

                    /* Ditaruh sebelum baris info chip, bukan sebelum
                       paginate. DULU "i" tidak ikut di-render di mobile
                       (dom "ftp"), jadi kartu bisa langsung menempel di bawah
                       tabel. Sekarang dom "fltip" selalu menampilkan baris
                       info, jadi titik sisipnya digeser ke atas supaya urutan
                       bacanya: tabel -> kartu -> info -> paginate. */
                    const anchor =
                        wrapper.querySelector(".dataTables_info") ||
                        wrapper.querySelector(".dataTables_paginate");

                    if(anchor){
                        wrapper.insertBefore(box, anchor);
                    } else {
                        wrapper.appendChild(box);
                    }
                }

                renderPaymentCards(this.api(), box);
            }
        };

        /* Mobile pakai "fltip": search + show entries + tabel + baris info chip
           + pagination.

           "dom" HANYA di-set untuk mobile. Di desktop/tablet TIDAK di-set,
           sama seperti halaman lain, jadi wrapper .row + .col-* bawaan
           DataTables Bootstrap 5 yang dipakai:

             <'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row dt-row'
             <'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>

           DULU desktop memakai "f l t p" lalu "#tablePayment_length" dipindah
           manual ke dalam .table-toolbar. Susunan manual itu sekarang
           dihapus; premium.css yang menata baris kontrolnya, persis seperti
           halaman lain. */
        if(mobile) options.dom = "fltip";

        return options;
    }

    function paymentDataTable(){
        const table = $("#tablePayment");
        if(!table.length) return null;

        const dt = table.DataTable(paymentDataTableOptions());

        /* Search + tombol "Tambah Pembayaran" dalam satu .table-action-wrapper
           (helper yang sama dipakai halaman Master). */
        MP_TABLE.placeActions("#tablePayment_filter", "#tpwBtnContainer");

        dt.columns.adjust();

        return dt;
    }

    function loadPayment(type){
        const paymentContent = document.getElementById("payment-content");

        /* Urutan benar: node <table id="tablePayment"> masih terpasang, jadi
           isDataTable() masih bisa menemukan instance lamanya. Kalau
           innerHTML diganti lebih dulu, node itu ikut terbuang dan
           isDataTable() selalu false - state tidak akan pernah tertangkap. */
        const prev = $.fn.DataTable.isDataTable("#tablePayment")
            ? $("#tablePayment").DataTable()
            : null;

        /* PENTING: loadPayment() dipanggil untuk dua hal yang BERBEDA, jadi
           sumber state-nya harus dibedakan.

             1. Tab yang sama di-reload (mis. setelah tambah pembayaran):
                tabel yang sedang tampil adalah milik tab itu juga, jadi
                posisinya yang harus dipertahankan.

             2. Pindah tab ("tenant" <-> "utility"):
                prev masih ada, tapi isinya milik tab LAIN. Kalau state-nya
                ikut dipakai, pindah tab akan membawa kata kunci pencarian
                dari tab sebelumnya - dan balik lagi ke tab pertama akan
                menemukan kata kunci yang bukan miliknya.

           Karena itu: sebelum tabel lama dibuang, posisinya ALWAYS disimpan ke
           cache milik tab asalnya. Setelah itu yang dipulihkan hanya state
           milik tab TUJUHAN - jadi reload tab yang sama ikut terjaga (karena
           cache-nya baru saja diisi dari tabel yang sedang tampil), pindah tab
           memakai state tab itu sendiri, dan tab yang belum pernah dibuka
           mulai dari halaman 0 tanpa pencarian (length masih null). */
        if (prev && currentPaymentType) {
            paymentTableState[currentPaymentType] = {
                page: prev.page(),
                keyword: prev.search(),
                length: prev.page.len()
            };
        }

        let live = paymentTableState[type].length
            ? paymentTableState[type]
            : null;

        if(prev) prev.destroy();

        paymentContent.innerHTML=`
            <div class="loading-box">
                <i class="fas fa-spinner fa-spin"></i>
                Memuat data...
            </div>
        `;

        fetch("tenant-detail-table.php?type="+type+"&tenant=<?= $tenant['id']; ?>")
        .then(res=>res.text())
        .then(html=>{

            paymentContent.innerHTML=html;

            paymentIsMobile = isPaymentMobile();
            currentPaymentType = type;

            const dt = paymentDataTable();

            if(dt && live){
                /* Halaman di luar rentang otomatis dikembalikan DataTable
                   ke 0, jadi aman walau baris habis. */
                dt.page.len(live.length)
                  .search(live.keyword)
                  .page(live.page)
                  .draw(false);
            }

            window.currentPaymentDT = dt;
        });
    }

    /* Ingat posisi terakhir tiap tab supaya pindah tab & reload setelah
       tambah pembayaran tidak mengembalikan tabel ke halaman 1. */
    function rememberPaymentState(type){
        if(!$.fn.DataTable.isDataTable("#tablePayment")) return;

        const dt = $("#tablePayment").DataTable();

        paymentTableState[type] = {
            page: dt.page(),
            keyword: dt.search(),
            length: dt.page.len()
        };
    }

    /* ---------- REBUILD KETIKA GANTI MOBILE / DESKTOP ----------

       DULU handler resize di halaman ini kosong, jadi tabel yang sudah
       dibangun dengan dom mobile tidak pernah dibangun ulang - susunan
       kontrolnya tetap versi mobile padahal lebar jendela sudah berubah.
       Sekarang tabel di-rebuild in-place (tanpa fetch) begitu breakpoint
       dilewati, dengan state halaman/pencarian/jumlah baris tetap terjaga. */
    let paymentResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(paymentResizeTimer);
        paymentResizeTimer = setTimeout(function(){
            if(isPaymentMobile() === paymentIsMobile) return;
            if(!$.fn.DataTable.isDataTable("#tablePayment")) return;

            const type = currentPaymentType || "tenant";

            /* Simpan posisi tabel yang sedang tampil dulu, baru flip flag dan
               bangun ulang in-place (tanpa fetch, jadi tidak ada flash
               kosong). */
            rememberPaymentState(type);

            $("#tablePayment").DataTable().destroy();

            paymentIsMobile = isPaymentMobile();

            const keep = paymentTableState[type];

            const dt = paymentDataTable();

            if(dt && keep.length){
                dt.page.len(keep.length)
                  .search(keep.keyword)
                  .page(keep.page)
                  .draw(false);
            }
        }, 250);
    });
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

                updateRowInTable(id, type);

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
        });
    });

    function updateRowInTable(id, type){
        fetch('tenant-payment-action.php?action=getOne&id='+id+'&type='+type)
        .then(res=>res.json())
        .then(data=>{
            if(data.status==='success'){
                const payment = data.payment;
                const row = $('#tablePayment').DataTable().row('[data-id="'+id+'"]');
                
                if(row.length){
                    const dateFormatted = new Date(payment.payment_date).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});
                    
                    row.node().dataset.date = dateFormatted;
                    row.node().dataset.paymentDate = payment.payment_date;
                    
                    $(row.node()).find('.date-main').html('<i class="fa-regular fa-calendar"></i> '+dateFormatted);
                    
                    $('#tablePayment').DataTable().draw(false);
                }
            }
        });
    }
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
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + id + '&type=' + type
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        const table = $('#tablePayment').DataTable();
                        table.row('[data-id="'+id+'"]').remove().draw(false);

                    }else{
                        QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(err=>{
                    console.error(err);
                    QToast('Error', 'Gagal menghapus data', 'error');
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

        /* DULU di sini ditulis "$type" dan "$tittle" (dengan tanda dolar),
           padahal ini JavaScript, bukan PHP. "$type" tidak pernah terdefinisi
           sehingga baris pengikutnya melempar ReferenceError dan judul struk
           tidak pernah ikut saat navigator.share aktif. */
        const title = type == 'tenant'
            ? 'Pembayaran Tenant'
            : 'Pembayaran Air & Listrik';

        // SHARE (jika user klik manual)
        if (navigator.share) {
            navigator.share({
                title: 'Struk ' + title,
                text: 'Berikut struk ' + title,
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
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

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
        btn.innerHTML = '<i class="fas fa-save"></i> Save';
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