<?php
include '../../sessions/session.php';
$roleLabel  = ($user['role'] === 'staff kasir') ? 'Staff Kasir' : ucfirst($user['role']);

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// ============================================================
// DATABASE
// ============================================================
$dbDomain = array(
    'MASTER'            => array('#38bdf8', 'Data dasar / master'),
    'AKUN & CHAT'       => array('#818cf8', 'Akun & komunikasi'),
    'PURCHASING'        => array('#2dd4bf', 'Pembelian & supplier'),
    'KONVERSI UNIT'     => array('#10b981', 'Satuan & konversi'),
    'STOK'              => array('#a78bfa', 'Ledger & permintaan stok'),
    'PENJUALAN'         => array('#fbbf24', 'Transaksi penjualan'),
    'TENANT & UTILITAS' => array('#f472b6', 'Penyewa & utilitas'),
    'SISTEM'            => array('#94a3b8', 'Pengaturan & changelog'),
);

$tables = array(
    'products' => array('name' => 'products', 'domain' => 'MASTER', 'icon' => 'fa-boxes-stacked',
        'desc' => 'Master produk: kode, nama, kategori, harga jual, satuan dasar (unit), grup konversi (conversion_id), ambang stok menipis gudang & kantin, foto, status katalog, dan tanda favorit (starred). Menjadi acuan katalog, stok, dan penjualan.',
        'rels' => array(
            array('unit_conversions', 'products.conversion_id'),
            array('product_supplier', 'product_supplier.product_id'),
            array('product_combo_items', 'product_combo_items.product_id'),
            array('purchase_items', 'purchase_items.product_id'),
            array('sales_stock', 'sales_stock.product_id'),
            array('stock_request_items', 'stock_request_items.product_id'),
            array('order_details', 'order_details.product_id'),
        )),
    'product_combos' => array('name' => 'product_combos', 'domain' => 'MASTER', 'icon' => 'fa-layer-group',
        'desc' => 'Paket produk racikan berupa gabungan beberapa produk sehingga bisa dijual sebagai satu item.',
        'rels' => array(
            array('product_combo_items', 'product_combo_items.combo_id'),
            array('order_details', 'order_details.product_combo_id'),
        )),
    'product_combo_items' => array('name' => 'product_combo_items', 'domain' => 'MASTER', 'icon' => 'fa-puzzle-piece',
        'desc' => 'Detail bahan penyusun tiap paket racikan beserta jumlah dan harganya.',
        'rels' => array(
            array('products', 'product_combo_items.product_id'),
            array('product_combos', 'product_combo_items.combo_id'),
        )),
    'suppliers' => array('name' => 'suppliers', 'domain' => 'MASTER', 'icon' => 'fa-truck',
        'desc' => 'Master pemasok barang untuk kebutuhan pembelian: nama, telepon, alamat, dan catatan.',
        'rels' => array(
            array('product_supplier', 'product_supplier.supplier_id'),
        )),
    'customers' => array('name' => 'customers', 'domain' => 'MASTER', 'icon' => 'fa-users',
        'desc' => 'Master pembeli di kantin (nama & telepon). Dipakai saat checkout untuk menandai nama pesanan.',
        'rels' => array(
            array('order_details', 'order_details.customer_id'),
        )),
    'tenants' => array('name' => 'tenants', 'domain' => 'MASTER', 'icon' => 'fa-store',
        'desc' => 'Data tenant / penyewa kantin: nama tenant, pemilik, status, dan tanggal pendaftaran.',
        'rels' => array(
            array('tenant_payments', 'tenant_payments.tenant_id'),
            array('utility_payments', 'utility_payments.tenant_id'),
        )),
    'users' => array('name' => 'users', 'domain' => 'AKUN & CHAT', 'icon' => 'fa-user-shield',
        'desc' => 'Akun pengguna beserta perannya (developer, administrator, staff kasir) dan status kehadiran online.',
        'rels' => array(
            array('orders', 'orders.staff_id'),
            array('stock_requests', 'stock_requests.created_by / approved_by'),
            array('tenant_payments', 'tenant_payments.staff_id'),
            array('utility_payments', 'utility_payments.staff_id'),
            array('chat_messages', 'chat_messages.sender_id / receiver_id'),
        )),
    'chat_messages' => array('name' => 'chat_messages', 'domain' => 'AKUN & CHAT', 'icon' => 'fa-comments',
        'desc' => 'Pesan chat satu lawan satu antar pengguna, termasuk pesan marker kartu revisi pesanan.',
        'rels' => array(
            array('users', 'chat_messages.sender_id / receiver_id'),
        )),
    'purchases' => array('name' => 'purchases', 'domain' => 'PURCHASING', 'icon' => 'fa-clipboard-list',
        'desc' => 'Transaksi pembelian / daftar belanja: menampung rencana hingga realisasi pembelian barang.',
        'rels' => array(
            array('purchase_items', 'purchase_items.purchase_id'),
        )),
    'purchase_items' => array('name' => 'purchase_items', 'domain' => 'PURCHASING', 'icon' => 'fa-cart-plus',
        'desc' => 'Baris detail pembelian: produk, jumlah rencana & realisasi, supplier, nomor invoice, satuan, dan harga beli.',
        'rels' => array(
            array('purchases', 'purchase_items.purchase_id'),
            array('products', 'purchase_items.product_id'),
        )),
    'product_supplier' => array('name' => 'product_supplier', 'domain' => 'PURCHASING', 'icon' => 'fa-link',
        'desc' => 'Relasi banyak-ke-banyak produk ke supplier: satu produk dapat dibeli dari beberapa supplier.',
        'rels' => array(
            array('products', 'product_supplier.product_id'),
            array('suppliers', 'product_supplier.supplier_id'),
        )),
    'units' => array('name' => 'units', 'domain' => 'KONVERSI UNIT', 'icon' => 'fa-ruler',
        'desc' => 'Master satuan barang (pcs, karton, slop, dus, box, lusin, sachet, botol, kg, liter, dll). Dikelola di Master Unit.',
        'rels' => array()),
    'unit_conversions' => array('name' => 'unit_conversions', 'domain' => 'KONVERSI UNIT', 'icon' => 'fa-arrows-rotate',
        'desc' => 'Grup konversi satuan per produk. Tiap produk punya base_unit (products.unit) dan bisa punya beberapa satuan konversi (mis. 1 karton = 100 pcs).',
        'rels' => array(
            array('products', 'unit_conversions.product_id'),
            array('unit_conversion_items', 'unit_conversion_items.conversion_id'),
        )),
    'unit_conversion_items' => array('name' => 'unit_conversion_items', 'domain' => 'KONVERSI UNIT', 'icon' => 'fa-calculator',
        'desc' => 'Detail konversi: 1 <unit_name> = <qty> <base_unit>. Contoh: 1 karton = 100 pcs, 1 rtg = 10 pcs.',
        'rels' => array(
            array('unit_conversions', 'unit_conversion_items.conversion_id'),
        )),
    'sales_stock' => array('name' => 'sales_stock', 'domain' => 'STOK', 'icon' => 'fa-scale-balanced',
        'desc' => 'Ledger pergerakan stok per produk: saldo awal, stok transfer masuk, pengurangan karena penjualan, dan pengembalian.',
        'rels' => array(
            array('products', 'sales_stock.product_id'),
        )),
    'stock_requests' => array('name' => 'stock_requests', 'domain' => 'STOK', 'icon' => 'fa-clipboard-check',
        'desc' => 'Permintaan stok kantin ke gudang beserta status persetujuan (pending / disetujui / ditolak).',
        'rels' => array(
            array('stock_request_items', 'stock_request_items.request_id'),
            array('users', 'stock_requests.created_by / approved_by'),
        )),
    'stock_request_items' => array('name' => 'stock_request_items', 'domain' => 'STOK', 'icon' => 'fa-boxes-stacked',
        'desc' => 'Detail produk yang diminta dalam satu permintaan stok.',
        'rels' => array(
            array('stock_requests', 'stock_request_items.request_id'),
            array('products', 'stock_request_items.product_id'),
        )),
    'orders' => array('name' => 'orders', 'domain' => 'PENJUALAN', 'icon' => 'fa-receipt',
        'desc' => 'Header pesanan: kode unik, tanggal, kasir penanggung jawab, total, dan status pembayaran.',
        'rels' => array(
            array('users', 'orders.staff_id'),
            array('order_details', 'order_details.order_id'),
        )),
    'order_details' => array('name' => 'order_details', 'domain' => 'PENJUALAN', 'icon' => 'fa-list-ul',
        'desc' => 'Baris item dalam pesanan: produk atau paket racikan, jumlah, harga, dan subtotal. Opsional dikaitkan ke customer.',
        'rels' => array(
            array('orders', 'order_details.order_id'),
            array('products', 'order_details.product_id'),
            array('product_combos', 'order_details.product_combo_id'),
            array('customers', 'order_details.customer_id'),
        )),
    'tenant_payments' => array('name' => 'tenant_payments', 'domain' => 'TENANT & UTILITAS', 'icon' => 'fa-coins',
        'desc' => 'Pembayaran sewa tenant per periode beserta statusnya.',
        'rels' => array(
            array('tenants', 'tenant_payments.tenant_id'),
            array('users', 'tenant_payments.staff_id'),
        )),
    'utility_payments' => array('name' => 'utility_payments', 'domain' => 'TENANT & UTILITAS', 'icon' => 'fa-bolt',
        'desc' => 'Pembayaran utilitas (listrik / air) tenant per periode beserta statusnya.',
        'rels' => array(
            array('tenants', 'utility_payments.tenant_id'),
            array('users', 'utility_payments.staff_id'),
        )),
    'updates' => array('name' => 'updates', 'domain' => 'SISTEM', 'icon' => 'fa-rocket',
        'desc' => 'Daftar rilis / changelog aplikasi: nama, versi, tipe, dan tanggal rilis.',
        'rels' => array(
            array('update_details', 'update_details.update_id'),
        )),
    'update_details' => array('name' => 'update_details', 'domain' => 'SISTEM', 'icon' => 'fa-file-alt',
        'desc' => 'Rincian perubahan pada tiap rilis update.',
        'rels' => array(
            array('updates', 'update_details.update_id'),
        )),
);

$tableOrder = array('MASTER', 'KONVERSI UNIT', 'PURCHASING', 'STOK', 'PENJUALAN', 'TENANT & UTILITAS', 'AKUN & CHAT', 'SISTEM');
function colorOf($domain, $dbDomain) { return $dbDomain[$domain][0]; }

// ============================================================
// LAYOUT NODE DIAGRAM SVG (x, y)
// ============================================================
$NW = 230; $NH = 60;
$pos = array(
    'products'              => array(40, 64),
    'product_combos'        => array(298, 64),
    'product_combo_items'   => array(557, 64),
    'product_supplier'      => array(815, 64),
    'suppliers'             => array(1074, 64),
    'customers'             => array(1332, 64),
    'tenants'               => array(640, 192),
    'users'                 => array(1020, 192),
    'units'                 => array(40, 400),
    'unit_conversions'      => array(340, 400),
    'unit_conversion_items' => array(640, 400),
    'purchases'             => array(960, 400),
    'purchase_items'        => array(1280, 400),
    'sales_stock'           => array(60, 600),
    'stock_requests'        => array(380, 600),
    'stock_request_items'   => array(700, 600),
    'orders'                => array(380, 820),
    'order_details'         => array(700, 820),
    'tenant_payments'       => array(60, 1040),
    'utility_payments'      => array(380, 1040),
    'chat_messages'         => array(700, 1040),
    'updates'               => array(1020, 1040),
    'update_details'        => array(1340, 1040),
);
$edges = array(
    array('products', 'product_combo_items', 'product_combo_items.product_id'),
    array('product_combos', 'product_combo_items', 'product_combo_items.combo_id'),
    array('products', 'product_supplier', 'product_supplier.product_id'),
    array('suppliers', 'product_supplier', 'product_supplier.supplier_id'),
    array('products', 'unit_conversions', 'products.conversion_id'),
    array('unit_conversions', 'unit_conversion_items', 'unit_conversion_items.conversion_id'),
    array('products', 'purchase_items', 'purchase_items.product_id'),
    array('purchases', 'purchase_items', 'purchase_items.purchase_id'),
    array('products', 'sales_stock', 'sales_stock.product_id'),
    array('products', 'stock_request_items', 'stock_request_items.product_id'),
    array('stock_requests', 'stock_request_items', 'stock_request_items.request_id'),
    array('users', 'stock_requests', 'stock_requests.created_by / approved_by'),
    array('users', 'orders', 'orders.staff_id'),
    array('orders', 'order_details', 'order_details.order_id'),
    array('products', 'order_details', 'order_details.product_id'),
    array('product_combos', 'order_details', 'order_details.product_combo_id'),
    array('customers', 'order_details', 'order_details.customer_id'),
    array('tenants', 'tenant_payments', 'tenant_payments.tenant_id'),
    array('users', 'tenant_payments', 'tenant_payments.staff_id'),
    array('tenants', 'utility_payments', 'utility_payments.tenant_id'),
    array('users', 'utility_payments', 'utility_payments.staff_id'),
    array('users', 'chat_messages', 'chat_messages.sender_id / receiver_id'),
    array('updates', 'update_details', 'update_details.update_id'),
);

// ============================================================
// RENDER
// ============================================================
function renderDbCards($tableOrder, $tables, $dbDomain) {
    $out = '';
    foreach ($tableOrder as $dom) {
        $c = $dbDomain[$dom][0];
        $out .= '<div class="dbt-group">';
        $out .= '<div class="dbt-ghead"><span class="dbt-gdot" style="background:' . $c . ';box-shadow:0 0 12px ' . $c . '"></span>';
        $out .= '<div class="dbt-gtitle">' . h($dom) . '</div>';
        $out .= '<div class="dbt-gsub">' . h($dbDomain[$dom][1]) . '</div></div>';
        $out .= '<div class="dbt-grid">';
        foreach ($tables as $id => $t) {
            if ($t['domain'] !== $dom) continue;
            $out .= '<article class="dbt-card" id="db-' . $id . '" style="--td:' . $c . '">';
            $out .= '<div class="dbt-top">';
            $out .= '<div class="dbt-ico" style="background:linear-gradient(135deg,' . $c . ',' . $c . 'cc)"><i class="fas ' . $t['icon'] . '"></i></div>';
            $out .= '<div class="dbt-name"><code>' . h($id) . '</code><span class="dbt-dom">' . h($t['domain']) . '</span></div>';
            $out .= '</div>';
            $out .= '<p class="dbt-desc">' . h($t['desc']) . '</p>';
            if (!empty($t['rels'])) {
                $out .= '<div class="dbt-rels"><span class="dbt-rels-label"><i class="fas fa-link"></i> Relasi</span><div class="dbt-relswrap">';
                foreach ($t['rels'] as $r) {
                    $out .= '<a class="dbt-rel" href="#db-' . $r[0] . '"><code>' . h($r[1]) . '</code> <i class="fas fa-arrow-right"></i> <b>' . h($r[0]) . '</b></a>';
                }
                $out .= '</div></div>';
            } else {
                $out .= '<div class="dbt-rels"><span class="dbt-rels-label"><i class="fas fa-link-slash"></i> Relasi</span>';
                if ($id === 'units') {
                    $out .= '<span class="dbt-rel dbt-rel-none">Master satuan global</span></div>';
                } else {
                    $out .= '<span class="dbt-rel dbt-rel-none">Konfigurasi global</span></div>';
                }
            }
            $out .= '</article>';
        }
        $out .= '</div></div>';
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Database Schema - Qieos</title>
    <?php include '../../script/headscript.php'; ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/database-schema.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/database-schema.css'); ?>">
</head>
<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <!-- ===== HEADER HERO ===== -->
    <header class="fd-hero">
        <div class="fd-glow fd-glow-1"></div>
        <div class="fd-glow fd-glow-2"></div>
        <div class="fd-grid"></div>

        <div class="fd-toprow">
            <div class="fd-identity">
                <div class="fd-icon"><i class="fas fa-database"></i></div>
                <div>
                    <div class="fd-eyebrow">Arsitektur Aplikasi &bull; <?= h($roleLabel) ?></div>
                    <h1 class="fd-title">Database Schema</h1>
                    <p class="fd-sub">Peta struktur tabel database Qieos beserta relasinya.</p>
                </div>
            </div>
            <div class="fd-stats">
                <div class="fd-stat"><span class="fd-stat-num"><?= count($tables) ?></span><span class="fd-stat-label">Tabel</span></div>
            </div>
        </div>
    </header>

    <!-- ===== SEKSI DATABASE ===== -->
    <section class="fd-section" id="database" style="scroll-margin-top:104px;">
        <div class="fd-sec-head">
            <div class="fd-sec-ico" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)"><i class="fas fa-database"></i></div>
            <div>
                <div class="fd-sec-title">Database Schema</div>
                <div class="fd-sec-sub"><?= count($tables) ?> tabel aplikasi &mdash; fungsi tiap tabel tanpa isi datanya</div>
            </div>
            <span class="fd-sec-count"><?= count($tables) ?> tabel</span>
        </div>

        <div class="db-panel" id="dbPanel">
            <div class="db-panel-head">
                <div class="db-ph-l"><i class="fas fa-diagram-project"></i><span>Struktur Relasi Tabel</span></div>
                <div class="db-legend">
                    <?php foreach ($tableOrder as $dom) : $c = $dbDomain[$dom][0]; ?>
                        <span class="db-legend-item"><i class="fas fa-square" style="color:<?= $c ?>"></i><?= h($dom) ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="db-ph-hint"><i class="fas fa-arrow-pointer"></i> Klik tabel untuk menyorot relasinya</div>
            </div>

            <div class="db-canvas" id="dbCanvas">
                <svg viewBox="0 0 1630 1120" role="img" aria-label="Diagram relasi tabel aplikasi">
                    <defs>
                        <marker id="fdAr" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                            <path d="M0,0 L10,5 L0,10 z" class="fd-arrow"></path>
                        </marker>
                        <linearGradient id="fdCanvasBg" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#0f172a"/>
                            <stop offset="1" stop-color="#1e293b"/>
                        </linearGradient>
                    </defs>
                    <rect x="0" y="0" width="1630" height="1120" fill="url(#fdCanvasBg)"/>

                    <?php
                    foreach ($edges as $e) {
                        list($sx, $sy) = $pos[$e[0]];
                        list($tx, $ty) = $pos[$e[1]];
                        if ($tx >= $sx) { $x1 = $sx + $NW; $y1 = $sy + $NH / 2; $x2 = $tx; $y2 = $ty + $NH / 2; }
                        else            { $x1 = $sx;      $y1 = $sy + $NH / 2; $x2 = $tx + $NW; $y2 = $ty + $NH / 2; }
                        $mx = ($x1 + $x2) / 2;
                        printf('<path class="db-edge" data-s="%s" data-t="%s" marker-end="url(#fdAr)" d="M%.1f %.1f C %.1f %.1f, %.1f %.1f, %.1f %.1f">',
                            h($e[0]), h($e[1]), $x1, $y1, $mx, $y1, $mx, $y2, $x2, $y2);
                        echo '<title>' . h($e[0]) . ' &rarr; ' . h($e[2]) . '</title></path>';
                    }
                    foreach ($tables as $id => $t) {
                        if (!isset($pos[$id])) continue;
                        $c = colorOf($t['domain'], $dbDomain);
                        list($x, $y) = $pos[$id];
                        printf('<g class="db-node" data-id="%s" tabindex="0" role="button" aria-label="Tabel %s">', h($id), h($id));
                        echo '<title>' . h($id) . ' - ' . h($t['desc']) . '</title>';
                        printf('<rect class="db-node-bg" x="%d" y="%d" width="%d" height="%d" rx="15" stroke="%s"/>', $x, $y, $NW, $NH, $c);
                        printf('<rect class="db-node-strip" x="%d" y="%d" width="4" height="%d" rx="2" fill="%s"/>', $x + 10, $y + 12, $NH - 24, $c);
                        printf('<text class="db-node-name" x="%d" y="%d">%s</text>', $x + 26, $y + 24, h($id));
                        printf('<text class="db-node-dom" x="%d" y="%d">%s</text>', $x + 26, $y + 44, h($t['domain']));
                        echo '</g>';
                    }
                    ?>
                </svg>
            </div>

            <div class="db-panel-foot">
                <span><i class="fas fa-circle-info"></i> Garis = relasi kolom antar tabel &middot; &uarr; arah panah menunjuk tabel rujukan</span>
            </div>
        </div>

        <!-- inspector -->
        <div class="db-inspector" id="dbInspector" hidden>
            <div class="dbi-head">
                <div class="dbi-ico" id="dbiIco"><i class="fas fa-database"></i></div>
                <div class="dbi-title" id="dbiTitle">Tabel</div>
                <button type="button" class="dbi-close" id="dbiClose" aria-label="Tutup detail tabel"><i class="fas fa-xmark"></i></button>
            </div>
            <p class="dbi-desc" id="dbiDesc"></p>
            <div class="dbi-rels" id="dbiRels"></div>
        </div>

        <!-- Keterangan tabel (grid, semua device) -->
        <div class="fd-sec-subhead"><i class="fas fa-table"></i> Keterangan Tabel</div>
        <?php echo renderDbCards($tableOrder, $tables, $dbDomain); ?>
    </section>

    <button class="fd-top" id="fdTop" aria-label="Kembali ke atas"><i class="fas fa-arrow-up"></i></button>
</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
(function () {
    // ---------- reveal on scroll ----------
    var targets = document.querySelectorAll('.dbt-card');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
            });
        }, { rootMargin: '0px 0px -30px 0px', threshold: 0.05 });
        targets.forEach(function (t) { io.observe(t); });
    } else {
        targets.forEach(function (t) { t.classList.add('in'); });
    }

    // ---------- back to top ----------
    var scroller = document.querySelector('.content') || window;
    var topBtn = document.getElementById('fdTop');
    if (topBtn) {
        var toggle = function () {
            var top = (scroller.scrollTop !== undefined) ? scroller.scrollTop : window.pageYOffset;
            topBtn.classList.toggle('show', top > 320);
        };
        scroller.addEventListener('scroll', toggle, { passive: true });
        toggle();
        topBtn.addEventListener('click', function () {
            (scroller.scrollTo ? scroller.scrollTo({ top: 0, behavior: 'smooth' }) : window.scrollTo({ top: 0, behavior: 'smooth' }));
        });
    }

    // ---------- diagram: sorot relasi ----------
    var nodes = Array.prototype.slice.call(document.querySelectorAll('.db-node'));
    var edges = Array.prototype.slice.call(document.querySelectorAll('.db-edge'));
    var inspector = document.getElementById('dbInspector');
    var hasCanvas = nodes.length > 0;

    var tableMeta = <?php echo json_encode(array_map(function ($t) {
        return array(
            'name' => $t['name'], 'domain' => $t['domain'], 'icon' => $t['icon'],
            'desc' => $t['desc'], 'rels' => $t['rels'],
        );
    }, $tables)); ?>;

    function clearHighlight() {
        nodes.forEach(function (n) { n.classList.remove('hl'); n.classList.remove('dim'); });
        edges.forEach(function (e) { e.classList.remove('hl-edge'); });
    }

    function highlight(id) {
        clearHighlight();
        if (!id) return;
        var meta = tableMeta[id];
        if (meta) {
            inspector.hidden = false;
            document.getElementById('dbiIco').innerHTML = '<i class="fas ' + meta.icon + '"></i>';
            document.getElementById('dbiTitle').textContent = id;
            document.getElementById('dbiDesc').textContent = meta.desc;
            var relWrap = document.getElementById('dbiRels');
            relWrap.innerHTML = '';
            if (meta.rels.length) {
                relWrap.innerHTML = '<span class="dbi-rels-label">Relasi</span>';
                meta.rels.forEach(function (r) {
                    var a = document.createElement('a');
                    a.className = 'dbi-rel';
                    a.href = '#db-' + r[0];
                    a.innerHTML = '<code>' + r[1] + '</code> <i class="fas fa-arrow-right"></i> <b>' + r[0] + '</b>';
                    relWrap.appendChild(a);
                });
            } else {
                relWrap.innerHTML = '<span class="dbi-rels-label">Relasi</span><span class="dbi-rel-none">Konfigurasi global</span>';
            }
        }

        nodes.forEach(function (n) {
            if (n.getAttribute('data-id') === id) { n.classList.add('hl'); }
            else { n.classList.add('dim'); }
        });
        edges.forEach(function (e) {
            if (e.getAttribute('data-s') === id || e.getAttribute('data-t') === id) e.classList.add('hl-edge');
        });
    }

    if (hasCanvas) {
        nodes.forEach(function (n) {
            var fire = function () { highlight(n.getAttribute('data-id')); };
            n.addEventListener('click', fire);
            n.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fire(); }
            });
        });
        document.getElementById('dbiClose').addEventListener('click', function () {
            clearHighlight(); inspector.hidden = true;
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !inspector.hidden) { clearHighlight(); inspector.hidden = true; }
        });
    }
})();
</script>
</body>
</html>
