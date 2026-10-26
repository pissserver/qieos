<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);

$query = mysqli_query($conn, "
    SELECT
        p.id,
        p.form,
        p.date,
        COUNT(pi.id) AS total_items,
        COALESCE(SUM(pi.price_buy), 0) AS total_price
    FROM purchases p
    LEFT JOIN purchase_items pi
        ON p.id = pi.purchase_id
        AND pi.deleted_at IS NULL
    WHERE p.deleted_at IS NULL
      AND DATE(p.date) BETWEEN '$first' AND '$last'
      AND EXISTS (
          SELECT 1
          FROM purchase_items pi2
          WHERE pi2.purchase_id = p.id
            AND pi2.deleted_at IS NULL
            AND pi2.qty_buy IS NOT NULL
      )
    GROUP BY p.id, p.form, p.date
    ORDER BY p.date DESC, p.id DESC
");

$no = 1;
$total = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $total += (float) $row['total_price'];
        $form = $row['form'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= report_date_id($row['date']) ?></td>
            <td class="text-center fw-bold"><?= htmlspecialchars($form) ?></td>
            <td class="text-center"><?= (int) $row['total_items'] ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['total_price']) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(5, 'Tidak ada pengeluaran belanja pada periode ini.');
}

report_foot($total);
