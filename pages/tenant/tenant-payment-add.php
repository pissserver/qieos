<?php
    include '../../sessions/session.php';

    $type = $_GET['type'];
    $tenant_id = (int)$_GET['tenant_id'];

    $table = ($type === 'utility')
        ? 'utility_payments'
        : 'tenant_payments';

    if ($type === 'utility') {

        $condition = "DATE_FORMAT(payment_date,'%Y-%m') = '".date('Y-m')."'";

    } else {

        $condition = "DATE(payment_date) = '".date('Y-m-d')."'";

    }

    $query = mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM $table
        WHERE tenant_id = $tenant_id
        AND $condition
    ");

    $data = mysqli_fetch_assoc($query);
    $total_payment = $data['total'];
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/tenant-payment-add.css">

<form id="addPaymentForm">

    <input type="hidden" name="tenant_id" value="<?= $tenant_id ?>">
    <input type="hidden" name="type" value="<?= $type ?>">

    <div class="section-title">
        Informasi Pembayaran
    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <input
                    type="number"
                    name="cost_payment"
                    class="form-control"
                    value="<?= $type === 'utility' ? '25000' : '50000' ?>"
                    required
                    readonly>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                </div>
                <input
                    type="date"
                    name="payment_date"
                    class="form-control"
                    value="<?= date('Y-m-d') ?>"
                    required
                    readonly>
            </div>
        </div>
    </div>

    <div class="text-end mt-4 mb-3">
        <?php if ($total_payment == 0):?>
            <button type="submit" class="btn-save">
                <i class="fas fa-money-bill-wave me-1"></i>
                Bayar
            </button>
        <?php else:?>
            <div class="payment-locked">
                <div class="payment-locked-icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="payment-locked-content">
                    <h6>Pembayaran Selesai</h6>
                    <p>
                        Tenant ini telah melakukan pembayaran.
                    </p>
                </div>

                <div class="payment-status">
                    <i class="fas fa-shield-check"></i>
                    Terbayar
                </div>
            </div>
        <?php endif;?>
    </div>

</form>