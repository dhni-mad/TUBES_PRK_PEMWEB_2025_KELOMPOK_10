<?php
include('../../config/database.php');

$page_title = "Cek Pesanan";
$search_result = null;
$search_error = '';
$receipt_number = '';

// Proses pencarian resi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receipt_number = trim($_POST['receipt_number'] ?? '');
    
    if (empty($receipt_number)) {
        $search_error = 'Silakan masukkan nomor resi';
    } else {
        try {
            $conn = getConnection();
            
            // Query untuk cari transaction dengan detail lengkap
            $query = "SELECT 
                        t.id,
                        t.nama_pelanggan,
                        t.no_hp,
                        t.berat_qty,
                        t.total_harga,
                        t.status_laundry,
                        t.status_bayar,
                        t.tgl_masuk,
                        t.tgl_estimasi_selesai,
                        t.tgl_selesai,
                        t.catatan,
                        p.nama_paket,
                        p.harga_per_qty,
                        p.satuan,
                        p.estimasi_hari,
                        u_kasir.full_name as kasir_nama
                      FROM transactions t
                      JOIN packages p ON t.package_id = p.id
                      LEFT JOIN users u_kasir ON t.kasir_input_id = u_kasir.id
                      WHERE t.id = ?
                      LIMIT 1";
            $stmt = $conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Database error: " . $conn->error);
            }
            
            $stmt->bind_param("s", $receipt_number);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $search_result = $result->fetch_assoc();
                
                // Ambil history status dari status_logs
                $status_query = "SELECT 
                                    sl.status_after as status,
                                    sl.created_at as changed_at,
                                    u.full_name as changed_by
                                 FROM status_logs sl
                                 LEFT JOIN users u ON sl.changed_by = u.id
                                 WHERE sl.transaction_id = ?
                                 ORDER BY sl.created_at ASC";
                $status_stmt = $conn->prepare($status_query);
                $status_stmt->bind_param("s", $receipt_number);
                $status_stmt->execute();
                $status_history = $status_stmt->get_result();
                $search_result['status_history'] = $status_history->fetch_all(MYSQLI_ASSOC);
                $status_stmt->close();
            } else {
                $search_error = 'Nomor resi tidak ditemukan. Silakan periksa kembali nomor Anda.';
            }
            
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $search_error = 'Terjadi kesalahan sistem. Silakan coba lagi nanti.';
            error_log("Search Error: " . $e->getMessage());
        }
    }
}

// Fungsi untuk menerjemahkan status
function getStatusLabel($status) {
    $status_labels = [
        'Pending' => 'Menunggu Diproses',
        'Washing' => 'Sedang Dicuci',
        'Ironing' => 'Sedang Disetrika',
        'Done' => 'Selesai',
        'Taken' => 'Sudah Diambil'
    ];
    return $status_labels[$status] ?? $status;
}

function getStatusColor($status) {
    $colors = [
        'Pending' => '#ffc107',
        'Washing' => '#17a2b8',
        'Ironing' => '#fd7e14',
        'Done' => '#28a745',
        'Taken' => '#6c757d'
    ];
    return $colors[$status] ?? '#6c757d';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - E-LAUNDRY</title>
    <link rel="stylesheet" href="../../assets/css/public.css?v=<?php echo time(); ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #008080 0%, #00a8a8 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Container */
        .container {
            max-width: 700px;
            margin: 3rem auto;
            padding: 0 1rem;
        }

        /* Search Card */
        .search-card {
            background: white;
            border-radius: 10px;
            padding: 3rem 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        .search-card h2 {
            color: #008080;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e0f2f1;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #008080;
            background-color: #f0fffe;
        }

        .search-hint {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .btn-search {
            width: 100%;
            padding: 1rem;
            background-color: #008080;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            background-color: #006666;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 128, 128, 0.3);
        }

        .btn-search:active {
            transform: translateY(0);
        }

        /* Error Message */
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        /* Result Card */
        .result-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .result-card h3 {
            color: #008080;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0f2f1;
            font-size: 1.5rem;
        }

        /* Detail Section */
        .detail-group {
            margin-bottom: 2rem;
        }

        .detail-group h4 {
            color: #008080;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e0f2f1;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
        }

        .detail-value {
            color: #666;
            text-align: right;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge.payment-success {
            background-color: #28a745;
        }

        .status-badge.payment-pending {
            background-color: #ffc107;
        }

        /* Progress Steps */
        .progress-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e0f2f1;
        }

        .progress-section h4 {
            color: #008080;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e0f2f1;
            z-index: 0;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            background-color: #e0f2f1;
            border: 3px solid #e0f2f1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.8rem;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background-color: #008080;
            border-color: #008080;
            color: white;
            box-shadow: 0 0 0 4px rgba(0, 128, 128, 0.2);
        }

        .step.completed .step-circle {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }

        .step-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #008080;
            font-weight: 600;
        }

        /* Back Button */
        .btn-back {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.8rem 1.5rem;
            background-color: #008080;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #006666;
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background-color: #008080;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }

        footer p {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1rem;
            }

            .search-card {
                padding: 2rem 1.5rem;
            }

            .search-card h2 {
                font-size: 1.5rem;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-value {
                text-align: left;
                margin-top: 0.3rem;
            }

            .progress-steps {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .progress-steps::before {
                display: none;
            }

            .step {
                flex: 0 0 calc(50% - 0.5rem);
            }
        }

        @media (max-width: 480px) {
            .container {
                margin: 1rem auto;
            }

            .search-card {
                padding: 1.5rem 1rem;
            }

            .search-card h2 {
                font-size: 1.3rem;
            }

            .result-card h3 {
                font-size: 1.2rem;
            }

            .step {
                flex: 0 0 calc(50% - 0.5rem);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include '../../includes/header_public.php'; ?>

    <!-- Main Container -->
    <div class="container">
        <!-- Search Card -->
        <div class="search-card">
            <h2>Cek Status Pesanan Anda</h2>
            
            <?php if ($search_error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($search_error); ?></div>
            <?php endif; ?>

            <form method="POST" class="search-form">
                <div class="form-group">
                    <label for="receipt_number">Nomor Resi</label>
                    <input 
                        type="text" 
                        id="receipt_number" 
                        name="receipt_number" 
                        placeholder="Masukkan nomor resi Anda (contoh: TRX001)" 
                        value="<?php echo htmlspecialchars($receipt_number); ?>"
                        required
                    >
                    <p class="search-hint">Nomor resi diberikan saat Anda melakukan pemesanan laundry</p>
                </div>
                <button type="submit" class="btn-search">Cari Pesanan</button>
            </form>
        </div>

        <!-- Result Section -->
        <?php if ($search_result): ?>
        <div class="result-card">
            <h3>✓ Hasil Pencarian Pesanan</h3>

            <!-- Customer Information -->
            <div class="detail-group">
                <h4>Informasi Pelanggan</h4>
                <div class="detail-row">
                    <span class="detail-label">Nomor Resi:</span>
                    <span class="detail-value"><strong><?php echo htmlspecialchars($search_result['id']); ?></strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nama Pelanggan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($search_result['nama_pelanggan']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nomor HP:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($search_result['no_hp']); ?></span>
                </div>
            </div>

            <!-- Service Details -->
            <div class="detail-group">
                <h4>Detail Layanan</h4>
                <div class="detail-row">
                    <span class="detail-label">Paket Layanan:</span>
                    <span class="detail-value"><strong><?php echo htmlspecialchars($search_result['nama_paket']); ?></strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Harga per <?php echo htmlspecialchars($search_result['satuan']); ?>:</span>
                    <span class="detail-value">Rp <?php echo number_format($search_result['harga_per_qty'], 0, ',', '.'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jumlah:</span>
                    <span class="detail-value"><?php echo number_format($search_result['berat_qty'], 1, ',', '.'); ?> <?php echo htmlspecialchars($search_result['satuan']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Harga:</span>
                    <span class="detail-value"><strong style="color: #008080; font-size: 1.1em;">Rp <?php echo number_format($search_result['total_harga'], 0, ',', '.'); ?></strong></span>
                </div>
                <?php if (!empty($search_result['catatan'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Catatan:</span>
                    <span class="detail-value"><?php echo nl2br(htmlspecialchars($search_result['catatan'])); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($search_result['kasir_nama'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Diinput oleh:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($search_result['kasir_nama']); ?> (Kasir)</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Status Information -->
            <div class="detail-group">
                <h4>Status Pesanan</h4>
                <div class="detail-row">
                    <span class="detail-label">Status Laundry:</span>
                    <span class="detail-value">
                        <span class="status-badge" style="background-color: <?php echo getStatusColor($search_result['status_laundry']); ?>;">
                            <?php echo getStatusLabel($search_result['status_laundry']); ?>
                        </span>
                    </span>
                </div>
                <?php if (!empty($search_result['worker_nama'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Dikerjakan oleh:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($search_result['worker_nama']); ?> (Petugas)</span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="detail-label">Status Pembayaran:</span>
                    <span class="detail-value">
                        <span class="status-badge <?php echo ($search_result['status_bayar'] == 'Paid') ? 'payment-success' : 'payment-pending'; ?>">
                            <?php echo ($search_result['status_bayar'] == 'Paid') ? 'Lunas' : 'Belum Lunas'; ?>
                        </span>
                    </span>
                </div>
            </div>

            <!-- Timeline -->
            <div class="detail-group">
                <h4>Jadwal</h4>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Masuk:</span>
                    <span class="detail-value"><?php echo date('d F Y, H:i', strtotime($search_result['tgl_masuk'])); ?> WIB</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estimasi Selesai:</span>
                    <span class="detail-value"><strong><?php echo date('d F Y, H:i', strtotime($search_result['tgl_estimasi_selesai'])); ?> WIB</strong> (<?php echo htmlspecialchars($search_result['estimasi_hari']); ?> hari)</span>
                </div>
                <?php if (!empty($search_result['tgl_selesai'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Selesai:</span>
                    <span class="detail-value" style="color: #28a745; font-weight: 600;"><?php echo date('d F Y, H:i', strtotime($search_result['tgl_selesai'])); ?> WIB</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Status History Timeline -->
            <?php if (!empty($search_result['status_history'])): ?>
            <div class="detail-group">
                <h4>Riwayat Status</h4>
                <?php foreach ($search_result['status_history'] as $history): ?>
                <div class="detail-row">
                    <span class="detail-label">
                        <span class="status-badge" style="background-color: <?php echo getStatusColor($history['status']); ?>; font-size: 0.8em;">
                            <?php echo getStatusLabel($history['status']); ?>
                        </span>
                    </span>
                    <span class="detail-value" style="font-size: 0.9em;">
                        <?php echo date('d/m/Y H:i', strtotime($history['changed_at'])); ?>
                        <?php if (!empty($history['changed_by'])): ?>
                            <br><small>oleh <?php echo htmlspecialchars($history['changed_by']); ?></small>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Progress Steps -->
            <div class="progress-section">
                <h4>Tahapan Proses</h4>
                <div class="progress-steps">
                    <?php
                    $status_steps = [
                        'Pending' => 'Pending',
                        'Washing' => 'Dicuci',
                        'Ironing' => 'Disetrika',
                        'Done' => 'Selesai'
                    ];
                    
                    $current_status = $search_result['status_laundry'];
                    $status_order = ['Pending', 'Washing', 'Ironing', 'Done'];
                    $current_index = array_search($current_status, $status_order);
                    
                    foreach ($status_order as $index => $status):
                        $is_active = ($index == $current_index);
                        $is_completed = ($index < $current_index);
                        $step_class = '';
                        
                        if ($is_completed) {
                            $step_class = 'completed';
                        } elseif ($is_active) {
                            $step_class = 'active';
                        }
                    ?>
                    <div class="step <?php echo $step_class; ?>">
                        <div class="step-circle">
                            <?php echo $index + 1; ?>
                        </div>
                        <div class="step-label"><?php echo getStatusLabel($status); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="check_receipt.php" class="btn-back">Cari Pesanan Lain</a>
        </div>
        <?php endif; ?>
    </div>

    
</body>
</html>
