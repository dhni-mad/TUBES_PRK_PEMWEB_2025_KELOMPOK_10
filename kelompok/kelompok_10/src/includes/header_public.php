<?php
// Start session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tentukan halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page == 'index.php' || $current_page == 'check_receipt.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Laundry Express' : 'Laundry Express'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        /* User Menu */
        .user-menu {
            position: relative;
        }

        .user-menu-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border: 2px solid white;
            border-radius: 20px;
            color: white;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .user-menu-toggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #008080;
            font-weight: bold;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            margin-top: 0.5rem;
            z-index: 1001;
            overflow: hidden;
        }

        .dropdown-menu.active {
            display: block;
            animation: dropdownSlide 0.3s ease;
        }

        @keyframes dropdownSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: block;
            width: 100%;
            padding: 1rem;
            border: none;
            background: none;
            text-align: left;
            color: #333;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .dropdown-menu a:last-child,
        .dropdown-menu button:last-child {
            border-bottom: none;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background-color: #f5f5f5;
            color: #008080;
            padding-left: 1.5rem;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 0.5rem 0;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
                flex-wrap: wrap;
            }

            .navbar-brand {
                font-size: 1.5rem;
                order: 1;
            }

            .mobile-menu-toggle {
                display: block;
                order: 3;
            }

            nav {
                order: 4;
                flex-basis: 100%;
                flex-direction: column;
                gap: 0;
                max-height: 0;
                overflow: hidden;
                margin-top: 1rem;
                transition: max-height 0.3s ease;
            }

            nav.active {
                max-height: 500px;
            }

            nav ul {
                flex-direction: column;
                gap: 0;
                width: 100%;
            }

            nav a {
                padding: 1rem;
                display: block;
                border-radius: 5px;
            }

            nav a::after {
                display: none;
            }

            nav a.active {
                background-color: rgba(255, 255, 255, 0.1);
                border: none;
            }

            .auth-buttons {
                order: 5;
                flex-basis: 100%;
                flex-direction: column;
                margin-top: 1rem;
                gap: 0.5rem;
            }

            .btn-auth {
                width: 100%;
                text-align: center;
            }

            .user-menu {
                order: 5;
                flex-basis: 100%;
                margin-top: 1rem;
            }

            .dropdown-menu {
                position: static;
                box-shadow: none;
                background-color: rgba(255, 255, 255, 0.1);
                margin-top: 0;
                display: none;
            }

            .dropdown-menu.active {
                display: block;
            }

            .user-menu-toggle {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 0.75rem;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .navbar-brand-text {
                display: none;
            }

            nav a {
                padding: 0.8rem;
            }

            .btn-auth {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
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
                    <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Beranda</a></li>
                    <li><a href="index.php#harga" class="<?php echo ($current_page == 'index.php') ? 'nav-link' : ''; ?>">Daftar Harga</a></li>
                    <li><a href="check_receipt.php" class="<?php echo ($current_page == 'check_receipt.php') ? 'active' : ''; ?>">Cek Resi</a></li>
                    <li><a href="#tentang">Tentang Kami</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>
            </nav>

            <div class="auth-buttons" id="authButtons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User is logged in -->
                    <div class="user-menu">
                        <button class="user-menu-toggle" id="userMenuToggle">
                            <span class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?></span>
                            <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            <span>▼</span>
                        </button>
                        <div class="dropdown-menu" id="userDropdown">
                            <a href="../auth/dashboard_customer.php">📊 Dashboard</a>
                            <a href="../auth/my_orders.php">📦 Pesanan Saya</a>
                            <a href="../auth/profile.php">👤 Profil</a>
                            <a href="../auth/settings.php">⚙️ Pengaturan</a>
                            <div class="dropdown-divider"></div>
                            <a href="../auth/logout.php" style="color: #f44336;">🚪 Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- User is not logged in -->
                    <a href="../auth/login.php" class="btn-auth btn-login">Masuk</a>
                    <a href="../auth/register.php" class="btn-auth btn-signup">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const navMenu = document.getElementById('navMenu');

        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });

        // Close mobile menu when a link is clicked
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
            });
        });

        // User menu dropdown
        const userMenuToggle = document.getElementById('userMenuToggle');
        const userDropdown = document.getElementById('userDropdown');

        if (userMenuToggle) {
            userMenuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('active');
                }
            });
        }

        // Active menu highlighting for current page
        function setActiveMenu() {
            const currentPage = '<?php echo $current_page; ?>';
            document.querySelectorAll('nav a').forEach(link => {
                const href = link.getAttribute('href');
                if (href === currentPage || (currentPage === 'index.php' && href === 'index.php')) {
                    link.classList.add('active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', setActiveMenu);
    </script>
</body>
</html>
