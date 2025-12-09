<?php
session_start();

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "Beranda";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Laundry Express</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        /* Header & Navigation */
        header {
            background-color: #008080;
            color: white;
            padding: 0;
            box-shadow: 0 2px 8px rgba(0, 128, 128, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: white;
            transition: opacity 0.3s ease;
        }

        .navbar-brand:hover {
            opacity: 0.9;
        }

        .navbar-brand-icon {
            font-size: 2rem;
        }

        .navbar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .navbar-brand-main {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .navbar-brand-sub {
            font-size: 0.65rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Navigation Menu */
        nav {
            display: flex;
            align-items: center;
            gap: 3rem;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 0;
            position: relative;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: white;
            transition: width 0.3s ease;
        }

        nav a:hover::after {
            width: 100%;
        }

        nav a.active {
            border-bottom: 2px solid white;
        }

        /* Authentication Buttons */
        .auth-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-auth {
            padding: 0.6rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-login {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-login:hover {
            background-color: white;
            color: #008080;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-signup {
            background-color: white;
            color: #008080;
        }

        .btn-signup:hover {
            background-color: #e0f2f1;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #008080 0%, #00a8a8 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .btn-primary {
            background-color: white;
            color: #008080;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary:hover {
            background-color: #e0f2f1;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Features Section */
        .features {
            max-width: 1200px;
            margin: -3rem auto 4rem;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            position: relative;
            z-index: 10;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 128, 128, 0.1);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 128, 128, 0.2);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #008080;
        }

        .feature-card h3 {
            color: #008080;
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: #666;
            font-size: 0.95rem;
        }

        /* Price List Section */
        .price-section {
            background-color: white;
            padding: 4rem 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #008080;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background-color: #008080;
            margin: 1rem auto 0;
            border-radius: 2px;
        }

        .price-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .price-card {
            background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f1 100%);
            border: 2px solid #008080;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .price-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #008080;
        }

        .price-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 128, 128, 0.2);
            border-color: #00a8a8;
        }

        .price-card.featured {
            background: linear-gradient(135deg, #008080 0%, #00a8a8 100%);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 128, 128, 0.3);
        }

        .price-card.featured h3 {
            color: white;
        }

        .price-card.featured .price {
            color: white;
        }

        .price-card.featured .badge {
            background-color: white;
            color: #008080;
        }

        .badge {
            display: inline-block;
            background-color: #008080;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .price-card h3 {
            color: #008080;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .price-card p {
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .price-card.featured p {
            color: rgba(255, 255, 255, 0.95);
        }

        .price {
            font-size: 2.5rem;
            font-weight: bold;
            color: #008080;
            margin-bottom: 1rem;
        }

        .price-card.featured .price {
            color: white;
        }

        .price-info {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1.5rem;
        }

        .price-card.featured .price-info {
            color: rgba(255, 255, 255, 0.9);
        }

        .features-list {
            text-align: left;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: white;
            border-radius: 5px;
        }

        .price-card.featured .features-list {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .features-list li {
            list-style: none;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0f2f1;
            color: #333;
        }

        .price-card.featured .features-list li {
            border-bottom-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .features-list li::before {
            content: '✓ ';
            color: #008080;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        .price-card.featured .features-list li::before {
            color: white;
        }

        .btn-choose {
            background-color: #008080;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-choose:hover {
            background-color: #006666;
            transform: translateY(-2px);
        }

        .price-card.featured .btn-choose {
            background-color: white;
            color: #008080;
        }

        .price-card.featured .btn-choose:hover {
            background-color: #e0f2f1;
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

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #e0f2f1;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1100;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                position: relative;
                padding: 1rem;
            }

            .navbar-brand {
                font-size: 1.5rem;
            }

            .mobile-menu-toggle {
                display: block;
            }

            nav {
                position: absolute;
                top: 65px;
                left: 0;
                right: 0;
                background-color: #006666;
                flex-direction: column;
                gap: 0;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }

            nav.active {
                max-height: 400px;
            }

            nav ul {
                flex-direction: column;
                gap: 0;
                width: 100%;
            }

            nav a {
                padding: 1rem;
                display: block;
                border-radius: 0;
            }

            .auth-buttons {
                position: absolute;
                top: 65px;
                right: 1rem;
                flex-direction: column;
                gap: 0.5rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .price-card.featured {
                transform: scale(1);
            }

            .features {
                margin-top: -1.5rem;
            }
        }

        @media (max-width: 480px) {
            .navbar-brand-text {
                display: none;
            }

            .hero h1 {
                font-size: 1.5rem;
            }

            .btn-primary {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
            }

            .price {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header & Navigation -->
    <header>
        <div class="navbar">
            <a href="index.php" class="navbar-brand">
                <span class="navbar-brand-icon">🧺</span>
                <span class="navbar-brand-text">
                    <span class="navbar-brand-main">Laundry Express</span>
                    <span class="navbar-brand-sub">Professional Laundry Service</span>
                </span>
            </a>

            <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>

            <nav id="navMenu">
                <ul>
                    <li><a href="index.php" class="active">Beranda</a></li>
                    <li><a href="#harga">Daftar Harga</a></li>
                    <li><a href="check_receipt.php">Cek Resi</a></li>
                    <li><a href="#tentang">Tentang Kami</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>
            </nav>

            <div class="auth-buttons" id="authButtons">
                <a href="../auth/login.php" class="btn-auth btn-login">Masuk</a>
                <a href="../auth/register.php" class="btn-auth btn-signup">Daftar</a>
            </div>
        </div>
    </header>
<body>
    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div class="hero-content">
            <h1>Selamat Datang di Laundry Express</h1>
            <p>Layanan laundry profesional dengan harga terjangkau dan kualitas terbaik</p>
            <button class="btn-primary" onclick="document.getElementById('harga').scrollIntoView({behavior: 'smooth'})">
                Lihat Daftar Harga
            </button>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Cepat & Terpercaya</h3>
            <p>Layanan laundry cepat tanpa mengurangi kualitas hasil akhir</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💰</div>
            <h3>Harga Terjangkau</h3>
            <p>Harga kompetitif dengan layanan premium untuk semua kalangan</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🚚</div>
            <h3>Pengiriman Gratis</h3>
            <p>Pengiriman dan pengambilan gratis untuk area tertentu</p>
        </div>
    </section>

    <!-- Price List Section -->
    <section class="price-section" id="harga">
        <h2 class="section-title">Daftar Harga Layanan Kami</h2>
        <div class="price-container">
            <!-- Regular Service -->
            <div class="price-card">
                <span class="badge">STANDAR</span>
                <h3>Cuci Biasa</h3>
                <p>Layanan cuci standar lengkap</p>
                <div class="price">Rp 5.000</div>
                <div class="price-info">per kg</div>
                <ul class="features-list">
                    <li>Cuci & Setrika</li>
                    <li>Pengering</li>
                    <li>Lipatan Rapi</li>
                    <li>Pengiriman Gratis*</li>
                </ul>
                <button class="btn-choose">Pilih Layanan</button>
            </div>

            <!-- Express Service -->
            <div class="price-card featured">
                <span class="badge">POPULER</span>
                <h3>Cuci Express</h3>
                <p>Layanan cuci ekspres - selesai 24 jam</p>
                <div class="price">Rp 7.500</div>
                <div class="price-info">per kg</div>
                <ul class="features-list">
                    <li>Cuci & Setrika</li>
                    <li>Pengering Khusus</li>
                    <li>Kemasan Premium</li>
                    <li>Garansi Cepat*</li>
                </ul>
                <button class="btn-choose">Pilih Layanan</button>
            </div>

            <!-- Special Service -->
            <div class="price-card">
                <span class="badge">SPESIAL</span>
                <h3>Cuci Khusus</h3>
                <p>Cuci untuk pakaian premium & khusus</p>
                <div class="price">Rp 10.000</div>
                <div class="price-info">per kg</div>
                <ul class="features-list">
                    <li>Cuci Lembut</li>
                    <li>Perawatan Khusus</li>
                    <li>Aroma Pilihan</li>
                    <li>Konsultasi Gratis*</li>
                </ul>
                <button class="btn-choose">Pilih Layanan</button>
            </div>

            <!-- Dry Cleaning -->
            <div class="price-card">
                <span class="badge">PREMIUM</span>
                <h3>Dry Cleaning</h3>
                <p>Pengeringan profesional untuk pakaian inti</p>
                <div class="price">Rp 15.000</div>
                <div class="price-info">per piece</div>
                <ul class="features-list">
                    <li>Pengeringan Profesional</li>
                    <li>Perawatan Khusus</li>
                    <li>Pengemasan Lengkap</li>
                    <li>Garansi Kualitas*</li>
                </ul>
                <button class="btn-choose">Pilih Layanan</button>
            </div>

            <!-- Iron Only -->
            <div class="price-card">
                <span class="badge">BASIC</span>
                <h3>Setrika Saja</h3>
                <p>Layanan setrika untuk pakaian siap cuci</p>
                <div class="price">Rp 2.500</div>
                <div class="price-info">per kg</div>
                <ul class="features-list">
                    <li>Setrika Profesional</li>
                    <li>Lipatan Rapi</li>
                    <li>Kemasan Bersih</li>
                    <li>Pengiriman Cepat*</li>
                </ul>
                <button class="btn-choose">Pilih Layanan</button>
            </div>

            <!-- Package -->
            <div class="price-card">
                <span class="badge">PAKET</span>
                <h3>Paket Hemat</h3>
                <p>Paket bulanan dengan diskon spesial</p>
                <div class="price">Rp 140.000</div>
                <div class="price-info">per bulan (20kg)</div>
                <ul class="features-list">
                    <li>Cuci Unlimited</li>
                    <li>Setrika Unlimited</li>
                    <li>Aroma Premium</li>
                    <li>Diskon 20%*</li>
                </ul>
                <button class="btn-choose">Pilih Layanan</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Laundry Express. All rights reserved.</p>
        <p>Layanan laundry terpercaya untuk kebutuhan Anda</p>
        <div class="footer-links">
            <a href="#beranda">Beranda</a>
            <a href="#harga">Daftar Harga</a>
            <a href="#">Kebijakan Privasi</a>
            <a href="#">Syarat & Ketentuan</a>
        </div>
    </footer>

    <script>
        // Add scroll behavior for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Handle service selection buttons
        document.querySelectorAll('.btn-choose').forEach(button => {
            button.addEventListener('click', function() {
                const serviceCard = this.closest('.price-card');
                const serviceName = serviceCard.querySelector('h3').textContent;
                const servicePrice = serviceCard.querySelector('.price').textContent;
                
                // Redirect ke halaman login atau order
                alert('Anda memilih: ' + serviceName + '\n' + servicePrice);
                // window.location.href = 'order.php?service=' + encodeURIComponent(serviceName);
            });
        });
    </script>
</body>
</html>