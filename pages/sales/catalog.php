<?php
include '../../sessions/session.php';
$query = mysqli_query($conn, 
            "SELECT p.*, COALESCE(SUM(ss.qty), 0) as stock FROM products p LEFT JOIN sales_stock ss ON p.id = ss.product_id
            WHERE p.catalog = 'active'
            GROUP BY p.id ORDER BY p.starred DESC, p.name ASC"
        );
?>

<style>
    /* ==========================
    LUXURY PRODUCT CARD
    ========================== */

    .product-card{
        background:linear-gradient(
            180deg,
            #0f172a,
            #020617
        );

        border-radius:35px;
        overflow:hidden;

        position:relative;

        box-shadow:
            0 25px 60px rgba(2,6,23,.35);

        transition:.4s;
    }

    .product-card:hover{
        transform:translateY(-10px);
    }

    .product-image-wrap{
        position:relative;
    }

    .product-img{
        width:100%;
        height:320px;
        object-fit:cover;
        transition:.5s;
    }

    .product-card:hover .product-img{
        transform:scale(1.06);
    }

    /* STOCK */

    .stock-badge{
        position:absolute;
        top:18px;
        left:18px;

        background:
            rgba(34,197,94,.15);

        backdrop-filter:blur(15px);

        border:
            1px solid rgba(34,197,94,.25);

        color:#4ade80;

        padding:10px 15px;

        border-radius:16px;

        font-size:13px;
        font-weight:700;

        display:flex;
        gap:8px;
        align-items:center;
    }

    /* PRICE FLOAT */

    .price-floating{
        position:absolute;
        top:18px;
        right:18px;

        background:
            rgba(251,191,36,.15);

        backdrop-filter:blur(15px);

        border:
            1px solid rgba(251,191,36,.25);

        color:#fbbf24;

        padding:10px 15px;

        border-radius:16px;

        font-size:13px;
        font-weight:800;
    }

    /* CONTENT */

    .product-content{

        margin-top:-40px;

        position:relative;

        z-index:10;

        background:
            linear-gradient(
                180deg,
                rgba(15,23,42,.98),
                rgba(2,6,23,1)
            );

        border-radius:30px 30px 0 0;

        padding:25px;
    }

    /* CATEGORY */

    .product-meta{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:15px;
    }

    .category-pill{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 15px;
        border-radius:999px;
        background:
            rgba(251,191,36,.1);
        color:#fbbf24;
        font-size:13px;
        font-weight:700;
        margin-bottom:15px;
    }

    .star-btn{
        width:48px;
        height:48px;
        border:none;
        border-radius:50%;
        background:rgba(255,255,255,.06);
        color:#64748b;
        transition:.3s;
        margin-top:-10px;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .star-btn:hover{
        transform:scale(1.1);
    }

    .star-btn.active{
        background:
            linear-gradient(
                135deg,
                #fcd34d,
                #f59e0b
            );
        color:#fff;
        box-shadow:
            0 0 20px rgba(245,158,11,.4);
    }

    .star-btn i{
        font-size:15px;
    }

    /* TITLE */

    .product-title{
        color:#fff;
        font-size:30px;
        font-weight:800;
        line-height:1.2;
        margin-bottom:12px;
    }

    /* DESC */

    .product-desc{
        color:#94a3b8;
        line-height:1.8;
        margin-bottom:20px;
    }

    /* QTY */

    .qty-box{
        background:rgba(255,255,255,.04);
        border:1px solid rgba(255,255,255,.08);
        border-radius:22px;
        padding:15px 18px;
        display:flex;
        justify-content:center; /* INI */
        align-items:center;
        margin-bottom:20px;
    }

    .qty-box span{
        color:#fff;
        font-weight:600;
    }

    .qty-control{
        display:flex;
        align-items:center;
        justify-content:center; /* INI */
        gap:15px;

        width:100%;
    }

    .qty-btn{
        width:50px;
        height:50px;

        border:none;

        border-radius:50%;

        background:
            linear-gradient(
                135deg,
                #334155,
                #0f172a
            );

        color:#fff;

        font-size:22px;

        transition:.3s;
    }

    .qty-btn:hover{
        transform:scale(1.08);
    }

    .qty-plus{
        background:
            linear-gradient(
                135deg,
                #fcd34d,
                #f59e0b
            );

        color:#111827;
    }

    .qty-input{
        width:50px;
        text-align:center;

        background:transparent;
        border:none;

        color:#fff;

        font-size:24px;
        font-weight:800;
    }

    /* BUTTON */

    .btn-checkout{

        width:100%;
        height:74px;

        border:none;

        border-radius:999px;

        background:
            linear-gradient(
                135deg,
                #fcd34d,
                #f59e0b
            );

        color:#111827;

        font-size:20px;
        font-weight:800;

        display:flex;
        justify-content:space-between;
        align-items:center;

        padding:0 25px;

        box-shadow:
            0 0 25px rgba(245,158,11,.4);

        transition:.3s;
    }

    .btn-checkout:hover{
        transform:translateY(-3px);
        box-shadow:
            0 0 35px rgba(245,158,11,.6);
    }

    /* ICON */

    .btn-checkout i{
        font-size:18px;
    }

    /* ==========================
    OUT OF STOCK
    ========================== */

    .out-stock-box{

        width:100%;
        height:50px;

        display:flex;
        align-items:center;
        justify-content:center;
        gap:10px;

        border-radius:18px;

        background:
            linear-gradient(
                135deg,
                rgba(239,68,68,.12),
                rgba(127,29,29,.18)
            );

        border:
            1px solid rgba(239,68,68,.25);

        color:#f87171;

        font-weight:700;
        font-size:14px;
    }

    .out-stock-box i{
        font-size:16px;
    }

    /* ==========================
    BUTTON DISABLED
    ========================== */

    .btn-disabled{

        background:
            linear-gradient(
                135deg,
                #334155,
                #1e293b
            ) !important;

        color:#94a3b8 !important;

        cursor:not-allowed;

        box-shadow:none !important;
    }

    .btn-disabled:hover{
        transform:none !important;
    }

    /* =========================
    PRODUCT GRID
    ========================= */

    .product-grid{
        display:grid;
        gap:16px;
        width:100%;
    }

    .product-grid.empty-mode{
        display:flex !important;
        justify-content:center;
        align-items:center;
        min-height:65vh;
    }

    /* Desktop */
    @media (min-width:1200px){
        .product-grid{
            grid-template-columns:repeat(4,minmax(0,1fr));
        }
    }

    /* Laptop */
    @media (min-width:768px) and (max-width:1199px){
        .product-grid{
            grid-template-columns:repeat(3,minmax(0,1fr));
        }
    }

    /* HP */
    @media (max-width:767px){
        .product-grid{
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:12px;
        }
    }

    /* =========================
    CARD
    ========================= */

    .product-item{
        width:100%;
        min-width:0;
    }

    .product-card{
        width:100%;
        min-width:0;
        overflow:hidden;
    }

    /* ====================================
    MOBILE
    ==================================== */

    @media (max-width:575px){

        .product-card{
            border-radius:16px;
        }

        .product-img{
            height:105px;
        }

        .product-content{
            padding:10px;
            margin-top:-18px;
            border-radius:18px 18px 0 0;
        }

        .category-pill{
            font-size:8px;
            padding:3px 7px;
            margin-bottom:6px;
        }

        .star-btn{
            margin-top:0;
            width:28px;
            height:28px;
            border:none;
            border-radius:50%;
            background:rgba(255,255,255,.06);
            color:#64748b;
            transition:.3s;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .product-title{
            font-size:11px;
            line-height:1.4;
            min-height:20px;
            display:-webkit-box;
            -webkit-line-clamp:2;
            -webkit-box-orient:vertical;
            overflow:hidden;
        }

        /* TAMPILKAN STATUS STOK */

        .product-desc{
            display:block;
            font-size:9px;
            margin-bottom:8px;
            color:#94a3b8;
            text-align:center;
        }

        .stock-badge,
        .price-floating{
            font-size:8px;
            padding:4px 7px;
            border-radius:10px;
        }

        .qty-box{
            padding:6px;
            margin-bottom:8px;
        }

        /* POSISI TENGAH */

        .qty-control{
            display:flex;
            justify-content:center;
            align-items:center;
            gap:10px;
            width:100%;
        }

        .qty-btn{
            width:26px;
            height:26px;
            min-width:26px;
            min-height:26px;
            font-size:12px;
        }

        .qty-input{
            width:24px;
            text-align:center;
            font-size:13px;
            font-weight:700;
            border:none;
            background:transparent;
            color:#fff;
        }

        /* ==========================
        OUT OF STOCK
        ========================== */

        .out-stock-box{
            height:25px;
            font-size:10px;
        }

        .out-stock-box i{
            font-size:10px;
        }

        /* ==========================
        BUTTON DISABLED
        ========================== */

        .btn-disabled{

            background:
                linear-gradient(
                    135deg,
                    #334155,
                    #1e293b
                ) !important;

            color:#94a3b8 !important;

            cursor:not-allowed;

            box-shadow:none !important;
        }

        .btn-disabled:hover{
            transform:none !important;
        }

        .btn-checkout{
            height:36px;
            padding:0 8px;
            font-size:9px;
            border-radius:999px;

            display:flex;
            align-items:center;
            justify-content:center;
            gap:5px;
        }

        .btn-checkout i{
            font-size:9px;
        }

    }

    /* ==========================
    PREMIUM TOOLBAR
    ========================== */

    .catalog-toolbar{
        display:flex;
        align-items:center;
        gap:12px;

        padding:14px;
        border-radius:24px;

        background:rgba(255,255,255,.8);
        backdrop-filter:blur(18px);

        border:1px solid rgba(255,255,255,.5);

        box-shadow:
            0 10px 35px rgba(15,23,42,.08),
            inset 0 1px 0 rgba(255,255,255,.9);
    }

    /* ==========================
    SEARCH
    ========================== */

    .search-modern{
        flex:1;
        position:relative;
    }

    .search-modern input{
        width:100%;
        height:54px;

        border:none;
        outline:none;

        background:#f8fafc;

        border-radius:18px;

        padding-left:52px;
        padding-right:18px;

        font-size:14px;
        font-weight:500;

        transition:.35s;
    }

    .search-modern input:focus{
        background:#fff;

        box-shadow:
            0 0 0 4px rgba(99,102,241,.10),
            0 8px 25px rgba(99,102,241,.12);
    }

    .search-icon{
        position:absolute;
        left:18px;
        top:50%;
        transform:translateY(-50%);
        color:#64748b;
        z-index:2;
    }

    /* ==========================
    FILTER AREA
    ========================== */

    .toolbar-actions{
        display:flex;
        gap:10px;
    }

    /* ==========================
    PREMIUM FILTER
    ========================== */

    .premium-filter{
        position:relative;
        border-radius:18px;
        overflow:hidden;
        box-shadow:
        0 0 20px rgba(99,102,241,.15),
        0 0 35px rgba(236,72,153,.10);
    }

    .premium-filter::before{
        content:"";

        position:absolute;
        inset:-4px;

        background:linear-gradient(
            135deg,
            #6366f1,
            #8b5cf6,
            #ec4899,
            #06b6d4,
            #6366f1
        );

        background-size:300% 300%;

        animation:borderMove 5s linear infinite;

        z-index:0;
    }

    @keyframes borderMove{

        0%{
            background-position:0% 50%;
        }

        100%{
            background-position:100% 50%;
        }

    }

    .toolbar-btn{
        position:relative;
        z-index:2;

        width:54px;
        height:54px;

        border:none;

        border-radius:16px;

        background:#fff;

        color:#475569;

        cursor:pointer;

        display:flex;
        align-items:center;
        justify-content:center;

        font-size:16px;

        transition:.35s;
    }

    .toolbar-btn:hover{

        color:#6366f1;

        transform:translateY(-2px);

        box-shadow:
            0 10px 25px rgba(99,102,241,.20);
    }

    .toolbar-btn i{
        transition:.35s;
    }

    .toolbar-btn:hover i{
        transform:rotate(10deg) scale(1.1);
    }

    /* ==========================
    HIDDEN SELECT
    ========================== */

    .hidden-select{
        position:absolute;
        inset:0;
        width:100%;
        height:100%;
        opacity:0;
        cursor:pointer;
        z-index:5;
    }

    /* ==========================
    MOBILE
    ========================== */

    @media(max-width:768px){

        .catalog-toolbar{
            flex-direction:row;
            align-items:center;
            gap:8px;
            padding:10px;
        }

        .search-modern{
            flex:1;
        }

        .search-modern input{
            height:48px;
            font-size:13px;
        }

        .toolbar-btn{
            width:48px;
            height:48px;
        }

    }

    /* Empty Search */
    .empty-search{
        width:520px;
        max-width:90%;
        min-height:auto;
        margin:auto;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        text-align:center;
        padding:50px;
        border-radius:28px;
        background:linear-gradient(
            135deg,
            rgba(255,255,255,.95),
            rgba(248,250,252,.98)
        );
        box-shadow:
            0 25px 60px rgba(15,23,42,.08);
    }

    .empty-search .empty-icon{

        width:120px;
        height:120px;

        display:flex;
        align-items:center;
        justify-content:center;

        border-radius:50%;

        background:
            linear-gradient(
                135deg,
                #6366f1,
                #8b5cf6
            );

        color:#fff;

        font-size:52px;

        margin-bottom:24px;

        box-shadow:
            0 15px 40px rgba(99,102,241,.35);

        animation:floatIcon 3s ease-in-out infinite;
    }

    .empty-search h5{

        margin-bottom:10px;

        font-size:28px;
        font-weight:700;

        color:#0f172a;
    }

    .empty-search p{

        margin:0;

        max-width:320px;

        line-height:1.7;

        color:#64748b;

        font-size:15px;
    }

    .empty-search::after{

        content:"";

        margin-top:25px;

        width:80px;
        height:4px;

        border-radius:20px;

        background:linear-gradient(
            90deg,
            #6366f1,
            #8b5cf6,
            #ec4899
        );

        background-size:200% 100%;

        animation:gradientMove 3s linear infinite;
    }

    @keyframes floatIcon{

        0%,100%{
            transform:translateY(0);
        }

        50%{
            transform:translateY(-10px);
        }

    }

    @keyframes gradientMove{

        0%{
            background-position:0% 50%;
        }

        100%{
            background-position:100% 50%;
        }

    }

    @keyframes emptyFade{

        from{
            opacity:0;
            transform:translateY(15px);
        }

        to{
            opacity:1;
            transform:translateY(0);
        }

    }
</style>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Katalog Produk - Qieos</title>

    <?php include '../../script/headscript.php'; ?>
</head>

<body>
    <?php include '../components/sidebar.php'; ?>

    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <div class="container-fluid px-0 mt-5 mb-5">

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
                            <option value="makanan">🍔 Makanan</option>
                            <option value="minuman">🥤 Minuman</option>
                            <option value="jajanan">🍪 Jajanan</option>

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

                            <option value="name">🔤 Nama</option>
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
                                <?php echo $row['stock']; ?> Stok
                            </div>

                            <div class="price-floating">
                                Rp <?php echo number_format($row['sell_price'],0,',','.'); ?>
                            </div>

                        </div>

                        <div class="product-content">

                            <div class="product-meta">
                                <div class="category-pill">
                                    <i class="fas fa-tag"></i>
                                    <?php echo ucfirst($row['category']); ?>
                                </div>

                                <button
                                    class="star-btn <?= $row['starred'] ? 'active' : '' ?>"
                                    onclick="toggleStar(<?= $row['id'] ?>,this)">
                                    <i class="<?= $row['starred'] ? 'fas' : 'far' ?> fa-star"></i>
                                </button>
                            </div>

                            <h4 class="product-title">
                                <?php echo ucwords(strtolower($row['name'])); ?>
                            </h4>

                            <p class="product-desc">
                                <?php if($row['stock'] > 0): ?>
                                    <i class="fas fa-circle text-success"></i>
                                    Stok tersedia
                                <?php else: ?>
                                    <i class="fas fa-circle text-danger"></i>
                                    Stok habis
                                <?php endif; ?>
                            </p>

                            <div class="qty-box">

                                <?php if($row['stock'] > 0): ?>

                                    <div class="qty-control">

                                        <button class="qty-btn"
                                            onclick="decreaseQty('<?php echo $row['id']; ?>')">
                                            -
                                        </button>

                                        <input
                                            type="text"
                                            id="qty-<?php echo $row['id']; ?>"
                                            value="0"
                                            class="qty-input"
                                            data-stock="<?php echo $row['stock']; ?>"
                                            readonly>

                                        <button class="qty-btn qty-plus"
                                            onclick="increaseQty('<?php echo $row['id']; ?>')">
                                            +
                                        </button>

                                    </div>

                                <?php else: ?>

                                    <div class="out-stock-box">

                                        <i class="fas fa-box-open"></i>

                                        Tidak Ada Stok

                                    </div>

                                <?php endif; ?>

                            </div>

                            <?php if($row['stock'] > 0): ?>

                            <button
                                class="btn-checkout"
                                onclick="addToCart(
                                    this,
                                    '<?php echo $row['id']; ?>',
                                    '<?php echo addslashes($row['name']); ?>',
                                    <?php echo $row['sell_price']; ?>
                                )">

                                <i class="fas fa-cart-plus"></i>
                                Tambah
                                <i class="fas fa-arrow-right"></i>

                            </button>

                            <?php else: ?>

                            <button
                                class="btn-checkout btn-disabled"
                                disabled>

                                <i class="fas fa-ban"></i>
                                Stok Habis

                            </button>

                            <?php endif; ?>

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

                        // STOCK BADGE
                        let el = document.getElementById('stock-' + id);

                        if (el) {
                            el.innerHTML = `
                                <i class="fas fa-cube"></i>
                                ${stock} Stok
                            `;
                        }

                        // PRODUCT CARD
                        let card = document.querySelector(`.product-item[data-id="${id}"]`);

                        if (card) {

                            // CLASS STOCK
                            if (stock <= 0) {
                                card.classList.add('out-of-stock');
                            } else {
                                card.classList.remove('out-of-stock');
                            }

                            // STATUS TEXT
                            let desc = card.querySelector('.product-desc');

                            if (desc) {

                                if (stock <= 0) {

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

                            // UPDATE DATA STOCK INPUT
                            let input = document.getElementById('qty-' + id);

                            if (input) {

                                input.dataset.stock = stock;

                                // jika stok habis reset qty
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

    </script>

</body>

</html>