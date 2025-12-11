<?php
// Menentukan halaman aktif
$active_page = $active_page ?? 'new_transaction';
?>

<div class="sidebar">
    <div class="sidebar-header">
        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 3h18v4H3z"></path>
            <path d="M3 9h18v12H3z"></path>
            <circle cx="7" cy="15" r="2"></circle>
            <circle cx="17" cy="15" r="2"></circle>
        </svg>
        <h1>E-LAUNDRY</h1>
        <p class="role-caption">Kasir</p>
    </div>

    <nav>

        <!-- TRANSAKSI BARU -->
        <a href="new_transaction.php"
           class="<?= ($active_page === 'new_transaction') ? 'active' : ''; ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M12 5v14"></path>
                <path d="M5 12h14"></path>
            </svg>
            Transaksi Baru
        </a>

        <!-- SEMUA TRANSAKSI -->
        <a href="transaction_list.php"
           class="<?= ($active_page === 'transaction_list') ? 'active' : ''; ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
            </svg>
            Daftar Transaksi
        </a>

        <!-- CETAK STRUK -->
        <a href="invoice_print.php"
           class="<?= ($active_page === 'invoice_print') ? 'active' : ''; ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M6 2h12v4H6z"></path>
                <path d="M6 6h12v16H6z"></path>
                <line x1="9" y1="10" x2="15" y2="10"></line>
                <line x1="9" y1="14" x2="15" y2="14"></line>
                <line x1="9" y1="18" x2="15" y2="18"></line>
            </svg>
            Cetak Struk
        </a>

    </nav>

    <div class="sidebar-footer">
        <a href="../auth/logout.php" class="btn-logout">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" style="margin-right: 8px;">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Keluar
        </a>
    </div>
</div>

<style>
body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f9; margin: 0; display: flex; min-height: 100vh; }

.sidebar { 
    width: 250px; 
    background-color: #038472; 
    color: white; 
    padding: 20px 0;
    position: sticky; top: 0; height: 100vh;
    display: flex; flex-direction: column;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    font-size: 0.95em;
}

.sidebar-header {
    text-align: center;
    padding: 0 20px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 10px;
}

.sidebar-header h1 { margin: 5px 0 0; font-size: 1.5em; font-weight: 600; }

.role-caption { color: rgba(255,255,255,0.7); font-size: 0.8em; }

.header-icon { width: 40px; height: 40px; margin-bottom: 5px; }

.sidebar nav { flex-grow: 1; }

.sidebar a {
    display: flex; align-items: center;
    padding: 12px 20px;
    text-decoration: none; color: white;
    transition: background 0.3s, border-left 0.3s;
}

.sidebar a:hover { background-color: rgba(0,0,0,0.1); }

.sidebar-icon { width: 20px; height: 20px; margin-right: 12px; }

.sidebar a.active {
    background-color: rgba(0,0,0,0.15);
    border-left: 5px solid white;
    padding-left: 15px;
}

.sidebar-footer {
    padding: 10px 0 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.btn-logout {
    display: flex; align-items: center;
    padding: 12px 20px;
    text-decoration: none; color: white;
}

.btn-logout:hover { background-color: #c0392b; }
</style>
