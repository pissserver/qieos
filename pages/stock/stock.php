<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Detail Stok - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/stock.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/stock.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/stock-detail.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/stock-detail.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/datatable-premium.css?v=<?php echo filemtime(__DIR__ . '/../../css/components/datatable-premium.css'); ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <div class="row stock-layout">
        <!-- STOK GUDANG -->
        <div class="col-lg-6 col-md-12 stock-panel-col">
            <div class="section-card">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Detail Stok
                            </div>
                            <div class="panel-subtitle">
                                Rincian persediaan produk per batch (FIFO)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <!-- TABLE -->
                    <div class="table-responsive-wrap dt-premium" id="stockTableContainer">
                        <!-- Loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>

        <!-- MUTASI STOK -->
        <div class="col-lg-6 col-md-12 fifo-panel-col">
            <div class="fifo-container">

                <div class="fifo-title">
                    <span class="fifo-icon">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                    </span>
                    <div class="fifo-title-text">
                        <h5 class="mb-0">Mutasi Stok</h5>
                        <small>Pergerakan stok gudang &amp; kantin per produk</small>
                    </div>

                    <button type="button" class="mutasi-export-btn" id="mutasiExportBtn" disabled
                            title="Ekspor mutasi stok sesuai tab yang dibuka">
                        <i class="fas fa-file-excel"></i>
                        <span class="mutasi-export-label">Export Gudang</span>
                    </button>
                </div>

                <div class="mutasi-toolbar">
                    <div class="mutasi-tabs" role="tablist" aria-label="Jenis mutasi">
                        <button type="button" class="mutasi-tab is-active" data-tab="gudang" role="tab" aria-selected="true">
                            <i class="fas fa-warehouse"></i>
                            <span>Stok Gudang</span>
                        </button>
                        <button type="button" class="mutasi-tab" data-tab="kantin" role="tab" aria-selected="false">
                            <i class="fas fa-store"></i>
                            <span>Stok Kantin</span>
                        </button>
                    </div>

                    <div class="mutasi-period">
                        <label class="mutasi-period-label"><i class="far fa-calendar"></i> Periode</label>
                        <div class="mutasi-period-fields">
                            <input type="date" id="mutasiFrom" aria-label="Tanggal mulai">
                            <span class="mutasi-period-sep">&ndash;</span>
                            <input type="date" id="mutasiTo" aria-label="Tanggal akhir">
                        </div>
                    </div>
                </div>

                <div id="fifo-detail" class="dt-premium">

                    <div class="fifo-empty">

                        <div class="fifo-empty-icon">
                            <i class="fas fa-arrow-right-arrow-left"></i>
                        </div>

                        <div class="fifo-empty-title">Belum ada detail</div>
                        <div class="fifo-empty-sub">
                            Klik salah satu produk di samping untuk melihat mutasi stok
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
</main>

<!-- ============================ MODAL DETAIL LAYER FIFO ============================ -->
<div class="fifo-modal" id="fifoModal" aria-hidden="true">
    <div class="fifo-modal-backdrop" data-fifo-close></div>

    <div class="fifo-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="fifoModalName">
        <div class="fifo-modal-head">
            <div class="fifo-modal-product">
                <img class="fifo-modal-img" id="fifoModalImg" alt="" hidden>
                <span class="fifo-modal-icon" id="fifoModalIcon"><i class="fas fa-box-open"></i></span>
                <div class="fifo-modal-meta">
                    <div class="fifo-modal-name" id="fifoModalName">Detail FIFO</div>
                    <div class="fifo-modal-code" id="fifoModalCode">Layer stok per batch pembelian</div>
                </div>
            </div>

            <button type="button" class="fifo-modal-close" data-fifo-close aria-label="Tutup">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="fifo-modal-body dt-premium" id="fifoModalBody">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<!-- ============================ MODAL ADJUSTMENT STOK ============================ -->
<div class="adjust-modal" id="adjustModal" aria-hidden="true">
    <div class="adjust-modal-backdrop" data-adjust-close></div>

    <div class="adjust-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="adjustModalName">
        <div class="adjust-modal-head">
            <div class="adjust-modal-product">
                <img class="adjust-modal-img" id="adjustModalImg" alt="" hidden>
                <span class="adjust-modal-icon" id="adjustModalIcon"><i class="fas fa-sliders"></i></span>
                <div class="adjust-modal-meta">
                    <div class="adjust-modal-name" id="adjustModalName">Adjustment Stok</div>
                    <div class="adjust-modal-code" id="adjustModalCode">Sesuaikan stok produk dengan kondisi fisik</div>
                </div>
            </div>

            <button type="button" class="adjust-modal-close" data-adjust-close aria-label="Tutup">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="adjust-modal-body">
            <div class="adjust-product-card">
                <span class="adjust-product-kicker">PRODUCT IN REVIEW</span>
                <div class="adjust-product-avatar" id="adjustProductAvatar">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="adjust-product-info">
                    <div class="adjust-product-name" id="adjustProductName">&mdash;</div>
                    <div class="adjust-product-sub">
                        <span id="adjustProductCode"></span>
                        <span class="adjust-product-dot">&bull;</span>
                        <span id="adjustProductUnit"></span>
                    </div>
                </div>
            </div>

            <div class="adjust-tabs" role="tablist" aria-label="Adjustment stok">
                <button type="button" class="adjust-tab is-active" id="adjustFormTab" data-adjust-tab="form" role="tab" aria-selected="true" aria-controls="adjustFormPane" tabindex="0">
                    <i class="fas fa-pen-to-square"></i>
                    <span>Form Adjustment</span>
                </button>
                <button type="button" class="adjust-tab" id="adjustHistoryTab" data-adjust-tab="history" role="tab" aria-selected="false" aria-controls="adjustHistoryPane" tabindex="-1">
                    <i class="fas fa-clock-rotate-left"></i>
                    <span>Riwayat Adjustment</span>
                </button>
            </div>

            <div class="adjust-panes">
                <div class="adjust-pane is-active" id="adjustFormPane" data-adjust-pane="form" role="tabpanel" aria-labelledby="adjustFormTab">
                    <form id="adjustForm" novalidate>
                        <div class="adjust-form-intro">
                            <span class="adjust-form-eyebrow">STOCK CONTROL</span>
                            <h3>Rincian perubahan</h3>
                            <p>Lengkapi detail penyesuaian sebelum menyimpan.</p>
                        </div>
                        <div class="adjust-fields">
                            <div class="adjust-field">
                                <label class="adjust-label" for="adjustType">Jenis Adjustment</label>
                                <div class="adjust-select-wrap">
                                    <select class="adjust-select" id="adjustType" required>
                                        <option value="" selected disabled>Pilih jenis adjustment...</option>
                                        <option value="in">Penambahan Stok</option>
                                        <option value="out">Pengurangan Stok</option>
                                    </select>
                                </div>
                            </div>

                            <div class="adjust-field">
                                <label class="adjust-label" for="adjustLocation">Lokasi Stok</label>
                                <div class="adjust-select-wrap">
                                    <select class="adjust-select" id="adjustLocation" required disabled>
                                        <option value="">Pilih jenis dahulu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="adjust-field" id="adjustLayerField" style="display:none;">
                                <label class="adjust-label" for="adjustLayer">Layer FIFO</label>
                                <select class="adjust-select" id="adjustLayer" disabled>
                                    <option value="">Pilih lokasi dahulu</option>
                                </select>
                                <small class="adjust-field-hint">Pilih layer pembelian yang di-adjust</small>
                            </div>

                            <div class="adjust-field" id="adjustQtyField">
                                <label class="adjust-label" for="adjustQty">Jumlah</label>
                                <div class="adjust-qty-wrap">
                                    <input type="number" class="adjust-input" id="adjustQty" min="1" step="1" placeholder="0" required disabled>
                                    <span class="adjust-qty-unit-chip" id="adjustQtyUnit" hidden></span>
                                </div>
                                <small class="adjust-field-hint" id="adjustQtyHint"></small>
                            </div>

                            <div class="adjust-field" id="adjustNoteField">
                                <label class="adjust-label" for="adjustNote">Catatan <span class="adjust-label-req" id="adjustNoteReq">(wajib)</span></label>
                                <textarea class="adjust-textarea" id="adjustNote" rows="3" maxlength="500" placeholder="Contoh: hasil opname, barang rusak, atau kedaluwarsa..." required></textarea>
                                <small class="adjust-field-hint">Jelaskan alasan penyesuaian agar perubahan stok mudah ditelusuri.</small>
                            </div>
                        </div>

                        <div class="adjust-modal-foot">
                            <button type="button" class="adjust-btn adjust-btn-ghost" data-adjust-close>Batal</button>
                            <button type="submit" class="adjust-btn adjust-btn-primary" id="adjustSubmitBtn" disabled hidden>
                                <span class="adjust-btn-spinner" hidden><i class="fas fa-circle-notch fa-spin"></i></span>
                                <span class="adjust-btn-text">Save Adjustment</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="adjust-pane" id="adjustHistoryPane" data-adjust-pane="history" role="tabpanel" aria-labelledby="adjustHistoryTab">
                    <div id="adjustHistoryContent" class="adjust-history-wrap">
                        <div class="adjust-loading">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            <span>Memuat riwayat...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../script/footscript.php'; ?>

    <script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<script>
    /* Breakpoint, pager, dan chip info milik helper bersama
       script/datatable-compact.js, sama seperti Master & Purchasing. */
    function isStockMobile(){
        return MP_TABLE.isMobile();
    }

    let stockIsMobile = isStockMobile();

    function loadingHTML(title){
        return `
            <div class="fifo-loading">
                <div class="fifo-loading-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="fifo-loading-title">${title}</div>
                <div class="fifo-loading-bar"><span></span></div>
            </div>
        `;
    }

    /* ============================ TABEL STOK GUDANG ============================ */

    function stockDataTable(){
        const mobile = isStockMobile();

        const options = {
            pageLength: 5,
            lengthMenu: [[5,10,25,50],[5,10,25,50]],

            /* Pager kustom dari script/datatable-compact.js: 3 nomor di
               mobile, 5 di tablet/desktop, halaman 1 & terakhir dikunci di
               dua ujung, dan "…" hanya muncul kalau memang ada nomor yang
               disembunyikan. */
            pagingType: "mp_compact",

            /* Chip "Menampilkan 1-5 dari 137 produk", plus
               "dari N produk" kalau sedang difilter. */
            infoCallback: MP_TABLE.infoCallback('produk'),

            searchDelay: 250,

            /* Responsif ditangani manual: tukar "dom" saat breakpoint mobile
               (lihat bawah) + overflow-x pada kolom tabel. Extension
               Responsive tidak dimuat di headscript.php. */
            autoWidth: false,
            language:{
                search:"",
                searchPlaceholder:"Cari produk...",

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

                zeroRecords: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                        <div class="empty-title">Produk tidak ditemukan</div>
                        <div class="empty-sub">Coba gunakan kata kunci lain</div>
                    </div>
                `,

                emptyTable: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                        <div class="empty-title">Belum ada data produk</div>
                        <div class="empty-sub">Silakan tambahkan stok terlebih dahulu</div>
                    </div>
                `
            }
        };

        /* "dom" HANYA di-set untuk mobile. Di desktop/tablet dibiarkan
           default supaya wrapper .row + .col-* bawaan DataTables Bootstrap 5
           yang dipakai css/components/datatable-premium.css. */
        if(mobile) options.dom = "fltip";

        const dt = $('#stockTable').DataTable(options);
        dt.columns.adjust();

        return dt;
    }

    function loadStockTable(){
        /* Ambil state SEBELUM markup ditimpa: sesudah innerHTML diganti, node
           <table id="stockTable"> lama ikut terbuang dan state-nya tidak bisa
           diambil lagi. */
        const prev = $.fn.DataTable.isDataTable('#stockTable')
            ? $('#stockTable').DataTable()
            : null;

        const keep = prev ? {
            page: prev.page(),
            keyword: prev.search(),
            length: prev.page.len()
        } : null;

        fetch('stock-table.php')
        .then(res=>res.text())
        .then(html=>{
            if(prev) prev.destroy();

            document.getElementById('stockTableContainer').innerHTML = html;

            setTimeout(()=>{
                stockIsMobile = isStockMobile();

                const dt = stockDataTable();

                if(keep){
                    dt.page.len(keep.length)
                      .search(keep.keyword)
                      .page(keep.page)
                      .draw(false);
                }

                if(window.activeStockId){
                    highlightActiveRow(window.activeStockId);
                }
            },100);
        });
    }

    /* ============================ PANEL MUTASI STOK ============================ */

    window.activeStockId = null;
    window.mutasiToken = 0;
    window.mutasiTab = 'gudang';

    function pad2(n){ return n < 10 ? '0' + n : '' + n; }

    function toDateInput(d){
        return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
    }

    function defaultFromDate(){
        const now = new Date();
        return toDateInput(new Date(now.getFullYear(), now.getMonth(), 1));
    }

    function defaultToDate(){
        return toDateInput(new Date());
    }

    function highlightActiveRow(id){
        $('.stock-row').removeClass('stock-row-active');
        const row = $('.stock-row[data-id="' + id + '"]');
        if(row.length){
            row.addClass('stock-row-active');
        }
    }

    function loadDetail(row){
        if(!row) return;
        const id = row.getAttribute('data-id');
        if(!id) return;

        window.activeStockId = id;
        highlightActiveRow(id);
        syncMutasiExport();

        loadMutasi();
    }

    function destroyMutasiTables(){
        document.querySelectorAll('#fifo-detail .table-mutasi').forEach(function(t){
            if($.fn.DataTable.isDataTable(t)){
                $(t).DataTable().destroy();
            }
        });
    }

    function loadMutasi(){
        const id = window.activeStockId;
        if(!id) return;

        const wrap = document.getElementById('fifo-detail');
        const token = ++window.mutasiToken;

        destroyMutasiTables();
        wrap.classList.remove('fifo-enter');
        wrap.innerHTML = loadingHTML('Memuat mutasi stok');

        const from = $('#mutasiFrom').val() || defaultFromDate();
        const to   = $('#mutasiTo').val()   || defaultToDate();

        fetch('stock-mutasi.php?id=' + encodeURIComponent(id)
            + '&from=' + encodeURIComponent(from)
            + '&to=' + encodeURIComponent(to))
        .then(res => res.text())
        .then(html => {
            if(window.mutasiToken !== token) return;

            wrap.innerHTML = html;

            applyMutasiTab(window.mutasiTab);
            initPaneTable(window.mutasiTab);

            wrap.classList.add('fifo-enter');
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    wrap.classList.remove('fifo-enter');
                });
            });
        });
    }

    function applyMutasiTab(tab){
        window.mutasiTab = tab;

        document.querySelectorAll('#fifo-detail .mutasi-pane').forEach(function(p){
            p.classList.toggle('is-active', p.getAttribute('data-pane') === tab);
        });

        document.querySelectorAll('.mutasi-tab').forEach(function(b){
            const on = b.getAttribute('data-tab') === tab;
            b.classList.toggle('is-active', on);
            b.setAttribute('aria-selected', on ? 'true' : 'false');
        });

        syncMutasiExport();
    }

    /* Tombol ekspor mengikuti tab yang sedang dibuka:
       gudang -> "Export Gudang", kantin -> "Export Kantin".
       Nonaktif sampai ada produk yang dipilih. */
    function syncMutasiExport(){
        const btn = document.getElementById('mutasiExportBtn');
        if(!btn) return;

        const kantin = window.mutasiTab === 'kantin';
        const label = btn.querySelector('.mutasi-export-label');
        if(label) label.textContent = kantin ? 'Export Kantin' : 'Export Gudang';

        btn.disabled = !window.activeStockId;
    }

    /* Unduh Excel mutasi sesuai tab + periode yang aktif. */
    function exportMutasi(){
        const id = window.activeStockId;
        if(!id) return;

        const tab  = window.mutasiTab === 'kantin' ? 'kantin' : 'gudang';
        const from = $('#mutasiFrom').val() || defaultFromDate();
        const to   = $('#mutasiTo').val()   || defaultToDate();

        const url = 'stock-mutasi-export.php?id=' + encodeURIComponent(id)
            + '&tab='  + encodeURIComponent(tab)
            + '&from=' + encodeURIComponent(from)
            + '&to='   + encodeURIComponent(to);

        const a = document.createElement('a');
        a.href = url;
        a.rel = 'noopener';
        document.body.appendChild(a);
        a.click();
        a.remove();
    }

    /* Opsi tabel riwayat mutasi. Dipisah jadi fungsi supaya dipakai ulang
       oleh handler resize + saat pindah tab (in-place, tanpa fetch). */
    function mutasiTableOptions(){
        const options = {
            pageLength: 10,
            lengthMenu: [[10,25,50],[10,25,50]],
            searching: true,

            pagingType: "mp_compact",
            infoCallback: MP_TABLE.infoCallback('riwayat'),

            autoWidth: false,
            ordering: false,
            language: {
                search: "",
                searchPlaceholder: "Cari riwayat...",

                paginate: {
                    previous: "&#8249;",
                    next: "&#8250;"
                },

                oAria: {
                    paginate: {
                        pageLabel: "Halaman {page}",
                        previous: "Halaman sebelumnya",
                        next: "Halaman berikutnya"
                    }
                },

                zeroRecords: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img" style="width:200px;">
                        <div class="empty-title">Belum ada pergerakan</div>
                        <div class="empty-sub">Tidak ada mutasi pada rentang tanggal ini</div>
                    </div>
                `,

                emptyTable: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img" style="width:200px;">
                        <div class="empty-title">Belum ada pergerakan</div>
                        <div class="empty-sub">Tidak ada mutasi pada rentang tanggal ini</div>
                    </div>
                `
            }
        };

        if(isStockMobile()) options.dom = "fltip";

        return options;
    }

    function initPaneTable(tab){
        const pane = document.querySelector('#fifo-detail .mutasi-pane[data-pane="' + tab + '"]');
        if(!pane) return;

        const table = pane.querySelector('.table-mutasi');
        if(!table) return;

        if(!$.fn.DataTable.isDataTable(table)){
            $(table).DataTable(mutasiTableOptions());
        } else {
            $(table).DataTable().columns.adjust().draw(false);
        }
    }

    /* ============================ MODAL DETAIL FIFO ============================ */

    window.fifoModalToken = 0;

    function openFifo(row){
        if(!row) return;

        const id    = row.getAttribute('data-id');
        const name  = row.getAttribute('data-name') || 'Detail FIFO';
        const code  = row.getAttribute('data-code') || '';

        document.getElementById('fifoModalName').textContent = name;
        document.getElementById('fifoModalCode').textContent = code || 'Layer stok per batch pembelian';

        const img = document.getElementById('fifoModalImg');
        const icon = document.getElementById('fifoModalIcon');
        const photo = row.getAttribute('data-photo') || '';

        if(photo){
            img.src = BASE_URL + '/assets/img/products/' + photo;
            img.alt = name;
            img.hidden = false;
            icon.hidden = true;
        } else {
            img.hidden = true;
            icon.hidden = false;
        }

        document.getElementById('fifoModal').classList.add('is-open');
        document.getElementById('fifoModal').setAttribute('aria-hidden', 'false');
        document.body.classList.add('fifo-modal-open');

        loadFifo(id);
    }

    function loadFifo(id){
        const body = document.getElementById('fifoModalBody');
        const token = ++window.fifoModalToken;

        if($.fn.DataTable.isDataTable('#tableStock')){
            $('#tableStock').DataTable().destroy();
        }

        body.innerHTML = loadingHTML('Memuat layer FIFO');

        fetch('stock-detail.php?id=' + encodeURIComponent(id))
        .then(res => res.text())
        .then(html => {
            if(window.fifoModalToken !== token) return;

            body.innerHTML = html;

            const table = document.getElementById('tableStock');
            if(table){
                if($.fn.DataTable.isDataTable(table)){
                    $(table).DataTable().destroy();
                }
                $(table).DataTable(stockDetailOptions());
            }
        });
    }

    function closeFifo(){
        const modal = document.getElementById('fifoModal');
        if(!modal) return;

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('fifo-modal-open');

        if($.fn.DataTable.isDataTable('#tableStock')){
            $('#tableStock').DataTable().destroy();
        }

        document.getElementById('fifoModalBody').innerHTML = '';
    }

    /* Opsi tabel detail FIFO di dalam modal. */
    function stockDetailOptions(){
        const detailOptions = {
            pageLength: 10,
            lengthMenu: [[10,25,50],[10,25,50]],
            searching: true,

            pagingType: "mp_compact",
            infoCallback: MP_TABLE.infoCallback('layer'),

            autoWidth: false,
            ordering: false,
            language: {
                search: "",
                searchPlaceholder:"Cari layer...",

                paginate: {
                    previous:"&#8249;",
                    next:"&#8250;"
                },

                oAria: {
                    paginate: {
                        pageLabel:"Halaman {page}",
                        previous:"Halaman sebelumnya",
                        next:"Halaman berikutnya"
                    }
                },

                zeroRecords: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img" style="width:200px;">
                        <div class="empty-title">Data tidak ditemukan</div>
                    </div>
                `
            }
        };

        if(isStockMobile()) detailOptions.dom = "fltip";

        return detailOptions;
    }

    /* ============================ EVENTS ============================ */

    // Pindah tab panel mutasi
    $(document).on('click', '.mutasi-tab', function(){
        const tab = $(this).data('tab');
        applyMutasiTab(tab);
        initPaneTable(tab);
    });

    // Ekspor Excel mutasi sesuai tab yang sedang dibuka
    $(document).on('click', '#mutasiExportBtn', exportMutasi);

    // Ganti periode -> muat ulang panel
    let mutasiPeriodTimer;
    $(document).on('change', '#mutasiFrom, #mutasiTo', function(){
        if(!window.activeStockId) return;
        clearTimeout(mutasiPeriodTimer);
        mutasiPeriodTimer = setTimeout(loadMutasi, 150);
    });

    // Tutup modal FIFO
    $(document).on('click', '[data-fifo-close]', closeFifo);    $(document).on('keydown', function(e){
        if(e.key === 'Escape' && document.getElementById('fifoModal').classList.contains('is-open')){
            closeFifo();
        }
    });

    // Rebuild tabel saat ganti mobile/desktop
    let stockResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(stockResizeTimer);
        stockResizeTimer = setTimeout(function(){
            if(isStockMobile() === stockIsMobile) return;
            stockIsMobile = isStockMobile();

            // 1) tabel utama
            if($.fn.DataTable.isDataTable('#stockTable')){
                const current = $('#stockTable').DataTable();
                const page = current.page();
                const keyword = current.search();
                const length = current.page.len();

                current.destroy();

                const dt = stockDataTable();
                dt.page.len(length)
                  .search(keyword)
                  .page(page)
                  .draw(false);
            }

            // 2) tabel riwayat panel mutasi
            document.querySelectorAll('#fifo-detail .table-mutasi').forEach(function(t){
                if($.fn.DataTable.isDataTable(t)) $(t).DataTable().destroy();
            });
            if(window.activeStockId) initPaneTable(window.mutasiTab);

            // 3) tabel layer FIFO di modal
            const modal = document.getElementById('fifoModal');
            if(modal && modal.classList.contains('is-open')){
                const t = document.getElementById('tableStock');
                if(t){
                    if($.fn.DataTable.isDataTable(t)) $(t).DataTable().destroy();
                    $(t).DataTable(stockDetailOptions());
                }
            }

            if(window.activeStockId){
                highlightActiveRow(window.activeStockId);
            }
        }, 250);
    });

    $(document).ready(function(){
        $('#mutasiFrom').val(defaultFromDate());
        $('#mutasiTo').val(defaultToDate());

        syncMutasiExport();
        loadStockTable();
    });

    /* ============================ MODAL ADJUSTMENT ============================ */

    let adjustProductData = null;
    let adjustFifoLayers = [];

    function openAdjust(row){
        if(!row) return;

        const id    = row.getAttribute('data-id');
        const name  = row.getAttribute('data-name') || 'Adjustment Stok';
        const code  = row.getAttribute('data-code') || '';
        const unit  = row.querySelector('.unit-badge')?.textContent.trim() || '';
        const photo = row.getAttribute('data-photo') || '';

        adjustProductData = { id, name, code, unit, photo };
        currentEditId = 0;
        currentEditAdj = null;

        document.getElementById('adjustModalName').textContent = 'Penyesuaian Stok';
        document.getElementById('adjustModalCode').textContent = 'Perbarui stok berdasarkan kondisi fisik';

        document.getElementById('adjustProductName').textContent = name;
        document.getElementById('adjustProductCode').textContent = code;
        document.getElementById('adjustProductUnit').textContent = unit;
        
        const qtyChip = document.getElementById('adjustQtyUnit');
        if(unit){
            qtyChip.textContent = unit;
            qtyChip.hidden = false;
        } else {
            qtyChip.hidden = true;
        }

        const avatar = document.getElementById('adjustProductAvatar');
        if(photo){
            avatar.innerHTML = '<img src="' + BASE_URL + '/assets/img/products/' + photo + '" alt="' + name + '">';
        } else {
            avatar.innerHTML = '<i class="fas fa-box-open"></i>';
        }

        document.getElementById('adjustModalImg').hidden = true;
        document.getElementById('adjustModalIcon').hidden = false;

        document.getElementById('adjustModal').classList.add('is-open');
        document.getElementById('adjustModal').setAttribute('aria-hidden', 'false');
        document.body.classList.add('adjust-modal-open');

        resetAdjustForm();
        loadFifoLayers(id);
        loadAdjustHistory(id);
    }

    function closeAdjust(){
        const modal = document.getElementById('adjustModal');
        if(!modal) return;

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('adjust-modal-open');

        resetAdjustForm();
        adjustProductData = null;
        adjustFifoLayers = [];
        currentEditId = 0;
        currentEditAdj = null;
    }

    function switchAdjustTab(tab){
        document.querySelectorAll('.adjust-tab').forEach(function(b){
            const isActive = b.getAttribute('data-adjust-tab') === tab;
            b.classList.toggle('is-active', isActive);
            b.setAttribute('aria-selected', isActive ? 'true' : 'false');
            b.setAttribute('tabindex', isActive ? '0' : '-1');
        });
        document.querySelectorAll('.adjust-pane').forEach(function(p){
            p.classList.toggle('is-active', p.getAttribute('data-adjust-pane') === tab);
        });
    }

    function loadAdjustHistory(productId){
        const wrap = document.getElementById('adjustHistoryContent');
        wrap.innerHTML = '<div class="adjust-loading"><i class="fas fa-circle-notch fa-spin"></i><span>Memuat riwayat...</span></div>';

        fetch('stock-adjustment-history.php?id=' + encodeURIComponent(productId))
        .then(res => res.text())
        .then(html => { wrap.innerHTML = html; });
    }

    /* Select2 layer FIFO: sekali init, isi opsi di-refresh tiap buka modal. */
    function initLayerSelect2(){
        const $layer = $('#adjustLayer');
        if(!$layer.data('select2')){
            $layer.select2({
                width: '100%',
                placeholder: 'Cari / pilih layer FIFO...',
                minimumResultsForSearch: 0,
                dropdownParent: $('#adjustModal'),
                templateResult: formatLayerOption,
                language: {
                    noResults: function(){ return 'Layer tidak ditemukan'; },
                    searching: function(){ return 'Mencari layer...' }
                }
            });
            $layer.on('change', onLayerChange);
        }
    }

    /* Opsi layer dua baris: badge form + tanggal di kiri, sisa stok kanan. */
    function formatLayerOption(layer){
        if(!layer.id) return layer.text;
        const $opt = $(layer.element);
        return $('<div class="adjust-opt">'
            + '<span class="adjust-opt-form">' + ($opt.data('form') || 'FIFO') + '</span>'
            + '<span class="adjust-opt-date">' + ($opt.data('date') || '') + '</span>'
            + '<span class="adjust-opt-sisa">Sisa ' + ($opt.data('sisa') || 0) + '</span>'
            + '</div>');
    }

    function resetAdjustForm(){
        const form = document.getElementById('adjustForm');
        form.reset();

        switchAdjustTab('form');

        document.getElementById('adjustType').value = '';
        document.getElementById('adjustLayerField').style.display = 'none';
        document.getElementById('adjustQty').disabled = true;
        document.getElementById('adjustQty').value = '';
        document.getElementById('adjustQty').removeAttribute('max');
        document.getElementById('adjustQtyHint').textContent = '';
        document.getElementById('adjustQtyHint').classList.remove('is-warn');
        document.getElementById('adjustSubmitBtn').disabled = true;
        document.getElementById('adjustSubmitBtn').hidden = true;

        const loc = document.getElementById('adjustLocation');
        loc.disabled = true;
        loc.innerHTML = '<option value="" selected disabled>Pilih jenis adjustment dahulu</option>';

        if($.fn.select2 && $('#adjustLayer').data('select2')){
            $('#adjustLayer').val('').trigger('change.select2');
        }

        document.querySelectorAll('.adjust-select, .adjust-input, .adjust-textarea').forEach(el => {
            el.classList.remove('is-invalid');
        });
    }

    function loadFifoLayers(productId){
        fetch('stock-detail.php?id=' + encodeURIComponent(productId))
        .then(res => res.text())
        .then(html => {
            const temp = document.createElement('div');
            temp.innerHTML = html;
            const rows = temp.querySelectorAll('tbody tr');

            adjustFifoLayers = [];
            rows.forEach((tr) => {
                const cells = tr.querySelectorAll('td');
                if(cells.length < 4) return;

                const itemId = tr.getAttribute('data-item-id');
                if(!itemId) return;

                const dateText = cells[0].querySelector('.date-main')?.textContent.trim() || '';
                const formText = cells[0].querySelector('.form-badge')?.textContent.trim() || '';
                const qtyText  = cells[1].querySelector('.badge-value')?.textContent.trim() || '0';
                const remText  = cells[2].querySelector('.badge-value')?.textContent.trim() || '0';
                const priceText= cells[3].querySelector('.badge-value')?.textContent.trim() || 'Rp 0';

                const qty = parseInt(qtyText);
                const remaining = parseInt(remText);

                adjustFifoLayers.push({
                    id: parseInt(itemId),
                    date: dateText,
                    form: formText,
                    qty: qty,
                    remaining: remaining,
                    price: priceText
                });
            });
        });
    }

    $(document).on('click', '.adjust-tab', function(){
        switchAdjustTab($(this).data('adjust-tab'));
    });

    $(document).on('change', '#adjustType', function(){
        const type = $(this).val();
        const locSelect = document.getElementById('adjustLocation');
        const layerField = document.getElementById('adjustLayerField');
        const qtyInput = document.getElementById('adjustQty');

        locSelect.innerHTML = '';
        qtyInput.value = '';
        qtyInput.disabled = true;
        layerField.style.display = 'none';
        document.getElementById('adjustQtyHint').textContent = '';
        document.getElementById('adjustSubmitBtn').disabled = true;
        document.getElementById('adjustSubmitBtn').hidden = true;

        if(!type){
            locSelect.disabled = true;
            locSelect.innerHTML = '<option value="">Pilih jenis adjustment dahulu</option>';
            return;
        }

        locSelect.disabled = false;
        locSelect.innerHTML = '<option value="" selected disabled>Pilih lokasi stok...</option>'
            + '<option value="gudang">Stok Gudang</option>'
            + '<option value="kantin">Stok Kantin</option>';
    });

    $(document).on('change', '#adjustLocation', function(){
        const location = $(this).val();
        const layerField = document.getElementById('adjustLayerField');
        const qtyInput = document.getElementById('adjustQty');
        const $layer = $('#adjustLayer');

        qtyInput.value = '';
        qtyInput.disabled = true;
        document.getElementById('adjustQtyHint').textContent = '';
        document.getElementById('adjustSubmitBtn').disabled = true;
        document.getElementById('adjustSubmitBtn').hidden = true;

        if(location === 'gudang'){
            layerField.style.display = '';

            initLayerSelect2();

            if(adjustFifoLayers.length === 0){
                $layer.html('<option value="">Tidak ada layer FIFO tersedia</option>').prop('disabled', true).trigger('change.select2');
                return;
            }

            let opts = '<option value="" selected>Pilih layer FIFO...</option>';
            adjustFifoLayers.forEach(function(layer){
                opts += '<option value="' + layer.id + '"'
                    + ' data-form="' + (layer.form || 'FIFO') + '"'
                    + ' data-date="' + layer.date + '"'
                    + ' data-sisa="' + layer.remaining + '">'
                    + layer.date + ' — ' + layer.form + ' (Sisa: ' + layer.remaining + ')'
                    + '</option>';
            });
            $layer.html(opts).prop('disabled', false).trigger('change.select2');

        } else if(location === 'kantin'){
            layerField.style.display = 'none';
            qtyInput.disabled = false;
            qtyInput.focus();
            document.getElementById('adjustQtyHint').textContent = 'Masukkan jumlah adjustment';
        } else {
            layerField.style.display = 'none';
        }

        validateAdjustForm();
    });

    function onLayerChange(){
        const layerId = $('#adjustLayer').val();
        const qtyInput = document.getElementById('adjustQty');
        const hint = document.getElementById('adjustQtyHint');

        if(!layerId || layerId === ''){
            qtyInput.disabled = true;
            qtyInput.value = '';
            hint.textContent = '';
            document.getElementById('adjustSubmitBtn').disabled = true;
            document.getElementById('adjustSubmitBtn').hidden = true;
            return;
        }

        const layer = adjustFifoLayers.find(l => l.id == layerId);
        const type = document.getElementById('adjustType').value;

        qtyInput.disabled = false;
        qtyInput.focus();

        if(type === 'out'){
            qtyInput.max = layer ? layer.remaining : '';
            hint.textContent = 'Maksimal ' + (layer ? layer.remaining : 0) + ' ' + (adjustProductData?.unit || '');
            hint.classList.add('is-warn');
        } else {
            qtyInput.removeAttribute('max');
            hint.textContent = 'Masukkan jumlah penambahan stok';
            hint.classList.remove('is-warn');
        }

        validateAdjustForm();
    }

    $(document).on('input', '#adjustQty', validateAdjustForm);
    $(document).on('input', '#adjustNote', validateAdjustForm);

    function validateAdjustForm(){
        const type = document.getElementById('adjustType').value;
        const location = document.getElementById('adjustLocation').value;
        const qty = parseInt(document.getElementById('adjustQty').value) || 0;
        const note = document.getElementById('adjustNote').value.trim();
        const submitBtn = document.getElementById('adjustSubmitBtn');

        submitBtn.hidden = true;
        submitBtn.disabled = true;

        if(!type || !location || qty <= 0 || note === ''){
            return;
        }

        if(location === 'gudang'){
            const layerId = $('#adjustLayer').val();
            if(!layerId || layerId === ''){
                return;
            }

            if(type === 'out'){
                const layer = adjustFifoLayers.find(l => l.id == layerId);
                let maxQty = layer ? layer.remaining : 0;

                /* Mode edit: sisa layer termasuk efek adjustment lama.
                   Koreksi max dengan efek lama dibatalkan. */
                if(currentEditAdj && currentEditAdj.location === 'gudang' && currentEditAdj.purchase_item_id == layerId){
                    if(currentEditAdj.type === 'out') maxQty += currentEditAdj.qty;
                    else maxQty -= currentEditAdj.qty;
                    if(maxQty < 0) maxQty = 0;
                }

                if(qty > maxQty){
                    document.getElementById('adjustQty').classList.add('is-invalid');
                    return;
                }
            }
        }

        document.getElementById('adjustQty').classList.remove('is-invalid');
        submitBtn.hidden = false;
        submitBtn.disabled = false;
    }

    $(document).on('submit', '#adjustForm', function(e){
        e.preventDefault();

        const submitBtn = document.getElementById('adjustSubmitBtn');
        if(submitBtn.hidden || submitBtn.disabled) return;

        const spinner = submitBtn.querySelector('.adjust-btn-spinner');
        const btnText = submitBtn.querySelector('.adjust-btn-text');

        submitBtn.disabled = true;
        spinner.hidden = false;
        btnText.textContent = 'Saving...';

        const formData = {
            edit_id: currentEditId,
            product_id: adjustProductData.id,
            type: document.getElementById('adjustType').value,
            location: document.getElementById('adjustLocation').value,
            purchase_item_id: document.getElementById('adjustLocation').value === 'gudang' ? ($('#adjustLayer').val() || 0) : 0,
            qty: document.getElementById('adjustQty').value,
            note: document.getElementById('adjustNote').value.trim()
        };

        fetch('stock-adjustment-save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                currentEditId = 0;
                currentEditAdj = null;

                /* Mutasi stok langsung refresh sesuai lokasi yang di-adjust */
                if(window.activeStockId){
                    window.mutasiTab = formData.location;
                    applyMutasiTab(window.mutasiTab);
                    loadMutasi();
                }

                /* Tabel stok & layer FIFO ikut terbarui */
                loadStockTable();
                loadFifoLayers(adjustProductData.id);

                /* Riwayat di modal ikut terbarui */
                loadAdjustHistory(adjustProductData.id);
                switchAdjustTab('history');

                resetAdjustFormValues();
            } else {
                alert('Gagal menyimpan: ' + (data.message || 'Error'));
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan: ' + err.message);
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.hidden = true;
            btnText.textContent = 'Save Adjustment';
            validateAdjustForm();
        });
    });

    /* Reset nilai form saja (tanpa pindah tab) supaya user bisa langsung
       input adjustment berikutnya setelah simpan. */
    function resetAdjustFormValues(){
        document.getElementById('adjustType').value = '';
        const loc = document.getElementById('adjustLocation');
        loc.value = '';
        loc.disabled = true;
        loc.innerHTML = '<option value="" selected disabled>Pilih jenis adjustment dahulu</option>';

        document.getElementById('adjustLayerField').style.display = 'none';
        document.getElementById('adjustQty').disabled = true;
        document.getElementById('adjustQty').value = '';
        document.getElementById('adjustQty').removeAttribute('max');
        document.getElementById('adjustQtyHint').textContent = '';
        document.getElementById('adjustQtyHint').classList.remove('is-warn');
        document.getElementById('adjustSubmitBtn').disabled = true;
        document.getElementById('adjustSubmitBtn').hidden = true;
        document.getElementById('adjustNote').value = '';

        if($.fn.select2 && $('#adjustLayer').data('select2')){
            $('#adjustLayer').val('').trigger('change.select2');
        }
    }

    $(document).on('click', '[data-adjust-close]', closeAdjust);

    $(document).on('keydown', function(e){
        if(e.key === 'Escape' && document.getElementById('adjustModal').classList.contains('is-open')){
            closeAdjust();
        }
    });

    /* ============================ EDIT & DELETE ADJUSTMENT ============================ */

    let currentEditId = 0;
    let currentEditAdj = null;

    $(document).on('click', '.adjust-hist-edit', function(){
        const adjId = $(this).data('adj-id');
        if(!adjId) return;

        fetch('stock-adjustment-edit.php?id=' + adjId)
        .then(res => res.json())
        .then(data => {
            if(!data.success){
                QToast('Gagal memuat data', data.message || 'Error', 'error');
                return;
            }

            currentEditId = data.data.id;
            switchAdjustTab('form');
            prefillAdjustForm(data.data);
        })
        .catch(err => QToast('Terjadi kesalahan', err.message, 'error'));
    });

    /* Isi form adjustment dari data riwayat tanpa cascade event change:
       setiap kontrol di-set langsung supaya tidak bergantung timing. */
    function prefillAdjustForm(adj){
        currentEditAdj = adj;

        const typeSel   = document.getElementById('adjustType');
        const locSel    = document.getElementById('adjustLocation');
        const layerField= document.getElementById('adjustLayerField');
        const qtyInput  = document.getElementById('adjustQty');
        const hint      = document.getElementById('adjustQtyHint');

        document.querySelectorAll('.adjust-select, .adjust-input, .adjust-textarea').forEach(el => el.classList.remove('is-invalid'));

        typeSel.value = adj.type;

        locSel.disabled = false;
        locSel.innerHTML = '<option value="" disabled>Pilih lokasi stok...</option>'
            + '<option value="gudang">Stok Gudang</option>'
            + '<option value="kantin">Stok Kantin</option>';
        locSel.value = adj.location;

        qtyInput.disabled = false;
        qtyInput.value = adj.qty;
        document.getElementById('adjustNote').value = adj.note || '';
        document.getElementById('adjustSubmitBtn').disabled = true;

        if(adj.location === 'gudang'){
            layerField.style.display = '';
            initLayerSelect2();

            let opts = '<option value="">Pilih layer FIFO...</option>';
            adjustFifoLayers.forEach(function(layer){
                const sel = (layer.id == adj.purchase_item_id) ? ' selected' : '';
                opts += '<option value="' + layer.id + '"' + sel
                    + ' data-form="' + (layer.form || 'FIFO') + '"'
                    + ' data-date="' + layer.date + '"'
                    + ' data-sisa="' + layer.remaining + '">'
                    + layer.date + ' — ' + layer.form + ' (Sisa: ' + layer.remaining + ')'
                    + '</option>';
            });

            /* Layer yang di-adjust bisa saja sudah habis (sisa 0) sehingga
               tidak ikut di daftar; tetap tampilkan supaya bisa diedit. */
            if(adj.purchase_item_id > 0 && !adjustFifoLayers.some(l => l.id == adj.purchase_item_id)){
                opts += '<option value="' + adj.purchase_item_id + '" selected'
                    + ' data-form="FIFO" data-date="" data-sisa="0">'
                    + 'Layer #' + adj.purchase_item_id + ' (Sisa: 0)'
                    + '</option>';
            }

            $('#adjustLayer').html(opts).prop('disabled', false).trigger('change.select2');

            const max = editLayerMax(adj);
            if(adj.type === 'out'){
                qtyInput.setAttribute('max', max);
                hint.textContent = 'Maksimal ' + max + ' ' + (adjustProductData?.unit || '');
                hint.classList.add('is-warn');
            } else {
                qtyInput.removeAttribute('max');
                hint.textContent = 'Masukkan jumlah penambahan stok';
                hint.classList.remove('is-warn');
            }
        } else {
            layerField.style.display = 'none';
            hint.textContent = 'Masukkan jumlah adjustment';
            hint.classList.remove('is-warn');
        }

        validateAdjustForm();
    }

    /* Batas qty 'out' saat mode edit: sisa layer sekarang dikoreksi dengan
       efek adjustment lama yang masih terbukukan di layer yang sama. */
    function editLayerMax(adj){
        const layer = adjustFifoLayers.find(l => l.id == adj.purchase_item_id);
        let max = layer ? layer.remaining : 0;
        if(adj.type === 'out') max += adj.qty; else max -= adj.qty;
        if(max < 0) max = 0;
        return max;
    }

    $(document).on('click', '.adjust-hist-delete', function(){
        const adjId = $(this).data('adj-id');
        if(!adjId) return;

        QConfirm(
            'Hapus Adjustment',
            'Stok akan dikembalikan ke kondisi sebelum adjustment. Lanjutkan?',
            {
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal',
                icon: 'fa-trash-can',
                confirmClass: 'q-confirm-btn-danger',
                iconClass: 'q-confirm-icon-danger'
            }
        ).then(ok => {
            if(!ok) return;

            fetch('stock-adjustment-delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: adjId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    QToast('Berhasil', 'Adjustment berhasil dihapus', 'success');
                    if(window.activeStockId){
                        loadMutasi();
                    }
                    loadStockTable();
                    loadFifoLayers(adjustProductData.id);
                    loadAdjustHistory(adjustProductData.id);
                } else {
                    QToast('Gagal menghapus', data.message || 'Error', 'error');
                }
            })
            .catch(err => QToast('Terjadi kesalahan', err.message, 'error'));
        });
    });
</script>

</body>
</html>
