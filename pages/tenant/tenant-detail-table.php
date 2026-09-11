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

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/tenant-detail-table.css">

<div class="stock-wrapper">

    <div class="card stock-card">

        <div class="stock-header-detail">
            <i class="fa-solid <?= $icon ?>"></i>
            <?= $title ?>
        </div>

        <?php if(in_array($user['role'], ['staff kasir', 'developer'])): ?>
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
                                <?php if($user['role'] != 'staff kasir'): ?>
                                <button class="action-btn btn-edit editPaymentBtn" data-id="<?= $d['id'] ?>" data-type="<?= $type ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="action-btn btn-delete deletePaymentBtn"
                                    data-id="<?= $d['id'] ?>"
                                    data-date="<?= $d['payment_date'] ?>"
                                    data-type="<?= $type ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php else: ?>
                                    <button class="action-btn btn-print" onclick="printReceipt(<?= $d['id'] ?>, '<?= $type ?>')">
                                        <i class="fas fa-print"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>