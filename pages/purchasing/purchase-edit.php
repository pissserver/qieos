<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$qF = mysqli_query($conn,"SELECT form FROM purchases WHERE id='$id' AND deleted_at IS NULL LIMIT 1");
$f = mysqli_fetch_assoc($qF);

$q = mysqli_query($conn,"
    SELECT
        pi.id AS item_id,
        COALESCE(products.name, '') AS name,
        products.unit AS unit,
        pi.qty,
        pi.price,
        pi.qty_buy,
        pi.unit_buy,
        pi.price_buy
    FROM purchase_items pi
    LEFT JOIN products
        ON products.id = pi.product_id
    WHERE pi.purchase_id = '$id'
      AND pi.deleted_at IS NULL
    ORDER BY pi.id ASC
");

$items = [];
while($d = mysqli_fetch_assoc($q)){
    $items[] = $d;
}
?>

<form id="editPurchaseForm" action="purchase-action.php?action=save_items" method="POST">

    <input type="hidden" name="purchase_id" value="<?= $id ?>">

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-hashtag"></i>
                </div>
                <input type="text" class="form-control fw-bold" value="<?= $f ? htmlspecialchars($f['form']) : 'FORM-' . str_pad($id,7,'0',STR_PAD_LEFT) ?>" readonly>
            </div>
        </div>
    </div>

    <div class="section-title">Detail Item Pembelian</div>

    <?php if(empty($items)): ?>

    <p class="text-muted mb-0">
        <i class="fas fa-info-circle me-1"></i>
        Form ini tidak memiliki item.
    </p>

    <?php else: ?>

    <?php foreach($items as $i => $it): ?>

    <div class="item-row purchase-card mb-3">

        <input type="hidden" name="item_id[]" value="<?= $it['item_id'] ?>">

        <!-- HEAD: nama produk + daftar belanja -->
        <div class="item-head">
            <div class="item-index"><?= $i+1 ?></div>

            <div class="item-name">
                <i class="fas fa-box"></i>
                <span><?= htmlspecialchars($it['name']) ?></span>
            </div>

            <div class="item-plan">
                <span class="plan-label">Belanja</span>
                <span class="plan-chip plan-qty">
                    <i class="fas fa-basket-shopping"></i>
                    <?= $it['qty_buy'] !== null ? (int)$it['qty_buy'] . ' ' . htmlspecialchars($it['unit_buy'] ?: $it['unit']) : '-' ?>
                </span>
                <span class="plan-chip plan-price">
                    <i class="fas fa-tag"></i>
                    <?= $it['price_buy'] !== null ? 'Rp ' . number_format((float)$it['price_buy'], 0, ',', '.') : '-' ?>
                </span>
            </div>
        </div>

        <!-- INPUTS -->
        <div class="item-inputs">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <input type="number"
                       name="qty[]"
                       class="form-control"
                       placeholder="Qty"
                       min="0"
                       required
                       value="<?= $it['qty'] !== null ? $it['qty'] : '' ?>">
            </div>

            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <input type="text"
                       class="form-control"
                       value="<?= htmlspecialchars($it['unit']) ?>"
                       readonly>
            </div>

            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <input type="number"
                       name="price[]"
                       class="form-control"
                       placeholder="Harga beli"
                       min="0"
                       required
                       value="<?= $it['price'] !== null ? $it['price'] : '' ?>">
            </div>
        </div>

    </div>

    <?php endforeach; ?>

    <?php endif; ?>

    <div class="text-end mt-4 mb-3">
        <button type="submit" class="btn-save">
            <i class="fas fa-save me-1"></i> Update
        </button>
    </div>

</form>