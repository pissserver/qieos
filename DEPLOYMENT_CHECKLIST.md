# Deployment Checklist untuk Qieos POS

## ✅ Pre-Deployment Checklist

### 1. Database Configuration
- [ ] Update database credentials di `script/connection.php`
- [ ] Export database dari development
- [ ] Import database ke production server
- [ ] Test koneksi database

### 2. File Permissions
- [ ] Set writable permissions untuk `assets/img/uploads/`
- [ ] Set writable permissions untuk `assets/img/products/`
- [ ] Pastikan file PHP tidak writable dari web

### 3. Security Settings
- [ ] Matikan error display di production (`error_reporting(0)`)
- [ ] Tambahkan security headers di `.htaccess`
- [ ] Pastikan menggunakan HTTPS
- [ ] Update session security settings

### 4. URL & Path Testing
- [ ] Test BASE_URL auto-detect: buka `test-base-url.php`
- [ ] Verifikasi semua CSS loading dengan benar
- [ ] Verifikasi semua JavaScript loading dengan benar
- [ ] Verifikasi semua images loading dengan benar
- [ ] Test PWA manifest: `manifest.php`
- [ ] Test offline page

### 5. Functionality Testing
- [ ] Test login/logout
- [ ] Test semua menu navigation
- [ ] Test upload photos (user & products)
- [ ] Test transaksi penjualan
- [ ] Test laporan & rekap
- [ ] Test PWA install di mobile

### 6. Cleanup
- [ ] Hapus `test-base-url.php` setelah verifikasi
- [ ] Hapus `manifest.json.backup` jika sudah tidak diperlukan
- [ ] Review dan hapus file development yang tidak diperlukan

## 📋 Deployment Scenarios

### Scenario 1: Deploy di Root Domain
```
https://yourdomain.com/
└── Semua file aplikasi di document root
```
**BASE_URL akan otomatis**: `""` (empty string)

### Scenario 2: Deploy di Subdirectory
```
https://yourdomain.com/pos/
└── Semua file aplikasi di subdirectory "pos"
```
**BASE_URL akan otomatis**: `"/pos"`

### Scenario 3: Deploy di Subdomain
```
https://pos.yourdomain.com/
└── Semua file aplikasi di document root subdomain
```
**BASE_URL akan otomatis**: `""` (empty string)

## 🔧 Environment-Specific Configuration

### Development (localhost/qieos/)
- BASE_URL: `/qieos` ✅ Auto-detect
- Database: localhost
- Error Display: ON

### Production
- BASE_URL: Auto-detect ✅
- Database: Production server
- Error Display: OFF (PENTING!)
- HTTPS: Required

## 📞 Support
Jika ada masalah setelah deployment:
1. Cek BASE_URL value dengan test file
2. Cek browser console untuk error CSS/JS loading
3. Cek database connection
4. Cek file permissions
