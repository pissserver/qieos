<?php
include '../../script/connection.php';

// AMBIL PARAMETER
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$date   = isset($_GET['date']) ? $_GET['date'] : '';

$limit = 5;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// FILTER
$where = "WHERE status_payment != 'cancelled'";

if (!empty($date)) {
    $where .= " AND DATE(tanggal) = '$date'";
}

if (!empty($search)) {
    $where .= " AND (customer_name LIKE '%$search%' OR id LIKE '%$search%')";
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
            <div class="order-header">
                <div>
                    <div class="order-id">
                        <i class="fas fa-user"></i> <?= $row['customer_name']; ?>
                    </div>
                    <div class="order-date">
                        <i class="fas fa-calendar-alt"></i> <?= tanggalIndo($row['tanggal']); ?>
                    </div>
                </div>
                <div class="<?= $row['status_payment'] === 'paid' ? 'badge-success' : 'badge-warning'; ?>">
                    <i class="fas <?= $row['status_payment'] === 'paid' ? 'fa-check-circle' : 'fa-spinner'; ?>"></i>
                    <?= $row['status_payment'] == 'paid' ? 'Terbayar' : 'Menunggu Pembayaran'; ?>
                </div>
            </div>

            <div class="order-body">
                <div class="badge-price">
                    <i class="fas fa-money-bill-wave"></i> Rp <?= number_format($row['total']); ?>
                </div>
                <div>
                    <button class="btn-soft btn-detail" onclick="showDetail(<?= $row['id']; ?>)">
                        Detail
                    </button>

                    <?php if ($row['status_payment'] !== 'paid'): ?>
                        <button class="btn-soft btn-cancel" onclick="cancelOrder(<?= $row['id']; ?>, '<?= $row['customer_name']; ?>')">
                            Cancel
                        </button>

                        <button class="btn-soft btn-pay" onclick="payOrder(<?= $row['id']; ?>, '<?= $row['customer_name']; ?>')">
                            Bayar
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