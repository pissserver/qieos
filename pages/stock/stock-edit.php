<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$sql = "
SELECT 
    *
FROM products
WHERE id = '$id'
";

$q = mysqli_query($conn, $sql);

if(!$q){
    die(mysqli_error($conn));
}

$d = mysqli_fetch_assoc($q);
?>

<form id="editStockForm" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $id ?>">

<div class="section-title">Informasi Produk</div>

<div class="row">
    <div class="col-md-4">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-box"></i></div>
            <input type="text" name="product_name" class="form-control"
                value="<?= $d['name'] ?>">
        </div>
    </div>

    <div class="col-md-4">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-coins"></i></div>
            <input type="number" name="sell_price" class="form-control"
                value="<?= $d['sell_price'] ?>">
        </div>
    </div>

    <div class="col-md-4">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-image"></i></div>
            <input type="file" name="photo" class="form-control">
        </div>
    </div>
</div>

<div class="text-end mt-4 mb-3">
    <button type="submit" class="btn-save">
        <i class="fas fa-save me-1"></i> Update
    </button>
</div>

</form>