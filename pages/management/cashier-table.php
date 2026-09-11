<?php
include '../../sessions/session.php';
?>
<table class="table table-hover align-middle" id="stockTable">
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
    $q = mysqli_query($conn,"
    SELECT
        *
    FROM users
    WHERE role = 'staff kasir'
    ORDER BY fullname ASC
    ");
    while($d=mysqli_fetch_assoc($q)): ?>

    <tr class="stock-row">

        <td>
            <div class="product-wrap">

                <?php if(!empty($d['photo'])): ?>
                    <img class="avatar-photo"
                        src="<?php echo BASE_URL; ?>/assets/img/uploads/<?= htmlspecialchars($d['photo']) ?>"
                        alt="<?= htmlspecialchars($d['fullname']) ?>">
                <?php else: ?>
                    <div class="avatar">
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

            <span class="stock-badge unit-badge text-capitalize">
                <i class="fas fa-id-badge me-1"></i>
                <?= htmlspecialchars($d['role']) ?>
            </span>

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
            <button class="action-btn btn-edit editStaffBtn" data-id="<?= $d['id'] ?>">
                <i class="fas fa-edit"></i>
            </button>

            <button class="action-btn btn-delete deleteStaffBtn"
                data-id="<?= $d['id'] ?>"
                data-fullname="<?= $d['fullname'] ?>">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>
