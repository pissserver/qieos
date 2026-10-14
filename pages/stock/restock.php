<?php
include '../../sessions/session.php';

$qLast = mysqli_query($conn,"SELECT MAX(CAST(REPLACE(form, 'FORM-', '') AS UNSIGNED)) AS last_num FROM purchases");
$dLast = mysqli_fetch_assoc($qLast);
$nextNum = ($dLast && $dLast['last_num'] ? (int)$dLast['last_num'] : 0) + 1;
$formNumber = 'FORM-' . str_pad($nextNum,7,'0',STR_PAD_LEFT);

// Produk untuk dropdown (semua kategori kecuali Additional)
$qProd = mysqli_query($conn, "SELECT id, name, unit FROM products WHERE deleted_at IS NULL AND category <> 'Additional' ORDER BY name ASC");
$products = [];
while($p = mysqli_fetch_assoc($qProd)){
    $products[] = ['id' => (int)$p['id'], 'name' => $p['name'], 'unit' => $p['unit']];
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Pembelian Stok - Qieos</title>
<?php include '../../script/headscript.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/restock.css?v=<?= filemtime(__DIR__ . '/../../css/pages/restock.css') ?>">

<!-- Search + Show entries + baris info + pagination premium.
     WAJIB setelah css/pages/restock.css: file ini menimpa gaya dasar
     DataTables di file halaman. Dipakai juga oleh 4 halaman Master. -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/datatable-premium.css?v=<?= filemtime(__DIR__ . '/../../css/components/datatable-premium.css') ?>">

<!-- CSS tabel daftar belanja dimuat di head (bukan di fragment
     restock-table.php) supaya toggle Form <-> Edit tidak kedap-kedip:
     style sudah terpasang sebelum markup tabel di-inject via innerHTML. -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/restock-table.css?v=<?= filemtime(__DIR__ . '/../../css/pages/restock-table.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>

<body>

<?php include '../components/sidebar.php'; ?>

<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-5">
    <!-- FORM -->
    <div class="section-card mb-4">
        <div class="panel-header panel-primary">
            <div class="panel-left">
                <div class="panel-icon">
                    <i class="fas fas fa-file-alt"></i>
                </div>

                <div>
                    <div class="panel-title">
                        Daftar Belanja 
                    </div>
                    <div class="panel-subtitle">
                        Buat daftar produk yang akan dibelanja
                    </div>
                </div>
            </div>

            <div class="panel-toggle-wrap">
                <span class="toggle-label active" id="labelForm">Form</span>

                <label class="switch-toggle">
                    <input type="checkbox" id="panelToggle">
                    <span class="slider-toggle"></span>
                </label>

                <span class="toggle-label" id="labelEdit">Edit</span>
            </div>
        </div>

        <div class="mt-5 px-4">
            <div class="stock-body">

                <div id="formMode" class="panel-mode active">
        <form id="form-stock"
                            action="restock-action.php?action=store"
                            method="POST">

                        <input type="hidden" name="form_number" value="<?= $formNumber ?>">

                        <div id="itemsContainer">

                            <div class="item-row row mb-3">

                                <div class="col-md-3 item-product">
                                    <div class="input-group-modern">
                                        <div class="input-icon input-icon-product">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <select
                                            name="product_id[]"
                                            class="form-control product-select"
                                            placeholder="Nama Produk"
                                            required>
                                            <option value=""></option>
                                            <?php foreach($products as $p): ?>
                                            <option value="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-unit="<?= htmlspecialchars($p['unit']) ?>"><?= htmlspecialchars($p['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="input-group-modern">
                                        <div class="input-icon input-icon-qty">
                                            <i class="fas fa-cubes"></i>
                                        </div>
                                        <input type="number"
                                            name="qty_buy[]"
                                            class="form-control"
                                            placeholder="Qty"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="input-group-modern">
                                        <div class="input-icon input-icon-unit">
                                            <i class="fas fa-balance-scale"></i>
                                        </div>
                                        <input type="text"
                                            name="unit_buy[]"
                                            class="form-control"
                                            placeholder="Satuan"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-2 item-unit-price">
                                    <div class="input-group-modern">
                                        <div class="input-icon input-icon-price">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <input type="number"
                                            name="unit_price[]"
                                            class="form-control"
                                            placeholder="Harga Satuan"
                                            min="0"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-3 item-price">
                                    <div class="input-group-modern">
                                        <div class="input-icon input-icon-price">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <input type="number"
                                            name="price_buy[]"
                                            class="form-control"
                                            placeholder="Total Harga Produk"
                                            min="0"
                                            required>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="mp-add-footer d-flex justify-content-end gap-2 mt-4">

                            <button type="button"
                                    class="btn-save btn-save-ghost"
                                    onclick="addItem()">

                                <i class="fas fa-plus"></i>
                                Tambah Item

                            </button>

                            <button type="submit"
                                    class="btn-save">

                                <i class="fas fa-save"></i>
                                Save

                            </button>

                        </div>

                    </form>
                </div>

                <div id="editMode" class="panel-mode">
                    <div id="purchase-table" class="dt-premium"></div>
                </div>
            </div>
        </div>
    </div>   
</div>
</main>

<!-- EDIT MODAL -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content stock-panel border-0">

            <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                <div class="panel-left">
                    <div class="panel-icon">
                        <i class="fas fas fa-file-alt"></i>
                    </div>

                    <div class="mp-add-head-text">
                        <div class="panel-title">
                            Edit Daftar Belanja 
                        </div>
                        <div class="panel-subtitle">
                            Edit barang, qty, dan satuan daftar belanja
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="mt-2 px-5 mp-add-body" id="editPurchaseContent"></div>
        </div>
    </div>
</div>

<!-- RESTOCK MODAL (validasi & restock) -->
<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content restock-modal-content border-0">

            <div class="restock-modal-header">
                <div class="restock-modal-head-left">
                    <div class="restock-modal-icon">
                        <i class="fas fa-truck-ramp-box"></i>
                    </div>

                    <div class="restock-modal-head-text">
                        <div class="restock-modal-title">Validasi &amp; Restock</div>
                        <div class="restock-modal-subtitle">
                            Periksa daftar belanja sebelum menambah stok gudang
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white restock-modal-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="restock-modal-body" id="restockContent"></div>

            <div class="restock-processing" id="restockProcessing" hidden>
                <div class="rp-ring">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
                <div class="rp-text" id="rpText">Memvalidasi daftar belanja...</div>
                <div class="rp-sub">Qty masuk ke Qty Stock, Remaining Qty &amp; Unit Stock</div>

                <div class="rp-steps">
                    <div class="rp-step" data-step="0">
                        <span class="rp-step-ico"><i class="fas fa-list-check"></i></span>
                        <span class="rp-step-title">Memvalidasi daftar belanja</span>
                    </div>
                    <div class="rp-step" data-step="1">
                        <span class="rp-step-ico"><i class="fas fa-boxes-stacked"></i></span>
                        <span class="rp-step-title">Menambahkan ke stok gudang</span>
                    </div>
                    <div class="rp-step" data-step="2">
                        <span class="rp-step-ico"><i class="fas fa-lock"></i></span>
                        <span class="rp-step-title">Mengunci data restock</span>
                    </div>
                </div>

                <div class="rp-progress"><span id="rpBarFill"></span></div>
            </div>
        </div>
    </div>
</div>

<?php include '../../script/footscript.php'; ?>

<!-- Pager ringkas + chip info (helper yang sama dipakai halaman Master) -->
<script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<script>
    document.getElementById("form-stock").addEventListener("submit", function(e){
        e.preventDefault();

        // Validasi manual (required Select2 tidak tervalidasi otomatis)
        let rows = document.querySelectorAll('#itemsContainer .item-row');
        let valid = true;
        rows.forEach(function(r){
            let sel = r.querySelector('select[name="product_id[]"]');
            let qty = r.querySelector('input[name="qty_buy[]"]');
            let unit = r.querySelector('input[name="unit_buy[]"]');
            let unitPrice = r.querySelector('input[name="unit_price[]"]');
            let priceBuy = r.querySelector('input[name="price_buy[]"]');
            
            if(!sel || !sel.value){
                QToast('Error', 'Nama produk wajib dipilih', 'error');
                valid = false;
                return;
            }
            if(!qty || !qty.value.trim()){
                QToast('Error', 'Qty wajib diisi', 'error');
                valid = false;
                return;
            }
            if(!unit || !unit.value.trim()){
                QToast('Error', 'Satuan wajib diisi', 'error');
                valid = false;
                return;
            }
            if(!unitPrice || !unitPrice.value.trim()){
                QToast('Error', 'Harga satuan wajib diisi', 'error');
                valid = false;
                return;
            }
            if(!priceBuy || !priceBuy.value.trim()){
                QToast('Error', 'Total harga produk wajib diisi', 'error');
                valid = false;
                return;
            }
        });
        if(!valid) return;

        let formData = new FormData(this);

        fetch(this.action,{
            method:"POST",
            body:formData
        })
        .then(res=>res.json())
        .then(res=>{
            if(res.status==="success"){
                QToast("Berhasil", res.msg, "success");
                resetItemRows();
                
                // Auto-switch ke tab Edit dan muat data terbaru
                panelToggle.checked = true;
                panelToggle.dispatchEvent(new Event('change'));
            }else{
                QToast("Error", res.msg, "error");
            }
        });
    });

    // Reset form item ke kondisi awal (satu baris kosong seperti halaman baru)
    function resetItemRows(){
        let container = document.getElementById('itemsContainer');
        container.querySelectorAll('.product-select').forEach(function(sel){
            let $s = $(sel);
            if($s.hasClass('select2-hidden-accessible')) $s.select2('destroy');
        });
        container.innerHTML = '';

        let row = document.createElement('div');
        row.className = 'item-row row mb-3';
        row.innerHTML = `
            <div class="col-md-3 item-product">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-product"><i class="fas fa-box"></i></div>
                    <select name="product_id[]" class="form-control product-select" placeholder="Nama Produk" required>
                        ${buildProductOptions('')}
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-qty"><i class="fas fa-cubes"></i></div>
                    <input type="number" name="qty_buy[]" class="form-control" placeholder="Qty" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-unit"><i class="fas fa-balance-scale"></i></div>
                    <input type="text" name="unit_buy[]" class="form-control" placeholder="Satuan" required>
                </div>
            </div>
            <div class="col-md-2 item-unit-price">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-price"><i class="fas fa-wallet"></i></div>
                    <input type="number" name="unit_price[]" class="form-control" placeholder="Harga Satuan" min="0">
                </div>
            </div>
            <div class="col-md-3 item-price">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-price"><i class="fas fa-wallet"></i></div>
                    <input type="number" name="price_buy[]" class="form-control" placeholder="Total Harga Produk" min="0">
                </div>
            </div>
        `;
        container.appendChild(row);
        initProductSelect(row.querySelector('.product-select'));
    }

    // Daftar produk untuk dropdown dinamis (semua kecuali Additional)
    var PRODUCT_LIST = <?= json_encode(array_values($products), JSON_UNESCAPED_UNICODE) ?>;

    function escAttr(s){
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function buildProductOptions(selected){
        let html = '<option value=""></option>';
        PRODUCT_LIST.forEach(function(p){
            let sel = (selected !== '' && String(selected) === String(p.id)) ? ' selected' : '';
            html += '<option value="' + p.id + '" data-name="' + escAttr(p.name) + '" data-unit="' + escAttr(p.unit || '') + '"' + sel + '>' + escAttr(p.name) + '</option>';
        });
        return html;
    }

    function applyItemSelect2($sel){
        if(!$sel || !$sel.length) return;
        if($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
        let opts = {
            width: '100%',
            placeholder: 'Cari / Pilih Produk...',
            allowClear: true
        };
        if($sel.closest('#editPurchaseModal').length){
            opts.dropdownParent = $('#editPurchaseModal');
        }
        $sel.select2(opts);
        $sel.on('change', function(){
            let unitInput = $(this).closest('.item-row').find('input[name="unit_buy[]"]');
            if(unitInput.length){
                let opt = this.options[this.selectedIndex];
                let u = opt ? opt.getAttribute('data-unit') : '';
                if(u) unitInput.val(u);
            }
        });
    }

    function initProductSelect(sel){
        if(!sel || typeof $ === 'undefined') return;
        applyItemSelect2($(sel));
    }

    function initEditSelects(){
        document.querySelectorAll('#itemsContainerEdit .product-select').forEach(function(sel){
            applyItemSelect2($(sel));
        });
    }

    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('#itemsContainer .product-select').forEach(initProductSelect);
    });

    const panelToggle = document.getElementById('panelToggle');

    // seluruh area toggle bisa diklik (kecuali switch langsung)
    const panelToggleWrap = document.querySelector('.panel-toggle-wrap');
    if(panelToggleWrap){
        panelToggleWrap.addEventListener('click', function(e){
            if(e.target.closest('.switch-toggle')) return;
            panelToggle.checked = !panelToggle.checked;
            panelToggle.dispatchEvent(new Event('change'));
        });
    }

    panelToggle.addEventListener('change', function(){

        const formMode = document.getElementById('formMode');
        const editMode = document.getElementById('editMode');

        const labelForm = document.getElementById('labelForm');
        const labelEdit = document.getElementById('labelEdit');

        if(this.checked){
            formMode.classList.remove('active');
            editMode.classList.add('active');

            labelForm.classList.remove('active');
            labelEdit.classList.add('active');

            /* Selalu muat ulang tiap pindah ke Edit supaya isinya selalu
               terbaru (realtime) tanpa refresh halaman. */
            loadPurchaseTable();
        }else{
            editMode.classList.remove('active');
            formMode.classList.add('active');

            labelEdit.classList.remove('active');
            labelForm.classList.add('active');
        }

    });

    /* ---------- DataTable ---------- */

    /* Breakpoint, pager, dan chip info sekarang milik helper bersama
       script/datatable-compact.js, sama seperti halaman Master. Jadi angka
       575.98 tidak lagi ditulis ulang di file ini - kalau suatu saat
       berubah, cukup diubah satu tempat. */
    function isPurchaseMobile(){
        return MP_TABLE.isMobile();
    }

    let purchaseIsMobile = isPurchaseMobile();

    function purchaseDataTable(){
        const mobile = isPurchaseMobile();

        const options = {
            pageLength: 5,
            lengthMenu: [[5,10,25,50],[5,10,25,50]],

            /* Pager kustom dari script/datatable-compact.js: 3 nomor di
               mobile, 5 di tablet/desktop, halaman 1 & terakhir dikunci di
               dua ujung, dan "…" hanya muncul kalau memang ada nomor yang
               disembunyikan. Bentuk pil & warnanya sudah diatur di
               css/components/datatable-premium.css. */
            pagingType: "mp_compact",

            /* Chip "Menampilkan 1-5 dari 137 daftar belanja", plus
               "dari N daftar belanja" kalau sedang difilter. */
            infoCallback: MP_TABLE.infoCallback('daftar belanja'),

            searchDelay: 250,

            /* Opsi "responsive: true" DIHAPUS. Opsi itu hanya dibaca oleh
               extension Responsive (responsive.dataTables.min.js) yang
               TIDAK dimuat di script/headscript.php - yang ada hanya
               jquery.dataTables.min.js + dataTables.bootstrap5.min.js,
               jadi nilainya diam-diam diabaikan. Responsif di halaman ini
               ditangani manual: tukar "dom" saat breakpoint mobile
               (lihat bawah) + overflow-x pada kolom tabel. */
            autoWidth: false,

            order: [[0, 'desc']],
            ordering: true,
            language:{
                search:"",
                searchPlaceholder:"Cari daftar belanja...",

                /* Panah Previous/Next memakai glyph, bukan teks bawaan.
                   Pada rentang 9 pil, dua pil TEKS ("Previous"/"Next")
                   memakan ~150px dari baris pager sementara sisa ruang di
                   layar 320-360px cuma ~290-330px. Aksesibilitas tetap
                   terjaga: aria-label dibacakan bahasa Indonesia lewat
                   oAria.paginate di bawah. */
                paginate:{
                    previous:"&#8249;",  // ‹
                    next:"&#8250;"       // ›
                },

                /* Label yang dibacakan screen reader tetap bahasa Indonesia. */
                oAria:{
                    paginate:{
                        pageLabel:"Halaman {page}",
                        previous:"Halaman sebelumnya",
                        next:"Halaman berikutnya"
                    }
                },

                zeroRecords: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                        <div class="empty-title">Purchase tidak ditemukan</div>
                        <div class="empty-sub">
                            Coba gunakan kata kunci lain
                        </div>
                    </div>
                `,

                emptyTable: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                        <div class="empty-title">Belum ada data list belanja</div>
                        <div class="empty-sub">
                            Silakan buat list belanja terlebih dahulu
                        </div>
                    </div>
                `
            }
        };

        /* Mobile pakai "fltip": search + show entries + tabel + baris info
           chip + pagination. "l" dan "i" ikut dirender supaya user bisa
           menaikkan jumlah baris dan tahu posisinya di daftar tanpa
           harus scroll ke atas.

           "dom" HANYA di-set untuk mobile. Di desktop/tablet TIDAK di-set,
           sama seperti keempat halaman Master, jadi wrapper .row + .col-*
           bawaan DataTables Bootstrap 5 yang dipakai:

             <'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row dt-row'
             <'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>

           Wrapper itu yang bikin css/components/datatable-premium.css bisa
           meratakan baris atas (Show n entries kiri + search kanan) dan
           baris bawah (chip info kiri + pagination kanan). DULU halaman ini
           memakai dom custom "<'dt-top'<'dl'l><'df'f>>rt<'dt-bot'i<'dt-pag'p>>"
           yang menghilangkan wrapper tersebut, sehingga tampilannya beda dari
           halaman Master. */
        if(mobile) options.dom = "fltip";

        const dt = $('#purchaseTable').DataTable(options);

        dt.columns.adjust();

        return dt;
    }

    function loadPurchaseTable(){
        /* --- Ambil state SEBELUM markup ditimpa -------------------------------
           Dulu state tidak disimpan sama sekali, jadi setiap loadPurchaseTable()
           (setelah simpan / edit daftar belanja) tabel lompat balik ke
           halaman 1 DAN filter pencarian ikut hilang.

           Penting diambil di sini: setelah innerHTML diganti, node
           <table id="purchaseTable"> yang lama ikut terbuang. isDataTable()
           mencari node berdasarkan identitas (o.nTable === t), jadi
           sesudah itu isDataTable('#purchaseTable') sudah false dan
           state-nya tidak bisa diambil lagi. */
        const prev = $.fn.DataTable.isDataTable('#purchaseTable')
            ? $('#purchaseTable').DataTable()
            : null;

        const keep = prev ? {
            page: prev.page(),
            keyword: prev.search(),
            length: prev.page.len()
        } : null;

        fetch('restock-table.php')
        .then(res => res.text())
        .then(html => {
            /* destroy dulu, node lama masih terpasang jadi aman. Kalau
               dibiarkan, instance-nya menumpuk di registry
               DataTable.settings setiap reload. Sesudahnya baris
               innerHTML = html menghapus node lama itu.

               DULU urutannya terbalik (innerHTML dulu, destroy belakangan)
               sehingga isDataTable() selalu false dan tidak ada yang
               pernah ikut di-destroy. */
            if(prev) prev.destroy();

            document.getElementById('purchase-table').innerHTML = html;

            // Tunggu DOM + CSS terinisialisasi (tanpa arbitrary delay)
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    purchaseIsMobile = isPurchaseMobile();

                    const dt = purchaseDataTable();

                    if(keep){
                        dt.page.len(keep.length)
                          .search(keep.keyword)
                          .page(keep.page)
                          .draw(false);
                    }
                });
            });
        });
    }

    // Update baris purchase menjadi state "restocked" tanpa reload tabel.
    // Animasi smooth: tombol berubah, class ditambahkan, pulse effect.
    function markPurchaseRestocked(purchaseId){
        if(!$.fn.DataTable.isDataTable('#purchaseTable')) return;

        const $table = $('#purchaseTable');
        const $row = $table.find('tr[data-id="' + purchaseId + '"]');
        
        if(!$row.length){
            // Fallback: row tidak ada di halaman saat ini (filtered/pagination),
            // atau markup belum punya data-id — reload tabel
            loadPurchaseTable();
            return;
        }

        // Tambah class restocked dengan animasi pulse
        $row.addClass('is-restocked').css('animation', 'restockPulse 0.6s ease');
        
        // Hapus tombol Edit & Delete, ubah tombol Restock jadi badge sukses
        const $actions = $row.find('.mp-actions');
        $actions.find('.btn-edit, .btn-delete').fadeOut(300, function(){ $(this).remove(); });
        
        const $restockBtn = $actions.find('.restockPurchaseBtn');
        $restockBtn.removeClass('btn-restock')
                   .addClass('btn-restocked')
                   .attr('title', 'Sudah direstock ke stok gudang')
                   .html('<i class="fas fa-circle-check"></i>')
                   .css({transform: 'scale(1.1)', transition: 'transform 0.3s ease'});
        
        setTimeout(() => $restockBtn.css('transform', 'scale(1)'), 300);

        // Tambah meta chip "Stok masuk" di mobile summary
        const $meta = $row.find('.mp-meta');
        if($meta.length && !$meta.find('.mp-meta-chip-restocked').length){
            $meta.find('.mp-meta-item').last().after(`
                <span class="mp-meta-item mp-meta-chip-restocked" style="animation: fadeInScale 0.4s ease;">
                    <i class="fas fa-circle-check"></i>
                    Stok masuk
                </span>
            `);
        }
    }

    /* Rebuild tabel saat pindah mobile <-> desktop.
       Re-init in-place (tanpa fetch): data yang sama tetap tampil, jadi
       tidak ada flash kosong tiap kali jendela di-resize. */
    let purchaseResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(purchaseResizeTimer);
        purchaseResizeTimer = setTimeout(function(){
            if(isPurchaseMobile() === purchaseIsMobile) return;
            if(!$.fn.DataTable.isDataTable('#purchaseTable')) return;

            const current = $('#purchaseTable').DataTable();
            const page = current.page();
            const keyword = current.search();
            const length = current.page.len();

            current.destroy();

            purchaseIsMobile = isPurchaseMobile();
            const dt = purchaseDataTable();
            dt.page.len(length)
              .search(keyword)
              .page(page)
              .draw(false);
        }, 250);
    });

    function addItem()
    {
        let html = `
            <div class="item-row row mb-3">

                <div class="col-md-3 item-product">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-product">
                            <i class="fas fa-box"></i>
                        </div>
                        <select
                            name="product_id[]"
                            class="form-control product-select"
                            placeholder="Nama Produk"
                            required>
                            ${buildProductOptions('')}
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-qty">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <input type="number"
                            name="qty_buy[]"
                            class="form-control"
                            placeholder="Qty"
                            required>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-unit">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <input type="text"
                            name="unit_buy[]"
                            class="form-control"
                            placeholder="Satuan"
                            required>
                    </div>
                </div>

                <div class="col-md-2 item-unit-price">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-price">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <input type="number"
                            name="unit_price[]"
                            class="form-control"
                            placeholder="Harga Satuan"
                            min="0">
                    </div>
                </div>

                <div class="col-md-2 item-price">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-price">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <input type="number"
                            name="price_buy[]"
                            class="form-control"
                            placeholder="Total Harga Produk"
                            min="0">
                    </div>
                </div>

                <div class="col-md-1 item-remove">
                    <div class="input-group-modern">
                        <button type="button"
                                class="input-icon input-icon-danger"
                                onclick="removeItem(this)">
                            <i class="fas fa-trash"></i>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>

            </div>
        `;

        document
            .getElementById('itemsContainer')
            .insertAdjacentHTML('beforeend', html);

        let last = document.querySelector('#itemsContainer .item-row:last-child select.product-select');
        initProductSelect(last);
    }

    function addItemEdit()
    {
        let html = `
            <div class="item-row row mb-3">

                <input type="hidden"
                    name="item_id[]"
                    value="">

                <div class="col-md-3 item-product">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-product">
                            <i class="fas fa-box"></i>
                        </div>
                        <select
                            name="product_id[]"
                            class="form-control product-select"
                            placeholder="Nama Produk"
                            required>
                            ${buildProductOptions('')}
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-qty">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <input type="number"
                            name="qty_buy[]"
                            class="form-control"
                            placeholder="Qty"
                            min="0"
                            required>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-unit">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <input type="text"
                            name="unit_buy[]"
                            class="form-control"
                            placeholder="Satuan"
                            required>
                    </div>
                </div>

                <div class="col-md-2 item-unit-price">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-price">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <input type="number"
                            name="unit_price[]"
                            class="form-control"
                            placeholder="Harga Satuan"
                            min="0"
                            value="">
                    </div>
                </div>

                <div class="col-md-2 item-price">
                    <div class="input-group-modern">
                        <div class="input-icon input-icon-price">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <input type="number"
                            name="price_buy[]"
                            class="form-control"
                            placeholder="Total Harga Produk"
                            min="0"
                            value="">
                    </div>
                </div>

                <div class="col-md-1 item-remove">
                    <div class="input-group-modern">
                        <button type="button"
                                class="input-icon input-icon-danger"
                                onclick="removeItem(this)">
                            <i class="fas fa-trash"></i>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>

            </div>
        `;

        document
            .querySelector('#itemsContainerEdit')
            .insertAdjacentHTML('beforeend', html);

        let last = document.querySelector('#itemsContainerEdit .item-row:last-child select.product-select');
        applyItemSelect2($(last));
    }

    function removeItem(button)
    {
        button.closest('.item-row').remove();
    }
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editPurchaseBtn',function(){

        let id = $(this).data('id');

        $('#editPurchaseModal').modal('show');

        document.getElementById('editPurchaseContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('restock-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editPurchaseContent').innerHTML = html;
            initEditSelects();
        });

    });

    // Edit Action
    $(document).on('submit','#editPurchaseForm',function(e){
        e.preventDefault();

        // Validasi manual (required Select2 tidak tervalidasi otomatis)
        let valid = true;
        $('#editPurchaseContent .item-row').each(function(){
            let r = $(this);
            let sel = r.find('select[name="product_id[]"]');
            let qty = r.find('input[name="qty_buy[]"]');
            let unit = r.find('input[name="unit_buy[]"]');
            let unitPrice = r.find('input[name="unit_price[]"]');
            let priceBuy = r.find('input[name="price_buy[]"]');

            if(!sel.val()){
                QToast('Error', 'Nama produk wajib dipilih', 'error');
                valid = false;
                return false;
            }
            if(!qty.val() || !String(qty.val()).trim()){
                QToast('Error', 'Qty wajib diisi', 'error');
                valid = false;
                return false;
            }
            if(!unit.val() || !String(unit.val()).trim()){
                QToast('Error', 'Satuan wajib diisi', 'error');
                valid = false;
                return false;
            }
            if(!unitPrice.val() || !String(unitPrice.val()).trim()){
                QToast('Error', 'Harga satuan wajib diisi', 'error');
                valid = false;
                return false;
            }
            if(!priceBuy.val() || !String(priceBuy.val()).trim()){
                QToast('Error', 'Total harga produk wajib diisi', 'error');
                valid = false;
                return false;
            }
        });
        if(!valid) return;

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('restock-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil diperbarui', 'success');

                $('#editPurchaseModal').modal('hide');

                loadPurchaseTable();

            }else{

                QToast('Gagal', res.msg || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
        });
    });

    // RESTOCK (validasi) MODAL
    $(document).on('click','.restockPurchaseBtn',function(){

        let id = $(this).data('id');

        $('#restockModal').modal('show');

        document.getElementById('restockContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('restock-view.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('restockContent').innerHTML = html;
        });

    });

    // Kunci modal + blur halaman belakang selama proses validasi
    let restockBusy = false;

    function setRestockBusy(on){
        restockBusy = on;
        document.body.classList.toggle('restock-busy', on);
    }

    $('#restockModal').on('hide.bs.modal', function(e){
        if(restockBusy) e.preventDefault();
    });

    // Validasi & Restock Action
    $(document).on('submit','#restockValidateForm',function(e){
        e.preventDefault();

        let form = $(this);

        // Konfirmasi dulu supaya tidak sembarangan klik
        QConfirm(
            'Validasi & Restock?',
            'Qty pada daftar belanja ini akan masuk ke Stok Gudang. Setelah divalidasi, data tidak dapat diubah lagi.',
            {
                confirmText:'Ya, Restock',
                cancelText:'Batal',
                icon:'fa-truck-ramp-box',
                confirmClass:'q-confirm-btn-success',
                iconClass:'q-confirm-icon-warning'
            }
        ).then(function(ok){
            if(!ok) return;
            runRestockValidate(form);
        });
    });

    function runRestockValidate(form){
        let formData = new FormData(form[0]);

        let btn = form.find('.btn-validate-restock');
        btn.prop('disabled', true).addClass('is-loading');
        btn.find('span').text('Memproses...');
        btn.find('i').attr('class', 'fas fa-circle-notch fa-spin');

        let processing = $('#restockProcessing');
        let rpText = $('#rpText');
        let rpSteps = $('.rp-step', processing);
        let rpBar = $('#rpBarFill');

        let DUR = 4800;
        let phaseMs = DUR / 3;
        let started = Date.now();

        rpSteps.removeClass('is-running is-done is-waiting').addClass('is-waiting');
        rpBar.css('width', '0%');

        let stepTitles = [
            'Memvalidasi daftar belanja...',
            'Menambahkan ke Stok Gudang...',
            'Mengunci data restock...'
        ];

        function runPhase(i){
            if(i > 0){
                rpSteps.eq(i - 1).removeClass('is-running').addClass('is-done');
            }
            rpSteps.eq(i).removeClass('is-waiting').addClass('is-running');
            rpBar.css('width', Math.round(((i + 1) / 3) * 100) + '%');
            rpText.text(stepTitles[i]);
        }

        setRestockBusy(true);
        processing.prop('hidden', false);
        runPhase(0);

        let timers = [];
        for(let i = 1; i < 3; i++){
            timers.push(setTimeout(function(){ if(i < 3) runPhase(i); }, phaseMs * i));
        }

        timers.push(setTimeout(function(){
            rpSteps.eq(2).removeClass('is-running').addClass('is-done');
            rpBar.css('width', '100%');
            rpText.text('Selesai');
        }, DUR));

        function cancelTimers(){
            timers.forEach(clearTimeout);
        }

        // Animasi sukses di dalam overlay processing: ring berubah jadi
        // checkmark besar + badge checklist di atasnya + burst partikel
        // + semua step hijau.
        function showRestockSuccess(processing, rpSteps, rpBar, rpText){
            rpSteps.removeClass('is-running').addClass('is-done');
            rpBar.css('width', '100%');
            rpText.text('Data berhasil direstock!');

            // Ring jadi checkmark statis besar dengan glow, icon check
            // putih di tengah lingkaran hijau
            let ring = processing.find('.rp-ring');
            ring.css('animation', 'none');
            ring.addClass('rp-ring-success');
            ring.html('<i class="fas fa-check"></i>');

            // Burst partikel (12 buah, posisi acak)
            let burst = $('<div class="rp-burst"></div>');
            for(let i = 0; i < 12; i++){
                let ang = (i / 12) * 360;
                burst.append('<span class="rp-particle" style="--ang:' + ang + 'deg;--i:' + i + '"></span>');
            }
            processing.append(burst);

            // Overlay jadi tema sukses
            processing.addClass('rp-success');

            // Hapus partikel setelah animasi selesai
            setTimeout(function(){ burst.remove(); }, 1400);
        }

        fetch('restock-action.php?action=validate',{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            let remain = Math.max(0, DUR - (Date.now() - started));

            setTimeout(function(){

                cancelTimers();

                if(res.status === 'success'){

                    // Animasi sukses spektakuler: ring jadi checkmark besar,
                    // confetti burst, semua step hijau, glow effect
                    showRestockSuccess(processing, rpSteps, rpBar, rpText);

                    // Setelah animasi sukses selesai (1.5s), sembunyikan
                    // overlay lalu isi field + tunggu 3s sebelum tutup modal
                    setTimeout(function(){
                        processing.prop('hidden', true);

                        // Isi field qty stock / remaining qty / unit stock langsung
                        // (tanpa animasi berjenjang) begitu proses selesai
                        form.find('.rv-field').each(function(){
                            let el = $(this);
                            let val = el.attr('data-value');
                            el.text(val !== undefined && val !== '' ? val : '—').addClass('is-filled');
                        });

                        // Ganti tombol validasi dengan alert permanen
                        form.find('.restock-view-footer').html(`
                            <div class="restock-alert-done">
                                <i class="fas fa-shield-halved"></i>
                                <span>Data sudah masuk ke stok gudang dan tidak dapat diubah.</span>
                            </div>
                        `);

                        form.find('.restock-notice').addClass('restock-notice-done')
                            .html(`
                                <i class="fas fa-circle-check"></i>
                                <div>
                                    <strong>Sudah direstock.</strong>
                                    Data sudah masuk ke <b>Stok Gudang</b> dan tidak dapat diubah lagi.
                                </div>
                            `);

                        QToast('Berhasil', res.msg || 'Restock berhasil', 'success');

                        setRestockBusy(false);

                        // Modal tetap tampil 3 detik supaya user melihat hasil
                        // konversi, lalu ditutup smooth. Tabel diperbarui realtime
                        // tanpa reload penuh.
                        setTimeout(function(){
                            $('#restockModal').modal('hide');
                            markPurchaseRestocked(formData.get('id'));
                        }, 3000);
                    }, 1500);

                }else{

                    processing.prop('hidden', true);

                    btn.prop('disabled', false).removeClass('is-loading');
                    btn.find('span').text('Validasi & Restock');
                    btn.find('i').attr('class', 'fas fa-circle-check');

                    QToast('Gagal', res.msg || 'Terjadi kesalahan', 'error');

                    setRestockBusy(false);

                }

            }, remain);

        })
        .catch(() => {
            cancelTimers();
            processing.prop('hidden', true);
            btn.prop('disabled', false).removeClass('is-loading');
            btn.find('span').text('Validasi & Restock');
            btn.find('i').attr('class', 'fas fa-circle-check');
            QToast('Error', 'Gagal memproses restock', 'error');
            setRestockBusy(false);
        });
    }

    // Delete Action
    $(document).on('click','.deletePurchaseBtn',function(){

        let id = $(this).data('id');
        let date = $(this).data('date');
        let formattedDate = new Date(date).toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        QConfirm('Hapus Daftar Belanja?', 'Daftar belanja tanggal ' + formattedDate + ' akan dihapus permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('restock-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        loadPurchaseTable();

                    }

                });
            }
        });

    });

    // Print Action
    $(document).on('click', '.printPurchaseBtn', function () {

        let id = $(this).data('id');
        let date = $(this).data('date');
        let formattedDate = new Date(date).toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        fetch('restock-action.php?action=get_print&id=' + id)
            .then(res => res.json())
            .then(res => {

                let items = res.data;

                let rows = '';
                let grandTotal = 0;

                items.forEach((item, i) => {
                    let price = parseFloat(item.price) || 0;
                    grandTotal += price;

                    rows += `
                        <tr>
                            <td style="text-align:center">${i + 1}</td>
                            <td>${item.name}</td>
                            <td style="text-align:center">${item.qty}</td>
                            <td style="text-align:center">${item.unit}</td>
                            <td style="text-align:right">Rp ${price.toLocaleString('id-ID')}</td>
                            <td style="text-align:center"><span class="chk"></span></td>
                        </tr>
                    `;
                });

                rows += `
                    <tr class="total-row">
                        <td colspan="4" style="text-align:right;font-weight:700">Total Harga Keseluruhan</td>
                        <td style="text-align:right;font-weight:700">Rp ${grandTotal.toLocaleString('id-ID')}</td>
                        <td></td>
                    </tr>
                `;

                let win = window.open('', '', 'width=900,height=700');

                win.document.write(`
                    <html>
                    <head>
                        <title>Print Daftar Belanja</title>
                        <style>
                            body { font-family: Poppins; padding: 20px; }
                            h2 { margin-bottom: 5px; text-align: center; text-transform: uppercase;}
                            .sub { color: #666; margin-bottom: 50px; text-align: center;}

                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }

                            table, th, td {
                                border: 1px solid #000;
                            }

                            th, td {
                                padding: 8px;
                            }

                            th {
                                background: #f2f2f2;
                            }

                            .chk {
                                display: inline-block;
                                width: 13px;
                                height: 13px;
                                border: 1.5px solid #000;
                                background: #fff;
                                vertical-align: middle;
                            }

                            .total-row td {
                                background: #f2f2f2;
                            }
                        </style>
                    </head>
                    <body>

                        <h2>Daftar Belanja (${formattedDate})</h2>
                        <div class="sub">by QIEOS</div>

                        <table>
                            <colgroup>
                                <col style="width:6%">
                                <col style="width:44%">
                                <col style="width:10%">
                                <col style="width:12%">
                                <col style="width:18%">
                                <col style="width:10%">
                            </colgroup>
                            
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang / Produk</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Total Harga</th>
                                    <th>Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows}
                            </tbody>
                        </table>

                    </body>
                    </html>
                `);

                win.document.close();

                // FIX IMPORTANT
                setTimeout(() => {
                    win.print();
                }, 300);

            });

    });
</script>

</body>
</html>
