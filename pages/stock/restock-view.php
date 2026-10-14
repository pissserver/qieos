<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"
    SELECT
        pi.id AS item_id,
        COALESCE(products.name, '') AS name,
        pi.qty_buy,
        pi.unit_buy,
        pi.price_unit,
        pi.price_buy,
        pi.qty_stock,
        pi.remaining_qty,
        pi.unit_stock,
        COALESCE(products.unit, '') AS product_unit
    FROM purchases pr
    LEFT JOIN purchase_items pi
        ON pi.purchase_id = pr.id
        AND pi.deleted_at IS NULL
    LEFT JOIN products
        ON products.id = pi.product_id
    WHERE pr.id = '$id'
      AND pi.id IS NOT NULL
    ORDER BY pi.id ASC
");

if(!$q){
    die(mysqli_error($conn));
}

$items = [];
$total = 0;
$pending = 0;
while($d = mysqli_fetch_assoc($q)){
    $items[] = $d;
    $total += (float)$d['price_buy'];
    if($d['qty_stock'] === null){
        $pending++;
    }
}

$isRestocked = !empty($items) && $pending === 0;
?>

<form id="restockValidateForm">

    <input type="hidden" name="id" value="<?= $id ?>">

    <?php if($isRestocked): ?>

    <div class="restock-notice restock-notice-done">
        <i class="fas fa-circle-check"></i>
        <div>
            <strong>Sudah direstock.</strong>
            Data sudah masuk ke <b>Stok Gudang</b> dan tidak dapat diubah lagi.
        </div>
    </div>

    <?php else: ?>

    <div class="restock-notice">
        <i class="fas fa-circle-info"></i>
        <div>
            <strong>Perhatian:</strong>
            Qty akan otomatis masuk ke <b>Stok Gudang</b> sesuai produk.
            Setelah divalidasi, data tidak dapat diubah.
        </div>
    </div>

    <?php endif; ?>

    <div class="restock-view-list">

        <?php foreach($items as $i => $d): ?>

        <?php
            $qtyStockVal = $d['qty_stock'] !== null ? (int)$d['qty_stock'] : (int)$d['qty_buy'];
            $remainVal   = $d['remaining_qty'] !== null ? (int)$d['remaining_qty'] : (int)$d['qty_buy'];
            $unitStockVal = $d['unit_stock'] ?: ($d['product_unit'] ?: $d['unit_buy']);
        ?>

        <div class="restock-view-item">

            <div class="rv-index"><?= $i + 1 ?></div>

            <div class="rv-name">
                <i class="fas fa-box"></i>
                <span><?= htmlspecialchars($d['name']) ?></span>
            </div>

            <div class="rv-stats">
                <div class="rv-stat">
                    <span class="rv-label">Qty Beli</span>
                    <span class="rv-value"><?= (int)$d['qty_buy'] ?> <?= htmlspecialchars($d['unit_buy']) ?></span>
                </div>

                <div class="rv-stat">
                    <span class="rv-label">Harga Satuan</span>
                    <span class="rv-value">Rp <?= number_format((float)$d['price_unit'], 0, ',', '.') ?></span>
                </div>

                <div class="rv-stat rv-stat-total">
                    <span class="rv-label">Total</span>
                    <span class="rv-value">Rp <?= number_format((float)$d['price_buy'], 0, ',', '.') ?></span>
                </div>
            </div>

            <div class="rv-fields">
                <div class="rv-field-item">
                    <span class="rv-target-label"><i class="fas fa-cubes-stacked"></i> Qty Stock</span>
                    <span class="rv-field<?= $isRestocked ? ' is-filled' : '' ?>" data-value="<?= $qtyStockVal ?>"><?= $isRestocked ? $qtyStockVal : '&mdash;' ?></span>
                </div>

                <div class="rv-field-item">
                    <span class="rv-target-label"><i class="fas fa-layer-group"></i> Remaining Qty</span>
                    <span class="rv-field<?= $isRestocked ? ' is-filled' : '' ?>" data-value="<?= $remainVal ?>"><?= $isRestocked ? $remainVal : '&mdash;' ?></span>
                </div>

                <div class="rv-field-item">
                    <span class="rv-target-label"><i class="fas fa-ruler"></i> Unit Stock</span>
                    <span class="rv-field rv-field-text<?= $isRestocked ? ' is-filled' : '' ?>" data-value="<?= htmlspecialchars($unitStockVal) ?>"><?= $isRestocked && $unitStockVal !== '' ? htmlspecialchars($unitStockVal) : '&mdash;' ?></span>
                </div>
            </div>

        </div>

        <?php endforeach; ?>

        <?php if(empty($items)): ?>
        <div class="restock-view-empty">
            <i class="fas fa-info-circle"></i>
            Tidak ada item pada daftar belanja ini.
        </div>
        <?php endif; ?>

    </div>

    <div class="restock-view-summary">
        <span class="rvs-label"><i class="fas fa-coins"></i> Total Belanja</span>
        <span class="rvs-value">Rp <?= number_format($total, 0, ',', '.') ?></span>
    </div>

    <div class="restock-view-footer">

        <?php if($isRestocked || empty($items)): ?>

        <div class="restock-alert-done">
            <i class="fas fa-shield-halved"></i>
            <span>Data sudah masuk ke stok gudang dan tidak dapat diubah.</span>
        </div>

        <?php else: ?>

        <button type="submit" class="btn-validate-restock">
            <i class="fas fa-circle-check"></i>
            <span>Validasi &amp; Restock</span>
        </button>

        <?php endif; ?>

    </div>

</form>
