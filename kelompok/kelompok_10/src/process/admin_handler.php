<?php
session_start();
require_once '../config/database.php';

// Pastikan yang akses adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        addUser($conn);
        break;
    
    case 'edit':
        editUser($conn);
        break;
    
    case 'delete':
        deleteUser($conn);
        break;
    
    case 'toggle_status':
        toggleStatus($conn);
        break;
    
    // Package Management
    case 'add_package':
        addPackage($conn);
        break;
    
    case 'edit_package':
        editPackage($conn);
        break;
    
    case 'delete_package':
        deletePackage($conn);
        break;
    
    case 'toggle_package_status':
        togglePackageStatus($conn);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function addUser($conn) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($username) || empty($full_name) || empty($role) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        return;
    }
    
    // Validasi panjang password
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter']);
        return;
    }
    
    // Validasi role
    if (!in_array($role, ['kasir', 'worker'])) {
        echo json_encode(['success' => false, 'message' => 'Role tidak valid']);
        return;
    }
    
    // Cek apakah username sudah ada
    $check_query = "SELECT id FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
        return;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    $insert_query = "INSERT INTO users (username, password, full_name, role, is_active) 
                     VALUES ('$username', '$hashed_password', '$full_name', '$role', TRUE)";
    
    if (mysqli_query($conn, $insert_query)) {
        echo json_encode(['success' => true, 'message' => 'User berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan user: ' . mysqli_error($conn)]);
    }
}

function editUser($conn) {
    $user_id = (int)$_POST['user_id'];
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = $_POST['password'] ?? '';
    
    // Validasi input
    if (empty($full_name) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Nama lengkap dan role wajib diisi']);
        return;
    }
    
    // Validasi role
    if (!in_array($role, ['kasir', 'worker', 'admin'])) {
        echo json_encode(['success' => false, 'message' => 'Role tidak valid']);
        return;
    }
    
    // Cek apakah user ada
    $check_query = "SELECT id, role FROM users WHERE id = $user_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
        return;
    }
    
    $user = mysqli_fetch_assoc($check_result);
    
    // Jangan izinkan mengubah admin
    if ($user['role'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat mengubah data admin']);
        return;
    }
    
    // Build update query
    $update_parts = [
        "full_name = '$full_name'",
        "role = '$role'"
    ];
    
    // Update password jika diisi
    if (!empty($password)) {
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter']);
            return;
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_parts[] = "password = '$hashed_password'";
    }
    
    $update_query = "UPDATE users SET " . implode(', ', $update_parts) . " WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        echo json_encode(['success' => true, 'message' => 'User berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui user: ' . mysqli_error($conn)]);
    }
}

function deleteUser($conn) {
    $user_id = (int)$_POST['user_id'];
    
    // Cek apakah user ada dan bukan admin
    $check_query = "SELECT id, username, role FROM users WHERE id = $user_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
        return;
    }
    
    $user = mysqli_fetch_assoc($check_result);
    
    // Jangan izinkan menghapus admin
    if ($user['role'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus admin']);
        return;
    }
    
    // Jangan izinkan menghapus diri sendiri
    if ($user_id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri']);
        return;
    }
    
    // Cek apakah user masih memiliki transaksi
    $transaction_check = "SELECT COUNT(*) as count FROM transactions 
                          WHERE kasir_input_id = $user_id OR kasir_bayar_id = $user_id";
    $transaction_result = mysqli_query($conn, $transaction_check);
    $transaction_count = mysqli_fetch_assoc($transaction_result)['count'];
    
    if ($transaction_count > 0) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus user yang memiliki riwayat transaksi']);
        return;
    }
    
    // Hapus user
    $delete_query = "DELETE FROM users WHERE id = $user_id";
    
    if (mysqli_query($conn, $delete_query)) {
        echo json_encode(['success' => true, 'message' => 'User berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus user: ' . mysqli_error($conn)]);
    }
}

function toggleStatus($conn) {
    $user_id = (int)$_POST['user_id'];
    $is_active = (int)$_POST['is_active'];
    
    // Cek apakah user ada dan bukan admin
    $check_query = "SELECT id, role FROM users WHERE id = $user_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
        return;
    }
    
    $user = mysqli_fetch_assoc($check_result);
    
    // Jangan izinkan mengubah status admin
    if ($user['role'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat mengubah status admin']);
        return;
    }
    
    // Update status
    $update_query = "UPDATE users SET is_active = $is_active WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $status_text = $is_active ? 'diaktifkan' : 'dinonaktifkan';
        echo json_encode(['success' => true, 'message' => "User berhasil $status_text"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengubah status: ' . mysqli_error($conn)]);
    }
}

// ============================================
// PACKAGE MANAGEMENT FUNCTIONS
// ============================================

function addPackage($conn) {
    $nama_paket = mysqli_real_escape_string($conn, trim($_POST['nama_paket']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $harga_per_qty = (float)$_POST['harga_per_qty'];
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
    $estimasi_hari = (int)$_POST['estimasi_hari'];
    
    // Validasi input
    if (empty($nama_paket) || empty($satuan) || $harga_per_qty <= 0) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi dengan benar']);
        return;
    }
    
    // Validasi satuan
    if (!in_array($satuan, ['kg', 'pcs'])) {
        echo json_encode(['success' => false, 'message' => 'Satuan tidak valid']);
        return;
    }
    
    // Validasi estimasi hari
    if ($estimasi_hari < 1 || $estimasi_hari > 30) {
        echo json_encode(['success' => false, 'message' => 'Estimasi hari harus antara 1-30 hari']);
        return;
    }
    
    // Cek apakah nama paket sudah ada
    $check_query = "SELECT id FROM packages WHERE nama_paket = '$nama_paket'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Nama paket sudah digunakan']);
        return;
    }
    
    // Insert paket baru
    $insert_query = "INSERT INTO packages (nama_paket, deskripsi, harga_per_qty, satuan, estimasi_hari, is_active) 
                     VALUES ('$nama_paket', '$deskripsi', $harga_per_qty, '$satuan', $estimasi_hari, TRUE)";
    
    if (mysqli_query($conn, $insert_query)) {
        echo json_encode(['success' => true, 'message' => 'Paket berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan paket: ' . mysqli_error($conn)]);
    }
}

function editPackage($conn) {
    $package_id = (int)$_POST['package_id'];
    $nama_paket = mysqli_real_escape_string($conn, trim($_POST['nama_paket']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $harga_per_qty = (float)$_POST['harga_per_qty'];
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
    $estimasi_hari = (int)$_POST['estimasi_hari'];
    
    // Validasi input
    if (empty($nama_paket) || empty($satuan) || $harga_per_qty <= 0) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi dengan benar']);
        return;
    }
    
    // Validasi satuan
    if (!in_array($satuan, ['kg', 'pcs'])) {
        echo json_encode(['success' => false, 'message' => 'Satuan tidak valid']);
        return;
    }
    
    // Validasi estimasi hari
    if ($estimasi_hari < 1 || $estimasi_hari > 30) {
        echo json_encode(['success' => false, 'message' => 'Estimasi hari harus antara 1-30 hari']);
        return;
    }
    
    // Cek apakah paket ada
    $check_query = "SELECT id FROM packages WHERE id = $package_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Paket tidak ditemukan']);
        return;
    }
    
    // Cek apakah nama paket sudah digunakan oleh paket lain
    $check_name = "SELECT id FROM packages WHERE nama_paket = '$nama_paket' AND id != $package_id";
    $name_result = mysqli_query($conn, $check_name);
    
    if (mysqli_num_rows($name_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Nama paket sudah digunakan']);
        return;
    }
    
    // Update paket
    $update_query = "UPDATE packages SET 
                     nama_paket = '$nama_paket',
                     deskripsi = '$deskripsi',
                     harga_per_qty = $harga_per_qty,
                     satuan = '$satuan',
                     estimasi_hari = $estimasi_hari
                     WHERE id = $package_id";
    
    if (mysqli_query($conn, $update_query)) {
        echo json_encode(['success' => true, 'message' => 'Paket berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui paket: ' . mysqli_error($conn)]);
    }
}

function deletePackage($conn) {
    $package_id = (int)$_POST['package_id'];
    
    // Cek apakah paket ada
    $check_query = "SELECT id, nama_paket FROM packages WHERE id = $package_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Paket tidak ditemukan']);
        return;
    }
    
    // Cek apakah paket masih digunakan dalam transaksi
    $transaction_check = "SELECT COUNT(*) as count FROM transactions WHERE package_id = $package_id";
    $transaction_result = mysqli_query($conn, $transaction_check);
    $transaction_count = mysqli_fetch_assoc($transaction_result)['count'];
    
    if ($transaction_count > 0) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus paket yang memiliki riwayat transaksi. Nonaktifkan paket sebagai gantinya.']);
        return;
    }
    
    // Hapus paket
    $delete_query = "DELETE FROM packages WHERE id = $package_id";
    
    if (mysqli_query($conn, $delete_query)) {
        echo json_encode(['success' => true, 'message' => 'Paket berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus paket: ' . mysqli_error($conn)]);
    }
}

function togglePackageStatus($conn) {
    $package_id = (int)$_POST['package_id'];
    $is_active = (int)$_POST['is_active'];
    
    // Cek apakah paket ada
    $check_query = "SELECT id FROM packages WHERE id = $package_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Paket tidak ditemukan']);
        return;
    }
    
    // Update status
    $update_query = "UPDATE packages SET is_active = $is_active WHERE id = $package_id";
    
    if (mysqli_query($conn, $update_query)) {
        $status_text = $is_active ? 'diaktifkan' : 'dinonaktifkan';
        echo json_encode(['success' => true, 'message' => "Paket berhasil $status_text"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengubah status: ' . mysqli_error($conn)]);
    }
}
?>
