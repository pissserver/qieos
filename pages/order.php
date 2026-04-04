<?php
    include '../sessions/session.php';
?>

    <style>
        body { background:#f8fafc; }
        .search-box {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            width: 250px;
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

        .date-picker-card {
            max-width: 120px;   /* lebih kecil */
            padding: 0.5rem;    /* lebih ringkas */
            cursor: pointer;
            user-select: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            background-color: #f8f9fa;
        }
        .date-picker-wrapper {
            position: relative;
            width: 30px;   /* ikon lebih kecil */
            height: 30px;
            margin: 0 auto;
        }
        input[type="date"] {
            position: absolute;
            width: 30px;
            height: 30px;
            opacity: 0;
            cursor: pointer;
            top: 0; left: 0;
        }
        .calendar-icon {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 18px;    /* lebih kecil */
            color: #1F2937;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 30px;
            width: 30px;
            pointer-events: none;
        }

        /* ORDER CARD */
        .order-card {
            background: linear-gradient(135deg, #fdfcfb 0%, #536c8f 100%);
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
            border: 1px solid #f1f5f9;
        }
        .order-card:hover { transform:translateY(-3px);box-shadow:0 12px 25px rgba(0,0,0,0.08); }
        .order-header { display:flex;justify-content:space-between;align-items:center; }
        .order-id { font-weight:600;font-size:16px;display:flex;align-items:center;gap:6px; }
        .order-date { font-size:12px;color:#64748b; }

        /* BADGES */
        .badge-success {
            background:#dcfce7;color:#16a34a;padding:5px 12px;border-radius:999px;font-size:12px;
            display:flex;align-items:center;gap:5px;
            text-transform: capitalize;
            font-weight: 600;
        }

        .badge-warning {
            background:#fef3c7;color:#92400e;padding:5px 12px;border-radius:999px;font-size:12px;
            display:flex;align-items:center;gap:5px;
            text-transform: capitalize;
            font-weight: 600;
        }

        .badge-price {
            background:#e0f2fe;color:#0284c7;padding:5px 12px;border-radius:999px;font-size:12px;
            display:flex;align-items:center;gap:5px;
            font-size:18px;font-weight:700;
        }

        .order-body { display:flex;justify-content:space-between;align-items:center;margin-top:12px; }

        /* BUTTONS */
        .btn-soft {
            border:none;padding:6px 12px;border-radius:10px;font-size:12px;margin-left:5px;
            transition:0.2s;display:inline-flex;align-items:center;gap:5px;
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

        .empty { text-align:center;padding:40px;color:#94a3b8; }

        /* PAGINATION */
        .pagination { display:flex;gap:6px;justify-content:center;margin-top:20px; }
        .page-item .page-link {
            border:none;border-radius:10px;padding:8px 14px;background: #d6dbdf;color:#334155;transition:0.2s;
        }
        .page-item.active .page-link { background:#3b82f6;color:white; }
        .page-link:hover { background:#e2e8f0; }

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
            background-size: 400% 400%; /* penting untuk animasi */

            color: #fff;
            border: none;
            padding: 6px 14px;
            font-size: 12px;
            border-radius: 20px;
            font-weight: 500;

            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
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
            box-shadow: 0 6px 14px rgba(0,0,0,0.25);
        }

        .btn-print:active {
            transform: scale(0.95);
        }

        .btn-print i {
            font-size: 12px;
        }

        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
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

    <?php include '../script/headscript.php'; ?>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <div class="container-fluid px-0 mt-5">
            <div class="row mb-3">
                <div class="col-md-6 col-sm-10">
                    <div class="input-group input-group-merge search-bar">
                        <span class="input-group-text" id="topbar-addon">
                            <svg
                                class="icon icon-xs"
                                x-description="Heroicon name: solid/search"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </span>
                        <input type="text" id="searchOrder" class="search-box" placeholder="Cari pesanan...">
                    </div>
                </div>

                <div class="col-md-6 col-sm-2 mb-3 d-flex justify-content-md-end justify-content-sm-end">
                    <div class="card date-picker-card shadow-sm">
                        <div class="date-picker-wrapper">
                            <input type="date" id="filterDate" />
                            <div class="calendar-icon">&#xf133;</div> <!-- ikon kalender fontawesome -->
                        </div>
                    </div>
                </div>
            </div>

            <div id="orders-container"></div>
            <div id="empty" class="empty" style="display:none;">
                <div class="empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h5>Pesanan tidak ditemukan</h5>
                <p>Coba gunakan kata kunci lain / filter tanggal lain</p>
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

    <?php include '../script/footscript.php'; ?>

    <script>
        let selectedDate = '';
        let currentSearch = '';
        let timeout = null;

        const input = document.getElementById("filterDate");

        // SEARCH (pakai debounce biar gak spam server)
        document.getElementById('searchOrder').addEventListener('keyup', function(){
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
        function loadPage(page){
            if(page < 1) return;

            let xhr = new XMLHttpRequest();
            xhr.open("GET", "../components/data/order-data.php?page=" + page + 
                    "&date=" + selectedDate + 
                    "&search=" + currentSearch, true);

            xhr.onload = function(){
                if(this.status == 200){
                    document.getElementById('orders-container').innerHTML = this.responseText;

                    const orders = document.querySelectorAll('.order-card');
                    const empty = document.getElementById('empty');
                    const pagination = document.getElementById('pagination');
                    
                    if(orders.length === 0){
                        empty.style.display = 'block';
                        if(pagination) pagination.style.display = 'none';
                    } else {
                        empty.style.display = 'none';
                        if(pagination) pagination.style.display = 'flex';
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
        function payOrder(id){
            Swal.fire({
                title: 'Bayar Pesanan?',
                text: "Apakah anda yakin ingin membayar pesanan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Bayar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('order-pay.php', {order_id: id}, function(response){
                        // pastikan response sudah di-parse JSON
                        if(response.status === 'success'){
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

        function cancelOrder(id){
            Swal.fire({
                title: 'Cancel Pesanan?',
                text: "Apakah anda yakin ingin membatalkan pesanan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Cancel!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('order-cancel.php', {order_id: id}, function(response){
                        if(response.status === 'success'){
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
                        <span class="badge bg-primary me-2 mb-2">
                            <i class="fas fa-user me-1"></i>&nbsp; ${order.customer_name}
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
                    <img src="../assets/img/uploads/${item.photo}" alt="${item.product_name}">
                    <div class="order-item-info">
                        <strong>${item.product_name}</strong>
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

        function printReceipt(id){
            const receiptUrl = `../pages/receipt.php?id=${id}`;

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