<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Bulan Indonesia array
$bulan_indo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Determine filter type
$filter_type = $_GET['filter_type'] ?? 'date';
$where_clause = "1=1";
$date_label = "";

switch ($filter_type) {
    case 'date':
        $selected_date = $_GET['date'] ?? date('Y-m-d');
        $where_clause = "DATE(t.tgl_masuk) = '$selected_date'";
        $date_parts = explode('-', $selected_date);
        $date_label = $date_parts[2] . ' ' . $bulan_indo[$date_parts[1]] . ' ' . $date_parts[0];
        break;
        
    case 'month':
        $selected_month = $_GET['month'] ?? date('m');
        $selected_month_year = $_GET['month_year'] ?? date('Y');
        $where_clause = "MONTH(t.tgl_masuk) = '$selected_month' AND YEAR(t.tgl_masuk) = '$selected_month_year'";
        $date_label = $bulan_indo[$selected_month] . ' ' . $selected_month_year;
        break;
}

// Query untuk statistik
$stats_query = "SELECT 
                    COUNT(*) as total_transaksi,
                    SUM(CASE WHEN t.status_bayar = 'Paid' THEN 1 ELSE 0 END) as transaksi_lunas,
                    SUM(CASE WHEN t.status_bayar = 'Unpaid' THEN 1 ELSE 0 END) as transaksi_belum_lunas,
                    COALESCE(SUM(CASE WHEN t.status_bayar = 'Paid' THEN t.total_harga ELSE 0 END), 0) as total_pendapatan,
                    COALESCE(SUM(t.berat_qty), 0) as total_berat
                FROM transactions t
                WHERE $where_clause";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Handle NULL values
$stats['total_transaksi'] = $stats['total_transaksi'] ?? 0;
$stats['transaksi_lunas'] = $stats['transaksi_lunas'] ?? 0;
$stats['transaksi_belum_lunas'] = $stats['transaksi_belum_lunas'] ?? 0;
$stats['total_pendapatan'] = $stats['total_pendapatan'] ?? 0;
$stats['total_berat'] = $stats['total_berat'] ?? 0;

// Query untuk daftar transaksi
$transactions_query = "SELECT t.id, t.nama_pelanggan, t.no_hp, t.berat_qty, t.total_harga,
                              t.status_laundry, t.status_bayar, t.tgl_masuk, t.tgl_estimasi_selesai,
                              p.nama_paket, p.satuan,
                              u.full_name as kasir_nama
                       FROM transactions t
                       JOIN packages p ON t.package_id = p.id
                       LEFT JOIN users u ON t.kasir_input_id = u.id
                       WHERE $where_clause
                       ORDER BY t.tgl_masuk DESC";
$transactions_result = mysqli_query($conn, $transactions_query);

$page_title = "Laporan Transaksi";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - E-Laundry</title>
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="admin-container">
        <?php include '../../includes/sidebar_admin.php'; ?>
        
        <main class="main-content">
            <?php include '../../includes/header_admin.php'; ?>
            
            <div class="content-wrapper">
                <div class="page-header">
                    <h1>Laporan Transaksi</h1>
                    <div class="header-actions">
                    <!-- Filter Dropdown -->
                    <div class="filter-dropdown-wrapper">
                        <button class="btn-filter" onclick="toggleFilterDropdown()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            Filter
                        </button>
                        
                        <div class="filter-dropdown-menu" id="filterDropdown">
                            <div class="filter-dropdown-header">Pilih Filter</div>
                            
                            <!-- Filter by Date -->
                            <div class="filter-option-group">
                                <div class="filter-option-title">Per Tanggal</div>
                                <form action="" method="get" class="filter-form">
                                    <input type="hidden" name="filter_type" value="date">
                                    <input type="date" name="date" class="filter-date-input" 
                                           onchange="this.form.submit()"
                                           value="<?php echo $filter_type == 'date' ? $selected_date : ''; ?>">
                                </form>
                            </div>
                            
                            <hr class="filter-divider">
                            
                            <!-- Filter by Month -->
                            <div class="filter-option-group">
                                <div class="filter-option-title">Per Bulan</div>
                                <form action="" method="get" class="filter-form">
                                    <input type="hidden" name="filter_type" value="month">
                                    <div class="filter-month-inputs">
                                        <select name="month" class="filter-month-select" onchange="this.form.submit()">
                                            <?php foreach($bulan_indo as $num => $nama): ?>
                                                <option value="<?php echo $num; ?>" 
                                                    <?php echo ($filter_type == 'month' && $selected_month == $num) ? 'selected' : ''; ?>>
                                                    <?php echo $nama; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select name="month_year" class="filter-month-select" onchange="this.form.submit()">
                                            <?php 
                                            $current_year = date('Y');
                                            for($y = $current_year; $y >= $current_year - 5; $y--): 
                                            ?>
                                                <option value="<?php echo $y; ?>"
                                                    <?php echo ($filter_type == 'month' && $selected_month_year == $y) ? 'selected' : ''; ?>>
                                                    <?php echo $y; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Button -->
                    <a href="export_laporan.php?filter_type=<?php echo $filter_type; ?><?php 
                        if($filter_type == 'date') echo '&date=' . $selected_date;
                        elseif($filter_type == 'month') echo '&month=' . $selected_month . '&month_year=' . $selected_month_year;
                    ?>" class="btn-export">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Export
                    </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-mini-grid">
                    <div class="stat-mini-card">
                        <h3>Total Transaksi</h3>
                        <div class="number"><?php echo number_format((int)$stats['total_transaksi']); ?></div>
                        <div class="subtitle">transaksi</div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>Transaksi Lunas</h3>
                        <div class="number" style="color: #28a745;"><?php echo number_format((int)$stats['transaksi_lunas']); ?></div>
                        <div class="subtitle">transaksi</div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>Belum Lunas</h3>
                        <div class="number" style="color: #dc3545;"><?php echo number_format((int)$stats['transaksi_belum_lunas']); ?></div>
                        <div class="subtitle">transaksi</div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>Total Pendapatan</h3>
                        <div class="number" style="font-size: 20px;">Rp <?php echo number_format((float)$stats['total_pendapatan'], 0, ',', '.'); ?></div>
                        <div class="subtitle">dari transaksi lunas</div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Daftar Transaksi</h2>
                        <div class="date-range-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <?php echo $date_label; ?>
                        </div>
                    </div>
                    
                    <?php if (mysqli_num_rows($transactions_result) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Kode Resi</th>
                                    <th style="width: 140px;">Tanggal</th>
                                    <th style="width: 180px;">Pelanggan</th>
                                    <th style="width: 150px;">Paket</th>
                                    <th style="width: 100px;">Jumlah</th>
                                    <th style="width: 120px;">Total</th>
                                    <th style="width: 130px;">Status Laundry</th>
                                    <th style="width: 120px;">Status Bayar</th>
                                    <th style="width: 120px;">Kasir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($trx = mysqli_fetch_assoc($transactions_result)): ?>
                                <tr>
                                    <td><span class="transaction-id"><?php echo htmlspecialchars($trx['id']); ?></span></td>
                                    <td style="white-space: nowrap;"><?php echo date('d/m/Y H:i', strtotime($trx['tgl_masuk'])); ?></td>
                                    <td>
                                        <div style="line-height: 1.4;">
                                            <strong style="display: block; margin-bottom: 3px;"><?php echo htmlspecialchars($trx['nama_pelanggan']); ?></strong>
                                            <small style="color: #666;"><?php echo htmlspecialchars($trx['no_hp']); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($trx['nama_paket']); ?></td>
                                    <td><?php echo $trx['berat_qty']; ?> <?php echo strtoupper($trx['satuan']); ?></td>
                                    <td><strong style="color: #008080;">Rp <?php echo number_format($trx['total_harga'], 0, ',', '.'); ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($trx['status_laundry']); ?>">
                                            <?php echo $trx['status_laundry']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($trx['status_bayar']); ?>">
                                            <?php echo $trx['status_bayar'] === 'Paid' ? 'Lunas' : 'Belum Lunas'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($trx['kasir_nama'] ?? '-'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        <p>Tidak ada transaksi pada periode yang dipilih</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleFilterDropdown() {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('filterDropdown');
            const wrapper = document.querySelector('.filter-dropdown-wrapper');
            
            if (wrapper && !wrapper.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
