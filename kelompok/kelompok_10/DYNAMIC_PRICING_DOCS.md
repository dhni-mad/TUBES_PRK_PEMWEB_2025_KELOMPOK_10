# Dokumentasi: Dynamic Pricing System

## Overview
Sistem untuk menampilkan daftar harga paket layanan E-LAUNDRY secara dinamis dari database MySQL.

## Flow Alur Kerja

```
1. User membuka halaman index.php (beranda)
   ↓
2. index.php include packages_handler.php
   ↓
3. packages_handler.php panggil getActivePackages()
   ↓
4. Query database: SELECT * FROM packages WHERE is_active = TRUE
   ↓
5. Data paket dikembalikan sebagai array
   ↓
6. index.php loop data dan render HTML dinamis
   ↓
7. Harga, deskripsi, dan semua info paket tampil otomatis di halaman
```

## File yang Digunakan

### 1. `packages_handler.php` (Backend Handler)
**Lokasi:** `/src/process/packages_handler.php`

**Fungsi Utama:**

#### `getActivePackages()`
- Mengambil semua paket aktif dari database
- Diurutkan berdasarkan harga (termurah ke termahal)
- Return: Array paket atau array kosong

```php
$packages = getActivePackages();
// Hasil:
// Array(
//     [0] => Array('id' => 1, 'nama_paket' => 'Setrika Saja', 'harga_per_qty' => 2500, ...)
//     [1] => Array('id' => 2, 'nama_paket' => 'Cuci Kering', 'harga_per_qty' => 4000, ...)
// )
```

#### `getPackageById($id)`
- Mengambil detail satu paket berdasarkan ID
- Return: Array paket atau null jika tidak ditemukan

```php
$package = getPackageById(1);
```

#### `formatHarga($harga)`
- Memformat angka harga ke format Rupiah
- Return: String format "Rp X.XXX"

```php
echo formatHarga(2500); // Output: Rp 2.500
```

#### `getBadgeCategory($index, $total)`
- Menentukan badge kategori paket berdasarkan posisinya
- Return: String badge (BASIC, STANDAR, POPULER, PREMIUM)

```php
// Paket di tengah-tengah akan mendapat badge POPULER
```

#### `getPackageIcon($nama_paket)`
- Menentukan icon/emoji berdasarkan nama paket
- Return: String emoji yang sesuai

```php
getPackageIcon('Express'); // Output: ⚡
getPackageIcon('Hemat');   // Output: 💰
```

### 2. `index.php` (Frontend)
**Lokasi:** `/src/pages/public/index.php`

**Perubahan:**
- Include `packages_handler.php` di bagian atas
- Ambil data paket dengan `getActivePackages()`
- Loop data dan generate HTML card secara dinamis

```php
<?php
require_once __DIR__ . '/../../../src/process/packages_handler.php';
$packages = getActivePackages();
$total_packages = count($packages);
?>

<!-- Dalam section pricing -->
<?php foreach ($packages as $index => $package): ?>
    <div class="price-card<?php echo $is_featured ? ' featured' : ''; ?>">
        <h3><?php echo htmlspecialchars($package['nama_paket']); ?></h3>
        <div class="price"><?php echo formatHarga($package['harga_per_qty']); ?></div>
        <!-- ... -->
    </div>
<?php endforeach; ?>
```

## Database Schema

### Tabel: `packages`

```sql
CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_paket VARCHAR(100) NOT NULL,          -- Nama layanan (e.g., "Cuci Komplit")
    deskripsi TEXT,                             -- Deskripsi singkat layanan
    harga_per_qty DECIMAL(10, 2) NOT NULL,    -- Harga per satuan (kg atau pcs)
    satuan ENUM('kg', 'pcs') NOT NULL,        -- Satuan harga (kilogram atau pieces)
    estimasi_hari INT DEFAULT 3,               -- Estimasi hari pengerjaan
    is_active BOOLEAN DEFAULT TRUE,            -- Status paket (aktif/non-aktif)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Sample Data

```sql
INSERT INTO packages (nama_paket, deskripsi, harga_per_qty, satuan, estimasi_hari) VALUES
('Setrika Saja', 'Khusus Setrika', 2500.00, 'kg', 1),
('Cuci Kering', 'Cuci dan Kering (Tanpa Setrika)', 4000.00, 'kg', 2),
('Cuci Komplit', 'Cuci, Kering, Setrika, dan Parfum', 6000.00, 'kg', 3),
('Express 1 Hari', 'Cuci Komplit Selesai 1 Hari', 12000.00, 'kg', 1),
('Dry Cleaning', 'Pengeringan profesional untuk pakaian premium', 15000.00, 'pcs', 2);
```

## Cara Menggunakan

### Menambah Paket Baru
1. Buka phpMyAdmin
2. Pilih database `laundry_system`
3. Buka tabel `packages`
4. Insert data paket baru
5. Refresh halaman beranda - paket baru akan otomatis muncul

### Mengubah Harga Paket
1. Buka phpMyAdmin → tabel `packages`
2. Edit nilai `harga_per_qty` di record yang ingin diubah
3. Simpan perubahan
4. Halaman beranda akan otomatis menampilkan harga terbaru (no coding needed!)

### Menonaktifkan Paket
1. Buka phpMyAdmin → tabel `packages`
2. Ubah `is_active` menjadi `0 (FALSE)` untuk paket yang ingin disembunyikan
3. Paket akan hilang dari tampilan halaman beranda

### Mengaktifkan Kembali Paket
1. Ubah `is_active` menjadi `1 (TRUE)`
2. Paket akan muncul lagi

## Keuntungan Sistem Dinamis

✅ **Fleksibel:** Tambah/ubah/hapus paket tanpa edit HTML  
✅ **Real-time:** Perubahan di database langsung terlihat di website  
✅ **Scalable:** Dapat menampilkan jumlah paket berapa pun  
✅ **Professional:** Format harga otomatis (Rp 2.500 bukan 2500)  
✅ **Maintainable:** Code lebih bersih dan mudah dikelola  

## Troubleshooting

### Paket tidak muncul di halaman
1. Cek apakah database sudah punya data di tabel `packages`
2. Pastikan `is_active = TRUE` untuk paket yang ingin ditampilkan
3. Cek error log di `error_log()` jika ada koneksi database yang gagal

### Harga tidak format dengan benar
- Pastikan kolom `harga_per_qty` bertipe DECIMAL, bukan VARCHAR

### Include path error
- Sesuaikan path di include statement sesuai lokasi file:
  ```php
  require_once __DIR__ . '/../../../src/process/packages_handler.php';
  ```

## File Struktur Terkait

```
src/
├── config/
│   └── database.php           (Koneksi database)
├── process/
│   └── packages_handler.php   (NEW: Backend handler)
└── pages/
    └── public/
        └── index.php          (UPDATED: Gunakan dynamic packages)
```

---

**Dibuat:** 10 Desember 2025  
**Status:** Aktif & Siap Digunakan
