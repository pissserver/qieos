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

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/history-request-table.css">

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