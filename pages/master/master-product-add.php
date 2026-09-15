<?php
include '../../sessions/session.php';
include __DIR__ . '/../components/data/stock-status.php';
$lowStockDefault = get_low_stock_default($conn);
?>

<form id="addProductForm" enctype="multipart/form-data">

    <div class="section-title">
        Informasi Produk
    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-barcode"></i>
                </div>
                <input
                    type="text"
                    name="code"
                    class="form-control"
                    placeholder="Kode Produk"
                    required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-box"></i>
                </div>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Nama Produk"
                    required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <select name="category" class="form-control" required>
                    <option value="">Kategori</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                    <option value="Jajanan">Jajanan</option>
                    <option value="Pelengkap">Pelengkap</option>
                    <option value="Additional">Additional</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <input
                    type="number"
                    name="price"
                    class="form-control"
                    placeholder="Harga Jual (Rp)"
                    min="0"
                    step="100"
                    required>
            </div>
        </div>

        <div class="col-md-12" id="addFieldUnit">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-ruler"></i>
                </div>
                <input
                    type="text"
                    name="unit"
                    class="form-control"
                    placeholder="Satuan (opsional, contoh: pcs, botol, dus)">
            </div>
        </div>

        <div class="col-md-12" id="addFieldLowStock">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-gauge-high"></i>
                </div>
                <input
                    type="number"
                    name="low_stock"
                    class="form-control"
                    placeholder="Batas Stok Menipis"
                    value="<?= (int)$lowStockDefault ?>"
                    min="0">
            </div>
            <small class="text-muted d-block mb-3 ms-5" style="font-size:11px;">
                Stok di bawah batas ini berstatus Menipis, 0 = Habis. Kosongkan sudah terisi default dari pengaturan.
            </small>
        </div>

        <div class="col-md-12 mb-4" id="addFieldSupplier">
            <label class="form-label supplier-form-label"><i class="fas fa-truck"></i> Supplier</label>
            <div id="supplierFields" class="supplier-fields">
                <div class="supplier-field-row">
                    <div class="form-input-wrap">
                        <div class="form-input-icon"><i class="fas fa-truck"></i></div>
                        <div class="select-wrap">
                            <select name="supplier_id[]" class="form-input">
                                <option value="">Supplier (opsional)</option>
                                <?php
                                $qs = mysqli_query($conn, "SELECT id, name FROM suppliers WHERE deleted_at IS NULL ORDER BY name ASC");
                                while($s = mysqli_fetch_assoc($qs)):
                                ?>
                                <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="button" class="btn-remove-supplier" data-remove title="Hapus supplier"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-add-supplier" id="btnAddSupplier">
                <i class="fas fa-plus"></i><span>Tambah Supplier</span>
            </button>
            <template id="supplierFieldTemplate">
                <div class="supplier-field-row">
                    <div class="form-input-wrap">
                        <div class="form-input-icon"><i class="fas fa-truck"></i></div>
                        <div class="select-wrap">
                            <select name="supplier_id[]" class="form-input">
                                <option value="">Supplier (opsional)</option>
                            </select>
                        </div>
                        <button type="button" class="btn-remove-supplier" data-remove title="Hapus supplier"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            </template>
        </div>

        <div class="col-md-12">
            <div class="photo-upload-box">
                <div class="photo-preview" id="addPhotoPreview">
                    <i class="fas fa-camera"></i>
                </div>
                <div class="photo-input-details">
                    <label><i class="fas fa-image me-1"></i> Foto Produk</label>
                    <input type="file" name="photo" accept="image/*" class="form-control">
                    <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2MB.</small>
                </div>
            </div>
        </div>

    </div>

    <div class="text-end mt-4 mb-3">
        <button type="submit" class="btn-save">
            <i class="fas fa-plus me-1"></i>
            Tambah Produk
        </button>
    </div>

</form>