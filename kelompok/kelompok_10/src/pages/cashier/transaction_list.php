<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
    header('Location: ../auth/login.php');
    exit();
}
$cashier_id = $_SESSION['user_id']; 
$cashier_name = $_SESSION['full_name']; 
$cashier_role = "Kasir";
$baseDir = dirname(__DIR__, 2);
$configPaths = [
    $baseDir . '/config/config.php',
    $baseDir . '/config/database.php',
    $baseDir . '/config/db.php',
    $baseDir . '/config/koneksi.php',
    $baseDir . '/config/connection.php',
    $baseDir . '/config.php'
];
$configLoaded = false;
foreach ($configPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $configLoaded = true;
        break;
    }
}
if (!$configLoaded) die("Config database tidak ditemukan!");
if (!isset($conn)) die("Variabel \$conn tidak tersedia!");
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$whereClause = "1=1";
switch ($filter) {
    case 'today':
        $whereClause = "DATE(t.tgl_masuk) = CURDATE()";
        break;
    case 'week':
        $whereClause = "YEARWEEK(t.tgl_masuk, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'month':
        $whereClause = "MONTH(t.tgl_masuk) = MONTH(CURDATE()) AND YEAR(t.tgl_masuk) = YEAR(CURDATE())";
        break;
    case 'custom':
        if (!empty($start_date) && !empty($end_date)) {
            $whereClause = "DATE(t.tgl_masuk) BETWEEN '$start_date' AND '$end_date'";
        }
        break;
    case 'all':
    default:
        $whereClause = "1=1";
        break;
}
$query = mysqli_query($conn, "
    SELECT t.*, p.nama_paket
    FROM transactions t
    JOIN packages p ON t.package_id = p.id
    WHERE $whereClause
    ORDER BY t.tgl_masuk DESC
");
$transactions = [];
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $transactions[] = $row;
    }
}
$active_page = "transaction_list";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Transaksi - Zira Laundry</title>
    <link href="https:
    <link rel="stylesheet" href="../../assets/css/cashier.css">
</head>
<body>
<?php include $baseDir . "/includes/sidebar_cashier.php"; ?>
<div class="content-area">
    <div class="page-header">
        <span>📄 Daftar Transaksi Laundry</span>
        <div class="profile-info">
            <div class="text">
                <span class="username"><?php echo htmlspecialchars($cashier_name); ?></span>
                <span class="role"><?php echo htmlspecialchars($cashier_role); ?></span>
            </div>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="profile-icon">
                <path d="M5.52 19c.4-.82.8-1.64 1.2-2.46l.48-.96c.07-.13.15-.24.23-.35.48-.6.94-1.2 1.4-1.8H14.8c.46.6  .92 1.2 1.4 1.8.08.11.16.22.23.35l.48.96c.4.82.8 1.64 1.2 2.46"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
    </div>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h6 class="mb-3">Filter Berdasarkan Tanggal</h6>
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter" class="form-label">Periode</label>
                    <select name="filter" id="filter" class="form-select" onchange="toggleCustomDate()">
                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Semua Data</option>
                        <option value="today" <?= $filter == 'today' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="week" <?= $filter == 'week' ? 'selected' : '' ?>>Minggu Ini</option>
                        <option value="month" <?= $filter == 'month' ? 'selected' : '' ?>>Bulan Ini</option>
                        <option value="custom" <?= $filter == 'custom' ? 'selected' : '' ?>>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3" id="customDate" style="display: <?= $filter == 'custom' ? 'block' : 'none' ?>;">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                </div>
                <div class="col-md-3" id="customEndDate" style="display: <?= $filter == 'custom' ? 'block' : 'none' ?>;">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary" style="background: var(--main-color); border: none;">
                        <svg width="16" height="16" fill="currentColor" class="bi bi-search me-1" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Filter
                    </button>
                    <?php if ($filter != 'all'): ?>
                        <a href="transaction_list.php" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header">Semua Transaksi</div>
        <div class="card-body">
            <table class="table table-bordered table-striped text-center">
                <thead style="background:#d7efe9;">
                    <tr>
                        <th>No Resi</th>
                        <th>Pelanggan</th>
                        <th>Paket</th>
                        <th>Total Harga</th>
                        <th>Status Laundry</th>
                        <th>Status Bayar</th>
                        <th>Masuk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($transactions as $row) : ?>
                    <tr>
                        <td><strong><?= $row['id']; ?></strong></td>
                        <td><?= $row['nama_pelanggan']; ?></td>
                        <td><?= $row['nama_paket']; ?></td>
                        <td>Rp<?= number_format($row['total_harga']); ?></td>
                        <td>
                            <span class="badge-status <?= strtolower($row['status_laundry']); ?>">
                                <?= $row['status_laundry']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-status <?= strtolower($row['status_bayar']); ?>">
                                <?= $row['status_bayar']; ?>
                            </span>
                        </td>
                        <td><?= date("d/m/Y H:i", strtotime($row['tgl_masuk'])); ?></td>
                        <td>
                            <a href="invoice_print.php?id=<?= urlencode($row['id']); ?>" class="btn btn-teal btn-sm mb-1">
                                🧾 Struk
                            </a>
                            <?php if ($row['status_bayar'] === 'Unpaid'): ?>
                                <button class="btn btn-success btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#paymentModal<?= $row['id']; ?>">
                                    💰 Bayar
                                </button>
                            <?php endif; ?>
                            <?php if ($row['status_laundry'] === 'Done' && $row['status_laundry'] !== 'Taken'): ?>
                                <a href="../../process/transaction_handler.php?action=take&id=<?= urlencode($row['id']); ?>" 
                                   class="btn btn-warning btn-sm"
                                   onclick="return confirm('Konfirmasi pengambilan laundry oleh pelanggan?')">
                                    ✔ Ambil
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <div class="modal fade" id="paymentModal<?= $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background: var(--main-color); color: white;">
                                    <h5 class="modal-title">💰 Proses Pembayaran</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="../../process/transaction_handler.php?action=payment" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="transaction_id" value="<?= $row['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">No Resi</label>
                                            <input type="text" class="form-control" value="<?= $row['id']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pelanggan</label>
                                            <input type="text" class="form-control" value="<?= $row['nama_pelanggan']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total Harga</label>
                                            <input type="text" class="form-control" value="Rp<?= number_format($row['total_harga']); ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Metode Pembayaran *</strong></label>
                                            <select name="metode_bayar" class="form-select" required>
                                                <option value="">-- Pilih Metode --</option>
                                                <option value="Tunai">Tunai</option>
                                                <option value="QRIS">QRIS</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Jumlah Bayar *</strong></label>
                                            <input type="number" name="jumlah_bayar" class="form-control" 
                                                   min="<?= $row['total_harga']; ?>" 
                                                   step="1000" 
                                                   value="<?= $row['total_harga']; ?>" 
                                                   required>
                                            <small class="text-muted">Minimal: Rp<?= number_format($row['total_harga']); ?></small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Catatan (Opsional)</label>
                                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">✔ Konfirmasi Pembayaran</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https:
<script>
function toggleCustomDate() {
    const filterType = document.getElementById('filter').value;
    const customDate = document.getElementById('customDate');
    const customEndDate = document.getElementById('customEndDate');
    if (filterType === 'custom') {
        customDate.style.display = 'block';
        customEndDate.style.display = 'block';
    } else {
        customDate.style.display = 'none';
        customEndDate.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleCustomDate();
});
</script>
</body>
</html>
