<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
SELECT 
    purchases.id,
    purchases.form,
    purchases.date,
    purchases.note,
    purchases.created_at,
    purchase_items.qty,
    purchase_items.unit,

    GROUP_CONCAT(products.name SEPARATOR ', ') as products

FROM purchases

LEFT JOIN purchase_items 
    ON purchase_items.purchase_id = purchases.id

LEFT JOIN products 
    ON products.id = purchase_items.product_id

WHERE purchases.deleted_at IS NULL

GROUP BY purchases.id
ORDER BY purchases.id DESC
");
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

    .action-btn:hover{
        transform:translateY(-2px);
        opacity:.92;
    }
</style>

<table id="purchaseTable" class="table table-hover align-middle">
<thead>
<tr>
    <th>ID FORM</th>
    <th class="text-center">TANGGAL PEMBELIAN</th>
    <th class="text-center">CATATAN</th>
    <th class="text-center">PEMBUATAN FORM</th>
    <th class="text-center">AKSI</th>
</tr>
</thead>

<tbody>
<?php while($d=mysqli_fetch_assoc($q)): ?>
<tr class="purchase-row">

    <!-- ID -->
    <td>
        <div class="purchase-box">
            <div class="purchase-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div>
                <div class="fw-bold"><?= $d['form'] ?></div>
                <small class="text-muted">
                    <i class="fas fa-receipt me-1"></i>Data Pembelian
                </small>
            </div>
        </div>
    </td>

    <!-- DATE -->
    <td class="text-center">
        <span class="date-badge">
            <i class="fas fa-calendar-alt"></i>
            <?= date('d F Y', strtotime($d['date'])) ?>
        </span>
    </td>

    <!-- NOTE -->
    <td class="text-center">
        <span class="note-badge">
            <i class="fas fa-sticky-note"></i>
            <?= $d['note'] ?: 'Tidak ada catatan' ?>
        </span>
    </td>

    <!-- CREATED -->
    <td class="text-center">
        <span class="created-badge">
            <i class="fas fa-clock"></i>
            <?= date('d F Y', strtotime($d['created_at'])) ?>
        </span>
    </td>

    <!-- ACTION -->
    <td class="text-center">

        <button class="action-btn btn-edit editPurchaseBtn" data-id="<?= $d['id'] ?>">
            <i class="fas fa-edit"></i>
        </button>

       <button class="action-btn btn-delete deletePurchaseBtn"
            data-id="<?= $d['id'] ?>"
            data-form="<?= $d['form'] ?>"
            data-products="<?= htmlspecialchars($d['products']) ?>"
            data-qty="<?= $d['qty'] ?>"
            data-unit="<?= $d['unit'] ?>">
            <i class="fas fa-trash"></i>
        </button>

    </td>

</tr>
<?php endwhile; ?>
</tbody>
</table>
