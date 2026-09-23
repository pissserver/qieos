<?php
include '../../../script/connection.php';
header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';

if ($q === '') {
    echo json_encode(['status' => 'success', 'results' => ['pages' => [], 'products' => [], 'suppliers' => [], 'customers' => [], 'orders' => [], 'tenants' => []]]);
    exit;
}

$like = '%' . $conn->real_escape_string($q) . '%';

$cs = BASE_URL . '/pages/coming-soon.php?menu=';

$badge = function($name, $url, $icon, $category) {
    return ['name' => $name, 'url' => $url, 'icon' => $icon, 'category' => $category];
};

$pages = [
    $badge('Dashboard', BASE_URL . '/pages/dashboard.php', 'fas fa-chart-pie', 'Halaman'),
    $badge('Profil', BASE_URL . '/pages/profile/profile.php', 'fas fa-user-circle', 'Akun'),
];

if ($role === 'developer') {
    $pages = array_merge($pages, [
        $badge('Master Produk', BASE_URL . '/pages/master/master-product.php', 'fas fa-boxes-stacked', 'Master Data'),
        $badge('Master Supplier', BASE_URL . '/pages/master/master-supplier.php', 'fas fa-truck', 'Master Data'),
        $badge('Master Customer', BASE_URL . '/pages/master/master-customer.php', 'fas fa-users', 'Master Data'),
        $badge('Daftar Belanja', BASE_URL . '/pages/purchasing/list.php', 'fas fa-file-alt', 'Purchasing'),
        $badge('Input Pembelian', BASE_URL . '/pages/purchasing/purchase.php', 'fas fa-cart-plus', 'Purchasing'),
        $badge('Stok Gudang', BASE_URL . '/pages/stock/stock.php', 'fas fa-warehouse', 'Persediaan'),
        $badge('Mutasi Stok', BASE_URL . '/pages/stock/mutation.php', 'fas fa-exchange-alt', 'Persediaan'),
        $badge('Transfer Gudang', BASE_URL . '/pages/stock/transfer.php', 'fas fa-truck-loading', 'Persediaan'),
        $badge('Stok Kantin', BASE_URL . '/pages/sales/sales-stock.php', 'fas fa-store', 'Penjualan'),
        $badge('Katalog Produk', BASE_URL . '/pages/sales/catalog.php', 'fas fa-book-open', 'Penjualan'),
        $badge('Pesanan', BASE_URL . '/pages/sales/order.php', 'fas fa-receipt', 'Penjualan'),
        $badge('Checkout', BASE_URL . '/pages/checkout.php', 'fas fa-credit-card', 'Penjualan'),
        $badge('Tenant', BASE_URL . '/pages/tenant/tenant.php', 'fas fa-store', 'Tenant'),
        $badge('Rekap Penjualan & Tenant', BASE_URL . '/pages/recap/recap.php', 'fas fa-clipboard-list', 'Rekap'),
        $badge('Laporan Penjualan', BASE_URL . '/pages/report/report-sales.php', 'fas fa-chart-bar', 'Laporan'),
        $badge('Laporan Tenant', BASE_URL . '/pages/report/report-tenant.php', 'fas fa-file-invoice-dollar', 'Laporan'),
        $badge('Master User', BASE_URL . '/pages/master/master-user.php', 'fas fa-users-cog', 'Management'),
        $badge('Update', BASE_URL . '/pages/other/update.php', 'fas fa-rocket', 'Sistem'),
    ]);
} elseif ($role === 'administrator') {
    $pages = array_merge($pages, [
        $badge('Master Produk', $cs . 'master-product', 'fas fa-boxes-stacked', 'Master Data'),
        $badge('Master Supplier', $cs . 'master-supplier', 'fas fa-truck', 'Master Data'),
        $badge('Master Customer', $cs . 'master-customer', 'fas fa-users', 'Master Data'),
        $badge('Daftar Belanja', $cs . 'list', 'fas fa-file-alt', 'Purchasing'),
        $badge('Input Pembelian', $cs . 'purchase', 'fas fa-cart-plus', 'Purchasing'),
        $badge('Stok Gudang', $cs . 'stock', 'fas fa-warehouse', 'Persediaan'),
        $badge('Mutasi Stok', $cs . 'mutation', 'fas fa-exchange-alt', 'Persediaan'),
        $badge('Transfer Gudang', $cs . 'transfer', 'fas fa-truck-loading', 'Persediaan'),
        $badge('Tenant', BASE_URL . '/pages/tenant/tenant.php', 'fas fa-store', 'Tenant'),
        $badge('Laporan Penjualan', $cs . 'report-sales', 'fas fa-chart-bar', 'Laporan'),
        $badge('Laporan Tenant', BASE_URL . '/pages/report/report-tenant.php', 'fas fa-file-invoice-dollar', 'Laporan'),
        $badge('Master User', BASE_URL . '/pages/master/master-user.php', 'fas fa-users-cog', 'Management'),
        $badge('Update', BASE_URL . '/pages/other/update.php', 'fas fa-rocket', 'Sistem'),
    ]);
} else {
    $pages = array_merge($pages, [
        $badge('Stok Kantin', $cs . 'sales-stock', 'fas fa-store', 'Penjualan'),
        $badge('Katalog Produk', $cs . 'catalog', 'fas fa-book-open', 'Penjualan'),
        $badge('Pesanan', $cs . 'order', 'fas fa-receipt', 'Penjualan'),
        $badge('Checkout', BASE_URL . '/pages/checkout.php', 'fas fa-credit-card', 'Penjualan'),
        $badge('Riwayat Pesanan', BASE_URL . '/pages/sales/order.php', 'fas fa-history', 'Penjualan'),
        $badge('Daftar Tenant', BASE_URL . '/pages/tenant/tenant.php', 'fas fa-store', 'Tenant'),
        $badge('Rekap Penjualan & Tenant', BASE_URL . '/pages/recap/recap.php', 'fas fa-clipboard-list', 'Rekap'),
        $badge('Update', BASE_URL . '/pages/other/update.php', 'fas fa-rocket', 'Sistem'),
    ]);
}

$matched_pages = [];
foreach ($pages as $p) {
    if (stripos($p['name'], $q) !== false) {
        $matched_pages[] = $p;
    }
}

$is_dev = ($role === 'developer');
$is_admin = ($role === 'administrator');

$matched_products = [];
$prod_q = $conn->query("SELECT id, name, code, category, sell_price, photo FROM products WHERE (name LIKE '$like' OR code LIKE '$like' OR category LIKE '$like') ORDER BY name ASC LIMIT 8");
if ($prod_q) {
    while ($row = $prod_q->fetch_assoc()) {
        $matched_products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'code' => $row['code'],
            'category' => $row['category'],
            'price' => 'Rp ' . number_format($row['sell_price'], 0, ',', '.'),
            'url' => $is_dev
                ? BASE_URL . '/pages/master/master-product-detail.php?id=' . $row['id']
                : ($is_admin ? $cs . 'master-product' : BASE_URL . '/pages/sales/catalog.php?highlight=' . $row['id']),
            'icon' => 'fas fa-box',
            'category_label' => 'Produk'
        ];
    }
}

$matched_suppliers = [];
$matched_customers = [];
if ($is_dev || $is_admin) {
    $sup_q = $conn->query("SELECT id, name, phone, address FROM suppliers WHERE deleted_at IS NULL AND (name LIKE '$like' OR phone LIKE '$like' OR address LIKE '$like') ORDER BY name ASC LIMIT 8");
    if ($sup_q) {
        while ($row = $sup_q->fetch_assoc()) {
            $matched_suppliers[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'phone' => $row['phone'] ?: '-',
                'address' => $row['address'] ?: '-',
                'url' => $is_dev
                    ? BASE_URL . '/pages/master/master-supplier.php'
                    : $cs . 'master-supplier',
                'icon' => 'fas fa-truck',
                'category_label' => 'Supplier'
            ];
        }
    }

    $cus_q = $conn->query("SELECT id, name, phone FROM customers WHERE deleted_at IS NULL AND (name LIKE '$like' OR phone LIKE '$like') ORDER BY name ASC LIMIT 8");
    if ($cus_q) {
        while ($row = $cus_q->fetch_assoc()) {
            $matched_customers[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'phone' => $row['phone'] ?: '-',
                'url' => $is_dev
                    ? BASE_URL . '/pages/master/master-customer.php'
                    : $cs . 'master-customer',
                'icon' => 'fas fa-user',
                'category_label' => 'Customer'
            ];
        }
    }
}

$matched_orders = [];
$ord_q = $conn->query("SELECT id, code, tanggal, total, status_payment FROM orders WHERE id LIKE '$like' OR code LIKE '$like' ORDER BY id DESC LIMIT 10");
if ($ord_q) {
    while ($row = $ord_q->fetch_assoc()) {
        $status_label = ucfirst($row['status_payment']);
        $matched_orders[] = [
            'id' => $row['id'],
            'code' => $row['code'],
            'date' => date('d M Y', strtotime($row['tanggal'])),
            'total' => 'Rp ' . number_format($row['total'], 0, ',', '.'),
            'status' => $status_label,
            'url' => BASE_URL . '/pages/receipt.php?id=' . $row['id'],
            'icon' => 'fas fa-receipt',
            'category_label' => 'Pesanan'
        ];
    }
}

$matched_tenants = [];
$tnt_q = $conn->query("SELECT id, tenant_name, tenant_owner FROM tenants WHERE tenant_name LIKE '$like' OR tenant_owner LIKE '$like' ORDER BY tenant_name ASC LIMIT 5");
if ($tnt_q) {
    while ($row = $tnt_q->fetch_assoc()) {
        $matched_tenants[] = [
            'id' => $row['id'],
            'name' => $row['tenant_name'],
            'owner' => $row['tenant_owner'],
            'url' => BASE_URL . '/pages/tenant/tenant-detail.php?id=' . $row['id'],
            'icon' => 'fas fa-store',
            'category_label' => 'Tenant'
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'results' => [
        'pages' => $matched_pages,
        'products' => $matched_products,
        'suppliers' => $matched_suppliers,
        'customers' => $matched_customers,
        'orders' => $matched_orders,
        'tenants' => $matched_tenants
    ]
]);