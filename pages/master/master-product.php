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
        <div class="col-md-12 mb-3">
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
    <div class="row master-product-last-row">
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

                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-plus"></i>
                        </div>

                        <div class="mp-add-head-text">
                            <div class="panel-title">
                                Tambah Produk
                            </div>
                            <div class="panel-subtitle">
                                Tambah kode, nama, kategori, foto, dan harga jual produk
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5 mp-add-body" id="addProductContent"></div>
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

                        <div class="combine-head-text">
                            <div class="panel-title" id="combineModalTitle">
                                Tambah Racikan
                            </div>
                            <div class="panel-subtitle combine-modal-subtitle">
                                Nama paket + pilih produk bahan
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white combine-modal-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="mt-2 px-5 combine-modal-body" id="combineFormContent"></div>
            </div>
        </div>
    </div>

<!-- STOCK SETTING MODAL -->
    <div class="modal fade" id="stockSettingModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3 mp-add-header" id="stockSettingHeader">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-gauge-high"></i>
                        </div>

                        <div class="mp-add-head-text">
                            <div class="panel-title">
                                Batas Stok Global
                            </div>
                            <div class="panel-subtitle">
                                Default batas stok menipis untuk produk baru
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="mt-2 px-5 mp-add-body">
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

                    <div class="mp-add-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-save" id="btnSaveStockSetting">
                            <i class="fas fa-save"></i>
                            Save
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
    const PRODUCT_MOBILE_BP = 575.98;

    function isProductMobile(){
        return window.innerWidth <= PRODUCT_MOBILE_BP;
    }

    let productIsMobile = isProductMobile();

    /* Tombol "Tambah Produk" & search dimasukkin ke wrapper yang sama.
       Dipanggil ulang setelah setiap init karena wrapper ikut hilang
       saat DataTable di-destroy. */
    function placeProductActions(){
        const filter = $('#stockTable_filter');
        if(!filter.length) return;

        if(!filter.parent().hasClass('table-action-wrapper')){
            filter.wrap('<div class="table-action-wrapper"></div>');
        }

        $('#btnContainer').show().appendTo(filter.parent());
    }

    function productDataTable(){
        const mobile = isProductMobile();

        const options = {
            pageLength: mobile ? 4 : 5,
            lengthMenu: mobile
                ? [[4,5,10,25,50],[4,5,10,25,50]]
                : [[5,10,25,50],[5,10,25,50]],

            /* pager disamakan dengan tabel Stok Kantin (simple_numbers) */
            pagingType: mobile ? "simple_numbers" : "full_numbers",
            searchDelay: 250,

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
            },

            order: []
        };

        /* "dom" HANYA di-set untuk mobile. Di desktop/tablet default
           DataTables Bootstrap 5-lah yang dipakai:
           "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row...>"
           Wrapper row/col itu yang menaruh "Show entries" (l) dan
           search + tombol (f) dalam satu baris. Kalau "dom" di-set
           manual, wrapper tersebut hilang dan search/tombol turun ke
           baris sendiri. */
        if(mobile) options.dom = "ftp";

        const dt = $('#stockTable').DataTable(options);

        dt.columns.adjust();
        placeProductActions();

        return dt;
    }

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
                productIsMobile = isProductMobile();
                productDataTable();
            },100);
        });
    }

    /* 🔥 REBUILD TABEL KETIKA GANTI MOBILE / DESKTOP */
    let productResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(productResizeTimer);
        productResizeTimer = setTimeout(function(){
            if(isProductMobile() === productIsMobile) return;
            if(!$.fn.DataTable.isDataTable('#stockTable')) return;

            const current = $('#stockTable').DataTable();
            const page = current.page();
            const keyword = current.search();

            // tombol dikeluarkan dulu supaya tidak ikut hilang bersama wrapper
            $('#btnContainer').appendTo('#productTableContainer');
            current.destroy();

            productIsMobile = isProductMobile();
            const dt = productDataTable();
            dt.search(keyword).page(page).draw(false);
        }, 250);
    });

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

        // Hancurkan select2 supplier dari render sebelumnya DULU, sebelum
        // #addProductContent di-replace. Dropdown-nya nempel ke modal
        // (dropdownParent), bukan ke content, jadi kalau tidak dihancurkan
        // dia akan nyangkut jadi node Animation di dalam modal.
        destroyAddSupplierSelect2();
        ADD_SUPPLIER_STATE.fields = null;

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
            initAddCodeValidation();
        });

    });

    // Bootstrap modal enforceFocus block input di luar modal body. Select2
    // search field di mobile (dropdownParent = body) harus di-allow.
    // Capture: true supaya handler ini jalan SEBELUM handler Bootstrap.
    document.addEventListener('focusin', function(e){
        if(e.target.closest && e.target.closest('.select2-search__field')){
            e.stopImmediatePropagation();
            e.stopPropagation();
        }
    }, true);

    //php:placeholder

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

    // Validasi realtime kode produk
    let addCodeCheckTimeout = null;
    function initAddCodeValidation(){
        const input = document.getElementById('addProductCode');
        const errorBox = document.getElementById('addCodeError');
        const errorMsg = document.getElementById('addCodeErrorMsg');
        const spinner = document.getElementById('addCodeSpinner');
        if(!input || !errorBox || !errorMsg) return;

        input.addEventListener('input', function(){
            const code = this.value.trim();
            
            if(addCodeCheckTimeout) clearTimeout(addCodeCheckTimeout);
            
            if(!code){
                errorBox.classList.add('d-none');
                spinner.classList.add('d-none');
                input.classList.remove('is-invalid');
                return;
            }

            spinner.classList.remove('d-none');

            addCodeCheckTimeout = setTimeout(function(){
                fetch('master-product-action.php?action=check_code', {
                    method: 'POST',
                    body: new URLSearchParams({ code: code })
                })
                .then(res => res.json())
                .then(res => {
                    spinner.classList.add('d-none');
                    if(res.exists){
                        errorBox.classList.remove('d-none');
                        errorMsg.textContent = 'Kode "' + code + '" sudah terdaftar untuk produk lain';
                        input.classList.add('is-invalid');
                    } else {
                        errorBox.classList.add('d-none');
                        input.classList.remove('is-invalid');
                    }
                })
                .catch(() => {
                    spinner.classList.add('d-none');
                    errorBox.classList.add('d-none');
                    input.classList.remove('is-invalid');
                });
            }, 500);
        });
    }

    // ===== MULTI SUPPLIER (sama seperti halaman detail) =====
    let ADD_SUPPLIER_STATE = { fields:null, template:null, list:[] };

    function ADD_SUPPLIER_MOBILE(){
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    // ===== SELECT2 UNTUK PICK SUPPLIER =====
    // Opsi <select> asli tetap jadi sumber data (dibaca .value saat submit),
    // select2 hanya mengganti tampilannya jadi bisa diketik & dicari.
    function addSupplierSelect2Config(){
        return {
            width: '100%',
            placeholder: 'Cari / pilih supplier...',
            allowClear: true,
            // Mobile: dropdown harus nempel di <body>. Dropdown yang parent-nya
            // modal di-append di dalam .modal-dialog, yang punya overflow, jadi
            // search field-nya.zero-height dan tidak bisa diketik.
            dropdownParent: $('#addProductModal').length && !ADD_SUPPLIER_MOBILE()
                ? $('#addProductModal')
                : document.body,
            language: {
                noResults: function(){ return 'Supplier tidak ditemukan'; },
                searching: function(){ return 'Mencari...' },
                inputTooShort: function(){ return 'Ketik nama supplier...' }
            }
        };
    }

    //php:placeholder

    function isAddSupplierSelect2(sel){
        return !!($.fn.select2) && $(sel).hasClass('select2-hidden-accessible');
    }

    // Hancurkan select2 di dalam scope (atau semua row kalau scope tidak diisi).
    // Wajib sebelum sel.innerHTML diganti: select2 menyimpan hasil render-nya
    // di sibling terpisah yang tidak ikut ter-update saat option di rebuild.
    function destroyAddSupplierSelect2(scope){
        let root = (scope && scope.querySelectorAll) ? scope : ADD_SUPPLIER_STATE.fields;
        if(!root) return;
        root.querySelectorAll('select').forEach(sel=>{
            if(isAddSupplierSelect2(sel)) $(sel).select2('destroy');
        });
    }

    function initAddSupplierSelect2(){
        if(!$.fn.select2 || !ADD_SUPPLIER_STATE.fields) return;
        ADD_SUPPLIER_STATE.fields.querySelectorAll('select').forEach(sel=>{
            if(isAddSupplierSelect2(sel)) return;
            var cfg = addSupplierSelect2Config();
            $(sel).select2(cfg);
            // Mobile: prevent native select dari override select2
            if(ADD_SUPPLIER_MOBILE()){
                $(sel).on('mousedown touchstart', function(e){
                    if($(this).hasClass('select2-hidden-accessible')){
                        e.preventDefault();
                        $(this).select2('open');
                    }
                });
            }
        });
    }

    // Select2 kadang menyisakan container dropdown yatim: 0x0, tidak terlihat, tapi
    // tetap menempel di modal. Contohnya saat tombol clear diklik, event
    // mouseup/click berikutnya mendarat di container select2 yang baru dibuat lalu
    // dropdown-nya dibuka, sementara instance yangDicetak sudah di-destroy.
    // Node yatim begitu tidak bisa ditutup lagi karena instance pemiliknya sudah
    // tidak ada, jadi harus dibersihkan manual.
    function sweepAddSupplierSelect2Orphans(){
        if(!$.fn.select2) return;
        let host = document.getElementById('addProductModal');
        if(!host) return;

        // dropdown yang masih "hidup" = milik instance select2 yang aktif
        let live = new Set();
        document.querySelectorAll('select.select2-hidden-accessible').forEach(sel=>{
            let box = window.qieosSelect2Container(sel);
            if(box) live.add(box);
        });

        // cuma anak langsung dari modal; selection box (yang di-select sebelumnya) dilewati
        Array.prototype.forEach.call(host.children, node=>{
            if(!node.classList || !node.classList.contains('select2-container')) return;
            if(live.has(node)) return;
            node.remove();
        });
    }

    $(document).on('click', sweepAddSupplierSelect2Orphans);

    // Select2 bisa menyisakan dropdown yang keburu terbuka — mis. saat event
    // "mousedown" tombol clear masih berjalan lalu select2 di-rebuild. Tutup
    // semua dropdown supaya hasil refresh selalu deterministik: tertutup.
    // Baris yang memang mau terbuka dibuka eksplisit di handler btnAddSupplier.
    function closeAddSupplierSelect2Dropdowns(scope){
        let root = (scope && scope.querySelectorAll) ? scope : ADD_SUPPLIER_STATE.fields;
        if(!root || !$.fn.select2) return;
        root.querySelectorAll('select').forEach(sel=>{
            if(isAddSupplierSelect2(sel)) $(sel).select2('close');
        });
    }

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

        destroyAddSupplierSelect2(ADD_SUPPLIER_STATE.fields);

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

        initAddSupplierSelect2();
        closeAddSupplierSelect2Dropdowns(ADD_SUPPLIER_STATE.fields);
        sweepAddSupplierSelect2Orphans();
    }

    $(document).on('click','#addProductContent #btnAddSupplier',function(){
        if(!ADD_SUPPLIER_STATE.fields || !ADD_SUPPLIER_STATE.template) return;
        ADD_SUPPLIER_STATE.fields.appendChild(ADD_SUPPLIER_STATE.template.content.cloneNode(true));
        refreshAddSupplierRows();
        /* Dropdown sengaja TIDAK dibuka di sini. User baru melihat daftar
           supplier setelah klik field select-nya sendiri. */
        const rows = ADD_SUPPLIER_STATE.fields.querySelectorAll('.supplier-field-row');
        const last = rows[rows.length - 1];
        if(last){
            const sel = last.querySelector('select');
            if(sel && !isAddSupplierSelect2(sel)) sel.focus();
        }
    });

    $(document).on('click','#addProductContent [data-remove]',function(){
        const row = this.closest('.supplier-field-row');
        if(row){
            destroyAddSupplierSelect2(row);
            row.parentNode.removeChild(row);
        }
        refreshAddSupplierRows();
    });

    $(document).on('change','#addProductContent #supplierFields select',function(){
        // Ditunda satu tick: 'change' dipancarkan select2 dari dalam handler
        // mouseup miliknya sendiri, jadi destroy() di tengah dispatch itu
        // menyisakan container dropdown yatim di dalam modal.
        setTimeout(refreshAddSupplierRows, 0);
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

        const codeInput = document.getElementById('addProductCode');
        if(codeInput && codeInput.classList.contains('is-invalid')){
            QToast('Gagal', 'Kode produk sudah digunakan, gunakan kode lain', 'error');
            codeInput.focus();
            return;
        }

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
    // 'required' sengaja tidak dipakai: select2 menyembunyikan <select> asli
    // jadi validasi HTML5 tidak bisa menunjuk field yang bermasalah.
    // Kekosongan dicek manual di handler submit.
    return '<div class="combine-item-row">' +
        '<select name="product_id[]" class="form-input">' + opts + '</select>' +
        '<span class="cb-row-price">' + rupiahN(0) + '</span>' +
        '<button type="button" class="cb-row-remove" title="Hapus bahan" aria-label="Hapus bahan"><i class="fas fa-times"></i></button>' +
    '</div>';
}

// ===== SELECT2 UNTUK PILIH PRODUK BAHAN =====
// Opsi <select> asli tetap jadi sumber data (dibaca .value saat submit),
// select2 hanya mengganti tampilannya jadi bisa diketik & dicari.
function combineProductSelect2Config(){
    return {
        width: '100%',
        placeholder: 'Cari / pilih produk...',
        // dropdown di-append ke dalam #combineFormContent (ruat konten modal),
        // BUKAN ke #combineModal. Kalau ke #combineModal, select2 menempelkan
        // dropdown SEBELUM .modal-dialog (paling atas modal), lalu browser
        // meng-scroll modal ke atas agar search field terlihat -> modal melompat
        // ke atas setiap kali dropdown dibuka. Di dalam content, dropdown
        // menempel tepat di bawah select & ikut scroll modal.
        dropdownParent: $('#combineFormContent'),
        minimumResultsForSearch: 0,
        language: {
            noResults: function(){ return 'Produk tidak ditemukan'; },
            searching: function(){ return 'Mencari...' },
            inputTooShort: function(){ return 'Ketik nama produk...' }
        }
    };
}

function isCombineSelect2(sel){
    return !!($.fn.select2) && $(sel).hasClass('select2-hidden-accessible');
}

// Hancurkan select2 di dalam scope. WAJIB sebelum node select-nya dilepas:
// container select2 hidup di <body>, bukan di dalam row, jadi ikut yatim.
function destroyCombineSelect2(scope){
    let root = (scope && scope.querySelectorAll) ? scope : COMBINE.items;
    if(!root) return;
    root.querySelectorAll('select').forEach(sel=>{
        if(isCombineSelect2(sel)) $(sel).select2('destroy');
    });
}

function initCombineSelect2(scope){
    if(!$.fn.select2) return;
    let root = (scope && scope.querySelectorAll) ? scope : COMBINE.items;
    if(!root) return;
    root.querySelectorAll('select').forEach(sel=>{
        if(isCombineSelect2(sel)) return;
        $(sel).select2(combineProductSelect2Config());
    });
}

// Select2 menyisakan container dropdown yatim: 0x0, tidak terlihat, tapi tetap
// menempel di <body> (mis. clear diklik lalu dropdown-nya di-rebuild). Node
// yatim tidak bisa ditutup lagi karena instance pemiliknya sudah tidak ada.
function sweepCombineSelect2Orphans(){
    if(!$.fn.select2) return;

    let live = new Set();
    document.querySelectorAll('select.select2-hidden-accessible').forEach(sel=>{
        let box = window.qieosSelect2Container(sel);
        if(box) live.add(box);
    });

    Array.prototype.forEach.call(document.body.children, node=>{
        if(!node.classList || !node.classList.contains('select2-container')) return;
        if(live.has(node)) return;
        node.remove();
    });
}

$(document).on('click', sweepCombineSelect2Orphans);

function addCombineRow(selectedId){
    if(!COMBINE.items) return null;
    var wrap = document.createElement('div');
    wrap.innerHTML = combineRowHtml(selectedId);
    var row = wrap.firstChild;
    COMBINE.items.appendChild(row);
    initCombineSelect2(row);
    recomputeCombineTotal();
    return row;
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
    if(COMBINE.items){
        // dropdown select2 menempel di <body>, jadi harus dihancurkan dulu
        // sebelum isi modal diganti
        destroyCombineSelect2(COMBINE.items);
        COMBINE.items.innerHTML = '';
    }

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

    // form racikan sebelumnya masih punya select2 hidup di <body>
    destroyCombineSelect2(document.getElementById('combineFormContent'));

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

// Dropdown select2 racikan menempel di <body>, jadi harus dibersihkan saat
// modal ditutup - kalau tidak, dropdown-nya melayang di atas halaman.
document.getElementById('combineModal').addEventListener('hidden.bs.modal', function(){
    destroyCombineSelect2(document.getElementById('combineFormContent'));
    sweepCombineSelect2Orphans();
});
$(document).on('click', '#combineItems .cb-row-remove', function(){
    var row = this.closest('.combine-item-row');
    if(row){
        destroyCombineSelect2(row);
        row.remove();
    }
    recomputeCombineTotal();
});
$(document).on('click', '#btnCombineAdd', function(){
    addCombineRow('');
});
$(document).on('change', '#combineItems select', function(){
    recomputeCombineTotal();
});

$(document).on('submit', '#combineForm', function(e){
    e.preventDefault();

    // validasi manual bahan racikan (lihat catatan di combineRowHtml)
    var firstEmpty = null;
    $('#combineItems select').each(function(){
        if(!this.value && !firstEmpty) firstEmpty = this;
    });
    if(firstEmpty){
        QToast('Gagal', 'Pilih produk untuk semua bahan racikan', 'error');
        setTimeout(function(){
            if(!isCombineSelect2(firstEmpty)) return;
            try{ $(firstEmpty).select2('open'); }catch(err){}
        }, 0);
        return;
    }

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