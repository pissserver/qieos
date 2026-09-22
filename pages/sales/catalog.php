<?php
include '../../sessions/session.php';
$query = mysqli_query($conn,
            "SELECT p.*, COALESCE(SUM(ss.qty), 0) as stock FROM products p LEFT JOIN sales_stock ss ON p.id = ss.product_id
            WHERE (p.catalog = 'active' OR p.category = 'additional') AND p.deleted_at IS NULL
            GROUP BY p.id ORDER BY p.starred DESC, p.name ASC"
        );

$items = [];
if($query){
    while($row = mysqli_fetch_assoc($query)){
        $items[] = $row;
    }
}

// Gabungkan Racikan / Combine ke katalog (bukan produk fisik: tanpa foto & tanpa stok)
$cq = mysqli_query($conn,
    "SELECT c.id, c.name, COALESCE(SUM(ci.price * ci.qty), 0) AS total
     FROM product_combos c
     LEFT JOIN product_combo_items ci ON ci.combo_id = c.id
     WHERE c.deleted_at IS NULL
     GROUP BY c.id
     ORDER BY c.id DESC"
);
if($cq){
    while($row = mysqli_fetch_assoc($cq)){
        $items[] = [
            'id'         => 'c' . $row['id'],
            'name'       => $row['name'],
            'sell_price' => $row['total'],
            'category'   => 'racikan',
            'photo'      => '',
            'starred'    => 0,
            'stock'      => 0,
            'is_combo'   => true,
        ];
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Katalog Produk - Qieos</title>

    <?php include '../../script/headscript.php'; ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/catalog.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/catalog.css'); ?>">
</head>

<body>
    <?php include '../components/sidebar.php'; ?>

    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <?php $catDefs = [
            ['all',        '📦', 'Semua Kategori'],
            ['makanan',    '🍜', 'Makanan'],
            ['minuman',    '🧋', 'Minuman'],
            ['jajanan',    '🍪', 'Jajanan'],
            ['pelengkap',  '🥄', 'Pelengkap'],
            ['racikan',    '🍱', 'Racikan'],
            ['additional', '➕', 'Additional'],
        ]; ?>

        <div class="container-fluid px-0 mt-5 mb-5">

            <!-- ===== HERO: TITLE + SEARCH + SORT (satu panel premium) ===== -->
            <div class="catalog-hero mb-4">
                <div class="ch-glow"></div>
                <div class="ch-glow ch-glow-2"></div>

                <div class="ch-main">
                    <div class="ch-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="ch-text">
                        <div class="ch-title">Katalog Produk</div>
                        <div class="ch-sub">Kelola menu produk yang siap dijual</div>
                    </div>
                </div>

                <div class="ch-tools">
                    <div class="ch-search">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            id="search"
                            placeholder="Cari produk..."
                            onkeyup="applyFilters()">
                    </div>

                    <div class="ch-sort">
                        <span class="ch-sort-lbl"><i class="fas fa-arrow-up-wide-short"></i> Urutkan</span>
                        <div class="ch-seg">
                            <button class="ch-seg-btn active" data-sort="name" onclick="sortProduct('name',this)">
                                <i class="fas fa-text-width"></i><span>Nama</span>
                            </button>
                            <button class="ch-seg-btn" data-sort="latest" onclick="sortProduct('latest',this)">
                                <i class="fas fa-clock"></i><span>Terbaru</span>
                            </button>
                            <button class="ch-seg-btn" data-sort="low" onclick="sortProduct('low',this)">
                                <i class="fas fa-arrow-down"></i><span>Harga</span>
                            </button>
                            <button class="ch-seg-btn" data-sort="high" onclick="sortProduct('high',this)">
                                <i class="fas fa-arrow-up"></i><span>Tertinggi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== CATEGORY BADGES (premium pills) ===== -->
            <div class="cat-bar mb-5">
                <?php foreach($catDefs as $cd): ?>
                <button
                    class="cat-badge<?= $cd[0] === 'all' ? ' active' : '' ?>"
                    data-cat="<?= $cd[0] ?>"
                    onclick="setCategory('<?= $cd[0] ?>',this)">
                    <span class="cb-ico"><?= $cd[1] ?></span>
                    <span class="cb-lbl"><?= $cd[2] ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <div id="product-list" class="product-grid">

                <?php $index = 0; $isCombo = false; $catName = ''; ?>
                <?php foreach($items as $row): $isCombo = !empty($row['is_combo']); $catName = strtolower($row['category']); ?>

                <div class="product-item"
                    data-index="<?php echo $index++; ?>"
                    data-name="<?php echo strtolower($row['name']); ?>"
                    data-id="<?php echo $row['id']; ?>"
                    data-category="<?php echo $row['category']; ?>"
                    data-price="<?php echo $row['sell_price']; ?>"
                    data-star="<?php echo isset($row['starred']) ? $row['starred'] : 0; ?>">

                    <div class="product-card">

                        <div class="product-image-wrap">

                            <?php if(!empty($row['photo'])): ?>
                                <img src="../../assets/img/products/<?php echo $row['photo']; ?>"
                                    class="product-img" loading="lazy" decoding="async">
                            <?php else: ?>
                                <div class="product-img product-img-empty">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            <?php endif; ?>

                            <?php if(!$isCombo): ?>
                            <div class="stock-badge" id="stock-<?php echo $row['id']; ?>">
                                <i class="fas fa-cube"></i>
                                <?php echo $catName !== 'additional' ? $row['stock'] : 'Tanpa' ; ?> Stok
                            </div>
                            <?php else: ?>
                            <div class="stock-badge">
                                <i class="fas fa-cube"></i>
                                Tanpa Stok
                            </div>
                            <?php endif; ?>

                            <div class="price-floating">
                                Rp <?php echo number_format($row['sell_price'],0,',','.'); ?>
                            </div>

                            <!-- MOBILE: Glass name overlay on image -->
                            <div class="card-name-wrap">
                                <div class="card-name-glass">
                                    <span class="card-name-text"><?php echo ucwords(strtolower($row['name'])); ?></span>
                                    <?php if(!$isCombo): ?>
                                    <button
                                        class="card-name-star <?= $row['starred'] ? 'active' : '' ?>"
                                        onclick="event.stopPropagation();toggleStar(<?= $row['id'] ?>,this)">
                                        <i class="<?= $row['starred'] ? 'fas' : 'far' ?> fa-star"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if(!$isCombo): ?>
                            <!-- DESKTOP: star on image -->
                            <button
                                class="star-btn <?= $row['starred'] ? 'active' : '' ?>"
                                onclick="toggleStar(<?= $row['id'] ?>,this)">
                                <i class="<?= $row['starred'] ? 'fas' : 'far' ?> fa-star"></i>
                            </button>
                            <?php endif; ?>

                        </div>

                        <div class="product-content">

                            <div class="product-meta">
                                <div class="category-pill">
                                    <i class="fas fa-tag"></i>
                                    <?php echo ucfirst($row['category']); ?>
                                </div>
                            </div>

                            <h4 class="product-title">
                                <?php echo ucwords(strtolower($row['name'])); ?>
                            </h4>

                            <p class="product-desc">
                                <?php if($isCombo || $catName === 'additional'): ?>
                                    <i class="fas fa-circle text-secondary"></i>
                                    Tersedia
                                <?php elseif($row['stock'] > 0): ?>
                                    <i class="fas fa-circle text-success"></i>
                                    Stok tersedia
                                <?php else: ?>
                                    <i class="fas fa-circle text-danger"></i>
                                    Stok habis
                                <?php endif; ?>
                            </p>

                            <!-- ACTION ROW: QTY + BUTTON -->
                            <div class="action-row">

                                <?php if($isCombo || $row['stock'] > 0 || $catName === 'additional'): ?>

                                    <?php if($catName !== 'additional'): ?>
                                    <div class="qty-mini">
                                        <button class="qty-mini-btn"
                                            onclick="decreaseQty('<?php echo $row['id']; ?>')">
                                            <i class="fas fa-minus"></i>
                                        </button>

                                        <input
                                            type="text"
                                            id="qty-<?php echo $row['id']; ?>"
                                            value="0"
                                            class="qty-mini-input"
                                            data-stock="<?php echo $isCombo ? 9999 : $row['stock']; ?>"
                                            readonly>

                                        <button class="qty-mini-btn qty-mini-plus"
                                            onclick="increaseQty('<?php echo $row['id']; ?>')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <?php endif; ?>

                                    <button
                                        class="btn-add"
                                        onclick="addToCart(
                                            this,
                                            '<?php echo $row['id']; ?>',
                                            '<?php echo addslashes($row['name']); ?>',
                                            '<?php echo $row['sell_price']; ?>',
                                            '<?php echo $row['category']; ?>'
                                        )">
                                        <i class="fas fa-cart-plus"></i>
                                        <span>Tambah</span>
                                    </button>

                                <?php else: ?>

                                    <div class="qty-mini" style="opacity:0.5;pointer-events:none">
                                        <span class="qty-mini-btn"><i class="fas fa-minus"></i></span>
                                        <span class="qty-mini-input">0</span>
                                        <span class="qty-mini-btn"><i class="fas fa-plus"></i></span>
                                    </div>

                                    <button class="btn-add btn-disabled" disabled>
                                        <i class="fas fa-ban"></i>
                                        <span>Habis</span>
                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>

                        <!-- MOBILE: Action bar below card -->
                        <div class="mobile-action">
                            <div class="mobile-cat-pill">
                                <i class="fas fa-tag"></i>
                                <?php echo ucfirst($row['category']); ?>
                            </div>

                            <div class="mobile-act-row">
                                <?php if($isCombo || $row['stock'] > 0 || $catName === 'additional'): ?>

                                    <?php if($catName !== 'additional'): ?>
                                    <div class="mobile-qty">
                                        <button class="mobile-qty-btn" onclick="decreaseQty('<?php echo $row['id']; ?>')">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" id="mqty-<?php echo $row['id']; ?>" value="0"
                                            class="mobile-qty-val" data-stock="<?php echo $isCombo ? 9999 : $row['stock']; ?>" readonly>
                                        <button class="mobile-qty-btn mobile-qty-plus" onclick="increaseQtyMobile('<?php echo $row['id']; ?>')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <?php endif; ?>

                                    <button class="mobile-add-btn"
                                        onclick="addToCartMobile(this,'<?php echo $row['id']; ?>','<?php echo addslashes($row['name']); ?>','<?php echo $row['sell_price']; ?>','<?php echo $row['category']; ?>')">
                                        <i class="fas fa-cart-plus"></i>
                                        <span>Tambah</span>
                                    </button>

                                <?php else: ?>

                                    <div class="mobile-qty" style="opacity:0.4;pointer-events:none">
                                        <span class="mobile-qty-btn"><i class="fas fa-minus"></i></span>
                                        <span class="mobile-qty-val">0</span>
                                        <span class="mobile-qty-btn"><i class="fas fa-plus"></i></span>
                                    </div>
                                    <button class="mobile-add-btn mobile-add-disabled" disabled>
                                        <i class="fas fa-ban"></i> Habis
                                    </button>

                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                </div>

                <?php endforeach; ?>

                <div id="empty-search" class="empty-search" style="display:none;">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>

                    <h5>Produk Tidak Ditemukan</h5>

                    <p>
                        Tidak ada produk yang cocok dengan pencarian Anda.
                        Coba gunakan kata kunci lain atau ubah filter.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../script/footscript.php'; ?>

    <script>
        function toggleStar(id,btn){

            fetch(
                'catalog-star.php',
                {
                    method:'POST',
                    headers:{
                        'Content-Type':'application/x-www-form-urlencoded'
                    },
                    body:'id='+id
                }
            )
            .then(res=>res.json())
            .then(data=>{

                let icon = btn.querySelector('i');

                let card = btn.closest('.product-item');

                if(data.starred){

                    btn.classList.add('active');

                    icon.classList.remove('far');
                    icon.classList.add('fas');

                    card.dataset.star = 1;

                }else{

                    btn.classList.remove('active');

                    icon.classList.remove('fas');
                    icon.classList.add('far');

                    card.dataset.star = 0;

                }

                // LANGSUNG PINDAH KE POSISI BARU
                reorderProducts();

            });

        }

        function increaseQty(id) {
            let input = document.getElementById('qty-' + id);
            let stock = parseInt(input.dataset.stock || 0);
            let currentVal = parseInt(input.value || 0);

            if (currentVal < stock) {
                input.value = currentVal + 1;
            }
        }

        function decreaseQty(id) {
            let input = document.getElementById('qty-' + id);
            let val = parseInt(input.value);
            if (val > 0) input.value = val - 1;

            // sync mobile qty
            let mInput = document.getElementById('mqty-' + id);
            if (mInput) mInput.value = input.value;
        }

        function increaseQtyMobile(id) {
            let mInput = document.getElementById('mqty-' + id);
            let dInput = document.getElementById('qty-' + id);
            let stock = parseInt(mInput.dataset.stock || 0);
            let currentVal = parseInt(mInput.value || 0);

            if (currentVal < stock) {
                mInput.value = currentVal + 1;
                if (dInput) dInput.value = mInput.value;
            }
        }

        function addToCartMobile(btn, id, name, price, category) {
            let mInput = document.getElementById('mqty-' + id);
            let dInput = document.getElementById('qty-' + id);

            // sync qty from mobile to desktop
            if (dInput && mInput) {
                dInput.value = mInput.value;
            }

            // call original addToCart
            addToCart(btn, id, name, price, category);

            // reset mobile qty after add
            if (mInput) mInput.value = 0;
        }

        let activeCategory = 'all';

        function setCategory(cat, btn){
            activeCategory = cat;
            document.querySelectorAll('.cat-badge').forEach(b => b.classList.toggle('active', b === btn));
            applyFilters();
        }

        function applyFilters() {
            let keyword = document.getElementById('search').value.toLowerCase();
            let category = activeCategory;
            let items = document.querySelectorAll('.product-item');
            let found = false;

            items.forEach(item => {

                let name = item.dataset.name.toLowerCase();
                let itemCategory = item.dataset.category.toLowerCase();

                let matchKeyword = name.includes(keyword);

                let matchCategory =
                    category === 'all' ||
                    itemCategory === category;

                if (matchKeyword && matchCategory) {

                    item.style.display = 'block';
                    found = true;

                } else {

                    item.style.display = 'none';

                }

            });

            let empty = document.getElementById('empty-search');
            let productList = document.getElementById('product-list');

            if (found) {

                empty.style.display = 'none';
                productList.classList.remove('empty-mode');

            } else {

                empty.style.display = 'flex';
                productList.classList.add('empty-mode');

            }

        }

        function reorderProducts(){

            let container = document.getElementById('product-list');
            let items = Array.from(document.querySelectorAll('.product-item'));

            items.sort((a,b)=>{

                let starA = parseInt(a.dataset.star || 0);
                let starB = parseInt(b.dataset.star || 0);

                // STAR always on top
                if(starA !== starB){
                    return starB - starA;
                }

                // fallback ke urutan asli
                return parseInt(a.dataset.index) - parseInt(b.dataset.index);
            });

            items.forEach(item=>{
                container.appendChild(item);
            });
        }

        function setInitialIndex() {
            document.querySelectorAll('.product-item').forEach((item, index) => {
                item.dataset.index = index;
            });
        }

        setInitialIndex();

        function sortProduct(type, btn) {

            if(btn){
                document.querySelectorAll('.ch-seg-btn').forEach(b => b.classList.toggle('active', b === btn));
            }

            let container = document.getElementById('product-list');
            let items = Array.from(document.querySelectorAll('.product-item'));

            items.sort((a, b) => {

                let starA = parseInt(a.dataset.star || 0);
                let starB = parseInt(b.dataset.star || 0);

                if (starA !== starB) {
                    return starB - starA;
                }

                if (type === 'name') {
                    return a.dataset.name.localeCompare(b.dataset.name);
                }

                if (type === 'latest') {
                    return parseInt(b.dataset.index) - parseInt(a.dataset.index);
                }

                if (type === 'low') {
                    return parseInt(a.dataset.price) - parseInt(b.dataset.price);
                }

                if (type === 'high') {
                    return parseInt(b.dataset.price) - parseInt(a.dataset.price);
                }

                return a.dataset.name.localeCompare(b.dataset.name);
            });

            items.forEach(item => container.appendChild(item));
        }

        function syncStock() {

            // skip polling saat tab tidak terlihat (hemat baterai & GPU di mobile)
            if (document.hidden) return;

            fetch('../components/data/get-stock.php')
                .then(res => res.json())
                .then(data => {

                    Object.keys(data).forEach(id => {

                        let stock = parseInt(data[id]);
                        let card = productCardMap.get(id);
                        if (!card) return;

                        let category = card.dataset.category;
                        let isAdditional = category.toLowerCase() === 'additional';

                        // STOCK BADGE - hanya ditulis ulang kalau angkanya berubah
                        if (!isAdditional) {
                            let el = document.getElementById('stock-' + id);
                            if (el && el.dataset.v !== String(stock)) {
                                el.dataset.v = String(stock);
                                el.innerHTML = `
                                    <i class="fas fa-cube"></i>
                                    ${stock} Stok
                                `;
                            }
                        }

                        // CLASS HABIS - hanya diputar balik kalau berubah
                        if (!isAdditional) {
                            if (stock <= 0) {
                                if (!card.classList.contains('out-of-stock')) card.classList.add('out-of-stock');
                            } else {
                                if (card.classList.contains('out-of-stock')) card.classList.remove('out-of-stock');
                            }
                        }

                        // STATUS TEXT - hanya ditulis ulang kalau berubah
                        let desc = card.querySelector('.product-desc');
                        if (desc) {
                            let key = isAdditional ? 'add' : (stock <= 0 ? 'out' : 'ok');
                            if (desc.dataset.v !== key) {
                                desc.dataset.v = key;
                                if (isAdditional) {
                                    desc.innerHTML = `
                                        <i class="fas fa-circle text-secondary"></i>
                                        Tersedia
                                    `;
                                } else if (stock <= 0) {
                                    desc.innerHTML = `
                                        <i class="fas fa-circle text-danger"></i>
                                        Stok habis
                                    `;
                                } else {
                                    desc.innerHTML = `
                                        <i class="fas fa-circle text-success"></i>
                                        Stok tersedia
                                    `;
                                }
                            }
                        }

                        // qty input stock
                        if (!isAdditional) {
                            let input = document.getElementById('qty-' + id);
                            if (input) {
                                input.dataset.stock = stock;
                                if (stock <= 0 && parseInt(input.value || 0) > 0) {
                                    input.value = 0;
                                }
                            }
                        }

                    });

                });

        }

        // Map cepat: id -> kartu (sekali saja, bukan querySelector tiap polling)
        const productCardMap = new Map();
        document.querySelectorAll('.product-item').forEach(card => {
            productCardMap.set(card.dataset.id, card);
        });

        // pertama kali load
        syncStock();

        // refresh lebih jarang + skip saat tab disembunyikan
        setInterval(syncStock, 5000);

        // === HIGHLIGHT FROM GLOBAL SEARCH ===
        (function(){
            var params = new URLSearchParams(window.location.search);
            var highlightId = params.get('highlight');
            if(!highlightId) return;

            var target = document.querySelector('.product-item[data-id="'+highlightId+'"]');
            if(!target) return;

            setTimeout(function(){
                target.scrollIntoView({behavior:'smooth',block:'center'});
                target.classList.add('highlight-target');
                var nameEl = target.querySelector('.product-title');
                var productName = nameEl ? nameEl.textContent.trim() : 'Produk';
                QToast('Produk Ditemukan', 'Anda diarahkan ke "'+productName+'" dari pencarian.', 'info');
                setTimeout(function(){ target.classList.remove('highlight-target'); },4000);
                history.replaceState(null,'',window.location.pathname);
            },400);
        })();

    </script>

</body>
</html>
