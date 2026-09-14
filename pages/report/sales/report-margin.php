<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);

$query = mysqli_query($conn, "
    SELECT
        p.name,
        COALESCE(SUM(od.qty), 0) AS qty_sold,
        COALESCE(AVG(od.price), 0) AS sell_price,
        COALESCE((
            SELECT AVG(pi.price)
            FROM purchase_items pi
            WHERE pi.product_id = p.id
              AND pi.deleted_at IS NULL
              AND pi.price > 0
        ), 0) AS price,
        COALESCE(SUM(od.subtotal), 0) AS revenue
    FROM order_details od
    JOIN orders o ON od.order_id = o.id
    JOIN products p ON od.product_id = p.id
    WHERE o.status_payment = 'paid'
      AND DATE(o.tanggal) BETWEEN '$first' AND '$last'
    GROUP BY p.id, p.name
    ORDER BY revenue DESC, p.name ASC
");

$no = 1;
$totalProfit = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $qty = (int) $row['qty_sold'];
        $sell = (float) $row['sell_price'];
        $buy = (float) $row['price'];
        $marginPct = $sell > 0 ? (($sell - $buy) / $sell) * 100 : 0;
        $profit = ($sell - $buy) * $qty;
        $totalProfit += $profit;
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center fw-bold"><?= htmlspecialchars($row['name']) ?></td>
            <td class="text-center"><?= $qty ?></td>
            <td class="text-center"><?= report_rp($buy) ?></td>
            <td class="text-center"><?= report_rp($sell) ?></td>
            <td class="text-center"><?= number_format($marginPct, 1, ',', '.') ?>%</td>
            <td class="text-center fw-semibold"><?= report_rp($profit) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(7, 'Tidak ada data margin produk pada periode ini.');
}

report_foot($totalProfit);
