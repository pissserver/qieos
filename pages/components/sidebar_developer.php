<!-- PEMBELIAN STOK -->
<li class="nav-title">PURCHASING</li>

<li class="nav-item <?= ($current_page == 'list.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/purchasing/list.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-file-alt me-3"></i>
        </span>
        <span>Daftar Belanja</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'purchase.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/purchasing/purchase.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-cart-plus me-2"></i>
        </span>
        <span>Input Pembelian</span>
    </a>
</li>

<!-- GUDANG STOK (SUMBER BARANG / FIFO) -->
<li class="nav-title">GUDANG STOK</li>

<li class="nav-item <?= ($current_page == 'stock.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/stock/stock.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-warehouse me-2"></i>
        </span>
        <span>Stok Gudang</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'mutation.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/stock/mutation.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-truck-ramp-box me-2"></i>
        </span>
        <span>Mutasi Stok</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'transfer.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/stock/transfer.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-exchange-alt me-2"></i>
        </span>
        <span>Transfer ke Penjualan</span>
    </a>
</li>

<!-- GUDANG PENJUALAN -->
<li class="nav-title">PENJUALAN</li>

<li
    class="nav-item <?= ($current_page == 'sales-stock.php') ? 'active' : ''; ?>"
>
    <a href="/qieos/pages/sales/sales-stock.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-store me-2"></i>
        </span>
        <span>Stok Penjualan</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'catalog.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/sales/catalog.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-book-open me-2"></i>
        </span>
        <span>Katalog Produk</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'order.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/sales/order.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-receipt me-3"></i>
        </span>
        <span>Pesanan</span>
    </a>
</li>

<!-- TENANT -->
<li class="nav-title">TENANT</li>

<li
    class="nav-item <?= ($current_page == 'registration.php') ? 'active' : ''; ?>"
>
    <a href="/qieos/pages/tenant/registration.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-pen-to-square me-2"></i>
        </span>
        <span>Pendaftaran Tenant</span>
    </a>
</li>

<li
    class="nav-item <?= ($current_page == 'tenant.php' || $current_page == 'tenant-detail.php') ? 'active' : ''; ?>"
>
    <a href="/qieos/pages/tenant/tenant.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-store me-2"></i>
        </span>
        <span>Tenant</span>
    </a>
</li>

<!-- REKAP -->
<li class="nav-title">REKAP</li>

<li class="nav-item <?= ($current_page == 'recap.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/recap/recap.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-chart-bar me-2"></i>
        </span>
        <span>Penjualan & Tenant</span>
    </a>
</li>

<!-- LAPORAN -->
<li class="nav-title">LAPORAN</li>

<li class="nav-item <?= ($current_page == 'report.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/management/administrator.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-chart-line me-2"></i>
        </span>
        <span>Laporan Penjualan</span>
    </a>
</li>

<li
    class="nav-item <?= ($current_page == 'report-tenant.php') ? 'active' : ''; ?>"
>
    <a href="/qieos/pages/report/report-tenant.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-chart-line me-2"></i>
        </span>
        <span>Laporan Tenant</span>
    </a>
</li>

<!-- MANAJEMEN STAFF -->
<li class="nav-title">MANAJEMEN USER</li>

<li
    class="nav-item <?= ($current_page == 'administrator.php') ? 'active' : ''; ?>"
>
    <a href="/qieos/pages/management/administrator.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-users me-2"></i>
        </span>
        <span>Administrator</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'cashier.php') ? 'active' : ''; ?>">
    <a href="/qieos/pages/management/cashier.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-users me-2"></i>
        </span>
        <span>Staff Kasir</span>
    </a>
</li>


<!-- LAINNYA -->
<li class="nav-title">LAINNYA</li>

<li
    class="nav-item <?= ($current_page == 'update.php') ? 'active' : ''; ?>"
>
    <a href="/qieos/pages/other/update.php" class="nav-link">
        <span class="sidebar-icon">
            <i class="fas fa-rocket me-2"></i>
        </span>
        <span>Update</span>
    </a>
</li>