<?php
include '../../sessions/session.php';

    $tenant_id = (int)$_GET['tenant'];
    $type = $_GET['type'] ? $_GET['type'] : 'tenant';

    $bulan = [
        1 => 'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];

    if($type == "utility"){

        $title = "Riwayat Pembayaran Air & Listrik";
        $icon = "fa-bolt";

        $q = mysqli_query($conn,"
            SELECT *
            FROM utility_payments
            WHERE tenant_id='$tenant_id'
            ORDER BY payment_date DESC
        ");

    }else{

        $title = "Riwayat Pembayaran Tenant";
        $icon = "fa-store";

        $q = mysqli_query($conn,"
            SELECT *
            FROM tenant_payments
            WHERE tenant_id='$tenant_id'
            ORDER BY payment_date DESC
        ");

    }
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    .stock-wrapper {
        background: #f4f6f9;
        padding: 20px;
        border-radius: 12px;
    }

    .stock-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .stock-header-detail {
        background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
        color: #fff;
        padding: 16px 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .table-stock thead {
        background: #f1f3f5;
    }

    .table-stock th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #555;
    }

    .table-stock td {
        vertical-align: middle;
        font-size: 14px;
    }

    .badge-qty {
        background: #e7f1ff;
        color: #1d4ed8;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-price {
        background: #fff3cd;
        color: #856404;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .date-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    /* ACTION */
    .action-btn{
        width:38px;
        height:38px;
        border:none;
        border-radius:10px;
        margin:0 4px;
        color:#fff;
        transition:.25s;
    }

    .btn-edit{
        background:#f59e0b;
    }

    .btn-delete{
        background:#ef4444;
    }

    .date-main {
        font-weight: 600;
        color: #111;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-badge {
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-badge i {
        color: #0ea5e9;
    }

    @media (max-width:768px){
        .stock-wrapper{
            padding:12px;
        }

        .stock-header-detail{
            padding:14px 16px;
            font-size:15px;
        }

        .table-stock th{
            font-size:11px;
        }

        .table-stock td{
            font-size:13px;
        }

        .badge-price,
        .badge-qty{
            font-size:11px;
            padding:5px 8px;
        }

        .action-btn{
            width:34px;
            height:34px;
            margin:2px;
        }

        #btnContainer{
            width:100%;
        }

        #btnContainer .btn{
            width:100%;
        }
    }
</style>

<div class="stock-wrapper">

    <div class="card stock-card">

        <div class="stock-header-detail">
            <i class="fa-solid <?= $icon ?>"></i>
            <?= $title ?>
        </div>

        <?php if($user['role'] == 'staff kasir'): ?>
        <div class="d-flex justify-content-between align-items-center px-4 pb-3 flex-wrap gap-2">

            <!-- <div>
                <small class="text-muted">
                    Kelola riwayat pembayaran.
                </small>
            </div> -->

            <div id="btnContainer">

                <button
                    type="button"
                    class="btn btn-primary addPaymentBtn" data-tenant-id="<?= $tenant_id ?>" data-type="<?= $type ?>">

                    <i class="fas fa-plus-circle me-2"></i>&nbsp;

                    Tambah Pembayaran

                </button>

            </div>

        </div>
        <?php endif; ?>

        <div class="card-body p-0 mb-3">

            <div class="table-responsive">
                <table id="tablePayment" class="table table-hover table-stock mb-0">
                    <thead>
                        <tr>
                            <th>
                                <i class="fa-regular fa-calendar"></i>
                                Tanggal
                            </th>

                            <th class="text-center">
                                <i class="fa-solid fa-wallet"></i>
                                Nominal
                            </th>

                            <th class="text-center">
                                <i class="fa-solid fa-circle-check"></i>
                                Status
                            </th>

                            <th class="text-center">
                                <i class="fa-solid fa-gear"></i>
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($d=mysqli_fetch_assoc($q)): ?>

                        <?php

                        $tgl=strtotime($d['payment_date']);

                        $tanggal=date('d',$tgl)." ".$bulan[(int)date('m',$tgl)]." ".date('Y',$tgl);

                        ?>

                        <tr>
                            <td>
                                <div class="date-main">
                                    <i class="fa-regular fa-calendar"></i>
                                    <?= $tanggal ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge-price">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                    Rp <?= number_format($d['cost_payment'],0,',','.') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?php if($d['status']=="paid"){ ?>
                                    <span class="badge-qty bg-success text-white">
                                        <i class="fas fa-check-circle"></i>
                                        Lunas
                                    </span>
                                <?php }else{ ?>
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock"></i>
                                        Belum Lunas
                                    </span>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <button class="action-btn btn-edit editPaymentBtn" data-id="<?= $d['id'] ?>" data-type="<?= $type ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="action-btn btn-delete deletePaymentBtn"
                                    data-id="<?= $d['id'] ?>"
                                    data-date="<?= $d['payment_date'] ?>"
                                    data-type="<?= $type ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>