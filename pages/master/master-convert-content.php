<?php
error_reporting(0);
include __DIR__ . '/../../sessions/session.php';
header('Cache-Control: no-store, no-cache, must-revalidate');

// Daftar grup konversi (belum dihapus) + jumlah baris konversi
$conversions = [];
$r = mysqli_query($conn, "
    SELECT c.id, c.base_unit, c.created_at,
           (SELECT COUNT(*) FROM unit_conversion_items ci WHERE ci.conversion_id = c.id) AS total
    FROM unit_conversions c
    WHERE c.deleted_at IS NULL
    ORDER BY c.id DESC
");
if($r){
    while($row = mysqli_fetch_assoc($r)){
        $conversions[] = $row;
    }
}

// Ambil semua baris konversi sekaligus (hindari query berulang per kartu)
$itemsByConv = [];
$ri = mysqli_query($conn, "
    SELECT conversion_id, unit_name, qty
    FROM unit_conversion_items
    ORDER BY id ASC
");
if($ri){
    while($row = mysqli_fetch_assoc($ri)){
        $itemsByConv[(int)$row['conversion_id']][] = $row;
    }
}

$bulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function convertDate($ts, $bulan){
    $t = strtotime($ts);
    return date('d', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}

function convertQty($n){
    $n = (float)$n;
    $s = number_format($n, 4, ',', '.');
    $s = rtrim(rtrim($s, '0'), ',');
    return $s === '' ? '0' : $s;
}
?>

<?php if(empty($conversions)): ?>
    <div class="combine-empty">
        <div class="combine-empty-icon"><i class="fas fa-right-left"></i></div>
        <div class="combine-empty-title">Belum ada konversi</div>
        <div class="combine-empty-sub">Buat unit base lalu tambahkan konversinya. Contoh: unit base <b>pcs</b>, lalu 1 karton = 100 pcs dan 1 rtg = 10 pcs.</div>
        <button type="button" class="btn btn-combine-ghost js-add-convert">
            <i class="fas fa-plus me-1"></i> Buat Konversi Pertama
        </button>
    </div>
<?php else: ?>
    <div class="combine-grid convert-grid">
        <?php foreach($conversions as $c): ?>
            <?php $items = isset($itemsByConv[(int)$c['id']]) ? $itemsByConv[(int)$c['id']] : []; ?>
            <div class="combine-card" id="convertCard<?= (int)$c['id'] ?>">
                <div class="combine-card-top">
                    <div class="combine-avatar convert-avatar"><i class="fas fa-right-left"></i></div>
                    <div class="combine-card-meta">
                        <div class="combine-card-name">Base <span class="text-capitalize"><?= htmlspecialchars($c['base_unit']) ?></span></div>
                        <div class="combine-card-date">
                            <i class="fas fa-calendar-alt"></i> <?= convertDate($c['created_at'], $bulan) ?> &middot; <?= count($items) ?> satuan
                        </div>
                    </div>
                    <button type="button" class="combine-edit convert-edit" data-id="<?= (int)$c['id'] ?>" title="Edit konversi" aria-label="Edit konversi">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button type="button" class="combine-delete convert-delete" data-id="<?= (int)$c['id'] ?>" title="Hapus konversi" aria-label="Hapus konversi">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <div class="combine-recipe">
                    <?php foreach($items as $ix => $it): ?>
                        <div class="combine-recipe-row">
                            <span class="cb-seq"><?= $ix + 1 ?></span>
                            <span class="cb-name">1 <span class="text-capitalize"><?= htmlspecialchars($it['unit_name']) ?></span></span>
                            <span class="convert-eq">= <?= convertQty($it['qty']) ?> <span class="text-capitalize"><?= htmlspecialchars($c['base_unit']) ?></span></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="combine-total">
                    <span class="combine-total-label"><i class="fas fa-ruler-combined"></i> Unit Base</span>
                    <span class="combine-total-price text-capitalize"><?= htmlspecialchars($c['base_unit']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
