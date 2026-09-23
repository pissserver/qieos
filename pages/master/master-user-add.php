<?php
include '../../sessions/session.php';

$selectedRole = (isset($_GET['role']) && $_GET['role'] === 'cashier') ? 'staff kasir' : 'administrator';
?>

<form id="addUserForm">

    <div class="section-title">
        Pilih Role
    </div>

    <div class="mu-role-picker">
        <input type="radio" id="muRoleAdmin" name="role" value="administrator"
            <?= $selectedRole !== 'staff kasir' ? 'checked' : '' ?>>
        <label for="muRoleAdmin" class="mu-role-opt">
            <span class="mu-role-ico"><i class="fas fa-user-shield"></i></span>
            <span>
                <span class="mu-role-name">Administrator</span>
                <small>Akses penuh manajemen &amp; laporan</small>
            </span>
        </label>

        <input type="radio" id="muRoleCashier" name="role" value="staff kasir"
            <?= $selectedRole === 'staff kasir' ? 'checked' : '' ?>>
        <label for="muRoleCashier" class="mu-role-opt">
            <span class="mu-role-ico"><i class="fas fa-user-tie"></i></span>
            <span>
                <span class="mu-role-name">Staff Kasir</span>
                <small>Akses penjualan &amp; rekap kantin</small>
            </span>
        </label>
    </div>

    <div class="section-title">
        Informasi User
    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                </div>
                <input
                    type="text"
                    name="fullname"
                    class="form-control"
                    placeholder="Nama Lengkap"
                    required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-at"></i>
                </div>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Username"
                    required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Password"
                    required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <input
                    type="password"
                    name="confirm_password"
                    class="form-control"
                    placeholder="Konfirmasi Password"
                    required>
            </div>
        </div>

    </div>

    <div class="text-end mt-4 mb-3">
        <button type="submit" class="btn-save">
            <i class="fas fa-plus me-1"></i>
            Tambah User
        </button>
    </div>

</form>