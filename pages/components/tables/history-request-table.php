<?php
include '../../../sessions/session.php';

$view = isset($_GET['view']) ? $_GET['view'] : '';

$q = mysqli_query($conn,"
SELECT
    r.*,
    u.username AS request_by,
    COUNT(i.id) AS total_items,
    COALESCE(SUM(i.qty),0) AS total_qty
FROM stock_requests r
LEFT JOIN stock_request_items i
    ON i.request_id = r.id
LEFT JOIN users u
    ON u.id = r.created_by
GROUP BY r.id
ORDER BY r.id DESC
LIMIT 20
");
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/history-request-table.css?v=<?php echo filemtime(__DIR__ . '/../../../css/pages/history-request-table.css'); ?>">

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
            <th>No. Request</th>
            <th class="text-center">Total Item</th>
            <th class="text-center">Status</th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Aksi</th>
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

        /* 🔥 Ambil detail item request */
        $qi = mysqli_query($conn,"
            SELECT i.qty, p.name, p.code, p.photo, p.id AS product_id
            FROM stock_request_items i
            JOIN products p ON p.id = i.product_id
            WHERE i.request_id = {$d['id']}
            ORDER BY i.id ASC
        ");

        $itemsJson = [];

        while($it = mysqli_fetch_assoc($qi)){
            $itemsJson[] = [
                'product_id'=> (int)$it['product_id'],
                'name'  => $it['name'],
                'code'  => $it['code'],
                'photo' => !empty($it['photo'])
                            ? BASE_URL.'/assets/img/products/'.htmlspecialchars($it['photo'])
                            : '',
                'qty'   => (int)$it['qty']
            ];
        }

        $itemsAttr = htmlspecialchars(json_encode($itemsJson, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES);

    ?>

    <tr class="history-row"
        data-id="<?= (int)$d['id'] ?>"
        data-code="<?= htmlspecialchars($d['code']) ?>"
        data-status="<?= htmlspecialchars($d['status']) ?>"
        data-items="<?= $itemsAttr ?>">

        <td>

            <div class="product-wrap">

                <button type="button" class="btn-expand" title="Lihat detail">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="req-code-badge">
                    <i class="fas fa-file-invoice"></i>
                    <?= htmlspecialchars($d['code']) ?>
                </div>

                <div>
                    <div class="product-name">
                        <?= number_format($d['total_items']) ?> produk
                    </div>

                    <div class="product-code">
                        oleh <?= htmlspecialchars($d['request_by'] ?: 'System') ?>
                    </div>
                </div>

            </div>

        </td>

        <td class="text-center">

            <span class="qty-badge">
                <i class="fas fa-cubes"></i>
                <?= number_format($d['total_qty']) ?> pcs
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

        <td class="text-center">

            <?php if($view === 'sales'): ?>

                <?php if($d['status'] === 'pending'): ?>

                <div class="row-actions">
                    <button class="act-btn act-edit"
                        onclick="editRequest(this)"
                        title="Edit request">
                        <i class="fas fa-pen"></i>
                    </button>

                    <button class="act-btn act-del"
                        onclick="deleteRequest(this)"
                        title="Hapus request">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <?php else: ?>

                <span class="lock-hint" title="Tidak dapat diubah">
                    <i class="fas fa-lock"></i>
                </span>

                <?php endif; ?>

            <?php elseif($view === 'transfer'): ?>

                <button class="act-btn act-print"
                    onclick="printRequest(this)"
                    title="Print request">
                    <i class="fas fa-print"></i>
                </button>

            <?php endif; ?>

        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>

<?php endif; ?>