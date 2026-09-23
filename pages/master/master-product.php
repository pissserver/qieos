<?php
include '../../sessions/session.php';
include __DIR__ . '/../components/data/stock-status.php';
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Master Produk - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product.css?v=<?= filemtime(__DIR__ . '/../../css/pages/master-product.css') ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product-detail.css?v=<?= filemtime(__DIR__ . '/../../css/pages/master-product-detail.css') ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-combine.css?v=<?= filemtime(__DIR__ . '/../../css/pages/master-combine.css') ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>

<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <div class="row">
        <div class="col-md-12 mb-5">
            <!-- Main Table -->
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Master Produk
                            </div>
                            <div class="panel-subtitle">
                                Kelola data produk, harga jual, dan foto produk
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn btn-stock-global"
                        id="btnStockSetting"
                        title="Atur batas stok menipis default untuk semua produk baru">
                        <i class="fas fa-gauge-high me-2"></i>
                        Batas Stok Global
                    </button>
                </div>

                <div class="mt-4 px-4">
                    <!-- Button Add -->
                    <div id="btnContainer" style="display:none;">
                        <button
                            type="button"
                            class="btn mu-add-btn"
                            id="btnAddProduct">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Produk
                        </button>
                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive-wrap" id="productTableContainer">
                        <!-- Loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RACIKAN / COMBINE PRODUK -->
    <div class="row">
        <div class="col-md-12 mb-5">
            <div class="section-card mb-4 combine-panel">
                <div class="panel-header panel-primary combine-header">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-blender"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Racikan / Combine Produk
                            </div>
                            <div class="panel-subtitle">
                                Gabungkan beberapa produk jadi satu paket, harga total dihitung otomatis dari tiap produk
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn btn-stock-global js-add-combine"
                        id="btnAddCombine">
                        <i class="fas fa-blender me-2"></i>
                        Tambah Racikan
                    </button>
                </div>

                <div class="mt-4 px-4 combine-body" id="combineContent">
                    <div class="text-center py-4 text-secondary">
                        <i class="fas fa-spinner fa-spin"></i> Memuat racikan...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-plus"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Tambah Produk
                            </div>
                            <div class="panel-subtitle">
                                Tambah kode, nama, kategori, foto, dan harga jual produk
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="addProductContent"></div>
            </div>
        </div>
    </div>

<!-- COMBINE MODAL -->
    <div class="modal fade" id="combineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-primary my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-blender"></i>
                        </div>

                        <div>
                            <div class="panel-title" id="combineModalTitle">
                                Tambah Racikan
                            </div>
                            <div class="panel-subtitle">
                                Nama paket + pilih produk bahan, total harga dihitung otomatis
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="mt-2 px-5" id="combineFormContent"></div>
            </div>
        </div>
    </div>

<!-- STOCK SETTING MODAL -->
    <div class="modal fade" id="stockSettingModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-primary my-3 mx-3" id="stockSettingHeader">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-gauge-high"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Batas Stok Global
                            </div>
                            <div class="panel-subtitle">
                                Default batas stok menipis untuk semua produk baru
                            </div>
                        </div>
                    </div>

                    <button type="button" class="modal-x-close" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mt-2 px-5 pb-4">
                    <label class="form-label"><i class="fas fa-gauge-high me-1"></i> Batas Stok Menipis (default)</label>
                    <div class="input-group-modern">
                        <div class="input-icon">
                            <i class="fas fa-gauge-high"></i>
                        </div>
                        <input
                            type="number"
                            id="lowStockDefaultInput"
                            class="form-control"
                            value="<?= (int)get_low_stock_default($conn) ?>"
                            min="0">
                    </div>
                    <small class="text-muted d-block" style="font-size:11px;">
                        Status stok: 0 = Habis, 1 s/d batas = Menipis, di atas batas = Ready. Produk lama memakai batasnya sendiri, produk baru memakai nilai ini.
                    </small>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnSaveStockSetting">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
    function loadProductTable(){
        $('#btnContainer').hide().insertBefore('#productTableContainer');
        fetch('master-product-table.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('productTableContainer').innerHTML = html;

            // Destroy old DataTable first
            if($.fn.DataTable.isDataTable('#stockTable')){
                $('#stockTable').DataTable().destroy();
            }

            // Reinit DataTable
            setTimeout(()=>{
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
                                Silakan tambahkan produk terlebih dahulu
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
            },100);
        });
    }

    $(document).ready(function(){
        loadProductTable();
        loadCombineContent();
    });
</script>

<!-- Script Add -->
<script>
    // Add Modal
    $(document).on('click','#btnAddProduct',function(){

        $('#addProductModal').modal('show');

        document.getElementById('addProductContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('master-product-add.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('addProductContent').innerHTML = html;
            initAddProductSuppliers();
            bindAddPhotoBox();
            initAddCategoryToggle();
        });

    });

    // Kategori "Additional" di form tambah: sembunyikan satuan, batas stok & supplier
    function initAddCategoryToggle(){
        const content = document.getElementById('addProductContent');
        if(!content) return;
        const cat = content.querySelector('select[name="category"]');
        if(!cat) return;
        const ids = ['addFieldUnit','addFieldLowStock','addFieldSupplier'];
        const toggle = function(){
            const isAdd = (cat.value || '').toLowerCase() === 'additional';
            ids.forEach(id => {
                const el = content.querySelector('#' + id);
                if(el) el.style.display = isAdd ? 'none' : '';
            });
        };
        cat.addEventListener('change', toggle);
        toggle();
    }

    // ===== MULTI SUPPLIER (sama seperti halaman detail) =====
    let ADD_SUPPLIER_STATE = { fields:null, template:null, list:[] };

    function initAddProductSuppliers(){
        const content = document.getElementById('addProductContent');
        const fields = content.querySelector('#supplierFields');
        const template = content.querySelector('#supplierFieldTemplate');
        ADD_SUPPLIER_STATE.fields = fields;
        ADD_SUPPLIER_STATE.template = template;
        ADD_SUPPLIER_STATE.list = [];
        if(!fields) return;
        // Ambil daftar supplier dari opsi select pertama (render awal berisi semua)
        const firstSel = fields.querySelector('select');
        if(firstSel){
            for(let i = 0; i < firstSel.options.length; i++){
                const o = firstSel.options[i];
                if(o.value !== '') ADD_SUPPLIER_STATE.list.push({ id: o.value, name: o.text });
            }
        }
        refreshAddSupplierRows();
    }

    function addSupplierOptionsHtml(selectedId, excludeIds){
        const esc = s => String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        let html = '<option value="">Supplier (opsional)</option>';
        for(let i = 0; i < ADD_SUPPLIER_STATE.list.length; i++){
            const s = ADD_SUPPLIER_STATE.list[i];
            const sid = String(s.id);
            if(excludeIds.indexOf(sid) >= 0) continue;
            const sel = sid === String(selectedId) ? ' selected' : '';
            html += '<option value="' + s.id + '"' + sel + '>' + esc(s.name) + '</option>';
        }
        return html;
    }

    function refreshAddSupplierRows(){
        if(!ADD_SUPPLIER_STATE.fields) return;
        const rows = ADD_SUPPLIER_STATE.fields.querySelectorAll('.supplier-field-row');
        // Hilangkan duplikat nilai antar row
        const seen = {};
        rows.forEach(function(r){
            const sel = r.querySelector('select');
            if(sel.value && seen[sel.value] && sel.value !== ''){ sel.value = ''; }
            seen[sel.value || ''] = true;
        });
        // Bangun ulang opsi tiap row: exclude supplier yang dipilih di row lain
        rows.forEach(function(r){
            const sel = r.querySelector('select');
            const selected = sel.value;
            const others = [];
            rows.forEach(function(o){
                const s = o.querySelector('select');
                if(o !== r && s.value) others.push(s.value);
            });
            sel.innerHTML = addSupplierOptionsHtml(selected, others);
        });
    }

    $(document).on('click','#addProductContent #btnAddSupplier',function(){
        if(!ADD_SUPPLIER_STATE.fields || !ADD_SUPPLIER_STATE.template) return;
        ADD_SUPPLIER_STATE.fields.appendChild(ADD_SUPPLIER_STATE.template.content.cloneNode(true));
        refreshAddSupplierRows();
        const rows = ADD_SUPPLIER_STATE.fields.querySelectorAll('.supplier-field-row');
        const last = rows[rows.length - 1];
        if(last){
            const sel = last.querySelector('select');
            if(sel) sel.focus();
        }
    });

    $(document).on('click','#addProductContent [data-remove]',function(){
        const row = this.closest('.supplier-field-row');
        if(row) row.parentNode.removeChild(row);
        refreshAddSupplierRows();
    });

    $(document).on('change','#addProductContent #supplierFields select',function(){
        refreshAddSupplierRows();
    });

    // Klik area upload foto membuka dialog file (sama seperti detail)
    function bindAddPhotoBox(){
        const box = document.querySelector('#addProductContent .photo-upload-box');
        const input = document.querySelector('#addProductContent input[name="photo"]');
        if(box && input){
            box.addEventListener('click', function(e){
                if(input.contains(e.target)) return;
                input.click();
            });
        }
    }

    // Photo preview - Add
    $(document).on('change','#addProductContent input[name="photo"]',function(){
        const file = this.files[0];
        const preview = document.getElementById('addPhotoPreview');
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<i class="fas fa-camera"></i>';
        }
    });

    // Add Action
    $(document).on('submit','#addProductForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch('master-product-action.php?action=store',{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data produk berhasil ditambahkan', 'success');

                $('#addProductModal').modal('hide');

                loadProductTable();

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses tambah data', 'error');
        });
    });
</script>
<script>
    // Setting default batas stok menipis
    $(document).on('click','#btnStockSetting',function(){
        $('#stockSettingModal').modal('show');
    });

    $(document).on('click','#btnSaveStockSetting',function(){
        var val = $('#lowStockDefaultInput').val();
        var btn = $(this).prop('disabled', true);

        fetch('master-product-action.php?action=save_low_stock_default', {
            method:'POST',
            body: new URLSearchParams({ low_stock_default: val })
        })
        .then(res => res.json())
        .then(res => {
            btn.prop('disabled', false);
            if(res.status === 'success'){
                QToast('Berhasil', 'Default batas stok diperbarui', 'success');
                $('#stockSettingModal').modal('hide');
                loadProductTable();
            } else {
                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(() => {
            btn.prop('disabled', false);
            QToast('Error', 'Gagal menyimpan pengaturan', 'error');
        });
    });
</script>

<script>
// ===== RACIKAN / COMBINE PRODUK =====
function escHtml(s){
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function rupiahN(n){ return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID'); }

function loadCombineContent(){
    fetch('master-combine-content.php')
    .then(res => res.text())
    .then(html => {
        document.getElementById('combineContent').innerHTML = html;
    })
    .catch(() => {});
}

var COMBINE = { products: [], items: null, totalEl: null };

function priceOf(pid){
    for(var i = 0; i < COMBINE.products.length; i++){
        if(String(COMBINE.products[i].id) === String(pid)) return Number(COMBINE.products[i].sell_price) || 0;
    }
    return 0;
}

function combineRowHtml(selectedId){
    var opts = '<option value="">Pilih produk...</option>';
    for(var i = 0; i < COMBINE.products.length; i++){
        var p = COMBINE.products[i];
        var sel = String(p.id) === String(selectedId) ? ' selected' : '';
        var label = p.name + (p.code ? ' (' + p.code + ')' : '') + ' \u2014 ' + rupiahN(p.sell_price) + (p.unit ? ' / ' + p.unit : '');
        opts += '<option value="' + p.id + '"' + sel + '>' + escHtml(label) + '</option>';
    }
    return '<div class="combine-item-row">' +
        '<select name="product_id[]" class="form-input" required>' + opts + '</select>' +
        '<span class="cb-row-price">' + rupiahN(0) + '</span>' +
        '<button type="button" class="cb-row-remove" title="Hapus bahan" aria-label="Hapus bahan"><i class="fas fa-times"></i></button>' +
    '</div>';
}

function addCombineRow(selectedId){
    if(!COMBINE.items) return;
    var wrap = document.createElement('div');
    wrap.innerHTML = combineRowHtml(selectedId);
    COMBINE.items.appendChild(wrap.firstChild);
    recomputeCombineTotal();
}

function recomputeCombineTotal(){
    if(!COMBINE.items) return;
    var rows = COMBINE.items.querySelectorAll('.combine-item-row');
    var total = 0;
    rows.forEach(function(r){
        var sel = r.querySelector('select');
        var unit = priceOf(sel.value);
        total += unit;
        var pe = r.querySelector('.cb-row-price');
        if(pe) pe.textContent = rupiahN(unit);
    });
    if(COMBINE.totalEl) COMBINE.totalEl.textContent = rupiahN(total);
    var cnt = document.getElementById('combineCount');
    if(cnt) cnt.textContent = rows.length + ' bahan';
}

function initCombineForm(){
    var dataEl = document.getElementById('combineProductData');
    if(!dataEl) return;
    COMBINE.products = JSON.parse(dataEl.getAttribute('data-products') || '[]');
    COMBINE.items = document.getElementById('combineItems');
    COMBINE.totalEl = document.getElementById('combineTotalVal');
    var nameIn = document.getElementById('combineNameInput');
    if(nameIn) nameIn.value = '';
    if(COMBINE.items) COMBINE.items.innerHTML = '';

    // Mode edit: prefill nama + bahan yang sudah dipilih
    var comboJson = JSON.parse(dataEl.getAttribute('data-combo') || 'null');
    if(comboJson){
        if(nameIn) nameIn.value = comboJson.name;
        var pids = comboJson.items || [];
        if(COMBINE.items && pids.length > 0){
            pids.forEach(function(pid){ addCombineRow(String(pid)); });
        } else {
            addCombineRow('');
        }
    } else {
        addCombineRow('');
    }
}

function openCombineModal(id){
    var url = 'master-combine-form.php';
    if(id) url += '?id=' + encodeURIComponent(id);

    var titleEl = document.getElementById('combineModalTitle');
    if(titleEl) titleEl.textContent = id ? 'Edit Racikan' : 'Tambah Racikan';

    $('#combineModal').modal('show');
    document.getElementById('combineFormContent').innerHTML =
        '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch(url)
    .then(res => res.text())
    .then(html => {
        document.getElementById('combineFormContent').innerHTML = html;
        initCombineForm();
    });
}

$(document).on('click', '.js-add-combine', function(){ openCombineModal(); });
$(document).on('click', '.combine-edit', function(){ openCombineModal(this.getAttribute('data-id')); });
$(document).on('click', '#combineItems .cb-row-remove', function(){
    this.closest('.combine-item-row').remove();
    recomputeCombineTotal();
});
$(document).on('click', '#btnCombineAdd', function(){ addCombineRow(''); });
$(document).on('change', '#combineItems select', function(){
    recomputeCombineTotal();
});

$(document).on('submit', '#combineForm', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    var idIn = document.getElementById('combineIdInput');
    var action = idIn ? 'edit' : 'store';
    var titleEl = document.getElementById('combineModalTitle');
    if(titleEl) titleEl.textContent = idIn ? 'Edit Racikan' : 'Tambah Racikan';
    fetch('master-combine-action.php?action=' + action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success'){
            QToast('Berhasil', res.message, 'success');
            $('#combineModal').modal('hide');
            loadCombineContent();
        } else {
            QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => { QToast('Error', 'Gagal menyimpan racikan', 'error'); });
});

$(document).on('click', '.combine-delete', function(){
    var id = this.getAttribute('data-id');
    var card = document.getElementById('combineCard' + id);
    var name = card ? card.querySelector('.combine-card-name').textContent : 'racikan ini';
    QConfirm('Hapus Racikan?', 'Racikan "' + name + '" akan dihapus.', {
        confirmText: 'Hapus',
        icon: 'fa-trash-can',
        confirmClass: 'q-confirm-btn-danger',
        iconClass: 'q-confirm-icon-danger'
    }).then(function(ok){
        if(!ok) return;
        fetch('master-combine-action.php?action=destroy', {
            method: 'POST',
            body: new URLSearchParams({ id: id })
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success'){
                QToast('Terhapus', 'Racikan berhasil dihapus', 'success');
                loadCombineContent();
            } else {
                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
            }
        });
    });
});
</script>

</body>
</html>