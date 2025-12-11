<?php

$baseDir = dirname(__DIR__, 2);

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
    die('Config database tidak ditemukan di folder src/config.');
}

if (!isset($conn)) {
    die('Variabel $conn tidak ditemukan. Pastikan config berisi $conn = mysqli_connect(...);');
}

$paketQuery = mysqli_query($conn, "SELECT * FROM packages ORDER BY nama_paket ASC");
if (!$paketQuery) {
    die("Query paket gagal: " . mysqli_error($conn));
}

$active_page = 'new_transaction';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Baru - E-Laundry</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --main-color: #038472;
            --main-dark: #026c5f;
        }

        body {
            margin: 0;
            background-color: #f4f6f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .content-area {
            margin-left: 0%px; 
            padding: 30px;
            width: calc(100% - 250px); 
        }

        .page-header {
            background-color: var(--main-color);
            color: #fff;
            padding: 14px 30px;
            border-radius: 10px;
            font-size: 22px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 25px;
        }

        .card {
            width: 100%;
        }

        .card-header {
            background-color: var(--main-color) !important;
            color: #fff !important;
            font-weight: 600;
        }

        .btn-success {
            background-color: var(--main-color) !important;
            border-color: var(--main-dark) !important;
        }

        .btn-success:hover {
            background-color: var(--main-dark) !important;
        }
    </style>
</head>
<body>

<?php include $baseDir . '/includes/sidebar_cashier.php'; ?>

<div class="content-area">

    <div class="page-header">
        🧺 Form Terima Cucian
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            Input Transaksi Baru
        </div>

        <div class="card-body">

            <form action="../../process/new_transaction_process.php" method="POST">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat (Opsional)</label>
                    <textarea name="alamat" class="form-control" rows="2"></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Paket Laundry</label>
                        <select name="package_id" class="form-select" required>
                            <option value="">-- Pilih Paket --</option>
                            <?php while ($row = mysqli_fetch_assoc($paketQuery)) : ?>
                                <option value="<?= $row['id']; ?>">
                                    <?= $row['nama_paket']; ?> - Rp<?= number_format($row['harga_per_qty'], 0, ',', '.'); ?>/<?= $row['satuan']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Berat / Jumlah</label>
                        <input type="number" name="berat_qty" class="form-control" min="1" step="0.1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="transaction_list.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
