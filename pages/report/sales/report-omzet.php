<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);

$query = mysqli_query($conn, "
    SELECT
        DATE(tanggal) AS dt,
        COUNT(*) AS total_order,
        SUM(CASE WHEN status_payment = 'paid' THEN 1 ELSE 0 END) AS paid_count,
        SUM(CASE WHEN status_payment = 'waiting' THEN 1 ELSE 0 END) AS waiting_count,
        SUM(CASE WHEN status_payment = 'paid' THEN total ELSE 0 END) AS omzet
    FROM orders
    WHERE DATE(tanggal) BETWEEN '$first' AND '$last'
    GROUP BY DATE(tanggal)
    ORDER BY dt DESC
");

$no = 1;
$total = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $total += (float) $row['omzet'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= report_date_id($row['dt']) ?></td>
            <td class="text-center"><?= (int) $row['total_order'] ?></td>
            <td class="text-center"><?= (int) $row['paid_count'] ?></td>
            <td class="text-center"><?= (int) $row['waiting_count'] ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['omzet']) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(6, 'Tidak ada omzet pada periode ini.');
}

report_foot($total);
