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
                    <div class="table-responsive-wrap" id="stockTableContainer">
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

                <div id="fifo-detail">

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

<script>
    const STOCK_MOBILE_BP = 575.98;

    function isStockMobile(){
        return window.innerWidth <= STOCK_MOBILE_BP;
    }

    let stockIsMobile = isStockMobile();

    function stockDataTable(){
        const mobile = isStockMobile();

        const options = {
            pageLength: mobile ? 4 : 5,
            lengthMenu: mobile
                ? [[4,5,10,25,50],[4,5,10,25,50]]
                : [[5,10,25,50],[5,10,25,50]],

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

        if(mobile) options.dom = "ftp";

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

            // Highlight baris yang sedang aktif supaya tidak hilang saat tabel dibangun ulang
            const activeId = window.activeStockId;

            current.destroy();

            stockIsMobile = isStockMobile();
            const dt = stockDataTable();
            dt.search(keyword).page(page).draw(false);

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
            const mobile = isStockMobile();

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

            $(table).DataTable({
                pageLength: 10,
                searching: true,
                /* Card mode mobile dikerjakan sendiri di CSS, jadi
                   DataTables Responsive dimatikan agar tidak membuat
                   tabel anak yang bentrok dengan layout kartu. */
                responsive: !mobile,
                autoWidth: false,
                ordering: false,
                language: {
                    search: "",
                    searchPlaceholder:"Cari detail...",
                    zeroRecords: `
                        <div class="empty-search">
                            <img src="../../assets/img/illustrations/empty-data.png" class="empty-img" style="width:200px;">
                            <div class="empty-title">Data tidak ditemukan</div>
                        </div>
                    `,
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "→",
                        previous: "←"
                    }
                }
            });

            done();

        });

    }
</script>

</body>
</html>