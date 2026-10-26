<?php
include '../../sessions/session.php';

    $tenant_id = (int)$_GET['tenant'];
    $type = $_GET['type'] ? $_GET['type'] : 'tenant';

    $bulan = [
        1 => 'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];

    $table = ($type == "utility") ? 'utility_payments' : 'tenant_payments';

    if($type == "utility"){

        $title = "Riwayat Pembayaran Air & Listrik";
        $icon = "fa-bolt";

    }else{

        $title = "Riwayat Pembayaran Tenant";
        $icon = "fa-store";

    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM $table WHERE tenant_id = ? ORDER BY payment_date DESC");
    mysqli_stmt_bind_param($stmt, "i", $tenant_id);
    mysqli_stmt_execute($stmt);
    $q = mysqli_stmt_get_result($stmt);
?>

<div class="tenant-payment-wrapper">

    <div class="tpw-header">
        <div class="tpw-header-left">
            <i class="fa-solid <?= $icon ?>"></i>
            <span><?= $title ?></span>
        </div>
    </div>

    <div id="tpwBtnContainer" style="display:none;">
        <?php if(in_array($user['role'], ['staff kasir', 'developer'])): ?>
        <button type="button" class="tpw-add-btn addPaymentBtn" data-tenant-id="<?= $tenant_id ?>" data-type="<?= $type ?>">
            <i class="fas fa-plus-circle"></i>
            <span>Tambah Pembayaran</span>
        </button>
        <?php endif; ?>
    </div>

    <div class="table-responsive-wrap" id="paymentTableWrapper">
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
                <?php 
                mysqli_data_seek($q,0);
                while($d=mysqli_fetch_assoc($q)): 
                $tgl=strtotime($d['payment_date']);
                $tanggal=date('d',$tgl)." ".$bulan[(int)date('m',$tgl)]." ".date('Y',$tgl);
                ?>

                <tr data-id="<?= $d['id'] ?>" 
                    data-date="<?= htmlspecialchars($tanggal) ?>"
                    data-amount="<?= $d['cost_payment'] ?>"
                    data-status="<?= $d['status'] ?>"
                    data-type="<?= $type ?>"
                    data-payment-date="<?= $d['payment_date'] ?>">
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
