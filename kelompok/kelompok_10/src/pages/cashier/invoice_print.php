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
    <title>Cetak Struk - E-Laundry</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --main-color: #038472;
            --main-dark: #026c5f;
        }

        body {
            margin: 0;
            background-color: #eef4f3;
            font-family: 'Courier New', monospace;
            display: flex;
        }

        .content-area {
            width: calc(100% - 250px);
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* Thermal Receipt Style */
        .receipt {
            width: 350px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
            font-size: 13px;
            line-height: 1.4;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .receipt-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .receipt-header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .receipt-body {
            margin-bottom: 15px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .receipt-row.bold {
            font-weight: bold;
            font-size: 14px;
        }

        .receipt-divider {
            border-top: 1px dashed #333;
            margin: 10px 0;
        }

        .receipt-divider.thick {
            border-top: 2px solid #333;
        }

        .receipt-item {
            margin: 8px 0;
        }

        .receipt-total {
            border-top: 2px solid #333;
            padding-top: 8px;
            margin-top: 10px;
        }

        .receipt-footer {
            text-align: center;
            border-top: 2px dashed #333;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 11px;
        }

        .barcode {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-unpaid {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-print {
            background: var(--main-color);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 15px;
            width: 100%;
        }

        .btn-print:hover {
            background: var(--main-dark);
        }

        @media print {
            body {
                background: white;
            }
            .sidebar {
                display: none !important;
            }
            .content-area {
                width: 100%;
                padding: 0;
            }
            .receipt {
                box-shadow: none;
                border: none;
                width: 80mm;
            }
            .btn-print {
                display: none;
            }
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
        
        <!-- Header -->
        <div class="receipt-header">
            <h2>E-LAUNDRY</h2>
            <p>Jl. Raya Laundry No. 123</p>
            <p>Telp: 0812-3456-7890</p>
        </div>

        <!-- Receipt Number Barcode Style -->
        <div class="barcode">
            <?= $transaksi["id"]; ?>
        </div>

        <!-- Transaction Date -->
        <div style="text-align: center; font-size: 11px; margin-bottom: 15px;">
            <?= date("d/m/Y H:i:s", strtotime($transaksi["tgl_masuk"])); ?>
        </div>

        <div class="receipt-divider"></div>

        <!-- Customer Info -->
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

        <!-- Items -->
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

        <!-- Total -->
        <div class="receipt-total">
            <div class="receipt-row bold" style="font-size: 16px;">
                <span>TOTAL</span>
                <span>Rp<?= number_format($transaksi["total_harga"], 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <!-- Status -->
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

        <!-- Footer -->
        <div class="receipt-footer">
            <p style="margin: 5px 0; font-weight: bold;">TERIMA KASIH</p>
            <p style="margin: 5px 0;">Simpan struk ini sebagai bukti</p>
            <p style="margin: 5px 0;">Gunakan NO RESI untuk cek status</p>
            <p style="margin: 10px 0 5px 0; font-size: 10px;">Powered by E-Laundry System</p>
        </div>

        <!-- Print Button -->
        <button class="btn-print" onclick="window.print();">🖨 CETAK STRUK</button>

    </div>

<?php endif; ?>

</div>

</body>
</html>
