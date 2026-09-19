<?php
include '../sessions/session.php';

$order_id = $_GET['id'];

$user = mysqli_query($conn, "
    SELECT * FROM users WHERE username = '" . $_SESSION['username'] . "'
")->fetch_assoc();

$order = mysqli_query($conn, "
    SELECT o.*, u.fullname
    FROM orders o
    LEFT JOIN users u ON o.staff_id = u.id
    WHERE o.id = $order_id
")->fetch_assoc();

$details = mysqli_query($conn, "
    SELECT od.*, p.name AS product_name 
    FROM order_details od
    LEFT JOIN products p ON od.product_id = p.id
    WHERE od.order_id = $order_id
    ORDER BY COALESCE(od.name, p.name) ASC
");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Struk Pembelian - Qieos</title>
    <link
        rel="icon"
        sizes="120x120"
        href="../assets/img/brand/qieos2.png" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/receipt.css?v=<?php echo filemtime(__DIR__ . '/../css/pages/receipt.css'); ?>">

</head>

<body>
    <div class="receipt">

        <div class="center">

            <div class="logo">QIEOS</div>

            <div class="receipt-title">
                STRUK PEMBELIAN
            </div>

        </div>

        <hr>

        <div class="info row">
            <span>Order ID.</span>
            <span><?= $order['code'] ?></span>
        </div>

        <div class="info row">
            <span>Tanggal</span>
            <span><?= date('d/m/Y', strtotime($order['tanggal'])) ?></span>
        </div>

        <div class="info row">
            <span>Kasir</span>
            <span><?= $order['fullname'] ?></span>
        </div>

        <hr>

        <?php while($d=mysqli_fetch_assoc($details)){ ?>

            <div class="item">

                <div class="item-name">
                    <?= htmlspecialchars(ucwords(strtolower($d['name'] ? $d['name'] : ($d['product_name'] ? $d['product_name'] : 'Barang')))) ?>
                </div>

                <div class="item-detail">

                    <span>
                        <?= $d['qty'] ?> × Rp <?= number_format($d['price']) ?>
                    </span>

                    <span>
                        Rp <?= number_format($d['subtotal']) ?>
                    </span>

                </div>

            </div>

        <?php } ?>

        <hr>

        <div class="row">
            <span>Subtotal</span>
            <span>Rp <?= number_format($order['total']) ?></span>
        </div>

        <hr>

        <div class="row total">
            <span>TOTAL</span>
            <span>Rp <?= number_format($order['total']) ?></span>
        </div>

        <hr>

        <div class="center">

            <div class="status">
                LUNAS
            </div>

        </div>

        <div class="footer">

            Terima kasih atas pembelian Anda.<br>

            Simpan struk ini sebagai bukti transaksi.

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