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

if (!$configLoaded) die("Config database tidak ditemukan!");
if (!isset($conn)) die("Variabel \$conn tidak tersedia!");

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
            display: inline-flex;
            align-items: center;
            gap: 10px;
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
        📄 Daftar Transaksi Laundry
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Semua Transaksi</div>

        <div class="card-body">

            <table class="table table-bordered table-striped text-center">
                <thead style="background:#d7efe9;">
                    <tr>
                        <th>ID</th>
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
                        <td><?= $row['id']; ?></td>
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
                            <a href="invoice_print.php?id=<?= $row['id']; ?>" class="btn btn-teal btn-sm mb-1">
                                🧾 Struk
                            </a>

                            <?php if ($row['status_laundry'] !== 'Taken'): ?>
                                <a href="take_laundry.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
                                    ✔ Ambil
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>
