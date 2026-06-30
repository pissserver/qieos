<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar-footer {
        padding: 20px 16px;
        text-align: center;
        position: relative;
    }

    /* subtle animated divider */
    .sidebar-footer::before {
        content: '';
        display: block;
        height: 1px;
        width: 75%;
        margin: 0 auto 16px;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(120, 180, 255, 0.15),
            rgba(120, 180, 255, 0.8),
            rgba(120, 180, 255, 0.15),
            transparent
        );
        background-size: 200% 100%;
        animation: lineFlow 3s linear infinite;
        opacity: 0.9;
    }

    @keyframes lineFlow {
        0% { background-position: 0% 0; }
        100% { background-position: 200% 0; }
    }

    /* premium animated badge */
    .version-badge {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 8px;

        padding: 8px 18px;
        border-radius: 999px;

        color: #eaf2ff;
        font-size: 12px;
        letter-spacing: 0.5px;

        background: rgba(15, 18, 30, 0.65);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);

        z-index: 1;
        overflow: hidden;

        transform: translateY(0);
        animation: floatBadge 4s ease-in-out infinite;

        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.45),
            inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    /* floating motion */
    @keyframes floatBadge {
        0%   { transform: translateY(0px); }
        50%  { transform: translateY(-4px); }
        100% { transform: translateY(0px); }
    }

    /* animated gradient border */
    .version-badge::before {
        content: "";
        position: absolute;
        inset: 0;
        padding: 1px;
        border-radius: 999px;

        background: linear-gradient(
            120deg,
            #4b6cb7,
            #8ec5fc,
            #182848,
            #4b6cb7
        );

        background-size: 300% 300%;
        animation: gradientMove 6s ease infinite;

        -webkit-mask: 
            linear-gradient(#000 0 0) content-box, 
            linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;

        pointer-events: none;
    }

    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* inner shine */
    .version-badge::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -60%;
        width: 60%;
        height: 200%;
        background: linear-gradient(
            120deg,
            transparent,
            rgba(255,255,255,0.10),
            transparent
        );
        transform: rotate(20deg);
        animation: shine 5s infinite;
    }

    @keyframes shine {
        0%   { left: -80%; opacity: 0; }
        20%  { opacity: 1; }
        50%  { opacity: 0.6; }
        100% { left: 130%; opacity: 0; }
    }

    .version-badge span {
        font-weight: 600;
        color: #ffffff;
    }

    .version-badge small {
        opacity: 0.7;
        font-weight: 500;
    }

    /* hover micro-interaction */
    .version-badge:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow:
            0 15px 40px rgba(0, 0, 0, 0.55),
            0 0 25px rgba(120, 160, 255, 0.25);
    }

    .nav-title {
        font-size: 11px;
        font-weight: 700;
        color: rgba(210, 220, 240, 0.45);
        margin: 15px 10px 5px;
        letter-spacing: 1.6px;
        text-transform: uppercase;
    }
</style>

<nav class="navbar navbar-dark navbar-theme-primary px-4 col-12 d-lg-none">
    <a class="navbar-brand me-lg-5" href="/qieos/pages/dashboard.php">
        <img
            class="navbar-brand-dark"
            src="/qieos/assets/img/brand/qieos.png"
            alt="Qieos Logo" />
        <img
            class="navbar-brand-light"
            src="/qieos/assets/img/brand/qieos.png"
            alt="Qieos Logo" />
    </a>
    <div class="d-flex align-items-center">
        <button
            class="navbar-toggler d-lg-none collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu"
            aria-controls="sidebarMenu"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<nav
    id="sidebarMenu"
    class="sidebar d-lg-block bg-gray-800 text-white collapse"
    data-simplebar>
    <div class="sidebar-inner px-4 pt-3">
        <div
            class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
            <div class="d-flex align-items-center">
                <div class="avatar-lg me-4">
                    <img
                        src="<?php echo $user['photo'] ? '/qieos/assets/img/uploads/' . $user['photo'] : '/qieos/assets/img/default-avatar.jpg'; ?>"
                        class="card-img-top rounded-circle border-white"
                        alt="Your Image" />
                </div>
                <div class="d-block">
                    <h2 class="h5 mb-3">Hi, <?php echo $user['fullname'] != '' ? $user['fullname'] : $_SESSION['email']; ?></h2>
                    <a
                        href="/qieos/sessions/logout.php"
                        class="btn btn-secondary btn-sm d-inline-flex align-items-center">
                        <svg
                            class="icon icon-xxs me-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Sign Out
                    </a>
                </div>
            </div>
            <div class="collapse-close d-md-none">
                <a
                    href="#sidebarMenu"
                    data-bs-toggle="collapse"
                    data-bs-target="#sidebarMenu"
                    aria-controls="sidebarMenu"
                    aria-expanded="true"
                    aria-label="Toggle navigation">
                    <svg
                        class="icon icon-xs"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>

        <ul class="nav flex-column pt-3 pt-md-0">
            <li class="nav-item">
                <a
                    href="/qieos/pages/dashboard.php"
                    class="nav-link d-flex align-items-center">
                    <span class="sidebar-icon">
                        <img
                            src="/qieos/assets/img/brand/qieos.png"
                            width="150"
                            alt="Qieos Logo" />
                    </span>
                </a>
            </li>

            <li class="nav-title">OVERVIEW</li>

            <li class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="/qieos/pages/dashboard.php" class="nav-link">
                    <span class="sidebar-icon">
                        <i class="fas fa-dashboard me-2"></i>
                    </span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            <?php if ($user['role'] == 'developer' || $user['role'] == 'administrator') { ?>

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

                <!-- MANAJEMEN STAFF -->
                <li class="nav-title">MANAJEMEN STAFF</li>

                <li class="nav-item <?= ($current_page == 'cashier.php') ? 'active' : ''; ?>">
                    <a href="/qieos/pages/management/cashier.php" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-users me-2"></i>
                        </span>
                        <span>Staff Kasir</span>
                    </a>
                </li>
            <?php } ?>

            <?php if ($user['role'] == 'developer' || $user['role'] == 'staff kasir') { ?>
                <!-- GUDANG PENJUALAN -->
                <li class="nav-title">PENJUALAN</li>

                <li class="nav-item <?= ($current_page == 'sales-stock.php') ? 'active' : ''; ?>">
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
            <?php } ?>

            <!-- LAPORAN -->
            <!-- <li class="nav-title">LAPORAN</li>

            <li class="nav-item <?= ($current_page == 'report-sales.php') ? 'active' : ''; ?>">
                <a href="/qieos/pages/report-sales.php" class="nav-link">
                    <span class="sidebar-icon">
                        <i class="fas fa-chart-line me-2"></i>
                    </span>
                    <span>Laporan Penjualan</span>
                </a>
            </li>

            <li class="nav-item <?= ($current_page == 'report-stock.php') ? 'active' : ''; ?>">
                <a href="/qieos/pages/report-stock.php" class="nav-link">
                    <span class="sidebar-icon">
                        <i class="fas fa-boxes me-2"></i>
                    </span>
                    <span>Laporan Stok</span>
                </a>
            </li>

            <li class="nav-item <?= ($current_page == 'report-profit.php') ? 'active' : ''; ?>">
                <a href="/qieos/pages/report-profit.php" class="nav-link">
                    <span class="sidebar-icon">
                        <i class="fas fa-coins me-2"></i>
                    </span>
                    <span>Laba Bersih</span>
                </a>
            </li> -->
        </ul>
    </div>

    <!-- Version -->
    <div class="sidebar-footer">
        <div class="version-badge">
            <span>Qieos</span>
            <small>v1.0.0</small>
        </div>
    </div>
</nav>