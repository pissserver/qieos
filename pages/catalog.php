<?php
include '../sessions/session.php';
$query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #eef2f7, #f8fafc);
    }

    .product-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        transition: all 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    .product-img {
        height: 230px;
        width: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .product-card:hover .product-img {
        transform: scale(1.08);
    }

    .price-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #111827;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .card-body {
        padding: 18px;
        text-align: center;
    }

    .name-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f3f4f6;
        padding: 6px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .category-badge {
        align-items: center;
        margin: 0 30px;
        gap: 6px;
        color: #fff;
        background: #cebd23;
        padding: 6px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .quantity-control {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 10px;
        gap: 10px;
    }

    .qty-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: #111827;
        color: white;
        font-size: 18px;
        transition: 0.2s;
    }

    .qty-btn:hover {
        background: #4f46e5;
    }

    .qty-input {
        width: 45px;
        text-align: center;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-weight: 600;
    }

    .add-to-cart {
        margin-top: 12px;
        width: 100%;
        border-radius: 12px;
        border: none;
        padding: 10px;
        background: linear-gradient(45deg, #6366f1, #8b5cf6);
        color: white;
        font-weight: 600;
        transition: 0.3s;
    }

    .add-to-cart:hover {
        opacity: 0.9;
    }

    .top-bar {
        margin-bottom: 25px;
    }

    .search-box {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 8px 12px;
        width: 250px;
    }

    .filter-select {
        border-radius: 10px;
        padding: 8px;
        border: 1px solid #e5e7eb;
    }

    .empty-search {
        text-align: center;
        padding: 50px 20px;
        color: #6b7280;
    }

    .empty-search .empty-icon {
        font-size: 60px;
        color: #6366f1;
        margin-bottom: 15px;
    }

    .empty-search h5 {
        font-weight: 600;
        color: #111827;
    }

    .empty-search p {
        font-size: 14px;
    }

    .empty-search {
        animation: fadeIn 0.3s ease;
    }

    .filter-select {
        width: auto;
        /* Lebar menyesuaikan isi */
        min-width: 80px;
        /* Lebar minimum agar tetap terlihat */
        max-width: 150px;
        /* Lebar maksimum */
        padding: 0.25rem 0.5rem;
        /* Lebih ringkas */
        font-size: 0.9rem;
        /* lebih kecil */
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Katalog Produk - Cartify</title>

    <?php include '../script/headscript.php'; ?>
</head>

<body>
    <?php include '../components/sidebar.php'; ?>

    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <div class="container-fluid px-0 mt-5">

            <div class="top-bar">
                <div class="row">
                    <div class="col-md-6 col-sm-7 mb-3">
                        <div class="input-group input-group-merge search-bar">
                            <span class="input-group-text" id="topbar-addon">
                                <svg
                                    class="icon icon-xs"
                                    x-description="Heroicon name: solid/search"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true">
                                    <path
                                        fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <input type="text" id="search" class="search-box" placeholder="Cari produk..." onkeyup="searchProduct()">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-5 mb-3 d-flex justify-content-md-end">
                        <select class="filter-select me-3" onchange="sortCategory(this.value)">
                            <option value="all">Semua</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="jajanan">Jajanan</option>
                        </select>

                        <select class="filter-select" onchange="sortProduct(this.value)">
                            <option value="latest">Terbaru</option>
                            <option value="low">Terendah</option>
                            <option value="high">Tertinggi</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row" id="product-list">

                <?php while ($row = mysqli_fetch_assoc($query)): ?>

                    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-4 product-item" data-name="<?php echo strtolower($row['product_name']); ?>" data-id="<?php echo $row['id']; ?>" data-category="<?php echo $row['category']; ?>" data-price="<?php echo $row['price']; ?>">
                        <div class="card product-card" id="product-<?php echo $row['id']; ?>">

                            <div style="position:relative">
                                <span class="price-badge">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></span>
                                <img src="../assets/img/uploads/<?php echo $row['photo']; ?>" class="product-img">
                            </div>

                            <div class="card-body">
                                <div class="name-badge">
                                    <i class="fas fa-box"></i>&nbsp;
                                    <?php echo ucwords(strtolower($row['product_name'])); ?>
                                </div>

                                <div class="category-badge mt-2">
                                    <i class="fas fa-tag"></i>&nbsp;
                                    <?php echo ucfirst($row['category']); ?>
                                </div>

                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="decreaseQty('<?php echo $row['id']; ?>')">-</button>
                                    <input type="text" id="qty-<?php echo $row['id']; ?>" value="0" class="qty-input" readonly>
                                    <button class="qty-btn" onclick="increaseQty('<?php echo $row['id']; ?>')">+</button>
                                </div>

                                <button class="add-to-cart" onclick="addToCart('<?php echo $row['id']; ?>','<?php echo $row['product_name']; ?>',<?php echo $row['price']; ?>)">Tambahkan &nbsp;<i class="fas fa-shopping-cart"></i></button>
                            </div>

                        </div>
                    </div>

                <?php endwhile; ?>

                <div id="empty-search" class="empty-search" style="display:none;">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>Produk tidak ditemukan</h5>
                    <p>Coba gunakan kata kunci lain</p>
                </div>
            </div>
        </div>
    </main>

    <?php include '../script/footscript.php'; ?>

    <script>
        function increaseQty(id) {
            let input = document.getElementById('qty-' + id);
            input.value = parseInt(input.value) + 1;
        }

        function decreaseQty(id) {
            let input = document.getElementById('qty-' + id);
            let val = parseInt(input.value);
            if (val > 0) input.value = val - 1;
        }

        function searchProduct() {
            let keyword = document.getElementById('search').value.toLowerCase();
            let items = document.querySelectorAll('.product-item');
            let found = false;

            items.forEach(item => {
                let name = item.getAttribute('data-name');

                if (name.includes(keyword)) {
                    item.style.display = 'block';
                    found = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // tampilkan / sembunyikan empty state
            document.getElementById('empty-search').style.display = found ? 'none' : 'block';
        }

        function sortCategory(category) {
            // Ambil semua elemen dengan class product-item
            const items = document.querySelectorAll('.product-item');

            items.forEach(item => {
                const itemCategory = item.getAttribute('data-category').toLowerCase();

                if (category === "all") {
                    // tampilkan semua
                    item.style.display = "block";
                } else {
                    // tampilkan hanya yang sesuai kategori
                    if (itemCategory === category.toLowerCase()) {
                        item.style.display = "block";
                    } else {
                        item.style.display = "none";
                    }
                }
            });
        }

        function sortProduct(type) {
            let container = document.getElementById('product-list');
            let items = Array.from(document.querySelectorAll('.product-item'));

            items.sort((a, b) => {
                let idA = parseInt(a.getAttribute('data-id'));
                let idB = parseInt(b.getAttribute('data-id'));
                let priceA = parseInt(a.getAttribute('data-price'));
                let priceB = parseInt(b.getAttribute('data-price'));

                if (type === 'latest') return idB - idA; // id besar duluan
                if (type === 'low') return priceA - priceB;
                if (type === 'high') return priceB - priceA;
                return 0;
            });

            items.forEach(item => container.appendChild(item));
        }
    </script>

</body>

</html>