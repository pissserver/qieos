<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$sql = "
SELECT 
    purchases.id,
    purchases.form,
    purchases.note,

    purchase_items.qty,
    purchase_items.unit,
    purchase_items.buy_price,
    
    products.id as product_id,
    products.name,
    products.code,
    products.sell_price,
    products.category

FROM purchases
LEFT JOIN purchase_items 
    ON purchase_items.purchase_id = purchases.id

LEFT JOIN products
    ON products.id = purchase_items.product_id

WHERE purchases.id = '$id'
LIMIT 1
";

$q = mysqli_query($conn, $sql);

if(!$q){
    die(mysqli_error($conn));
}

$d = mysqli_fetch_assoc($q);

$formNumber = $d['form'];
?>

<form id="editPurchaseForm" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $id ?>">

<div class="row mb-3">
    <div class="col-md-12">
        <div class="input-group-modern">
            <div class="input-icon">
                <i class="fas fa-hashtag"></i>
            </div>
            <input type="text" class="form-control fw-bold" value="<?= $formNumber ?>" readonly>
        </div>
    </div>
</div>

<div class="section-title">Informasi Produk</div>

<div class="row">
    <div class="col-md-3">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-barcode"></i></div>
            <input type="text" name="code" class="form-control"
                   value="<?= $d['code'] ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-box"></i></div>
            <input type="text" name="product_name" class="form-control"
                   value="<?= $d['name'] ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-tags"></i></div>
            <select name="category" class="form-control">
                <option <?= $d['category']=='makanan'?'selected':'' ?> value="makanan">Makanan</option>
                <option <?= $d['category']=='minuman'?'selected':'' ?> value="minuman">Minuman</option>
                <option <?= $d['category']=='jajanan'?'selected':'' ?> value="jajanan">Jajanan</option>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-image"></i></div>
            <input type="file" name="photo" class="form-control">
        </div>
    </div>
</div>

<div class="section-title mt-2">Detail Stok</div>

<div class="row">
    <div class="col-md-3">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-cubes"></i></div>
            <input type="number" name="qty" class="form-control"
                   value="<?= $d['qty'] ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-balance-scale"></i></div>
            <input type="text" name="unit" class="form-control"
                   value="<?= $d['unit'] ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-wallet"></i></div>
            <input type="number" name="buy_price" class="form-control"
                   value="<?= $d['buy_price'] ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-coins"></i></div>
            <input type="number" name="sell_price" class="form-control"
                   value="<?= $d['sell_price'] ?>">
        </div>
    </div>
</div>

<div class="input-group-modern">
    <div class="input-icon">
        <i class="fas fa-sticky-note"></i>
    </div>
    <textarea name="note" class="form-control"><?= $d['note'] ?></textarea>
</div>

<div class="text-end mt-4 mb-3">
    <button type="submit" class="btn-save">
        <i class="fas fa-save me-1"></i> Update
    </button>
</div>

</form>