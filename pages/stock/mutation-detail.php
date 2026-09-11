<?php
include '../../sessions/session.php';

$id = $_GET['id'];

$q = mysqli_query($conn, "
    SELECT 
        p.id,
        p.name,
        od.qty,
        o.code,
        o.tanggal
    FROM products p
    LEFT JOIN order_details od
        ON od.product_id = p.id
    LEFT JOIN orders o
        ON o.id = od.order_id
    WHERE p.id = '$id'
    AND o.status_payment = 'paid'
    ORDER BY o.tanggal DESC
");

$bulan = [
    1 => 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/mutation-detail.css">

<div class="stock-wrapper">

    <div class="card stock-card">

        <div class="stock-header-detail">
            <i class="fa-solid fa-boxes-stacked"></i>
            Detail Penjualan Stok Kantin
        </div>

        <div class="card-body p-0 mb-3">

            <div class="table-responsive">
                <table id="tableMutation" class="table table-hover table-stock mb-0">

                    <thead>
                        <tr>
                            <th><i class="fa-regular fa-calendar"></i> Tanggal</th>
                            <th><i class="fa-solid fa-file-invoice"></i> Order</th>
                            <th><i class="fa-solid fa-cubes"></i> Qty</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while($d = mysqli_fetch_assoc($q)): ?>

                        <?php
                            $tgl = strtotime($d['tanggal']);
                            $tanggal = date('d', $tgl).' '.$bulan[(int)date('m',$tgl)].' '.date('Y',$tgl);
                        ?>

                        <tr>

                            <!-- TANGGAL + BARANG -->
                            <td>
                                <div class="date-block">

                                    <div class="date-main">
                                        <i class="fa-regular fa-calendar"></i>
                                        <?= $tanggal ?>
                                    </div>

                                    <div class="form-badge">
                                        <i class="fas fa-box-open"></i>
                                        <?= htmlspecialchars($d['name']) ?>
                                    </div>

                                </div>
                            </td>

                            <!-- CODE ORDER -->
                            <td>
                                <span class="badge-qty">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    <?= $d['code'] ?>
                                </span>
                            </td>

                            <!-- QTY -->
                            <td>
                                <span class="badge-remain">
                                    <i class="fa-solid fa-cubes"></i>
                                    <?= $d['qty'] ?>
                                </span>
                            </td>

                        </tr>

                    <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

        </div>

    </div>
</div>