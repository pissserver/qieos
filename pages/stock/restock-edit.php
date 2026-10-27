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
        pi.price_unit,
        pi.price_buy,
        pi.supplier_id,
        pi.invoice_no
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

// Satuan untuk dropdown (Master Unit)
$qUnit = mysqli_query($conn, "SELECT name FROM units WHERE deleted_at IS NULL ORDER BY name ASC");
$units = [];
while($u = mysqli_fetch_assoc($qUnit)){
    $units[] = $u['name'];
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

// Opsi satuan; nilai tersimpan tetap ditampilkan walau tidak ada di Master Unit
function buildUnitOptions($units, $current){
    $current = $current === null ? '' : (string)$current;
    $html = '<option value=""></option>';
    $found = false;
    foreach($units as $u){
        $sel = ($current !== '' && (string)$u === $current);
        if($sel) $found = true;
        $html .= '<option value="' . htmlspecialchars($u) . '"' . ($sel ? ' selected' : '') . '>' . htmlspecialchars($u) . '</option>';
    }
    if($current !== '' && !$found){
        $html .= '<option value="' . htmlspecialchars($current) . '" selected>' . htmlspecialchars($current) . '</option>';
    }
    return $html;
}
?>

<form id="editPurchaseForm" method="POST">

    <input type="hidden" name="id" value="<?= $id ?>">

    <div id="itemsContainerEdit">

        <?php while($d = mysqli_fetch_assoc($q)){ ?>

        <div class="item-row mb-3 border rounded-3 p-3">

            <input type="hidden"
                   name="item_id[]"
                   value="<?= $d['item_id'] ?>">

            <div class="row g-3 align-items-end">
            <div class="col-md-4 item-product">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-product">
                        <i class="fas fa-box"></i>
                    </div>
                    <select
                        name="product_id[]"
                        class="form-control product-select"
                        placeholder="Nama Produk"
                        required>
                        <?= buildProductOptions($products, $d['name'], $d['product_id']) ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-qty">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <input type="number"
                           name="qty_buy[]"
                           class="form-control"
                           value="<?= $d['qty_buy'] ?>"
                           required>
                </div>
            </div>

            <div class="col-md-2">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-unit">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <select
                           name="unit_buy[]"
                           class="form-control unit-select"
                           placeholder="Satuan"
                           required>
                        <?= buildUnitOptions($units, $d['unit_buy']) ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2 item-price">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-price">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <input type="number"
                           name="price_buy[]"
                           class="form-control"
                           placeholder="Total Harga Produk"
                           min="0"
                           required
                           value="<?= $d['price_buy'] !== null && $d['price_buy'] !== '' ? (int)$d['price_buy'] : '' ?>">
                </div>
            </div>

            <div class="col-md-2 item-unit-price">
                <div class="input-group-modern">
                    <div class="input-icon input-icon-price">
                        <i class="fas fa-coins"></i>
                    </div>
                    <input type="number"
                           name="unit_price[]"
                           class="form-control"
                           placeholder="Harga Satuan"
                           min="0"
                           required
                           value="<?= $d['price_unit'] !== null && $d['price_unit'] !== '' ? (int)$d['price_unit'] : '' ?>">
                </div>
            </div>
            </div>

            <div class="row g-3 align-items-end mt-1">
                <div class="col-md-5">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select
                           name="supplier_id[]"
                           class="form-control supplier-select"
                           data-selected="<?= (int)$d['supplier_id'] ?>"
                           required>
                        <option value=""></option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label">No Invoice <span class="text-danger">*</span></label>
                    <input type="text"
                           name="invoice_no[]"
                           class="form-control"
                           placeholder="Masukkan no invoice"
                           maxlength="100"
                           value="<?= htmlspecialchars((string)$d['invoice_no'], ENT_QUOTES, 'UTF-8') ?>"
                           required>
                </div>

            <div class="col-md-2 item-remove">
                <div class="input-group-modern">
                    <button type="button"
                            class="input-icon input-icon-danger"
                            onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
            </div>

        </div>

        <?php } ?>

    </div>

    <div class="mp-add-footer d-flex justify-content-end gap-2 mt-4 mb-4">

        <button type="button"
                class="btn-save btn-save-ghost"
                onclick="addItemEdit()">
            <i class="fas fa-plus"></i>
            Tambah Item
        </button>

        <button type="submit"
                class="btn-save">
            <i class="fas fa-save"></i>
            Save
        </button>

    </div>

</form>

<script>
    function removeItem(button)
    {
        button.closest('.item-row').remove();
    }
</script>
