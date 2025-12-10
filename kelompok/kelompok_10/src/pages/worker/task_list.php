<?php

$active_page = 'task_list';

$worker_id = 3; 
$worker_name = "Syandra"; 
$worker_role = "Petugas"; 


require_once __DIR__ . '/../../config/database.php';

$query = "
    SELECT 
        t.id, t.nama_pelanggan, p.nama_paket, t.berat_qty, t.status_laundry, t.tgl_masuk
    FROM 
        transactions t
    JOIN 
        packages p ON t.package_id = p.id
    WHERE 
        t.status_laundry IN ('Pending', 'Washing', 'Ironing') 
    ORDER BY 
        tgl_masuk ASC
";

$tasks = fetchData($conn, $query);

if ($tasks === false) {
    $tasks = [];
    $error_message = "Gagal mengambil data dari database.";
} else {
    $error_message = null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tugas Aktif - E-LAUNDRY</title>
    <link rel="stylesheet" href="../../assets/css/worker.css?v=<?php echo time(); ?>"> 
</head>
<body>
    
    <?php require_once __DIR__ . '/../../includes/sidebar_worker.php'; ?>

    <div class="main-content">
        <header class="page-header">
            <h2>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="header-icon-title">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg> 
                Daftar Tugas Aktif
            </h2>
            
            <div class="profile-info">
                <div class="text">
                    <span class="username"><?php echo htmlspecialchars($worker_name); ?></span>
                    <span class="role"><?php echo htmlspecialchars($worker_role); ?></span>
                </div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="profile-icon">
                    <path d="M5.52 19c.4-.82.8-1.64 1.2-2.46l.48-.96c.07-.13.15-.24.23-.35.48-.6.94-1.2 1.4-1.8H14.8c.46.6  .92 1.2 1.4 1.8.08.11.16.22.23.35l.48.96c.4.82.8 1.64 1.2 2.46"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            </header>

        <div class="content-panel">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Kode Resi</th>
                            <th>Pelanggan</th>
                            <th>Paket</th>
                            <th>Berat (Kg)</th>
                            <th>Tanggal Masuk</th>
                            <th>Status Pengerjaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($tasks) > 0): ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['id']); ?></td>
                                    <td><?php echo htmlspecialchars($task['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($task['nama_paket']); ?></td>
                                    <td><?php echo htmlspecialchars($task['berat_qty']); ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($task['tgl_masuk'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($task['status_laundry']); ?>">
                                            <?php echo htmlspecialchars($task['status_laundry']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="../../process/worker_handler.php" style="display:inline;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="transaction_id" value="<?php echo $task['id']; ?>">
                                            <input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>"> 
                                            <input type="hidden" name="status_before" value="<?php echo $task['status_laundry']; ?>">
                                            
                                            <?php 
                                            $current_status = $task['status_laundry'];
                                            $next_status_value = '';
                                            $next_status_label = '';
                                            $button_class = 'btn-primary';

                                            if ($current_status == 'Pending') {
                                                $next_status_label = 'Mulai Cuci (Washing)';
                                                $next_status_value = 'Washing';
                                                $button_class = 'btn-success';
                                            } elseif ($current_status == 'Washing') {
                                                $next_status_label = 'Selesai Cuci (Ironing)';
                                                $next_status_value = 'Ironing';
                                                $button_class = 'btn-warning';
                                            } elseif ($current_status == 'Ironing') {
                                                $next_status_label = 'Selesai Penuh (Done)';
                                                $next_status_value = 'Done';
                                                $button_class = 'btn-danger';
                                            }
                                            ?>
                                            
                                            <?php if ($next_status_value): ?>
                                                <input type="hidden" name="next_status" value="<?php echo $next_status_value; ?>">
                                                <button type="submit" class="btn <?php echo $button_class; ?> btn-sm">
                                                    <?php echo $next_status_label; ?>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-secondary">Sudah Tahap Akhir</span>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 20px;">
                                    <p> Tidak ada tugas cucian aktif saat ini. Anda bisa istirahat sebentar!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>