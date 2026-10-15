<?php
include '../../sessions/session.php';
$role = $user['role'];
$base = BASE_URL;
$roleLabel = ($role === 'staff kasir') ? 'Staff Kasir' : ucfirst($role);

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Flow aplikasi (ringkas) — link halaman mengikuti role (seperti guide.php)
$pages = array(
    'master-product'   => array('Master Produk', 'pages/master/master-product.php'),
    'master-unit'      => array('Master Unit', 'pages/master/master-unit.php'),
    'restock'          => array('Restock', 'pages/stock/restock.php'),
    'stok-gudang'      => array('Stok Gudang', 'pages/stock/stock.php'),
    'transfer'         => array('Transfer', 'pages/stock/transfer.php'),
    'stok-kantin'      => array('Stok Kantin', 'pages/sales/sales-stock.php'),
    'katalog'          => array('Katalog', 'pages/sales/catalog.php'),
    'pesanan'          => array('Pesanan', 'pages/sales/order.php'),
    'tenant'           => array('Tenant', 'pages/tenant/tenant.php'),
    'rekap'            => array('Rekap', 'pages/recap/recap.php'),
);
$rolePages = array(
    'developer'     => array('master-product', 'master-unit', 'restock', 'stok-gudang', 'transfer', 'stok-kantin', 'katalog', 'pesanan', 'tenant', 'rekap'),
    'administrator' => array('restock', 'stok-gudang', 'transfer', 'tenant', 'rekap'),
    'staff kasir'   => array('stok-kantin', 'katalog', 'pesanan', 'tenant', 'rekap'),
);
$myPages = isset($rolePages[$role]) ? $rolePages[$role] : array();

$phases = array(
    array(
        'id' => 'flow-master', 'icon' => 'fa-cubes-stacked', 'title' => 'Master Data',
        'desc' => 'Data dasar sistem: produk, supplier, customer, user, tenant, unit satuan.',
        'color' => '#0ea5e9',
        'tables' => array('products', 'suppliers', 'customers', 'users', 'tenants', 'units', 'unit_conversions', 'unit_conversion_items', 'product_combos', 'product_combo_items', 'product_supplier'),
        'pages' => array('master-product', 'master-unit'),
    ),
    array(
        'id' => 'flow-purchasing', 'icon' => 'fa-cart-shopping', 'title' => 'Purchasing',
        'desc' => 'Daftar belanja & pembelian barang dari supplier.',
        'color' => '#14b8a6',
        'tables' => array('purchases', 'purchase_items'),
        'pages' => array('restock'),
    ),
    array(
        'id' => 'flow-stock', 'icon' => 'fa-warehouse', 'title' => 'Stok & Transfer',
        'desc' => 'Ledger stok gudang/kantin, transfer antar lokasi, request pengisian.',
        'color' => '#8b5cf6',
        'tables' => array('sales_stock', 'stock_requests', 'stock_request_items'),
        'pages' => array('stok-gudang', 'transfer', 'stok-kantin'),
    ),
    array(
        'id' => 'flow-sales', 'icon' => 'fa-store', 'title' => 'Penjualan',
        'desc' => 'Katalog, keranjang, checkout pesanan, dan pengelolaan pesanan.',
        'color' => '#f59e0b',
        'tables' => array('orders', 'order_details'),
        'pages' => array('katalog', 'pesanan'),
    ),
    array(
        'id' => 'flow-tenant', 'icon' => 'fa-building', 'title' => 'Tenant',
        'desc' => 'Kelola tenant penyewa kantin & pembayaran sewa/utilitas.',
        'color' => '#ec4899',
        'tables' => array('tenants', 'tenant_payments', 'utility_payments'),
        'pages' => array('tenant', 'rekap'),
    ),
    array(
        'id' => 'flow-system', 'icon' => 'fa-cog', 'title' => 'Sistem',
        'desc' => 'Pengaturan global, chat, dan changelog aplikasi.',
        'color' => '#64748b',
        'tables' => array('app_settings', 'chat_messages', 'updates', 'update_details'),
        'pages' => array(),
    ),
);

// Database: 25 tabel
$tables = array(
    'products' => array(
        'domain' => 'MASTER', 'icon' => 'fa-boxes-stacked', 'color' => '#0ea5e9',
        'desc' => 'Master produk: kode, nama, kategori, harga jual, unit, low stock, foto.',
    ),
    'suppliers' => array(
        'domain' => 'MASTER', 'icon' => 'fa-truck', 'color' => '#0ea5e9',
        'desc' => 'Master supplier/pemasok barang.',
    ),
    'customers' => array(
        'domain' => 'MASTER', 'icon' => 'fa-users', 'color' => '#0ea5e9',
        'desc' => 'Master customer pembeli di kantin.',
    ),
    'users' => array(
        'domain' => 'MASTER', 'icon' => 'fa-user-shield', 'color' => '#0ea5e9',
        'desc' => 'Akun pengguna: developer, administrator, staff kasir.',
    ),
    'tenants' => array(
        'domain' => 'MASTER', 'icon' => 'fa-store', 'color' => '#0ea5e9',
        'desc' => 'Data tenant penyewa kantin.',
    ),
    'units' => array(
        'domain' => 'MASTER', 'icon' => 'fa-ruler-combined', 'color' => '#0ea5e9',
        'desc' => 'Master satuan barang (pcs, karton, slop, dll).',
    ),
    'unit_conversions' => array(
        'domain' => 'MASTER', 'icon' => 'fa-right-left', 'color' => '#0ea5e9',
        'desc' => 'Konversi unit per produk: unit base + daftar konversi.',
    ),
    'unit_conversion_items' => array(
        'domain' => 'MASTER', 'icon' => 'fa-list-ul', 'color' => '#0ea5e9',
        'desc' => 'Detail konversi per produk: unit + qty per unit base.',
    ),
    'product_combos' => array(
        'domain' => 'MASTER', 'icon' => 'fa-layer-group', 'color' => '#0ea5e9',
        'desc' => 'Paket racikan produk.',
    ),
    'product_combo_items' => array(
        'domain' => 'MASTER', 'icon' => 'fa-puzzle-piece', 'color' => '#0ea5e9',
        'desc' => 'Bahan penyusun paket racikan.',
    ),
    'product_supplier' => array(
        'domain' => 'MASTER', 'icon' => 'fa-link', 'color' => '#0ea5e9',
        'desc' => 'Relasi produk ke supplier (many-to-many).',
    ),
    'purchases' => array(
        'domain' => 'PURCHASING', 'icon' => 'fa-clipboard-list', 'color' => '#14b8a6',
        'desc' => 'Transaksi pembelian / daftar belanja.',
    ),
    'purchase_items' => array(
        'domain' => 'PURCHASING', 'icon' => 'fa-cart-plus', 'color' => '#14b8a6',
        'desc' => 'Detail item pembelian: qty, unit, harga.',
    ),
    'sales_stock' => array(
        'domain' => 'STOK', 'icon' => 'fa-scale-balanced', 'color' => '#8b5cf6',
        'desc' => 'Ledger stok: saldo, transfer, penjualan, retur.',
    ),
    'stock_requests' => array(
        'domain' => 'STOK', 'icon' => 'fa-clipboard-check', 'color' => '#8b5cf6',
        'desc' => 'Permintaan stok kantin ke gudang.',
    ),
    'stock_request_items' => array(
        'domain' => 'STOK', 'icon' => 'fa-boxes-stacked', 'color' => '#8b5cf6',
        'desc' => 'Detail produk dalam permintaan stok.',
    ),
    'orders' => array(
        'domain' => 'PENJUALAN', 'icon' => 'fa-receipt', 'color' => '#f59e0b',
        'desc' => 'Header pesanan: kode, tanggal, kasir, total, status bayar.',
    ),
    'order_details' => array(
        'domain' => 'PENJUALAN', 'icon' => 'fa-list-ul', 'color' => '#f59e0b',
        'desc' => 'Item pesanan: produk/paket, qty, harga, customer.',
    ),
    'tenant_payments' => array(
        'domain' => 'TENANT', 'icon' => 'fa-coins', 'color' => '#ec4899',
        'desc' => 'Pembayaran sewa tenant per periode.',
    ),
    'utility_payments' => array(
        'domain' => 'TENANT', 'icon' => 'fa-bolt', 'color' => '#ec4899',
        'desc' => 'Pembayaran utilitas (listrik/air) tenant.',
    ),
    'app_settings' => array(
        'domain' => 'SISTEM', 'icon' => 'fa-sliders', 'color' => '#64748b',
        'desc' => 'Pengaturan global (key-value).',
    ),
    'chat_messages' => array(
        'domain' => 'SISTEM', 'icon' => 'fa-comments', 'color' => '#64748b',
        'desc' => 'Pesan chat antar pengguna.',
    ),
    'updates' => array(
        'domain' => 'SISTEM', 'icon' => 'fa-rocket', 'color' => '#64748b',
        'desc' => 'Riwayat rilis aplikasi.',
    ),
    'update_details' => array(
        'domain' => 'SISTEM', 'icon' => 'fa-file-alt', 'color' => '#64748b',
        'desc' => 'Detail perubahan per rilis.',
    ),
    'schema_migrations' => array(
        'domain' => 'SISTEM', 'icon' => 'fa-database', 'color' => '#64748b',
        'desc' => 'Log migrasi database (internal).',
    ),
);

$domains = array(
    'MASTER' => array('color' => '#0ea5e9', 'label' => 'Data Master'),
    'PURCHASING' => array('color' => '#14b8a6', 'label' => 'Purchasing'),
    'STOK' => array('color' => '#8b5cf6', 'label' => 'Stok & Transfer'),
    'PENJUALAN' => array('color' => '#f59e0b', 'label' => 'Penjualan'),
    'TENANT' => array('color' => '#ec4899', 'label' => 'Tenant'),
    'SISTEM' => array('color' => '#64748b', 'label' => 'Sistem'),
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flow &amp; Database - Qieos</title>
    <?php include '../../script/headscript.php'; ?>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/pages/flow-database.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/flow-database.css'); ?>">
</head>
<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <!-- HERO -->
    <header class="fd-hero">
        <div class="fd-glow fd-glow-1"></div>
        <div class="fd-glow fd-glow-2"></div>
        <div class="fd-grid"></div>

        <div class="fd-toprow">
            <div class="fd-identity">
                <div class="fd-icon"><i class="fas fa-sitemap"></i></div>
                <div>
                    <div class="fd-eyebrow">Arsitektur Aplikasi &bull; <?= h($roleLabel) ?></div>
                    <h1 class="fd-title">Flow &amp; Database</h1>
                    <p class="fd-sub">Alur kerja Qieos dari master data sampai penjualan, plus peta 25 tabel database.</p>
                </div>
            </div>
            <div class="fd-stats">
                <div class="fd-stat"><span class="fd-stat-num"><?= count($phases) ?></span><span class="fd-stat-label">Tahap</span></div>
                <div class="fd-stat"><span class="fd-stat-num"><?= count($tables) ?></span><span class="fd-stat-label">Tabel</span></div>
                <div class="fd-stat"><span class="fd-stat-num">6</span><span class="fd-stat-label">Domain</span></div>
            </div>
        </div>

        <nav class="fd-chips">
            <span class="fd-chips-label"><i class="fas fa-compass"></i> Navigasi</span>
            <a href="#alur" class="fd-chip" style="--c:#2dd4bf"><i class="fas fa-circle"></i> Alur</a>
            <a href="#database" class="fd-chip" style="--c:#818cf8"><i class="fas fa-circle"></i> Database</a>
        </nav>
    </header>

    <!-- ALUR -->
    <section class="fd-section" id="alur">
        <div class="fd-sec-head">
            <div class="fd-sec-ico" style="background:linear-gradient(135deg,#06b6d4,#8b5cf6)"><i class="fas fa-route"></i></div>
            <div>
                <div class="fd-sec-title">Alur Aplikasi</div>
                <div class="fd-sec-sub">Perjalanan data master → purchasing → stok → penjualan → tenant</div>
            </div>
        </div>

        <div class="flow-grid">
            <?php foreach ($phases as $i => $p) : ?>
            <article class="flow-card" style="--fc:<?= $p['color'] ?>;--i:<?= $i ?>">
                <div class="fc-top">
                    <div class="fc-ico"><i class="fas <?= $p['icon'] ?>"></i></div>
                    <div class="fc-title"><?= h($p['title']) ?></div>
                </div>
                <p class="fc-desc"><?= h($p['desc']) ?></p>
                
                <?php if (!empty($p['pages'])) : ?>
                <div class="fc-pages">
                    <span class="fc-pg-label"><i class="fas fa-arrow-right"></i> Halaman</span>
                    <div class="fc-pg-wrap">
                        <?php foreach ($p['pages'] as $pk) : 
                            if (!isset($pages[$pk])) continue;
                            list($label, $url) = $pages[$pk];
                            $accessible = in_array($pk, $myPages, true);
                            $href = $accessible ? ($base . '/' . $url) : ($base . '/pages/coming-soon.php?menu=' . $pk);
                        ?>
                        <a href="<?= h($href) ?>" class="fc-pg-chip<?= $accessible ? '' : ' fc-pg-soon' ?>"><?= h($label) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="fc-tables">
                    <span class="fc-tbl-label"><i class="fas fa-database"></i> Tabel</span>
                    <div class="fc-tbl-wrap">
                        <?php foreach ($p['tables'] as $t) : ?>
                        <span class="fc-tbl-chip"><?= h($t) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- DATABASE -->
    <section class="fd-section" id="database">
        <div class="fd-sec-head">
            <div class="fd-sec-ico" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)"><i class="fas fa-database"></i></div>
            <div>
                <div class="fd-sec-title">Database</div>
                <div class="fd-sec-sub">25 tabel db_kantin &mdash; struktur & fungsi</div>
            </div>
        </div>

        <div class="db-legend">
            <?php foreach ($domains as $dom => $meta) : ?>
            <span class="db-legend-item"><i class="fas fa-square" style="color:<?= $meta['color'] ?>"></i><?= h($meta['label']) ?></span>
            <?php endforeach; ?>
        </div>

        <?php foreach ($domains as $dom => $meta) : $color = $meta['color']; ?>
        <div class="db-group">
            <div class="db-ghead">
                <span class="db-gdot" style="background:<?= $color ?>;box-shadow:0 0 12px <?= $color ?>"></span>
                <div class="db-gtitle"><?= h($meta['label']) ?></div>
            </div>
            <div class="db-grid">
                <?php foreach ($tables as $id => $t) : if ($t['domain'] !== $dom) continue; ?>
                <article class="db-card" style="--td:<?= $t['color'] ?>">
                    <div class="db-top">
                        <div class="db-ico"><i class="fas <?= $t['icon'] ?>"></i></div>
                        <code class="db-name"><?= h($id) ?></code>
                    </div>
                    <p class="db-desc"><?= h($t['desc']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <button class="fd-top" id="fdTop"><i class="fas fa-arrow-up"></i></button>
</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
(function () {
    var cards = document.querySelectorAll('.flow-card, .db-card');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
            });
        }, { rootMargin: '0px 0px -30px 0px', threshold: 0.05 });
        cards.forEach(function (c) { io.observe(c); });
    } else {
        cards.forEach(function (c) { c.classList.add('in'); });
    }

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
})();
</script>
</body>
</html>
