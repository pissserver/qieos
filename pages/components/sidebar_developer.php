<!-- MASTER DATA -->
<li class="nav-title">MASTER</li>

<li class="nav-item <?= ($current_page == 'master-product.php' || $current_page == 'master-product-detail.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/master/master-product.php" class="nav-link" data-tooltip="Master Produk">
        <span class="sidebar-icon"><i class="fas fa-boxes-stacked"></i></span>
        <span class="sidebar-text">Master Produk</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'master-supplier.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/master/master-supplier.php" class="nav-link" data-tooltip="Master Supplier">
        <span class="sidebar-icon"><i class="fas fa-truck"></i></span>
        <span class="sidebar-text">Master Supplier</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'master-customer.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/master/master-customer.php" class="nav-link" data-tooltip="Master Customer">
        <span class="sidebar-icon"><i class="fas fa-users"></i></span>
        <span class="sidebar-text">Master Customer</span>
    </a>
</li>

<!-- PEMBELIAN STOK -->
<li class="nav-title">PURCHASING</li>

<li class="nav-item <?= ($current_page == 'list.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/purchasing/list.php" class="nav-link" data-tooltip="Daftar Belanja">
        <span class="sidebar-icon"><i class="fas fa-file-alt"></i></span>
        <span class="sidebar-text">Daftar Belanja</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'purchase.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/purchasing/purchase.php" class="nav-link" data-tooltip="Input Pembelian">
        <span class="sidebar-icon"><i class="fas fa-cart-plus"></i></span>
        <span class="sidebar-text">Input Pembelian</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'additional.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/purchasing/additional.php" class="nav-link" data-tooltip="Produk Tambahan">
        <span class="sidebar-icon"><i class="fas fa-box-open"></i></span>
        <span class="sidebar-text">Produk Tambahan</span>
    </a>
</li>

<!-- GUDANG STOK (SUMBER BARANG / FIFO) -->
<li class="nav-title">GUDANG STOK</li>

<li class="nav-item <?= ($current_page == 'stock.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/stock/stock.php" class="nav-link" data-tooltip="Stok Gudang">
        <span class="sidebar-icon"><i class="fas fa-warehouse"></i></span>
        <span class="sidebar-text">Stok Gudang</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'mutation.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/stock/mutation.php" class="nav-link" data-tooltip="Mutasi Stok">
        <span class="sidebar-icon"><i class="fas fa-truck-ramp-box"></i></span>
        <span class="sidebar-text">Mutasi Stok</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'transfer.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/stock/transfer.php" class="nav-link" data-tooltip="Transfer Gudang">
        <span class="sidebar-icon"><i class="fas fa-exchange-alt"></i></span>
        <span class="sidebar-text">Transfer Gudang</span>
    </a>
</li>

<!-- GUDANG KANTIN -->
<li class="nav-title">KANTIN</li>

<li class="nav-item <?= ($current_page == 'sales-stock.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/sales/sales-stock.php" class="nav-link" data-tooltip="Stok Kantin">
        <span class="sidebar-icon"><i class="fas fa-store"></i></span>
        <span class="sidebar-text">Stok Kantin</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'catalog.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/sales/catalog.php" class="nav-link" data-tooltip="Katalog Produk">
        <span class="sidebar-icon"><i class="fas fa-book-open"></i></span>
        <span class="sidebar-text">Katalog Produk</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'order.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/sales/order.php" class="nav-link" data-tooltip="Pesanan">
        <span class="sidebar-icon"><i class="fas fa-receipt"></i></span>
        <span class="sidebar-text">Pesanan</span>
    </a>
</li>

<!-- TENANT -->
<li class="nav-title">TENANT</li>

<li class="nav-item <?= ($current_page == 'tenant.php' || $current_page == 'tenant-detail.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/tenant/tenant.php" class="nav-link" data-tooltip="Tenant">
        <span class="sidebar-icon"><i class="fas fa-store"></i></span>
        <span class="sidebar-text">Tenant</span>
    </a>
</li>

<!-- REKAP -->
<li class="nav-title">REKAP</li>

<li class="nav-item <?= ($current_page == 'recap.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/recap/recap.php" class="nav-link" data-tooltip="Penjualan & Tenant">
        <span class="sidebar-icon"><i class="fas fa-chart-bar"></i></span>
        <span class="sidebar-text">Penjualan & Tenant</span>
    </a>
</li>

<!-- LAPORAN -->
<li class="nav-title">LAPORAN</li>

<li class="nav-item <?= ($current_page == 'report-sales.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/report/report-sales.php" class="nav-link" data-tooltip="Laporan Penjualan">
        <span class="sidebar-icon"><i class="fas fa-chart-line"></i></span>
        <span class="sidebar-text">Laporan Penjualan</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'report-tenant.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/report/report-tenant.php" class="nav-link" data-tooltip="Laporan Tenant">
        <span class="sidebar-icon"><i class="fas fa-chart-line"></i></span>
        <span class="sidebar-text">Laporan Tenant</span>
    </a>
</li>

<!-- MANAJEMEN STAFF -->
<li class="nav-title">MANAJEMEN USER</li>

<li class="nav-item <?= ($current_page == 'administrator.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/management/administrator.php" class="nav-link" data-tooltip="Administrator">
        <span class="sidebar-icon"><i class="fas fa-users"></i></span>
        <span class="sidebar-text">Administrator</span>
    </a>
</li>

<li class="nav-item <?= ($current_page == 'cashier.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/management/cashier.php" class="nav-link" data-tooltip="Staff Kasir">
        <span class="sidebar-icon"><i class="fas fa-users"></i></span>
        <span class="sidebar-text">Staff Kasir</span>
    </a>
</li>

<!-- LAINNYA -->
<li class="nav-title">LAINNYA</li>

<li class="nav-item <?= ($current_page == 'update.php') ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL; ?>/pages/other/update.php" class="nav-link" data-tooltip="Update">
        <span class="sidebar-icon"><i class="fas fa-rocket"></i></span>
        <span class="sidebar-text">Update</span>
    </a>
</li>
