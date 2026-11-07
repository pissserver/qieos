<?php
include '../../sessions/session.php';
include __DIR__ . '/../components/data/product-conversion.php';

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"
    SELECT
        pi.id AS item_id,
        pi.product_id,
        COALESCE(products.name, '') AS name,
        pi.qty_buy,
        pi.unit_buy,
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

// Satuan beli yang berbeda dari satuan produk wajib punya konversi.
// Bila ada minimal satu item seperti itu tanpa konversi, restock diblokir.
$hasBlock = false;
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
            Qty akan otomatis masuk ke <b>Stok Gudang</b>. Produk yang punya
            <b>konversi</b> akan dikonversi ke satuan stok lebih dulu.
            Setelah divalidasi, data tidak dapat diubah.
        </div>
    </div>

    <?php endif; ?>

    <div class="restock-view-list">

        <?php foreach($items as $i => $d): ?>

        <?php
            $isPending = $d['qty_stock'] === null;

            $conv        = product_conversion_for_unit($conn, isset($d['product_id']) ? $d['product_id'] : 0, $d['unit_buy']);
            $hasConvert  = $conv['has_convert'];
            $convFactor  = $conv['factor'];
            $baseUnit    = $conv['base_unit'] !== '' ? $conv['base_unit'] : $d['product_unit'];

            $productUnit = $d['product_unit'] !== '' ? $d['product_unit'] : $d['unit_buy'];
            $sameUnit    = strtolower(trim($d['unit_buy'])) === strtolower(trim($productUnit));
            $blocked     = $isPending && !$hasConvert && !$sameUnit;
            if($blocked) $hasBlock = true;

            if($isPending){
                if($hasConvert){
                    $qtyStockVal  = (int)round(((float)$d['qty_buy']) * $convFactor);
                    $unitStockVal = $baseUnit;
                } else {
                    $qtyStockVal  = (int)round((float)$d['qty_buy']);
                    $unitStockVal = $productUnit;
                }
                $remainVal = $qtyStockVal;
            } else {
                $qtyStockVal  = (int)$d['qty_stock'];
                $remainVal    = $d['remaining_qty'] !== null ? (int)$d['remaining_qty'] : $qtyStockVal;
                $unitStockVal = $d['unit_stock'] ?: ($d['product_unit'] ?: $d['unit_buy']);
            }
            $unitPriceStock = $qtyStockVal > 0
                ? (float)$d['price_buy'] / $qtyStockVal
                : null;
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

                <div class="rv-stat rv-stat-total">
                    <span class="rv-label">Total</span>
                    <span class="rv-value">Rp <?= number_format((float)$d['price_buy'], 0, ',', '.') ?></span>
                </div>
            </div>

            <div class="rv-conversion-row">
            <?php if($isPending): ?>
                <?php if($hasConvert): ?>
                <div class="rv-convert rv-convert-on">
                    <div class="rvc-header">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                        <span class="rvc-label">Konversi Otomatis</span>
                    </div>
                    <div class="rvc-body">
                        <div class="rvc-input">
                            <span class="rvc-qty"><?= product_qty_label($d['qty_buy']) ?></span>
                            <span class="rvc-unit"><?= htmlspecialchars($d['unit_buy']) ?></span>
                        </div>
                        <div class="rvc-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="rvc-result">
                            <span class="rvc-qty-big"><?= product_qty_label($qtyStockVal) ?></span>
                            <span class="rvc-unit-big"><?= htmlspecialchars($unitStockVal) ?></span>
                        </div>
                    </div>
                    <div class="rvc-footer">
                        <i class="fas fa-circle-info"></i>
                        <span>1 <?= htmlspecialchars($d['unit_buy']) ?> = <?= product_qty_label($convFactor) ?> <?= htmlspecialchars($baseUnit) ?></span>
                    </div>
                </div>
                <?php elseif($sameUnit): ?>
                <div class="rv-convert rv-convert-same">
                    <div class="rvc-header">
                        <i class="fas fa-circle-check"></i>
                        <span class="rvc-label">Satuan Sesuai</span>
                    </div>
                    <div class="rvc-body rvc-body-same">
                        <div class="rvc-result-same">
                            <span class="rvc-qty-same"><?= product_qty_label($qtyStockVal) ?></span>
                            <span class="rvc-unit-same"><?= htmlspecialchars($unitStockVal) ?></span>
                        </div>
                    </div>
                    <div class="rvc-footer">
                        <i class="fas fa-circle-info"></i>
                        <span>Qty belanja langsung masuk stok gudang</span>
                    </div>
                </div>
                <?php else: ?>
                <div class="rv-convert rv-convert-block">
                    <div class="rvc-header rvc-header-error">
                        <i class="fas fa-triangle-exclamation"></i>
                        <span class="rvc-label">Tidak Bisa Diproses</span>
                    </div>
                    <div class="rvc-body rvc-body-error">
                        <div class="rvc-error-detail">
                            <div class="rvc-error-row">
                                <span class="rvc-error-label">Satuan Beli:</span>
                                <span class="rvc-error-value"><?= htmlspecialchars($d['unit_buy']) ?></span>
                            </div>
                            <div class="rvc-error-row">
                                <span class="rvc-error-label">Satuan Produk:</span>
                                <span class="rvc-error-value"><?= htmlspecialchars($productUnit) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="rvc-footer rvc-footer-error">
                        <i class="fas fa-circle-exclamation"></i>
                        <span>Tambahkan konversi unit untuk produk ini atau sesuaikan satuan beli</span>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($unitPriceStock !== null && !$blocked): ?>
                <div class="rv-unit-price-detail">
                    <span class="rv-unit-price-label"><i class="fas fa-calculator"></i> Harga per unit stock</span>
                    <strong>Rp <?= number_format($unitPriceStock, 2, ',', '.') ?></strong>
                    <small>Rp <?= number_format((float)$d['price_buy'], 0, ',', '.') ?> ÷ <?= product_qty_label($qtyStockVal) ?> <?= htmlspecialchars($unitStockVal) ?></small>
                </div>
            <?php endif; ?>
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
                <div class="rv-field-item rv-field-price">
                    <span class="rv-target-label"><i class="fas fa-coins"></i> Harga Satuan</span>
                    <span class="rv-field<?= $unitPriceStock !== null ? ' is-filled' : '' ?>" data-value="<?= $unitPriceStock !== null ? 'Rp ' . number_format($unitPriceStock, 2, ',', '.') : '' ?>"><?= $unitPriceStock !== null ? 'Rp ' . number_format($unitPriceStock, 2, ',', '.') : '&mdash;' ?></span>
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

        <?php elseif($hasBlock): ?>

        <div class="restock-alert-block">
            <i class="fas fa-triangle-exclamation"></i>
            <span>
                <strong>Tidak bisa divalidasi.</strong>
                Ada item dengan satuan beli berbeda dari satuan produk dan belum punya konversi.
                Perbaiki satuan beli atau tambahkan konversi produk terlebih dahulu.
            </span>
        </div>

        <?php else: ?>

        <button type="submit" class="btn-validate-restock">
            <i class="fas fa-circle-check"></i>
            <span>Validasi &amp; Restock</span>
        </button>

        <?php endif; ?>

    </div>

</form>
