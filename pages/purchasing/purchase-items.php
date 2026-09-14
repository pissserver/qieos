<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"
    SELECT
        pi.id AS item_id,
        COALESCE(products.name, '') AS name,
        products.unit AS unit
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

<?php } else { ?>

<?php while($d = mysqli_fetch_assoc($q)){ ?>

<div class="item-row row mb-3">

    <input type="hidden"
           name="item_id[]"
           value="<?= $d['item_id'] ?>">

    <!-- NAMA PRODUK -->
    <div class="col-md-5">
        <div class="input-group-modern">
            <div class="input-icon">
                <i class="fas fa-box"></i>
            </div>
            <input type="text"
                   class="form-control"
                   value="<?= htmlspecialchars($d['name']) ?>"
                   readonly>
        </div>
    </div>

    <!-- QTY -->
    <div class="col-md-2">
        <div class="input-group-modern">
            <div class="input-icon">
                <i class="fas fa-cubes"></i>
            </div>
            <input type="number"
                   name="qty[]"
                   class="form-control"
                   placeholder="Qty"
                   min="0"
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
                   value="<?= htmlspecialchars($d['unit']) ?>"
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
                   required>
        </div>
    </div>

</div>

<?php } ?>

<?php } ?>