<?php
include '../../sessions/session.php';

$role = isset($_GET['role']) ? $_GET['role'] : 'administrator';

// Hitung jumlah untuk badge tab
$q_admin = mysqli_query($conn, "SELECT COUNT(*) c FROM users WHERE role IN ('administrator','developer')");
$q_cashier = mysqli_query($conn, "SELECT COUNT(*) c FROM users WHERE role = 'staff kasir'");
$count_admin = $q_admin ? (int)mysqli_fetch_assoc($q_admin)['c'] : 0;
$count_cashier = $q_cashier ? (int)mysqli_fetch_assoc($q_cashier)['c'] : 0;
?>
<table class="table table-hover align-middle" id="userTable"
    data-count-admin="<?= $count_admin ?>"
    data-count-cashier="<?= $count_cashier ?>">
    <thead>
        <tr style="font-size:13px;color:#64748b;">
            <th>Nama</th>
            <th class="text-center">Role</th>
            <th class="text-center">Terbuat</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>

    <?php
    if ($role === 'cashier'):
        $q = mysqli_query($conn,"
        SELECT *
        FROM users
        WHERE role = 'staff kasir'
        ORDER BY fullname ASC
        ");
    else:
        $q = mysqli_query($conn,"
        SELECT *
        FROM users
        WHERE role IN ('administrator', 'developer')
        ORDER BY fullname ASC
        ");
    endif;

    while($d=mysqli_fetch_assoc($q)): ?>

    <tr class="stock-row">

        <td>
            <div class="product-wrap">

                <?php if(!empty($d['photo'])): ?>
                    <img class="avatar-photo"
                        src="<?php echo BASE_URL; ?>/assets/img/uploads/<?= htmlspecialchars($d['photo']) ?>"
                        alt="<?= htmlspecialchars($d['fullname']) ?>">
                <?php else: ?>
                    <div class="avatar avatar-<?= $role === 'cashier' ? 'cashier' : 'admin' ?>">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>

                <div>
                    <div class="fw-bold">
                        <?= htmlspecialchars($d['fullname']) ?>
                    </div>

                    <small class="text-muted text-capitalize">
                        <?= htmlspecialchars($d['username']) ?>
                    </small>
                </div>

            </div>
        </td>

        <td class="text-center">

            <?php if($d['role'] === 'developer'): ?>
                <span class="stock-badge dev-badge text-capitalize">
                    <i class="fas fa-crown me-1"></i>
                    <?= htmlspecialchars($d['role']) ?>
                </span>
            <?php elseif($d['role'] === 'administrator'): ?>
                <span class="stock-badge adm-badge text-capitalize">
                    <i class="fas fa-user-shield me-1"></i>
                    <?= htmlspecialchars($d['role']) ?>
                </span>
            <?php else: ?>
                <span class="stock-badge csr-badge text-capitalize">
                    <i class="fas fa-user-tie me-1"></i>
                    <?= htmlspecialchars($d['role']) ?>
                </span>
            <?php endif; ?>

        </td>

        <td class="text-center">

            <span class="unit-badge">
                <i class="fas fa-cubes me-1"></i>
                <?php
                $bulan = [
                    1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
                ];

                $tgl = strtotime($d['created_at']);
                echo date('d', $tgl) . ' ' . $bulan[(int)date('n', $tgl)] . ' ' . date('Y', $tgl);
                ?>
            </span>

        </td>

        <td class="text-center">
            <?php if($d['role'] === 'developer'): ?>
                <span class="text-muted" style="font-size:13px;">
                    <i class="fas fa-lock me-1"></i>
                    Tidak dapat diubah
                </span>
            <?php elseif($d['username'] === $_SESSION['username']): ?>
                <span class="text-muted" style="font-size:13px;">
                    <i class="fas fa-lock me-1"></i>
                    Anda
                </span>
            <?php else: ?>
                <button class="action-btn btn-edit editUserBtn" data-id="<?= $d['id'] ?>">
                    <i class="fas fa-edit"></i>
                </button>

                <button class="action-btn btn-delete deleteUserBtn"
                    data-id="<?= $d['id'] ?>"
                    data-fullname="<?= $d['fullname'] ?>">
                    <i class="fas fa-trash"></i>
                </button>
            <?php endif; ?>
        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>