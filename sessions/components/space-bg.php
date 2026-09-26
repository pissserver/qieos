<?php if (!defined('SID_AUTH_BG')) { define('SID_AUTH_BG', true); } ?>
<!-- ===== QIEOS SPACE BACKGROUND ===== -->
<div class="auth-shapes" aria-hidden="true">
    <!-- Nebula orbs -->
    <span class="shape"></span>
    <span class="shape"></span>
    <span class="shape"></span>
    <span class="shape"></span>
    <span class="shape"></span>

    <!-- Moon -->
    <span class="auth-moon"></span>

    <!-- Twinkling stars -->
    <div class="auth-stars">
        <?php
            $sets = array(
                array('n' => 14, 's' => 1.2, 'g' => 1.7, 'r' => 3.6),
                array('n' => 12, 's' => 2.0, 'g' => 2.6, 'r' => 3.0),
                array('n' => 8,  's' => 3.0, 'g' => 3.8, 'r' => 2.4)
            );
            $k = 0;
            foreach ($sets as $set) {
                for ($i = 0; $i < $set['n']; $i++, $k++) {
                    $t = 2 + (($k * 37) % 93);
                    $l = 2 + (($k * 53) % 93);
                    $s = round($set['s'] + (($k % 5) / 4) * ($set['g'] - $set['s']), 1);
                    $d = round(($k % 9) * 0.32, 2);
                    $r = round($set['r'] + (($k % 7) * 0.12), 2);
                    $glow = $s >= 3.0
                        ? '0 0 9px rgba(186,230,253,.95), 0 0 3px rgba(255,255,255,.7)'
                        : '0 0 6px rgba(186,230,253,.8)';
                    echo '<span class="star" style="top:' . $t . '%;left:' . $l . '%;--s:' . $s . 'px;--d:' . $d . 's;--r:' . $r . 's;--glow:' . $glow . ';"></span>';
                }
            }
        ?>
    </div>

    <!-- Falling meteors -->
    <div class="auth-meteors">
        <span class="meteor"></span>
        <span class="meteor"></span>
        <span class="meteor"></span>
        <span class="meteor"></span>
    </div>
</div>