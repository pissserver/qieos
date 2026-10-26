<?php
error_reporting(0);
include __DIR__ . '/../../sessions/session.php';
header('Cache-Control: no-store, no-cache, must-revalidate');

// Daftar racikan (belum dihapus) beserta total harga
$combos = [];
$r = mysqli_query($conn, "
    SELECT c.id, c.name, c.created_at,
           COALESCE(SUM(ci.price * ci.qty), 0) AS total
    FROM product_combos c
    LEFT JOIN product_combo_items ci ON ci.combo_id = c.id
    WHERE c.deleted_at IS NULL
    GROUP BY c.id
    ORDER BY c.id DESC
");
if($r){
    while($row = mysqli_fetch_assoc($r)){
        $combos[] = $row;
    }
}

// Helper format rupiah
function comboRp($n){
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
}

$bulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function comboDate($ts, $bulan){
    $t = strtotime($ts);
    return date('d', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}

function comboInitials($name){
    $words = preg_split('/\s+/', trim($name));
    $init = '';
    foreach(array_slice($words, 0, 2) as $w){
        if($w !== '') $init .= mb_strtoupper(mb_substr($w, 0, 1));
    }
    return $init !== '' ? $init : 'RC';
}
?>

<?php if(empty($combos)): ?>
    <div class="combine-empty">
        <div class="combine-empty-icon"><i class="fas fa-blender"></i></div>
        <div class="combine-empty-title">Belum ada racikan</div>
        <div class="combine-empty-sub">Gabungkan beberapa produk jadi satu paket. Contoh: Kopi Bubuk + Susu + Gula.</div>
        <button type="button" class="btn btn-combine-ghost js-add-combine">
            <i class="fas fa-plus me-1"></i> Buat Racikan Pertama
        </button>
    </div>
<?php else: ?>
    <div class="combine-grid">
        <?php foreach($combos as $c): ?>
            <?php
                $items = [];
                $qi = mysqli_query($conn, "
                    SELECT ci.qty, ci.price, p.id AS product_id, p.name AS product_name,
                           p.code, COALESCE(p.unit, '') AS unit, COALESCE(p.photo, '') AS photo
                    FROM product_combo_items ci
                    INNER JOIN products p ON p.id = ci.product_id
                    WHERE ci.combo_id = {$c['id']}
                    ORDER BY ci.id ASC
                ");
                if($qi){
                    while($row = mysqli_fetch_assoc($qi)){ $items[] = $row; }
                }
            ?>
            <div class="combine-card" id="combineCard<?= (int)$c['id'] ?>">
                <div class="combine-card-top">
                    <div class="combine-avatar"><?= htmlspecialchars(comboInitials($c['name'])) ?></div>
                    <div class="combine-card-meta">
                        <div class="combine-card-name"><?= htmlspecialchars($c['name']) ?></div>
                        <div class="combine-card-date">
                            <i class="fas fa-calendar-alt"></i> <?= comboDate($c['created_at'], $bulan) ?> &middot; <?= count($items) ?> bahan
                        </div>
                    </div>
                    <button type="button" class="combine-edit" data-id="<?= (int)$c['id'] ?>" title="Edit racikan" aria-label="Edit racikan">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button type="button" class="combine-delete" data-id="<?= (int)$c['id'] ?>" title="Hapus racikan" aria-label="Hapus racikan">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <div class="combine-recipe">
                    <?php foreach($items as $ix => $it): ?>
                        <div class="combine-recipe-row">
                            <span class="cb-seq"><?= $ix + 1 ?></span>
                            <span class="cb-product">
                                <?php if(!empty($it['photo'])):
                                    $src = BASE_URL . '/assets/img/products/' . htmlspecialchars($it['photo']); ?>
                                    <img src="<?= $src ?>" alt="" loading="lazy" class="cb-thumb">
                                <?php else: ?>
                                    <span class="cb-thumb cb-thumb-ph"><i class="fas fa-box"></i></span>
                                <?php endif; ?>
                                <span class="cb-name">
                                    <?= htmlspecialchars($it['product_name']) ?>
                                    <?php if(!empty($it['code'])): ?><small><?= htmlspecialchars($it['code']) ?></small><?php endif; ?>
                                </span>
                            </span>
                            <?php if((int)$it['qty'] > 1): ?><span class="cb-qty">&times;<?= (int)$it['qty'] ?></span><?php endif; ?>
                            <span class="cb-price"><?= comboRp($it['price']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="combine-total">
                    <span class="combine-total-label"><i class="fas fa-coins"></i> Total Harga</span>
                    <span class="combine-total-price"><?= comboRp($c['total']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>