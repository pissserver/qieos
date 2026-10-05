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

        <!-- DETAIL FIFO -->
        <div class="col-lg-6 col-md-12 fifo-panel-col">
            <div class="fifo-container">

                <div class="fifo-title">
                    <span class="fifo-icon">
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <div class="fifo-title-text">
                        <h5 class="mb-0">Layer FIFO</h5>
                        <small>Detail stok per batch pembelian</small>
                    </div>
                </div>

                <div id="fifo-detail" class="dt-premium">

                    <div class="fifo-empty">

                        <div class="fifo-empty-icon">
                            <i class="fas fa-box-open"></i>
                        </div>

                        <div class="fifo-empty-title">Belum ada detail</div>
                        <div class="fifo-empty-sub">
                            Klik salah satu produk di samping untuk melihat detail FIFO
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

    <script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<script>
    /* Breakpoint, pager, dan chip info sekarang milik helper bersama
       script/datatable-compact.js, sama seperti Master & Purchasing. Jadi
       angka 575.98 tidak lagi ditulis ulang di file ini - kalau suatu saat
       berubah, cukup diubah satu tempat. */
    function isStockMobile(){
        return MP_TABLE.isMobile();
    }

    let stockIsMobile = isStockMobile();

    function stockDataTable(){
        const mobile = isStockMobile();

        const options = {
            pageLength: 5,
            lengthMenu: [[5,10,25,50],[5,10,25,50]],

            /* Pager kustom dari script/datatable-compact.js: 3 nomor di
               mobile, 5 di tablet/desktop, halaman 1 & terakhir dikunci di
               dua ujung, dan "…" hanya muncul kalau memang ada nomor yang
               disembunyikan. Bentuk pil & warnanya sudah diatur di
               css/components/datatable-premium.css.

               DULU mobile memakai "simple_numbers" dengan pageLength 4,
               sekarang keduanya disamakan supaya urutan pil (panah, 1 2 3,
               elipsis, halaman terakhir, panah) persis sama dengan Master,
               Purchasing, dan Mutasi. */
            pagingType: "mp_compact",

            /* Chip "Menampilkan 1-5 dari 137 produk", plus
               "dari N produk" kalau sedang difilter. */
            infoCallback: MP_TABLE.infoCallback('produk'),

            searchDelay: 250,

            /* Opsi "responsive: true" DIHAPUS. Opsi itu hanya dibaca oleh
               extension Responsive (responsive.dataTables.min.js) yang
               TIDAK dimuat di script/headscript.php - yang ada hanya
               jquery.dataTables.min.js + dataTables.bootstrap5.min.js,
               jadi nilainya diam-diam diabaikan. Responsif di halaman ini
               ditangani manual: tukar "dom" saat breakpoint mobile
               (lihat bawah) + overflow-x pada kolom tabel. */
            autoWidth: false,
            language:{
                search:"",
                searchPlaceholder:"Cari produk...",

                /* Panah Previous/Next memakai glyph, bukan teks "Previous".
                   Pada rentang 9 pil, dua pil teks memakan ~150px dari baris
                   pager sementara sisa ruang di layar 320-360px cuma
                   ~290-330px. Aksesibilitas tetap terjaga: aria-label
                   dibacakan bahasa Indonesia lewat oAria.paginate di bawah. */
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

        /* Mobile pakai "fltip": search + show entries + tabel + baris info chip +
           pagination. "l" dan "i" ikut dirender supaya user bisa menaikkan
           jumlah baris dan tahu posisinya di daftar tanpa scroll ke atas.

           DULU mobile memakai dom "ftp" (tanpa "l" & "i") plus rule
           "#stockTableContainer .dataTables_length/_info{display:none}" di
           css/pages/stock.css, sehingga Show n entries dan baris info
           disembunyikan di layar kecil. Sekarang keduanya tampil, sama
           seperti Master & Purchasing.

           "dom" HANYA di-set untuk mobile. Di desktop/tablet TIDAK di-set,
           sama seperti Master & Purchasing, jadi wrapper .row + .col-*
           bawaan DataTables Bootstrap 5 yang dipakai:

             <'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row dt-row'
             <'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>

           Wrapper itu yang bikin css/components/datatable-premium.css bisa
           meratakan baris atas (Show n entries kiri + search kanan) dan
           baris bawah (chip info kiri + pagination kanan). */
        if(mobile) options.dom = "fltip";

        const dt = $('#stockTable').DataTable(options);
        dt.columns.adjust();

        return dt;
    }

    function loadStockTable(){
        /* --- Ambil state SEBELUM markup ditimpa -------------------------------
           Penting diambil di sini: setelah innerHTML diganti, node
           <table id="stockTable"> yang lama ikut terbuang. isDataTable()
           mencari node berdasarkan identitas (o.nTable === t), jadi
           sesudah itu isDataTable('#stockTable') sudah false dan
           state-nya tidak bisa diambil lagi. */
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
            /* destroy dulu, node lama masih terpasang jadi aman. Kalau
               dibiarkan, instance-nya menumpuk di registry
               DataTable.settings setiap reload. Sesudahnya baris
               innerHTML = html menghapus node lama itu.

               DULU urutannya terbalik (innerHTML dulu, destroy belakangan)
               sehingga isDataTable() selalu false dan tidak ada yang
               pernah ikut di-destroy. */
            if(prev) prev.destroy();

            document.getElementById('stockTableContainer').innerHTML = html;

            setTimeout(()=>{
                stockIsMobile = isStockMobile();

                const dt = stockDataTable();

                if(keep){
                    /* Halaman di luar rentang otomatis dikembalikan
                       DataTable ke 0, jadi aman walau baris habis (mis.
                       seluruh hasil filter ikut terhapus). */
                    dt.page.len(keep.length)
                      .search(keep.keyword)
                      .page(keep.page)
                      .draw(false);
                }

                /* Baris yang sedang aktif harus disorot ulang karena
                   markup-nya baru, bukan hasil filter yang sama. */
                if(window.activeStockId){
                    highlightActiveRow(window.activeStockId);
                }
            },100);
        });
    }

    // Rebuild tabel saat ganti mobile/desktop
    let stockResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(stockResizeTimer);
        stockResizeTimer = setTimeout(function(){
            if(isStockMobile() === stockIsMobile) return;
            if(!$.fn.DataTable.isDataTable('#stockTable')) return;

            const current = $('#stockTable').DataTable();
            const page = current.page();
            const keyword = current.search();
            const length = current.page.len();

            // Highlight baris yang sedang aktif supaya tidak hilang saat tabel dibangun ulang
            const activeId = window.activeStockId;

            current.destroy();

            stockIsMobile = isStockMobile();
            const dt = stockDataTable();
            dt.page.len(length)
              .search(keyword)
              .page(page)
              .draw(false);

            /* Panel detail FIFO juga harus dibangun ulang supaya search,
               Show n entries, chip info, dan pager-nya ikut pindah mode.
               Di-re-init in-place (tanpa fetch) supaya isi panel tidak
               berkedip kosong. Kalau belum ada detail yang dimuat atau
               markup-nya yang empty state, dilewati saja. */
            const detailTable = document.getElementById('tableStock');
            if(detailTable && $.fn.dataTable.isDataTable(detailTable)){
                const currentDetail = $(detailTable).DataTable();
                const detailKeep = {
                    page: currentDetail.page(),
                    keyword: currentDetail.search(),
                    length: currentDetail.page.len()
                };

                currentDetail.destroy();

                const rebuilt = stockDetailDataTable();
                if(rebuilt){
                    rebuilt.page.len(detailKeep.length)
                            .search(detailKeep.keyword)
                            .page(detailKeep.page)
                            .draw(false);
                }
            }

            // Restore highlight
            if(activeId){
                highlightActiveRow(activeId);
            }
        }, 250);
    });

    // Track active row dan highlight
    window.activeStockId = null;
    window.fifoRequestToken = 0;
    window.fifoLoaded = false;

    function highlightActiveRow(id){
        $('.stock-row').removeClass('stock-row-active');
        const row = $('.stock-row[onclick*="loadDetail(' + id + ')"]');
        if(row.length){
            row.addClass('stock-row-active');
        }
    }

    $(document).ready(function(){
        loadStockTable();
    });

    /* Opsi tabel detail FIFO. Dipisah jadi fungsi supaya bisa dipakai lagi oleh
       handler resize - panel ini ikut perlu dibangun ulang ketika pindah
       mobile <-> desktop, kalau tidak kontrolnya (search, Show n entries,
       chip info, pager) tetap dalam mode yang lama. */
    function stockDetailOptions(){
        const detailOptions = {
            pageLength: 10,
            /* Panel detail tetap 10 baris (bukan 5 seperti tabel utama)
               supaya FIFO yang banyak tetap terbaca tanpa banyak scroll.
               Dropdown-nya hanya menawarkan angka yang masuk akal untuk
               panel selebar ini. */
            lengthMenu: [[10,25,50],[10,25,50]],
            searching: true,

            /* Pager + chip info yang sama dengan tabel utama, supaya panel
               kanan tidak terlihat seperti tabel kelas dua. */
            pagingType: "mp_compact",
            infoCallback: MP_TABLE.infoCallback('batch FIFO'),

            /* Opsi "responsive: !mobile" DIHAPUS. Extension Responsive tidak
               dimuat di script/headscript.php, jadi nilainya selalu
               diabaikan. Card mode mobile di panel ini dikerjakan sendiri
               oleh css/pages/stock-detail.css, cukup dengan menukar "dom". */
            autoWidth: false,
            ordering: false,
            language: {
                search: "",
                searchPlaceholder:"Cari detail...",

                paginate: {
                    previous:"&#8249;",  // ‹
                    next:"&#8250;"       // ›
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

        /* Sama seperti tabel utama: "dom" hanya di-set untuk mobile.
           Di desktop/tablet dibiarkan default supaya wrapper .row + .col-*
           bawaan Bootstrap 5 yang dipakai
           css/components/datatable-premium.css.

           DULU "l" & "i" tidak ada di mobile (dom "ftp") dan
           css/pages/stock-detail.css menyembunyikan
           "#tableStock_wrapper .dataTables_length/_info". Sekarang keduanya
           tampil supaya panel ini serasi dengan tabel utama. */
        if(isStockMobile()) detailOptions.dom = "fltip";

        return detailOptions;
    }

    function stockDetailDataTable(){
        const table = document.getElementById('tableStock');
        if(!table) return null;

        const dt = $(table).DataTable(stockDetailOptions());
        dt.columns.adjust();

        return dt;
    }

    function loadDetail(id){

        // Abaikan klik ulang pada baris yang sama
        if (String(window.activeStockId) === String(id) && window.fifoLoaded) return;

        // Highlight row yang diklik
        window.activeStockId = id;
        highlightActiveRow(id);

        const wrap = document.getElementById('fifo-detail');

        // Token request: mencegah respons lama menimpa data baru
        // saat user klik beberapa baris dengan cepat
        const token = ++window.fifoRequestToken;
        window.fifoLoaded = false;

        // Reset animasi masuk supaya tidak numpuk dengan request sebelumnya
        wrap.classList.remove('fifo-enter');

        // Tutup DataTable lama SEBELUM DOM diganti agar tidak menyisakan
        // wrapper/Styling yang bentrok dengan markup baru
        if ($.fn.DataTable.isDataTable('#tableStock')) {
            $('#tableStock').DataTable().destroy();
        }

        // Loading state langsung tampil tanpa jeda supaya tidak terasa "nunggu".
        // Content lama dicabut seketika, jadi tidak ada data lama yang
        // tertinggal kalau user klik baris lain sebelum request selesai.
        wrap.innerHTML = `
            <div class="fifo-loading">
                <div class="fifo-loading-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="fifo-loading-title">Memuat detail FIFO</div>
                <div class="fifo-loading-bar"><span></span></div>
            </div>
        `;

        fetch('stock-detail.php?id=' + encodeURIComponent(id))
        .then(res => res.text())
        .then(html => {

            // Abaikan respons yang sudah basi
            if (window.fifoRequestToken !== token) return;

            wrap.innerHTML = html;

            // Empty state dirender sebagai markup terpisah, bukan baris tabel,
            // jadi tidak ada sisa styling tabel yang ikut terbawa
            const table = document.getElementById('tableStock');

            const done = () => {
                if (window.fifoRequestToken !== token) return;
                window.fifoLoaded = true;

                wrap.classList.add('fifo-enter');
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        wrap.classList.remove('fifo-enter');
                    });
                });
            };

            if(!table){
                done();
                return;
            }

            if($.fn.dataTable.isDataTable(table)){
                $(table).DataTable().destroy();
            }

            stockDetailDataTable();

            done();

        });

    }
</script>

</body>
</html>