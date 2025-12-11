<?php
$active_page = $active_page ?? 'task_list'; 
?>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="../../assets/img/Zira_Laundry.jpg" alt="Zira Laundry" class="header-icon" style="width: 80px; height: 80px; object-fit: contain; border-radius: 10px;">
        <h1>ZIRA LAUNDRY</h1>
        <p class="role-caption">Petugas Cuci</p>
    </div>
    <nav>
        <a href="task_list.php" class="<?php echo ($active_page === 'task_list') ? 'active' : ''; ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect></svg>
            Daftar Tugas Aktif
        </a>
        <a href="task_history.php" class="<?php echo ($active_page === 'task_history') ? 'active' : ''; ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Riwayat Tugas
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../../process/auth_handler.php?action=logout" class="btn-logout">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Keluar
        </a>
    </div>
</div>
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f9; margin: 0; display: flex; min-height: 100vh; }
.sidebar { 
    width: 250px; 
    background-color: #038472; 
    color: white; 
    padding: 20px 0; 
    position: sticky; 
    top: 0; 
    height: 100vh; 
    display: flex; 
    flex-direction: column; 
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    font-size: 0.95em; 
}
.sidebar-header {
    text-align: center;
    padding: 0 20px 20px; 
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 10px;
}
.sidebar-header h1 {
    margin: 5px 0 0;
    font-size: 1.5em; 
    font-weight: 600;
}
.role-caption {
    color: rgba(255, 255, 255, 0.7); 
    font-size: 0.8em;
    margin: 0;
}
.header-icon {
    width: 40px;
    height: 40px;
    margin-bottom: 5px;
}
.sidebar nav {
    flex-grow: 1; 
}
.sidebar a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    text-decoration: none;
    color: white;
    transition: background-color 0.3s, border-left 0.3s;
}
.sidebar a:hover {
    background-color: rgba(0, 0, 0, 0.1); 
}
.sidebar-icon {
    width: 20px;
    height: 20px;
    margin-right: 12px;
}
.sidebar a.active {
    background-color: rgba(0, 0, 0, 0.15); 
    border-left: 5px solid white; 
    padding-left: 15px; 
}
.sidebar-footer {
    padding: 10px 0 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}
.btn-logout {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white;
    padding: 12px 20px;
    transition: background-color 0.3s;
}
.btn-logout:hover {
    background-color: #c0392b; 
}
</style>