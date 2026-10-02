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

/* Daftar item dibatasi 10 baris. Sisanya diringkas jadi satu
   keterangan "+n produk lainnya" di bawah daftar. */
$ITEM_MAX = 10;
?>

<?php if(mysqli_num_rows($q)==0): ?>

<div class="empty-state">

    <span class="empty-mark">
        <i class="fas fa-inbox"></i>
    </span>

    <h6 class="empty-title">Tidak Ada Request</h6>

    <small class="empty-text">
        Saat ini tidak ada permintaan transfer stok yang menunggu persetujuan.
    </small>

</div>

<?php else: ?>

<?php while($d=mysqli_fetch_assoc($q)): ?>

<?php
    $qi = mysqli_query($conn,"
        SELECT i.qty, p.name, p.code, p.photo
        FROM stock_request_items i
        JOIN products p ON p.id = i.product_id
        WHERE i.request_id = {$d['id']}
        ORDER BY i.id ASC
    ");

    $items = [];
    while($it = mysqli_fetch_assoc($qi)){
        $items[] = $it;
    }

    $reqId    = (int)$d['id'];
    $totalQty = (int)$d['total_qty'];
    $shown    = count($items);
?>

<article class="request-card" data-request-id="<?= $reqId ?>">

    <!-- RAIL: penanda tipis di tepi kiri kartu -->
    <span class="request-rail" aria-hidden="true"></span>

    <!-- Kepala = satu tombol disclosure: area sentuh besar di mobile,
         tetap bisa dijangkau keyboard dan terbaca screen reader. -->
    <button type="button"
        class="request-head"
        aria-expanded="false"
        aria-controls="requestItems<?= $reqId ?>"
        onclick="toggleRequestItems(this)">

        <span class="request-mark">
            <i class="fas fa-file-invoice"></i>
        </span>

        <span class="request-head-text">

            <span class="request-code"><?= htmlspecialchars($d['code']) ?></span>

            <span class="request-by">

                <i class="far fa-user" aria-hidden="true"></i>
                <span><?= htmlspecialchars($d['request_by'] ?: 'System') ?></span>

                <span class="request-by-dot" aria-hidden="true"></span>

                <span><i class="far fa-clock" aria-hidden="true"></i> <?= date('d M Y · H:i', strtotime($d['created_at'])) ?></span>

            </span>

        </span>

        <span class="request-side">

            <span class="status-badge status-pending">
                <i class="fas fa-clock" aria-hidden="true"></i>
                Pending
            </span>

            <span class="stat-chip">
                <i class="fas fa-layer-group" aria-hidden="true"></i>
                <?= number_format($d['total_items']) ?> produk
            </span>

            <span class="stat-chip stat-chip-strong">
                <i class="fas fa-cubes" aria-hidden="true"></i>
                <?= number_format($totalQty) ?> pcs
            </span>

        </span>

        <span class="request-toggle">

            <span class="request-toggle-text">
                Detail
                <span class="request-toggle-count"><?= number_format($d['total_items']) ?></span>
            </span>

            <i class="fas fa-chevron-down" aria-hidden="true"></i>

        </span>

    </button>

    <div class="request-items" id="requestItems<?= $reqId ?>">

        <div class="request-items-inner">

            <div class="request-items-panel">

                <div class="request-items-head">
                    <span class="request-items-title">Daftar Produk</span>
                    <span class="request-items-total">
                        <?= number_format($d['total_items']) ?> produk &middot; <?= number_format($totalQty) ?> pcs
                    </span>
                </div>

                <div class="item-list">

                    <?php foreach(array_slice($items, 0, $ITEM_MAX) as $it): ?>

                    <div class="item-line" title="<?= htmlspecialchars($it['name'] . ' (' . $it['code'] . ')') ?>">

                        <div class="item-prod">

                            <?php if(!empty($it['photo'])): ?>
                            <img class="item-thumb"
                                src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($it['photo']) ?>"
                                alt=""
                                loading="lazy"
                                width="40" height="40">
                            <?php else: ?>
                            <span class="item-thumb item-thumb-empty" aria-hidden="true">
                                <i class="fas fa-box-open"></i>
                            </span>
                            <?php endif; ?>

                            <div class="item-text">
                                <span class="item-name"><?= htmlspecialchars($it['name']) ?></span>
                                <span class="item-code"><?= htmlspecialchars($it['code']) ?></span>
                            </div>

                        </div>

                        <span class="item-qty">
                            <i class="fas fa-cubes" aria-hidden="true"></i>
                            <?= number_format($it['qty']) ?>
                        </span>

                    </div>

                    <?php endforeach; ?>

                </div>

                <?php if($shown > $ITEM_MAX): ?>

                <div class="line-more">
                    <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                    <?= number_format($shown - $ITEM_MAX) ?> produk lainnya
                </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <div class="request-action">

        <button type="button"
            class="btn-reject"
            onclick="reject(<?= $reqId ?>)">
            <i class="fas fa-xmark" aria-hidden="true"></i>
            Tolak
        </button>

        <button type="button"
            class="btn-approve"
            onclick="approve(<?= $reqId ?>)">
            <i class="fas fa-check" aria-hidden="true"></i>
            Setujui
        </button>

    </div>

</article>

<?php endwhile; ?>

<?php endif; ?>
