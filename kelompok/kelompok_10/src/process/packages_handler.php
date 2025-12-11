<?php
/**
 * packages_handler.php
 * Handler untuk mengambil data paket layanan dari database
 */
require_once __DIR__ . '/../config/database.php';
/**
 * Fungsi untuk mengambil semua paket aktif dari database
 * @return array Data paket atau array kosong jika tidak ada
 */
function getActivePackages() {
    $conn = getConnection();
    if (!$conn) {
        return [];
    }
    $query = "SELECT id, nama_paket, deskripsi, harga_per_qty, satuan, estimasi_hari 
              FROM packages 
              WHERE is_active = TRUE 
              ORDER BY harga_per_qty ASC";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("Query Error: " . mysqli_error($conn));
        closeConnection($conn);
        return [];
    }
    $packages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $packages[] = $row;
    }
    mysqli_free_result($result);
    closeConnection($conn);
    return $packages;
}
/**
 * Fungsi untuk mengambil satu paket berdasarkan ID
 * @param int $package_id ID paket
 * @return array|null Data paket atau null jika tidak ditemukan
 */
function getPackageById($package_id) {
    $conn = getConnection();
    if (!$conn) {
        return null;
    }
    $query = "SELECT id, nama_paket, deskripsi, harga_per_qty, satuan, estimasi_hari 
              FROM packages 
              WHERE id = ? AND is_active = TRUE";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Prepare Error: " . mysqli_error($conn));
        closeConnection($conn);
        return null;
    }
    mysqli_stmt_bind_param($stmt, "i", $package_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $package = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    closeConnection($conn);
    return $package;
}
/**
 * Fungsi untuk memformat harga ke format Rupiah
 * @param float $harga Harga dalam angka
 * @return string Harga yang sudah diformat (Rp X.XXX)
 */
function formatHarga($harga) {
    return "Rp " . number_format($harga, 0, ',', '.');
}
/**
 * Fungsi untuk menentukan badge kategori berdasarkan urutan harga
 * @param int $index Indeks paket dalam urutan harga (0 = termurah)
 * @param int $total Total paket
 * @return string Badge category
 */
function getBadgeCategory($index, $total) {
    if ($total <= 1) {
        return "STANDAR";
    }
    if ($index === floor($total / 2)) {
        return "POPULER";
    }
    if ($index < floor($total / 2)) {
        return "BASIC";
    }
    return "PREMIUM";
}
/**
 * Fungsi untuk mengambil icon/emoji berdasarkan nama paket
 * @param string $nama_paket Nama paket
 * @return string Icon/emoji yang sesuai
 */
function getPackageIcon($nama_paket) {
    $nama_lower = strtolower($nama_paket);
    if (strpos($nama_lower, 'express') !== false || strpos($nama_lower, 'cepat') !== false) {
        return "⚡";
    }
    if (strpos($nama_lower, 'hemat') !== false || strpos($nama_lower, 'paket') !== false) {
        return "💰";
    }
    if (strpos($nama_lower, 'khusus') !== false || strpos($nama_lower, 'premium') !== false) {
        return "✨";
    }
    if (strpos($nama_lower, 'setrika') !== false) {
        return "🔥";
    }
    if (strpos($nama_lower, 'dry') !== false) {
        return "💧";
    }
    return "👕"; 
}
?>
