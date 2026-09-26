<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"
    SELECT
        pi.id AS item_id,
        COALESCE(products.name, '') AS name,
        products.unit AS unit,
        pi.qty_buy,
        pi.unit_buy,
        pi.price_buy
    FROM purchase_items pi
    LEFT JOIN products
        ON products.id = pi.product_id
    WHERE pi.purchase_id = '$id'
      AND pi.deleted_at IS NULL
      AND pi.qty_buy IS NOT NULL
    ORDER BY pi.id ASC
");

if(!$q){
    die(mysqli_error($conn));
}

$count = mysqli_num_rows($q);

if($count === 0){ ?>

<p class="text-muted mb-0">
    <i class="fas fa-info-circle me-1"></i>
    Form ini tidak memiliki daftar belanja yang bisa diinput.
</p>

<?php } else { $i = 0; while($d = mysqli_fetch_assoc($q)){ $i++; ?>

<div class="item-row purchase-card mb-3">

    <input type="hidden"
           name="item_id[]"
           value="<?= $d['item_id'] ?>">

    <!-- HEAD: nama produk + daftar belanja -->
    <div class="item-head">
        <div class="item-index"><?= $i ?></div>

        <div class="item-name">
            <i class="fas fa-box"></i>
            <span><?= htmlspecialchars($d['name']) ?></span>
        </div>

        <div class="item-plan">
            <span class="plan-label">Belanja</span>
            <span class="plan-chip plan-qty">
                <i class="fas fa-basket-shopping"></i>
                <?= (int)$d['qty_buy'] ?> <?= htmlspecialchars($d['unit_buy'] ?: $d['unit']) ?>
            </span>
            <span class="plan-chip plan-price">
                <i class="fas fa-tag"></i>
                <?= $d['price_buy'] !== null ? 'Rp ' . number_format((float)$d['price_buy'], 0, ',', '.') : '-' ?>
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
                   placeholder="Qty input stok"
                   min="0"
                   required>
        </div>

        <div class="input-group-modern">
            <div class="input-icon">
                <i class="fas fa-balance-scale"></i>
            </div>
            <input type="text"
                   class="form-control"
                   value="<?= htmlspecialchars($d['unit']) ?>"
                   readonly>
        </div>

        <div class="input-group-modern">
            <div class="input-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <input type="number"
                   name="price[]"
                   class="form-control"
                   placeholder="Harga beli satuan"
                   min="0"
                   required>
        </div>
    </div>

</div>

<?php } ?>

<?php } ?>