<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Master Produk - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product-detail.css?v=<?= filemtime(__DIR__ . '/../../css/pages/master-product-detail.css') ?>">
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
                </div>

                <div class="mt-4 px-4">
                    <!-- Button Add -->
                    <div id="btnContainer" style="display:none;">
                        <button
                            type="button"
                            class="btn btn-primary"
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
        });

    });

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

</body>
</html>