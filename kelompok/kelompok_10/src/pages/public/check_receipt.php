<?php
session_start();
// Include database connection jika diperlukan
// include '../../config/database.php';

// Dummy data - Ganti dengan query database sebenarnya
$receipts = array(
    'RCP001' => array(
        'id' => 'RCP001',
        'customer_name' => 'Budi Santoso',
        'service' => 'Cuci Biasa',
        'weight' => '5 kg',
        'amount' => 'Rp 25.000',
        'order_date' => '2025-12-05',
        'pickup_date' => '2025-12-06',
        'delivery_date' => '2025-12-08',
        'status' => 'Selesai',
        'status_color' => '#28a745'
    ),
    'RCP002' => array(
        'id' => 'RCP002',
        'customer_name' => 'Siti Nurhaliza',
        'service' => 'Cuci Express',
        'weight' => '3 kg',
        'amount' => 'Rp 22.500',
        'order_date' => '2025-12-08',
        'pickup_date' => '2025-12-08',
        'delivery_date' => '2025-12-09',
        'status' => 'Pengiriman',
        'status_color' => '#ffc107'
    ),
    'RCP003' => array(
        'id' => 'RCP003',
        'customer_name' => 'Ahmad Hidayat',
        'service' => 'Dry Cleaning',
        'weight' => '2 pieces',
        'amount' => 'Rp 30.000',
        'order_date' => '2025-12-07',
        'pickup_date' => '2025-12-07',
        'delivery_date' => '2025-12-10',
        'status' => 'Diproses',
        'status_color' => '#0099ff'
    ),
    'RCP004' => array(
        'id' => 'RCP004',
        'customer_name' => 'Rini Wijaya',
        'service' => 'Setrika Saja',
        'weight' => '4 kg',
        'amount' => 'Rp 10.000',
        'order_date' => '2025-12-09',
        'pickup_date' => '2025-12-09',
        'delivery_date' => 'Menunggu pengambilan',
        'status' => 'Siap Diambil',
        'status_color' => '#17a2b8'
    )
);

$search_receipt = '';
$found_receipt = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_receipt = strtoupper(trim($_POST['receipt_number']));
    
    if (array_key_exists($search_receipt, $receipts)) {
        $found_receipt = $receipts[$search_receipt];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Resi - Laundry Express</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f1 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Header */
        header {
            background-color: #008080;
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 5px rgba(0, 128, 128, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #e0f2f1;
        }

        /* Main Container */
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-title {
            text-align: center;
            color: #008080;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            font-weight: 700;
        }

        /* Search Section */
        .search-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 128, 128, 0.1);
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #008080;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0f2f1;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #008080;
            background-color: #f9fffe;
        }

        .btn-search {
            background-color: #008080;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            height: 44px;
        }

        .btn-search:hover {
            background-color: #006666;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 128, 128, 0.2);
        }

        /* Info Box */
        .info-box {
            background-color: #e0f7fa;
            border-left: 4px solid #008080;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 5px;
            display: flex;
            gap: 1rem;
        }

        .info-box-icon {
            font-size: 1.5rem;
        }

        .info-box p {
            color: #00695c;
            font-size: 0.95rem;
        }

        /* Result Section */
        .result-section {
            margin-top: 2rem;
        }

        .receipt-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 128, 128, 0.15);
            overflow: hidden;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .receipt-header {
            background: linear-gradient(135deg, #008080 0%, #00a8a8 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-number {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .receipt-body {
            padding: 2rem;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-item {
            border-left: 4px solid #008080;
            padding-left: 1rem;
        }

        .info-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        .info-value {
            color: #008080;
            font-size: 1.3rem;
            font-weight: 600;
        }

        /* Timeline */
        .timeline {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e0f2f1;
        }

        .timeline-title {
            color: #008080;
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .timeline-items {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .timeline-item {
            display: flex;
            gap: 1.5rem;
            position: relative;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 19px;
            top: 40px;
            width: 2px;
            height: 40px;
            background-color: #e0f2f1;
        }

        .timeline-dot {
            width: 40px;
            height: 40px;
            background-color: #008080;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            flex-shrink: 0;
        }

        .timeline-content {
            padding-top: 0.5rem;
        }

        .timeline-date {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .timeline-text {
            color: #333;
            font-weight: 500;
        }

        /* Error Message */
        .error-message {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .error-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        /* Success Message */
        .success-message {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .success-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
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
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .search-form {
                flex-direction: column;
            }

            .form-group {
                min-width: 100%;
            }

            .receipt-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .receipt-info {
                grid-template-columns: 1fr;
            }

            .timeline-items {
                gap: 1rem;
            }

            .page-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                margin: 1rem auto;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .search-section {
                padding: 1.5rem;
            }

            .receipt-body {
                padding: 1rem;
            }

            .info-item {
                padding-left: 0.75rem;
            }

            .info-value {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo">🧺 Laundry Express</div>
            <nav class="nav-links">
                <a href="index.php">Beranda</a>
                <a href="index.php#harga">Daftar Harga</a>
                <a href="check_receipt.php">Cek Resi</a>
                <a href="../auth/login.php">Masuk</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title">Cek Status Resi</h1>

        <!-- Info Box -->
        <div class="info-box">
            <div class="info-box-icon">ℹ️</div>
            <div>
                <p><strong>Masukkan nomor resi Anda</strong> untuk melihat status pesanan laundry Anda secara real-time.</p>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="POST" class="search-form">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="receipt_number">Nomor Resi</label>
                    <input 
                        type="text" 
                        id="receipt_number" 
                        name="receipt_number" 
                        placeholder="Contoh: RCP001" 
                        value="<?php echo htmlspecialchars($search_receipt); ?>"
                        required
                    >
                </div>
                <button type="submit" name="search" class="btn-search">Cari</button>
            </form>
        </div>

        <!-- Result Section -->
        <div class="result-section">
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <?php if ($found_receipt): ?>
                    <div class="success-message">
                        <div class="success-icon">✓</div>
                        <div>Resi ditemukan! Berikut adalah detail pesanan Anda.</div>
                    </div>

                    <div class="receipt-card">
                        <!-- Receipt Header -->
                        <div class="receipt-header">
                            <div>
                                <div class="receipt-number"><?php echo $found_receipt['id']; ?></div>
                                <div style="font-size: 0.95rem; opacity: 0.9;">Nomor Resi Pesanan</div>
                            </div>
                            <div class="status-badge" style="background-color: <?php echo $found_receipt['status_color']; ?>80; color: white;">
                                <?php echo $found_receipt['status']; ?>
                            </div>
                        </div>

                        <!-- Receipt Body -->
                        <div class="receipt-body">
                            <!-- Customer & Service Info -->
                            <div class="receipt-info">
                                <div class="info-item">
                                    <div class="info-label">Nama Pelanggan</div>
                                    <div class="info-value"><?php echo $found_receipt['customer_name']; ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Jenis Layanan</div>
                                    <div class="info-value"><?php echo $found_receipt['service']; ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Berat / Jumlah</div>
                                    <div class="info-value"><?php echo $found_receipt['weight']; ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Total Biaya</div>
                                    <div class="info-value" style="color: #ff6b00;"><?php echo $found_receipt['amount']; ?></div>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="timeline">
                                <div class="timeline-title">Riwayat Pesanan</div>
                                <div class="timeline-items">
                                    <div class="timeline-item">
                                        <div class="timeline-dot">1</div>
                                        <div class="timeline-content">
                                            <div class="timeline-date"><?php echo $found_receipt['order_date']; ?></div>
                                            <div class="timeline-text">Pesanan Diterima</div>
                                        </div>
                                    </div>

                                    <div class="timeline-item">
                                        <div class="timeline-dot">2</div>
                                        <div class="timeline-content">
                                            <div class="timeline-date"><?php echo $found_receipt['pickup_date']; ?></div>
                                            <div class="timeline-text">Pakaian Diambil</div>
                                        </div>
                                    </div>

                                    <div class="timeline-item">
                                        <div class="timeline-dot">3</div>
                                        <div class="timeline-content">
                                            <div class="timeline-date">Sedang Diproses</div>
                                            <div class="timeline-text">Pakaian dalam proses pencucian</div>
                                        </div>
                                    </div>

                                    <div class="timeline-item">
                                        <div class="timeline-dot">4</div>
                                        <div class="timeline-content">
                                            <div class="timeline-date"><?php echo $found_receipt['delivery_date']; ?></div>
                                            <div class="timeline-text">Pengiriman / Siap Diambil</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="error-message">
                        <div class="error-icon">✕</div>
                        <div>
                            <strong>Resi Tidak Ditemukan</strong><br>
                            Nomor resi "<strong><?php echo htmlspecialchars($search_receipt); ?></strong>" tidak ditemukan dalam sistem kami. 
                            Mohon periksa kembali nomor resi Anda atau hubungi layanan pelanggan kami.
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Sample Receipts Info -->
        <div style="background: white; padding: 2rem; border-radius: 10px; margin-top: 2rem; box-shadow: 0 5px 15px rgba(0, 128, 128, 0.1);">
            <h3 style="color: #008080; margin-bottom: 1rem;">📝 Nomor Resi</h3>
            <p style="color: #666; margin-bottom: 1rem;">Gunakan salah satu nomor resi berikut untuk melihat status pesanan :</p>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0f2f1;">
                    <strong style="color: #008080;">RCP001</strong> - Status: Selesai
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0f2f1;">
                    <strong style="color: #008080;">RCP002</strong> - Status: Pengiriman
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0f2f1;">
                    <strong style="color: #008080;">RCP003</strong> - Status: Diproses
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong style="color: #008080;">RCP004</strong> - Status: Siap Diambil
                </li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Laundry Express. All rights reserved.</p>
        <p>Layanan Pelanggan: 0812-3456-7890 | Email: support@laundryexpress.com</p>
    </footer>

    <script>
        // Auto-uppercase input
        document.getElementById('receipt_number').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Focus on input when page loads
        window.addEventListener('load', function() {
            document.getElementById('receipt_number').focus();
        });
    </script>
</body>
</html>
