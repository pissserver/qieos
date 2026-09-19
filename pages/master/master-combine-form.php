<?php
error_reporting(0);
include __DIR__ . '/../../sessions/session.php';

// Daftar produk aktif untuk dipilih sebagai bahan racikan
$products = [];
$q = mysqli_query($conn, "
    SELECT id, name, code, COALESCE(sell_price, 0) AS sell_price, COALESCE(unit, '') AS unit, COALESCE(category, '') AS category
    FROM products
    WHERE deleted_at IS NULL
    ORDER BY name ASC
");
if($q){
    while($row = mysqli_fetch_assoc($q)){
        $products[] = $row;
    }
}
$productJson = json_encode($products);

// Mode edit: prefill nama + bahan racikan yang dipilih
$editCombo = null;
$editId    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($editId > 0){
    $cq = mysqli_query($conn, "SELECT * FROM product_combos WHERE id = $editId AND deleted_at IS NULL");
    if($cq && $cq->num_rows > 0){
        $combo = $cq->fetch_assoc();
        $pids = [];
        $iq = mysqli_query($conn, "SELECT product_id FROM product_combo_items WHERE combo_id = $editId ORDER BY id ASC");
        if($iq){
            while($row = $iq->fetch_assoc()){ $pids[] = (int)$row['product_id']; }
        }
        $editCombo = ['id' => (int)$combo['id'], 'name' => $combo['name'], 'items' => $pids];
    }
}
$editJson = $editCombo ? json_encode($editCombo) : 'null';
?>

<div id="combineProductData"
    data-products="<?= htmlspecialchars($productJson, ENT_QUOTES) ?>"
    data-combo="<?= htmlspecialchars($editJson, ENT_QUOTES) ?>"></div>

<form id="combineForm">

<?php if($editCombo): ?>
    <input type="hidden" name="id" id="combineIdInput" value="<?= (int)$editCombo['id'] ?>">
<?php endif; ?>

<div class="section-title">
    Nama Racikan
</div>
<div class="input-group-modern">
    <div class="input-icon">
        <i class="fas fa-flask"></i>
    </div>
    <input
        type="text"
        name="name"
        id="combineNameInput"
        class="form-control"
        placeholder="Contoh: Kopi Susu Gula"
        required>
</div>

<div class="section-title">
    Pilih Produk
    <span class="combine-count" id="combineCount">0 bahan</span>
</div>

<div id="combineItems"></div>

<button type="button" class="btn btn-combine-add" id="btnCombineAdd">
    <i class="fas fa-plus me-1"></i> Tambah Produk
</button>

<div class="combine-sticky-total">
    <div class="combine-sticky-icon"><i class="fas fa-coins"></i></div>
    <div class="combine-sticky-meta">
        <div class="combine-sticky-label">Total Harga (pengeluaran bahan)</div>
        <div class="combine-sticky-note">Dihitung otomatis dari harga masing-masing produk</div>
    </div>
    <div class="combine-sticky-value" id="combineTotalVal">Rp 0</div>
</div>

<div class="text-end mt-3 mb-2">
    <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-save">
        <i class="fas fa-save me-1"></i> Simpan Racikan
    </button>
</div>

</form>