<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mutasi Stok - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/mutation.css">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <!-- HEADER -->
    <!-- <div class="stock-header mt-5">
        <div>
            <h3>Stok Gudang</h3>
            <p>Monitoring stok produk dan FIFO layer secara realtime</p>
        </div>

        <div class="header-icon">
            <i class="fas fa-warehouse"></i>
        </div>
    </div> -->

    <div class="row">
        <div class="col-md-7">
            <!-- Main Table -->
            <div class="section-card mb-5 mt-4">
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
                    <div class="table-responsive-wrap">
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

        <div class="col-md-5">
            <!-- DETAIL FIFO -->
            <div class="fifo-container mt-4 mb-5">

                <div class="fifo-title">
                    <i class="fas fa-layer-group text-primary"></i>
                    <h5 class="mb-0">Detail Stok</h5>
                </div>

                <div id="fifo-detail">

                    <div class="text-center py-4">

                        <i class="fas fa-box-open fa-3x text-secondary mb-3"></i>

                        <p class="text-muted mb-0">
                            Klik salah satu produk untuk melihat pengeluaran stok
                        </p>

                    </div>

                </div>

            </div>
        </div>
    </div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
    function loadDetail(id){

        fetch('mutation-detail.php?id=' + id)
        .then(res => res.text())
        .then(html => {

            document.getElementById("fifo-detail").innerHTML = html;

            if ($.fn.DataTable.isDataTable('#tableMutation')) {
                $('#tableMutation').DataTable().destroy();
            }

            $('#tableMutation').DataTable({
                pageLength: 10,
                searching: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                language: {
                    search: "",
                    searchPlaceholder:"Cari detail...",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "�",
                        previous: "�"
                    }
                }
            });

        });

    }

    $(document).ready(function(){
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
                            Silakan tambahkan stok terlebih dahulu
                        </div>
                    </div>
                `
            }
        });
    });
</script>

</body>
</html>