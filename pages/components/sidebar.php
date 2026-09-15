<?php
    if (!defined('BASE_URL')) {
        require_once __DIR__ . '/../../script/connection.php';
    }

    $current_page = basename($_SERVER['PHP_SELF']);
    $menu = isset($_GET['menu']) ? $_GET['menu'] : '';

    // Ambil versi update terbaru dari database jika koneksi $conn tersedia
    $latest_version = 'v1.0.0';
    if (isset($conn) && $conn) {
        $q_ver = mysqli_query($conn, "SELECT update_version FROM updates WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 1");
        if ($q_ver && mysqli_num_rows($q_ver) > 0) {
            $row_ver = mysqli_fetch_assoc($q_ver);
            $latest_version = 'v' . ltrim($row_ver['update_version'], 'v');
        }
    }
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/sidebar.css">

<!-- Mobile Overlay Backdrop -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Floating Tooltip Container for Collapsed State -->
<div class="sidebar-tooltip" id="sidebarTooltip"></div>

<!-- Mobile Top Navbar -->
<nav class="navbar navbar-dark navbar-theme-primary px-3 d-lg-none" style="gap:8px;">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>/pages/dashboard.php">
        <img src="<?php echo BASE_URL; ?>/assets/img/brand/qieos.png" alt="Qieos Logo" style="height: 40px; width: auto;" />
    </a>
    </a>
    <div class="d-flex align-items-center gap-2 ms-auto">
        <button class="mobile-nav-btn" onclick="openSearch()" aria-label="Search" style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;">
            <i class="fas fa-search"></i>
        </button>
        <button class="mobile-nav-btn" id="whatsNewBtnMobile" aria-label="What's New" style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;position:relative;">
            <i class="fas fa-bullhorn"></i>
            <span style="position:absolute;top:-3px;right:-3px;background:#ef4444;color:#fff;font-size:7px;font-weight:700;padding:2px 4px;border-radius:4px;">NEW</span>
        </button>
        <button
            class="navbar-toggler"
            type="button"
            id="mobileSidebarToggle"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Main Collapsible Sidebar -->
<div class="sidebar" id="sidebarMenu">
    <!-- Header -->
    <div class="sidebar-header">
        <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <img src="<?php echo BASE_URL; ?>/assets/img/brand/qieos2.png" alt="Qieos Logo" />
            </div>
            <div class="sidebar-logo-text">
                <h4>Qieos</h4>
                <span>POS Management</span>
            </div>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <!-- Mobile User Profile Block -->
    <div class="mobile-user-card d-lg-none mx-3 mt-3">
        <div class="d-flex align-items-center gap-3">
            <img
                src="<?php echo $user['photo'] ? BASE_URL . '/assets/img/uploads/' . $user['photo'] : BASE_URL . '/assets/img/default-avatar.jpg'; ?>"
                class="rounded-circle"
                style="width: 38px; height: 38px; object-fit: cover;"
                alt="User" />
            <div class="overflow-hidden">
                <h6 class="mb-0 text-white text-truncate" style="font-size: 13px; font-weight: 700;">
                    <?php echo $user['fullname'] != '' ? $user['fullname'] : $_SESSION['username']; ?>
                </h6>
                <small class="text-secondary" style="font-size: 11px;">
                    <?php echo ucwords(strtolower($user['role'])); ?>
                </small>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>/sessions/logout.php" class="btn btn-sm btn-danger w-100 mt-2 py-1" style="background: #ef4444; border: none; font-size: 12px; font-weight: 600;">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </div>

    <!-- Menu Items -->
    <div class="sidebar-menu" id="sidebarMenuContainer">
        <ul class="nav flex-column">
            <li class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="nav-link" data-tooltip="Dashboard">
                    <span class="sidebar-icon"><i class="fas fa-th-large"></i></span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            <?php if ($user['role'] == 'developer') { ?>
                <?php include 'sidebar_developer.php' ?>
            <?php } ?>

            <?php if ($user['role'] == 'administrator') { ?>

                <!-- MASTER DATA -->
                <li class="nav-title">MASTER</li>

                <li class="nav-item <?= ($current_page == 'coming-soon.php' && $menu == 'master-product') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=master-product" class="nav-link" data-tooltip="Master Produk">
                        <span class="sidebar-icon"><i class="fas fa-boxes-stacked"></i></span>
                        <span class="sidebar-text">Master Produk</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'coming-soon.php' && $menu == 'master-supplier') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=master-supplier" class="nav-link" data-tooltip="Master Supplier">
                        <span class="sidebar-icon"><i class="fas fa-truck"></i></span>
                        <span class="sidebar-text">Master Supplier</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'coming-soon.php' && $menu == 'master-customer') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=master-customer" class="nav-link" data-tooltip="Master Customer">
                        <span class="sidebar-icon"><i class="fas fa-users"></i></span>
                        <span class="sidebar-text">Master Customer</span>
                    </a>
                </li>

                <!-- PEMBELIAN STOK -->
                <li class="nav-title">PURCHASING</li>

                <li class="nav-item <?= ($current_page == 'list.php' || ($current_page == 'coming-soon.php' && $menu == 'list')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=list" class="nav-link" data-tooltip="Daftar Belanja">
                        <span class="sidebar-icon"><i class="fas fa-file-alt"></i></span>
                        <span class="sidebar-text">Daftar Belanja</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'purchase.php' || ($current_page == 'coming-soon.php' && $menu == 'purchase')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=purchase" class="nav-link" data-tooltip="Input Pembelian">
                        <span class="sidebar-icon"><i class="fas fa-cart-plus"></i></span>
                        <span class="sidebar-text">Input Pembelian</span>
                    </a>
                </li>

                <!-- GUDANG STOK (SUMBER BARANG / FIFO) -->
                <li class="nav-title">GUDANG STOK</li>

                <li class="nav-item <?= ($current_page == 'stock.php' || ($current_page == 'coming-soon.php' && $menu == 'stock')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=stock" class="nav-link" data-tooltip="Stok Gudang">
                        <span class="sidebar-icon"><i class="fas fa-warehouse"></i></span>
                        <span class="sidebar-text">Stok Gudang</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'mutation.php' || ($current_page == 'coming-soon.php' && $menu == 'mutation')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=mutation" class="nav-link" data-tooltip="Mutasi Stok">
                        <span class="sidebar-icon"><i class="fas fa-truck-ramp-box"></i></span>
                        <span class="sidebar-text">Mutasi Stok</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'transfer.php' || ($current_page == 'coming-soon.php' && $menu == 'transfer')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=transfer" class="nav-link" data-tooltip="Transfer Gudang">
                        <span class="sidebar-icon"><i class="fas fa-exchange-alt"></i></span>
                        <span class="sidebar-text">Transfer Gudang</span>
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

                <!-- LAPORAN -->
                <li class="nav-title">LAPORAN</li>

                <li class="nav-item <?= ($current_page == 'report-sales.php' || ($current_page == 'coming-soon.php' && $menu == 'report-sales')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=report-sales" class="nav-link" data-tooltip="Laporan Penjualan">
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

                <li class="nav-item <?= ($current_page == 'administrator.php' || ($current_page == 'administrator.php' && $menu == 'administrator')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/management/administrator.php" class="nav-link" data-tooltip="Administrator">
                        <span class="sidebar-icon"><i class="fas fa-users"></i></span>
                        <span class="sidebar-text">Administrator</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'cashier.php' || ($current_page == 'cashier.php' && $menu == 'cashier')) ? 'active' : ''; ?>">
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
            <?php } ?>

            <?php if ($user['role'] == 'staff kasir') { ?>
                <!-- GUDANG KANTIN -->
                <li class="nav-title">KANTIN</li>

                <li class="nav-item <?= ($current_page == 'sales-stock.php' || ($current_page == 'coming-soon.php' && $menu == 'sales-stock')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=sales-stock" class="nav-link" data-tooltip="Stok Kantin">
                        <span class="sidebar-icon"><i class="fas fa-store"></i></span>
                        <span class="sidebar-text">Stok Kantin</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'catalog.php' || ($current_page == 'coming-soon.php' && $menu == 'catalog')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=catalog" class="nav-link" data-tooltip="Katalog Produk">
                        <span class="sidebar-icon"><i class="fas fa-book-open"></i></span>
                        <span class="sidebar-text">Katalog Produk</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'order.php' || ($current_page == 'coming-soon.php' && $menu == 'order')) ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/coming-soon.php?menu=order" class="nav-link" data-tooltip="Pesanan">
                        <span class="sidebar-icon"><i class="fas fa-receipt"></i></span>
                        <span class="sidebar-text">Pesanan</span>
                    </a>
                </li>

                <!-- TENANT -->
                <li class="nav-title">TENANT</li>

                <li class="nav-item <?= ($current_page == 'tenant.php' || $current_page == 'tenant-detail.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/tenant/tenant.php" class="nav-link" data-tooltip="Daftar Tenant">
                        <span class="sidebar-icon"><i class="fas fa-store"></i></span>
                        <span class="sidebar-text">Daftar Tenant</span>
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

                <!-- LAINNYA -->
                <li class="nav-title">LAINNYA</li>

                <li class="nav-item <?= ($current_page == 'update.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/other/update.php" class="nav-link" data-tooltip="Update">
                        <span class="sidebar-icon"><i class="fas fa-rocket"></i></span>
                        <span class="sidebar-text">Update</span>
                    </a>
                </li>
            <?php } ?>
        
        </ul>
    </div>

    <!-- Footer -->
    <div class="sidebar-footer">
        <div class="version-badge">
            <i class="fas fa-code-branch version-icon"></i>
            <div class="version-text">
                <span>Qieos</span>
                <small><?php echo htmlspecialchars($latest_version); ?></small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebarMenu');
        const overlay = document.getElementById('sidebarOverlay');
        const desktopToggle = document.getElementById('sidebarToggle');
        const mobileToggle = document.getElementById('mobileSidebarToggle');
        const tooltip = document.getElementById('sidebarTooltip');
        const menuContainer = document.getElementById('sidebarMenuContainer');

        // Restore collapsed state on desktop from localStorage
        if (window.innerWidth >= 992) {
            const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
            }
        }

        // Auto Scroll to Active Menu Item
        const activeItem = sidebar.querySelector('.nav-item.active');
        if (activeItem && menuContainer) {
            setTimeout(function() {
                activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }

        // Desktop Toggle Action
        if (desktopToggle) {
            desktopToggle.addEventListener('click', function() {
                const content = document.querySelector('.content');
                if (content) content.classList.add('sidebar-transition');
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
                if (tooltip) tooltip.classList.remove('show');
                setTimeout(function() {
                    if (content) content.classList.remove('sidebar-transition');
                }, 320);
            });
        }

        // Mobile Toggle Action
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }

        // Mobile Overlay Click Close
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        // Floating Tooltip for Collapsed Sidebar
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('mouseenter', function() {
                if (sidebar.classList.contains('collapsed') && window.innerWidth >= 992) {
                    const text = this.getAttribute('data-tooltip') || this.querySelector('.sidebar-text')?.textContent;
                    if (text && tooltip) {
                        const rect = this.getBoundingClientRect();
                        tooltip.textContent = text.trim();
                        tooltip.style.top = (rect.top + rect.height / 2) + 'px';
                        tooltip.classList.add('show');
                    }
                }
            });

            link.addEventListener('mouseleave', function() {
                if (tooltip) tooltip.classList.remove('show');
            });
        });

        if (menuContainer) {
            menuContainer.addEventListener('scroll', function() {
                if (tooltip) tooltip.classList.remove('show');
            });
        }
    });
</script>
