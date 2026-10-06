<?php
include '../../../sessions/session.php';

// AMBIL PARAMETER
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : '';
$date_end   = isset($_GET['date_end']) ? $_GET['date_end'] : '';

$limit = 5;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// FILTER
$where = "WHERE status_payment != 'cancelled'";

if (!empty($date_start) && !empty($date_end)) {
    $where .= " AND DATE(tanggal) BETWEEN '$date_start' AND '$date_end'";
} elseif (!empty($date_start)) {
    $where .= " AND DATE(tanggal) >= '$date_start'";
} elseif (!empty($date_end)) {
    $where .= " AND DATE(tanggal) <= '$date_end'";
}

if (!empty($search)) {
    $where .= " AND (code LIKE '%$search%' OR id LIKE '%$search%')";
}

// QUERY DATA
$query = mysqli_query($conn, "
    SELECT sub.*,
        (
            SELECT c.id FROM order_details od
            JOIN customers c ON c.id = od.customer_id
            WHERE od.order_id = sub.id AND od.customer_id IS NOT NULL
            LIMIT 1
        ) AS customer_id,
        (
            SELECT c.name FROM order_details od
            JOIN customers c ON c.id = od.customer_id
            WHERE od.order_id = sub.id AND od.customer_id IS NOT NULL
            LIMIT 1
        ) AS customer_name
    FROM (
        SELECT * FROM orders
        WHERE status_payment != 'cancelled'
        ORDER BY id DESC
        LIMIT 50
    ) AS sub
    $where
    ORDER BY id DESC
    LIMIT $start, $limit
");

// TOTAL DATA
$totalQuery = mysqli_query($conn, "
    SELECT COUNT(*) as total FROM (
        SELECT * FROM orders
        WHERE status_payment != 'cancelled'
        ORDER BY id DESC
        LIMIT 50
    ) AS sub
    $where
");
$totalData  = mysqli_fetch_assoc($totalQuery)['total'];
$totalPage  = ceil($totalData / $limit);

// JUMLAH ASAL (sebelum filter pencarian/tanggal) - untuk sub-chip "dari N pesanan"
$baseQuery = mysqli_query($conn, "
    SELECT COUNT(*) as total FROM (
        SELECT * FROM orders
        WHERE status_payment != 'cancelled'
        ORDER BY id DESC
        LIMIT 50
    ) AS sub
");
$baseData = mysqli_fetch_assoc($baseQuery)['total'];

// FORMAT TANGGAL
function tanggalIndo($date)
{
    $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $pecah = explode('-', date('Y-m-d', strtotime($date)));
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

/* Pager ringkas ala Master (script/datatable-compact.js -> mp_compact):
   jendela 5 nomor di tablet/desktop, 3 nomor di mobile, halaman 1 &
   terakhir dikunci di ujung + ellipsis "…" hanya kalau ada halaman yang
   disembunyikan. Dua versi dirender sekaligus supaya tidak perlu fetch
   ulang saat layar dirotasi - CSS yang memilih versi mana yang tampil. */
function mpPagerHtml($page, $totalPage, $span)
{
    if ($totalPage < 1) return '';
    $last = $totalPage;
    $h = '';

    $h .= '<li class="page-item previous' . ($page <= 1 ? ' disabled' : '') . '">'
        . '<a class="page-link" href="#" aria-label="Halaman sebelumnya" onclick="loadPage(' . max(1, $page - 1) . ')">&#8249;</a></li>';

    if ($last > 1) {
        $half = (int) floor(($span - 1) / 2);
        $startPage = $page - $half;
        if ($startPage < 1) $startPage = 1;
        if ($startPage > $last - ($span - 1)) $startPage = $last - ($span - 1);
        if ($startPage < 1) $startPage = 1;
        $endPage = min($startPage + $span - 1, $last);

        if ($startPage > 1) {
            $h .= '<li class="page-item"><a class="page-link" href="#" onclick="loadPage(1)">1</a></li>';
            if ($startPage > 2) {
                $h .= '<li class="page-item disabled mp-ellipsis"><span class="page-link" aria-hidden="true">&hellip;</span></li>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $h .= '<li class="page-item' . ($i == $page ? ' active' : '') . '">'
                . '<a class="page-link" href="#" onclick="loadPage(' . $i . ')">' . $i . '</a></li>';
        }

        if ($endPage < $last) {
            if ($endPage < $last - 1) {
                $h .= '<li class="page-item disabled mp-ellipsis"><span class="page-link" aria-hidden="true">&hellip;</span></li>';
            }
            $h .= '<li class="page-item"><a class="page-link" href="#" onclick="loadPage(' . $last . ')">' . $last . '</a></li>';
        }
    } else {
        $h .= '<li class="page-item active"><a class="page-link" href="#" onclick="loadPage(1)">1</a></li>';
    }

    $h .= '<li class="page-item next' . ($page >= $totalPage ? ' disabled' : '') . '">'
        . '<a class="page-link" href="#" aria-label="Halaman berikutnya" onclick="loadPage(' . min($totalPage, $page + 1) . ')">&#8250;</a></li>';

    return $h;
}
?>

<div id="order-list">
    <?php while ($row = mysqli_fetch_assoc($query)): ?>
        <div class="order-card oc-<?= $row['status_payment'] === 'paid' ? 'paid' : 'wait'; ?>" data-id="<?= $row['id']; ?>">
            <div class="oc-glow"></div>
            <div class="oc-glow oc-glow-2"></div>

            <div class="order-header">
                <div class="oc-id">
                    <i class="fas fa-file-invoice"></i>
                    <div>
                        <span class="oc-code"><?= $row['code']; ?></span>
                        <div class="order-date">
                            <i class="fas fa-calendar-alt"></i> <?= tanggalIndo($row['tanggal']); ?>
                        </div>
                    </div>
                </div>
                <div class="oc-status-wrap">
                    <?php if (!empty($row['customer_name'])): ?>
                        <span class="oc-customer" title="Customer">
                            <i class="fas fa-user"></i>
                            <span class="oc-customer-name"><?= htmlspecialchars($row['customer_name']); ?></span>
                        </span>
                    <?php endif; ?>

                    <div class="oc-status <?= $row['status_payment'] === 'paid' ? 's-paid' : 's-wait'; ?>">
                        <i class="fas <?= $row['status_payment'] === 'paid' ? 'fa-check-circle' : 'fa-spinner fa-spin'; ?>"></i>
                        <?= $row['status_payment'] == 'paid' ? 'Terbayar' : 'Waiting'; ?>
                    </div>
                </div>
            </div>

            <div class="oc-body">
                <div class="oc-price">
                    <span class="oc-price-lbl"><i class="fas fa-money-bill-wave"></i> Total</span>
                    <span class="oc-amount">Rp <?= number_format($row['total']); ?></span>
                </div>
                <div class="oc-actions">
                    <button class="btn-soft btn-detail" onclick="showDetail(<?= $row['id']; ?>)">
                        <i class="fas fa-eye"></i> Lihat
                    </button>

                    <button class="btn-soft btn-edit-cust" onclick="editCustomer(<?= $row['id']; ?>, '<?= htmlspecialchars($row['code']); ?>', <?= $row['customer_id'] ? (int)$row['customer_id'] : 'null'; ?>)">
                        <i class="fas fa-user-pen"></i> Edit
                    </button>

                    <?php if ($row['status_payment'] !== 'paid'): ?>
                        <?php if ($user['role'] === 'developer'): ?>
                        <button class="btn-soft btn-cancel" onclick="cancelOrder(<?= $row['id']; ?>, '<?= $row['code']; ?>')">
                            <i class="fas fa-ban"></i> Cancel
                        </button>
                        <?php endif; ?>

                        <button class="btn-soft btn-pay" onclick="payOrder(<?= $row['id']; ?>, '<?= $row['code']; ?>')">
                            <i class="fas fa-money-bill-wave"></i> Bayar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php if ($totalData > 0):
    $from = ($page - 1) * $limit + 1;
    $to   = min($page * $limit, $totalData);
?>
    <div id="pagination" class="mp-pager-wrap">

        <!-- Chip info: posisi user di daftar (sama dengan halaman Master) -->
        <span class="mp-info">
            <span class="mp-info-ico"><i class="fas fa-layer-group"></i></span>
            <span class="mp-info-txt">Menampilkan <b><?= $from ?></b>&ndash;<b><?= $to ?></b> dari <b><?= $totalData ?></b> pesanan</span>
            <?php if ($totalData != $baseData): ?>
                <span class="mp-info-sub">dari <?= $baseData ?> pesanan</span>
            <?php endif; ?>
        </span>

        <!-- Dua salinan pager: desktop 5 nomor / mobile 3 nomor. Yang
             tampil dipilih CSS (lihat css/pages/order.css). -->
        <ul class="pagination mp-pager-desktop"><?= mpPagerHtml($page, $totalPage, 5) ?></ul>
        <ul class="pagination mp-pager-mobile"><?= mpPagerHtml($page, $totalPage, 3) ?></ul>

    </div>
<?php endif; ?>