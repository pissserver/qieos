<?php
include '../../../sessions/session.php';

$q = mysqli_query($conn,"
SELECT
    r.*,
    p.name,
    p.code
FROM stock_requests r
JOIN products p
    ON p.id = r.product_id
ORDER BY r.id DESC
LIMIT 20
");
?>

<style>
    #requestHistory{
        border-collapse:separate;
        border-spacing:0 14px;
    }

    #requestHistory thead th{
        font-size:12px;
        color:#64748b;
        font-weight:700;
        border:none !important;
        text-transform:uppercase;
    }

    .history-table{
        border-collapse:separate;
        border-spacing:0 12px;
    }

    .history-table thead th{
        border:none !important;
        font-size:12px;
        text-transform:uppercase;
        color:#64748b;
        font-weight:700;
    }

    .history-row{
        background:#fff;
        border-radius:16px;
        box-shadow:0 6px 18px rgba(15,23,42,.05);
        transition:.25s;
    }

    .history-row:hover{
        box-shadow:0 12px 24px rgba(15,23,42,.10);
        transform:translateY(-2px);
    }

    .history-row td{
        padding:20px 18px;
        border:none !important;
        vertical-align:middle;
    }

    .history-row td:first-child{
        border-radius:14px 0 0 14px;
    }

    .history-row td:last-child{
        border-radius:0 14px 14px 0;
    }

    .product-wrap{
        display:flex;
        align-items:center;
        gap:14px;
    }

    .product-icon{
        width:48px;
        height:48px;
        border-radius:14px;
        background:linear-gradient(135deg,#334155,#0f172a);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:18px;
    }

    .product-name{
        font-weight:700;
        color:#0f172a;
    }

    .product-code{
        color:#64748b;
        font-size:12px;
    }

    .qty-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 14px;
        border-radius:10px;
        background:#eef2ff;
        color:#4338ca;
        font-weight:600;
    }

    .date-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 14px;
        border-radius:10px;
        background:#f8fafc;
        border:1px solid #e2e8f0;
        color:#334155;
        font-weight:600;
    }

    .status-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 14px;
        border-radius:10px;
        font-weight:700;
    }

    .status-approved{
        background:#dcfce7;
        color:#166534;
    }

    .status-rejected{
        background:#fee2e2;
        color:#991b1b;
    }

    .status-pending{
        background:#e2e8f0;
        color:#475569;
    }

    .empty-state{
        text-align:center;
        padding:60px 20px;
        color:#64748b;
    }

    .empty-state i{
        font-size:50px;
        opacity:.5;
        margin-bottom:12px;
    }

    /* DATATABLE */

    .dataTables_filter input{
        border-radius:8px !important;
        border:1px solid #cbd5e1 !important;
        padding:8px 14px !important;
        width:180px !important;
    }

    .dataTables_length select{
        min-width:75px !important;
        height:38px !important;
        padding:0 30px 0 12px !important;
        border-radius:8px !important;
        border:1px solid #cbd5e1 !important;
    }
</style>

<?php if(mysqli_num_rows($q)==0): ?>

<div class="empty-state">

    <i class="fas fa-clock"></i>

    <h6 class="mb-1">
        Belum Ada Riwayat
    </h6>

    <small>
        Riwayat transfer stok akan muncul di sini.
    </small>

</div>

<?php else: ?>

<table id="requestHistory" class="table table-hover history-table align-middle">

    <thead>
        <tr>
            <th>Produk</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Status</th>
            <th class="text-center">Tanggal</th>
        </tr>
    </thead>

    <tbody>

    <?php while($d=mysqli_fetch_assoc($q)): ?>

    <?php

        if($d['status']=='approved'){

            $statusClass = 'status-approved';
            $statusIcon  = 'fa-circle-check';
            $statusText  = 'Approved';

        }elseif($d['status']=='rejected'){

            $statusClass = 'status-rejected';
            $statusIcon  = 'fa-circle-xmark';
            $statusText  = 'Rejected';

        }else{

            $statusClass = 'status-pending';
            $statusIcon  = 'fa-clock';
            $statusText  = 'Pending';

        }

    ?>

    <tr class="history-row">
        <td>

            <div class="product-wrap">

                <div class="product-icon">
                    <i class="fas fa-box-open"></i>
                </div>

                <div>

                    <div class="product-name">
                        <?= htmlspecialchars($d['name']) ?>
                    </div>

                    <div class="product-code">
                        <?= htmlspecialchars($d['code']) ?>
                    </div>

                </div>

            </div>

        </td>

        <td class="text-center">

            <span class="qty-badge">
                <i class="fas fa-cubes"></i>
                <?= number_format($d['qty']) ?>
            </span>

        </td>

        <td class="text-center">

            <span class="status-badge <?= $statusClass ?>">

                <i class="fas <?= $statusIcon ?>"></i>

                <?= $statusText ?>

            </span>

        </td>

        <td class="text-center">

            <span class="date-badge">

                <i class="far fa-calendar-alt"></i>

                <?= date('d M Y H:i', strtotime($d['created_at'])) ?>

            </span>

        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>

<?php endif; ?>