<?php
session_start();
require_once '../../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
$bulan_indo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
$filter_type = $_GET['filter_type'] ?? 'date';
$where_clause = "1=1";
$date_label = "";
switch ($filter_type) {
    case 'all':
        $where_clause = "1=1";
        $date_label = "Semua Data";
        break;
    case 'today':
        $where_clause = "DATE(t.tgl_masuk) = CURDATE()";
        $date_label = "Hari Ini - " . date('d/m/Y');
        break;
    case 'week':
        $where_clause = "YEARWEEK(t.tgl_masuk, 1) = YEARWEEK(CURDATE(), 1)";
        $date_label = "Minggu Ini";
        break;
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
    case 'custom':
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';
        if (!empty($start_date) && !empty($end_date)) {
            $where_clause = "DATE(t.tgl_masuk) BETWEEN '$start_date' AND '$end_date'";
            $date_label = date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));
        } else {
            $where_clause = "1=1";
            $date_label = "Custom Range (belum dipilih)";
        }
        break;
}
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
$filename = "Laporan_Transaksi_" . date('Y-m-d_His') . ".xls";
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .header-section { margin-bottom: 20px; }
        .header-section h1 { 
            color: #038472; 
            font-size: 20px; 
            margin: 0;
            text-align: center;
        }
        .info-table { 
            width: 100%; 
            margin-bottom: 20px; 
            border-collapse: collapse;
        }
        .info-table td { 
            padding: 5px; 
            font-size: 12px;
        }
        .info-table td:first-child { 
            font-weight: bold; 
            width: 150px;
        }
        .stats-section {
            background-color: #f0f8f7;
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #038472;
        }
        .stats-section h2 {
            color: #038472;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }
        .stats-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .stats-table td:first-child {
            font-weight: bold;
            background-color: #e8f5f3;
            width: 200px;
        }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        .data-table th { 
            background-color: #038472; 
            color: white; 
            padding: 10px; 
            text-align: center;
            border: 1px solid #026c5f;
            font-weight: bold;
        }
        .data-table td { 
            padding: 8px; 
            border: 1px solid #ddd; 
            text-align: center;
        }
        .data-table tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        .badge-done { 
            background-color: #28a745; 
            color: white; 
            padding: 4px 8px;
            border-radius: 3px;
        }
        .badge-pending { 
            background-color: #ffc107; 
            color: black; 
            padding: 4px 8px;
            border-radius: 3px;
        }
        .badge-paid { 
            background-color: #28a745; 
            color: white; 
            padding: 4px 8px;
            border-radius: 3px;
        }
        .badge-unpaid { 
            background-color: #dc3545; 
            color: white; 
            padding: 4px 8px;
            border-radius: 3px;
        }
        .total-row td {
            font-weight: bold;
            background-color: #e8f5f3 !important;
        }
    </style>
</head>
<body>
    <div class="header-section">
        <h1>LAPORAN TRANSAKSI ZIRA LAUNDRY</h1>
    </div>
    <table class="info-table">
        <tr>
            <td>Periode</td>
            <td>: <?php echo $date_label; ?></td>
        </tr>
        <tr>
            <td>Dicetak Tanggal</td>
            <td>: <?php echo date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr>
            <td>Dicetak Oleh</td>
            <td>: <?php echo htmlspecialchars($_SESSION['full_name']); ?></td>
        </tr>
    </table>
    <div class="stats-section">
        <h2>📊 RINGKASAN</h2>
        <table class="stats-table">
            <tr>
                <td>Total Transaksi</td>
                <td><?php echo number_format($stats['total_transaksi'] ?? 0); ?> transaksi</td>
            </tr>
            <tr>
                <td>Transaksi Lunas</td>
                <td style="color: #28a745; font-weight: bold;"><?php echo number_format($stats['transaksi_lunas'] ?? 0); ?> transaksi</td>
            </tr>
            <tr>
                <td>Transaksi Belum Lunas</td>
                <td style="color: #dc3545; font-weight: bold;"><?php echo number_format($stats['transaksi_belum_lunas'] ?? 0); ?> transaksi</td>
            </tr>
            <tr>
                <td>Total Pendapatan (Lunas)</td>
                <td style="color: #28a745; font-weight: bold; font-size: 14px;">Rp <?php echo number_format($stats['total_pendapatan'] ?? 0); ?></td>
            </tr>
            <tr>
                <td>Total Berat Laundry</td>
                <td><?php echo number_format($stats['total_berat'] ?? 0, 2); ?> kg</td>
            </tr>
        </table>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No Resi</th>
                <th>Pelanggan</th>
                <th>No HP</th>
                <th>Paket</th>
                <th>Berat/Qty</th>
                <th>Total Harga</th>
                <th>Status Laundry</th>
                <th>Status Bayar</th>
                <th>Tgl Masuk</th>
                <th>Estimasi Selesai</th>
                <th>Kasir</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_harga_semua = 0;
            $total_berat_semua = 0;
            while ($row = mysqli_fetch_assoc($transactions_result)): 
                $total_harga_semua += $row['total_harga'];
                $total_berat_semua += $row['berat_qty'];
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_paket']); ?></td>
                <td><?php echo number_format($row['berat_qty'], 2); ?> <?php echo htmlspecialchars($row['satuan']); ?></td>
                <td style="text-align: right;">Rp <?php echo number_format($row['total_harga']); ?></td>
                <td>
                    <span class="badge-<?php echo strtolower($row['status_laundry']); ?>">
                        <?php echo htmlspecialchars($row['status_laundry']); ?>
                    </span>
                </td>
                <td>
                    <span class="badge-<?php echo strtolower($row['status_bayar']); ?>">
                        <?php echo htmlspecialchars($row['status_bayar']); ?>
                    </span>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_masuk'])); ?></td>
                <td><?php echo $row['tgl_estimasi_selesai'] ? date('d/m/Y H:i', strtotime($row['tgl_estimasi_selesai'])) : '-'; ?></td>
                <td><?php echo htmlspecialchars($row['kasir_nama'] ?? '-'); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">TOTAL</td>
                <td><?php echo number_format($total_berat_semua, 2); ?> kg</td>
                <td style="text-align: right;">Rp <?php echo number_format($total_harga_semua); ?></td>
                <td colspan="5"></td>
            </tr>
        </tbody>
    </table>
    <div style="margin-top: 30px; padding: 10px; background-color: #f0f0f0; border-left: 4px solid #038472;">
        <small>
            <strong>Catatan:</strong><br>
            - Laporan ini digenerate otomatis oleh sistem Zira Laundry<br>
            - Total Pendapatan hanya menghitung transaksi yang sudah lunas (Paid)<br>
            - Data dapat berubah sesuai dengan aktivitas transaksi terbaru
        </small>
    </div>
</body>
</html>
<?php
exit();
?>
