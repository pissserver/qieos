<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mutasi Stok - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/mutation.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/mutation.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/mutation-detail.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/mutation-detail.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/datatable-premium.css?v=<?php echo filemtime(__DIR__ . '/../../css/components/datatable-premium.css'); ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <div class="row stock-layout">
        <!-- MUTASI STOK -->
        <div class="col-lg-6 col-md-12 stock-panel-col">
            <div class="section-card h-100">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Mutasi Stok
                            </div>
                            <div class="panel-subtitle">
                                Total stok gudang, stok penjualan, dan detail stok
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <!-- TABLE -->
                    <div class="table-responsive-wrap dt-premium" id="stockTableContainer">
                        <table class="table table-hover align-middle" id="stockTable">
                            <thead>
                                <tr style="font-size:13px;color:#64748b;">
                                    <th>Produk</th>
                                    <th class="text-center">Gudang</th>
                                    <th class="text-center">Kantin</th>
                                    <th class="text-center">Total Stok</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php
                            $q = mysqli_query($conn,"
                            SELECT
                                p.id,
                                p.name,
                                p.code,
                                p.photo,
                                COALESCE((SELECT SUM(pi.remaining_qty) FROM purchase_items pi WHERE pi.product_id = p.id AND pi.deleted_at IS NULL),0) stock,
                                COALESCE((SELECT SUM(ss.qty) FROM sales_stock ss WHERE ss.product_id = p.id),0) sales_qty
                            FROM products p
                            WHERE p.category != 'additional'
                            ORDER BY p.name ASC
                            ");
                            while($d=mysqli_fetch_assoc($q)): ?>

                            <tr class="stock-row"
                                onclick="loadDetail(<?= $d['id'] ?>)">

                                <td>
                                    <div class="product-wrap">

                                        <?php if(!empty($d['photo'])): ?>
                                        <img class="product-img"
                                            src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($d['photo']) ?>"
                                            alt="<?= htmlspecialchars($d['name']) ?>">
                                        <?php else: ?>
                                        <div class="product-icon">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                        <?php endif; ?>

                                        <div>
                                            <div class="fw-bold">
                                                <?= htmlspecialchars($d['name']) ?>
                                            </div>

                                            <small class="text-muted">
                                                <?= htmlspecialchars($d['code']) ?>
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                <td class="text-center">

                                    <span class="stock-badge stock-success">
                                        <i class="fas fa-cubes me-1"></i>
                                        <?= number_format($d['stock']) ?>
                                    </span>

                                </td>

                                <td class="text-center">

                                    <span class="stock-badge stock-danger">
                                        <i class="fas fa-cubes me-1"></i>
                                        <?= number_format($d['sales_qty']) ?>
                                    </span>

                                </td>

                                <td class="text-center">
                                    <span class="stock-badge stock-empty">
                                        <i class="fas fa-cubes me-1"></i>
                                        <?= number_format($d['stock'] + $d['sales_qty']) ?>
                                    </span>
                                </td>
                            </tr>

                            <?php endwhile; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAIL -->
        <div class="col-lg-6 col-md-12 fifo-panel-col">
            <div class="fifo-container">

                <div class="fifo-title">
                    <span class="fifo-icon">
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <div class="fifo-title-text">
                        <h5 class="mb-0">Detail Stok</h5>
                        <small>Pengeluaran stok dari penjualan kantin</small>
                    </div>
                </div>

                <div id="fifo-detail" class="dt-premium">

                    <div class="fifo-empty">

                        <div class="fifo-empty-icon">
                            <i class="fas fa-box-open"></i>
                        </div>

                        <div class="fifo-empty-title">Belum ada detail</div>
                        <div class="fifo-empty-sub">
                            Klik salah satu produk di samping untuk melihat pengeluaran stok
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
       script/datatable-compact.js, sama seperti Master, Purchasing, dan
       Stok Gudang. Jadi angka 575.98 tidak lagi ditulis ulang di file ini -
       kalau suatu saat berubah, cukup diubah satu tempat. */
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
               sekarang keduanya disamakan supaya urutannya persis sama
               dengan Master, Purchasing, dan Stok Gudang. */
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
           css/pages/mutation.css, sehingga Show n entries dan baris info
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
            const detailTable = document.getElementById('tableMutation');
            if(detailTable && $.fn.dataTable.isDataTable(detailTable)){
                const currentDetail = $(detailTable).DataTable();
                const detailKeep = {
                    page: currentDetail.page(),
                    keyword: currentDetail.search(),
                    length: currentDetail.page.len()
                };

                currentDetail.destroy();

                const rebuilt = mutationDetailDataTable();
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
        stockIsMobile = isStockMobile();
        stockDataTable();
    });

    /* Opsi tabel detail FIFO mutasi. Dipisah jadi fungsi supaya bisa dipakai
       lagi oleh handler resize - panel ini ikut perlu dibangun ulang ketika
       pindah mobile <-> desktop, kalau tidak kontrolnya (search, Show n
       entries, chip info, pager) tetap dalam mode yang lama. */
    function mutationDetailOptions(){
        const detailOptions = {
            pageLength: 10,
            /* Panel detail tetap 10 baris (bukan 5 seperti tabel utama)
               supaya riwayat mutasi yang panjang tetap terbaca tanpa banyak
               scroll. Dropdown-nya hanya menawarkan angka yang masuk akal
               untuk panel selebar ini. */
            lengthMenu: [[10,25,50],[10,25,50]],
            searching: true,

            /* Pager + chip info yang sama dengan tabel utama, supaya panel
               kanan tidak terlihat seperti tabel kelas dua. */
            pagingType: "mp_compact",
            infoCallback: MP_TABLE.infoCallback('riwayat mutasi'),

            /* Opsi "responsive: !mobile" DIHAPUS. Extension Responsive tidak
               dimuat di script/headscript.php, jadi nilainya selalu
               diabaikan. Card mode mobile di panel ini dikerjakan sendiri
               oleh css/pages/mutation-detail.css, cukup dengan menukar
               "dom". */
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
           css/pages/mutation-detail.css menyembunyikan
           "#tableMutation_wrapper .dataTables_length/_info". Sekarang
           keduanya tampil supaya panel ini serasi dengan tabel utama. */
        if(isStockMobile()) detailOptions.dom = "fltip";

        return detailOptions;
    }

    function mutationDetailDataTable(){
        const table = document.getElementById('tableMutation');
        if(!table) return null;

        const dt = $(table).DataTable(mutationDetailOptions());
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
        if ($.fn.DataTable.isDataTable('#tableMutation')) {
            $('#tableMutation').DataTable().destroy();
        }

        // Loading state langsung tampil tanpa jeda supaya tidak terasa "nunggu".
        // Content lama dicabut seketika, jadi tidak ada data lama yang
        // tertinggal kalau user klik baris lain sebelum request selesai.
        wrap.innerHTML = `
            <div class="fifo-loading">
                <div class="fifo-loading-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="fifo-loading-title">Memuat detail stok</div>
                <div class="fifo-loading-bar"><span></span></div>
            </div>
        `;

        fetch('mutation-detail.php?id=' + encodeURIComponent(id))
        .then(res => res.text())
        .then(html => {

            // Abaikan respons yang sudah basi
            if (window.fifoRequestToken !== token) return;

            wrap.innerHTML = html;

            // Empty state dirender sebagai markup terpisah, bukan baris tabel,
            // jadi tidak ada sisa styling tabel yang ikut terbawa
            const table = document.getElementById('tableMutation');

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

            mutationDetailDataTable();

            done();

        });

    }
</script>

</body>
</html>