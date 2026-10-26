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
    background:linear-gradient(135deg,#0f172a,#1e293b,#334155);
    border:1px solid rgba(99,102,241,.3);
    color:#fff;
    box-shadow:0 8px 20px rgba(0,0,0,.3);
    margin-bottom:20px;
    position:relative;
    overflow:hidden;
}
.blue-strip::before{
    content:'';
    position:absolute;
    inset:0;
    background:radial-gradient(circle at 30% 50%,rgba(99,102,241,.18) 0%,transparent 35%),
               radial-gradient(circle at 70% 50%,rgba(139,92,246,.15) 0%,transparent 35%);
    pointer-events:none;
    z-index:0;
}
.blue-strip > *{
    position:relative;
    z-index:1;
}
.blue-strip .strip-icon{
    width:58px;
    height:58px;
    flex-shrink:0;
    border-radius:14px;
    background:linear-gradient(135deg,rgba(255,255,255,.15),rgba(255,255,255,.08));
    border:1px solid rgba(255,255,255,.2);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    box-shadow:0 4px 12px rgba(0,0,0,.1);
}
.blue-strip .strip-title{
    font-size:17px;
    font-weight:700;
}
.blue-strip .strip-subtitle{
    font-size:13px;
    color:#94a3b8;
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