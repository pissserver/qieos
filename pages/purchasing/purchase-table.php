<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
SELECT 
    purchases.id,
    purchases.form,
    purchases.date,
    purchases.note,
    purchases.created_at,
    purchase_items.qty,
    purchase_items.remaining_qty,
    purchase_items.unit,

    GROUP_CONCAT(products.name SEPARATOR ', ') as products

FROM purchases

LEFT JOIN purchase_items 
    ON purchase_items.purchase_id = purchases.id

LEFT JOIN products 
    ON products.id = purchase_items.product_id

WHERE purchases.deleted_at IS NULL

GROUP BY purchases.id
ORDER BY purchases.id DESC
");
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/purchase-table.css">

<table id="purchaseTable" class="table table-hover align-middle">
<thead>
<tr>
    <th>ID FORM</th>
    <th class="text-center">TANGGAL PEMBELIAN</th>
    <th class="text-center">CATATAN</th>
    <th class="text-center">PEMBUATAN FORM</th>
    <th class="text-center">AKSI</th>
</tr>
</thead>

<tbody>
<?php while($d=mysqli_fetch_assoc($q)): ?>
<tr class="purchase-row">

    <!-- ID -->
    <td>
        <div class="purchase-box">
            <div class="purchase-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div>
                <div class="fw-bold"><?= $d['form'] ?></div>
                <small class="text-muted">
                    <i class="fas fa-receipt me-1"></i>Data Pembelian
                </small>
            </div>
        </div>
    </td>

    <!-- DATE -->
    <td class="text-center">
        <span class="date-badge">
            <i class="fas fa-calendar-alt"></i>
            <?= date('d F Y', strtotime($d['date'])) ?>
        </span>
    </td>

    <!-- NOTE -->
    <td class="text-center">
        <span class="note-badge">
            <i class="fas fa-sticky-note"></i>
            <?= $d['note'] ?: 'Tidak ada catatan' ?>
        </span>
    </td>

    <!-- CREATED -->
    <td class="text-center">
        <span class="created-badge">
            <i class="fas fa-clock"></i>
            <?= date('d F Y', strtotime($d['created_at'])) ?>
        </span>
    </td>

    <!-- ACTION -->
    <td class="text-center">

        <?php if ($d['remaining_qty'] === $d['qty']): ?>

        <button class="action-btn btn-edit editPurchaseBtn" data-id="<?= $d['id'] ?>">
            <i class="fas fa-edit"></i>
        </button>

       <button class="action-btn btn-delete deletePurchaseBtn"
            data-id="<?= $d['id'] ?>"
            data-form="<?= $d['form'] ?>"
            data-products="<?= htmlspecialchars($d['products']) ?>"
            data-qty="<?= $d['qty'] ?>"
            data-unit="<?= $d['unit'] ?>">
            <i class="fas fa-trash"></i>
        </button>

        <?php else: ?>

        <span class="text-muted">
            <i class="fas fa-lock me-1"></i> Tidak dapat diubah
        </span>

        <?php endif; ?>
        
    </td>

</tr>
<?php endwhile; ?>
</tbody>
</table>
