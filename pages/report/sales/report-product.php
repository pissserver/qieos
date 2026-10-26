<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);
$productId = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

$query = mysqli_query($conn, "
    SELECT
        o.code,
        o.tanggal,
        od.qty,
        od.price,
        od.subtotal
    FROM order_details od
    JOIN orders o ON od.order_id = o.id
    WHERE od.product_id = $productId
      AND o.status_payment = 'paid'
      AND DATE(o.tanggal) BETWEEN '$first' AND '$last'
    ORDER BY o.tanggal DESC, o.id DESC
");

$no = 1;
$total = 0;
$totalQty = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $total += (float) $row['subtotal'];
        $totalQty += (int) $row['qty'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= report_date_id($row['tanggal']) ?></td>
            <td class="text-center fw-bold"><?= htmlspecialchars($row['code']) ?></td>
            <td class="text-center"><?= (int) $row['qty'] ?></td>
            <td class="text-center"><?= report_rp($row['price']) ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['subtotal']) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(6, 'Tidak ada penjualan untuk produk ini pada periode tersebut.');
}

echo '<!--SPLIT_FOOT-->';
echo json_encode(array(
    'qty'   => number_format($totalQty, 0, ',', '.'),
    'omzet' => report_rp($total)
));
