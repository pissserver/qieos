<?php
include '../../sessions/session.php';
?>

<form id="addRegistrationForm">

    <div class="section-title">
        Informasi Tenant
    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-store"></i>
                </div>
                <input
                    type="text"
                    name="tenant_name"
                    class="form-control"
                    placeholder="Nama Tenant"
                    required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                </div>
                <input
                    type="text"
                    name="tenant_owner"
                    class="form-control"
                    placeholder="Nama Pemilik Tenant"
                    required>
            </div>
        </div>
    </div>

    <div class="text-end mt-4 mb-3">
        <button type="submit" class="btn-save">
            <i class="fas fa-plus me-1"></i>
            Tambah Tenant
        </button>
    </div>

</form>