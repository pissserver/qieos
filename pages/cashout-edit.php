<?php
include '../sessions/session.php';

$query = mysqli_query($conn, "SELECT cashouts.*, cashout_categories.category_name FROM cashouts LEFT JOIN cashout_categories ON cashouts.category_id = cashout_categories.id WHERE cashouts.id='" . $_GET['id'] . "'");
$row = mysqli_fetch_assoc($query);
?>

<style>
    .modal-content {
        border-radius: 12px;
    }

    .btn-dark {
        background: linear-gradient(135deg, #2c3e50, #000000);
        border: none;
    }

    .btn-dark:hover {
        background: linear-gradient(135deg, #000000, #2c3e50);
    }

    /* Modal utama */
    .custom-modal {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        animation: fadeInUp 0.3s ease;
    }

    /* Animasi muncul */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Icon bulat */
    .modal-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: linear-gradient(135deg, #4e73df, #224abe);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    /* Input modern */
    .input-custom {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
    }

    .input-custom:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, 0.2);
    }

    /* Button save */
    .btn-save {
        border-radius: 10px;
        background: linear-gradient(135deg, #4e73df, #224abe);
        border: none;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #224abe, #4e73df);
    }

    /* Tombol batal */
    .btn-light {
        border-radius: 10px;
    }
</style>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Kas Keluar - Cartify</title>

    <?php include '../script/headscript.php'; ?>
</head>

<body>
    <?php include '../components/sidebar.php'; ?>

    <main class="content">
        <?php include '../components/navbar.php'; ?>

        <div class="row mt-5">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Edit Kas Keluar</h5>
                            <small class="text-muted">Edit data kas keluar</small>
                        </div>

                        <!-- BUTTON TAMBAH KAS KELUAR -->
                        <a href="cashout.php" class="btn btn-dark btn-sm px-3 text-white" >
                            <i class="fas fa-plus-circle me-1"></i> Tambah Kas Keluar
                        </a>
                    </div>

                    <div class="card-body">

                        <form id="cashOutForm" action="cashout-action.php?action=edit" method="POST" enctype="multipart/form-data">

                            <div class="row">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                
                                <!-- Tanggal -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                        <input
                                            type="date"
                                            name="cashout_date"
                                            class="form-control"
                                            value="<?php echo $row['cashout_date']; ?>"
                                            required>
                                    </div>
                                </div>

                                <!-- Kategori -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Kategori</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-box"></i>
                                        </span>
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                                            <?php
                                            $categoryQuery = "SELECT * FROM cashout_categories ORDER BY category_name ASC";
                                            $categoryResult = $conn->query($categoryQuery);
                                            if ($categoryResult->num_rows > 0) {
                                                while ($rowCategory = $categoryResult->fetch_assoc()) {
                                                    echo "<option value='" . $rowCategory['id'] . "'>" . $rowCategory['category_name'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Nama Pengeluaran -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Nama Pengeluaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-box"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="expense_name"
                                            class="form-control"
                                            placeholder="Masukkan nama pengeluaran"
                                            value="<?php echo $row['expense_name']; ?>"
                                            required>
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Jumlah</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-sort-numeric-up"></i>
                                        </span>
                                        <input
                                            type="number"
                                            name="quantity"
                                            class="form-control"
                                            placeholder="Jumlah pengeluaran"
                                            value="<?php echo $row['quantity']; ?>"
                                            required>
                                    </div>
                                </div>

                                <!-- Satuan -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Satuan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-weight"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="unit"
                                            class="form-control"
                                            placeholder="Satuan (misal: pcs, kg, liter)"
                                            value="<?php echo $row['unit']; ?>"
                                            required>
                                    </div>
                                </div>

                                <!-- Harga -->
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </span>
                                        <input
                                            type="number"
                                            name="price"
                                            class="form-control"
                                            placeholder="Harga per satuan"
                                            value="<?php echo $row['price']; ?>"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <!-- Tombol -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" name="save_product" class="btn btn-primary">
                                    Simpan Edit
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

        <!-- Table Cashout -->
        <div class="mb-5">
            <div id="cashoutContainer">
                <?php include '../components/tables/cashout-table.php'; ?>
            </div>
        </div>
    </main>


    <?php include '../script/footscript.php'; ?>


    <!-- Cashout Script -->
    <script>
        document.getElementById('cashOutForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Server error");
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // 🔥 UPDATE FORM VALUE TANPA RELOAD
                    form.cashout_date.value = data.data.cashout_date;
                    form.category.value = data.data.category;
                    form.expense_name.value = data.data.expense_name;
                    form.quantity.value = data.data.quantity;
                    form.unit.value = data.data.unit;
                    form.price.value = data.data.price;
                    
                    loadCategory(currentCategory);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan server'
                });
            });
        });
    </script>

    <!-- Cashkout list (table data) -->
    <script>
        let currentPage = 1;
        const perPage = 6;
        let currentCategory = 'all';

        /* =========================
        INIT (SEARCH + PAGINATION)
        ========================= */
        function initCashout(){
            currentPage = 1;

            const search = document.getElementById("searchInput");

            if(search){
                search.addEventListener("keyup", ()=>{
                    currentPage = 1;
                    filterData();
                });
            }

            // DATE RANGE PICKER
            if($('#dateRange').length){
                $('#dateRange').daterangepicker({
                    autoUpdateInput: false,
                    opens: 'left',
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    },
                    ranges: {
                    'Hari Ini': [moment(), moment()],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });

                // APPLY
                $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(
                        picker.startDate.format('YYYY-MM-DD') + ' - ' + 
                        picker.endDate.format('YYYY-MM-DD')
                    );
                    currentPage = 1;
                    filterData();
                });

                // CLEAR
                $('#dateRange').on('cancel.daterangepicker', function() {
                    $(this).val('');
                    currentPage = 1;
                    filterData();
                });
            }

            filterData();
        }

        /* =========================
        ACTIVE TAB
        ========================= */
        function setActiveTab(cat){
            document.querySelectorAll('.nav-link').forEach(el=>{
                el.classList.remove('active');
            });

            document.querySelectorAll('.nav-link').forEach(el=>{
                if(el.getAttribute('onclick')?.includes(`'${cat}'`)){
                    el.classList.add('active');
                }
            });
        }

        /* =========================
        FILTER DATA
        ========================= */
        function filterData(){
            let keyword = document.getElementById("searchInput")?.value.toLowerCase() || "";
            let dateRange = document.getElementById("dateRange")?.value || "";

            let startDate = null;
            let endDate = null;

            // PARSE DATE RANGE
            if(dateRange){
                let split = dateRange.split(" - ");
                startDate = new Date(split[0]);
                endDate = new Date(split[1]);

                // biar inclusive (sampai akhir hari)
                endDate.setHours(23,59,59,999);
            }

            let cards = document.querySelectorAll("#cashList > div");
            let visible = [];
            let total = 0;

            cards.forEach(card => {
                let text = card.innerText.toLowerCase();
                let cardDate = card.getAttribute("data-date");
                let amount = parseInt(card.getAttribute("data-amount")) || 0;

                let show = true;

                // 🔍 SEARCH FILTER
                if(!text.includes(keyword)){
                    show = false;
                }

                // 📅 DATE FILTER
                if(dateRange && cardDate){
                    let d = new Date(cardDate);
                    if(d < startDate || d > endDate){
                        show = false;
                    }
                }

                if(show){
                    visible.push(card);
                    total += amount;
                }
            });

            // HIDE ALL
            cards.forEach(c => c.style.display = "none");

            // PAGINATION
            let start = (currentPage - 1) * perPage;
            let end = start + perPage;

            visible.forEach((card, i)=>{
                if(i >= start && i < end){
                    card.style.display = "block";
                }
            });

            // EMPTY STATE
            const empty = document.getElementById("emptyState");
            if(empty){
                empty.classList.toggle("d-none", visible.length !== 0);
            }

            // RESULT COUNT
            const result = document.getElementById("resultCount");
            if(result){
                result.innerText = visible.length + " data";
            }

            // 💰 TOTAL DINAMIS (INI YANG KAMU MAU)
            const totalEl = document.getElementById("totalAmount");
            if(totalEl){
                totalEl.innerText = "Rp " + total.toLocaleString('id-ID');
            }

            renderPagination(visible.length);
        }

        /* =========================
        PAGINATION
        ========================= */
        function renderPagination(total){
            let totalPage = Math.ceil(total / perPage);
            let html = "";

            if(totalPage <= 1){
                document.getElementById("pagination").innerHTML = "";
                return;
            }

            // PREVIOUS
            html += `
                <button 
                    class="btn btn-sm btn-light me-1 ${currentPage==1?'disabled':''}"
                    onclick="goPage(${currentPage-1})">
                    «
                </button>
            `;

            let maxVisible = 5; // jumlah angka tampil
            let start = Math.max(1, currentPage - 2);
            let end = Math.min(totalPage, currentPage + 2);

            // kalau awal jauh → tampilkan 1 + ...
            if(start > 1){
                html += `<button class="btn btn-sm btn-light me-1" onclick="goPage(1)">1</button>`;
                if(start > 2){
                    html += `<span class="mx-1">...</span>`;
                }
            }

            // PAGE NUMBERS
            for(let i = start; i <= end; i++){
                html += `
                    <button 
                        class="btn btn-sm ${i==currentPage?'btn-primary':'btn-light'} me-1"
                        onclick="goPage(${i})">
                        ${i}
                    </button>
                `;
            }

            // kalau akhir jauh → tampilkan ... + last
            if(end < totalPage){
                if(end < totalPage - 1){
                    html += `<span class="mx-1">...</span>`;
                }
                html += `
                    <button class="btn btn-sm btn-light me-1" onclick="goPage(${totalPage})">
                        ${totalPage}
                    </button>
                `;
            }

            // NEXT
            html += `
                <button 
                    class="btn btn-sm btn-light ms-1 ${currentPage==totalPage?'disabled':''}"
                    onclick="goPage(${currentPage+1})">
                    »
                </button>
            `;

            document.getElementById("pagination").innerHTML = html;
        }

        function goPage(p){
            let totalPage = Math.ceil(document.querySelectorAll("#cashList > div").length / perPage);

            if(p < 1 || p > totalPage) return;

            currentPage = p;
            filterData();
        }

        /* =========================
        AJAX CATEGORY (FIX UTAMA)
        ========================= */
        function loadCategory(cat){
            currentCategory = cat;


            fetch("../components/tables/cashout-table.php?category=" + cat)
            .then(res => res.text())
            .then(html => {
                document.getElementById("cashoutContainer").innerHTML = html;

                setActiveTab(cat);

                // ⭐ WAJIB: RE-INIT SETELAH AJAX
                initCashout();
            });
        }

        /* =========================
        DELETE
        ========================= */
        function deleteData(id, name){
            Swal.fire({
                title:'Hapus?',
                icon:'warning',
                html:`Yakin ingin menghapus data pengeluaran <strong class="text-capitalize">${name}</strong>?`,
                showCancelButton:true
            }).then(r=>{
                if(r.isConfirmed){

                    fetch(`cashout-action.php?action=delete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status === 'success'){
                            Swal.fire({
                                icon:'success',
                                title:'Berhasil',
                                text:data.message,
                                timer:1200,
                                showConfirmButton:false
                            });

                            // 🔥 reload list tanpa reload page
                            loadCategory(currentCategory);

                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });

                }
            });
        }

        /* =========================
        FIRST LOAD
        ========================= */
        document.addEventListener("DOMContentLoaded", function(){
            initCashout();
        });
    </script>

</body>

</html>