<?php
// Dynamic PWA Manifest Generator
require_once __DIR__ . '/script/connection.php';

header('Content-Type: application/json');
header('Cache-Control: public, max-age=3600'); // Cache for 1 hour

$manifest = [
    "name" => "Qieos - Point of Sales",
    "short_name" => "Qieos",
    "description" => "Aplikasi manajemen penjualan, stok, dan tenant Qieos.",
    "id" => BASE_URL . "/",
    "start_url" => BASE_URL . "/pages/dashboard.php",
    "scope" => BASE_URL . "/",
    "display" => "standalone",
    "display_override" => ["window-controls-overlay", "fullscreen", "standalone", "minimal-ui"],
    "orientation" => "any",
    "background_color" => "#0f172a",
    "theme_color" => "#4f46e5",
    "lang" => "id",
    "dir" => "ltr",
    "categories" => ["business", "productivity", "shopping"],
    "prefer_related_applications" => false,
    "icons" => [
        [
            "src" => BASE_URL . "/assets/img/brand/icon-192.png",
            "sizes" => "192x192",
            "type" => "image/png",
            "purpose" => "any"
        ],
        [
            "src" => BASE_URL . "/assets/img/brand/icon-512.png",
            "sizes" => "512x512",
            "type" => "image/png",
            "purpose" => "any"
        ],
        [
            "src" => BASE_URL . "/assets/img/brand/icon-192.png",
            "sizes" => "192x192",
            "type" => "image/png",
            "purpose" => "maskable"
        ],
        [
            "src" => BASE_URL . "/assets/img/brand/icon-512.png",
            "sizes" => "512x512",
            "type" => "image/png",
            "purpose" => "maskable"
        ]
    ],
    "shortcuts" => [
        [
            "name" => "Dashboard",
            "short_name" => "Dashboard",
            "url" => BASE_URL . "/pages/dashboard.php",
            "icons" => [["src" => BASE_URL . "/assets/img/brand/icon-192.png", "sizes" => "192x192"]]
        ],
        [
            "name" => "Pesanan",
            "short_name" => "Pesanan",
            "url" => BASE_URL . "/pages/sales/order.php",
            "icons" => [["src" => BASE_URL . "/assets/img/brand/icon-192.png", "sizes" => "192x192"]]
        ],
        [
            "name" => "Stok Gudang",
            "short_name" => "Stok",
            "url" => BASE_URL . "/pages/stock/stock.php",
            "icons" => [["src" => BASE_URL . "/assets/img/brand/icon-192.png", "sizes" => "192x192"]]
        ]
    ]
];

echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
