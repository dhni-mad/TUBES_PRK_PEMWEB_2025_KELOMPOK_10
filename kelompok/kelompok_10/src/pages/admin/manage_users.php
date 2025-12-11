<?php
session_start();
require_once '../../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
$users_query = "SELECT id, username, full_name, role, is_active, created_at 
                FROM users 
                ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
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
    <title>Kelola User - Zira Laundry</title>
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="admin-container">
        <?php include '../../includes/sidebar_admin.php'; ?>
        <main class="main-content">
            <?php include '../../includes/header_admin.php'; ?>
            <div class="content-wrapper">
                <div class="page-header">
                    <button class="btn-primary" onclick="openAddModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah User
                    </button>
                </div>
                <div id="alertContainer"></div>
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
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge-status badge-active" style="cursor: default;" title="Admin selalu aktif">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <button class="badge-status <?php echo $user['is_active'] ? 'badge-active' : 'badge-inactive'; ?>" 
                                                onclick="toggleStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? '0' : '1'; ?>, this)">
                                            <?php echo $user['is_active'] ? 'Aktif' : 'Non-Aktif'; ?>
                                        </button>
                                    <?php endif; ?>
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
        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
            document.getElementById('userForm').reset();
        }
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target === modal) {
                closeModal();
            }
        }
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
        function toggleStatus(userId, isActive, buttonElement) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('user_id', userId);
            formData.append('is_active', isActive);
            fetch('../../process/admin_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    if (isActive == 1) {
                        buttonElement.className = 'badge-status badge-active';
                        buttonElement.textContent = 'Aktif';
                        buttonElement.onclick = function() { toggleStatus(userId, 0, this); };
                    } else {
                        buttonElement.className = 'badge-status badge-inactive';
                        buttonElement.textContent = 'Non-Aktif';
                        buttonElement.onclick = function() { toggleStatus(userId, 1, this); };
                    }
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                showAlert('error', 'Terjadi kesalahan: ' + error.message);
            });
        }
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
