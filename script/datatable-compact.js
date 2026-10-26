/*!
 * Helper DataTables untuk halaman Master (Produk / Supplier).
 *
 * Dipakai lewat <script> di halaman yang membutuhkannya, SESUDAH
 * script/footscript.php dimuat (DataTables core + integrasi Bootstrap 5
 * sudah termuat di headscript.php).
 *
 * Isi:
 *   MP_TABLE.MOBILE_BP      - breakpoint mobile, dipakai bersama
 *   MP_TABLE.isMobile()     - helper cek breakpoint
 *   MP_TABLE.infoCallback() - chip "Menampilkan 1-5 dari 137 produk"
 *   MP_TABLE.placeActions() - bungkus search + tombol Tambah
 *   DataTable.ext.pager.mp_compact - pagination ringkas + ellipsis
 */
(function (global) {
    'use strict';

    var $ = global.jQuery;

    if (!$) {
        console.error('[MP_TABLE] jQuery belum termuat - helper tidak diinisialisasi.');
        return;
    }

    /* ------------------------------------------------------------------
       BREAKPOINT

       Semua halaman Master memakai angka yang sama. Halaman-halaman itu
       TIDAK defines breakpoint sendiri lagi, semuanya lewat MP_TABLE
       supaya keputusan "dom" dan lebar jendela pager tidak mungkin
       berbeda karena salah ketik.
       ------------------------------------------------------------------ */
    var helpers = {
        MOBILE_BP: 575.98,
        isMobile: function () {
            return global.innerWidth <= helpers.MOBILE_BP;
        },

        /* ------------------------------------------------------------------
           CHIP INFO "Menampilkan 1-5 dari 137 produk"

           Signature infoCallback di DataTables 1.13:
           (settings, start, end, max, total, pre) - BUKAN (start, end, total).
           Argumen pertama itu object settings, jadi harus dilewati.

             start = baris pertama (SUDAH 1-based, DataTables yang menambah 1)
             end   = baris terakhir di halaman ini
             max   = jumlah baris SEBELUM filter
             total = jumlah baris SETELAH filter
             pre   = string default DataTables (bahasa Inggris, tidak dipakai)

           Kalau total !== max berarti sedang difilter, tambahkan jumlah
           asal datanya ("dari 137 produk") di sebelah kanan.

           Catatan markup: .mp-info-sub sengaja diletakkan SEBIDAK anak
           langsung .mp-info, bukan di dalam .mp-info-txt. .mp-info dibuat
           flex dengan flex-wrap, jadi waktu baris info sempit di mobile
           bagian "dari N produk" bisa turun ke baris sendiri -
           .mp-info-txt yang isinya teks inline tidak bisa dipecah
           oleh flex-wrap.
           ------------------------------------------------------------------ */
        infoCallback: function (noun) {
            return function (settings, start, end, max, total, pre) {
                if (!total) return '';

                var sub = (total !== max)
                    ? '<span class="mp-info-sub">dari ' + max + ' ' + noun + '</span>'
                    : '';

                return '<span class="mp-info">'
                    + '<span class="mp-info-ico"><i class="fas fa-layer-group"></i></span>'
                    + '<span class="mp-info-txt">Menampilkan <b>' + start + '</b>&ndash;<b>' + end
                    + '</b> dari <b>' + total + '</b> ' + noun + '</span>'
                    + sub
                    + '</span>';
            };
        },

        /* ------------------------------------------------------------------
           WRAPPER SEARCH + TOMBOL "TAMBAH ..."

           DataTables membuat #xxx_filter; tombol Tambah ada di markup
           halaman (di luar wrapper tabel) supaya tidak ikut hilang saat
           container diisi ulang. Fungsi ini memindahkan keduanya ke satu
           wrapper flex supaya search dan tombol jadi satu baris rapi.

           Dipanggil ulang setelah setiap init karena wrapper ikut hilang
           saat DataTable di-destroy / markup di-rewrite.
           ------------------------------------------------------------------ */
        placeActions: function (filterSelector, buttonSelector) {
            var $filter = $(filterSelector);
            if (!$filter.length) return;

            if (!$filter.parent().hasClass('table-action-wrapper')) {
                $filter.wrap('<div class="table-action-wrapper"></div>');
            }

            $(buttonSelector).show().appendTo($filter.parent());
        }
    };

    /* ------------------------------------------------------------------
       PAGER RINGKAS + ELLIPSIS KONTEKSTUAL
       (disimpan sebagai DataTable.ext.pager.mp_compact)

       Kenapa tidak pakai "simple_numbers" bawaan?
       simple_numbers -> _numbers() di dalam jquery.dataTables.js, yang
       ukuran jendelanya diambil dari DataTable.ext.pager.numbers_length
       (default 7). Di tengah daftar _numbers() memunculkan
       numbers_length-3 = 4 nomor sekaligus, contoh:

           ‹ 1 … 4 5 6 7 … 20 ›

       Kalau tabelnya panjang, pilnya jadi kandidat panjang dan memenuhi
       hampir seluruh baris bawah tabel. Pager di bawah menggantinya
       dengan window yang ukurannya ditentukan eksplisit per breakpoint:

           mobile (<= 575.98) : 3 nomor ->  ‹ 1 … 4 5 6 … 20 ›
           tablet / desktop    : 5 nomor ->  ‹ 1 … 3 4 5 6 7 … 20 ›

       Halaman pertama (1) dan halaman terakhir (N) SELALU dikunci di dua
       ujung, jadi user tidak pernah kehilangan akses ke awal/akhir daftar.
       Kalau rentang di tengah tidak ada yang tersembunyi (mis. hanya 3
       halaman), ellipsis tidak dirender sama sekali - tidak ada "…" yang
       menggantung.

       Cara kerja: DataTable.ext.pager[type] dipanggil tiap draw sebagai
       pager(page, pages) dengan page 0-based, dan harus mengembalikan array
       tombol. Scalar = satu tombol ("previous"/"next"/"ellipsis"/index
       halaman 0-based); array = wrapper. Renderer Bootstrap 5 membungkus
       semuanya jadi <li class="page-item"> + <a class="page-link">, dan
       "ellipsis" otomatis dapat class "disabled" jadi tidak bisa diklik.

       Cara mendaftarkan ini idempoten, jadi aman kalau file ini Termuat
       ulang oleh halaman yang tidak pernah meng-unload halaman.
       ------------------------------------------------------------------ */
    if (global.DataTable && global.DataTable.ext && global.DataTable.ext.pager) {
        global.DataTable.ext.pager.mp_compact = function (page, pages) {
            var last = pages - 1;

            /* Degenerate: 1 halaman -> ‹ 1 ›, 0 halaman (tabel kosong) -> ‹ ›.
               Samakan dengan output _numbers() bawaan yang memakai
               _range(0, pages): pages=1 -> [0], pages=0 -> []. */
            if (last <= 0) {
                return last === 0
                    ? ['previous', 0, 'next']
                    : ['previous', 'next'];
            }

            /* 3 nomor di mobile, 5 di tablet/desktop */
            var span = helpers.isMobile() ? 3 : 5;
            var half = Math.floor((span - 1) / 2);

            /* Jendela digeser seperlunya supaya halaman aktif di tengah,
               tapi tetap menyisakan ruang untuk halaman terakhir. */
            var start = page - half;
            if (start < 0) start = 0;
            if (start > last - (span - 1)) start = last - (span - 1);
            if (start < 0) start = 0;

            var end = Math.min(start + span - 1, last);
            var buttons = [];

            /* Jendela belum mulai dari halaman 1 -> kunci halaman pertama */
            if (start > 0) {
                buttons.push(0);
                if (start > 1) buttons.push('ellipsis');
            }

            for (var i = start; i <= end; i++) buttons.push(i);

            /* Jendela belum sampai halaman terakhir -> kunci halaman terakhir */
            if (end < last) {
                if (end < last - 1) buttons.push('ellipsis');
                buttons.push(last);
            }

            return ['previous'].concat(buttons, ['next']);
        };
    } else {
        console.error('[MP_TABLE] DataTable.ext.pager tidak ditemukan - pagination ringkas tidak dipasang.');
    }

    global.MP_TABLE = helpers;

})(window);