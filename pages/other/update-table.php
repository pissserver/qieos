<?php
include '../../sessions/session.php';
?>

<div class="table-mobile">
    <table class="table table-hover align-middle" id="stockTable">
        <thead>
            <tr style="font-size:13px;color:#64748b;">
                <th>Nama Update</th>
                <th class="text-center">Version</th>
                <th class="text-center">Type</th>
                <th class="text-center">Tanggal Update</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>

        <?php
        // Dipakai di kolom Tanggal (desktop) DAN di .mp-meta (mobile),
        // jadi didefinisikan sekali di luar loop.
        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $q = mysqli_query($conn,"
        SELECT
            *
        FROM updates
        WHERE deleted_at IS NULL
        ORDER BY id DESC, update_date DESC
        ");
        while($d=mysqli_fetch_assoc($q)): ?>

        <tr class="stock-row">

            <?php
            $tgl = strtotime($d['update_date']);
            $tglLabel = date('d', $tgl) . ' ' . $bulan[(int)date('n', $tgl)] . ' ' . date('Y', $tgl);
            $jamLabel = date('h:i', $tgl);

            $typeBadge = $d['update_type'] === 'major'
                ? 'stock-danger'
                : ($d['update_type'] === 'minor' ? 'stock-success' : 'unit-badge');
            ?>

            <td>
                <div class="product-wrap">

                    <div class="avatar">
                        <i class="fas fa-rocket"></i>
                    </div>

                    <div>
                        <div class="fw-bold">
                            <?= htmlspecialchars($d['update_name']) ?>
                        </div>

                        <!-- Info yang disembunyikan di mobile (versi, tipe,
                             tanggal) dipindah ke sini supaya tidak hilang
                             waktu kolomnya disembunyikan. .mp-meta hanya
                             tampil di <=575.98px, jadi desktop tidak dobel. -->
                        <div class="mp-meta">
                            <small class="text-muted text-capitalize">
                                <i class="fas fa-code-branch me-1"></i><?= htmlspecialchars($d['update_version']) ?>
                            </small>

                            <small class="text-muted text-capitalize">
                                <i class="fas fa-layer-group me-1"></i><?= htmlspecialchars($d['update_type']) ?>
                            </small>

                            <small class="text-muted">
                                <i class="fas fa-calendar-day me-1"></i><?= $tglLabel ?> | <?= $jamLabel ?>
                            </small>
                        </div>
                    </div>

                </div>
            </td>

            <td class="text-center mp-col-version">

                <span class="stock-badge <?= $typeBadge ?> text-capitalize">

                    <i class="fas fa-code-branch me-1"></i>
                    <?= htmlspecialchars($d['update_version']) ?>

                </span>

            </td>

            <td class="text-center mp-col-type">

                <span class="stock-badge <?= $typeBadge ?> text-capitalize">

                    <i class="fas fa-layer-group me-1"></i>
                    <?= htmlspecialchars($d['update_type']) ?>
                </span>

            </td>

            <td class="text-center mp-col-date">

                <span class="unit-badge">
                    <i class="fas fa-cubes me-1"></i>
                    <?= $tglLabel ?> | <?= $jamLabel ?>
                </span>

            </td>

            <td class="text-center">
                <button class="action-btn btn-show showUpdateBtn" data-id="<?= $d['id'] ?>">
                    <i class="fas fa-eye"></i>
                </button>

                <?php if($user['role'] == 'developer') : ?>
                <button class="action-btn btn-edit editUpdateBtn" data-id="<?= $d['id'] ?>">
                    <i class="fas fa-edit"></i>
                </button>

                <button class="action-btn btn-delete deleteUpdateBtn"
                    data-id="<?= $d['id'] ?>"
                    data-name="<?= $d['update_name'] ?>"
                    data-version="<?= $d['update_version'] ?>">
                    <i class="fas fa-trash"></i>
                </button>
                <?php endif; ?>
            </td>
        </tr>

        <?php endwhile; ?>

        </tbody>
    </table>
</div>
