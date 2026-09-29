<?php
session_start();
$_SESSION['username'] = 'dev';
ob_start();
include __DIR__ . '/master-product.php';
$html = ob_get_clean();
echo str_replace('</body>', '<script src="_s2harness.js?v=' . microtime(true) . '"></script></body>', $html);
