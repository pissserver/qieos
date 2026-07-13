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

<style>
    .payment-locked{
        display:flex;
        align-items:center;
        gap:20px;

        padding:22px 24px;

        border-radius:20px;

        background:linear-gradient(135deg,#f5fffb,#ecfff8);

        border:1px solid rgba(16,185,129,.25);

        box-shadow:
            0 15px 35px rgba(16,185,129,.08);

        transition:.3s;
    }

    .payment-locked:hover{
        transform:translateY(-2px);
        box-shadow:0 20px 40px rgba(16,185,129,.12);
    }

    .payment-locked-icon{
        width:60px;
        height:60px;
        min-width:60px;

        display:flex;
        align-items:center;
        justify-content:center;

        border-radius:18px;

        background:linear-gradient(135deg,#22c55e,#16a34a);

        color:#fff;
        font-size:26px;

        box-shadow:0 12px 25px rgba(34,197,94,.25);
    }

    .payment-locked-content{
        flex:1;
    }

    .payment-locked-content h6{
        margin:0 0 8px;
        color:#065f46;
        font-size:20px;
        font-weight:700;
    }

    .payment-locked-content p{
        margin:0;
        color:#64748b;
        font-size:14px;
        line-height:1.7;
    }

    .payment-status{
        white-space:nowrap;

        padding:10px 18px;

        border-radius:999px;

        background:#dcfce7;

        color:#15803d;

        font-weight:700;
        font-size:13px;

        display:flex;
        align-items:center;
        gap:8px;
    }

    @media (max-width:991px){

        .payment-locked{
            padding:20px;
            gap:16px;
        }

        .payment-locked-content h6{
            font-size:18px;
        }

    }

    @media (max-width:576px){

        .payment-locked{
            flex-direction:column;
            text-align:center;
            align-items:center;
            padding:22px 18px;
        }

        .payment-locked-icon{
            width:70px;
            height:70px;
            min-width:70px;
            font-size:30px;
        }

        .payment-locked-content{
            width:100%;
        }

        .payment-locked-content h6{
            font-size:18px;
            margin-bottom:10px;
        }

        .payment-locked-content p{
            font-size:13px;
            line-height:1.6;
        }

        .payment-status{
            width:100%;
            justify-content:center;
            margin-top:8px;
        }

    }
</style>

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