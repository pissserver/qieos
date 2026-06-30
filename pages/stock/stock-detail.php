<?php
include '../../sessions/session.php';

$id = $_GET['id'];

$q = mysqli_query($conn, "
    SELECT 
        pi.qty,
        pi.remaining_qty,
        pi.date,
        pi.unit,
        pi.buy_price,
        p.form
    FROM purchase_items pi
    LEFT JOIN purchases p ON p.id = pi.purchase_id
    WHERE pi.product_id = $id 
    AND pi.deleted_at IS NULL
    AND pi.remaining_qty > 0
    ORDER BY pi.id ASC
");

$bulan = [
    1 => 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.stock-wrapper {
    background: #f4f6f9;
    padding: 20px;
    border-radius: 12px;
}

.stock-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.stock-header-detail {
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color: #fff;
    padding: 16px 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.table-stock thead {
    background: #f1f3f5;
}

.table-stock th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #555;
}

.table-stock td {
    vertical-align: middle;
    font-size: 14px;
}

.badge-qty {
    background: #e7f1ff;
    color: #1d4ed8;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.badge-remain {
    background: #e9fbe7;
    color: #15803d;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.badge-price {
    background: #fff3cd;
    color: #856404;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.date-block {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.date-main {
    font-weight: 600;
    color: #111;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-badge {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-badge i {
    color: #0ea5e9;
}
</style>

<div class="stock-wrapper">

    <div class="card stock-card">

        <div class="stock-header-detail mb-3">
            <i class="fa-solid fa-boxes-stacked"></i>
            Detail Stok Masuk Barang
        </div>

        <div class="card-body p-0 mb-3">

            <div class="table-responsive">
                <table id="tableStock"  class="table table-hover table-stock mb-0">

                    <thead>
                        <tr>
                            <th><i class="fa-regular fa-calendar"></i> Tanggal</th>
                            <th><i class="fa-solid fa-scale-balanced"></i> Qty Masuk</th>
                            <th><i class="fa-solid fa-box"></i> Sisa Stok</th>
                            <th><i class="fa-solid fa-tags"></i> Harga Beli</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while($d = mysqli_fetch_assoc($q)): ?>

                        <?php
                            $tgl = strtotime($d['date']);
                            $tanggal = date('d', $tgl).' '.$bulan[(int)date('m',$tgl)].' '.date('Y',$tgl);

                            $remaining = $d['remaining_qty'] ? $d['remaining_qty'] : 0;
                            $form = $d['form'] ? $d['form'] : '-';
                        ?>

                        <tr>

                            <!-- TANGGAL + FORM -->
                            <td>
                                <div class="date-block">

                                    <div class="date-main">
                                        <i class="fa-regular fa-calendar"></i>
                                        <?= $tanggal ?>
                                    </div>

                                    <div class="form-badge">
                                        <i class="fa-solid fa-file-lines"></i>
                                        Form: <?= htmlspecialchars($form) ?>
                                    </div>

                                </div>
                            </td>

                            <!-- QTY -->
                            <td>
                                <span class="badge-qty">
                                    <i class="fa-solid fa-arrow-down"></i>
                                    <?= $d['qty'] ?> <?= $d['unit'] ?>
                                </span>
                            </td>

                            <!-- REMAINING -->
                            <td>
                                <span class="badge-remain">
                                    <i class="fa-solid fa-rotate"></i>
                                    <?= $remaining ?> <?= $d['unit'] ?>
                                </span>
                            </td>

                            <!-- PRICE -->
                            <td>
                                <span class="badge-price">
                                    Rp <?= number_format($d['buy_price'], 0, ',', '.') ?>
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