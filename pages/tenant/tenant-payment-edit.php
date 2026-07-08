<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];
$type = $_GET['type'];

if($type === 'utility'){
    $table = 'utility_payments';
}else{
    $table = 'tenant_payments';
}

$sql = "
SELECT 
    p.*,
    t.tenant_name
FROM $table p
LEFT JOIN tenants t ON p.tenant_id = t.id
WHERE p.id = '$id'
";

$q = mysqli_query($conn, $sql);

if(!$q){
    die(mysqli_error($conn));
}

$d = mysqli_fetch_assoc($q);
?>

<form id="editPaymentForm" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="type" value="<?= $type ?>">

<div class="section-title">Informasi Pembayaran</div>

<div class="row">
    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-store"></i></div>
            <select name="tenant_id" class="form-control">
                <option value="<?= $d['tenant_id'] ?>"><?= $d['tenant_name'] ?></option>
                <?php
                    $qTenant = mysqli_query($conn,"
                        SELECT *
                        FROM tenants
                        WHERE id != '$d[tenant_id]'
                        ORDER BY tenant_name ASC
                    ");

                    while($tenant = mysqli_fetch_assoc($qTenant)){
                        echo '<option value="'.$tenant['id'].'">'.$tenant['tenant_name'].'</option>';
                    }
                ?>
            </select>   
        </div>
    </div>

    <div class="col-md-6">
        <div class="input-group-modern">
            <div class="input-icon"><i class="fas fa-user"></i></div>
            <input type="date" name="payment_date" class="form-control"
                value="<?= $d['payment_date'] ?>">
        </div>
    </div>
</div>

<div class="text-end mt-4 mb-3">
    <button type="submit" class="btn-save">
        <i class="fas fa-save me-1"></i> Update
    </button>
</div>

</form>