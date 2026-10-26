<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
if ($limit <= 0) {
    $limit = 10;
}

$query = mysqli_query($conn, "
    SELECT
        p.name,
        p.category,
        COALESCE(SUM(od.qty), 0) AS qty_sold,
        COALESCE(SUM(od.subtotal), 0) AS omzet
    FROM order_details od
    JOIN orders o ON od.order_id = o.id
    JOIN products p ON od.product_id = p.id
    WHERE o.status_payment = 'paid'
      AND DATE(o.tanggal) BETWEEN '$first' AND '$last'
    GROUP BY p.id, p.name, p.category
    ORDER BY qty_sold DESC, omzet DESC
    LIMIT $limit
");

$rank = 1;
$totalQty = 0;
$totalOmzet = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $totalQty += (int) $row['qty_sold'];
        $totalOmzet += (float) $row['omzet'];
        $rankClass = '';
        if ($rank === 1) {
            $rankClass = ' gold';
        } elseif ($rank === 2) {
            $rankClass = ' silver';
        } elseif ($rank === 3) {
            $rankClass = ' bronze';
        }
        $category = $row['category'] ? ucfirst($row['category']) : '-';
        ?>
        <tr>
            <td class="text-center">
                <span class="rank-badge<?= $rankClass ?>"><?= $rank ?></span>
            </td>
            <td class="text-center fw-bold"><?= htmlspecialchars($row['name']) ?></td>
            <td class="text-center"><?= htmlspecialchars($category) ?></td>
            <td class="text-center"><?= (int) $row['qty_sold'] ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['omzet']) ?></td>
        </tr>
        <?php
        $rank++;
    }
} else {
    report_empty(5, 'Tidak ada data produk terlaris pada periode ini.');
}

echo '<!--SPLIT_FOOT-->';
echo json_encode(array(
    'qty'   => number_format($totalQty, 0, ',', '.'),
    'omzet' => report_rp($totalOmzet)
));
