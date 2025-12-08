<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$summary_query = "SELECT * FROM v_dashboard_summary";
$summary_result = mysqli_query($conn, $summary_query);
$summary = mysqli_fetch_assoc($summary_result);

$recent_transactions_query = "SELECT t.id, t.nama_pelanggan, t.status_laundry, t.status_bayar, 
                                      t.total_harga, t.tgl_masuk, p.nama_paket
                               FROM transactions t
                               JOIN packages p ON t.package_id = p.id
                               ORDER BY t.tgl_masuk DESC
                               LIMIT 5";
$recent_transactions = mysqli_query($conn, $recent_transactions_query);

$page_title = "Dashboard";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - E-Laundry</title>
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="admin-container">
        <?php include '../../includes/sidebar_admin.php'; ?>
        
        <main class="main-content">
            <?php include '../../includes/header_admin.php'; ?>
            
            <div class="content-wrapper">
                <div class="stats-grid">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon">
                            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="#008080" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Total Transaksi</h3>
                            <p class="stat-value"><?php echo number_format($summary['total_transaksi'] ?? 0); ?></p>
                        </div>
                    </div>

                    <div class="stat-card stat-warning">
                        <div class="stat-icon">
                            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="#FFC107" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Sedang Proses</h3>
                            <p class="stat-value"><?php echo number_format($summary['sedang_proses'] ?? 0); ?></p>
                        </div>
                    </div>

                    <div class="stat-card stat-success">
                        <div class="stat-icon">
                            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Siap Diambil</h3>
                            <p class="stat-value"><?php echo number_format($summary['siap_diambil'] ?? 0); ?></p>
                        </div>
                    </div>

                    <div class="stat-card stat-danger">
                        <div class="stat-icon">
                            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Belum Dibayar</h3>
                            <p class="stat-value"><?php echo number_format($summary['belum_dibayar'] ?? 0); ?></p>
                        </div>
                    </div>
                </div>

                <div class="revenue-grid">
                    <div class="revenue-card">
                        <h3>Pendapatan Hari Ini</h3>
                        <p class="revenue-value">Rp <?php echo number_format($summary['pendapatan_hari_ini'] ?? 0, 0, ',', '.'); ?></p>
                    </div>
                    <div class="revenue-card">
                        <h3>Pendapatan Bulan Ini</h3>
                        <p class="revenue-value">Rp <?php echo number_format($summary['pendapatan_bulan_ini'] ?? 0, 0, ',', '.'); ?></p>
                    </div>
                </div>

                <div class="recent-transactions">
                    <div class="card-header">
                        <h2>Transaksi Terbaru</h2>
                        <a href="transactions.php" class="btn-link">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode Resi</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Paket</th>
                                    <th>Total</th>
                                    <th>Status Laundry</th>
                                    <th>Status Bayar</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent_transactions) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($recent_transactions)): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_paket']); ?></td>
                                            <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($row['status_laundry']); ?>">
                                                    <?php echo $row['status_laundry']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($row['status_bayar']); ?>">
                                                    <?php echo $row['status_bayar']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_masuk'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">Belum ada transaksi</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
