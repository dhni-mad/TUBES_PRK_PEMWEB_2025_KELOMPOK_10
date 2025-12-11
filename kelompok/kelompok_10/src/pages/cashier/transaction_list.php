<?php
session_start();

// Cek apakah user sudah login dan rolenya kasir
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
    header('Location: ../auth/login.php');
    exit();
}

// Ambil data user yang login dari session
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

$query = mysqli_query($conn, "
    SELECT t.*, p.nama_paket
    FROM transactions t
    JOIN packages p ON t.package_id = p.id
    ORDER BY t.tgl_masuk DESC
");

// Store results untuk digunakan di modal juga
$transactions = [];
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $transactions[] = $row;
    }
}

// Reset query pointer
$query = mysqli_query($conn, "
    SELECT t.*, p.nama_paket
    FROM transactions t
    JOIN packages p ON t.package_id = p.id
    ORDER BY t.tgl_masuk DESC
");

$active_page = "transaction_list";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Transaksi - E-Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --main-color: #038472;
            --main-dark: #026c5f;
        }

        body {
            background-color: #eef4f3;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
        }

        .content-area {
            margin-left: 0%px !important;
            padding: 30px;
            width: calc(100% - 250px);
        }

        .page-header {
            background: var(--main-color);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-info .text {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            text-align: right;
        }

        .profile-info .username {
            font-size: 15px;
            font-weight: 600;
            color: #fff;
        }

        .profile-info .role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
        }

        .profile-icon {
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 6px;
        }

        .card-header {
            background-color: var(--main-color) !important;
            color: white !important;
            font-weight: 600;
        }

        .badge-status {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            color: white;
        }
        .pending { background: #ffc107 !important; color: black !important; }
        .washing { background: #0dcaf0 !important; }
        .ironing { background: #6f42c1 !important; }
        .done    { background: #198754 !important; }
        .taken   { background: #6c757d !important; }

        .unpaid { background: #dc3545 !important; }
        .paid   { background: #198754 !important; }

        .btn-teal {
            background-color: var(--main-color);
            border-color: var(--main-dark);
            color: white;
        }
        .btn-teal:hover {
            background-color: var(--main-dark);
        }
    </style>
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
                <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                    <tr>
                        <td><strong><?= $row['id']; ?></strong></td>
                        <td><?= $row['nama_pelanggan']; ?></td>
                        <td><?= $row['nama_paket']; ?></td>
                        <td>Rp<?= number_format($row['total_harga']); ?></td>

                        <!-- Status Laundry -->
                        <td>
                            <span class="badge-status <?= strtolower($row['status_laundry']); ?>">
                                <?= $row['status_laundry']; ?>
                            </span>
                        </td>

                        <!-- Status Pembayaran -->
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

                    <!-- Modal Pembayaran -->
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

                <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
