<?php
// Load Config Database
$baseDir = dirname(__DIR__, 2);

$configPaths = [
    $baseDir . '/config/config.php',
    $baseDir . '/config/database.php',
    $baseDir . '/config/db.php',
    $baseDir . '/config/koneksi.php',
    $baseDir . '/config/connection.php',
    $baseDir . '/config.php',
    $baseDir . '/database/config.php',
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
    die("File config database tidak ditemukan!");
}

if (!isset($conn)) {
    die("Variabel \$conn tidak tersedia. Periksa file config.");
}

// Ambil paket aktif
$paketQuery = mysqli_query($conn, "SELECT * FROM packages WHERE is_active = TRUE ORDER BY nama_paket ASC");
if (!$paketQuery) {
    die("Query paket gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi Baru - E-Laundry</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Tema warna mengikuti sidebar kasir */
        :root {
            --main-color: #038472;
            --main-color-dark: #026c5f;
        }

        body {
            background-color: #eef4f3;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Header halaman */
        .page-header {
            background-color: var(--main-color);
            color: white;
            padding: 18px 28px;
            border-radius: 10px;
            display: inline-block;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        /* Card properti */
        .card-header {
            background-color: var(--main-color) !important;
            color: white !important;
            font-weight: 600;
        }

        /* Tombol primary (hijau teal) */
        .btn-primary,
        .btn-success {
            background-color: var(--main-color) !important;
            border-color: var(--main-color-dark) !important;
        }

        .btn-primary:hover,
        .btn-success:hover {
            background-color: var(--main-color-dark) !important;
        }

    </style>

</head>

<body>

<div class="container py-4">

    <!-- HEADER -->
    <div class="page-header">
        🧺 Form Terima Cucian
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            Input Transaksi Baru
        </div>

        <div class="card-body">

            <form action="../../process/new_transaction_process.php" method="POST">

                <!-- Nama Pelanggan -->
                <div class="mb-3">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-control" required>
                </div>

                <!-- Nomor HP -->
                <div class="mb-3">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="no_hp" class="form-control" required>
                </div>

                <!-- Alamat -->
                <div class="mb-3">
                    <label class="form-label">Alamat (Opsional)</label>
                    <textarea name="alamat" class="form-control"></textarea>
                </div>

                <!-- Pilih Paket -->
                <div class="mb-3">
                    <label class="form-label">Paket Laundry</label>
                    <select name="package_id" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Paket --</option>

                        <?php while ($row = mysqli_fetch_assoc($paketQuery)) : ?>
                            <option value="<?= $row['id']; ?>">
                                <?= $row['nama_paket']; ?> — 
                                Rp<?= number_format($row['harga_per_qty']); ?>/<?= $row['satuan']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Berat / Qty -->
                <div class="mb-3">
                    <label class="form-label">Berat / Jumlah</label>
                    <input type="number" name="berat_qty" class="form-control" min="0.1" step="0.1" required>
                </div>

                <!-- Catatan -->
                <div class="mb-3">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control"></textarea>
                </div>

                <!-- Tombol -->
                <div class="d-flex justify-content-between">
                    <a href="transactions_list.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
