<?php

$active_page = 'task_history';

$worker_name = "Syandra"; 
$worker_role = "Petugas"; 


require_once __DIR__ . '/../../config/database.php';

$query = "
    SELECT 
        t.id, t.nama_pelanggan, p.nama_paket, t.berat_qty, t.status_laundry, 
        t.status_bayar, t.tgl_masuk, t.tgl_selesai, t.tgl_diambil
    FROM 
        transactions t
    JOIN 
        packages p ON t.package_id = p.id
    WHERE 
        t.status_laundry IN ('Done', 'Taken') 
    ORDER BY 
        t.tgl_selesai DESC
";

$history_tasks = fetchData($conn, $query);

if ($history_tasks === false) {
    $history_tasks = [];
    $error_message = "Gagal mengambil data riwayat dari database.";
} else {
    $error_message = null;
}


function get_payment_badge_class($status) {
    if ($status === 'Paid') {
        return 'status-done'; 
    }
    return 'status-danger'; 
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Tugas - E-LAUNDRY</title>
    <link rel="stylesheet" href="../../assets/css/worker.css?v=<?php echo time(); ?>"> 
</head>
<body>
    
    <?php require_once __DIR__ . '/../../includes/sidebar_worker.php'; ?>

    <div class="main-content">
        <header class="page-header">
            <h2>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="header-icon-title">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                </svg> 
                Riwayat Tugas
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
                            <th>Qty</th>
                            <th>Tgl Masuk</th>
                            <th>Tgl Selesai</th>
                            <th>Status Laundry</th>
                            <th>Status Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($history_tasks) > 0): ?>
                            <?php foreach ($history_tasks as $task): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['id']); ?></td>
                                    <td><?php echo htmlspecialchars($task['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($task['nama_paket']); ?></td>
                                    <td><?php echo htmlspecialchars($task['berat_qty']); ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($task['tgl_masuk'])); ?></td>
                                    <td>
                                        <?php 
                                            // Tgl Selesai adalah Tgl Done/Tgl Diambil
                                            echo date('d M Y H:i', strtotime($task['tgl_selesai'] ?? $task['tgl_diambil'])); 
                                        ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($task['status_laundry']); ?>">
                                            <?php echo htmlspecialchars($task['status_laundry']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo get_payment_badge_class($task['status_bayar']); ?>">
                                            <?php echo htmlspecialchars($task['status_bayar']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 20px;">
                                    <p>Belum ada riwayat tugas yang selesai.</p>
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