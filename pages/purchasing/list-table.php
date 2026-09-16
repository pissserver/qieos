<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
    SELECT
        p.id,
        p.form,
        p.date,
        COUNT(pi.id) AS total_items,
        COALESCE(SUM(pi.price_buy), 0) AS total_price,
        MAX(CASE WHEN pi.qty IS NOT NULL THEN 1 ELSE 0 END) AS used_flag
    FROM purchases p
    LEFT JOIN purchase_items pi
        ON p.id = pi.purchase_id
        AND pi.deleted_at IS NULL
    WHERE p.deleted_at IS NULL
      AND pi.qty_buy IS NOT NULL
    GROUP BY p.id, p.form, p.date
    HAVING COUNT(pi.id) > 0
    ORDER BY p.date DESC, p.id DESC
");
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/list-table.css">

<table id="purchaseTable" class="table table-hover align-middle">
<thead>
<tr>
    <th>ID FORM</th>
    <th class="text-center">TANGGAL PEMBUATAN</th>
    <th class="text-center">TOTAL ITEM</th>
    <th class="text-center">TOTAL BELANJA</th>
    <th class="text-center">AKSI</th>
</tr>
</thead>

<tbody>
<?php
    while($d=mysqli_fetch_assoc($q)): 
?>
<tr class="purchase-row">

    <!-- ID -->
    <td>
        <div class="purchase-box">
            <div class="purchase-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div>
                <div class="fw-bold"><?= htmlspecialchars($d['form']) ?></div>
                <small class="text-muted">
                    <i class="fas fa-receipt me-1"></i>Daftar Belanja
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

    <!-- TOTAL ITEM -->
    <td class="text-center">
        <span class="note-badge">
            <i class="fas fa-box"></i>
            <?= $d['total_items'] ?>
        </span>
    </td>

    <!-- TOTAL PRICE -->
    <td class="text-center">
        <span class="created-badge">
            <i class="fas fa-money-bill"></i>
            Rp <?= number_format($d['total_price'], 0, ',', '.') ?>
        </span>
    </td>

    <!-- ACTION -->
    <td class="text-center">

        <button class="action-btn btn-edit editPurchaseBtn" data-id="<?= $d['id'] ?>">
            <i class="fas fa-edit"></i>
        </button>

        <?php if((int)$d['used_flag'] === 0): ?>
        <button class="action-btn btn-delete deletePurchaseBtn"
            data-id="<?= $d['id'] ?>"
            data-date="<?= $d['date'] ?>">
            <i class="fas fa-trash"></i>
        </button>
        <?php endif; ?>

        <button class="action-btn btn-print printPurchaseBtn"
            data-id="<?= $d['id'] ?>"
            data-date="<?= $d['date'] ?>">
            <i class="fas fa-print"></i>
        </button>
        
    </td>

</tr>
<?php endwhile; ?>
</tbody>
</table>