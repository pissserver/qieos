<?php
include '../../sessions/session.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$q = mysqli_query($conn, "
    SELECT 
        p.id,
        p.name,
        od.qty,
        o.code,
        o.tanggal
    FROM products p
    LEFT JOIN order_details od
        ON od.product_id = p.id
    LEFT JOIN orders o
        ON o.id = od.order_id
    WHERE p.id = $id
    AND o.status_payment = 'paid'
    ORDER BY o.tanggal DESC
");

$rows = [];
while($d = mysqli_fetch_assoc($q)){
    $rows[] = $d;
}

$hasRow = !empty($rows);

$bulan = [
    1 => 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];
?>

<?php if(!$hasRow): ?>

    <div class="mutation-detail-empty-state">
        <div class="mutation-detail-empty-icon">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <div class="mutation-detail-empty-title">Belum ada pengeluaran stok</div>
        <div class="mutation-detail-empty-sub">
            Produk ini belum pernah terjual dari kantin
        </div>
    </div>

<?php else: ?>

<div class="stock-wrapper">

    <div class="stock-header-detail mb-3">
        <i class="fa-solid fa-boxes-stacked"></i>
        Detail Penjualan Stok Kantin
    </div>

    <div class="table-responsive">
        <table id="tableMutation" class="table table-hover table-stock mb-0">

            <thead>
                <tr>
                    <th><i class="fa-regular fa-calendar"></i> Tanggal</th>
                    <th><i class="fa-solid fa-file-invoice"></i> Order</th>
                    <th><i class="fa-solid fa-cubes"></i> Qty</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach($rows as $d): ?>

                <?php
                    $tgl = strtotime($d['tanggal']);
                    $tanggal = date('d', $tgl).' '.$bulan[(int)date('m',$tgl)].' '.date('Y',$tgl);
                ?>

                <tr>

                    <!-- TANGGAL + BARANG -->
                    <td>
                        <div class="date-block">

                            <div class="date-main">
                                <i class="fa-regular fa-calendar"></i>
                                <?= $tanggal ?>
                            </div>

                            <div class="form-badge">
                                <i class="fas fa-box-open"></i>
                                <?= htmlspecialchars($d['name']) ?>
                            </div>

                        </div>
                    </td>

                    <!-- CODE ORDER -->
                    <td>
                        <span class="chip-order">
                            <span class="chip-order-icon">
                                <i class="fa-solid fa-receipt"></i>
                            </span>
                            <span class="chip-order-code"><?= htmlspecialchars($d['code']) ?></span>
                        </span>
                    </td>

                    <!-- QTY -->
                    <td>
                        <span class="chip-qty">
                            <span class="chip-qty-value"><?= (int)$d['qty'] ?></span>
                            <span class="chip-qty-unit">item</span>
                        </span>
                    </td>

                </tr>

            <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

<?php endif; ?>