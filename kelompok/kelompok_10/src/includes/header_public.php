<header>
    <div class="logo-section">
        <div class="logo-icon">
            <img src="../../assets/img/Zira_Laundry.jpg" alt="Zira Laundry">
        </div>
        <div class="logo-text">
            <h1>ZIRA LAUNDRY</h1>
            <p>PROFESSIONAL LAUNDRY SERVICE</p>
        </div>
    </div>
    <div class="header-right">
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']);
        if ($current_page == 'check_receipt.php'): 
        ?>
            <a href="index.php" class="btn-back-home">Kembali ke Beranda</a>
        <?php else: ?>
            <a href="../auth/login.php" class="btn-login-employee">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Masuk Karyawan
            </a>
        <?php endif; ?>
    </div>
</header>
