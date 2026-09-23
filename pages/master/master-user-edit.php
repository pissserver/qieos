<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$q = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");

if(!$q){
    die(mysqli_error($conn));
}

$d = mysqli_fetch_assoc($q);

// Role user yang bisa diedit hanya 'administrator' atau 'staff kasir'
$isAdminRole = ($d['role'] === 'administrator');
$isDeveloper = ($d['role'] === 'developer');
?>

<form id="editUserForm">

<input type="hidden" name="id" value="<?= $id ?>">

<?php if(!$isDeveloper): ?>
<div class="section-title">Role</div>

<div class="mu-role-picker">
    <input type="radio" id="muRoleAdmin" name="role" value="administrator" <?= $isAdminRole ? 'checked' : '' ?>>
    <label for="muRoleAdmin" class="mu-role-opt">
        <span class="mu-role-ico"><i class="fas fa-user-shield"></i></span>
        <span>
            <span class="mu-role-name">Administrator</span>
            <small>Akses penuh manajemen &amp; laporan</small>
        </span>
    </label>

    <input type="radio" id="muRoleCashier" name="role" value="staff kasir" <?= !$isAdminRole ? 'checked' : '' ?>>
    <label for="muRoleCashier" class="mu-role-opt">
        <span class="mu-role-ico"><i class="fas fa-user-tie"></i></span>
        <span>
            <span class="mu-role-name">Staff Kasir</span>
            <small>Akses penjualan &amp; rekap kantin</small>
        </span>
    </label>
</div>
<?php endif; ?>

<div class="section-title">Informasi User</div>

<div class="row">
    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-user"></i></div>
            <input type="text" name="fullname" class="form-control"
                value="<?= htmlspecialchars($d['fullname']) ?>">
        </div>
    </div>

    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-at"></i></div>
            <input type="text" name="username" class="form-control"
                value="<?= htmlspecialchars($d['username']) ?>">
        </div>
    </div>

    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-lock"></i></div>
            <input type="password" name="password" class="form-control" placeholder="Password (kosongkan jika tidak diubah)">
        </div>
    </div>

    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-lock"></i></div>
            <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi Password">
        </div>
    </div>
</div>

<div class="text-end mt-4 mb-3">
    <button type="submit" class="btn-save">
        <i class="fas fa-save me-1"></i> Update
    </button>
</div>

</form>