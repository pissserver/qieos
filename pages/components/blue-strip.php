<?php
// Komponen strip biru di atas header halaman (konsisten dengan panel master)
// Pakai variabel sebelum include:
//   $blue_strip_icon, $blue_strip_title, $blue_strip_subtitle (opsional)
$blueStripIcon   = isset($blue_strip_icon)   ? $blue_strip_icon   : 'fa-boxes-stacked';
$blueStripTitle  = isset($blue_strip_title)  ? $blue_strip_title  : '';
$blueStripSub    = isset($blue_strip_subtitle) ? $blue_strip_subtitle : '';
?>
<style>
.blue-strip{
    display:flex;
    align-items:center;
    gap:16px;
    padding:18px 22px;
    border-radius:18px;
    background:linear-gradient(135deg,#4f46e5,#4338ca);
    color:#fff;
    box-shadow:0 8px 22px rgba(79,70,229,.22);
    margin-bottom:20px;
}
.blue-strip .strip-icon{
    width:58px;
    height:58px;
    flex-shrink:0;
    border-radius:16px;
    background:rgba(255,255,255,.12);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
}
.blue-strip .strip-title{
    font-size:17px;
    font-weight:700;
}
.blue-strip .strip-subtitle{
    font-size:13px;
    opacity:.85;
    margin:0;
}
@media (max-width:575.98px){
    .blue-strip{padding:16px 18px;}
    .blue-strip .strip-icon{width:48px;height:48px;font-size:18px;border-radius:14px;}
    .blue-strip .strip-title{font-size:15px;}
    .blue-strip .strip-subtitle{font-size:12px;}
}
</style>

<div class="blue-strip">
    <div class="strip-icon">
        <i class="fas <?= htmlspecialchars($blueStripIcon) ?>"></i>
    </div>

    <div>
        <div class="strip-title">
            <?= htmlspecialchars($blueStripTitle) ?>
        </div>

        <?php if($blueStripSub !== ''): ?>
        <p class="strip-subtitle">
            <?= htmlspecialchars($blueStripSub) ?>
        </p>
        <?php endif; ?>
    </div>
</div>