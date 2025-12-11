<?php
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
if (!$configLoaded) {
    $error_message = "Config database tidak ditemukan!";
}
if (!isset($conn)) {
    $error_message = "Variabel \$conn tidak ditemukan!";
}
$transaksi = null;
if (!isset($_GET["id"])) {
    $error_message = "ID transaksi tidak ditemukan!";
} else {
    $id = mysqli_real_escape_string($conn, $_GET["id"]);
    $query = mysqli_query($conn, "
        SELECT t.*, p.nama_paket, p.harga_per_qty, p.satuan
        FROM transactions t
        JOIN packages p ON t.package_id = p.id
        WHERE t.id = '$id'
    ");
    if ($query && mysqli_num_rows($query) > 0) {
        $transaksi = mysqli_fetch_assoc($query);
    } else {
        $error_message = "Transaksi dengan ID $id tidak ditemukan!";
    }
}
$active_page = "invoice_print";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Struk - Zira Laundry</title>
    <link href="https:
    <link rel="stylesheet" href="../../assets/css/cashier.css">
    <style>
        body {
            font-family: 'Courier New', monospace;
        }
        .content-area {
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php include $baseDir . "/includes/sidebar_cashier.php"; ?>
<div class="content-area">
<?php if (isset($error_message)) : ?>
    <div class="alert alert-danger">
        <strong>Error:</strong> <?= $error_message; ?>
    </div>
<?php else: ?>
    <div class="receipt">
        <div class="receipt-header">
            <h2>ZIRA LAUNDRY</h2>
            <p>Jl. Raya Laundry No. 123</p>
            <p>Telp: 0812-3456-7890</p>
        </div>
        <div class="barcode">
            <?= $transaksi["id"]; ?>
        </div>
        <div style="text-align: center; font-size: 11px; margin-bottom: 15px;">
            <?= date("d/m/Y H:i:s", strtotime($transaksi["tgl_masuk"])); ?>
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-body">
            <div class="receipt-row bold">
                <span>PELANGGAN</span>
            </div>
            <div class="receipt-row">
                <span>Nama:</span>
                <span><?= $transaksi["nama_pelanggan"]; ?></span>
            </div>
            <div class="receipt-row">
                <span>No. HP:</span>
                <span><?= $transaksi["no_hp"]; ?></span>
            </div>
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-body">
            <div class="receipt-row bold">
                <span>DETAIL PESANAN</span>
            </div>
            <div class="receipt-item">
                <div class="receipt-row">
                    <span><?= $transaksi["nama_paket"]; ?></span>
                </div>
                <div class="receipt-row">
                    <span><?= $transaksi["berat_qty"]; ?> <?= $transaksi["satuan"]; ?> x Rp<?= number_format($transaksi["harga_per_qty"], 0, ',', '.'); ?></span>
                    <span>Rp<?= number_format($transaksi["total_harga"], 0, ',', '.'); ?></span>
                </div>
            </div>
            <?php if (!empty($transaksi["catatan"])): ?>
            <div class="receipt-row" style="font-size: 11px; font-style: italic; margin-top: 5px;">
                <span>Catatan: <?= $transaksi["catatan"]; ?></span>
            </div>
            <?php endif; ?>
        </div>
        <div class="receipt-divider thick"></div>
        <div class="receipt-total">
            <div class="receipt-row bold" style="font-size: 16px;">
                <span>TOTAL</span>
                <span>Rp<?= number_format($transaksi["total_harga"], 0, ',', '.'); ?></span>
            </div>
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-body">
            <div class="receipt-row">
                <span>Status Laundry:</span>
                <span style="font-weight: bold;"><?= $transaksi["status_laundry"]; ?></span>
            </div>
            <div class="receipt-row">
                <span>Status Bayar:</span>
                <span class="status-badge status-<?= strtolower($transaksi["status_bayar"]); ?>">
                    <?= $transaksi["status_bayar"]; ?>
                </span>
            </div>
            <?php if ($transaksi["tgl_estimasi_selesai"]): ?>
            <div class="receipt-row">
                <span>Estimasi Selesai:</span>
                <span><?= date("d/m/Y", strtotime($transaksi["tgl_estimasi_selesai"])); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <div class="receipt-footer">
            <p style="margin: 5px 0; font-weight: bold;">TERIMA KASIH</p>
            <p style="margin: 5px 0;">Simpan struk ini sebagai bukti</p>
            <p style="margin: 5px 0;">Gunakan NO RESI untuk cek status</p>
            <p style="margin: 10px 0 5px 0; font-size: 10px;">Powered by Zira Laundry System</p>
        </div>
        <button class="btn-print" onclick="window.print();">🖨 CETAK STRUK</button>
    </div>
<?php endif; ?>
</div>
</body>
</html>
