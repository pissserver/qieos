<?php
include '../../sessions/session.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE id = $id AND deleted_at IS NULL");
$d = mysqli_fetch_assoc($q);

if(!$d){
    echo '<div class="text-center py-4 text-danger">Data tidak ditemukan.</div>';
    exit;
}
?>

<form id="editSupplierForm">
    <input type="hidden" name="id" value="<?= $d['id'] ?>">
    <div class="section-title">Informasi Supplier</div>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-truck"></i></div>
                <input type="text" name="name" class="form-control" placeholder="Nama Supplier" value="<?= htmlspecialchars($d['name']) ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-phone"></i></div>
                <input type="text" name="phone" class="form-control" placeholder="No. Telepon" value="<?= htmlspecialchars($d['phone']) ?>">
            </div>
        </div>
        <div class="col-md-12">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-map-marker-alt"></i></div>
                <textarea name="address" class="form-control" placeholder="Alamat Lengkap"><?= htmlspecialchars($d['address']) ?></textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-sticky-note"></i></div>
                <textarea name="note" class="form-control" placeholder="Catatan"><?= htmlspecialchars($d['note']) ?></textarea>
            </div>
        </div>
    </div>
    <div class="mp-add-footer">
        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save</button>
    </div>
</form>