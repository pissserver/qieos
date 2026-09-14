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
        pi.price
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

    <?php foreach($items as $it): ?>

    <div class="item-row row mb-3">

        <input type="hidden" name="item_id[]" value="<?= $it['item_id'] ?>">

        <!-- NAMA PRODUK -->
        <div class="col-md-4">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-box"></i>
                </div>
                <input type="text"
                       class="form-control"
                       value="<?= htmlspecialchars($it['name']) ?>"
                       readonly>
            </div>
        </div>

        <!-- QTY -->
        <div class="col-md-3">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <input type="number"
                       name="qty[]"
                       class="form-control"
                       placeholder="Qty"
                       min="0"
                       value="<?= $it['qty'] !== null ? $it['qty'] : '' ?>"
                       required>
            </div>
        </div>

        <!-- SATUAN -->
        <div class="col-md-2">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <input type="text"
                       class="form-control"
                       value="<?= htmlspecialchars($it['unit']) ?>"
                       readonly>
            </div>
        </div>

        <!-- HARGA BELI -->
        <div class="col-md-3">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <input type="number"
                       name="price[]"
                       class="form-control"
                       placeholder="Harga Beli"
                       min="0"
                       value="<?= $it['price'] !== null ? $it['price'] : '' ?>"
                       required>
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