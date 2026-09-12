<?php
include '../../sessions/session.php';

header('Cache-Control: no-store, no-cache, must-revalidate');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$q = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$d = mysqli_fetch_assoc($q);

if(!$d){
    echo '<!doctype html><html><head><title>Tidak Ditemukan</title></head><body class="d-flex align-items-center justify-content-center vh-100"><h3 class="text-muted">Produk tidak ditemukan.</h3></body></html>';
    exit;
}

// Hitung jumlah pembelian
$qi = mysqli_query($conn,"
    SELECT COALESCE(SUM(pi.qty),0) as total_qty, COUNT(pi.id) as total_transaksi
    FROM purchase_items pi
    WHERE pi.product_id = {$d['id']} AND pi.deleted_at IS NULL
");
$di = mysqli_fetch_assoc($qi);

// Ambil suppliers dari tabel relasi product_supplier (multi supplier)
$supplierNamesArr = [];
$currentSupplierIds = [];
$qs = mysqli_query($conn, "
    SELECT s.id, s.name
    FROM product_supplier ps
    JOIN suppliers s ON s.id = ps.supplier_id
    WHERE ps.product_id = {$d['id']} AND s.deleted_at IS NULL
    ORDER BY s.name ASC
");
while($s = mysqli_fetch_assoc($qs)){
    $currentSupplierIds[] = (int)$s['id'];
    $supplierNamesArr[] = $s['name'];
}

// Semua supplier aktif (untuk opsi dropdown & JS)
$allSuppliers = [];
$qs = mysqli_query($conn, "SELECT id, name FROM suppliers WHERE deleted_at IS NULL ORDER BY name ASC");
while($s = mysqli_fetch_assoc($qs)){
    $allSuppliers[] = ['id' => (int)$s['id'], 'name' => $s['name']];
}

// Bangun opsi <option> untuk satu baris supplier (filter supplier yang sudah dipilih di row lain)
function buildSupplierOptions($selectedId, $excludeIds, $allSuppliers){
    $html = '<option value="">Supplier (opsional)</option>';
    foreach($allSuppliers as $s){
        if(in_array((int)$s['id'], $excludeIds)) continue;
        $sel = ((int)$selectedId === (int)$s['id']) ? ' selected' : '';
        $html .= '<option value="' . (int)$s['id'] . '"' . $sel . '>' . htmlspecialchars($s['name']) . '</option>';
    }
    return $html;
}

$photo = !empty($d['photo']) ? BASE_URL . '/assets/img/products/' . htmlspecialchars($d['photo']) : '';
$code  = htmlspecialchars($d['code']);
$name  = htmlspecialchars($d['name']);
$cat   = ucwords(strtolower(htmlspecialchars($d['category'])));
$price = number_format($d['sell_price'], 0, ',', '.');
$totalQty    = number_format($di['total_qty'], 0, ',', '.');
$totalTrans  = $di['total_transaksi'];

$bulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$created = isset($d['created_at']) ? $d['created_at'] : date('Y-m-d H:i:s');
$tgl = strtotime($created);
$tglStr = date('d', $tgl) . ' ' . $bulan[(int)date('n', $tgl)] . ' ' . date('Y', $tgl) . ', ' . date('H:i', $tgl);

$currentCategory = isset($d['category']) ? $d['category'] : '';
$currentPrice    = isset($d['sell_price']) ? $d['sell_price'] : 0;
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $name ?> - Detail Produk</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product-detail.css?v=<?= filemtime(__DIR__ . '/../../css/pages/master-product-detail.css') ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>

<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="detail-bg">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="particles">
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
    </div>
</div>

<div class="detail-wrapper">

    <!-- TOP BAR -->
    <div class="top-bar">
        <a href="<?php echo BASE_URL; ?>/pages/master/master-product.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <!-- QUICK PRODUCT SEARCH -->
        <div class="product-search-wrap">
            <div class="product-search-input-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="quickProductSearch" placeholder="Cari & ganti produk master..." autocomplete="off">
                <i class="fas fa-times clear-search-icon" id="clearSearchBtn" style="display:none;"></i>
            </div>
            <div class="product-search-results" id="searchResultDropdown"></div>
        </div>

        <div class="top-actions">
            <button type="button" class="act-btn act-edit" id="btnToggleEdit">
                <i class="fas fa-pen"></i> Edit Produk
            </button>
            <button type="button" class="act-btn act-delete" id="btnDeleteProduct" data-id="<?= $d['id'] ?>">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>

    <!-- HERO CARD -->
    <div class="hero-card premium-card">
        <div class="hero-top">
            <div class="hero-photo">
                <?php if($photo): ?>
                    <img src="<?= $photo ?>" alt="<?= $name ?>">
                <?php else: ?>
                    <div class="hero-photo-placeholder"><i class="fas fa-box-open"></i><span>Tidak ada foto</span></div>
                <?php endif; ?>
            </div>
            <div class="hero-info">
                <div class="hero-category" id="heroCategory"><i class="fas fa-tag"></i> <?= $cat ?></div>
                <h1 class="hero-name" id="heroName"><?= $name ?></h1>
                <div class="hero-code"><i class="fas fa-barcode"></i> Kode: <span class="code-tag" id="heroCode"><?= $code ?></span></div>
                <div class="hero-supplier"><i class="fas fa-truck"></i> Supplier: <span class="supp-badge-list" id="heroSupplierName"><?php if(empty($supplierNamesArr)): ?><span class="supp-badge"><i class="fas fa-store"></i> -</span><?php else: foreach($supplierNamesArr as $nm): ?><span class="supp-badge"><i class="fas fa-store"></i> <?= htmlspecialchars($nm) ?></span><?php endforeach; endif; ?></span></div>
                <div class="hero-price" id="heroPrice">Rp <?= $price ?></div>
                <div class="hero-date"><i class="fas fa-calendar-alt"></i> Ditambahkan: <?= $tglStr ?></div>
            </div>
        </div>
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-icon stat-icon-purple"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-value"><?= $totalTrans ?></div>
                <div class="stat-label">Total Transaksi</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon stat-icon-green"><i class="fas fa-cubes"></i></div>
                <div class="stat-value"><?= $totalQty ?></div>
                <div class="stat-label">Total Qty Dibeli</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon stat-icon-amber"><i class="fas fa-coins"></i></div>
                <div class="stat-value">Rp <?= $price ?></div>
                <div class="stat-label">Harga Jual</div>
            </div>
        </div>
    </div>

    <!-- EDIT CARD (inline, hidden by default) -->
    <div class="edit-card premium-card" id="editCard">
        <div class="edit-card-inner">
            <div class="edit-card-header">
                <div class="edit-card-icon"><i class="fas fa-pen"></i></div>
                <div class="edit-card-title">Edit Data Produk</div>
            </div>

        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-barcode"></i> Kode Produk</label>
                    <div class="form-input-wrap">
                        <div class="form-input-icon"><i class="fas fa-barcode"></i></div>
                        <input type="text" name="code" class="form-input" value="<?= $code ?>" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-box"></i> Nama Produk</label>
                    <div class="form-input-wrap">
                        <div class="form-input-icon"><i class="fas fa-box"></i></div>
                        <input type="text" name="name" class="form-input" value="<?= $name ?>" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-tags"></i> Kategori</label>
                    <div class="form-input-wrap">
                        <div class="form-input-icon"><i class="fas fa-tags"></i></div>
                        <?php $catLower = strtolower($currentCategory); ?>
                        <select name="category" class="form-input" required>
                            <option value="">Kategori</option>
                            <option value="Makanan" <?= $catLower === 'makanan' ? 'selected' : '' ?>>Makanan</option>
                            <option value="Minuman" <?= $catLower === 'minuman' ? 'selected' : '' ?>>Minuman</option>
                            <option value="Jajanan" <?= $catLower === 'jajanan' ? 'selected' : '' ?>>Jajanan</option>
                            <option value="Pelengkap" <?= $catLower === 'pelengkap' ? 'selected' : '' ?>>Pelengkap</option>
                            <option value="Additional" <?= $catLower === 'additional' ? 'selected' : '' ?>>Additional</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-tag"></i> Harga Jual (Rp)</label>
                    <div class="form-input-wrap">
                        <div class="form-input-icon"><i class="fas fa-tag"></i></div>
                        <input type="number" name="price" class="form-input" value="<?= $currentPrice ?>" min="0" step="100" required>
                    </div>
                </div>
                <div class="col-md-12 mb-4">
                    <label class="form-label"><i class="fas fa-truck"></i> Supplier</label>
                    <div id="supplierFields" class="supplier-fields">
                        <?php
                        $rowsRender = !empty($currentSupplierIds) ? $currentSupplierIds : [''];
                        foreach($rowsRender as $rowVal):
                            $exclude = array_filter($currentSupplierIds, function($v) use ($rowVal){
                                return $rowVal !== '' && (int)$v !== (int)$rowVal;
                            });
                        ?>
                        <div class="supplier-field-row">
                            <div class="form-input-wrap">
                                <div class="form-input-icon"><i class="fas fa-truck"></i></div>
                                <div class="select-wrap">
                                    <select name="supplier_id[]" class="form-input">
                                        <?= buildSupplierOptions($rowVal, $exclude, $allSuppliers) ?>
                                    </select>
                                </div>
                                <button type="button" class="btn-remove-supplier" data-remove title="Hapus supplier"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add-supplier" id="btnAddSupplier">
                        <i class="fas fa-plus"></i><span>Tambah Supplier</span>
                    </button>
                    <template id="supplierFieldTemplate">
                        <div class="supplier-field-row">
                            <div class="form-input-wrap">
                                <div class="form-input-icon"><i class="fas fa-truck"></i></div>
                                <div class="select-wrap">
                                    <select name="supplier_id[]" class="form-input">
                                        <option value="">Supplier (opsional)</option>
                                    </select>
                                </div>
                                <button type="button" class="btn-remove-supplier" data-remove title="Hapus supplier"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="col-md-12">
                    <div class="photo-upload-box">
                        <div class="photo-preview" id="editPhotoPreview">
                            <?php if(!empty($d['photo'])): ?>
                                <img src="<?php echo BASE_URL; ?>/assets/img/products/<?= htmlspecialchars($d['photo']) ?>" alt="Preview">
                            <?php else: ?>
                                <i class="fas fa-camera"></i>
                            <?php endif; ?>
                        </div>
                        <div class="photo-input-details">
                            <label><i class="fas fa-image me-1"></i> Foto Produk</label>
                            <input type="file" name="photo" accept="image/*" class="form-control">
                            <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2MB. Kosongkan jika tidak ingin mengganti.</small>
                            <?php if(!empty($d['photo'])): ?>
                                <input type="hidden" name="old_photo" value="<?= htmlspecialchars($d['photo']) ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" id="btnCancelEdit">Batal</button>
                <button type="submit" class="btn-update"><i class="fas fa-save me-1"></i> Update Produk</button>
            </div>
        </form>
        </div>
    </div>

    <!-- INFO CARD -->
    <div class="detail-card premium-card">
        <div class="detail-card-inner">
            <div class="detail-card-header">
                <div class="detail-card-icon"><i class="fas fa-th-large"></i></div>
                <div class="detail-card-title">Informasi Lengkap</div>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-item-icon"><i class="fas fa-barcode"></i></div>
                    <div class="info-item-label">Kode Produk</div>
                    <div class="info-item-value" id="infoCode"><?= $code ?></div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon"><i class="fas fa-box"></i></div>
                    <div class="info-item-label">Nama Produk</div>
                    <div class="info-item-value" id="infoName"><?= $name ?></div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon"><i class="fas fa-tags"></i></div>
                    <div class="info-item-label">Kategori</div>
                    <div class="info-item-value" id="infoCategory"><?= $cat ?></div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon"><i class="fas fa-coins"></i></div>
                    <div class="info-item-label">Harga Jual</div>
                    <div class="info-item-value price-val" id="infoPrice">Rp <?= $price ?></div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon"><i class="fas fa-truck"></i></div>
                    <div class="info-item-label">Supplier</div>
                    <div class="info-item-value"><div class="supp-badge-list" id="infoSupplier"><?php if(empty($supplierNamesArr)): ?><span class="supp-badge"><i class="fas fa-store"></i> -</span><?php else: foreach($supplierNamesArr as $nm): ?><span class="supp-badge"><i class="fas fa-store"></i> <?= htmlspecialchars($nm) ?></span><?php endforeach; endif; ?></div></div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon"><i class="fas fa-cubes"></i></div>
                    <div class="info-item-label">Total Dibeli</div>
                    <div class="info-item-value"><?= $totalQty ?> unit</div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="info-item-label">Total Transaksi</div>
                    <div class="info-item-value"><?= $totalTrans ?> kali</div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="info-item-label">Tanggal Input</div>
                    <div class="info-item-value"><?= $tglStr ?></div>
                </div>
            </div>
        </div>
    </div>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
// Toggle edit card
var contentEl = document.querySelector('.content');
document.getElementById('btnToggleEdit').addEventListener('click', function(){
    var card = document.getElementById('editCard');
    card.classList.toggle('show');
    if(card.classList.contains('show')){
        setTimeout(function(){
            var scrollTarget = window.innerWidth < 992
                ? window.innerHeight * 2
                : window.innerHeight * 0.8;
            try { contentEl.scrollTo({ top: scrollTarget, behavior: 'smooth' }); }
            catch(e){ contentEl.scrollTop = scrollTarget; }
        }, 200);
    }
});

document.getElementById('btnCancelEdit').addEventListener('click', function(){
    document.getElementById('editCard').classList.remove('show');
});

// Photo preview — klik area upload membuka file dialog
var photoBox = document.querySelector('.photo-upload-box');
var photoInput = document.querySelector('input[name="photo"]');
if(photoBox && photoInput){
    photoBox.addEventListener('click', function(e){
        if(photoInput.contains(e.target)) return;
        photoInput.click();
    });
}
if(photoInput){
    photoInput.addEventListener('change', function(){
        var file = this.files[0];
        var preview = document.getElementById('editPhotoPreview');
        if(file){
            var reader = new FileReader();
            reader.onload = function(e){ preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">'; };
            reader.readAsDataURL(file);
        }
    });
}

// Supplier tambahan — tambah / hapus dropdown dengan desain yang sama
var SUPPLIER_LIST = <?= json_encode($allSuppliers) ?>;
var supplierFields = document.getElementById('supplierFields');
var supplierTemplate = document.getElementById('supplierFieldTemplate');

function escHtml(s){
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function supplierOptionsHtml(selectedId, excludeIds){
    var html = '<option value="">Supplier (opsional)</option>';
    for(var i = 0; i < SUPPLIER_LIST.length; i++){
        var s = SUPPLIER_LIST[i];
        var sid = String(s.id);
        if(excludeIds.indexOf(sid) >= 0) continue;
        var sel = sid === String(selectedId) ? ' selected' : '';
        html += '<option value="' + s.id + '"' + sel + '>' + escHtml(s.name) + '</option>';
    }
    return html;
}

function refreshAllSupplierRows(){
    var rows = supplierFields.querySelectorAll('.supplier-field-row');
    // Hilangkan duplikat nilai antar row
    var seen = {};
    rows.forEach(function(r){
        var sel = r.querySelector('select');
        if(sel.value && seen[sel.value] && sel.value !== ''){ sel.value = ''; }
        seen[sel.value || ''] = true;
    });
    // Bangun ulang opsi tiap row: exclude supplier yang dipilih di row lain
    rows.forEach(function(r){
        var sel = r.querySelector('select');
        var selected = sel.value;
        var others = [];
        rows.forEach(function(o){
            var s = o.querySelector('select');
            if(o !== r && s.value) others.push(s.value);
        });
        sel.innerHTML = supplierOptionsHtml(selected, others);
    });
}

function getSelectedSupplierNames(){
    var names = [];
    var rows = supplierFields.querySelectorAll('.supplier-field-row');
    rows.forEach(function(r){
        var v = r.querySelector('select').value;
        if(!v) return;
        for(var i = 0; i < SUPPLIER_LIST.length; i++){
            if(String(SUPPLIER_LIST[i].id) === String(v)){
                names.push(SUPPLIER_LIST[i].name);
                break;
            }
        }
    });
    return names;
}

function getSelectedSupplierIds(){
    var ids = [];
    supplierFields.querySelectorAll('.supplier-field-row').forEach(function(r){
        var v = r.querySelector('select').value;
        if(v) ids.push(v);
    });
    return ids;
}

function renderSupplierBadges(el, names){
    if(!el) return;
    if(names && names.length){
        el.innerHTML = names.map(function(n){
            return '<span class="supp-badge"><i class="fas fa-store"></i> ' + escHtml(n) + '</span>';
        }).join('');
    } else {
        el.innerHTML = '<span class="supp-badge"><i class="fas fa-store"></i> -</span>';
    }
}

function setSupplierRows(ids){
    ids = ids || [];
    var need = ids.length > 0 ? ids.length : 1;
    var rows = supplierFields.querySelectorAll('.supplier-field-row');
    while(rows.length < need){
        supplierFields.appendChild(supplierTemplate.content.cloneNode(true));
        rows = supplierFields.querySelectorAll('.supplier-field-row');
    }
    // Isi opsi dulu agar .value bisa diterapkan pada row kloningan
    refreshAllSupplierRows();
    for(var i = 0; i < rows.length; i++){
        if(i < need){
            rows[i].querySelector('select').value = ids[i] || '';
        } else {
            rows[i].remove();
        }
    }
    refreshAllSupplierRows();
}

document.getElementById('btnAddSupplier').addEventListener('click', function(){
    if(!supplierFields || !supplierTemplate) return;
    var clone = supplierTemplate.content.cloneNode(true);
    var row = clone.querySelector('.supplier-field-row');
    supplierFields.appendChild(clone);
    refreshAllSupplierRows();
    if(row){
        try { row.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        catch(e){}
        var sel = row.querySelector('select');
        if(sel) sel.focus();
    }
});

supplierFields.addEventListener('change', function(e){
    if(e.target.tagName === 'SELECT'){
        refreshAllSupplierRows();
    }
});

supplierFields.addEventListener('click', function(e){
    var btn = e.target.closest('[data-remove]');
    if(!btn) return;
    var row = btn.closest('.supplier-field-row');
    if(row) row.remove();
    refreshAllSupplierRows();
});

// Inisialisasi opsi dropdown sesuai data awal
refreshAllSupplierRows();

// Update action — in-place tanpa refresh
document.getElementById('editProductForm').addEventListener('submit', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    var id = formData.get('id');

    fetch('master-product-action.php?action=update&id=' + id, {
        method: 'POST',
        body: formData
    })
    .then(function(res){ return res.json(); })
    .then(function(res){
        if(res.status === 'success'){
            try {
                // Ambil nilai baru dari form
                var newCode = formData.get('code');
                var newName = formData.get('name');
                var newCat  = formData.get('category');
                var newPrice = formData.get('price');

                // Update hero card
                document.getElementById('heroName').textContent = newName;
                document.getElementById('heroCode').textContent = newCode;
                document.getElementById('heroCategory').textContent = newCat;
                document.getElementById('heroPrice').textContent = 'Rp ' + parseInt(newPrice).toLocaleString('id-ID');

                // Update supplier (bisa lebih dari satu)
                var supNames = getSelectedSupplierNames();
                renderSupplierBadges(document.getElementById('heroSupplierName'), supNames);
                renderSupplierBadges(document.getElementById('infoSupplier'), supNames);

                // Update info card
                document.getElementById('infoCode').textContent = newCode;
                document.getElementById('infoName').textContent = newName;
                document.getElementById('infoCategory').textContent = newCat;
                document.getElementById('infoPrice').textContent = 'Rp ' + parseInt(newPrice).toLocaleString('id-ID');

                // Update foto hero jika ada file baru
                var photoFile = formData.get('photo');
                if(photoFile && photoFile.name){
                    var reader = new FileReader();
                    reader.onload = function(ev){
                        var heroImg = document.querySelector('.hero-photo img');
                        if(heroImg){
                            heroImg.src = ev.target.result;
                        } else {
                            var placeholder = document.querySelector('.hero-photo-placeholder');
                            if(placeholder){
                                placeholder.outerHTML = '<img src="' + ev.target.result + '" alt="' + newName + '">';
                            }
                        }
                    };
                    reader.readAsDataURL(photoFile);
                }

                // Tutup edit card & scroll ke atas
                document.getElementById('editCard').classList.remove('show');
                setTimeout(function(){
                    try { contentEl.scrollTo({ top: 0, behavior: 'smooth' }); }
                    catch(e){ contentEl.scrollTop = 0; }
                }, 150);
            } catch(e){
                console.error(e);
            }
            QToast('Berhasil', 'Data produk berhasil diperbarui', 'success');
        } else {
            QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(function(){ QToast('Error', 'Gagal memproses update', 'error'); });
});

// Delete action
document.getElementById('btnDeleteProduct').addEventListener('click', function(){
    var id = this.getAttribute('data-id') || <?= $d['id'] ?>;
    var nameEl = document.getElementById('heroName');
    var name = nameEl ? nameEl.textContent : 'produk ini';

    QConfirm('Hapus Produk?', 'Produk ' + name + ' akan dihapus permanen.', {
        confirmText: 'Hapus',
        icon: 'fa-trash-can',
        confirmClass: 'q-confirm-btn-danger',
        iconClass: 'q-confirm-icon-danger'
    }).then(function(ok){
        if(ok){
            fetch('master-product-action.php?action=destroy', {
                method: 'POST',
                body: new URLSearchParams({ id: id })
            })
            .then(function(res){ return res.json(); })
            .then(function(res){
                if(res.status === 'success'){
                    QToast('Terhapus', 'Produk berhasil dihapus', 'success');
                    setTimeout(function(){
                        window.location.href = BASE_URL + '/pages/master/master-product.php';
                    }, 600);
                }
            });
        }
    });
});

// QUICK PRODUCT SEARCH — AJAX tanpa reload
(function(){
    var input = document.getElementById('quickProductSearch');
    var dropdown = document.getElementById('searchResultDropdown');
    var clearBtn = document.getElementById('clearSearchBtn');
    var searchTimeout = null;

    if(!input || !dropdown) return;

    // Fungsi update semua konten halaman
    function switchProduct(item){
        // Hero photo
        var heroImg = document.querySelector('.hero-photo img');
        var heroPh = document.querySelector('.hero-photo-placeholder');
        if(item.photo){
            if(heroImg){ heroImg.src = item.photo; heroImg.alt = item.name; }
            else if(heroPh){ heroPh.outerHTML = '<img src="' + item.photo + '" alt="' + item.name + '" style="width:100%;height:100%;object-fit:cover;">'; }
        }

        // Hero info
        var set = function(id, val){ var el = document.getElementById(id); if(el) el.textContent = val; };
        set('heroName', item.name);
        set('heroCode', item.code);
        set('heroCategory', item.category);
        set('heroPrice', 'Rp ' + item.priceFormatted);
        var supNames = item.supplierNames || [];
        renderSupplierBadges(document.getElementById('heroSupplierName'), supNames);
        renderSupplierBadges(document.getElementById('infoSupplier'), supNames);

        // Info card
        set('infoCode', item.code);
        set('infoName', item.name);
        set('infoCategory', item.category);
        set('infoPrice', 'Rp ' + item.priceFormatted);

        // Stats
        var sv = document.querySelectorAll('.stat-value');
        if(sv.length >= 3){
            sv[0].textContent = item.totalTransaksi;
            sv[1].textContent = item.totalQtyFormatted;
            sv[2].textContent = 'Rp ' + item.priceFormatted;
        }

        // Edit form id
        var idInput = document.querySelector('#editProductForm input[name="id"]');
        if(idInput) idInput.value = item.id;

        // Update edit form fields
        var setForm = function(name, val){ var el = document.querySelector('#editProductForm [name="' + name + '"]'); if(el) el.value = val; };
        setForm('code', item.code);
        setForm('name', item.name);
        setForm('category', item.category);
        setForm('price', item.price);

        // Update category select
        var catSelect = document.querySelector('#editProductForm select[name="category"]');
        if(catSelect){
            var catLower = (item.category || '').toLowerCase();
            for(var i = 0; i < catSelect.options.length; i++){
                if(catSelect.options[i].value.toLowerCase() === catLower){
                    catSelect.selectedIndex = i;
                    break;
                }
            }
        }

        // Update supplier select (sesuaikan jumlah row dengan supplier produk)
        setSupplierRows(item.supplierIds || (item.supplierId ? [item.supplierId] : []));

        // Update photo preview
        var photoPrev = document.getElementById('editPhotoPreview');
        if(photoPrev && item.photo){
            photoPrev.innerHTML = '<img src="' + item.photo + '" alt="Preview">';
        }

        // Delete button
        var delBtn = document.getElementById('btnDeleteProduct');
        if(delBtn) delBtn.setAttribute('data-id', item.id);

        // URL & title
        window.history.pushState({}, '', BASE_URL + '/pages/master/master-product-detail.php?id=' + item.id);
        document.title = item.name + ' - Detail Produk';

        // Animasi hero card
        var heroCard = document.querySelector('.hero-card');
        if(heroCard){ heroCard.style.animation = 'none'; heroCard.offsetHeight; heroCard.style.animation = 'cardEnter .5s ease both'; }

        QToast('Berhasil', 'Beralih ke: ' + item.name, 'success');
    }

    input.addEventListener('input', function(){
        var val = this.value.trim();
        if(clearBtn) clearBtn.style.display = val.length > 0 ? 'block' : 'none';

        if(searchTimeout) clearTimeout(searchTimeout);

        if(val.length < 1){
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(function(){
            var url = BASE_URL + '/pages/master/master-product-search.php?q=' + encodeURIComponent(val);
            fetch(url)
            .then(function(res){ return res.json(); })
            .then(function(data){
                if(data.length === 0){
                    dropdown.innerHTML = '<div class="search-item-empty"><i class="fas fa-search"></i>Produk tidak ditemukan</div>';
                } else {
                    var html = '';
                    data.forEach(function(item){
                        var photoHtml = item.photo
                            ? '<img src="' + item.photo + '" class="search-item-img">'
                            : '<div class="search-item-icon"><i class="fas fa-box"></i></div>';

                        html += '<div class="search-item-link" data-id="' + item.id + '" data-name="' + (item.name||'').replace(/"/g,'&quot;') + '">' +
                            photoHtml +
                            '<div class="search-item-info">' +
                                '<div class="search-item-name">' + item.name + '</div>' +
                                '<div class="search-item-meta"><span>' + item.code + '</span><span>' + item.category + '</span></div>' +
                            '</div>' +
                            '<div class="search-item-price">Rp ' + item.priceFormatted + '</div>' +
                        '</div>';
                    });
                    dropdown.innerHTML = html;
                }
                dropdown.style.display = 'block';
            })
            .catch(function(err){ console.log('Search error:', err); });
        }, 250);
    });

    // Klik item → AJAX switch tanpa reload
    dropdown.addEventListener('click', function(e){
        var link = e.target.closest('.search-item-link');
        if(!link) return;

        var itemId = link.getAttribute('data-id');

        fetch(BASE_URL + '/pages/master/master-product-search.php?q=' + encodeURIComponent(link.getAttribute('data-name')))
        .then(function(res){ return res.json(); })
        .then(function(data){
            var found = null;
            for(var i = 0; i < data.length; i++){
                if(data[i].id == itemId){ found = data[i]; break; }
            }
            if(found) switchProduct(found);
        });

        dropdown.style.display = 'none';
        dropdown.innerHTML = '';
        input.value = '';
        if(clearBtn) clearBtn.style.display = 'none';
    });

    input.addEventListener('focus', function(){
        if(this.value.trim().length > 0 && dropdown.innerHTML !== ''){
            dropdown.style.display = 'block';
        }
    });

    if(clearBtn){
        clearBtn.addEventListener('click', function(){
            input.value = '';
            clearBtn.style.display = 'none';
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            input.focus();
        });
    }

    document.addEventListener('click', function(e){
        var wrap = document.querySelector('.product-search-wrap');
        if(wrap && !wrap.contains(e.target)){
            dropdown.style.display = 'none';
        }
    });
})();
</script>

</body>
</html>