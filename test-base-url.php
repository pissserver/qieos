<?php
// Test file untuk cek BASE_URL
require_once __DIR__ . '/script/connection.php';

echo "BASE_URL value: " . (defined('BASE_URL') ? BASE_URL : 'NOT DEFINED');
echo "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'];
echo "\n";
echo "Expected BASE_URL: /qieos";
