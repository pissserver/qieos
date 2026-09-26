<?php
// Helper stok & status produk
// - Stok Gudang  : sisa FIFO (purchase_items.remaining_qty)
// - Stok Kantin  : SUM(sales_stock.qty) per produk
// - Total Stok   : gudang + kantin
// - Status       : 0 = Habis, >0 s/d batas = Menipis, > batas = Ready
//   Batas menipis diambil dari products.low_stock, fallback ke app_settings.low_stock_default

if(!function_exists('get_low_stock_default')){

    function get_low_stock_default($conn){
        static $default = null;
        if($default !== null) return $default;
        $default = 5;
        $r = @mysqli_query($conn, "SELECT value FROM app_settings WHERE name = 'low_stock_default' LIMIT 1");
        if($r && mysqli_num_rows($r) > 0){
            $row = mysqli_fetch_assoc($r);
            $val = (int)$row['value'];
            if($val >= 0) $default = $val;
        }
        return $default;
    }

}

if(!function_exists('resolve_product_low_stock')){

    // Batas menipis sebuah produk (pakai nilai kolom, fallback ke default global)
    function resolve_product_low_stock($conn, $d){
        if(isset($d['low_stock']) && $d['low_stock'] !== null && $d['low_stock'] !== ''){
            return max(0, (int)$d['low_stock']);
        }
        return get_low_stock_default($conn);
    }

}

if(!function_exists('get_product_stock')){

    // return: ['gudang' => int, 'kantin' => int, 'total' => int]
    function get_product_stock($conn, $productId){
        $productId = (int)$productId;

        $gudang = 0;
        $pi = @mysqli_query($conn, "
            SELECT COALESCE(SUM(pi.remaining_qty),0) AS v
            FROM purchase_items pi
            INNER JOIN purchases p ON p.id = pi.purchase_id AND p.deleted_at IS NULL
            WHERE pi.product_id = $productId AND pi.deleted_at IS NULL
        ");
        if($pi && $row = mysqli_fetch_assoc($pi)) $gudang = (int)$row['v'];

        $kantin = 0;
        $ss = @mysqli_query($conn, "
            SELECT COALESCE(SUM(qty),0) AS v
            FROM sales_stock
            WHERE product_id = $productId
        ");
        if($ss && $row = mysqli_fetch_assoc($ss)) $kantin = (int)$row['v'];

        return ['gudang' => $gudang, 'kantin' => $kantin, 'total' => $gudang + $kantin];
    }

}

if(!function_exists('product_status_view')){

    // return: ['key'=>'ready|menipis|habis', 'label'=>..., 'css'=>..., 'icon'=>...]
    function product_status_view($total, $lowStock){
        $total = (int)$total;
        $lowStock = max(0, (int)$lowStock);
        if($total <= 0){
            return ['key'=>'habis', 'label'=>'Habis', 'css'=>'st-habis', 'icon'=>'fa-circle-xmark'];
        }
        if($total <= $lowStock){
            return ['key'=>'menipis', 'label'=>'Menipis', 'css'=>'st-menipis', 'icon'=>'fa-triangle-exclamation'];
        }
        return ['key'=>'ready', 'label'=>'Ready', 'css'=>'st-ready', 'icon'=>'fa-circle-check'];
    }

}

if(!function_exists('fmt')){

    function fmt($n){
        return number_format((int)$n, 0, ',', '.');
    }

}