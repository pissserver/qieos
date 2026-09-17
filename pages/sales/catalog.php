<?php
include '../../sessions/session.php';
$query = mysqli_query($conn,
            "SELECT p.*, COALESCE(SUM(ss.qty), 0) as stock FROM products p LEFT JOIN sales_stock ss ON p.id = ss.product_id
            WHERE (p.catalog = 'active' OR p.category = 'additional') AND p.deleted_at IS NULL
            GROUP BY p.id ORDER BY p.starred DESC, p.name ASC"
        );
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

        <?php $blue_strip_icon='fa-book-open'; $blue_strip_title='Katalog Produk'; $blue_strip_subtitle='Kelola menu produk yang siap dijual'; ?>

        <div class="container-fluid px-0 mt-5 mb-5">

            <?php include '../components/blue-strip.php'; ?>

            <div class="catalog-toolbar mb-5">

                <div class="search-modern">

                    <div class="search-icon">
                        <i class="fas fa-search"></i>
                    </div>

                    <input
                        type="text"
                        id="search"
                        placeholder="Cari produk..."
                        onkeyup="applyFilters()">

                </div>

                <div class="toolbar-actions">

                    <!-- FILTER KATEGORI -->
                    <div class="premium-filter">

                        <button class="toolbar-btn">
                            <i class="fas fa-layer-group"></i>
                        </button>

                        <select
                            id="category-filter"
                            class="hidden-select"
                            onchange="applyFilters()">

                            <option value="all">📦 Semua Kategori</option>
                            <option value="makanan">🍜 Makanan</option>
                            <option value="minuman">🧋 Minuman</option>
                            <option value="jajanan">🍪 Jajanan</option>
                            <option value="pelengkap">🥄 Pelengkap</option>
                            <option value="additional">➕ Tambahan</option>

                        </select>

                    </div>

                    <!-- SORT HARGA -->
                    <div class="premium-filter">

                        <button class="toolbar-btn">
                            <i class="fas fa-arrow-up-wide-short"></i>
                        </button>

                        <select
                            id="sort-filter"
                            class="hidden-select"
                            onchange="sortProduct(this.value)">

                            <option value="name">🔥 Nama</option>
                            <option value="latest">✨ Terbaru</option>
                            <option value="low">⬇ Harga Terendah</option>
                            <option value="high">⬆ Harga Tertinggi</option>

                        </select>

                    </div>

                </div>

            </div>

            <div id="product-list" class="product-grid">

                <?php $index = 0; ?>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>

                <div class="product-item"
                    data-index="<?php echo $index++; ?>"
                    data-name="<?php echo strtolower($row['name']); ?>"
                    data-id="<?php echo $row['id']; ?>"
                    data-category="<?php echo $row['category']; ?>"
                    data-price="<?php echo $row['sell_price']; ?>"
                    data-star="<?php echo $row['starred']; ?>">

                    <div class="product-card">

                        <div class="product-image-wrap">

                            <img src="../../assets/img/products/<?php echo $row['photo']; ?>"
                                class="product-img">

                            <div class="stock-badge" id="stock-<?php echo $row['id']; ?>">
                                <i class="fas fa-cube"></i>
                                <?php echo strtolower($row['category']) !== 'additional' ? $row['stock'] : 'Tanpa' ; ?> Stok
                            </div>

                            <div class="price-floating">
                                Rp <?php echo number_format($row['sell_price'],0,',','.'); ?>
                            </div>

                            <!-- MOBILE: Glass name overlay on image -->
                            <div class="card-name-wrap">
                                <div class="card-name-glass">
                                    <span class="card-name-text"><?php echo ucwords(strtolower($row['name'])); ?></span>
                                    <button
                                        class="card-name-star <?= $row['starred'] ? 'active' : '' ?>"
                                        onclick="event.stopPropagation();toggleStar(<?= $row['id'] ?>,this)">
                                        <i class="<?= $row['starred'] ? 'fas' : 'far' ?> fa-star"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- DESKTOP: star on image -->
                            <button
                                class="star-btn <?= $row['starred'] ? 'active' : '' ?>"
                                onclick="toggleStar(<?= $row['id'] ?>,this)">
                                <i class="<?= $row['starred'] ? 'fas' : 'far' ?> fa-star"></i>
                            </button>

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
                                <?php if(strtolower($row['category']) === 'additional'): ?>
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

                                <?php if($row['stock'] > 0 || strtolower($row['category']) === 'additional'): ?>

                                    <?php if(strtolower($row['category']) !== 'additional'): ?>
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
                                            data-stock="<?php echo $row['stock']; ?>"
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
                                <?php if($row['stock'] > 0 || strtolower($row['category']) === 'additional'): ?>

                                    <?php if(strtolower($row['category']) !== 'additional'): ?>
                                    <div class="mobile-qty">
                                        <button class="mobile-qty-btn" onclick="decreaseQty('<?php echo $row['id']; ?>')">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" id="mqty-<?php echo $row['id']; ?>" value="0"
                                            class="mobile-qty-val" data-stock="<?php echo $row['stock']; ?>" readonly>
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

                <?php endwhile; ?>

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

        function applyFilters() {
            let keyword = document.getElementById('search').value.toLowerCase();
            let category = document.getElementById('category-filter').value.toLowerCase();
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

        function sortProduct(type) {

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

            fetch('../components/data/get-stock.php')
                .then(res => res.json())
                .then(data => {

                    Object.keys(data).forEach(id => {

                        let stock = parseInt(data[id]);
                        let card = document.querySelector(`.product-item[data-id="${id}"]`);

                        if (!card) return;

                        let category = card.dataset.category;
                        let isAdditional = category.toLowerCase() === 'additional';

                        // STOCK BADGE - skip for additional (stays "Tanpa Stok")
                        if (!isAdditional) {
                            let el = document.getElementById('stock-' + id);
                            if (el) {
                                el.innerHTML = `
                                    <i class="fas fa-cube"></i>
                                    ${stock} Stok
                                `;
                            }
                        }

                        // CLASS STOCK - skip for additional
                        if (!isAdditional) {
                            if (stock <= 0) {
                                card.classList.add('out-of-stock');
                            } else {
                                card.classList.remove('out-of-stock');
                            }
                        }

                        // STATUS TEXT
                        let desc = card.querySelector('.product-desc');
                        if (desc) {
                            if (isAdditional) {
                                // Additional: always show "Tersedia"
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

                        // UPDATE DATA STOCK INPUT - skip for additional
                        if (!isAdditional) {
                            let input = document.getElementById('qty-' + id);
                            if (input) {
                                input.dataset.stock = stock;
                                if (stock <= 0) {
                                    input.value = 0;
                                }
                            }
                        }

                    });

                })
                .catch(err => {
                    console.error('Gagal sync stock:', err);
                });

        }

        // pertama kali load
        syncStock();

        // refresh tiap 3 detik
        setInterval(syncStock, 3000);

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
