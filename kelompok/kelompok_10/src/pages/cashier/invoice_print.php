<?php
// Load config database
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


// --- AMBIL ID TRANSAKSI ---
if (!isset($_GET['id'])) {
    die("ID transaksi tidak ditemukan!");
}

$trx_id = $_GET['id'];


// --- AMBIL DATA TRANSAKSI + PAKET ---
$query = mysqli_query($conn, "
    SELECT t.*, p.nama_paket, p.harga_per_qty, p.satuan
    FROM transactions t
    JOIN packages p ON t.package_id = p.id
    WHERE t.id = '$trx_id'
");

if (!$query || mysqli_num_rows($query) === 0) {
    die("Transaksi tidak ditemukan!");
}

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Struk - <?= $data['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --main-color: #038472;
            --main-dark: #026c5f;
        }

        body {
            background-color: #eef4f3;
            font-family: 'Segoe UI', sans-serif;
            padding: 30px;
        }

        .invoice-header {
            background-color: var(--main-color);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .card-header {
            background-color: var(--main-color);
            color: white;
            font-weight: 600;
        }

        .btn-print {
            background-color: var(--main-color);
            border-color: var(--main-dark);
        }

        .btn-print:hover {
            background-color: var(--main-dark);
        }

        @media print {
            .btn-print,
            .btn-back {
                display: none !important;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="invoice-header">
    🧾 Cetak Struk Transaksi
</div>

<div class="card shadow-sm">
    <div class="card-header">
        Struk Transaksi — <?= $data['id']; ?>
    </div>

    <div class="card-body">

        <h5 class="fw-bold mb-3">Informasi Pelanggan</h5>

        <table class="table table-bordered">
            <tr>
                <th style="width: 30%;">Nama Pelanggan</th>
                <td><?= $data['nama_pelanggan']; ?></td>
            </tr>
            <tr>
                <th>Nomor HP</th>
                <td><?= $data['no_hp']; ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?= $data['alamat'] ?: '-'; ?></td>
            </tr>
        </table>

        <h5 class="fw-bold mt-4 mb-3">Detail Laundry</h5>

        <table class="table table-bordered">
            <tr>
                <th style="width: 30%;">Paket</th>
                <td><?= $data['nama_paket']; ?> (<?= $data['satuan']; ?>)</td>
            </tr>

            <tr>
                <th>Harga per <?= $data['satuan']; ?></th>
                <td>Rp<?= number_format($data['harga_per_qty']); ?></td>
            </tr>

            <tr>
                <th>Berat / Jumlah</th>
                <td><?= $data['berat_qty']; ?> <?= $data['satuan']; ?></td>
            </tr>

            <tr>
                <th>Total Harga</th>
                <td><strong>Rp<?= number_format($data['total_harga']); ?></strong></td>
            </tr>

            <tr>
                <th>Status Laundry</th>
                <td><?= $data['status_laundry']; ?></td>
            </tr>

            <tr>
                <th>Status Pembayaran</th>
                <td><?= $data['status_bayar']; ?></td>
            </tr>

            <tr>
                <th>Tanggal Masuk</th>
                <td><?= $data['tgl_masuk']; ?></td>
            </tr>

            <tr>
                <th>Estimasi Selesai</th>
                <td><?= $data['tgl_estimasi_selesai']; ?></td>
            </tr>
        </table>

        <!-- CATATAN -->
        <h5 class="fw-bold mt-4 mb-2">Catatan</h5>
        <p><?= $data['catatan'] ?: '-'; ?></p>

        <div class="mt-4 d-flex justify-content-between">
            <a href="transactions_list.php" class="btn btn-secondary btn-back">Kembali</a>
            <button class="btn btn-success btn-print" onclick="window.print();">🖨 Cetak Struk</button>
        </div>

    </div>
</div>

</body>
</html>
