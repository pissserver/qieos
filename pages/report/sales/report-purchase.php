<?php

include '../../../sessions/session.php';
include 'report-helper.php';

$purchaseId = isset($_GET['purchase_id']) ? (int) $_GET['purchase_id'] : 0;

if ($purchaseId <= 0) {
    report_empty(11, 'Pilih form belanja untuk menampilkan detail pembelian.');
    exit;
}

$qPurchase = mysqli_query($conn, "
    SELECT p.id, p.date, p.form
    FROM purchases p
    WHERE p.id = $purchaseId
      AND p.deleted_at IS NULL
    LIMIT 1
");

if (!$qPurchase || mysqli_num_rows($qPurchase) === 0) {
    report_empty(11, 'Form belanja tidak ditemukan.');
    exit;
}

$purchase = mysqli_fetch_assoc($qPurchase);

$query = mysqli_query($conn, "
    SELECT
        pi.id,
        pi.product_id,
        pi.qty_buy,
        pi.price_unit,
        pi.price_buy,
        p.code AS product_code,
        p.name AS product_name,
        p.unit AS product_unit
    FROM purchase_items pi
    JOIN products p ON pi.product_id = p.id
    WHERE pi.purchase_id = $purchaseId
      AND pi.deleted_at IS NULL
      AND pi.qty_buy IS NOT NULL
    ORDER BY pi.id ASC
");

$no = 1;
$total = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $total += (float) $row['price_buy'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= report_date_id($purchase['date']) ?></td>
            <td class="text-center"><?= $purchase['id'] ?></td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
            <td class="text-center"><?= htmlspecialchars($row['product_code']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['product_name']) ?></td>
            <td class="text-center"><?= htmlspecialchars(strtoupper($row['product_unit'])) ?></td>
            <td class="text-center"><?= (int) $row['qty_buy'] ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['price_buy']) ?></td>
            <td class="text-center"><?= report_rp($row['price_unit']) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(11, 'Tidak ada item pada form belanja ini.');
}

report_foot($total);
