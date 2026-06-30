<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$sql = "
SELECT 
    *
FROM users
WHERE id = '$id'
";

$q = mysqli_query($conn, $sql);

if(!$q){
    die(mysqli_error($conn));
}

$d = mysqli_fetch_assoc($q);
?>

<form id="editStaffForm" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $id ?>">

<div class="section-title">Informasi Staff Kasir</div>

<div class="row">
    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-user"></i></div>
            <input type="text" name="fullname" class="form-control"
                value="<?= $d['fullname'] ?>">
        </div>
    </div>

    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-envelope"></i></div>
            <input type="email" name="email" class="form-control"
                value="<?= $d['email'] ?>">
        </div>
    </div>

    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-lock"></i></div>
            <input type="password" name="password" class="form-control" placeholder="Password">
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