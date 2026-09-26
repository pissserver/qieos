<?php
include '../../sessions/session.php';

$query = mysqli_query($conn,
    "SELECT * FROM tenants WHERE deleted_at IS NULL ORDER BY tenant_name ASC"
);

$bulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

while ($row = mysqli_fetch_assoc($query)):
    $tgl = strtotime($row['registration_date']);
    $tanggal = date('d', $tgl);
    $bulanText = $bulan[(int)date('n', $tgl)];
    $tahun = date('Y', $tgl);
    $search = strtolower($row['tenant_name'].' '.$row['tenant_owner'].' '.$tanggal.' '.$bulanText.' '.$tahun.' '.date('Y-m-d', $tgl));
?>

<div class="product-item" data-search="<?php echo htmlspecialchars($search); ?>">
    <div class="product-card">
        <div class="product-image-wrap">
            <img src="../../assets/img/tenant-img.jpg" class="product-img" loading="lazy" decoding="async">

            <div class="card-actions">
                <button type="button" class="card-act card-act-edit"
                    onclick="openEdit(<?= $row['id'] ?>,'<?= htmlspecialchars(addslashes($row['tenant_name'])) ?>','<?= htmlspecialchars(addslashes($row['tenant_owner'])) ?>')"
                    title="Edit">
                    <i class="fas fa-pen"></i>
                </button>
                <button type="button" class="card-act card-act-delete"
                    onclick="deleteTenant(<?= $row['id'] ?>,'<?= htmlspecialchars(addslashes($row['tenant_name'])) ?>')"
                    title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>

        <div class="product-content">
            <div class="product-meta">
                <div class="category-pill">
                    <i class="fas fa-user"></i>
                    <?php echo ucfirst($row['tenant_owner']); ?>
                </div>
            </div>
            <h4 class="product-title">
                <?php echo ucwords(strtolower($row['tenant_name'])); ?>
            </h4>
            <p class="product-desc">
                <i class="fas fa-calendar text-success"></i>
                <?php echo $tanggal . ' ' . $bulanText . ' ' . $tahun; ?>
            </p>
            <a href="tenant-detail.php?id=<?php echo $row['id']; ?>" class="btn-detail">
                <span>Detail Tenant <i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </div>
</div>

<?php endwhile; ?>

<div id="empty-search" class="empty-search" style="display:none;">
    <div class="empty-icon"><i class="fas fa-search"></i></div>
    <h5>Tenant Tidak Ditemukan</h5>
    <p>Tidak ada tenant yang cocok dengan pencarian Anda.</p>
</div>
