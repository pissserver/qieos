<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
    SELECT
        lp.*,
        COUNT(lpi.id) AS total_items,
        SUM(lpi.price) AS total_price
    FROM list_purchases lp
    LEFT JOIN list_purchase_items lpi
        ON lp.id = lpi.list_purchase_id
    WHERE lp.deleted_at IS NULL
    GROUP BY lp.id
    ORDER BY lp.date_list DESC
");

$totalPrice = 0;
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/list-table.css">

<table id="purchaseTable" class="table table-hover align-middle">
<thead>
<tr>
    <th>ID FORM</th>
    <th class="text-center">TANGGAL PEMBUATAN</th>
    <th class="text-center">TOTAL ITEM</th>
    <th class="text-center">TOTAL BELANJA</th>
    <th class="text-center">AKSI</th>
</tr>
</thead>

<tbody>
<?php
    while($d=mysqli_fetch_assoc($q)): 
?>
<tr class="purchase-row">

    <!-- ID -->
    <td>
        <div class="purchase-box">
            <div class="purchase-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div>
                <div class="fw-bold">BELANJA-000<?= $d['id'] ?></div>
                <small class="text-muted">
                    <i class="fas fa-receipt me-1"></i>Daftar Belanja
                </small>
            </div>
        </div>
    </td>

    <!-- DATE -->
    <td class="text-center">
        <span class="date-badge">
            <i class="fas fa-calendar-alt"></i>
            <?= date('d F Y', strtotime($d['date_list'])) ?>
        </span>
    </td>

    <!-- TOTAL PRICE -->
    <td class="text-center">
        <span class="note-badge">
            <i class="fas fa-box"></i>
            <?= $d['total_items'] ?>
        </span>
    </td>

    <!-- TOTAL PRICE -->
    <td class="text-center">
        <span class="created-badge">
            <i class="fas fa-money-bill"></i>
            Rp <?= number_format($d['total_price'], 0, ',', '.') ?>
        </span>
    </td>

    <!-- ACTION -->
    <td class="text-center">

        <button class="action-btn btn-edit editPurchaseBtn" data-id="<?= $d['id'] ?>">
            <i class="fas fa-edit"></i>
        </button>

        <button class="action-btn btn-delete deletePurchaseBtn"
            data-id="<?= $d['id'] ?>"
            data-date="<?= $d['date_list'] ?>">
            <i class="fas fa-trash"></i>
        </button>

        <button class="action-btn btn-print printPurchaseBtn"
            data-id="<?= $d['id'] ?>"
            data-date="<?= $d['date_list'] ?>">
            <i class="fas fa-print"></i>
        </button>
        
    </td>

</tr>
<?php endwhile; ?>
</tbody>
</table>
