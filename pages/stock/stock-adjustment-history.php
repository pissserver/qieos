<?php
include '../../sessions/session.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0){
    echo '<div class="adjust-empty"><i class="fas fa-box-open"></i><div>Produk tidak ditemukan</div></div>';
    exit;
}

$bulan = [
    1 => 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];

$unit = '';
$pq = mysqli_query($conn, "SELECT unit FROM products WHERE id = $id");
if($pq && ($pd = mysqli_fetch_assoc($pq))) $unit = strtoupper($pd['unit'] ?: '');

$q = mysqli_query($conn, "
    SELECT sa.id, sa.type, sa.location, sa.qty, sa.note, sa.created_at,
           p.form
    FROM stock_adjustments sa
    LEFT JOIN purchase_items pi ON pi.id = sa.purchase_item_id
    LEFT JOIN purchases p ON p.id = pi.purchase_id
    WHERE sa.product_id = $id
    ORDER BY sa.created_at DESC, sa.id DESC
    LIMIT 100
");

$rows = [];
while($q && $r = mysqli_fetch_assoc($q)) $rows[] = $r;

if(!$rows){
    echo '<div class="adjust-empty">'
        . '<div class="adjust-empty-icon"><i class="fas fa-clock-rotate-left"></i></div>'
        . '<div class="adjust-empty-title">Belum ada adjustment</div>'
        . '<div class="adjust-empty-sub">Riwayat adjustment produk ini akan tampil di sini</div>'
        . '</div>';
    exit;
}

foreach($rows as $r){
    $ts = strtotime($r['created_at']);
    $isIn = $r['type'] === 'in';
    $tone = $isIn ? 'in' : 'out';
    $label = $isIn ? 'Penambahan Stok' : 'Pengurangan Stok';
    $sign = $isIn ? '+' : '−';
    $locLabel = $r['location'] === 'kantin' ? 'Stok Kantin' : 'Stok Gudang';
    $locIcon  = $r['location'] === 'kantin' ? 'fa-store' : 'fa-warehouse';
    $form = ($r['location'] === 'gudang' && !empty($r['form'])) ? $r['form'] : '';
    $adjId = (int)$r['id'];

    echo '<div class="adjust-hist-item adjust-hist-' . $tone . '" data-adj-id="' . $adjId . '">'
        . '<div class="adjust-hist-top">'
        .   '<span class="adjust-hist-ico"><i class="fas ' . ($isIn ? 'fa-arrow-down-long' : 'fa-arrow-up-long') . '"></i></span>'
        .   '<div class="adjust-hist-head">'
        .     '<div class="adjust-hist-title">' . $label . '</div>'
        .     '<div class="adjust-hist-meta">'
        .       '<span class="adjust-hist-loc"><i class="fas ' . $locIcon . '"></i>' . $locLabel . '</span>'
        .       ($form ? '<span class="adjust-hist-form"><i class="fas fa-file-lines"></i>' . htmlspecialchars($form) . '</span>' : '')
        .       '<span class="adjust-hist-date"><i class="far fa-calendar"></i>' . date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts) . ' · ' . date('H:i', $ts) . '</span>'
        .     '</div>'
        .   '</div>'
        .   '<span class="adjust-hist-qty">' . $sign . number_format((int)$r['qty']) . ($unit ? ' ' . $unit : '') . '</span>'
        .   '<div class="adjust-hist-actions">'
        .     '<button type="button" class="adjust-hist-btn adjust-hist-edit" data-adj-id="' . $adjId . '" title="Edit adjustment">'
        .       '<i class="fas fa-pen-to-square"></i>'
        .     '</button>'
        .     '<button type="button" class="adjust-hist-btn adjust-hist-delete" data-adj-id="' . $adjId . '" title="Hapus adjustment">'
        .       '<i class="fas fa-trash-can"></i>'
        .     '</button>'
        .   '</div>'
        . '</div>'
        . ($r['note'] !== '' ? '<div class="adjust-hist-note"><i class="far fa-comment-dots"></i>' . htmlspecialchars($r['note']) . '</div>' : '')
        . '</div>';
}
