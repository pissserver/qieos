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
    SELECT * FROM (
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

// FORMAT TANGGAL
function tanggalIndo($date)
{
    $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $pecah = explode('-', date('Y-m-d', strtotime($date)));
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}
?>

<div id="order-list">
    <?php while ($row = mysqli_fetch_assoc($query)): ?>
        <div class="order-card">
            <div class="oc-glow"></div>

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
                <div class="oc-status <?= $row['status_payment'] === 'paid' ? 's-paid' : 's-wait'; ?>">
                    <i class="fas <?= $row['status_payment'] === 'paid' ? 'fa-check-circle' : 'fa-spinner fa-spin'; ?>"></i>
                    <?= $row['status_payment'] == 'paid' ? 'Terbayar' : 'Waiting'; ?>
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

<?php if ($totalData > 0): ?>
    <ul class="pagination justify-content-center" id="pagination">

        <!-- PREV -->
        <li class="page-item <?= ($page == 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="#" onclick="loadPage(<?= $page - 1 ?>)">Prev</a>
        </li>

        <?php
        $startPage = max(1, $page - 2);
        $endPage   = min($totalPage, $page + 2);

        if ($startPage > 1) {
            echo '<li class="page-item"><a class="page-link" onclick="loadPage(1)">1</a></li>';
            if ($startPage > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo '<li class="page-item ' . $active . '">
                <a class="page-link" onclick="loadPage(' . $i . ')">' . $i . '</a>
              </li>';
        }

        if ($endPage < $totalPage) {
            if ($endPage < $totalPage - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" onclick="loadPage(' . $totalPage . ')">' . $totalPage . '</a></li>';
        }
        ?>

        <!-- NEXT -->
        <li class="page-item <?= ($page == $totalPage) ? 'disabled' : ''; ?>">
            <a class="page-link" href="#" onclick="loadPage(<?= $page + 1 ?>)">Next</a>
        </li>

    </ul>
<?php endif; ?>