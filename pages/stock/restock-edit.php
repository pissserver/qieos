<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$qPurchase = mysqli_query($conn, "
    SELECT
        pr.form,
        pr.receipt,
        EXISTS (
            SELECT 1
            FROM purchase_items pi
            WHERE pi.purchase_id = pr.id
              AND pi.deleted_at IS NULL
              AND pi.qty_stock IS NOT NULL
        ) AS is_restocked
    FROM purchases pr
    WHERE pr.id = '$id'
      AND pr.deleted_at IS NULL
    LIMIT 1
");
$purchase = $qPurchase ? mysqli_fetch_assoc($qPurchase) : null;
if(!$purchase){
    http_response_code(404);
    exit('Form pembelian tidak ditemukan.');
}
$isPurchaseLocked = !empty($purchase['receipt']) || (int)$purchase['is_restocked'] === 1;

$q = mysqli_query($conn,"
    SELECT
        pi.id AS item_id,
        pi.product_id,
        COALESCE(products.name, '') AS name,
        pi.qty_buy,
        pi.unit_buy,
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

$productUnits = [];
foreach($products as $product){
    $productId = (int)$product['id'];
    $productUnits[$productId] = [];
    if(trim((string)$product['unit']) !== ''){
        $productUnits[$productId][] = $product['unit'];
    }
}
$qConversionUnits = mysqli_query($conn, "
    SELECT uc.product_id, uci.unit_name
    FROM unit_conversions uc
    JOIN unit_conversion_items uci
        ON uci.conversion_id = uc.id
    WHERE uc.deleted_at IS NULL
    ORDER BY uc.product_id ASC, uci.id ASC
");
if(!$qConversionUnits){
    die(mysqli_error($conn));
}
while($conversionUnit = mysqli_fetch_assoc($qConversionUnits)){
    $productId = (int)$conversionUnit['product_id'];
    $unitName = trim((string)$conversionUnit['unit_name']);
    if(!isset($productUnits[$productId]) || $unitName === '') continue;
    $existingUnits = array_map('strtolower', $productUnits[$productId]);
    if(!in_array(strtolower($unitName), $existingUnits, true)){
        $productUnits[$productId][] = $unitName;
    }
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
function buildUnitOptions($productUnits, $productId, $current){
    $current = $current === null ? '' : (string)$current;
    $html = '<option value=""></option>';
    $units = isset($productUnits[(int)$productId]) ? $productUnits[(int)$productId] : [];
    foreach($units as $u){
        $sel = ($current !== '' && strtolower((string)$u) === strtolower($current));
        $html .= '<option value="' . htmlspecialchars($u) . '"' . ($sel ? ' selected' : '') . '>' . htmlspecialchars($u) . '</option>';
    }
    return $html;
}
?>

<form id="editPurchaseForm" method="POST"
      data-has-receipt="<?= !empty($purchase['receipt']) ? '1' : '0' ?>"
      data-is-locked="<?= $isPurchaseLocked ? '1' : '0' ?>">

    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="purchase-receipt-toolbar">
        <div class="purchase-form-badges">
            <span class="purchase-form-badge">
                <i class="fas fa-file-lines"></i>
                <span>
                    <small>FORM PEMBELIAN</small>
                    <strong><?= htmlspecialchars($purchase['form'], ENT_QUOTES, 'UTF-8') ?></strong>
                </span>
            </span>
            <?php if(!empty($purchase['receipt'])): ?>
            <span class="purchase-receipt-badge">
                <i class="fas fa-circle-check"></i>
                <span>
                    <small>FORM RECEIPT</small>
                    <strong><?= htmlspecialchars($purchase['receipt'], ENT_QUOTES, 'UTF-8') ?></strong>
                </span>
            </span>
            <?php endif; ?>
        </div>
        <div class="purchase-receipt-actions">
            <?php if(empty($purchase['receipt']) && (int)$purchase['is_restocked'] === 0): ?>
            <button type="button" class="btn btn-receipt-submit" id="createReceiptBtn">
                <i class="fas fa-file-circle-check"></i>
                <span>Buat Receipt</span>
            </button>
            <?php elseif(!empty($purchase['receipt'])): ?>
            <button type="button" class="btn btn-receipt-print" data-print-purchase="<?= $id ?>">
                <i class="fas fa-print"></i>
                <span>Print Receipt</span>
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php if($isPurchaseLocked): ?>
    <div class="alert alert-success py-2 mb-3">
        <i class="fas fa-lock me-1"></i>
        <?= !empty($purchase['receipt'])
            ? 'Receipt sudah dibuat. Detail pembelian dikunci dan tidak dapat diubah.'
            : 'Pembelian sudah direstock. Detail pembelian dikunci dan tidak dapat diubah.' ?>
    </div>
    <?php endif; ?>

    <div class="receipt-processing" id="receiptProcessing" hidden aria-live="polite">
        <div class="receipt-processing-card">
            <div class="receipt-processing-icon" id="receiptProcessingIcon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="receipt-processing-title" id="receiptProcessingTitle">Menyiapkan receipt</div>
            <div class="receipt-processing-message" id="receiptProcessingMessage">Menyimpan perubahan pembelian...</div>
            <div class="receipt-processing-steps" aria-label="Proses pembuatan receipt">
                <span class="is-active" data-receipt-step="save"><i class="fas fa-file-pen"></i> Menyimpan</span>
                <span data-receipt-step="create"><i class="fas fa-receipt"></i> Membuat receipt</span>
                <span data-receipt-step="done"><i class="fas fa-circle-check"></i> Selesai</span>
            </div>
            <div class="receipt-processing-track"><span id="receiptProcessingProgress"></span></div>
        </div>
    </div>

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
                        <?= buildUnitOptions($productUnits, $d['product_id'], $d['unit_buy']) ?>
                    </select>
                </div>
            </div>

            <div class="col-md-4 item-price">
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

            <?php if(!$isPurchaseLocked): ?>
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
            <?php endif; ?>
            </div>

        </div>

        <?php } ?>

    </div>

    <?php if(!$isPurchaseLocked): ?>
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
    <?php endif; ?>

</form>

<script>
    function removeItem(button)
    {
        button.closest('.item-row').remove();
    }
</script>
