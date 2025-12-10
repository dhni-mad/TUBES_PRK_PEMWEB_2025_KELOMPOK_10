<?php
$page_title = "Beranda";

// Include packages handler untuk mengambil data paket dari database
require_once __DIR__ . '/../../../src/process/packages_handler.php';

// Ambil semua paket aktif dari database
$packages = getActivePackages();
$total_packages = count($packages);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <?php
    $page_title = "Beranda";
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
                background-color: #f5f5f5;
                color: #333;
                line-height: 1.6;
            }

            /* Header */
            header {
                background-color: #008080;
                color: white;
                padding: 1rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 8px rgba(0, 128, 128, 0.15);
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
/* Feature Section Styling */
.feature-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 128, 128, 0.1);
    transition: all 0.3s ease;
    border: 1px solid #e0f2f1;
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0, 128, 128, 0.15);
    border-color: #008080;
}

.feature-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 50%;
    padding: 15px;
    transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
    background: #e0f2f1;
    transform: scale(1.05);
}

.feature-icon svg {
    width: 100%;
    height: 100%;
}

.feature-card h3 {
    color: #008080;
    margin-bottom: 10px;
    font-size: 1.3rem;
    font-weight: 600;
}

.feature-card p {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
}
            .header-right {
                /* Kosong sesuai permintaan */
            }

            /* Hero Section */
            .hero {
                background: linear-gradient(135deg, rgba(0, 128, 128, 0.85) 0%, rgba(0, 168, 168, 0.85) 100%);
                color: white;
                padding: 6rem 2rem;
                text-align: center;
                min-height: 600px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }

            .hero-background {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 0;
                pointer-events: none;
            }

            .laundry-item {
                position: absolute;
                opacity: 0.45;
                animation: float 6.5s ease-in-out infinite;
                filter: drop-shadow(0 6px 10px rgba(0,0,0,0.18));
                transform-origin: center;
            }

            @keyframes float {
                0% { transform: translateY(0px) rotate(0deg) scale(1); }
                50% { transform: translateY(-18px) rotate(3deg) scale(1.02); }
                100% { transform: translateY(0px) rotate(0deg) scale(1); }
            }

            /* Positions for each decorative SVG */
            .laundry-item:nth-child(1) { top: 8%; left: 6%; animation-delay: 0s; animation-duration: 6s; }
            .laundry-item:nth-child(2) { top: 62%; left: 8%; animation-delay: 1s; animation-duration: 7.2s; }
            .laundry-item:nth-child(3) { top: 18%; left: 22%; animation-delay: 2s; animation-duration: 8s; }
            .laundry-item:nth-child(4) { top: 48%; left: 26%; animation-delay: 0.5s; animation-duration: 6.5s; }
            .laundry-item:nth-child(5) { top: 28%; right: 12%; animation-delay: 1.5s; animation-duration: 7.5s; }
            .laundry-item:nth-child(6) { top: 72%; right: 14%; animation-delay: 0.8s; animation-duration: 6.8s; }
            .laundry-item:nth-child(7) { top: 14%; right: 22%; animation-delay: 2.2s; animation-duration: 8.2s; }
            .laundry-item:nth-child(8) { top: 56%; right: 6%; animation-delay: 1.2s; animation-duration: 7.2s; }

            /* Make background icons subtler on small screens to avoid clutter */
            @media (max-width: 768px) {
                .laundry-item { opacity: 0.28; filter: none; }
                .laundry-item:nth-child(2), .laundry-item:nth-child(6), .laundry-item:nth-child(8) { display: none; }
            }

            .hero-content {
                max-width: 700px;
                margin: 0 auto;
                position: relative;
                z-index: 1;
            }

            .hero h1 {
                font-size: 3.5rem;
                margin-bottom: 1.5rem;
                font-weight: 700;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            }

            .hero p {
                font-size: 1.15rem;
                margin-bottom: 3rem;
                opacity: 0.95;
                line-height: 1.8;
            }

            .hero-buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn {
                padding: 0.9rem 2rem;
                border: none;
                border-radius: 25px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
            }

            .btn-primary {
                background-color: #ff6b35;
                color: white;
            }

            .btn-primary:hover {
                background-color: #ff5520;
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
            }

            .btn-secondary {
                background-color: transparent;
                color: white;
                border: 2px solid white;
            }

            .btn-secondary:hover {
                background-color: white;
                color: #008080;
                transform: translateY(-3px);
            }

            /* Features Section */
            .features {
                max-width: 1200px;
                margin: -4rem auto 4rem;
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
                padding: 3rem 2rem;
                margin-top: 4rem;
            }

            footer p {
                margin-bottom: 0.5rem;
                text-align: center;
            }

            .footer-container {
                max-width: 1200px;
                margin: 0 auto;
            }

            .footer-intro {
                text-align: center;
                margin-bottom: 2rem;
                font-size: 1rem;
            }

            .footer-contact {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 2rem;
                margin-bottom: 2rem;
            }

            .contact-item {
                display: flex;
                align-items: flex-start;
                gap: 1rem;
            }

            .contact-icon {
                width: 40px;
                height: 40px;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(255, 255, 255, 0.2);
                border-radius: 8px;
            }

            .contact-icon svg {
                width: 24px;
                height: 24px;
            }

            .contact-info h4 {
                margin: 0 0 0.5rem 0;
                font-size: 1rem;
                font-weight: 600;
            }

            .contact-info p {
                margin: 0;
                font-size: 0.95rem;
                line-height: 1.5;
                text-align: left;
            }

            .footer-bottom {
                text-align: center;
                padding-top: 2rem;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
                font-size: 0.95rem;
            }

            /* Responsive */
            @media (max-width: 768px) {
                header {
                    flex-direction: column;
                    gap: 1rem;
                }

                .hero h1 {
                    font-size: 2.2rem;
                }

                .hero p {
                    font-size: 1rem;
                }

                .hero-buttons {
                    flex-direction: column;
                }

                .btn {
                    width: 100%;
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
                .hero h1 {
                    font-size: 1.8rem;
                }

                .hero p {
                    font-size: 0.95rem;
                }

                .price {
                    font-size: 2rem;
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
            <div class="header-right">
                <!-- Kosong sesuai permintaan -->
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-background">
                <!-- Detailed Laundry SVG Items: 2x Washing Machine, Detergent, T-Shirt, Shirt, Pants, Hanger -->

                <!-- Washing Machine A (front drum, buttons, legs) -->
                <svg class="laundry-item" width="84" height="84" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="6" y="6" width="52" height="52" rx="6" stroke="white" stroke-width="2" fill="none"/>
                    <circle cx="32" cy="32" r="16" stroke="white" stroke-width="2" fill="rgba(255,255,255,0.03)"/>
                    <circle cx="32" cy="32" r="10" stroke="white" stroke-width="1.2" fill="none"/>
                    <rect x="12" y="12" width="8" height="4" rx="1" fill="white" opacity="0.12"/>
                    <rect x="44" y="12" width="6" height="4" rx="1" fill="white" opacity="0.12"/>
                    <rect x="14" y="50" width="8" height="4" rx="1" fill="white" opacity="0.08"/>
                    <rect x="42" y="50" width="8" height="4" rx="1" fill="white" opacity="0.08"/>
                </svg>

                <!-- Washing Machine B (with panel, knob, small feet) -->
                <svg class="laundry-item" width="72" height="72" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="8" y="8" width="48" height="48" rx="5" stroke="white" stroke-width="1.6" fill="none"/>
                    <rect x="10" y="10" width="14" height="10" rx="2" stroke="white" stroke-width="1.2" fill="rgba(255,255,255,0.02)"/>
                    <circle cx="36" cy="34" r="14" stroke="white" stroke-width="1.6" fill="rgba(255,255,255,0.03)"/>
                    <circle cx="36" cy="34" r="8" stroke="white" stroke-width="1.2"/>
                    <circle cx="36" cy="34" r="3" fill="white" opacity="0.12"/>
                    <rect x="22" y="46" width="20" height="2" rx="1" fill="white" opacity="0.08"/>
                </svg>

                <!-- Detergent Bottle -->
                <svg class="laundry-item" width="56" height="80" viewBox="0 0 64 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 12h24c4 0 8 3 8 7v58c0 4-4 7-8 7H16c-4 0-8-3-8-7V19c0-4 4-7 8-7z" stroke="white" stroke-width="1.6" fill="rgba(255,255,255,0.02)"/>
                    <rect x="24" y="8" width="8" height="6" rx="1" fill="white" opacity="0.12"/>
                    <rect x="20" y="38" width="24" height="20" rx="3" stroke="white" stroke-width="1.2" fill="rgba(255,255,255,0.03)"/>
                    <text x="32" y="52" text-anchor="middle" font-size="6" fill="white" opacity="0.9">DETER</text>
                </svg>

                <!-- T-Shirt / Kaos (collar + seam lines) -->
                <svg class="laundry-item" width="70" height="56" viewBox="0 0 72 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 10 L20 6 H52 L60 10 L52 28 V48 H20 V28 L12 10 Z" stroke="white" stroke-width="1.6" fill="rgba(255,255,255,0.02)"/>
                    <path d="M36 10 L36 18" stroke="white" stroke-width="1" stroke-linecap="round"/>
                    <path d="M24 18 L48 18" stroke="white" stroke-width="0.9" stroke-linecap="round" opacity="0.9"/>
                </svg>

                <!-- Kemeja / Shirt (buttons + collar pattern) -->
                <svg class="laundry-item" width="64" height="72" viewBox="0 0 64 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8 H52 L60 24 V64 H4 V24 L12 8 Z" stroke="white" stroke-width="1.6" fill="rgba(255,255,255,0.02)"/>
                    <path d="M24 18 L32 28 L40 18" stroke="white" stroke-width="1" stroke-linecap="round"/>
                    <line x1="32" y1="30" x2="32" y2="56" stroke="white" stroke-width="1"/>
                    <circle cx="32" cy="38" r="1.6" fill="white"/>
                    <circle cx="32" cy="46" r="1.6" fill="white"/>
                </svg>

                <!-- Pants / Celana (two legs) -->
                <svg class="laundry-item" width="56" height="76" viewBox="0 0 64 88" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8 H28 L32 40 L36 8 H52 V80 H44 V48 L36 80 H28 L20 48 V80 H12 V8 Z" stroke="white" stroke-width="1.4" fill="rgba(255,255,255,0.02)"/>
                    <line x1="20" y1="44" x2="44" y2="44" stroke="white" stroke-width="0.9" opacity="0.9"/>
                </svg>

                <!-- Hanger (hook + curved top) -->
                <svg class="laundry-item" width="64" height="56" viewBox="0 0 64 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 8 C34 4 44 4 46 8" stroke="white" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <path d="M8 40 H56 L40 24 H24 L8 40 Z" stroke="white" stroke-width="1.6" fill="rgba(255,255,255,0.02)"/>
                </svg>

            </div>

            <div class="hero-content">
                <h1>E-LAUNDRY</h1>
                <p>
                    Selamat datang di E-LAUNDRY!<br>
                    Pesan layanan laundry dengan mudah, hasil bersih dan wangi, serta status pesanan yang bisa dipantau kapan saja.
                </p>
                <div class="hero-buttons">
                    <a href="check_receipt.php" class="btn btn-secondary">Cek Pesanan Anda</a>
                </div>
            </div>
        </section>

        <!-- Features -->
<section class="features" id="layanan">
    <!-- Logo 1: Cepat & Terpercaya -->
    <div class="feature-card">
        <div class="feature-icon">
            <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="40" fill="#FFF9E6" stroke="#FF9800" stroke-width="2"/>
                <!-- Lightning bolt -->
                <path d="M40 35 L50 45 L45 50 L60 65 L35 50 L45 50 L30 35 Z" 
                      fill="#FF9800" stroke="#FF8C00" stroke-width="1.5"/>
                <!-- Speed line -->
                <line x1="65" y1="35" x2="75" y2="30" stroke="#FF9800" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
        <h3>Cepat & Terpercaya</h3>
        <p>Layanan laundry cepat tanpa mengurangi kualitas hasil akhir</p>
    </div>
    
    <!-- Logo 2: Harga Terjangkau -->
    <div class="feature-card">
        <div class="feature-icon">
            <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="40" fill="#F1F8E9" stroke="#4CAF50" stroke-width="2"/>
                <!-- Money bill -->
                <rect x="35" y="40" width="30" height="18" rx="3" fill="#4CAF50" stroke="#388E3C" stroke-width="1.5"/>
                <!-- RP text -->
                <text x="50" y="52" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="#FFFFFF">Rp</text>
                <!-- Coin -->
                <circle cx="60" cy="32" r="6" fill="#FFD600" stroke="#FFC107" stroke-width="1.5"/>
            </svg>
        </div>
        <h3>Harga Terjangkau</h3>
        <p>Harga kompetitif dengan layanan premium untuk semua kalangan</p>
    </div>
    
    <!-- Logo 3: Pengiriman Gratis -->
    <div class="feature-card">
        <div class="feature-icon">
            <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="40" fill="#E8F4FD" stroke="#2196F3" stroke-width="2"/>
                <!-- Delivery truck -->
                <rect x="35" y="45" width="30" height="12" rx="2" fill="#2196F3"/>
                <rect x="40" y="38" width="20" height="10" fill="#1976D2" rx="1"/>
                <!-- Wheels -->
                <circle cx="42" cy="60" r="5" fill="#333"/>
                <circle cx="42" cy="60" r="2" fill="#FFF"/>
                <circle cx="58" cy="60" r="5" fill="#333"/>
                <circle cx="58" cy="60" r="2" fill="#FFF"/>
                <!-- FREE badge -->
                <rect x="62" y="32" width="16" height="8" rx="2" fill="#FF5722"/>
                <text x="70" y="37" text-anchor="middle" font-family="Arial, sans-serif" font-size="5" font-weight="bold" fill="#FFF">FREE</text>
            </svg>
        </div>
        <h3>Pengiriman Gratis</h3>
        <p>Pengiriman dan pengambilan gratis untuk area tertentu</p>
    </div>
</section>

        <!-- Price List Section -->
        <section class="price-section" id="harga">
            <h2 class="section-title">Daftar Harga Layanan Kami</h2>
            <div class="price-container">
                <?php if (!empty($packages)): ?>
                    <?php foreach ($packages as $index => $package): 
                        $is_featured = ($index === floor($total_packages / 2)) && $total_packages > 1;
                        $badge = getBadgeCategory($index, $total_packages);
                    ?>
                    <div class="price-card<?php echo $is_featured ? ' featured' : ''; ?>">
                        <span class="badge"><?php echo htmlspecialchars($badge); ?></span>
                        <h3><?php echo htmlspecialchars($package['nama_paket']); ?></h3>
                        <p><?php echo htmlspecialchars($package['deskripsi']); ?></p>
                        <div class="price"><?php echo formatHarga($package['harga_per_qty']); ?></div>
                        <div class="price-info">per <?php echo htmlspecialchars($package['satuan']); ?> (<?php echo $package['estimasi_hari']; ?> hari)</div>
                        <ul class="features-list">
                            <li>Layanan Berkualitas</li>
                            <li>Dikerjakan Profesional</li>
                            <li>Tepat Waktu</li>
                            <li>Harga Terjangkau</li>
                        </ul>
                        <button class="btn-choose">Pilih Layanan</button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #666;">
                        <p>Tidak ada paket layanan yang tersedia saat ini. Silahkan coba lagi nanti.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="footer-container">
                <p class="footer-intro" style="margin-bottom: 2rem;">Untuk informasi dan layanan lebih lanjut, hubungi kami melalui kontak di bawah ini.</p>
                
                <div class="footer-contact">
                    <!-- Alamat -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="contact-info">
                            <h4>Alamat</h4>
                            <p>Jl. Bumi Manti No. 10<br>Kota Bandar Lampung<br>Indonesia</p>
                        </div>
                    </div>

                    <!-- Telepon -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div class="contact-info">
                            <h4>Telepon</h4>
                            <p><a href="tel:0219940445" style="color: white; text-decoration: none;">021-9940445</a></p>
                        </div>
                    </div>

                    <!-- WhatsApp -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <div class="contact-info">
                            <h4>WhatsApp</h4>
                            <p><a href="https://wa.me/6281234567890" style="color: white; text-decoration: none;" target="_blank">0812-3456-7890</a></p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        </div>
                        <div class="contact-info">
                            <h4>Email</h4>
                            <p><a href="mailto:info@elaundry.com" style="color: white; text-decoration: none;">info@elaundry.com</a></p>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; 2025 E-LAUNDRY. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <script>
            // Handle service selection buttons
            document.querySelectorAll('.btn-choose').forEach(button => {
                button.addEventListener('click', function() {
                    const serviceCard = this.closest('.price-card');
                    const serviceName = serviceCard.querySelector('h3').textContent;
                    const servicePrice = serviceCard.querySelector('.price').textContent;
                
                    alert('Anda memilih: ' + serviceName + '\n' + servicePrice);
                });
            });
        </script>
    </body>
    </html>
        // Handle service selection buttons
