<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);

$query = mysqli_query($conn, "
    SELECT
        o.id,
        o.code,
        o.tanggal,
        o.total,
        o.status_payment,
        COALESCE(NULLIF(u.fullname, ''), u.username, '-') AS cashier_name
    FROM orders o
    LEFT JOIN users u ON o.staff_id = u.id
    WHERE DATE(o.tanggal) BETWEEN '$first' AND '$last'
    ORDER BY o.tanggal DESC, o.id DESC
");

$no = 1;
$totalOmzet = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        if ($row['status_payment'] === 'paid') {
            $totalOmzet += (float) $row['total'];
        }
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center fw-bold"><?= htmlspecialchars($row['code']) ?></td>
            <td class="text-center"><?= report_date_id($row['tanggal']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['cashier_name']) ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['total']) ?></td>
            <td class="text-center"><?= report_status_badge($row['status_payment']) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(6, 'Tidak ada transaksi pada periode ini.');
}

report_foot($totalOmzet);
