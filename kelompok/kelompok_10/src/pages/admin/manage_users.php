<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Query untuk mengambil semua user
$users_query = "SELECT id, username, full_name, role, is_active, created_at 
                FROM users 
                ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);

// Hitung statistik user
$stats_query = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN role = 'kasir' THEN 1 ELSE 0 END) as total_kasir,
                    SUM(CASE WHEN role = 'worker' THEN 1 ELSE 0 END) as total_worker
                FROM users 
                WHERE role != 'admin'";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

$page_title = "Kelola User";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - E-Laundry</title>
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #333;
        }

        .btn-primary {
            background: #008080;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: #006666;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.3);
        }

        .stats-mini-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-mini-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .stat-mini-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .stat-mini-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #008080;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid #E0E0E0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 18px;
            color: #333;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 8px 35px 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
            font-size: 14px;
        }

        .search-box input:focus {
            outline: none;
            border-color: #008080;
        }

        .search-box svg {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #F8F9FA;
            padding: 15px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody td {
            padding: 18px 20px;
            border-top: 1px solid #F0F0F0;
            font-size: 14px;
            color: #333;
        }

        tbody tr:hover {
            background: #F8FFFE;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-admin {
            background: #FFE5E5;
            color: #CC0000;
        }

        .badge-kasir {
            background: #E5F3FF;
            color: #0066CC;
        }

        .badge-worker {
            background: #F0E5FF;
            color: #6600CC;
        }

        .status-toggle {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .status-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 26px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #008080;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-icon:hover {
            background: #F0F0F0;
        }

        .btn-edit {
            color: #008080;
        }

        .btn-delete {
            color: #CC0000;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #E0E0E0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 20px;
            color: #333;
        }

        .close {
            font-size: 28px;
            font-weight: 300;
            color: #999;
            cursor: pointer;
            border: none;
            background: none;
            line-height: 1;
        }

        .close:hover {
            color: #333;
        }

        .modal-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #008080;
        }

        .form-group input:disabled {
            background: #F5F5F5;
            cursor: not-allowed;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .btn-secondary {
            background: #E0E0E0;
            color: #666;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #D0D0D0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert-success {
            background: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
        }

        .alert-error {
            background: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include '../../includes/sidebar_admin.php'; ?>
        
        <main class="main-content">
            <?php include '../../includes/header_admin.php'; ?>
            
            <div class="content-wrapper">
                <div class="page-header">
                    <h1>Kelola User</h1>
                    <button class="btn-primary" onclick="openAddModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah User
                    </button>
                </div>

                <div id="alertContainer"></div>

                <!-- Statistik Mini -->
                <div class="stats-mini-grid">
                    <div class="stat-mini-card">
                        <h3>Total User</h3>
                        <div class="number"><?php echo $stats['total_users']; ?></div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>User Aktif</h3>
                        <div class="number"><?php echo $stats['active_users']; ?></div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>Kasir</h3>
                        <div class="number"><?php echo $stats['total_kasir']; ?></div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>Petugas</h3>
                        <div class="number"><?php echo $stats['total_worker']; ?></div>
                    </div>
                </div>

                <!-- Tabel User -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Daftar User</h2>
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Cari user..." onkeyup="searchTable()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <?php if (mysqli_num_rows($users_result) > 0): ?>
                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user['role']; ?>">
                                        <?php 
                                        echo $user['role'] === 'admin' ? 'Admin' : 
                                             ($user['role'] === 'kasir' ? 'Kasir' : 'Petugas'); 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <label class="status-toggle">
                                        <input type="checkbox" 
                                               <?php echo $user['is_active'] ? 'checked' : ''; ?>
                                               onchange="toggleStatus(<?php echo $user['id']; ?>, this.checked)"
                                               <?php echo $user['role'] === 'admin' ? 'disabled' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon btn-edit" 
                                                onclick='editUser(<?php echo json_encode($user); ?>)'
                                                title="Edit">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                        <button class="btn-icon btn-delete" 
                                                onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')"
                                                title="Hapus">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <p>Belum ada user yang terdaftar</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah/Edit User -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah User</h2>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="user_id">
                    <input type="hidden" id="formAction" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Nama Lengkap *</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="kasir">Kasir</option>
                            <option value="worker">Petugas</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="passwordGroup">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password">
                        <small style="color: #666; font-size: 12px;">Minimal 6 karakter</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                        <button type="submit" class="btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk membuka modal tambah user
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah User';
            document.getElementById('formAction').value = 'add';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('username').disabled = false;
            document.getElementById('password').required = true;
            document.getElementById('passwordGroup').style.display = 'block';
            document.getElementById('userModal').style.display = 'block';
        }

        // Fungsi untuk membuka modal edit user
        function editUser(user) {
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('userId').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('username').disabled = true;
            document.getElementById('full_name').value = user.full_name;
            document.getElementById('role').value = user.role;
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('passwordGroup').querySelector('small').textContent = 'Kosongkan jika tidak ingin mengubah password';
            document.getElementById('userModal').style.display = 'block';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
            document.getElementById('userForm').reset();
        }

        // Tutup modal jika klik di luar modal
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Handle form submit
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../../process/admin_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                showAlert('error', 'Terjadi kesalahan: ' + error.message);
            });
        });

        // Fungsi toggle status user
        function toggleStatus(userId, isActive) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('user_id', userId);
            formData.append('is_active', isActive ? 1 : 0);
            
            fetch('../../process/admin_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message);
                    // Kembalikan toggle jika gagal
                    location.reload();
                }
            })
            .catch(error => {
                showAlert('error', 'Terjadi kesalahan: ' + error.message);
                location.reload();
            });
        }

        // Fungsi delete user
        function deleteUser(userId, username) {
            if (confirm(`Apakah Anda yakin ingin menghapus user "${username}"?`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('user_id', userId);
                
                fetch('../../process/admin_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    showAlert('error', 'Terjadi kesalahan: ' + error.message);
                });
            }
        }

        // Fungsi search table
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('usersTable');
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 1; i < tr.length; i++) {
                let found = false;
                const td = tr[i].getElementsByTagName('td');
                
                for (let j = 0; j < td.length - 1; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }

        // Fungsi untuk menampilkan alert
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    ${message}
                </div>
            `;
            
            const alert = alertContainer.querySelector('.alert');
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
