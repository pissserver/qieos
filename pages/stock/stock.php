<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Stok Gudang - Qieos</title>
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
            <div class="section-card h-100">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Stok Gudang
                            </div>
                            <div class="panel-subtitle">
                                List stok produk siap transfer ke penjualan
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

    // Ganti periode -> muat ulang panel
    let mutasiPeriodTimer;
    $(document).on('change', '#mutasiFrom, #mutasiTo', function(){
        if(!window.activeStockId) return;
        clearTimeout(mutasiPeriodTimer);
        mutasiPeriodTimer = setTimeout(loadMutasi, 150);
    });

    // Tutup modal FIFO
    $(document).on('click', '[data-fifo-close]', closeFifo);
    $(document).on('keydown', function(e){
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

        loadStockTable();
    });
</script>

</body>
</html>
