<?php
include '../../sessions/session.php';
$query = mysqli_query($conn,
            "SELECT * FROM tenants WHERE deleted_at IS NULL ORDER BY tenant_name ASC"
        );
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/tenant.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/tenant.css'); ?>">

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Daftar Tenant - Qieos</title>

    <?php include '../../script/headscript.php'; ?>

    <style>
        /* ===== FORM ===== */
        .reg-label{display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:8px}
        .reg-label i{color:#6366f1;margin-right:4px}

        .reg-inp-wrap{position:relative;display:flex;align-items:center}
        .reg-inp-wrap .reg-inp-icon{
            position:absolute;left:16px;color:#94a3b8;font-size:15px;
            transition:color 0.25s;z-index:5;pointer-events:none;
        }
        .reg-inp{
            width:100%;height:50px;padding:0 16px 0 46px;
            font-size:14px;font-weight:500;color:#0f172a;background:#f8fafc;
            border:1.5px solid #e2e8f0;border-radius:14px;outline:none;
            transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .reg-inp::placeholder{color:#94a3b8;font-weight:400}
        .reg-inp:focus{background:#fff;border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,0.12)}
        .reg-inp-wrap:focus-within .reg-inp-icon{color:#6366f1}

        .reg-btn{
            width:100%;height:50px;border:none;border-radius:14px;
            background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%);
            color:#fff;font-size:14px;font-weight:700;
            display:flex;align-items:center;justify-content:center;gap:10px;
            cursor:pointer;box-shadow:0 6px 20px rgba(79,70,229,0.35);
            transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
            position:relative;overflow:hidden;
        }
        .reg-btn::after{
            content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);
            transition:left 0.5s;
        }
        .reg-btn:hover::after{left:100%}
        .reg-btn:hover{background:linear-gradient(135deg,#4338ca 0%,#4f46e5 100%);transform:translateY(-2px);box-shadow:0 10px 28px rgba(79,70,229,0.45)}
        .reg-btn:active{transform:translateY(0);box-shadow:0 4px 12px rgba(79,70,229,0.25)}

        /* ===== CARD ACTION BUTTONS ===== */
        .card-actions{
            position:absolute;top:10px;right:10px;z-index:20;
            display:flex;gap:6px;opacity:0;transition:opacity 0.25s;
        }
        .product-card:hover .card-actions{opacity:1}
        .card-act{
            width:30px;height:30px;border:none;border-radius:9px;
            display:flex;align-items:center;justify-content:center;
            font-size:12px;cursor:pointer;transition:all 0.25s;
        }
        .card-act-edit{background:rgba(99,102,241,.85);color:#fff}
        .card-act-edit:hover{background:#6366f1;transform:scale(1.1);box-shadow:0 4px 14px rgba(99,102,241,.4)}
        .card-act-delete{background:rgba(239,68,68,.85);color:#fff}
        .card-act-delete:hover{background:#ef4444;transform:scale(1.1);box-shadow:0 4px 14px rgba(239,68,68,.4)}

        /* Mobile: always show */
        @media(max-width:575px){
            .card-actions{opacity:1}
            .card-act{width:28px;height:28px;font-size:10.5px;border-radius:8px}
        }

        /* ===== EDIT MODAL ===== */
        .tm-modal-content{border-radius:20px;overflow:hidden;box-shadow:0 25px 60px rgba(15,23,42,.25)}
        .tm-header{
            display:flex;align-items:center;justify-content:space-between;
            padding:20px 24px;
            background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);
            position:relative;overflow:hidden;
        }
        .tm-header::after{
            content:'';position:absolute;top:-50%;right:-5%;
            width:160px;height:160px;
            background:radial-gradient(circle,rgba(99,102,241,.18) 0%,transparent 70%);
            pointer-events:none;
        }
        .tm-header-left{display:flex;align-items:center;gap:14px;position:relative;z-index:2}
        .tm-icon{
            width:44px;height:44px;border-radius:12px;
            display:flex;align-items:center;justify-content:center;
            font-size:18px;color:#fff;flex-shrink:0;
        }
        .tm-icon-edit{background:linear-gradient(135deg,#6366f1,#4f46e5);box-shadow:0 6px 16px rgba(99,102,241,.4)}
        .tm-title{color:#fff;font-size:17px;font-weight:700;margin:0 0 2px}
        .tm-subtitle{color:#94a3b8;font-size:12px;margin:0}
        .tm-close{
            width:36px;height:36px;border-radius:10px;border:none;
            background:rgba(255,255,255,.1);color:#94a3b8;font-size:16px;
            display:flex;align-items:center;justify-content:center;
            cursor:pointer;transition:all 0.2s;position:relative;z-index:2;
        }
        .tm-close:hover{background:rgba(239,68,68,.25);color:#fca5a5}

        .tm-body{padding:24px;background:#fff}
        .tm-footer{
            display:flex;justify-content:flex-end;gap:10px;
            margin-top:24px;padding-top:18px;border-top:1px solid #f1f5f9;
        }
        .tm-btn{
            display:inline-flex;align-items:center;gap:8px;
            padding:10px 22px;border:none;border-radius:12px;
            font-size:14px;font-weight:600;cursor:pointer;transition:all 0.25s;
        }
        .tm-btn-cancel{background:#f1f5f9;color:#64748b}
        .tm-btn-cancel:hover{background:#e2e8f0;color:#334155}
        .tm-btn-save{
            background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;
            box-shadow:0 4px 14px rgba(79,70,229,.35);
        }
        .tm-btn-save:hover{background:linear-gradient(135deg,#4338ca,#4f46e5);transform:translateY(-1px);box-shadow:0 6px 20px rgba(79,70,229,.45)}

        /* ===== RESPONSIVE ===== */
        @media(max-width:991px){
            .reg-btn{margin-top:4px}
        }
        @media(max-width:575px){
            .reg-label{font-size:12px;margin-bottom:6px}
            .reg-inp{height:46px;font-size:13px;padding-left:42px;border-radius:12px}
            .reg-inp-wrap .reg-inp-icon{left:14px;font-size:14px}
            .reg-btn{height:46px;font-size:13px;border-radius:12px}
        }
    </style>
</head>

<body>
    <?php include '../components/sidebar.php'; ?>

    <main class="content" style="height:100vh;overflow-y:auto;overflow-x:hidden;">
        <?php include '../components/navbar.php'; ?>

        <div class="container-fluid px-0 mt-5 mb-5">

            <!-- FORM PENDAFTARAN + SEARCH di header -->
            <div class="section-card mb-5">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>

                        <div>
                            <div class="panel-title" id="formTitle">Pendaftaran Tenant Baru</div>
                            <div class="panel-subtitle" id="formSubtitle">Tambahkan tenant baru ke dalam ekosistem Qieos</div>
                        </div>
                    </div>

                    <div class="panel-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search" placeholder="Cari tenant...">
                    </div>
                </div>

                <div class="mt-4">
                    <form id="form-add-tenant">
                        <input type="hidden" name="id" id="formTenantId" value="">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5 col-md-6">
                                <label class="reg-label">
                                    <i class="fas fa-store"></i> Nama Tenant
                                </label>
                                <div class="reg-inp-wrap">
                                    <span class="reg-inp-icon"><i class="fas fa-shop"></i></span>
                                    <input type="text" name="tenant_name" id="formTenantName" class="reg-inp" placeholder="Contoh: Kantin Berkah 01" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6">
                                <label class="reg-label">
                                    <i class="fas fa-user-tie"></i> Nama Pemilik
                                </label>
                                <div class="reg-inp-wrap">
                                    <span class="reg-inp-icon"><i class="fas fa-user"></i></span>
                                    <input type="text" name="tenant_owner" id="formTenantOwner" class="reg-inp" placeholder="Contoh: H. Ahmad Supardi" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-12">
                                <button type="submit" class="reg-btn" id="formSubmitBtn">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Daftarkan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- GRID -->
            <div id="product-list" class="product-grid">

                <?php while ($row = mysqli_fetch_assoc($query)):
                    $tgl = strtotime($row['registration_date']);
                    $tanggal = date('d', $tgl);
                    $bulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                    $bulanText = $bulan[(int)date('n', $tgl)];
                    $tahun = date('Y', $tgl);
                    $search = strtolower($row['tenant_name'].' '.$row['tenant_owner'].' '.$tanggal.' '.$bulanText.' '.$tahun.' '.date('Y-m-d', $tgl));
                ?>
                <div class="product-item" data-search="<?php echo htmlspecialchars($search); ?>">
                    <div class="product-card">
                        <div class="product-image-wrap">
                            <img src="../../assets/img/tenant-img.jpg" class="product-img" loading="lazy" decoding="async">

                            <!-- CARD ACTIONS -->
                            <div class="card-actions">
                                <button type="button" class="card-act card-act-edit"
                                    onclick="openEdit(<?= $row['id'] ?>,'<?= htmlspecialchars(addslashes($row['tenant_name'])) ?>','<?= htmlspecialchars(addslashes($row['tenant_owner'])) ?>')"
                                    title="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="card-act card-act-delete"
                                    onclick="deleteTenant(<?= $row['id'] ?>,'<?= htmlspecialchars(addslashes($row['tenant_name'])) ?>')"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="product-content">
                            <div class="product-meta">
                                <div class="category-pill">
                                    <i class="fas fa-user"></i>
                                    <?php echo ucfirst($row['tenant_owner']); ?>
                                </div>
                            </div>
                            <h4 class="product-title">
                                <?php echo ucwords(strtolower($row['tenant_name'])); ?>
                            </h4>
                            <p class="product-desc">
                                <i class="fas fa-calendar text-success"></i>
                                <?php echo $tanggal . ' ' . $bulanText . ' ' . $tahun; ?>
                            </p>
                            <a href="tenant-detail.php?id=<?php echo $row['id']; ?>" class="btn-detail">
                                <span>Detail Tenant <i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

                <div id="empty-search" class="empty-search" style="display:none;">
                    <div class="empty-icon"><i class="fas fa-search"></i></div>
                    <h5>Tenant Tidak Ditemukan</h5>
                    <p>Tidak ada tenant yang cocok dengan pencarian Anda.</p>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../script/footscript.php'; ?>

    <!-- ===== SEARCH ===== -->
    <script>
        const searchInput = document.getElementById('search');
        const items = document.querySelectorAll('.product-item');
        const empty = document.getElementById('empty-search');
        const productList = document.getElementById('product-list');

        searchInput.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            let found = false;
            items.forEach(item => {
                if (item.dataset.search.includes(keyword)) {
                    item.style.display = '';
                    found = true;
                } else {
                    item.style.display = 'none';
                }
            });
            empty.style.display = found ? 'none' : 'flex';
            productList.classList.toggle('empty-mode', !found);
        });
    </script>

    <!-- ===== ADD / EDIT ===== -->
    <script>
        const form = document.getElementById('form-add-tenant');
        let editingId = null;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            let btn = document.getElementById('formSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            try {
                let url = editingId
                    ? 'registration-action.php?action=update&id=' + editingId
                    : 'registration-action.php?action=store';

                let res = await fetch(url, { method: 'POST', body: new FormData(this) });
                let data = await res.json();

                QToast(data.status === 'success' ? 'Berhasil' : 'Gagal', data.message, data.status);

                if (data.status === 'success') {
                    resetForm();
                    reloadGrid();
                }
            } catch {
                QToast('Error', 'Gagal memproses data', 'error');
            }

            btn.disabled = false;
            btn.innerHTML = editingId
                ? '<i class="fas fa-check-circle"></i> Simpan'
                : '<i class="fas fa-plus-circle"></i> Daftarkan';
        });

        function openEdit(id, name, owner) {
            editingId = id;
            document.getElementById('formTenantId').value = id;
            document.getElementById('formTenantName').value = name;
            document.getElementById('formTenantOwner').value = owner;
            document.getElementById('formTitle').textContent = 'Edit Tenant';
            document.getElementById('formSubtitle').textContent = 'Perbarui informasi tenant';
            document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-check-circle"></i> Simpan';

            // Scroll ke atas
            document.querySelector('.content').scrollTo({ top: 0, behavior: 'smooth' });

            // Highlight card form
            document.querySelector('.section-card').style.boxShadow = '0 0 0 3px rgba(99,102,241,0.3), 0 14px 40px rgba(15,23,42,0.1)';
            setTimeout(() => {
                document.querySelector('.section-card').style.boxShadow = '';
            }, 2000);
        }

        function resetForm() {
            editingId = null;
            form.reset();
            document.getElementById('formTenantId').value = '';
            document.getElementById('formTitle').textContent = 'Pendaftaran Tenant Baru';
            document.getElementById('formSubtitle').textContent = 'Tambahkan tenant baru ke dalam ekosistem Qieos';
            document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-plus-circle"></i> Daftarkan';
        }

        function deleteTenant(id, name) {
            QConfirm(
                'Hapus Tenant?',
                'Tenant "' + name + '" akan dihapus permanen.',
                {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}
            ).then(function(ok){
                if (ok) {
                    fetch('registration-action.php?action=destroy', {
                        method: 'POST',
                        body: new URLSearchParams({ id: id })
                    })
                    .then(res => res.json())
                    .then(res => {
                        QToast('Terhapus', 'Tenant berhasil dihapus', 'success');
                        reloadGrid();
                    });
                }
            });
        }

        function reloadGrid() {
            fetch('tenant-grid.php')
            .then(res => res.text())
            .then(html => {
                document.getElementById('product-list').innerHTML = html;

                // Rebind search
                const newItems = document.querySelectorAll('.product-item');
                const keyword = searchInput.value.trim().toLowerCase();
                let found = false;
                newItems.forEach(item => {
                    if (keyword && item.dataset.search.includes(keyword)) {
                        item.style.display = '';
                        found = true;
                    } else if (!keyword) {
                        item.style.display = '';
                        found = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                const empty = document.getElementById('empty-search');
                const grid = document.getElementById('product-list');
                empty.style.display = found ? 'none' : 'flex';
                grid.classList.toggle('empty-mode', !found);
            });
        }
    </script>

</body>
</html>
