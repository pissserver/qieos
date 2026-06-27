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

<style>
    #purchaseTable{
        border-collapse:separate;
        border-spacing:0 14px;
    }

    #purchaseTable thead th{
        font-size:12px;
        color:#64748b;
        font-weight:700;
        border:none !important;
        text-transform:uppercase;
    }

    /* CARD */
    .purchase-row{
        background:#fff;
        border-radius:16px;
        box-shadow:0 6px 18px rgba(15,23,42,.05);
        transition:.25s;
    }

    .purchase-row:hover{
        transform:translateY(-3px);
        box-shadow:0 12px 24px rgba(15,23,42,.10);
    }

    .purchase-row td:first-child{
        border-radius:14px 0 0 14px;
    }

    .purchase-row td:last-child{
        border-radius:0 14px 14px 0;
    }

    #purchaseTable tbody td{
        padding:20px 18px;
        border:none !important;
        vertical-align:middle;
    }

    /* ICON BOX */
    .purchase-box{
        display:flex;
        align-items:center;
        gap:14px;
    }

    .purchase-icon{
        width:48px;
        height:48px;
        border-radius:14px;
        background:linear-gradient(135deg,#334155,#0f172a);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        font-size:18px;
    }

    /* BADGES */
    .date-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#eef2ff;
        color:#4338ca;
        padding:8px 14px;
        border-radius:10px;
        font-weight:600;
    }

    .note-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#f8fafc;
        border:1px solid #e2e8f0;
        color:#334155;
        padding:8px 14px;
        border-radius:10px;
        font-weight:600;
    }

    .created-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#ecfeff;
        color:#0f766e;
        padding:8px 14px;
        border-radius:10px;
        font-weight:600;
    }

    /* ACTION */
    .action-btn{
        width:38px;
        height:38px;
        border:none;
        border-radius:10px;
        margin:0 4px;
        color:#fff;
        transition:.25s;
    }

    .btn-edit{
        background:#f59e0b;
    }

    .btn-delete{
        background:#ef4444;
    }

    .btn-print{
        background:linear-gradient(135deg,#334155,#0f172a);
    }

    .action-btn:hover{
        transform:translateY(-2px);
        opacity:.92;
    }
</style>

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
