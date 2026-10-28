<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);
$escFirst = mysqli_real_escape_string($conn, $first);
$escLast  = mysqli_real_escape_string($conn, $last);

$query = mysqli_query($conn, "
SELECT 
    DATE(tanggal) AS dt,
    COUNT(*) AS total_order,
    SUM(total) AS omzet
FROM orders
WHERE status_payment = 'paid'
  AND DATE(tanggal) BETWEEN '$escFirst' AND '$escLast'
GROUP BY DATE(tanggal)
ORDER BY dt ASC
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
        <td class="text-center"><?= $row['total_order'] ?></td>
        <td class="text-center fw-semibold"><?= report_rp($row['omzet']) ?></td>
    </tr>
        <?php
    }
} else {
    report_empty(4, 'Tidak ada omzet pada periode ini.');
}

report_foot($total);
