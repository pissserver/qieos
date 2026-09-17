<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pesanan - Qieos</title>

    <?php include '../../script/headscript.php'; ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/order.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/order.css'); ?>">
</head>

<body>
    <?php include '../components/sidebar.php'; ?>
    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <?php $blue_strip_icon='fa-receipt'; $blue_strip_title='Pesanan'; $blue_strip_subtitle='Daftar pesanan penjualan di kantin'; ?>

        <div class="container-fluid px-0 mt-5">
            <?php include '../components/blue-strip.php'; ?>

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
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
            QConfirm('Konfirmasi Pembayaran?', 'Pesanan ' + name + ' akan ditandai sebagai lunas.', {confirmText:'Bayar', icon:'fa-money-bill-wave', confirmClass:'q-confirm-btn-success', iconClass:'q-confirm-icon-success'}).then(function(ok){
                if(ok){
                    $.post('order-pay.php', {
                        order_id: id
                    }, function(response) {
                        // pastikan response sudah di-parse JSON
                        if (response.status === 'success') {
                            QToast('Berhasil!', 'Pesanan telah terbayar.', 'success');
                            loadPage(1); // reload halaman pertama
                            updateOmzet(); // update omzet di navbar
                        } else {
                            QToast('Gagal!', response.message || 'Terjadi kesalahan saat memproses pembayaran.', 'error');
                        }
                    }, 'json');
                }
            });
        }

        function cancelOrder(id, name) {
            QConfirm('Batalkan Pesanan?', 'Pesanan ' + name + ' akan dibatalkan. Stok akan dikembalikan.', {confirmText:'Batalkan', icon:'fa-ban', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
                if(ok){
                    $.post('order-cancel.php', {
                        order_id: id
                    }, function(response) {
                        if (response.status === 'success') {
                            QToast('Berhasil!', 'Pesanan telah dibatalkan.', 'success');
                            loadPage(1); // reload halaman pertama
                            updateOmzet(); // update omzet di navbar
                        } else {
                            QToast('Gagal!', response.message || 'Terjadi kesalahan saat membatalkan pesanan.', 'error');
                        }
                    }, 'json');
                }
            });
        }

        let orderDetailModalInstance = null;

        function showDetail(id) {
            if (!orderDetailModalInstance) {
                orderDetailModalInstance = new bootstrap.Modal(document.getElementById('orderDetailModal'));
            }
            orderDetailModalInstance.show();

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