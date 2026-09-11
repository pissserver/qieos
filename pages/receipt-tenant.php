<?php
include '../sessions/session.php';

$payment_id = $_GET['payment_id'];
$type = $_GET['type'];

if ($type === 'utility') {
    $table = 'utility_payments';
    $title = "PEMBAYARAN AIR & LISTRIK";
} else {
    $table = 'tenant_payments';
    $title = "PEMBAYARAN TENANT";
}

$details = mysqli_query($conn, "
    SELECT d.*, t.tenant_name, u.fullname
    FROM $table d
    LEFT JOIN users u ON d.staff_id = u.id 
    LEFT JOIN tenants t ON d.tenant_id = t.id
    WHERE d.id = '$payment_id'
")->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - Qieos</title>
    <link
        rel="icon"
        sizes="120x120"
        href="../assets/img/brand/qieos2.png" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/receipt-tenant.css">

</head>

<body>
    <div class="receipt">

        <div class="center">
            <div class="logo">QIEOS</div>
            <div class="title"><?= $title ?></div>
        </div>

        <hr>

        <div class="row">
            <span class="label">Tanggal</span>
            <span><?= date('d/m/Y', strtotime($details['payment_date'])) ?></span>
        </div>

        <div class="row">
            <span class="label">Kasir</span>
            <span><?= $details['fullname'] ?></span>
        </div>

        <div class="row">
            <span class="label">Tenant</span>
            <span><?= $details['tenant_name'] ?></span>
        </div>

        <hr>

        <div class="row">
            <span>Tagihan Tenant</span>
            <span>Rp <?= number_format($details['cost_payment']) ?></span>
        </div>

        <hr>

        <div class="row total">
            <span>TOTAL</span>
            <span>Rp <?= number_format($details['cost_payment']) ?></span>
        </div>

        <hr>

        <div class="center">
            <div class="status">LUNAS</div>
        </div>

        <div class="footer">
            Terima kasih telah melakukan pembayaran.<br>
            Simpan struk ini sebagai bukti pembayaran.
        </div>

    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 300); // delay biar render dulu
        };
    </script>
</body>

</html>