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

    <div class="col-md-6 etp-field">
        <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">
            <i class="fas fa-store" style="color:#6366f1;margin-right:4px"></i> Nama Tenant
        </label>
        <select name="tenant_id" class="form-control" style="height:46px;border-radius:10px;font-size:14px;padding:0 14px;cursor:pointer;">
            <option value="<?= $d['tenant_id'] ?>"><?= $d['tenant_name'] ?></option>
            <?php
                $qTenant = mysqli_query($conn,"
                    SELECT *
                    FROM tenants
                    WHERE status = 'active' AND deleted_at IS NULL
                    ORDER BY tenant_name ASC
                ");

                while($tenant = mysqli_fetch_assoc($qTenant)){
                    if($tenant['id'] != $d['tenant_id']){
                        echo '<option value="'.$tenant['id'].'">'.$tenant['tenant_name'].'</option>';
                    }
                }
            ?>
        </select>
    </div>

    <div class="col-md-6 etp-field">
        <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">
            <i class="fas fa-calendar" style="color:#6366f1;margin-right:4px"></i> Tanggal Pembayaran
        </label>
        <input type="date" name="payment_date" class="form-control"
            value="<?= $d['payment_date'] ?>"
            style="height:46px;border-radius:10px;font-size:14px;padding:0 14px;cursor:pointer;">
    </div>

</div>

<div class="text-end mt-4 mb-3">
    <button type="submit" class="btn-save">
        <i class="fas fa-save me-1"></i> Save
    </button>
</div>

</form>
