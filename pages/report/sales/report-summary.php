<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);

$omzet = report_scalar($conn, "
    SELECT COALESCE(SUM(total), 0) AS v
    FROM orders
    WHERE status_payment = 'paid'
      AND DATE(tanggal) BETWEEN '$first' AND '$last'
");

$expense = report_scalar($conn, "
    SELECT COALESCE(SUM(pi.price_buy), 0) AS v
    FROM purchase_items pi
    JOIN purchases p ON pi.purchase_id = p.id
    WHERE p.deleted_at IS NULL
      AND pi.deleted_at IS NULL
      AND DATE(p.date) BETWEEN '$first' AND '$last'
");

$orderCount = report_scalar($conn, "
    SELECT COUNT(*) AS v
    FROM orders
    WHERE status_payment = 'paid'
      AND DATE(tanggal) BETWEEN '$first' AND '$last'
");

$profit = $omzet - $expense;

$rows = array(
    array(
        'label' => 'Omzet Penjualan',
        'note' => (int) $orderCount . ' pesanan terbayar',
        'amount' => $omzet
    ),
    array(
        'label' => 'Pengeluaran Belanja',
        'note' => 'Dari daftar belanja stok',
        'amount' => $expense
    ),
    array(
        'label' => 'Laba Bersih',
        'note' => 'Omzet dikurangi pengeluaran',
        'amount' => $profit
    ),
);

$no = 1;
foreach ($rows as $row) {
    ?>
    <tr>
        <td class="text-center"><?= $no++ ?></td>
        <td class="text-center fw-bold"><?= htmlspecialchars($row['label']) ?></td>
        <td class="text-center"><?= htmlspecialchars($row['note']) ?></td>
        <td class="text-center fw-semibold"><?= report_rp($row['amount']) ?></td>
    </tr>
    <?php
}

echo '<!--SPLIT_FOOT-->';
echo json_encode(array(
    'omzet' => report_rp($omzet),
    'expense' => report_rp($expense),
    'profit' => report_rp($profit)
));
