<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/auth/login.php');
    exit();
}
$baseDir = dirname(__DIR__);
$configPaths = [
    $baseDir . '/config/config.php',
    $baseDir . '/config/database.php',
    $baseDir . '/config/db.php',
    $baseDir . '/config/koneksi.php',
    $baseDir . '/config/connection.php',
];
$configLoaded = false;
foreach ($configPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $configLoaded = true;
        break;
    }
}
if (!$configLoaded) {
    die('Config database tidak ditemukan!');
}
if (!isset($conn)) {
    die('Variabel $conn tidak tersedia!');
}
$action = $_GET['action'] ?? $_POST['action'] ?? '';
switch ($action) {
    case 'create':
        createTransaction($conn);
        break;
    case 'take':
        takeTransaction($conn);
        break;
    case 'payment':
        processPayment($conn);
        break;
    case 'delete':
        deleteTransaction($conn);
        break;
    default:
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
}
/**
 * Membuat transaksi baru
 */
function createTransaction($conn) {
    if ($_SESSION['role'] !== 'kasir') {
        $_SESSION['error'] = 'Anda tidak memiliki akses untuk membuat transaksi!';
        header('Location: ../pages/cashier/new_transaction.php');
        exit();
    }
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $package_id = intval($_POST['package_id'] ?? 0);
    $berat_qty = floatval($_POST['berat_qty'] ?? 0);
    $catatan = trim($_POST['catatan'] ?? '');
    if (empty($nama_pelanggan) || empty($no_hp) || $package_id <= 0 || $berat_qty <= 0) {
        $_SESSION['error'] = 'Data tidak lengkap! Pastikan semua field required diisi.';
        header('Location: ../pages/cashier/new_transaction.php');
        exit();
    }
    $paketQuery = mysqli_query($conn, "SELECT * FROM packages WHERE id = $package_id");
    if (!$paketQuery || mysqli_num_rows($paketQuery) === 0) {
        $_SESSION['error'] = 'Paket tidak ditemukan!';
        header('Location: ../pages/cashier/new_transaction.php');
        exit();
    }
    $paket = mysqli_fetch_assoc($paketQuery);
    $harga_per_qty = $paket['harga_per_qty'];
    $estimasi_hari = $paket['estimasi_hari'];
    $total_harga = $harga_per_qty * $berat_qty;
    $date = date('Ymd');
    $randNum = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    $transaction_id = "TRX-$date-$randNum";
    $checkId = mysqli_query($conn, "SELECT id FROM transactions WHERE id = '$transaction_id'");
    while (mysqli_num_rows($checkId) > 0) {
        $randNum = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $transaction_id = "TRX-$date-$randNum";
        $checkId = mysqli_query($conn, "SELECT id FROM transactions WHERE id = '$transaction_id'");
    }
    $tgl_masuk = date('Y-m-d H:i:s');
    $tgl_estimasi_selesai = date('Y-m-d H:i:s', strtotime("+$estimasi_hari days"));
    $kasir_id = $_SESSION['user_id'];
    $nama_pelanggan = mysqli_real_escape_string($conn, $nama_pelanggan);
    $no_hp = mysqli_real_escape_string($conn, $no_hp);
    $alamat = mysqli_real_escape_string($conn, $alamat);
    $catatan = mysqli_real_escape_string($conn, $catatan);
    $insertQuery = "INSERT INTO transactions 
        (id, nama_pelanggan, no_hp, alamat, package_id, berat_qty, total_harga, 
         status_laundry, status_bayar, tgl_masuk, tgl_estimasi_selesai, kasir_input_id, catatan)
        VALUES 
        ('$transaction_id', '$nama_pelanggan', '$no_hp', '$alamat', $package_id, $berat_qty, $total_harga,
         'Pending', 'Unpaid', '$tgl_masuk', '$tgl_estimasi_selesai', $kasir_id, '$catatan')";
    if (mysqli_query($conn, $insertQuery)) {
        $logQuery = "INSERT INTO status_logs (transaction_id, status_before, status_after, changed_by, catatan)
                     VALUES ('$transaction_id', NULL, 'Pending', $kasir_id, 'Transaksi dibuat oleh kasir')";
        mysqli_query($conn, $logQuery);
        $_SESSION['success'] = "Transaksi berhasil dibuat dengan ID: $transaction_id";
        header("Location: ../pages/cashier/invoice_print.php?id=$transaction_id");
        exit();
    } else {
        $_SESSION['error'] = 'Gagal menyimpan transaksi: ' . mysqli_error($conn);
        header('Location: ../pages/cashier/new_transaction.php');
        exit();
    }
}
/**
 * Proses pengambilan laundry oleh pelanggan
 */
function takeTransaction($conn) {
    if ($_SESSION['role'] !== 'kasir') {
        $_SESSION['error'] = 'Anda tidak memiliki akses!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction_id = $_GET['id'] ?? '';
    if (empty($transaction_id)) {
        $_SESSION['error'] = 'ID transaksi tidak valid!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction_id = mysqli_real_escape_string($conn, $transaction_id);
    $query = mysqli_query($conn, "SELECT * FROM transactions WHERE id = '$transaction_id'");
    if (!$query || mysqli_num_rows($query) === 0) {
        $_SESSION['error'] = 'Transaksi tidak ditemukan!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction = mysqli_fetch_assoc($query);
    if ($transaction['status_laundry'] === 'Taken') {
        $_SESSION['error'] = 'Laundry sudah diambil sebelumnya!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    if ($transaction['status_laundry'] !== 'Done') {
        $_SESSION['error'] = 'Laundry belum selesai! Status saat ini: ' . $transaction['status_laundry'];
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $tgl_diambil = date('Y-m-d H:i:s');
    $kasir_id = $_SESSION['user_id'];
    $status_before = $transaction['status_laundry'];
    $updateQuery = "UPDATE transactions 
                    SET status_laundry = 'Taken', 
                        tgl_diambil = '$tgl_diambil'
                    WHERE id = '$transaction_id'";
    if (mysqli_query($conn, $updateQuery)) {
        $logQuery = "INSERT INTO status_logs (transaction_id, status_before, status_after, changed_by, catatan)
                     VALUES ('$transaction_id', '$status_before', 'Taken', $kasir_id, 'Laundry diambil oleh pelanggan')";
        mysqli_query($conn, $logQuery);
        $_SESSION['success'] = 'Status laundry berhasil diubah menjadi Diambil!';
    } else {
        $_SESSION['error'] = 'Gagal update status: ' . mysqli_error($conn);
    }
    header('Location: ../pages/cashier/transaction_list.php');
    exit();
}
/**
 * Proses pembayaran transaksi
 */
function processPayment($conn) {
    if ($_SESSION['role'] !== 'kasir') {
        $_SESSION['error'] = 'Anda tidak memiliki akses!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction_id = $_POST['transaction_id'] ?? '';
    $jumlah_bayar = floatval($_POST['jumlah_bayar'] ?? 0);
    $metode_bayar = $_POST['metode_bayar'] ?? 'Tunai';
    $catatan = trim($_POST['catatan'] ?? '');
    if (empty($transaction_id) || $jumlah_bayar <= 0 || empty($metode_bayar)) {
        $_SESSION['error'] = 'Data pembayaran tidak lengkap!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction_id = mysqli_real_escape_string($conn, $transaction_id);
    $metode_bayar = mysqli_real_escape_string($conn, $metode_bayar);
    $catatan = mysqli_real_escape_string($conn, $catatan);
    $query = mysqli_query($conn, "SELECT * FROM transactions WHERE id = '$transaction_id'");
    if (!$query || mysqli_num_rows($query) === 0) {
        $_SESSION['error'] = 'Transaksi tidak ditemukan!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction = mysqli_fetch_assoc($query);
    if ($transaction['status_bayar'] === 'Paid') {
        $_SESSION['error'] = 'Transaksi sudah dibayar sebelumnya!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    if ($jumlah_bayar < $transaction['total_harga']) {
        $_SESSION['error'] = 'Jumlah bayar kurang dari total harga!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $kasir_id = $_SESSION['user_id'];
    $created_at = date('Y-m-d H:i:s');
    $updateQuery = "UPDATE transactions 
                    SET status_bayar = 'Paid',
                        kasir_bayar_id = $kasir_id
                    WHERE id = '$transaction_id'";
    if (mysqli_query($conn, $updateQuery)) {
        $paymentQuery = "INSERT INTO payment_history 
                         (transaction_id, jumlah_bayar, metode_bayar, kasir_id, catatan, created_at)
                         VALUES 
                         ('$transaction_id', $jumlah_bayar, '$metode_bayar', $kasir_id, '$catatan', '$created_at')";
        if (mysqli_query($conn, $paymentQuery)) {
            $kembalian = $jumlah_bayar - $transaction['total_harga'];
            $_SESSION['success'] = 'Pembayaran berhasil diproses! Kembalian: Rp' . number_format($kembalian, 0, ',', '.');
        } else {
            $_SESSION['error'] = 'Pembayaran berhasil, tapi gagal menyimpan riwayat: ' . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = 'Gagal memproses pembayaran: ' . mysqli_error($conn);
    }
    header('Location: ../pages/cashier/transaction_list.php');
    exit();
}
/**
 * Hapus transaksi (hanya untuk transaksi yang masih Pending)
 */
function deleteTransaction($conn) {
    if (!in_array($_SESSION['role'], ['kasir', 'admin'])) {
        $_SESSION['error'] = 'Anda tidak memiliki akses!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction_id = $_GET['id'] ?? '';
    if (empty($transaction_id)) {
        $_SESSION['error'] = 'ID transaksi tidak valid!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction_id = mysqli_real_escape_string($conn, $transaction_id);
    $query = mysqli_query($conn, "SELECT * FROM transactions WHERE id = '$transaction_id'");
    if (!$query || mysqli_num_rows($query) === 0) {
        $_SESSION['error'] = 'Transaksi tidak ditemukan!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $transaction = mysqli_fetch_assoc($query);
    if ($transaction['status_laundry'] !== 'Pending' || $transaction['status_bayar'] === 'Paid') {
        $_SESSION['error'] = 'Hanya transaksi Pending dan belum dibayar yang bisa dihapus!';
        header('Location: ../pages/cashier/transaction_list.php');
        exit();
    }
    $deleteQuery = "DELETE FROM transactions WHERE id = '$transaction_id'";
    if (mysqli_query($conn, $deleteQuery)) {
        $_SESSION['success'] = "Transaksi $transaction_id berhasil dihapus!";
    } else {
        $_SESSION['error'] = 'Gagal menghapus transaksi: ' . mysqli_error($conn);
    }
    header('Location: ../pages/cashier/transaction_list.php');
    exit();
}
?>
