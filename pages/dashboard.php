<?php
    include '../sessions/session.php';


    $query = mysqli_query($conn, "
        SELECT 
            COUNT(customer_name) as total_customer,
            SUM(total) as total_revenue,
            MIN(tanggal) as start_order_date
        FROM orders
        WHERE status_payment != 'cancelled'
    ");

    $data = mysqli_fetch_assoc($query);

    $total_customers   = $data['total_customer'] ? $data['total_customer'] : 0;
    $total_revenue     = $data['total_revenue'] ? $data['total_revenue'] : 0;
    $start_order_date  = $data['start_order_date'] ? $data['start_order_date'] : null;

    // =========================
    // PAGINATION SETTING
    // =========================
    $limit = 5;
    $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page  = $page < 1 ? 1 : $page;

    $offset = ($page - 1) * $limit;


    // =========================
    // AMBIL SEMUA DATA (TANPA LIMIT)
    // =========================
    $monthly_query = mysqli_query($conn, "
        SELECT 
            DATE_FORMAT(tanggal, '%Y-%m') as bulan,
            DATE_FORMAT(tanggal, '%M %Y') as bulan_label,
            COUNT(customer_name) as pelanggan,
            COALESCE(SUM(total), 0) as revenue
        FROM orders
        GROUP BY YEAR(tanggal), MONTH(tanggal)
        ORDER BY YEAR(tanggal) ASC, MONTH(tanggal) ASC
    ");

    $all_data = [];
    while ($row = mysqli_fetch_assoc($monthly_query)) {
        $all_data[] = $row;
    }


    // =========================
    // HITUNG GROWTH (GLOBAL)
    // =========================
    $prev_revenue = null;

    for ($i = 0; $i < count($all_data); $i++) {
        $curr = $all_data[$i]['revenue'];

        if ($prev_revenue === null) {
            $growth = 0;
        } else {
            $growth = ($prev_revenue > 0)
                ? (($curr - $prev_revenue) / $prev_revenue) * 100
                : 0;
        }

        $all_data[$i]['growth'] = $growth;
        $prev_revenue = $curr;
    }


    // =========================
    // BALIK JADI DESC (TERBARU DI ATAS)
    // =========================
    $all_data = array_reverse($all_data);


    // =========================
    // PAGINATION DARI DATA GLOBAL
    // =========================
    $total_data  = count($all_data);
    $total_pages = ceil($total_data / $limit);

    $monthly_data = array_slice($all_data, $offset, $limit);

    // Top Customers
    $top_customers_query = mysqli_query($conn, "
        SELECT 
            customer_name,
            COUNT(*) as total_orders,
            COALESCE(SUM(total), 0) as total_spent
        FROM orders
        GROUP BY customer_name
        ORDER BY total_orders DESC
        LIMIT 5
    ");

    $top_customers = [];
    while ($row = mysqli_fetch_assoc($top_customers_query)) {
        $top_customers[] = $row;
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Primary Meta Tags -->
        <title>Dashboard - Cartify</title>

        <?php include '../script/headscript.php'; ?>
    </head>

    <body>
        <?php include '../components/sidebar.php'; ?>

        <main class="content">
            <?php include '../components/navbar.php'; ?>
            
            <div class="row mt-5">
                <div class="col-12 col-sm-6 col-xl-6 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <div
                                class="row d-block d-xl-flex align-items-center"
                            >
                                <div
                                    class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center"
                                >
                                    <div
                                        class="icon-shape icon-shape-primary rounded me-4 me-sm-0"
                                    >
                                        <svg
                                            class="icon"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div class="d-sm-none">
                                        <h2 class="h5">Pelanggan</h2>
                                        <h3 class="fw-extrabold mb-1">
                                            <?php echo $total_customers; ?> Orang
                                        </h3>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-7 px-xl-0">
                                    <div class="d-none d-sm-block">
                                        <h2 class="h6 text-gray-400 mb-0">
                                            Pelanggan
                                        </h2>
                                        <h3 class="fw-extrabold mb-2">
                                            <?php echo $total_customers; ?> Orang
                                        </h3>
                                    </div>
                                    <small
                                        class="d-flex align-items-center text-gray-500"
                                    >
                                        Sejak <?php echo date('F j Y', strtotime($start_order_date)); ?>,
                                        <svg
                                            class="icon icon-xxs text-gray-500 ms-2 me-1"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z"
                                                clip-rule="evenodd"
                                            ></path>
                                        </svg>
                                        <?php echo $user['city']; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-6 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <div
                                class="row d-block d-xl-flex align-items-center"
                            >
                                <div
                                    class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center"
                                >
                                    <div
                                        class="icon-shape icon-shape-secondary rounded me-4 me-sm-0"
                                    >
                                        <svg 
                                            class="icon"
                                            xmlns="http://www.w3.org/2000/svg" 
                                            viewBox="0 0 24 24" 
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <rect x="2" y="6" width="20" height="12" rx="3"></rect>
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M6 12h0"></path>
                                            <path d="M18 12h0"></path>
                                        </svg>
                                    </div>
                                    <div class="d-sm-none">
                                        <h2 class="fw-extrabold h5">Revenue</h2>
                                        <h3 class="mb-1"><?php echo 'Rp ' . number_format($total_revenue); ?></h3>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-7 px-xl-0">
                                    <div class="d-none d-sm-block">
                                        <h2 class="h6 text-gray-400 mb-0">
                                            Revenue
                                        </h2>
                                        <h3 class="fw-extrabold mb-2">
                                            <?php echo 'Rp ' . number_format($total_revenue); ?>
                                        </h3>
                                    </div>
                                    <small
                                        class="d-flex align-items-center text-gray-500"
                                    >
                                        Sejak <?php echo date('F j Y', strtotime($start_order_date)); ?>,
                                        <svg
                                            class="icon icon-xxs text-gray-500 ms-2 me-1"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z"
                                                clip-rule="evenodd"
                                            ></path>
                                        </svg>
                                        <?php echo $user['city']; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-xl-12">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card border-0 shadow">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h2 class="fs-5 fw-bold mb-0">
                                                Monthly Revenue
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table
                                        class="table align-items-center table-flush"
                                    >
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="text-align: center;"
                                                    class="border-bottom"
                                                    scope="col"
                                                >
                                                    Bulan
                                                </th>
                                                <th style="text-align: center;"
                                                    class="border-bottom"
                                                    scope="col"
                                                >
                                                    Pelanggan
                                                </th>
                                                <th style="text-align: center;"
                                                    class="border-bottom"
                                                    scope="col"
                                                >
                                                    Revenue
                                                </th>
                                                <th style="text-align: center;"
                                                    class="border-bottom"
                                                    scope="col"
                                                >
                                                    Perubahan
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($monthly_data as $row): ?>
                                                <tr>
                                                    <td ><?= $row['bulan_label'] ?></td>
                                                    <td style="text-align: center;"><?= $row['pelanggan'] ?> Orang</td>
                                                    <td class="text-warning fw-bold" style="text-align: center;">
                                                        Rp <?= number_format($row['revenue'], 0, ',', '.') ?>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <?php if ($row['growth'] >= 0): ?>
                                                                <!-- UP -->
                                                                <svg class="icon icon-xs text-success me-2" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                <span class="text-success fw-bold">
                                                                    <?= number_format($row['growth'], 2) ?>%
                                                                </span>
                                                            <?php else: ?>
                                                                <!-- DOWN -->
                                                                <svg class="icon icon-xs text-danger me-2" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                <span class="text-danger fw-bold">
                                                                    <?= number_format(abs($row['growth']), 2) ?>%
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <div class="card-footer px-3 border-0 d-flex justify-content-between align-items-center">
                                        <span class="small text-gray-500">
                                            Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                                        </span>

                                        <ul class="pagination mb-0">

                                            <!-- PREV -->
                                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $page-1; ?>">‹</a>
                                            </li>

                                            <?php
                                            $range = 2; // jumlah halaman di sekitar current

                                            for ($i = 1; $i <= $total_pages; $i++) {

                                                if (
                                                    $i == 1 || 
                                                    $i == $total_pages || 
                                                    ($i >= $page - $range && $i <= $page + $range)
                                                ) {
                                            ?>
                                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                                        <a class="page-link" href="?page=<?php echo $i; ?>">
                                                            <?php echo $i; ?>
                                                        </a>
                                                    </li>
                                            <?php
                                                } 
                                                elseif (
                                                    $i == $page - $range - 1 || 
                                                    $i == $page + $range + 1
                                                ) {
                                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                }
                                            }
                                            ?>

                                            <!-- NEXT -->
                                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $page+1; ?>">›</a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xxl-12 mb-4">
                            <div class="card border-0 shadow">
                                <div
                                    class="card-header border-bottom d-flex align-items-center justify-content-between"
                                >
                                    <h2 class="fs-5 fw-bold mb-0">
                                        Pelanggan Teratas
                                    </h2>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush list my--3">
                                        <?php foreach ($top_customers as $customer): ?>
                                            <li class="list-group-item px-0">
                                                <div class="row align-items-center">
                                                    
                                                    <!-- Avatar (auto generate dari huruf nama) -->
                                                    <div class="col-auto">
                                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width:40px;height:40px;font-weight:bold;">
                                                            <?= strtoupper(substr($customer['customer_name'], 0, 1)) ?>
                                                        </div>
                                                    </div>

                                                    <!-- Nama + info -->
                                                    <div class="col-auto ms--2">
                                                        <h4 class="h6 mb-0">
                                                            <?= htmlspecialchars($customer['customer_name']) ?>
                                                        </h4>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-success dot rounded-circle me-1"></div>
                                                            <small><?= $customer['total_orders'] ?> orders</small>
                                                        </div>
                                                    </div>

                                                    <!-- Total pembelian -->
                                                    <div class="col text-end">
                                                        <span class="fw-bold text-warning">
                                                            Rp <?= number_format($customer['total_spent'], 0, ',', '.') ?>
                                                        </span>
                                                    </div>

                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include "../script/footscript.php"; ?>
    </body>
</html>
