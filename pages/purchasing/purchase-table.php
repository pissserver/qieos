<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
SELECT 
    purchases.id,
    purchases.form,
    purchases.date,
    purchases.created_at,

    GROUP_CONCAT(products.name SEPARATOR ', ') as products,
    COUNT(CASE WHEN purchase_items.qty IS NOT NULL AND purchase_items.deleted_at IS NULL THEN 1 END) as total_item,
    COUNT(CASE WHEN purchase_items.deleted_at IS NULL AND purchase_items.remaining_qty < purchase_items.qty THEN 1 END) as used_item

FROM purchases

LEFT JOIN purchase_items 
    ON purchase_items.purchase_id = purchases.id

LEFT JOIN products 
    ON products.id = purchase_items.product_id

WHERE purchases.deleted_at IS NULL
  AND EXISTS (
      SELECT 1
      FROM purchase_items pi2
      WHERE pi2.purchase_id = purchases.id
        AND pi2.deleted_at IS NULL
        AND pi2.qty IS NOT NULL
  )

GROUP BY purchases.id
ORDER BY purchases.id DESC
");

if(!$q){
    die(mysqli_error($conn));
}
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/purchase-table.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/purchase-table.css'); ?>">

<table id="purchaseTable" class="table table-hover align-middle">
<thead>
<tr>
    <th>ID FORM</th>
    <th class="text-center">TANGGAL PEMBELIAN</th>
    <th class="text-center">TOTAL ITEM</th>
    <th class="text-center">AKSI</th>
</tr>
</thead>

<tbody>
<?php while($d=mysqli_fetch_assoc($q)): ?>
<?php $isUsed = ((int)$d['used_item'] > 0); ?>
<tr class="purchase-row">

    <!-- ID -->
    <td>
        <div class="purchase-box">
            <div class="purchase-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div class="purchase-box-text">
                <div class="fw-bold"><?= htmlspecialchars($d['form']) ?></div>
                <small class="text-muted">
                    <i class="fas fa-receipt me-1"></i>Data Pembelian
                </small>

                <!-- ringkasan tambahan, hanya tampil di mobile -->
                <div class="mp-meta">
                    <span class="mp-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <?= date('d M Y', strtotime($d['date'])) ?>
                    </span>
                    <span class="mp-meta-item">
                        <i class="fas fa-boxes-stacked"></i>
                        <?= (int)$d['total_item'] ?> item
                    </span>
                </div>
            </div>
        </div>
    </td>

    <!-- DATE -->
    <td class="text-center mp-hide">
        <span class="date-badge">
            <i class="fas fa-calendar-alt"></i>
            <?= date('d F Y', strtotime($d['date'])) ?>
        </span>
    </td>

    <!-- TOTAL ITEM -->
    <td class="text-center mp-hide">
        <span class="created-badge">
            <i class="fas fa-boxes-stacked"></i>
            <?= (int)$d['total_item'] ?> item
        </span>
    </td>

    <!-- ACTION -->
    <td class="text-center">
        <div class="mp-actions">

            <?php if(!$isUsed): ?>

            <button class="action-btn btn-edit editPurchaseBtn" data-id="<?= (int)$d['id'] ?>">
                <i class="fas fa-edit"></i>
            </button>

            <?php else: ?>

            <span class="action-lock">
                <i class="fas fa-lock"></i>
                <span class="action-lock-text">Tidak dapat diubah</span>
            </span>

            <?php endif; ?>

        </div>
    </td>

</tr>
<?php endwhile; ?>
</tbody>
</table>
