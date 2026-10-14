<?php
error_reporting(0);
include __DIR__ . '/../../sessions/session.php';

// Mode edit: prefill unit base + baris konversi
$editConv = null;
$editId   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($editId > 0){
    $cq = mysqli_query($conn, "SELECT * FROM unit_conversions WHERE id = $editId AND deleted_at IS NULL");
    if($cq && $cq->num_rows > 0){
        $c = $cq->fetch_assoc();
        $items = [];
        $iq = mysqli_query($conn, "SELECT unit_name, qty FROM unit_conversion_items WHERE conversion_id = $editId ORDER BY id ASC");
        if($iq){
            while($row = mysqli_fetch_assoc($iq)){
                $items[] = ['unit' => $row['unit_name'], 'qty' => (float)$row['qty']];
            }
        }
        $editConv = ['id' => (int)$c['id'], 'base_unit' => $c['base_unit'], 'items' => $items];
    }
}
$editBase = $editConv ? $editConv['base_unit'] : '';

// Daftar satuan aktif untuk dropdown unit base & baris konversi
$units = [];
$qu = mysqli_query($conn, "SELECT name FROM units WHERE deleted_at IS NULL ORDER BY name ASC");
if($qu){
    while($u = mysqli_fetch_assoc($qu)){ $units[] = $u['name']; }
}

// Unit base yang SUDAH dipakai konversi lain tidak boleh dipilih lagi.
// Saat mengedit, grup yang sedang dibuka dikecualikan agar patokannya tetap bisa dipilih.
$usedBase = [];
$usedBaseNames = [];
$qb = mysqli_query($conn, "SELECT base_unit FROM unit_conversions WHERE deleted_at IS NULL"
    . ($editId > 0 ? " AND id <> $editId" : ""));
if($qb){
    while($row = mysqli_fetch_assoc($qb)){
        $usedBase[strtolower($row['base_unit'])] = true;
        $usedBaseNames[] = $row['base_unit'];
    }
}

$baseUnits = [];
foreach($units as $u){
    if(!isset($usedBase[strtolower($u)])) $baseUnits[] = $u;
}

$unitJson = json_encode($units);
$baseJson = json_encode($baseUnits);
$editJson = $editConv ? json_encode($editConv) : 'null';
?>

<div id="convertUnitData"
    data-units="<?= htmlspecialchars($unitJson, ENT_QUOTES) ?>"
    data-bases="<?= htmlspecialchars($baseJson, ENT_QUOTES) ?>"
    data-convert="<?= htmlspecialchars($editJson, ENT_QUOTES) ?>"></div>

<form id="convertForm">

<?php if($editConv): ?>
    <input type="hidden" name="id" id="convertIdInput" value="<?= (int)$editConv['id'] ?>">
<?php endif; ?>

<div class="section-title">
    Unit Base
</div>
<div class="convert-note">
    <i class="fas fa-circle-info"></i>
    <div>Pilih unit base (satuan acuan) konversi. Contoh: unit base <b>pcs</b>, lalu tambahkan <b>1 karton = 100 pcs</b> dan <b>1 rtg = 10 pcs</b>.</div>
</div>
<div class="convert-base-wrap">
    <select name="base_unit" id="convertBaseSelect" class="form-input" required>
        <option value="">Pilih unit base...</option>
        <?php foreach($baseUnits as $u): ?>
        <option value="<?= htmlspecialchars($u) ?>"<?= $u === $editBase ? ' selected' : '' ?>><?= htmlspecialchars(ucwords($u)) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php if(!empty($usedBaseNames)): ?>
<div class="convert-alert convert-alert-info">
    <i class="fas fa-circle-info"></i>
    <div>
        <b>Unit base yang sudah dipakai tidak bisa dipilih lagi.</b><br>
        Sudah dipakai:
        <?php foreach($usedBaseNames as $ix => $bn): ?><?= $ix > 0 ? ', ' : '' ?><span class="text-capitalize"><?= htmlspecialchars($bn) ?></span><?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<?php if(empty($baseUnits) && !$editConv): ?>
<div class="convert-note convert-note-warn">
    <i class="fas fa-triangle-exclamation"></i>
    <div>Semua unit sudah dipakai sebagai unit base. Hapus salah satu konversi atau tambah unit baru di Master Unit terlebih dahulu.</div>
</div>
<?php endif; ?>

<div class="section-title">
    Daftar Konversi
    <span class="combine-count" id="convertCount">0 satuan</span>
</div>

<div id="convertItems"></div>

<button type="button" class="btn btn-combine-add" id="btnConvertAdd">
    <i class="fas fa-plus me-1"></i> Tambah Satuan
</button>

<div class="combine-sticky-total">
    <div class="combine-sticky-icon"><i class="fas fa-ruler-combined"></i></div>
    <div class="combine-sticky-meta">
        <div class="combine-sticky-label">Unit Base</div>
        <div class="combine-sticky-note">Nilai acuan untuk semua konversi di bawah</div>
    </div>
    <div class="combine-sticky-value" id="convertBaseLabel">&mdash;</div>
</div>

<div class="text-end mt-3 mb-2 convert-footer">
    <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-save">
        <i class="fas fa-save me-1"></i> Save
    </button>
</div>

</form>
