<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);
$selection = report_product_selection(isset($_GET['product_id']) ? $_GET['product_id'] : 'all');
$productId = (int) $selection['id'];

$range = "o.status_payment = 'paid' AND DATE(o.tanggal) BETWEEN '$first' AND '$last'";

$normalSelect = "
    SELECT
        o.code,
        o.tanggal,
        od.qty,
        od.price,
        od.subtotal,
        p.name AS item_name,
        COALESCE(p.code, '') AS item_code,
        0 AS is_combo,
        NULL AS combo_items
    FROM order_details od
    JOIN orders o ON od.order_id = o.id
    JOIN products p ON od.product_id = p.id
";

$comboSelect = "
    SELECT
        o.code,
        o.tanggal,
        MIN(FLOOR(od.qty / COALESCE(NULLIF(pci.qty, 0), 1))) AS qty,
        ROUND(SUM(od.subtotal) / NULLIF(MIN(FLOOR(od.qty / COALESCE(NULLIF(pci.qty, 0), 1))), 0)) AS price,
        SUM(od.subtotal) AS subtotal,
        pc.name AS item_name,
        '-' AS item_code,
        1 AS is_combo,
        GROUP_CONCAT(
            CONCAT(ip.name, IF(pci.qty > 1, CONCAT(' x', pci.qty), ''))
            ORDER BY pci.id SEPARATOR '||'
        ) AS combo_items
    FROM order_details od
    JOIN orders o ON od.order_id = o.id
    JOIN product_combos pc ON pc.id = od.product_combo_id
    LEFT JOIN product_combo_items pci ON pci.combo_id = od.product_combo_id AND pci.product_id = od.product_id
    LEFT JOIN products ip ON ip.id = pci.product_id
";

if ($selection['type'] === 'combo') {
    $query = mysqli_query($conn, "
        $comboSelect
        WHERE $range
          AND od.product_combo_id = $productId
        GROUP BY od.order_id, od.product_combo_id, o.code, o.tanggal, pc.name
        ORDER BY o.tanggal ASC, o.code ASC
    ");
} elseif ($selection['type'] === 'product') {
    $query = mysqli_query($conn, "
        $normalSelect
        WHERE $range
          AND od.product_id = $productId
        ORDER BY o.tanggal ASC, o.id ASC
    ");
} else {
    $query = mysqli_query($conn, "
        SELECT * FROM (
            $normalSelect
            WHERE $range
              AND od.product_combo_id IS NULL

            UNION ALL

            $comboSelect
            WHERE $range
            GROUP BY od.order_id, od.product_combo_id, o.code, o.tanggal, pc.name
        ) t
        ORDER BY t.tanggal ASC, t.code ASC
    ");
}

$no = 1;
$total = 0;
$totalQty = 0;
$hasData = $query && mysqli_num_rows($query) > 0;

if ($hasData) {
    while ($row = mysqli_fetch_assoc($query)) {
        $total += (float) $row['subtotal'];
        $totalQty += (int) $row['qty'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center">
                <div class="fw-bold">
                    <?= htmlspecialchars($row['item_name']) ?>
                    <?php if (!empty($row['is_combo'])) : ?>
                        <span class="fw-normal text-muted">(Racikan)</span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($row['is_combo']) && !empty($row['combo_items'])) : ?>
                    <?php foreach (array_filter(explode('||', $row['combo_items'])) as $ingredient) : ?>
                        <div class="small text-muted"><?= htmlspecialchars($ingredient) ?></div>
                    <?php endforeach; ?>
                <?php elseif (!empty($row['item_code'])) : ?>
                    <div class="small text-muted"><?= htmlspecialchars($row['item_code']) ?></div>
                <?php endif; ?>
            </td>
            <td class="text-center"><?= report_date_id($row['tanggal']) ?></td>
            <td class="text-center fw-bold"><?= htmlspecialchars($row['code']) ?></td>
            <td class="text-center"><?= (int) $row['qty'] ?></td>
            <td class="text-center"><?= report_rp($row['price']) ?></td>
            <td class="text-center fw-semibold"><?= report_rp($row['subtotal']) ?></td>
        </tr>
        <?php
    }
} else {
    report_empty(7, 'Tidak ada penjualan untuk produk atau racikan pada periode tersebut.');
}

echo '<!--SPLIT_FOOT-->';
echo json_encode(array(
    'qty'   => number_format($totalQty, 0, ',', '.'),
    'omzet' => report_rp($total)
));
