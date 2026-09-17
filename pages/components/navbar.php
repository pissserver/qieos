<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../script/connection.php';
}
?>
<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/navbar.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/navbar.css'); ?>">

<nav class="premium-navbar">

    <div class="premium-brand">
        <div class="brand-content">
            <h4>Dashboard</h4>
            <span>Point Of Sales Management</span>
        </div>
    </div>

    <div class="premium-actions">
        <!-- SEARCH -->
        <div class="search-trigger" id="searchTrigger" onclick="openSearch()">
            <i class="fas fa-search"></i>
            <span class="kbd-hint">⌘K</span>
        </div>

        <!-- WHAT'S NEW -->
        <div
            class="premium-action-btn"
            id="whatsNewBtn">

            <i class="fas fa-bullhorn"></i>

            <span class="cart-badge" style="font-size:8px;">
                NEW
            </span>

        </div>
        
        <?php if ($user['role'] == 'developer' || $user['role'] == 'staff kasir') { ?>
        
        <!-- CART -->
        <div
            class="premium-action-btn"
            onclick="openCart()">

            <i class="fas fa-shopping-cart"></i>

            <span id="cart-count"
                class="cart-badge d-none">
                0
            </span>
        </div>

        <!-- OMZET -->
        <?php
        $today = date('Y-m-d');

        $omzetQuery = $conn->prepare("
            SELECT SUM(total) as omzet
            FROM orders
            WHERE DATE(tanggal)=?
            AND status_payment!='cancelled'
        ");

        $omzetQuery->bind_param("s", $today);
        $omzetQuery->execute();

        $omzetResult = $omzetQuery->get_result();

        $omzet = 0;

        if ($omzetResult && $omzetResult->num_rows > 0) {
            $omzet = $omzetResult->fetch_assoc()['omzet'];

            if ($omzet === null) {
                $omzet = 0;
            }
        }
        ?>


        <div class="premium-omzet">

            <div class="premium-omzet-icon">
                <i class="fas fa-coins"></i>
            </div>

            <div>

                <span class="omzet-label">
                    Omzet Hari Ini
                </span>

                <div class="omzet-value">
                    Rp <span id="omzet-today">0</span>
                </div>

            </div>

        </div>

<?php } ?>

<!-- Global Search Overlay -->
<div class="search-overlay" id="searchOverlay">
    <div class="search-container">
        <div class="search-input-wrap">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" id="searchInput" placeholder="Cari halaman, produk, tenant..." autocomplete="off" spellcheck="false">
            <button class="search-close-btn" onclick="closeSearch()">ESC</button>
        </div>
        <div class="search-body" id="searchBody">
            <div class="search-recent" id="searchRecent"></div>
            <div id="searchResults"></div>
        </div>
        <div class="search-footer">
            <div class="search-hint">
                <span><kbd>&uarr;</kbd><kbd>&darr;</kbd> Navigasi</span>
                <span><kbd>&crarr;</kbd> Buka</span>
                <span><kbd>ESC</kbd> Tutup</span>
            </div>
            <div class="search-result-count" id="searchCount"></div>
        </div>
    </div>
</div>

<!-- Screensaver (appended to body via JS) -->

<script>
(function(){
    var overlay = document.getElementById('searchOverlay');
    var input = document.getElementById('searchInput');
    var results = document.getElementById('searchResults');
    var recentEl = document.getElementById('searchRecent');
    var countEl = document.getElementById('searchCount');
    var focusIndex = -1;
    var allItems = [];
    var debounceTimer = null;
    var isSearchOpen = false;
    var recentSearches = JSON.parse(localStorage.getItem('qieos_recent_search') || '[]');

    function renderRecent(){
        if(!recentSearches.length){ recentEl.innerHTML = ''; return; }
        var html = '<div class="search-section-label"><i class="fas fa-clock-rotate-left"></i> Pencarian Terakhir</div>';
        recentSearches.slice(0,5).forEach(function(s,i){
            html += '<div class="search-recent-item" onclick="window._searchSelect(\''+s.replace(/'/g,"\\'")+'\')"><i class="fas fa-clock"></i><span>'+escapeHtml(s)+'</span><span class="clear-recent" onclick="event.stopPropagation();window._removeRecent('+i+')"><i class="fas fa-times"></i></span></div>';
        });
        recentEl.innerHTML = html;
    }

    function addRecent(q){
        recentSearches = recentSearches.filter(function(s){ return s.toLowerCase() !== q.toLowerCase(); });
        recentSearches.unshift(q);
        if(recentSearches.length > 8) recentSearches.pop();
        localStorage.setItem('qieos_recent_search', JSON.stringify(recentSearches));
    }

    window._removeRecent = function(i){
        recentSearches.splice(i,1);
        localStorage.setItem('qieos_recent_search', JSON.stringify(recentSearches));
        renderRecent();
    };

    window._searchSelect = function(q){
        input.value = q;
        doSearch(q);
        input.focus();
    };

    function escapeHtml(t){
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(t));
        return d.innerHTML;
    }

    function doSearch(q){
        if(!q || q.length < 1){
            results.innerHTML = '';
            countEl.textContent = '';
            focusIndex = -1;
            allItems = [];
            renderRecent();
            return;
        }

        results.innerHTML = '<div class="search-loading"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>';
        recentEl.innerHTML = '';
        countEl.textContent = '';

        var role = '<?php echo $user["role"]; ?>';
        fetch(BASE_URL + '/pages/components/data/search-api.php?q='+encodeURIComponent(q)+'&role='+encodeURIComponent(role))
        .then(function(r){ return r.json(); })
        .then(function(data){
            if(data.status !== 'success') return;
            var r = data.results;
            var html = '';
            var total = 0;
            allItems = [];
            focusIndex = -1;

            if(r.pages && r.pages.length){
                html += '<div class="search-section-label"><i class="fas fa-compass"></i> Halaman</div>';
                r.pages.forEach(function(p){
                    var idx = allItems.length;
                    allItems.push(p.url);
                    html += '<div class="search-item" data-idx="'+idx+'" onclick="window._goSearchItem('+idx+')">';
                    html += '<div class="search-item-icon"><i class="'+p.icon+'"></i></div>';
                    html += '<div class="search-item-info"><div class="search-item-name">'+escapeHtml(p.name)+'</div><div class="search-item-meta">'+escapeHtml(p.category)+'</div></div>';
                    html += '<span class="search-item-badge badge-page">Halaman</span>';
                    html += '</div>';
                    total++;
                });
            }

            if(r.products && r.products.length){
                html += '<div class="search-section-label"><i class="fas fa-cube"></i> Produk</div>';
                r.products.forEach(function(p){
                    var idx = allItems.length;
                    allItems.push(p.url);
                    html += '<div class="search-item" data-idx="'+idx+'" onclick="window._goSearchItem('+idx+')">';
                    html += '<div class="search-item-icon product-icon"><i class="'+p.icon+'"></i></div>';
                    html += '<div class="search-item-info"><div class="search-item-name">'+escapeHtml(p.name)+'</div><div class="search-item-meta">'+escapeHtml(p.code)+' &middot; '+escapeHtml(p.category)+' &middot; '+p.price+'</div></div>';
                    html += '<span class="search-item-badge badge-product">Produk</span>';
                    html += '</div>';
                    total++;
                });
            }

            if(r.suppliers && r.suppliers.length){
                html += '<div class="search-section-label"><i class="fas fa-truck"></i> Supplier</div>';
                r.suppliers.forEach(function(s){
                    var idx = allItems.length;
                    allItems.push(s.url);
                    html += '<div class="search-item" data-idx="'+idx+'" onclick="window._goSearchItem('+idx+')">';
                    html += '<div class="search-item-icon supplier-icon"><i class="'+s.icon+'"></i></div>';
                    html += '<div class="search-item-info"><div class="search-item-name">'+escapeHtml(s.name)+'</div><div class="search-item-meta">'+escapeHtml(s.phone)+' &middot; '+escapeHtml(s.address)+'</div></div>';
                    html += '<span class="search-item-badge badge-supplier">Supplier</span>';
                    html += '</div>';
                    total++;
                });
            }

            if(r.customers && r.customers.length){
                html += '<div class="search-section-label"><i class="fas fa-users"></i> Customer</div>';
                r.customers.forEach(function(c){
                    var idx = allItems.length;
                    allItems.push(c.url);
                    html += '<div class="search-item" data-idx="'+idx+'" onclick="window._goSearchItem('+idx+')">';
                    html += '<div class="search-item-icon customer-icon"><i class="'+c.icon+'"></i></div>';
                    html += '<div class="search-item-info"><div class="search-item-name">'+escapeHtml(c.name)+'</div><div class="search-item-meta">'+escapeHtml(c.phone)+'</div></div>';
                    html += '<span class="search-item-badge badge-customer">Customer</span>';
                    html += '</div>';
                    total++;
                });
            }

            if(r.orders && r.orders.length){
                html += '<div class="search-section-label"><i class="fas fa-receipt"></i> Pesanan</div>';
                r.orders.forEach(function(o){
                    var idx = allItems.length;
                    allItems.push(o.url);
                    html += '<div class="search-item" data-idx="'+idx+'" onclick="window._goSearchItem('+idx+')">';
                    html += '<div class="search-item-icon" style="background:rgba(239,68,68,.1);color:#f87171"><i class="'+o.icon+'"></i></div>';
                    html += '<div class="search-item-info"><div class="search-item-name">'+escapeHtml(o.code)+'</div><div class="search-item-meta">'+o.date+' &middot; '+o.total+' &middot; '+escapeHtml(o.status)+'</div></div>';
                    html += '<span class="search-item-badge" style="background:rgba(239,68,68,.1);color:#f87171">Pesanan</span>';
                    html += '</div>';
                    total++;
                });
            }

            if(r.tenants && r.tenants.length){
                html += '<div class="search-section-label"><i class="fas fa-store"></i> Tenant</div>';
                r.tenants.forEach(function(t){
                    var idx = allItems.length;
                    allItems.push(t.url);
                    html += '<div class="search-item" data-idx="'+idx+'" onclick="window._goSearchItem('+idx+')">';
                    html += '<div class="search-item-icon tenant-icon"><i class="'+t.icon+'"></i></div>';
                    html += '<div class="search-item-info"><div class="search-item-name">'+escapeHtml(t.name)+'</div><div class="search-item-meta">'+escapeHtml(t.owner)+'</div></div>';
                    html += '<span class="search-item-badge badge-tenant">Tenant</span>';
                    html += '</div>';
                    total++;
                });
            }

            if(total === 0){
                html = '<div class="search-empty"><i class="fas fa-search"></i><p>Tidak ditemukan hasil untuk "<strong>'+escapeHtml(q)+'</strong>"</p></div>';
            }

            results.innerHTML = html;
            countEl.textContent = total + ' hasil ditemukan';
        })
        .catch(function(){
            results.innerHTML = '<div class="search-empty"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat pencarian</p></div>';
        });
    }

    window._goSearchItem = function(idx){
        if(allItems[idx]) window.location.href = allItems[idx];
    };

    window.openSearch = function(){
        overlay.classList.add('active');
        isSearchOpen = true;
        input.value = '';
        results.innerHTML = '';
        countEl.textContent = '';
        allItems = [];
        focusIndex = -1;
        renderRecent();
        history.pushState({search:true}, '');
        setTimeout(function(){ input.focus(); },100);
    };

    window.closeSearch = function(){
        overlay.classList.remove('active');
        isSearchOpen = false;
        input.value = '';
        results.innerHTML = '';
        countEl.textContent = '';
        allItems = [];
        focusIndex = -1;
    };

    overlay.addEventListener('click', function(e){
        if(e.target === overlay){
            closeSearch();
            history.back();
        }
    });

    input.addEventListener('input', function(){
        clearTimeout(debounceTimer);
        var v = this.value.trim();
        debounceTimer = setTimeout(function(){ doSearch(v); }, 180);
    });

    input.addEventListener('keydown', function(e){
        var items = results.querySelectorAll('.search-item');
        if(!items.length) return;

        if(e.key === 'ArrowDown'){
            e.preventDefault();
            focusIndex = Math.min(focusIndex + 1, items.length - 1);
            updateFocus(items);
        } else if(e.key === 'ArrowUp'){
            e.preventDefault();
            focusIndex = Math.max(focusIndex - 1, 0);
            updateFocus(items);
        } else if(e.key === 'Enter'){
            e.preventDefault();
            if(focusIndex >= 0 && allItems[focusIndex]){
                var q = input.value.trim();
                if(q) addRecent(q);
                window.location.href = allItems[focusIndex];
            }
        }
    });

    function updateFocus(items){
        items.forEach(function(it){ it.classList.remove('focused'); });
        if(focusIndex >= 0 && items[focusIndex]){
            items[focusIndex].classList.add('focused');
            items[focusIndex].scrollIntoView({block:'nearest'});
        }
    }

    document.addEventListener('keydown', function(e){
        if((e.ctrlKey || e.metaKey) && e.key === 'k'){
            e.preventDefault();
            if(overlay.classList.contains('active')) closeSearch();
            else openSearch();
        }
        if(e.key === 'Escape' && overlay.classList.contains('active')){
            closeSearch();
            history.back();
        }
    });

    window.addEventListener('popstate', function(e){
        if(isSearchOpen){
            overlay.classList.remove('active');
            isSearchOpen = false;
            input.value = '';
            results.innerHTML = '';
            countEl.textContent = '';
            allItems = [];
            focusIndex = -1;
        }
    });

    renderRecent();
})();
</script>

        <!-- PROFILE -->
        <div class="dropdown" style="position: relative; z-index: 1050;">

            <a href="#"
                class="text-decoration-none"
                data-bs-toggle="dropdown"
                data-bs-auto-close="true">

                <div class="premium-profile">

                    <img
                        src="<?php echo $user['photo'] ? BASE_URL . '/assets/img/uploads/' . $user['photo'] : BASE_URL . '/assets/img/default-avatar.jpg'; ?>"
                        class="premium-avatar">

                    <div class="premium-user">

                        <div class="premium-name">
                            <?php echo $user['fullname'] != '' ? $user['fullname'] : $_SESSION['username']; ?>
                        </div>

                        <div class="premium-role">
                            <?= ucwords(strtolower($user['role'])) ?>
                        </div>

                    </div>

                    <i class="fas fa-chevron-down text-white premium-chevron"></i>

                </div>

            </a>

            <div class="dropdown-menu dropdown-menu-end premium-dropdown">

                <a class="dropdown-item"
                href="<?php echo BASE_URL; ?>/pages/profile/profile.php">
                    <i class="fas fa-user-circle"></i>
                    <span>Profil</span>
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger"
                href="<?php echo BASE_URL; ?>/sessions/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign Out</span>
                </a>

            </div>

        </div>

    </div>

</nav>

<!-- Modal Cart -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(45deg,#6366f1,#8b5cf6); color:white;">
                <h5 class="mb-0 text-white"><i class="fas fa-shopping-cart"></i>&nbsp; Keranjang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="cart-items" class="cart-container"></div>
            </div>

            <div class="modal-footer cart-footer">
                <!-- TOTAL -->
                <div class="cart-total-box">
                    <div class="total-label">
                        <i class="fas fa-receipt me-1"></i> Total keseluruhan:
                    </div>

                    <div class="total-value">
                        <i class="fas fa-money-bill-wave"></i>&nbsp;
                        Rp <span id="cart-total">0</span>
                    </div>
                </div>

                <!-- BUTTON -->
                <button class="btn btn-checkout" onclick="checkout()">
                    Pesan <i class="fas fa-shopping-bag ms-1"></i>
                </button>

            </div>
        </div>
    </div>
</div>

<!-- Update Overlay -->
<div class="update-overlay" id="updateOverlay">
    <div class="update-modal">
        <div class="update-header">
            <div>
                <div class="update-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h4 id="updateTitle"></h4>
                <div class="update-meta">
                    <span id="updateVersion"></span>
                    <span id="updateType" style="color: #000;"></span>
                    <span id="updateDate"></span>
                </div>
            </div>
            <button class="closeUpdate">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="update-body">
            <h6 class="mb-3">
                <i class="fas fa-sparkles me-2"></i>
                What's New
            </h6>
            <div id="updateDetailList" class="mb-5"></div>
        </div>
    </div>
</div>

<script>
    $(document).on("click", ".closeUpdate", function () {
        $("#updateOverlay").fadeOut(150);
    });

    $(document).on("click", "#updateOverlay", function (e) {
        if (e.target === this) {
            $(this).fadeOut(150);
        }
    });

    // Show Latest Update Details
    function showLatestUpdate() {
        $.get(BASE_URL + "/pages/other/update-detail.php", function(res) {
            if (res.status != "success") {
                QToast("Error", "Tidak dapat memuat update terbaru", "error");
                return;
            }

            $("#updateTitle").text(res.update_name);
            $("#updateVersion").html(`<span class="stock-badge">${res.update_version}</span>`);
            $("#updateType").html(res.badge);
            $("#updateDate").text(res.update_date);

            let html = "";
            res.details.forEach(function(item) {
                html += `
                    <div class="update-item">
                        <i class="fas fa-check-circle"></i>
                        <div>${item.description}</div>
                    </div>
                `;
            });

            $("#updateDetailList").html(html);
            $("#updateOverlay").css("display","flex").hide().fadeIn(200);
        }, "json");
    }

    // Attach event to Whats New button
    document.getElementById("whatsNewBtn").addEventListener("click", showLatestUpdate);
    var mobileWnb = document.getElementById("whatsNewBtnMobile");
    if(mobileWnb) mobileWnb.addEventListener("click", showLatestUpdate);
</script>

<?php if ($user['role'] == 'developer' || $user['role'] == 'staff kasir') { ?>
<script>
    // Load cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    let cartModalInstance = null;

    function openCart() {
        if (!cartModalInstance) {
            cartModalInstance = new bootstrap.Modal(document.getElementById('cartModal'));
        }
        cartModalInstance.show();
    }

    function addToCart(btn, id, name, price, category){

        let qty = 1;
        let input = null;
        let isAdditional = category.toLowerCase() === 'additional';

        if (!isAdditional) {
            input = document.getElementById('qty-' + id);
            qty = parseInt(input.value || 0);

            if(qty <= 0){
                QToast('Tambahkan qty produk terlebih dahulu!', '', 'warning');
                return;
            }
        }

        let img = btn.closest('.product-item')
                    .querySelector('.product-img').src;

        let existing = cart.find(item => item.id == id);

        if(existing){
            if (!isAdditional) {
                existing.qty += qty;
            } else {
                QToast(name + ' sudah ditambahkan!', '', 'warning');
                return;
            }
        } else {
            cart.push({
                id,
                name,
                price,
                qty,
                photo: img,
                category
            });
        }

        updateCart();

        if (input) {
            input.value = 0;
        }

        QToast('Ditambahkan', name+' masuk keranjang', 'success');
    }

    function updateCart() {
        localStorage.setItem('cart', JSON.stringify(cart));

        let count = cart.length;
        let total = 0;
        let html = '';

        const cartBadge = document.getElementById('cart-count');

        // 👉 JIKA KOSONG
        if (cart.length === 0) {
            html = `
            <div class="empty-cart">
                <div class="empty-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>

                <h5>Keranjang kosong</h5>
                <p>Yuk tambahkan produk ke keranjang kamu</p>
                <a href="../sales/catalog.php" class="btn btn-primary mt-2">
                    Mulai Belanja
                </a>
            </div>
            `;

            document.getElementById('cart-items').innerHTML = html;

            // 🔴 badge disembunyikan
            cartBadge.innerText = 0;
            cartBadge.classList.add('d-none');

            // ❌ sembunyikan footer
            document.querySelector('.cart-footer').style.display = 'none';

            return;
        }

        // ✅ tampilkan footer
        document.querySelector('.cart-footer').style.display = 'flex';

        cart.forEach((item, index) => {
            let subtotal = item.qty * item.price;

            total += subtotal;

            html += `
                <div class="cart-card">

                    <!-- LEFT -->
                    <div class="cart-left">
                        <img src="${item.photo}" class="cart-img">

                        <div>
                            <div class="cart-title text-capitalize">${item.name}</div>

                            <div class="cart-meta">
                                <span class="badge-price">
                                    Rp ${Number(item.price).toLocaleString('id-ID', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    })}
                                </span>

                                ${
                                    item.category.toLowerCase() !== 'additional'
                                    ? `
                                        <span class="badge-qty">
                                            Qty: ${item.qty}
                                        </span>
                                    `
                                    : ''
                                }
                            </div>
                        </div>
                    </div>

                    <!-- CENTER -->
                    <div class="cart-qty">
                        ${
                            item.category.toLowerCase() !== 'additional'
                            ? `
                                <button onclick="changeQty(${index}, -1)">-</button>
                                <span>${item.qty}</span>
                                <button onclick="changeQty(${index}, 1)">+</button>
                            `
                            : ''
                        }
                    </div>

                    <!-- RIGHT -->
                    <div class="cart-right">
                        <div class="cart-subtotal">
                            Rp ${subtotal.toLocaleString()}
                        </div>

                        <div class="remove-btn" onclick="removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>

                </div>
            `;
        });

        // 🔥 UPDATE BADGE (TOTAL QTY)
        cartBadge.innerText = count > 99 ? '99+' : count;

        // tampilkan badge kalau ada isi
        if (count > 0) {
            cartBadge.classList.remove('d-none');
        } else {
            cartBadge.classList.add('d-none');
        }

        // render cart
        document.getElementById('cart-items').innerHTML = html;
        document.getElementById('cart-total').innerText = total.toLocaleString();
    }

    function changeQty(index, change) {
        cart[index].qty += change;

        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }

        updateCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        updateCart();
    }

    // Load cart on page load
    window.onload = function() {
        updateCart();
    };

    // Checkout
    function checkout() {
        if (cart.length === 0) {
            QToast('Keranjang kosong', '', 'warning');
            return;
        }

        fetch('../checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cart: cart })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {

                    QToast('Berhasil!', 'Pesanan berhasil dibuat', 'success');

                    const receiptUrl = `../receipt.php?id=${res.order_id}`;
                    window.open(receiptUrl, '_blank');

                    if (navigator.share) {
                        navigator.share({
                            title: 'Struk Pembelian',
                            text: 'Berikut struk pembelian',
                            url: receiptUrl
                        });
                    }

                    // reset cart
                    cart = [];
                    localStorage.removeItem('cart');
                    updateCart();

                    let modal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
                    modal.hide();
                }
            })
    }

    // Update Omzet
    function updateOmzet() {
        fetch(BASE_URL + '/pages/components/data/get-omzet.php')
            .then(res => res.json())
            .then(data => {
                const el = document.getElementById('omzet-today');

                el.style.transform = "scale(1.2)";
                el.style.transition = "0.2s";

                setTimeout(() => {
                    el.innerText = data.omzet.toLocaleString('id-ID');
                    el.style.transform = "scale(1)";
                }, 150);
            });
    }

    // jalan tiap 1 detik
    setInterval(updateOmzet, 3000);

    // pertama kali load
    updateOmzet();
</script>

<script>
    function animateValue(id, end, duration = 800) {
        const obj = document.getElementById(id);
        let startTimestamp = null;

        function step(timestamp) {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);

            const value = Math.floor(progress * end);
            obj.innerHTML = value.toLocaleString('id-ID');

            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                obj.innerHTML = end.toLocaleString('id-ID');

                // trigger pulse
                obj.classList.add("pulse");
                setTimeout(() => obj.classList.remove("pulse"), 600);
            }
        }

        window.requestAnimationFrame(step);
    }

    // dari PHP
    let omzet = <?php echo $omzet; ?>;
    animateValue("omzet-today", omzet, 800); // cepat & smooth
</script>

<script>
(function(){
    var userName = '<?php echo addslashes($user["fullname"] ? $user["fullname"] : $user["username"]); ?>';

    var ss = document.createElement('div');
    ss.className = 'screensaver';
    ss.id = 'screensaver';
    ss.innerHTML = '<div class="ss-aurora"></div><div class="ss-content"><div class="ss-clock-wrap"><div class="ss-clock-ring"></div><div class="screensaver-time" id="ssTime"></div></div><div class="screensaver-date" id="ssDate"></div><div class="screensaver-greeting">Halo, <span>'+userName+'</span></div><div class="ss-quote-divider"></div><div class="screensaver-quote" id="ssQuote"></div></div><div class="screensaver-hint">SENTUH ATAU TEKAN TOMBOL APAPUN</div>';
    document.body.appendChild(ss);

    var ssTime = document.getElementById('ssTime');
    var ssDate = document.getElementById('ssDate');
    var ssQuote = document.getElementById('ssQuote');
    var timer = null;
    var IDLE = 120000;
    var isActive = false;
    var clockInterval = null;

    var quotes = [
        "Kerja keras mengalahkan bakat saat bakat tidak bekerja keras.",
        "Produktivitas bukan tentang bekerja lebih lama, tapi bekerja lebih cerdas.",
        "Setiap detail kecil menentukan hasil akhir yang besar.",
        "Konsistensi adalah kunci dari setiap pencapaian besar.",
        "Jangan tunda pekerjaan yang bisa dilakukan hari ini.",
        "Fokus pada satu hal, selesaikan dengan sempurna.",
        "Disiplin adalah jembatan antara tujuan dan pencapaian.",
        "Kemajuan kecil setiap hari menghasilkan perubahan besar.",
        "Jangan takut membuat kesalahan, takutlah tidak belajar darinya.",
        "Tim yang solid menghasilkan karya yang luar biasa.",
        "Waktu tidak menunggu siapapun, manfaatkan setiap detiknya.",
        "Bekerja dengan passion membuat hidup tidak terasa seperti bekerja.",
        "Kesuksesan dimulai dari langkah kecil yang berani diambil.",
        "Profesionalisme terlihat dari bagaimana kamu menyelesaikan masalah.",
        "Komunikasi yang baik adalah fondasi dari tim yang sukses.",
        "Perencanaan yang matang menghemat waktu dan tenaga.",
        "Setiap tantangan adalah kesempatan untuk menjadi lebih baik.",
        "Hasil tidak pernah berbohong tentang usaha yang telah dilakukan.",
        "Pekerjaan yang ditunda hanya menumpuk menjadi masalah.",
        "Kolaborasi yang baik menghasilkan ide yang revolusioner.",
        "Jadilah solusi, bukan bagian dari masalah.",
        "Kualitas pekerjaan mencerminkan karakter seseorang.",
        "Jangan bandingkan kemajuanmu dengan orang lain.",
        "Investasi terbaik adalah investasi pada diri sendiri.",
        "Pikiran yang tenang menghasilkan keputusan yang tepat."
    ];

    var dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabit'];
    var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    function updateClock(){
        var now = new Date();
        var h = String(now.getHours()).padStart(2,'0');
        var m = String(now.getMinutes()).padStart(2,'0');
        var s = String(now.getSeconds()).padStart(2,'0');
        ssTime.innerHTML = h+'<span class="ss-time-colon">:</span>'+m+'<span class="ss-time-colon">:</span>'+s;
        ssDate.textContent = dayNames[now.getDay()]+', '+now.getDate()+' '+monthNames[now.getMonth()]+' '+now.getFullYear();
    }

    function showSS(){
        if(isActive) return;
        isActive = true;
        ssQuote.textContent = quotes[Math.floor(Math.random()*quotes.length)];
        updateClock();
        clockInterval = setInterval(updateClock, 1000);
        ss.classList.add('active');
    }

    function hideSS(){
        if(!isActive) return;
        isActive = false;
        ss.classList.remove('active');
        if(clockInterval){ clearInterval(clockInterval); clockInterval = null; }
        resetTimer();
    }

    function resetTimer(){
        clearTimeout(timer);
        timer = setTimeout(showSS, IDLE);
    }

    var events = ['mousemove','mousedown','keydown','scroll','touchstart','touchmove','click','resize'];
    events.forEach(function(evt){
        document.addEventListener(evt, function(){
            if(isActive) hideSS();
            else resetTimer();
        }, {passive:true});
    });

    updateClock();
    resetTimer();
})();
</script>

<?php } ?>