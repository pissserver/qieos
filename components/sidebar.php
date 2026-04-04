<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar-footer {
        padding: 15px;
        text-align: center;
        position: relative;
    }

    /* garis glowing tipis */
    .sidebar-footer::before {
        content: '';
        display: block;
        height: 1px;
        width: 60%;
        margin: 0 auto 10px;
        background: linear-gradient(90deg, transparent, #4b6cb7, transparent);
        animation: glowLine 2s infinite linear;
    }

    @keyframes glowLine {
        0% {opacity: 0.3;}
        50% {opacity: 1;}
        100% {opacity: 0.3;}
    }

    /* badge version */
    .version-badge {
        display: inline-block;
        background: linear-gradient(135deg, #4b6cb7, #182848);
        padding: 6px 14px;
        border-radius: 20px;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        animation: floaty 3s ease-in-out infinite;
    }

    /* animasi floating halus */
    @keyframes floaty {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-3px); }
        100% { transform: translateY(0px); }
    }

    .version-badge span {
        font-weight: 600;
        margin-right: 5px;
    }

    .version-badge small {
        opacity: 0.8;
    }

    .version-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
</style>

<nav class="navbar navbar-dark navbar-theme-primary px-4 col-12 d-lg-none">
    <a class="navbar-brand me-lg-5" href="../pages/dashboard.php">
        <img
            class="navbar-brand-dark"
            src="../assets/img/brand/cartify.png"
            alt="Cartify Logo"
        />
        <img
            class="navbar-brand-light"
            src="../assets/img/brand/cartify.png"
            alt="Cartify Logo"
        />
    </a>
    <div class="d-flex align-items-center">
        <button
            class="navbar-toggler d-lg-none collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu"
            aria-controls="sidebarMenu"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<nav
    id="sidebarMenu"
    class="sidebar d-lg-block bg-gray-800 text-white collapse"
    data-simplebar
>
    <div class="sidebar-inner px-4 pt-3">
        <div
            class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
            <div class="d-flex align-items-center">
                <div class="avatar-lg me-4">
                    <img
                        src="<?php echo $user['photo'] ? $user['photo'] : '../assets/img/default-avatar.jpg'; ?>"
                        class="card-img-top rounded-circle border-white"
                        alt="Bonnie Green"
                            />
                </div>
                <div class="d-block">
                    <h2 class="h5 mb-3">Hi, <?php echo $user['fullname'] != '' ? $user['fullname'] : $_SESSION['email']; ?></h2>
                    <a
                        href="../sessions/logout.php"
                        class="btn btn-secondary btn-sm d-inline-flex align-items-center"
                    >
                        <svg
                            class="icon icon-xxs me-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                            ></path>
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
                    aria-label="Toggle navigation"
                >
                    <svg
                        class="icon icon-xs"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        ></path>
                    </svg>
                </a>
            </div>
        </div>
        <ul class="nav flex-column pt-3 pt-md-0">
            <li class="nav-item">
                <a
                    href="../pages/dashboard.php"
                    class="nav-link d-flex align-items-center"
                >
                    <span class="sidebar-icon">
                        <img
                            src="../assets/img/brand/cartify.png"
                            width="150"
                            alt="Cartify Logo"
                        />
                    </span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="../pages/dashboard.php" class="nav-link">
                    <span class="sidebar-icon">
                        <svg
                            class="icon icon-xs me-2"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"
                            ></path>
                            <path
                                d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"
                            ></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'catalog.php') ? 'active' : ''; ?>">
                <a href="../pages/catalog.php" class="nav-link">
                    <span class="sidebar-icon">
                        <svg class="icon icon-xs me-2" 
                            fill="currentColor" 
                            viewBox="0 0 20 20" 
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 3h4v4H4V3zm0 6h4v4H4V9zm6-6h4v4h-4V3zm0 6h4v4h-4V9z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Katalog</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'order.php') ? 'active' : ''; ?>">
                <a href="../pages/order.php" class="nav-link">
                    <span class="sidebar-icon">
                        <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 14h6M9 10h6M5 3h14a1 1 0 011 1v17l-3-2-3 2-3-2-3 2-3-2V4a1 1 0 011-1z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Pesanan</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'product-add.php' || $current_page == 'product-edit.php') ? 'active' : ''; ?>">
                <a href="../pages/product-add.php" class="nav-link">
                    <span class="sidebar-icon">
                        <svg class="icon icon-xs me-2"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">

                            <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2
                            0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"></path>

                        </svg>
                    </span>
                    <span class="sidebar-text">Tambah Produk</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'report.php') ? 'active' : ''; ?>">
                <a href="../pages/report.php" class="nav-link">
                    <span class="sidebar-icon">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm3.707 1.707a1 1 0 00-1.414-1.414l-3
                            3a1 1 0 001.414 1.414l3-3zM11
                            7h4a1 1 0 110
                            2h-4a1 1
                            0
                            110-2zm0
                            4h4a1
                            1
                            0
                            110
                            2h-4a1
                            1
                            0
                            110-2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Laporan</span>
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Version -->
    <div class="sidebar-footer">
        <div class="version-badge">
            <span>Cartify</span>
            <small>v1.0.0</small>
        </div>
    </div>
</nav>
