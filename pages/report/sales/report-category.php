<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);
$categoryRaw = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$category = report_esc($conn, $categoryRaw);

/* 'all' (atau kosong) = produk + racikan, 'racikan' = racikan saja. */
$isAll = ($categoryRaw === '' || strtolower($categoryRaw) === 'all');
$isComboOnly = (strtolower($categoryRaw) === 'racikan');
$range = "o.status_payment = 'paid' AND DATE(o.tanggal) BETWEEN '$first' AND '$last'";

$rows = array();

if (!$isComboOnly) {
    $catWhere = $isAll ? '' : "AND p.category = '$category'";

    $qProduct = mysqli_query($conn, "
        SELECT
            p.name,
            p.code,
            COALESCE(SUM(od.qty), 0) AS qty_sold,
            COALESCE(SUM(od.subtotal), 0) AS omzet,
            MIN(o.tanggal) AS first_sold
        FROM order_details od
        JOIN orders o ON od.order_id = o.id
        JOIN products p ON od.product_id = p.id
        WHERE $range
          $catWhere
        GROUP BY p.id, p.name, p.code
    ");

    while ($qProduct && $row = mysqli_fetch_assoc($qProduct)) {
        $row['is_combo'] = 0;
        $row['combo_items'] = '';
        $rows[] = $row;
    }
}

if ($isAll || $isComboOnly) {
    $rows = array_merge($rows, report_combo_sales($conn, $range));
}

report_sort_by_first_sold($rows);

$no = 1;
$total = 0;
$totalQty = 0;
$hasData = count($rows) > 0;

if ($hasData) {
    foreach ($rows as $row) {
        $total += (float) $row['omzet'];
        $totalQty += (int) $row['qty_sold'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center">
                <div class="fw-bold">
                    <?= htmlspecialchars($row['name']) ?>
                    <?php if (!empty($row['is_combo'])) : ?>
                        <span class="fw-normal text-muted">(Racikan)</span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($row['is_combo']) && !empty($row['combo_items'])) : ?>
                    <?php foreach (array_filter(explode('||', $row['combo_items'])) as $ingredient) : ?>
                        <div class="small text-muted"><?= htmlspecialchars($ingredient) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </td>
            <td class="text-center"><?= htmlspecialchars($row['code']) ?></td>
            <td class="text-center">
                <?= (int) $row['qty_sold'] ?>
                <?php if (!empty($row['is_combo'])) : ?>
                    <div class="small text-muted" style="white-space:normal;">sudah diinclude ke qty produk asli</div>
                <?php endif; ?>
            </td>
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
