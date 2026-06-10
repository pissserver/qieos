<?php
include '../../sessions/session.php';
?>

<style>
    body {
        background: #f8fafc;
    }

    /* ==========================
    PREMIUM TOOLBAR
    ========================== */

    .premium-toolbar{
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

    .search-modern input::placeholder{
        color:#64748b;
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
    ACTIONS
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
    DATE INPUT HIDDEN
    ========================== */

    .hidden-date{
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

        .premium-toolbar{
            padding:10px;
            gap:8px;
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

    /* =========================
    EMPTY SEARCH PREMIUM
    ========================= */

    .empty-search-order{

        position:relative;

        width:100%;
        max-width:700px;

        margin:40px auto;

        padding:60px 40px;

        text-align:center;

        border-radius:32px;

        overflow:hidden;

        backdrop-filter:blur(25px);

        background:
            linear-gradient(
                135deg,
                rgba(255,255,255,.96),
                rgba(248,250,252,.92)
            );

        border:1px solid rgba(255,255,255,.8);

        box-shadow:
            0 20px 60px rgba(15,23,42,.08),
            0 10px 30px rgba(99,102,241,.08);

        animation:emptyFade .5s ease;
    }

    /* Glow background */

    .empty-bg-circle{

        position:absolute;

        border-radius:50%;

        filter:blur(60px);

        opacity:.18;

        pointer-events:none;
    }

    .circle-1{

        width:220px;
        height:220px;

        background:#6366f1;

        top:-80px;
        left:-80px;
    }

    .circle-2{

        width:250px;
        height:250px;

        background:#ec4899;

        right:-100px;
        bottom:-100px;
    }

    /* Badge */

    .empty-badge{

        display:inline-flex;
        align-items:center;
        gap:8px;

        padding:8px 18px;

        border-radius:999px;

        background:
            linear-gradient(
                135deg,
                rgba(99,102,241,.12),
                rgba(236,72,153,.12)
            );

        color:#6366f1;

        font-size:13px;
        font-weight:700;

        margin-bottom:24px;
    }

    /* Icon */

    .empty-icon{

        width:140px;
        height:140px;

        margin:auto auto 30px;

        border-radius:50%;

        display:flex;
        align-items:center;
        justify-content:center;

        font-size:58px;
        color:white;

        background:
            linear-gradient(
                135deg,
                #6366f1,
                #8b5cf6,
                #ec4899
            );

        background-size:200% 200%;

        animation:
            floatIcon 4s ease-in-out infinite,
            gradientMove 5s linear infinite;

        box-shadow:
            0 25px 50px rgba(99,102,241,.30);
    }

    .empty-icon i{
        color:#fff;
    }

    /* Title */

    .empty-search-order h3{

        font-size:36px;
        font-weight:800;

        margin-bottom:15px;

        color:#0f172a;
    }

    /* Text */

    .empty-search-order p{

        max-width:500px;

        margin:auto;

        line-height:1.9;

        color:#64748b;

        font-size:16px;
    }

    /* Animated line */

    .empty-search-order::after{

        content:"";

        position:absolute;

        left:50%;
        bottom:0;

        transform:translateX(-50%);

        width:180px;
        height:5px;

        border-radius:999px;

        background:
            linear-gradient(
                90deg,
                #6366f1,
                #8b5cf6,
                #ec4899,
                #6366f1
            );

        background-size:300% 100%;

        animation:gradientMove 4s linear infinite;
    }

    /* Animations */

    @keyframes floatIcon{

        0%,100%{
            transform:translateY(0px);
        }

        50%{
            transform:translateY(-12px);
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
            transform:translateY(20px);
        }

        to{
            opacity:1;
            transform:translateY(0);
        }
    }

    /* =========================
    TABLET
    ========================= */

    @media(max-width:991px){

        .empty-search-order{

            padding:50px 30px;
        }

        .empty-search-order h3{

            font-size:30px;
        }

        .empty-icon{

            width:120px;
            height:120px;

            font-size:48px;
        }
    }

    /* =========================
    MOBILE
    ========================= */

    @media(max-width:576px){

        .empty-search-order{

            margin:20px auto;

            padding:35px 22px;

            border-radius:24px;
        }

        .empty-icon{

            width:95px;
            height:95px;

            font-size:38px;
        }

        .empty-search-order h3{

            font-size:24px;
        }

        .empty-search-order p{

            font-size:14px;
            line-height:1.7;
        }
    }

    /* ORDER CARD */
    .order-card {
        background: linear-gradient(135deg, #fdfcfb 0%, #536c8f 100%);
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
        border: 1px solid #f1f5f9;
    }

    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-id {
        font-weight: 600;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .order-date {
        font-size: 12px;
        color: #64748b;
    }

    /* BADGES */
    .badge-success {
        background: #dcfce7;
        color: #16a34a;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
        text-transform: capitalize;
        font-weight: 600;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
        text-transform: capitalize;
        font-weight: 600;
    }

    .badge-price {
        background: #e0f2fe;
        color: #0284c7;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 18px;
        font-weight: 700;
    }

    .order-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
    }

    /* BUTTONS */
    .btn-soft {
        border: none;
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 12px;
        margin-left: 5px;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-detail {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0284c7;
        margin-bottom: 3px;
    }

    .btn-detail:hover {
        background: #bae6fd;
    }

    .btn-cancel {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
        margin-bottom: 3px;
    }

    .btn-cancel:hover {
        background: #fecaca;
    }

    .btn-pay {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #10b981;
        margin-bottom: 3px;
    }

    .btn-pay:hover {
        background: #a7f3d0;
    }

    .empty {
        text-align: center;
        padding: 40px;
        color: #94a3b8;
    }

    /* PAGINATION */
    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        margin-top: 20px;
    }

    .page-item .page-link {
        border: none;
        border-radius: 10px;
        padding: 8px 14px;
        background: #d6dbdf;
        color: #334155;
        transition: 0.2s;
    }

    .page-item.active .page-link {
        background: #3b82f6;
        color: white;
    }

    .page-link:hover {
        background: #e2e8f0;
    }

    /* Modal Detail */
    .order-info-badges .badge {
        font-size: 0.95rem;
        padding: 0.4em 0.8em;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
    }

    .order-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 12px;
        background: #fff;
        box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);
        gap: 12px;
    }

    .order-item img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
    }

    .order-item-info {
        flex: 1;
    }

    .order-item-info strong {
        display: block;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .order-item-badges {
        display: flex;
        gap: 12px;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .badge-price {
        background: #6366f1;
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .badge-qty {
        background: #9ca3af;
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .order-item-qty {
        font-weight: 600;
        margin-right: 20px;
    }

    .order-item-subtotal {
        font-weight: 700;
        white-space: nowrap;
    }

    .order-total-box {
        background: #7c3aed;
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
    }

    .btn-print {
        margin-top: -5px;
        background: linear-gradient(270deg, #4b6cb7, #ff416c, #00b09b, #4b6cb7);
        background-size: 400% 400%;
        /* penting untuk animasi */

        color: #fff;
        border: none;
        padding: 6px 14px;
        font-size: 12px;
        border-radius: 20px;
        font-weight: 500;

        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;

        animation: gradientMove 5s ease infinite;
    }

    @keyframes gradientMove {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .btn-print:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
    }

    .btn-print:active {
        transform: scale(0.95);
    }

    .btn-print i {
        font-size: 12px;
    }

    .btn-print:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    .btn-print i {
        font-size: 12px;
    }
</style>

<!doctype html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pesanan - Cartify</title>

    <?php include '../../script/headscript.php'; ?>
</head>

<body>
    <?php include '../components/sidebar.php'; ?>
    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <div class="container-fluid px-0 mt-5">
            <div class="premium-toolbar mb-4">

                <div class="search-modern">

                    <div class="search-icon">
                        <i class="fas fa-search"></i>
                    </div>

                    <input
                        type="text"
                        id="searchOrder"
                        placeholder="Cari pesanan...">

                </div>

                <div class="toolbar-actions">

                    <div class="premium-filter">

                        <button class="toolbar-btn">
                            <i class="fas fa-calendar-alt"></i>
                        </button>

                        <input
                            type="date"
                            id="filterDate"
                            class="hidden-date">

                    </div>

                </div>

            </div>

            <!-- Data Container -->
            <div id="orders-container"></div>

            <div id="empty-search-order" class="empty-search-order" style="display:none;">

                <div class="empty-bg-circle circle-1"></div>
                <div class="empty-bg-circle circle-2"></div>

                <div class="empty-badge">
                    <i class="fas fa-search"></i>
                    Tidak Ditemukan
                </div>

                <div class="empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>

                <h3>Pesanan Tidak Ditemukan</h3>

                <p>
                    Tidak ada pesanan yang sesuai dengan pencarian Anda.
                    Coba gunakan kata kunci lain atau ubah filter pencarian.
                </p>
            </div>
        </div>
    </main>

    <!-- Modal Detail Order -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(45deg,#6366f1,#8b5cf6); color:white;">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-receipt"></i>&nbsp; Detail Pesanan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="order-detail-body">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin"></i> Memuat...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../script/footscript.php'; ?>

    <script>
        let selectedDate = '';
        let currentSearch = '';
        let timeout = null;

        const input = document.getElementById("filterDate");

        // SEARCH (pakai debounce biar gak spam server)
        document.getElementById('searchOrder').addEventListener('keyup', function() {
            clearTimeout(timeout);
            let val = this.value;

            timeout = setTimeout(() => {
                currentSearch = val;
                loadPage(1);
            }, 300);
        });

        // FILTER TANGGAL
        input.addEventListener("change", function() {
            selectedDate = this.value;
            loadPage(1);
        });

        // AJAX Pagination
        function loadPage(page) {
            if (page < 1) return;

            let xhr = new XMLHttpRequest();
            xhr.open("GET", "../components/data/order-data.php?page=" + page +
                "&date=" + selectedDate +
                "&search=" + currentSearch, true);

            xhr.onload = function() {
                if (this.status == 200) {
                    document.getElementById('orders-container').innerHTML = this.responseText;

                    const orders = document.querySelectorAll('.order-card');
                    const empty = document.getElementById('empty-search-order');
                    const pagination = document.getElementById('pagination');

                    if (orders.length === 0) {
                        empty.style.display = 'block';
                        if (pagination) pagination.style.display = 'none';
                    } else {
                        empty.style.display = 'none';
                        if (pagination) pagination.style.display = 'flex';
                    }
                }
            }
            xhr.send();
        }

        // pertama kali load
        loadPage(1);
    </script>

    <!-- Action -->
    <script>
        function payOrder(id, name) {
            Swal.fire({
                title: 'Bayar Pesanan?',
                html: `Yakin ingin menandai pesanan dari pelanggan <strong class="text-capitalize">${name}</strong> sebagai terbayar?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Bayar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('order-pay.php', {
                        order_id: id
                    }, function(response) {
                        // pastikan response sudah di-parse JSON
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', 'Pesanan telah terbayar.', 'success');
                            loadPage(1); // reload halaman pertama
                            updateOmzet(); // update omzet di navbar
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan saat memproses pembayaran.', 'error');
                        }
                    }, 'json');
                }
            });
        }

        function cancelOrder(id, name) {
            Swal.fire({
                title: 'Cancel Pesanan?',
                html: `Yakin ingin membatalkan pesanan dari pelanggan <strong class="text-capitalize">${name}</strong>?`,
                icon: 'warning',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Cancel!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('order-cancel.php', {
                        order_id: id
                    }, function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', 'Pesanan telah dibatalkan.', 'success');
                            loadPage(1); // reload halaman pertama
                            updateOmzet(); // update omzet di navbar
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan saat membatalkan pesanan.', 'error');
                        }
                    }, 'json');
                }
            });
        }

        function showDetail(id) {
            const modalEl = new bootstrap.Modal(document.getElementById('orderDetailModal'));
            modalEl.show();

            const container = document.getElementById('order-detail-body');
            container.innerHTML = `<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>`;

            fetch(`order-detail.php?id=${id}`)
                .then((res) => res.json())
                .then((data) => {
                    if (data.status === "success") {
                        const order = data.order;
                        const items = data.items;

                        let total = 0;
                        let html = `
                <div class="order-info-badges mb-3 d-flex justify-content-between align-items-start flex-wrap">

                    <!-- LEFT SIDE -->
                    <div class="d-flex flex-wrap">
                        <span class="badge bg-primary me-2 mb-2 text-capitalize">
                            <i class="fas fa-file-invoice me-1"></i>&nbsp; ${order.code}
                        </span>

                        <span class="badge bg-info text-dark me-2 mb-2">
                            <i class="fas fa-calendar me-1"></i>&nbsp; ${order.tanggal}
                        </span>

                        <span class="badge 
                            ${order.status_payment === 'paid' ? 'bg-success' : 'bg-warning text-dark'} mb-2 me-2">
                            <i class="fas ${order.status_payment === 'paid' ? 'fa-check-circle' : 'fa-spinner'} me-1"></i>&nbsp; 
                            ${order.status_payment === 'paid' ? 'Terbayar' : 'Menunggu Pembayaran'}
                        </span>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div>
                        <button class="btn btn-print text-white" onclick="printReceipt(${order.id})">
                            <i class="fas fa-print me-1 text-white"></i> Print
                        </button>
                    </div>

                </div>
                <hr>
                `;

                        items.forEach((item) => {
                            const subtotal = item.qty * item.price;
                            total += subtotal;
                            html += `
                    <div class="order-item">
                    <img src="../../assets/img/products/${item.photo}" alt="${item.product_name}">
                    <div class="order-item-info">
                        <strong class="text-capitalize">${item.product_name}</strong>
                        <div class="order-item-badges">
                        <span class="badge-price">Rp ${Number(item.price).toLocaleString()}</span>
                        <span class="badge-qty">Qty: ${item.qty}</span>
                        </div>
                    </div>
                    <div class="order-item-subtotal">Rp ${subtotal.toLocaleString()}</div>
                    </div>
                `;
                        });

                        html += `
                <div class="order-total-box mt-4">
                    <i class="fas fa-money-bill-wave"></i> Total keseluruhan: Rp ${total.toLocaleString()}
                </div>
                `;

                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle"></i> ${data.message}
                </div>
                `;
                    }
                })
                .catch((err) => {
                    container.innerHTML = `
                <div class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan memuat data.
                </div>
            `;
                    console.error(err);
                });
        }

        function printReceipt(id) {
            const receiptUrl = `../receipt.php?id=${id}`;

            // buka struk di tab baru
            const newWindow = window.open(receiptUrl, '_blank');

            // OPTIONAL: auto focus
            if (newWindow) {
                newWindow.focus();
            }

            // SHARE (jika user klik manual)
            if (navigator.share) {
                navigator.share({
                    title: 'Struk Pembelian',
                    text: 'Berikut struk pembelian',
                    url: receiptUrl
                }).catch(err => console.log(err));
            }
        }
    </script>
</body>

</html>