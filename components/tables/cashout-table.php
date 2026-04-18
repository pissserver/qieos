<?php
if (!isset($conn)) {
    include $_SERVER['DOCUMENT_ROOT'] . '/cartify/script/connection.php';
}
$active_category = isset($_GET['category']) ? $_GET['category'] : 'all';

$categories = mysqli_query($conn, "SELECT * FROM cashout_categories ORDER BY category_name ASC");

if ($active_category == 'all') {
    $query = mysqli_query($conn, "
        SELECT cashouts.*, cashout_categories.category_name 
        FROM cashouts
        LEFT JOIN cashout_categories 
        ON cashouts.category_id = cashout_categories.id
        ORDER BY cashouts.id DESC
    ");
} else {
    $query = mysqli_query($conn, "
        SELECT cashouts.*, cashout_categories.category_name 
        FROM cashouts
        LEFT JOIN cashout_categories 
        ON cashouts.category_id = cashout_categories.id
        WHERE cashouts.category_id = '$active_category'
        ORDER BY cashouts.id DESC
    ");
}

if ($active_category == 'all') {
    $totalQuery = mysqli_query($conn, "
        SELECT SUM(amount) as total 
        FROM cashouts
    ");
} else {
    $totalQuery = mysqli_query($conn, "
        SELECT SUM(amount) as total 
        FROM cashouts 
        WHERE category_id = '$active_category'
    ");
}

$totalData = mysqli_fetch_assoc($totalQuery);
?>

<style>
.card { border-radius:16px; }
.nav-tabs { border:none; gap:6px; }
.nav-tabs .nav-link {
    border:none; border-radius:10px; padding:8px 16px;
    color:#666; cursor:pointer;
}
.nav-tabs .nav-link.active {
    background:linear-gradient(45deg,#4e73df,#224abe);
    color:#fff;
}
.cash-card {
    border-radius:14px; padding:16px; background:#fff;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    transition:.2s;
}
.cash-card:hover {
    transform:translateY(-3px);
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}
.icon-box {
    width:45px;height:45px;border-radius:10px;
    background:linear-gradient(45deg,#4e73df,#224abe);
    color:#fff;display:flex;align-items:center;justify-content:center;
}
.cash-title { font-weight:600; }
.cash-meta { font-size:13px;color:#888; }
.cash-amount { font-weight:600;color:#198754; }

#pagination button {
    min-width: 35px;
    border-radius: 8px;
}
</style>

<div class="card border-0 shadow mt-3">

    <!-- HEADER -->
    <div class="card-header bg-white border-0 pb-0">

        <h5><i class="fas fa-wallet text-primary"></i> Kas Keluar</h5>

        <ul class="nav nav-tabs mt-4">
            <li class="nav-item">
                <span class="nav-link <?php echo ($active_category=='all')?'active':'';?>"
                      onclick="loadCategory('all')">Semua</span>
            </li>

            <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                <li class="nav-item">
                    <span class="nav-link <?php echo ($active_category==$cat['id'])?'active':'';?>"
                          onclick="loadCategory('<?php echo $cat['id']; ?>')">
                        <?php echo $cat['category_name']; ?>
                    </span>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="card-body">

        <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">

            <!-- SEARCH -->
            <input 
                type="text" 
                id="searchInput" 
                class="form-control w-auto" 
                placeholder="Cari..."
            >

            <!-- DATE RANGE -->
            <input 
                type="text" 
                id="dateRange" 
                class="form-control"
                style="max-width:260px"
                placeholder="Filter tanggal"
                readonly
            >

            <div id="resultCount" class="small text-muted"></div>

        </div>

        <!-- TOTAL -->
        <div class="alert alert-light border d-flex justify-content-between mb-4">
            <span>Total Pengeluaran</span>
            <b id="totalAmount" class="text-success">Rp <?php echo number_format($totalData['total'] ? $totalData['total'] : 0); ?></b>
        </div>

        <!-- EMPTY -->
        <div id="emptyState" class="text-center d-none py-5">
            <i class="fas fa-search fa-2x text-muted"></i>
            <p class="text-muted mt-2">Data tidak ditemukan</p>
        </div>

        <!-- LIST -->
        <div class="row g-3" id="cashList">
            <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                <div class="col-md-6 col-lg-6" data-date="<?php echo $row['cashout_date']; ?>" data-amount="<?php echo $row['amount']; ?>">
                    <div class="cash-card">

                        <div class="d-flex justify-content-between">

                            <div class="d-flex">
                                <div class="icon-box me-2">
                                    <i class="fas fa-receipt"></i>
                                </div>

                                <div>
                                    <div class="cash-title">
                                        <?php echo $row['expense_name']; ?>
                                    </div>
                                    <div class="cash-meta">
                                        <?php echo date('d M Y', strtotime($row['cashout_date'])); ?>
                                    </div>
                                    <div class="cash-meta">
                                        <?php echo $row['category_name'] ? $row['category_name'] : 'Tanpa Kategori'; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <div class="cash-amount">
                                    Rp <?php echo number_format($row['amount']); ?>
                                </div>
                                <small class="text-muted">
                                    <?php echo $row['quantity']." ".$row['unit']; ?>
                                </small>
                            </div>

                        </div>

                        <div class="mt-3 text-end">
                            <a href="cashout-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteData(<?php echo $row['id']; ?>, '<?php echo $row['expense_name']; ?>')" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="text-center mt-4" id="pagination"></div>

    </div>
</div>
