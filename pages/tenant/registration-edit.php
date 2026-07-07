<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$sql = "
SELECT 
    *
FROM tenants
WHERE id = '$id'
";

$q = mysqli_query($conn, $sql);

if(!$q){
    die(mysqli_error($conn));
}

$d = mysqli_fetch_assoc($q);
?>

<form id="editRegistrationForm" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $id ?>">

<div class="section-title">Informasi Tenant</div>

<div class="row">
    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-store"></i></div>
            <input type="text" name="tenant_name" class="form-control"
                value="<?= $d['tenant_name'] ?>">
        </div>
    </div>

    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-user"></i></div>
            <input type="text" name="tenant_owner" class="form-control"
                value="<?= $d['tenant_owner'] ?>">
        </div>
    </div>
</div>

<div class="text-end mt-4 mb-3">
    <button type="submit" class="btn-save">
        <i class="fas fa-save me-1"></i> Update
    </button>
</div>

</form>