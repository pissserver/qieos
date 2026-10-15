<?php
error_reporting(0);
include __DIR__ . '/../../sessions/session.php';

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
$editPids   = $editCombo ? $editCombo['items'] : [];
$editPidSet = array_flip($editPids);

// Daftar produk yang boleh dipilih sebagai bahan racikan:
// - Produk yang SUDAH TERDAFTAR di kantin, yaitu punya minimal satu baris
//   di sales_stock (walaupun stok kantinnya sedang 0). Definisi "terdaftar"
//   ini sama seperti katalog kasir di pages/sales/sales-table.php.
// - Produk kategori "additional" selalu ikut ditampilkan.
// Produk yang belum pernah masuk kantin (tanpa baris sales_stock) TIDAK
// ditampilkan, kecuali kalau produk itu sudah menjadi bahan racikan yang
// sedang diedit supaya bahan lama tidak hilang saat edit.
$products = [];
$q = mysqli_query($conn, "
    SELECT
        p.id, p.name, p.code,
        COALESCE(p.sell_price, 0) AS sell_price,
        COALESCE(p.unit, '') AS unit,
        COALESCE(p.category, '') AS category,
        CASE WHEN ss.product_id IS NULL THEN 0 ELSE 1 END AS kantin_registered,
        COALESCE(ss.v, 0) AS kantin
    FROM products p
    LEFT JOIN (
        SELECT product_id, SUM(qty) AS v
        FROM sales_stock
        GROUP BY product_id
    ) ss ON ss.product_id = p.id
    WHERE p.deleted_at IS NULL
      AND (
            ss.product_id IS NOT NULL
         OR LOWER(COALESCE(p.category, '')) = 'additional'
      )
    ORDER BY p.name ASC
");
if($q){
    while($row = mysqli_fetch_assoc($q)){
        $row['kantin'] = max(0, (int)$row['kantin']);
        $products[] = $row;
    }
}

// Sisipkan bahan racikan yang sedang diedit tapi belum terdaftar di kantin,
// supaya nilainya tetap tampil di form edit.
if($editPidSet){
    $have = [];
    foreach($products as $p){ $have[(int)$p['id']] = true; }
    $missing = [];
    foreach($editPids as $pid){
        if(!isset($have[$pid])) $missing[] = (int)$pid;
    }
    if($missing){
        $idList = implode(',', $missing);
        $mq = mysqli_query($conn, "
            SELECT
                p.id, p.name, p.code,
                COALESCE(p.sell_price, 0) AS sell_price,
                COALESCE(p.unit, '') AS unit,
                COALESCE(p.category, '') AS category,
                0 AS kantin_registered,
                COALESCE(ss.v, 0) AS kantin
            FROM products p
            LEFT JOIN (
                SELECT product_id, SUM(qty) AS v
                FROM sales_stock
                GROUP BY product_id
            ) ss ON ss.product_id = p.id
            WHERE p.deleted_at IS NULL AND p.id IN ($idList)
            ORDER BY p.name ASC
        ");
        if($mq){
            while($row = mysqli_fetch_assoc($mq)){
                $row['kantin'] = max(0, (int)$row['kantin']);
                $products[] = $row;
            }
        }
    }
}
$productJson = json_encode($products);
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

<div class="combine-stock-note">
    <i class="fas fa-circle-info"></i>
    <div>
        Hanya menampilkan produk yang <b>sudah terdaftar di kantin</b> (walaupun stoknya sedang 0)
        dan produk kategori <b>Additional</b>. Produk yang belum masuk kantin tidak ditampilkan.
    </div>
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

<div class="text-end mt-3 mb-2 combine-footer">
    <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-save">
        <i class="fas fa-save me-1"></i> Save
    </button>
</div>

</form>