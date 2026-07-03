<?php
include '../../sessions/session.php';
?>

<form id="addAdministratorForm">

    <div class="section-title">
        Informasi Administrator
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
                    <i class="fas fa-envelope"></i>
                </div>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Email"
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
            Tambah Administrator
        </button>
    </div>

</form>