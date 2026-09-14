<?php
include '../../sessions/session.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$q = mysqli_query($conn, "SELECT * FROM customers WHERE id = $id AND deleted_at IS NULL");
$d = mysqli_fetch_assoc($q);
if(!$d){
    echo '<div class="text-center py-4 text-danger">Data tidak ditemukan.</div>';
    exit;
}
?>

<form id="editCustomerForm">
    <input type="hidden" name="id" value="<?= $d['id'] ?>">
    <div class="section-title">Informasi Customer</div>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-user"></i></div>
                <input type="text" name="name" class="form-control" placeholder="Nama Customer" value="<?= htmlspecialchars($d['name']) ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-phone"></i></div>
                <input type="text" name="phone" class="form-control" placeholder="No. Telepon" value="<?= htmlspecialchars($d['phone']) ?>">
            </div>
        </div>
    </div>
    <div class="text-end mt-4 mb-3">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
    </div>
</form>