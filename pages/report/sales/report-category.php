<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);
$category = isset($_GET['category']) ? report_esc($conn, $_GET['category']) : '';

/* 'all' (atau kosong) = tampilkan seluruh kategori tanpa filter. */
$filterCategory = ($category !== '' && strtolower($category) !== 'all');
$catWhere = $filterCategory ? "AND p.category = '$category'" : '';

$query = mysqli_query($conn, "
    SELECT
        p.name,
        p.code,
        COALESCE(SUM(od.qty), 0) AS qty_sold,
        COALESCE(SUM(od.subtotal), 0) AS omzet
    FROM order_details od
    JOIN orders o ON od.order_id = o.id
    JOIN products p ON od.product_id = p.id
    WHERE o.status_payment = 'paid'
      AND DATE(o.tanggal) BETWEEN '$first' AND '$last'
      $catWhere
    GROUP BY p.id, p.name, p.code
    ORDER BY omzet DESC, p.name ASC
");

$no = 1;
$total = 0;
$totalQty = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $total += (float) $row['omzet'];
        $totalQty += (int) $row['qty_sold'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center fw-bold"><?= htmlspecialchars($row['name']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['code']) ?></td>
            <td class="text-center"><?= (int) $row['qty_sold'] ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['omzet']) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(5, 'Tidak ada penjualan pada periode tersebut.');
}

echo '<!--SPLIT_FOOT-->';
echo json_encode(array(
    'qty'   => number_format($totalQty, 0, ',', '.'),
    'omzet' => report_rp($total)
));
