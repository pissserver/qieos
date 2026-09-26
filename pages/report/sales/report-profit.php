<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);

$query = mysqli_query($conn, "
    SELECT
        d.dt,
        COALESCE(o.omzet, 0) AS omzet,
        COALESCE(e.expense, 0) AS expense
    FROM (
        SELECT DATE(tanggal) AS dt
        FROM orders
        WHERE DATE(tanggal) BETWEEN '$first' AND '$last'
        UNION
        SELECT DATE(date) AS dt
        FROM purchases
        WHERE deleted_at IS NULL
          AND DATE(date) BETWEEN '$first' AND '$last'
    ) d
    LEFT JOIN (
        SELECT DATE(tanggal) AS dt, COALESCE(SUM(total), 0) AS omzet
        FROM orders
        WHERE status_payment = 'paid'
          AND DATE(tanggal) BETWEEN '$first' AND '$last'
        GROUP BY DATE(tanggal)
    ) o ON o.dt = d.dt
    LEFT JOIN (
        SELECT DATE(p.date) AS dt, COALESCE(SUM(pi.price_buy), 0) AS expense
        FROM purchases p
        JOIN purchase_items pi ON p.id = pi.purchase_id
        WHERE p.deleted_at IS NULL
          AND pi.deleted_at IS NULL
          AND DATE(p.date) BETWEEN '$first' AND '$last'
        GROUP BY DATE(p.date)
    ) e ON e.dt = d.dt
    ORDER BY d.dt DESC
");

$no = 1;
$totalProfit = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $omzet = (float) $row['omzet'];
        $expense = (float) $row['expense'];
        $profit = $omzet - $expense;
        $totalProfit += $profit;
        $isProfit = $profit >= 0;
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= report_date_id($row['dt']) ?></td>
            <td class="text-center"><?= report_rp($omzet) ?></td>
            <td class="text-center"><?= report_rp($expense) ?></td>
            <td class="text-center fw-semibold"><?= report_rp($profit) ?></td>
            <td class="text-center">
                <?php if ($isProfit): ?>
                    <span class="status-paid">Untung</span>
                <?php else: ?>
                    <span class="status-unpaid">Rugi</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
} else {
    report_empty(6, 'Tidak ada data laba rugi pada periode ini.');
}

report_foot($totalProfit);
