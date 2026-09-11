<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_kantin";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Auto-detect base URL untuk support deployment di berbagai environment
    // Development: /qieos | Production: akan auto-detect sesuai deployment path
    if (!defined('BASE_URL')) {
        // Gunakan __DIR__ untuk mendapatkan path aplikasi secara reliable
        $document_root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
        $app_root = str_replace('\\', '/', dirname(__DIR__)); // Parent directory dari script/

        // Hitung base path dari perbedaan antara app root dan document root
        $base_path = str_replace($document_root, '', $app_root);

        // Jika deployed di root domain, base_path akan kosong
        if ($base_path === '' || $base_path === '/') {
            $base_path = '';
        }

        define('BASE_URL', $base_path);
    }