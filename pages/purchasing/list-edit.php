<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"
    SELECT
        pi.id AS item_id,
        pi.product_id,
        COALESCE(products.name, '') AS name,
        pi.qty_buy,
        pi.unit_buy,
        pi.price_buy
    FROM purchases pr
    LEFT JOIN purchase_items pi
        ON pi.purchase_id = pr.id
        AND pi.deleted_at IS NULL
    LEFT JOIN products
        ON products.id = pi.product_id
    WHERE pr.id = '$id'
    ORDER BY pi.id ASC
");

if(!$q){
    die(mysqli_error($conn));
}

// Produk untuk dropdown (semua kategori kecuali Additional)
$qProd = mysqli_query($conn, "SELECT id, name, unit FROM products WHERE deleted_at IS NULL AND category <> 'Additional' ORDER BY name ASC");
$products = [];
while($p = mysqli_fetch_assoc($qProd)){
    $products[] = $p;
}

// Bangun opsi <select> untuk satu item; pilih berdasarkan product_id,
// fallback berdasarkan nama agar data lama yang belum punya id tetap terbaca
function buildProductOptions($products, $currentName, $currentId){
    $html = '<option value=""></option>';
    foreach($products as $p){
        $sel = false;
        if($currentId && (int)$currentId === (int)$p['id']){
            $sel = true;
        }
        if(!$sel && $currentName !== '' && strtolower(trim($p['name'])) === strtolower(trim($currentName))){
            $sel = true;
        }
        $html .= '<option value="' . (int)$p['id'] . '"'
               . ' data-name="' . htmlspecialchars($p['name']) . '"'
               . ' data-unit="' . htmlspecialchars($p['unit']) . '"'
               . ($sel ? ' selected' : '') . '>' . htmlspecialchars($p['name']) . '</option>';
    }
    return $html;
}
?>

<form id="editPurchaseForm" method="POST">

    <input type="hidden" name="id" value="<?= $id ?>">

    <div id="itemsContainerEdit">

        <?php while($d = mysqli_fetch_assoc($q)){ ?>

        <div class="item-row row mb-3">

            <input type="hidden"
                   name="item_id[]"
                   value="<?= $d['item_id'] ?>">

            <div class="col-md-4">
                <select
                    name="product_id[]"
                    class="form-control product-select"
                    placeholder="Nama Produk"
                    required>
                    <?= buildProductOptions($products, $d['name'], $d['product_id']) ?>
                </select>
            </div>

            <div class="col-md-2">
                <input type="number"
                       name="qty_buy[]"
                       class="form-control"
                       value="<?= $d['qty_buy'] ?>"
                       required>
            </div>

            <div class="col-md-2">
                <input type="text"
                       name="unit_buy[]"
                       class="form-control"
                       value="<?= htmlspecialchars($d['unit_buy']) ?>"
                       required>
            </div>

            <div class="col-md-3">
                <input type="number"
                       name="price_buy[]"
                       class="form-control"
                       placeholder="Harga"
                       min="0"
                       value="<?= $d['price_buy'] !== null && $d['price_buy'] !== '' ? (int)$d['price_buy'] : '' ?>">
            </div>

            <div class="col-md-1">
                <button type="button"
                        class="btn btn-danger w-100"
                        onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

        </div>

        <?php } ?>

    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 mb-4">

        <button type="button"
                class="btn-save"
                onclick="addItemEdit()">
            <i class="fas fa-plus"></i>
            Tambah Item
        </button>

        <button type="submit"
                class="btn-save">
            <i class="fas fa-save"></i>
            Update
        </button>

    </div>

</form>

<script>
    function removeItem(button)
    {
        button.closest('.item-row').remove();
    }
</script>