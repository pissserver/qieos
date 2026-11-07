<?php
include '../../sessions/session.php';
include __DIR__ . '/stock-mutasi-data.php';

/* ============================================================
   Endpoint panel "Mutasi Stok" (pages/stock/stock.php).

   Mengembalikan HTML dua pane (Gudang & Kantin) untuk satu produk,
   langsung dirender ke panel kanan lewat fetch. Tiap pane berisi
   4 kartu ringkasan (qty awal / masuk / keluar / akhir) + tabel
   riwayat pergerakan.

   Perhitungan data ada di stock-mutasi-data.php (dipakai bersama
   oleh versi ekspor Excel stock-mutasi-export.php).
   ============================================================ */

if(!$prod){
    echo '<div class="mutasi-pane-empty"><i class="fas fa-box-open"></i><div>Produk tidak ditemukan</div></div>';
    exit;
}

function mutasi_stat($label, $value, $unit, $icon, $tone){
    $unitHtml = $unit !== '' ? '<em>' . htmlspecialchars($unit) . '</em>' : '';
    return '<div class="mutasi-stat mutasi-stat-' . $tone . '">'
        . '<div class="mutasi-stat-top">'
        .     '<span class="mutasi-stat-ico"><i class="fas ' . $icon . '"></i></span>'
        .     '<span class="mutasi-stat-label">' . $label . '</span>'
        . '</div>'
        . '<div class="mutasi-stat-value">' . number_format((int)$value) . $unitHtml . '</div>'
        . '</div>';
}

/* Kartu kecil "Adjustment": tampil di antara Qty Keluar dan Qty Akhir.
   Nilai net adjustment (+in −out); tanda minus kalau net negatif,
   strip kalau nol. */
function mutasi_stat_adj($adjIn, $adjOut, $unit, $tone){
    $net  = (int)$adjIn - (int)$adjOut;
    $unitHtml = $unit !== '' ? '<em>' . htmlspecialchars($unit) . '</em>' : '';
    $value = $net > 0
        ? '<span class="mutasi-adj-pos">+' . number_format($net) . '</span>' . $unitHtml
        : ($net < 0
            ? '<span class="mutasi-adj-neg">−' . number_format(-$net) . '</span>' . $unitHtml
            : '<span class="mutasi-adj-nil">–</span>');

    return '<div class="mutasi-stat mutasi-stat-adj mutasi-stat-' . $tone . '">'
        . '<div class="mutasi-stat-top">'
        .     '<span class="mutasi-stat-ico"><i class="fas fa-sliders"></i></span>'
        .     '<span class="mutasi-stat-label">Adjustment</span>'
        . '</div>'
        . '<div class="mutasi-stat-value">' . $value . '</div>'
        . '</div>';
}

function mutasi_rows_html($rows, $bulan){
    $html = '';
    foreach($rows as $r){
        $masuk  = $r['masuk']  > 0 ? '<span class="mutasi-in">+'  . number_format((int)$r['masuk'])  . '</span>' : '<span class="mutasi-nil">–</span>';
        $keluar = $r['keluar'] > 0 ? '<span class="mutasi-out">−' . number_format((int)$r['keluar']) . '</span>' : '<span class="mutasi-nil">–</span>';

        $sub = $r['sub'] !== '' ? '<span class="mutasi-ket-sub">' . htmlspecialchars($r['sub']) . '</span>' : '';

        $html .= '<tr>'
            . '<td data-label="Tanggal"><div class="date-main"><i class="far fa-calendar"></i> ' . mutasi_tgl($r['tgl'], $bulan) . '</div></td>'
            . '<td data-label="Keterangan"><div class="mutasi-ket"><span class="mutasi-ket-title">' . htmlspecialchars($r['title']) . '</span>' . $sub . '</div></td>'
            . '<td data-label="Masuk" class="text-center">' . $masuk . '</td>'
            . '<td data-label="Keluar" class="text-center">' . $keluar . '</td>'
            . '<td data-label="Saldo" class="text-center"><span class="mutasi-saldo">' . number_format((int)$r['saldo']) . '</span></td>'
            . '</tr>';
    }
    return $html;
}
?>
<div class="mutasi-product">
    <div class="mutasi-product-info">
        <div class="mutasi-product-name"><?= htmlspecialchars($prod['name']) ?></div>
        <div class="mutasi-product-code"><?= htmlspecialchars($prod['code']) ?></div>
    </div>
    <div class="mutasi-product-range">
        <i class="far fa-calendar"></i>
        <?= mutasi_tgl($from, $bulan) ?> &ndash; <?= mutasi_tgl($to, $bulan) ?>
    </div>
</div>

<section class="mutasi-pane is-active" data-pane="gudang">
    <div class="mutasi-stats">
        <?= mutasi_stat('Qty Awal',  $gAwal,  $unit, 'fa-box-archive',      'awal') ?>
        <?= mutasi_stat('Qty Masuk', $gMasuk, $unit, 'fa-arrow-down-long',  'masuk') ?>
        <?= mutasi_stat('Qty Keluar',$gKeluar,$unit, 'fa-arrow-up-long',    'keluar') ?>
        <?= mutasi_stat_adj($gAdjIn, $gAdjOut, $unit, 'adj') ?>
        <?= mutasi_stat('Qty Akhir', $gAkhir, $unit, 'fa-cubes-stacked',    'akhir') ?>
    </div>

    <div class="mutasi-riwayat">
        <div class="mutasi-riwayat-head">
            <span class="mutasi-riwayat-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Mutasi Gudang</span>
        </div>
        <div class="table-responsive-wrap">
            <table class="table-mutasi" id="tableMutasiGudang">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-center">Masuk</th>
                        <th class="text-center">Keluar</th>
                        <th class="text-center">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?= mutasi_rows_html($rowsG, $bulan) ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="mutasi-pane" data-pane="kantin">
    <div class="mutasi-stats">
        <?= mutasi_stat('Qty Awal',  $kAwal,  $unit, 'fa-box-archive',      'awal') ?>
        <?= mutasi_stat('Qty Masuk', $kMasuk, $unit, 'fa-arrow-down-long',  'masuk') ?>
        <?= mutasi_stat('Qty Keluar',$kKeluar,$unit, 'fa-arrow-up-long',    'keluar') ?>
        <?= mutasi_stat_adj($kAdjIn, $kAdjOut, $unit, 'adj') ?>
        <?= mutasi_stat('Qty Akhir', $kAkhir, $unit, 'fa-cubes-stacked',    'akhir') ?>
    </div>

    <div class="mutasi-riwayat">
        <div class="mutasi-riwayat-head">
            <span class="mutasi-riwayat-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Mutasi Kantin</span>
        </div>
        <div class="table-responsive-wrap">
            <table class="table-mutasi" id="tableMutasiKantin">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-center">Masuk</th>
                        <th class="text-center">Keluar</th>
                        <th class="text-center">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?= mutasi_rows_html($rowsK, $bulan) ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
