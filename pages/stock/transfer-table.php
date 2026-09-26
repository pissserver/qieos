<?php
include '../../sessions/session.php';

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
WHERE r.status='pending'
GROUP BY r.id
ORDER BY r.id DESC
");
?>

<?php if(mysqli_num_rows($q)==0): ?>

<div class="empty-state">

    <i class="fas fa-inbox"></i>

    <h6 class="mb-1">
        Tidak Ada Request
    </h6>

    <small>
        Saat ini tidak ada permintaan transfer stok yang menunggu persetujuan.
    </small>

</div>

<?php else: ?>

<?php while($d=mysqli_fetch_assoc($q)): ?>

<?php
    /* 🔥 Item detail request pending */
    $qi = mysqli_query($conn,"
        SELECT i.qty, p.name, p.code, p.photo
        FROM stock_request_items i
        JOIN products p ON p.id = i.product_id
        WHERE i.request_id = {$d['id']}
        ORDER BY i.id ASC
    ");
?>

<div class="request-card" data-request-id="<?= (int)$d['id'] ?>">

    <div class="request-main">

        <div class="request-head">

            <div class="req-code-pill">
                <i class="fas fa-file-invoice me-2"></i>
                <?= htmlspecialchars($d['code']) ?>
            </div>

            <div class="request-meta">

                <span class="req-by">
                    <i class="fas fa-user me-1"></i>
                    <?= htmlspecialchars($d['request_by'] ?: 'System') ?>
                </span>

                <?php if($d['total_items'] > 0): ?>
                <span class="qty-badge">
                    <i class="fas fa-boxes-stacked me-1"></i>
                    <?= number_format($d['total_items']) ?> item
                    &middot;
                    <i class="fas fa-cubes me-1"></i>
                    <?= number_format($d['total_qty']) ?> pcs
                </span>
                <?php endif; ?>

                <span class="date-badge">
                    <i class="far fa-calendar-alt me-1"></i>
                    <?= date('d M Y H:i', strtotime($d['created_at'])) ?>
                </span>

            </div>

        </div>

        <?php if(mysqli_num_rows($qi) > 0): ?>

        <div class="request-items">

            <?php while($it = mysqli_fetch_assoc($qi)): ?>

            <div class="mini-item">

                <div class="mini-prod">

                    <?php if(!empty($it['photo'])): ?>
                    <img class="product-img-sm"
                        src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($it['photo']) ?>"
                        alt="<?= htmlspecialchars($it['name']) ?>">
                    <?php else: ?>
                    <div class="product-avatar-sm">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <?php endif; ?>

                    <div>
                        <div class="product-name">
                            <?= htmlspecialchars($it['name']) ?>
                        </div>
                        <div class="product-code">
                            <?= htmlspecialchars($it['code']) ?>
                        </div>
                    </div>

                </div>

                <span class="qty-badge">
                    <i class="fas fa-cubes me-1"></i>
                    <?= number_format($it['qty']) ?> pcs
                </span>

            </div>

            <?php endwhile; ?>

        </div>

        <?php endif; ?>

    </div>

    <div class="request-action">

        <button
            onclick="approve(<?= $d['id'] ?>)"
            class="btn-approve"
            title="Terima request">

            <i class="fas fa-check me-1"></i>
            Approve

        </button>

        <button
            onclick="reject(<?= $d['id'] ?>)"
            class="btn-reject-modern"
            title="Tolak request">

            <i class="fas fa-times me-1"></i>
            Reject

        </button>

    </div>

</div>

<?php endwhile; ?>

<?php endif; ?>