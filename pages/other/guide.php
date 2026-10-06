<?php
include '../../sessions/session.php';
$role = $user['role'];
$base = BASE_URL;

// status
$A = 'aktif';
$S = 'segera';

// helper pembungkus item
function g($icon, $name, $url, $status, $desc, $points, $tag = '') {
    return array('icon' => $icon, 'name' => $name, 'url' => $url, 'status' => $status,
                 'desc' => $desc, 'points' => $points, 'tag' => $tag);
}

// ============================================================
// DEFINISI ITEM (universal)
// ============================================================
$D = array();

$D['dashboard'] = g('fa-th-large', 'Dashboard', $base . '/pages/dashboard.php', $A,
    'Pusat kendali utama: ringkasan omzet, aktivitas pesanan, dan akses cepat ke menu penting hari ini.',
    array('Omzet & ringkasan penjualan hari ini', 'Status pesanan terbayar / menunggu', 'Akses cepat ke menu utama'));

$D['pencarian'] = g('fa-search', 'Pencarian Global', '', $A,
    'Kotak pencarian di navbar (tombol Ctrl + K) untuk menemukan halaman, produk, supplier, customer, pesanan, dan tenant.',
    array('Hasil pencarian menyesuaikan role Anda', 'Cari cepat tanpa pindah halaman', 'Navigasi dengan keyboard (atas / bawah / Enter)'), 'Navbar');

$D['whatsnew'] = g('fa-bullhorn', "What's New", '', $A,
    "Pop-up pembaruan sistem terbaru lewat ikon megafon di navbar - ringkasan dari halaman Update.",
    array('Notifikasi update terbaru', 'Versi & tanggal rilis', 'Klik untuk membuka detail'), 'Navbar');

$D['chat'] = g('fa-comments', 'Chat', $base . '/pages/chat/chat.php', $A,
    'Obrolan dua arah antar pengguna (kasir, administrator, developer) dengan notifikasi pesan baru.',
    array('Kirim & terima pesan antar pengguna', 'Badge pesan belum dibaca', 'Riwayat percakapan tersimpan'));

$D['keranjang'] = g('fa-shopping-cart', 'Keranjang', '', $A,
    'Keranjang belanja di navbar, terhubung ke Katalog Produk: memilih produk, mengatur qty, hingga checkout pesanan.',
    array('Tercetak otomatis saat belanja di Katalog', 'Customer (opsional) untuk nama pesanan', 'Checkout -> pesanan baru & struk'), 'Navbar');

$D['omzet'] = g('fa-coins', 'Omzet Hari Ini', '', $A,
    'Ringkasan omzet hari ini yang tampil langsung di navbar, dengan pembaruan otomatis.',
    array('Total penjualan hari ini', 'Auto-refresh tanpa reload halaman', 'Notifikasi saat ada pesanan baru'), 'Navbar');

$D['profil'] = g('fa-user-circle', 'Profil', $base . '/pages/profile/profile.php', $A,
    'Kelola identitas & keamanan akun: foto profil, nama, username, kata sandi, sampai keluar dari aplikasi.',
    array('Ubah foto & identitas', 'Ganti username / kata sandi', 'Keluar dari aplikasi (Sign Out)'));

$D['master-produk'] = g('fa-boxes-stacked', 'Master Produk', $base . '/pages/master/master-product.php', $A,
    'Pusat data produk: nama, kode, kategori, harga beli & jual, stok minimal, dan foto produk. Dasar bagi Katalog, Stok, dan Pesanan.',
    array('Tambah, ubah, hapus, atau nonaktifkan produk', 'Atur stok minimal untuk peringatan low stock', 'Detail produk menampilkan histori harga & stok'));

$D['master-supplier'] = g('fa-truck', 'Master Supplier', $base . '/pages/master/master-supplier.php', $A,
    'Kelola data supplier / pemasok barang untuk mendukung proses pembelian dan pengisian stok.',
    array('Simpan profil lengkap supplier', 'Tandai status aktif / nonaktif', 'Terhubung dengan form Input Pembelian'));

$D['master-customer'] = g('fa-users', 'Master Customer', $base . '/pages/master/master-customer.php', $A,
    'Kelola data customer pembeli di kantin: nama, kontak, hingga riwayat transaksi tiap customer.',
    array('Tambah / ubah / hapus customer', 'Riwayat transaksi tiap customer', 'Pilihan customer saat checkout pesanan'));

$D['master-user'] = g('fa-users-cog', 'Master User', $base . '/pages/master/master-user.php', $A,
    'Kelola akun pengguna sistem beserta perannya: developer, administrator, dan staff kasir.',
    array('Buat akun & tentukan role', 'Reset / ubah kata sandi', 'Kontrol akses tiap role ke menu'));

$D['daftar-belanja'] = g('fa-file-alt', 'Daftar Belanja', $base . '/pages/purchasing/list.php', $A,
    'Daftar rencana / request pembelian barang. Memantau status dari draft hingga barang masuk.',
    array('Buat daftar belanja baru', 'Pantau status & prioritas', 'Lanjutkan menjadi pembelian'));

$D['input-pembelian'] = g('fa-cart-plus', 'Input Pembelian', $base . '/pages/purchasing/purchase.php', $A,
    'Pencatatan pembelian dari supplier. Stok gudang bertambah otomatis sesuai data pembelian.',
    array('Pilih supplier & produk', 'Harga beli & qty langsung masuk stok', 'Histori pembelian tersimpan lengkap'));

$D['stok-gudang'] = g('fa-warehouse', 'Stok Gudang', $base . '/pages/stock/stock.php', $A,
    'Monitoring stok gudang dengan metode FIFO (First In, First Out) per layer, lengkap dengan histori masuk & keluar.',
    array('Lihat total stok & per layer FIFO', 'Deteksi stok menipis (low stock)', 'Riwayat barang keluar & masuk'));

$D['mutasi-stok'] = g('fa-truck-ramp-box', 'Mutasi Stok', $base . '/pages/stock/mutation.php', $A,
    'Pencatatan mutasi / perpindahan stok antar lokasi, misalnya dari gudang menuju kantin.',
    array('Pilih lokasi asal & tujuan', 'Catat jumlah & catatan mutasi', 'Stok kedua sisi ter-update otomatis'));

$D['transfer-gudang'] = g('fa-exchange-alt', 'Transfer Gudang', $base . '/pages/stock/transfer.php', $A,
    'Transfer stok antar gudang. Stok pengirim berkurang, stok penerima bertambah, keduanya terekam rapi.',
    array('Pilih gudang asal & tujuan', 'Update stok dua sisi otomatis', 'Riwayat transfer tersimpan'));

$D['stok-kantin'] = g('fa-store', 'Stok Kantin', $base . '/pages/sales/sales-stock.php', $A,
    'Monitoring stok produk di kantin; mengajukan pengisian dari gudang untuk menjaga ketersediaan produk.',
    array('Sinkron stok gudang -> kantin', 'Ajukan pengisian (pending transfer)', 'Info low stock di kantin'));

$D['katalog'] = g('fa-book-open', 'Katalog Produk', $base . '/pages/sales/catalog.php', $A,
    'Beranda kasir: katalog produk untuk transaksi belanja. Pilih produk & qty, masukkan keranjang, lalu checkout.',
    array('Klik produk untuk menambah ke keranjang', 'Atur qty langsung di keranjang', 'Checkout -> buat pesanan + struk'));

$D['pesanan'] = g('fa-receipt', 'Pesanan', $base . '/pages/sales/order.php', $A,
    'Daftar pesanan real-time. Kelola status pembayaran, editing nama customer, hingga pencetakan struk.',
    array('Tandai terbayar / batalkan pesanan', 'Edit nama customer pesanan', 'Lihat detail & cetak struk'));

$D['tenant'] = g('fa-store', 'Tenant', $base . '/pages/tenant/tenant.php', $A,
    'Data tenant penyewa: pendaftaran, kartu identitas, hingga rincian penyewaan & pembayaran.',
    array('Pendaftaran tenant baru', 'Kartu tenant siap cetak', 'Detail penyewaan & status pembayaran'));

$D['rekap'] = g('fa-chart-bar', 'Penjualan & Tenant', $base . '/pages/recap/recap.php', $A,
    'Rekap penjualan & penyewaan tenant dalam satu ringkasan untuk melihat performa kantin.',
    array('Total penjualan & sewa tenant', 'Tampilan rekap ringkas', 'Dasar untuk laporan lengkap'));

$D['laporan-penjualan'] = g('fa-chart-line', 'Laporan Penjualan', $base . '/pages/report/report-sales.php', $A,
    'Laporan penjualan lengkap: omzet, produk terlaris, kategori, margin, dan pengeluaran dengan grafik serta ekspor PDF.',
    array('Grafik & tabel penjualan', 'Filter berdasarkan periode', 'Cetak / ekspor ke PDF'));

$D['laporan-tenant'] = g('fa-chart-line', 'Laporan Tenant', $base . '/pages/report/report-tenant.php', $A,
    'Rekap laporan tenant per periode: status sewa, pembayaran, dan tunggakan.',
    array('Rekap per periode', 'Status pembayaran tenant', 'Cetak / ekspor ke PDF'));

$D['update'] = g('fa-rocket', 'Update', $base . '/pages/other/update.php', $A,
    'Riwayat changelog / pembaruan sistem. Menampilkan versi, tanggal, dan daftar perubahan setiap rilis.',
    array('Lihat riwayat update', 'Detail perubahan & fitur baru', '(Developer) mengelola changelog'));

$D['guide'] = g('fa-book', 'Guide', $base . '/pages/other/guide.php', $A,
    'Halaman ini. Petunjuk penggunaan setiap halaman Qieos sesuai role Anda.',
    array('Cara pakai tiap menu', 'Status fitur: aktif / segera hadir', 'Buka halaman langsung dari kartu'));

// ============================================================
// KOMPOSISI PER ROLE
// ============================================================
$cs = function ($menu) use ($base) {
    return $base . '/pages/coming-soon.php?menu=' . $menu;
};

$sections = array();

switch ($role) {
    case 'developer':
        $sections['UMUM'] = array('dashboard', 'pencarian', 'whatsnew', 'chat', 'keranjang', 'omzet', 'profil');
        $sections['MASTER'] = array('master-produk', 'master-supplier', 'master-customer', 'master-user');
        $sections['PURCHASING'] = array('daftar-belanja', 'input-pembelian');
        $sections['GUDANG STOK'] = array('stok-gudang', 'mutasi-stok', 'transfer-gudang');
        $sections['KANTIN'] = array('stok-kantin', 'katalog', 'pesanan');
        $sections['TENANT'] = array('tenant');
        $sections['REKAP'] = array('rekap');
        $sections['LAPORAN'] = array('laporan-penjualan', 'laporan-tenant');
        $sections['LAINNYA'] = array('update', 'guide');
        break;

    case 'administrator':
        $sections['UMUM'] = array('dashboard', 'pencarian', 'whatsnew', 'chat', 'profil');
        $sections['MASTER'] = array('master-produk', 'master-supplier', 'master-customer', 'master-user');
        $sections['PURCHASING'] = array('daftar-belanja', 'input-pembelian');
        $sections['GUDANG STOK'] = array('stok-gudang', 'mutasi-stok', 'transfer-gudang');
        $sections['TENANT'] = array('tenant');
        $sections['LAPORAN'] = array('laporan-penjualan', 'laporan-tenant');
        $sections['LAINNYA'] = array('update', 'guide');
        break;

    default: // staff kasir
        $sections['UMUM'] = array('dashboard', 'pencarian', 'whatsnew', 'chat', 'keranjang', 'omzet', 'profil');
        $sections['MASTER'] = array('master-customer');
        $sections['KANTIN'] = array('stok-kantin', 'katalog', 'pesanan');
        $sections['TENANT'] = array('tenant');
        $sections['REKAP'] = array('rekap');
        $sections['LAINNYA'] = array('update', 'guide');
        break;
}

// ubah item yang belum tersedia sesuai role -> "segera hadir"
$segera = array(
    'administrator' => array('master-produk', 'master-supplier', 'master-customer', 'daftar-belanja',
        'input-pembelian', 'stok-gudang', 'mutasi-stok', 'transfer-gudang', 'laporan-penjualan'),
    'staff kasir'   => array('master-customer', 'stok-kantin', 'katalog', 'pesanan'),
);

if (isset($segera[$role])) {
    foreach ($segera[$role] as $k) {
        if (isset($D[$k])) { $D[$k]['url'] = $cs($k); $D[$k]['status'] = $S; }
    }
}

// ============================================================
// WARNA GRADASI PER SEKSI
// ============================================================
$groupColors = array(
    'UMUM'        => '#4f46e5,#6366f1',
    'MASTER'      => '#0284c7,#0ea5e9',
    'PURCHASING'  => '#0d9488,#14b8a6',
    'GUDANG STOK' => '#7c3aed,#8b5cf6',
    'KANTIN'      => '#d97706,#f59e0b',
    'TENANT'      => '#db2777,#ec4899',
    'REKAP'       => '#0891b2,#22d3ee',
    'LAPORAN'     => '#e11d48,#f43f5e',
    'LAINNYA'     => '#475569,#64748b',
);

function slugify($s) {
    return preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($s)));
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Guide - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/guide.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/guide.css'); ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <!-- HITUNG RINGKASAN -->
    <?php
    $totalMenus = 0; $soonCount = 0;
    foreach ($sections as $keys) {
        $totalMenus += count($keys);
        foreach ($keys as $k) {
            if (isset($D[$k]) && $D[$k]['status'] === $S) { $soonCount++; }
        }
    }
    $seksiCount = count($sections);
    $roleLabel = ($role === 'staff kasir') ? 'Staff Kasir' : ucfirst($role);
    ?>

    <!-- HEADER HERO -->
    <header class="guide-hero">
        <div class="gh-glow gh-glow-1"></div>
        <div class="gh-glow gh-glow-2"></div>
        <div class="gh-grid"></div>

        <div class="gh-top">
            <div class="gh-identity">
                <div class="gh-icon"><i class="fas fa-book-open"></i></div>
                <div>
                    <div class="gh-eyebrow">Panduan Aplikasi &bull; <?= htmlspecialchars($roleLabel) ?></div>
                    <h1 class="gh-title">Guide</h1>
                    <p class="gh-sub">Petunjuk penggunaan setiap halaman Qieos sesuai role Anda &mdash; apa fungsi tiap menu dan bagaimana cara menggunakannya.</p>
                </div>
            </div>

            <div class="gh-stats">
                <div class="gh-stat">
                    <span class="gh-stat-num"><?= $totalMenus ?></span>
                    <span class="gh-stat-label">Halaman</span>
                </div>
                <div class="gh-stat">
                    <span class="gh-stat-num"><?= $seksiCount ?></span>
                    <span class="gh-stat-label">Seksi</span>
                </div>
                <div class="gh-stat">
                    <span class="gh-stat-num"><?= $soonCount ?></span>
                    <span class="gh-stat-label">Segera Hadir</span>
                </div>
            </div>
        </div>

        <nav class="gh-chips" aria-label="Lompat ke seksi">
            <span class="gh-chips-label"><i class="fas fa-layer-group"></i> Menu per seksi</span>
            <?php $ci = 0; foreach (array_keys($sections) as $t) : $gd = isset($groupColors[$t]) ? $groupColors[$t] : '#4f46e5,#6366f1'; list($c1) = explode(',', $gd); ?>
                <a href="#<?= slugify($t) ?>" class="gh-chip" style="--c:<?= $c1 ?>;--i:<?= $ci ?>;">
                    <i class="fas fa-circle"></i><?= htmlspecialchars($t) ?>
                </a>
            <?php $ci++; endforeach; ?>
        </nav>
    </header>

    <!-- SEKSI -->
    <?php foreach ($sections as $title => $keys) :
        $grad = isset($groupColors[$title]) ? $groupColors[$title] : '#4f46e5,#6366f1';
        $slug = slugify($title);
    ?>
    <section class="guide-section" id="<?= $slug ?>">
        <div class="gs-head">
            <div class="gs-ico" style="background:linear-gradient(135deg,<?= $grad ?>);">
                <i class="fas <?= $D[$keys[0]]['icon'] ?>"></i>
            </div>
            <div>
                <div class="gs-title"><?= htmlspecialchars($title) ?></div>
                <div class="gs-sub">Menu pada grup <?= htmlspecialchars(strtolower($title)) ?></div>
            </div>
            <span class="gs-count"><?= count($keys) ?> menu</span>
        </div>

        <div class="guide-grid">
            <?php $i = 0; foreach ($keys as $k) :
                $item = $D[$k];
                $isSoon = ($item['status'] === $S);
                $extra = ($k === 'tenant' && $role === 'staff kasir') ? 'Daftar Tenant' : $item['name'];
            ?>
            <article class="guide-card<?= $isSoon ? ' g-coming' : '' ?>" style="--gs:linear-gradient(135deg,<?= $grad ?>);--i:<?= $i ?>;">
                <div class="gc-top">
                    <div class="gc-ico"><i class="fas <?= $item['icon'] ?>"></i></div>
                    <div class="gc-id">
                        <div class="gc-name">
                            <?= htmlspecialchars($extra) ?>
                            <?php if ($item['tag'] !== '') : ?>
                                <span class="gc-tag"><?= htmlspecialchars($item['tag']) ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="g-badge <?= $isSoon ? 'g-b-segera' : 'g-b-aktif' ?>">
                            <?= $isSoon ? 'Segera Hadir' : 'Aktif' ?>
                        </span>
                    </div>
                </div>

                <p class="gc-desc"><?= htmlspecialchars($item['desc']) ?></p>

                <ul class="gc-points">
                    <?php foreach ($item['points'] as $p) : ?>
                        <li class="gp"><i class="fas fa-check"></i><?= htmlspecialchars($p) ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="gc-foot">
                    <?php if ($isSoon) : ?>
                        <span class="gc-soon"><i class="fas fa-hourglass-half"></i> Segera Hadir</span>
                    <?php elseif ($item['url'] !== '') : ?>
                        <a href="<?= htmlspecialchars($item['url']) ?>" class="gc-btn">
                            Buka Halaman <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php else : ?>
                        <span class="gc-soon gc-soon-nav"><i class="fas fa-mouse-pointer"></i> Akses dari Navbar</span>
                    <?php endif; ?>
                </div>
            </article>
            <?php $i++; endforeach; ?>
        </div>
    </section>
    <?php endforeach; ?>

    <!-- KEMBALI KE ATAS -->
    <button class="guide-top" id="guideTop" aria-label="Kembali ke atas"><i class="fas fa-arrow-up"></i></button>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
(function () {
    var cards = document.querySelectorAll('.guide-card');

    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('in');
                    io.unobserve(e.target);
                }
            });
        }, { rootMargin: '0px 0px -40px 0px', threshold: 0.08 });
        cards.forEach(function (c) { io.observe(c); });
    } else {
        cards.forEach(function (c) { c.classList.add('in'); });
    }

    // tombol kembali ke atas (scroller aplikasi = .content)
    var scroller = document.querySelector('.content') || window;
    var topBtn = document.getElementById('guideTop');
    if (topBtn) {
        var toggle = function () {
            var top = (scroller.scrollTop !== undefined) ? scroller.scrollTop : window.pageYOffset;
            topBtn.classList.toggle('show', top > 320);
        };
        scroller.addEventListener('scroll', toggle, { passive: true });
        toggle();
        topBtn.addEventListener('click', function () {
            if (scroller.scrollTo) { scroller.scrollTo({ top: 0, behavior: 'smooth' }); }
            else { window.scrollTo({ top: 0, behavior: 'smooth' }); }
        });
    }
})();
</script>
</body>
</html>