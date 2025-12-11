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
    $id = intval($_GET["id"]);

    $query = mysqli_query($conn, "
        SELECT t.*, p.nama_paket, p.harga_per_qty, p.satuan
        FROM transactions t
        JOIN packages p ON t.package_id = p.id
        WHERE t.id = $id
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
            font-family: 'Segoe UI', sans-serif;
            display: flex;
        }

        .content-area {
            width: calc(100% - 250px);
            padding: 30px;
        }

        .card-header {
            background: var(--main-color) !important;
            color: white !important;
            font-weight: bold;
        }

        .btn-print {
            background: var(--main-color);
            color: white;
        }
        .btn-print:hover {
            background: var(--main-dark);
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

    <div class="card shadow-sm" style="max-width: 600px;">
        <div class="card-header">🧾 Struk Transaksi Laundry</div>

        <div class="card-body">

            <h5><strong><?= $transaksi["nama_pelanggan"]; ?></strong></h5>
            <p>No. HP: <?= $transaksi["no_hp"]; ?></p>
            <p>Tanggal Masuk: <?= date("d/m/Y H:i", strtotime($transaksi["tgl_masuk"])); ?></p>

            <hr>

            <p>Paket: <strong><?= $transaksi["nama_paket"]; ?></strong></p>
            <p>Harga per <?= $transaksi["satuan"]; ?>: Rp<?= number_format($transaksi["harga_per_qty"]); ?></p>
            <p>Qty / Berat: <?= $transaksi["berat_qty"]; ?></p>

            <h4>Total: <strong>Rp<?= number_format($transaksi["total_harga"]); ?></strong></h4>

            <hr>

            <p>Status Laundry: <strong><?= $transaksi["status_laundry"]; ?></strong></p>
            <p>Status Pembayaran: <strong><?= $transaksi["status_bayar"]; ?></strong></p>

            <button class="btn btn-print mt-3" onclick="window.print();">🖨 Cetak</button>

        </div>
    </div>

<?php endif; ?>

</div>

</body>
</html>
