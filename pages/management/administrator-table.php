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
    WHERE role IN ('administrator', 'developer')
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

            <span class="stock-badge <?php echo $d['role'] === 'developer' ? 'dev-badge' : 'unit-badge'; ?> text-capitalize">
                <?php if($d['role'] === 'developer'): ?>
                    <i class="fas fa-crown me-1"></i>
                <?php else: ?>
                    <i class="fas fa-user-shield me-1"></i>
                <?php endif; ?>
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
                <button class="action-btn btn-edit editAdministratorBtn" data-id="<?= $d['id'] ?>">
                    <i class="fas fa-edit"></i>
                </button>

                <button class="action-btn btn-delete deleteAdministratorBtn"
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
