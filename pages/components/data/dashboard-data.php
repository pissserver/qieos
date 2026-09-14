<?php
include '../../../script/connection.php';
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

$start = isset($_GET['start']) ? $_GET['start'] : date('Y-01-01');
$end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
$today = date('Y-m-d');

function sq($conn, $sql) { $r = @mysqli_query($conn, $sql); return $r ? mysqli_fetch_assoc($r) : null; }
function sv($conn, $sql) { $row = sq($conn, $sql); return $row ? (float)$row['v'] : 0; }
function sc($conn, $sql) { $row = sq($conn, $sql); return $row ? (int)$row['c'] : 0; }

$d_paid = "status_payment='paid' AND tanggal >= '$start' AND tanggal <= '$end'";
$d = "tanggal >= '$start' AND tanggal <= '$end'";
$dateP = "p.date >= '$start' AND p.date <= '$end'";
$t = "tp.payment_date >= '$start' AND tp.payment_date <= '$end'";
$u = "up.payment_date >= '$start' AND up.payment_date <= '$end'";

$pendapatan = sv($conn, "SELECT COALESCE(SUM(total),0) as v FROM orders WHERE status_payment='paid' AND $d");
$pengeluaran_beli = sv($conn, "SELECT COALESCE(SUM(pi.price_buy),0) as v FROM purchase_items pi JOIN purchases p ON pi.purchase_id=p.id WHERE p.deleted_at IS NULL AND pi.deleted_at IS NULL AND pi.price_buy > 0 AND $dateP");
$bayar_tenant = sv($conn, "SELECT COALESCE(SUM(tp.cost_payment),0) as v FROM tenant_payments tp WHERE tp.status='paid' AND $t");
$bayar_utility = sv($conn, "SELECT COALESCE(SUM(up.cost_payment),0) as v FROM utility_payments up WHERE up.status='paid' AND $u");
$total_pengeluaran = $pengeluaran_beli;
$laba = $pendapatan - $total_pengeluaran;

$pesanan = sc($conn, "SELECT COUNT(*) as c FROM orders WHERE $d");
$produk = sc($conn, "SELECT COUNT(*) as c FROM products");
$today_pendapatan = sv($conn, "SELECT COALESCE(SUM(total),0) as v FROM orders WHERE status_payment='paid' AND tanggal='$today'");
$today_orders = sc($conn, "SELECT COUNT(*) as c FROM orders WHERE tanggal='$today'");

$s_paid = sc($conn, "SELECT COUNT(*) as c FROM orders WHERE status_payment='paid' AND $d");
$s_wait = sc($conn, "SELECT COUNT(*) as c FROM orders WHERE status_payment='waiting' AND $d");
$s_cancel = sc($conn, "SELECT COUNT(*) as c FROM orders WHERE status_payment='cancelled' AND $d");

$chart_months = []; $chart_p = []; $chart_b = [];
$cr = @mysqli_query($conn, "SELECT DATE_FORMAT(o.tanggal,'%b %y') as label, DATE_FORMAT(o.tanggal,'%Y-%m') as km, COALESCE(SUM(o.total),0) as v FROM orders o WHERE o.status_payment='paid' AND o.tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY km ORDER BY km ASC");
if ($cr) while ($row = mysqli_fetch_assoc($cr)) {
    $chart_months[] = $row['label'];
    $chart_p[] = (float)$row['v'];
    $km = $row['km'];
    $chart_b[] = sv($conn, "SELECT COALESCE(SUM(pi.price_buy),0) as v FROM purchase_items pi JOIN purchases p ON pi.purchase_id=p.id WHERE p.deleted_at IS NULL AND pi.deleted_at IS NULL AND pi.price_buy > 0 AND DATE_FORMAT(p.date,'%Y-%m')='$km'");
}

$week = [];
$wr = @mysqli_query($conn, "SELECT DATE(tanggal) as d, DAYNAME(tanggal) as dn, COALESCE(SUM(total),0) as v FROM orders WHERE status_payment='paid' AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY d ORDER BY d ASC");
if ($wr) while ($row = mysqli_fetch_assoc($wr)) $week[] = ['l' => substr($row['dn'],0,3), 'v' => (float)$row['v']];

$recent_orders = [];
$or = @mysqli_query($conn, "SELECT o.code, o.total, o.tanggal, o.status_payment, u.fullname as operator FROM orders o LEFT JOIN users u ON o.staff_id=u.id ORDER BY o.tanggal DESC, o.id DESC LIMIT 6");
if ($or) while ($row = mysqli_fetch_assoc($or)) $recent_orders[] = $row;

$top_products = [];
$tpr = @mysqli_query($conn, "SELECT p.name, p.category, COALESCE(SUM(od.qty),0) as qty, COALESCE(SUM(od.subtotal),0) as rev FROM order_details od JOIN products p ON od.product_id=p.id JOIN orders o ON od.order_id=o.id WHERE o.status_payment='paid' GROUP BY od.product_id ORDER BY rev DESC LIMIT 5");
if ($tpr) while ($row = mysqli_fetch_assoc($tpr)) $top_products[] = $row;

$recent_list_purchases = [];
$rpr = @mysqli_query($conn, "SELECT p.date AS date_list, COUNT(pi.id) as items, COALESCE(SUM(pi.price_buy),0) as total FROM purchases p JOIN purchase_items pi ON p.id=pi.purchase_id AND pi.deleted_at IS NULL WHERE p.deleted_at IS NULL GROUP BY p.id ORDER BY p.date DESC, p.id DESC LIMIT 5");
if ($rpr) while ($row = mysqli_fetch_assoc($rpr)) $recent_list_purchases[] = $row;

echo json_encode([
    'stats' => [
        'pendapatan' => $pendapatan,
        'pengeluaran_beli' => $pengeluaran_beli,
        'bayar_tenant' => $bayar_tenant,
        'bayar_utility' => $bayar_utility,
        'total_pengeluaran' => $total_pengeluaran,
        'laba' => $laba,
        'pesanan' => $pesanan,
        'produk' => $produk,
        'today_pendapatan' => $today_pendapatan,
        'today_orders' => $today_orders,
    ],
    'status' => ['paid' => $s_paid, 'waiting' => $s_wait, 'cancelled' => $s_cancel],
    'chart_months' => $chart_months,
    'chart_pendapatan' => $chart_p,
    'chart_pengeluaran' => $chart_b,
    'chart_week' => $week,
    'recent_orders' => $recent_orders,
    'top_products' => $top_products,
    'recent_list_purchases' => $recent_list_purchases,
]);
