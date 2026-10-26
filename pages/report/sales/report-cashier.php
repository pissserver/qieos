<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);
$cashierId = isset($_GET['cashier_id']) ? (int) $_GET['cashier_id'] : 0;

$query = mysqli_query($conn, "
    SELECT
        o.code,
        o.tanggal,
        o.total,
        o.status_payment
    FROM orders o
    WHERE o.staff_id = $cashierId
      AND DATE(o.tanggal) BETWEEN '$first' AND '$last'
    ORDER BY o.tanggal DESC, o.id DESC
");

$no = 1;
$total = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        if ($row['status_payment'] === 'paid') {
            $total += (float) $row['total'];
        }
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center fw-bold"><?= htmlspecialchars($row['code']) ?></td>
            <td class="text-center"><?= report_date_id($row['tanggal']) ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['total']) ?></td>
            <td class="text-center"><?= report_status_badge($row['status_payment']) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(5, 'Tidak ada transaksi untuk kasir ini pada periode tersebut.');
}

report_foot($total);
