<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
SELECT
    r.*,
    p.name,
    p.code
FROM stock_requests r
JOIN products p
    ON p.id = r.product_id
WHERE r.status='pending'
ORDER BY r.id DESC
");
?>

<style>

.request-card{
    background:#fff;
    border-radius:18px;
    padding:20px;
    margin-bottom:15px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 8px 18px rgba(15,23,42,.05);
    transition:.25s;
    border:1px solid #f1f5f9;
}

.request-card:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 30px rgba(15,23,42,.10);
}

.request-left{
    display:flex;
    align-items:center;
    gap:16px;
}

.product-avatar{
    width:58px;
    height:58px;
    border-radius:16px;
    background:linear-gradient(135deg,#334155,#0f172a);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
}

.product-name{
    font-size:15px;
    font-weight:700;
    color:#0f172a;
}

.product-code{
    font-size:12px;
    color:#64748b;
}

.request-meta{
    margin-top:10px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.qty-badge{
    background:#eef2ff;
    color:#4338ca;
    padding:8px 12px;
    border-radius:10px;
    font-size:13px;
    font-weight:600;
}

.date-badge{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    color:#334155;
    padding:8px 12px;
    border-radius:10px;
    font-size:13px;
    font-weight:600;
}

.request-action{
    display:flex;
    gap:10px;
}

.btn-approve{
    border:none;
    background:linear-gradient(135deg,#16a34a,#15803d);
    color:#fff;
    padding:10px 16px;
    border-radius:12px;
    font-weight:600;
    transition:.25s;
}

.btn-approve:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(22,163,74,.25);
}

.btn-reject-modern{
    border:none;
    background:linear-gradient(135deg,#dc2626,#b91c1c);
    color:#fff;
    padding:10px 16px;
    border-radius:12px;
    font-weight:600;
    transition:.25s;
}

.btn-reject-modern:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(220,38,38,.25);
}

.empty-state{
    text-align:center;
    padding:60px 20px;
    color:#64748b;
}

.empty-state i{
    font-size:48px;
    margin-bottom:15px;
    opacity:.5;
}

@media(max-width:768px){

    .request-card{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .request-action{
        width:100%;
    }

    .request-action button{
        flex:1;
    }

}
</style>

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

<div class="request-card">

    <div class="request-left">

        <div class="product-avatar">
            <i class="fas fa-box-open"></i>
        </div>

        <div>

            <div class="product-name">
                <?= htmlspecialchars($d['name']) ?>
            </div>

            <div class="product-code">
                <?= htmlspecialchars($d['code']) ?>
            </div>

            <div class="request-meta">

                <span class="qty-badge">
                    <i class="fas fa-cubes me-1"></i>
                    Qty Request :
                    <?= number_format($d['qty']) ?>
                </span>

                <span class="date-badge">
                    <i class="far fa-calendar-alt me-1"></i>
                    <?= date('d M Y H:i', strtotime($d['created_at'])) ?>
                </span>

            </div>

        </div>

    </div>

    <div class="request-action">

        <button
            onclick="approve(<?= $d['id'] ?>)"
            class="btn-approve">

            <i class="fas fa-check me-1"></i>
            Approve

        </button>

        <button
            onclick="reject(<?= $d['id'] ?>)"
            class="btn-reject-modern">

            <i class="fas fa-times me-1"></i>
            Reject

        </button>

    </div>

</div>

<?php endwhile; ?>

<?php endif; ?>