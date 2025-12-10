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
            
            // Query untuk cari transaction berdasarkan ID (nomor resi)
            $query = "SELECT id, nama_pelanggan, no_hp, package_id, berat_qty, total_harga, status_laundry, status_bayar, tgl_masuk, tgl_estimasi_selesai, tgl_selesai FROM transactions WHERE id = ?";
            $stmt = $conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Database error: " . $conn->error);
            }
            
            $stmt->bind_param("s", $receipt_number);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $search_result = $result->fetch_assoc();
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

        /* Header */
        header {
            background-color: #008080;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .logo-icon {
            font-size: 2rem;
        }

        .logo-text h1 {
            font-size: 1.2rem;
            margin: 0;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .logo-text p {
            font-size: 0.65rem;
            margin: 0.1rem 0 0 0;
            opacity: 0.9;
            letter-spacing: 0.8px;
        }

        .header-nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        /* Styling button Kembali Beranda di header - VERSI PUTIH */
    .header-nav a {
        color: #008080; /* Warna teks hijau tua */
        text-decoration: none;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 10px 20px;
        border-radius: 25px;
        background-color: white; /* Background putih */
        border: 2px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: inline-block;
    }

    .header-nav a:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        background-color: #f8f9fa; /* Putih sedikit gelap saat hover */
        border-color: #f8f9fa;
        color: #008080; /* Tetap hijau tua */
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
    <header>
        <div class="logo-section">
            <div class="logo-text">
                <h1>E-LAUNDRY</h1>
                <p>PROFESSIONAL LAUNDRY SERVICE</p>
            </div>
        </div>
        <nav class="header-nav">
            <a href="index.php">Kembali ke Beranda</a>
        </nav>
    </header>

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
                    <span class="detail-value"><?php echo htmlspecialchars($search_result['package_id']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Berat (Qty):</span>
                    <span class="detail-value"><?php echo htmlspecialchars($search_result['berat_qty']); ?> kg</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Harga:</span>
                    <span class="detail-value"><strong>Rp <?php echo number_format($search_result['total_harga'], 0, ',', '.'); ?></strong></span>
                </div>
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
                <div class="detail-row">
                    <span class="detail-label">Status Pembayaran:</span>
                    <span class="detail-value">
                        <span class="status-badge <?php echo ($search_result['status_bayar'] == 'Success') ? 'payment-success' : 'payment-pending'; ?>">
                            <?php echo htmlspecialchars($search_result['status_bayar']); ?>
                        </span>
                    </span>
                </div>
            </div>

            <!-- Timeline -->
            <div class="detail-group">
                <h4>Jadwal</h4>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Masuk:</span>
                    <span class="detail-value"><?php echo date('d-m-Y H:i', strtotime($search_result['tgl_masuk'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estimasi Selesai:</span>
                    <span class="detail-value"><?php echo date('d-m-Y H:i', strtotime($search_result['tgl_estimasi_selesai'])); ?></span>
                </div>
                <?php if ($search_result['tgl_selesai']): ?>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Selesai:</span>
                    <span class="detail-value"><?php echo date('d-m-Y H:i', strtotime($search_result['tgl_selesai'])); ?></span>
                </div>
                <?php endif; ?>
            </div>

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
