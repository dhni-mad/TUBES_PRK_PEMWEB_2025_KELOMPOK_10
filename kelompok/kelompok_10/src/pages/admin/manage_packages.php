<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Query untuk mengambil semua paket
$packages_query = "SELECT id, nama_paket, deskripsi, harga_per_qty, satuan, estimasi_hari, is_active, created_at 
                   FROM packages 
                   ORDER BY created_at DESC";
$packages_result = mysqli_query($conn, $packages_query);

// Hitung statistik paket
$stats_query = "SELECT 
                    COUNT(*) as total_packages,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_packages,
                    SUM(CASE WHEN satuan = 'kg' THEN 1 ELSE 0 END) as total_kg,
                    SUM(CASE WHEN satuan = 'pcs' THEN 1 ELSE 0 END) as total_pcs
                FROM packages";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

$page_title = "Kelola Paket";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket - E-Laundry</title>
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

        .package-name {
            font-weight: 600;
            color: #008080;
        }

        .package-desc {
            color: #666;
            font-size: 13px;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .price {
            font-weight: 600;
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-kg {
            background: #E3F2FD;
            color: #1976D2;
        }

        .badge-pcs {
            background: #F3E5F5;
            color: #7B1FA2;
        }

        .badge-status {
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
            border: none;
            background: none;
        }

        .badge-active {
            background: #D4EDDA;
            color: #155724;
        }

        .badge-active:hover {
            background: #C3E6CB;
            transform: translateY(-1px);
        }

        .badge-inactive {
            background: #F8D7DA;
            color: #721C24;
        }

        .badge-inactive:hover {
            background: #F5C6CB;
            transform: translateY(-1px);
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
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            animation: slideDown 0.3s;
            max-height: 90vh;
            overflow-y: auto;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #008080;
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

        .input-prefix {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .input-prefix span {
            padding: 10px 15px;
            background: #F5F5F5;
            color: #666;
            font-size: 14px;
            border-right: 1px solid #ddd;
        }

        .input-prefix input {
            border: none;
            flex: 1;
        }

        .helper-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
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
                    <h1>Kelola Paket</h1>
                    <button class="btn-primary" onclick="openAddModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Paket
                    </button>
                </div>

                <div id="alertContainer"></div>

                <!-- Statistik Mini -->
                <div class="stats-mini-grid">
                    <div class="stat-mini-card">
                        <h3>Total Paket</h3>
                        <div class="number"><?php echo $stats['total_packages']; ?></div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>Paket Aktif</h3>
                        <div class="number"><?php echo $stats['active_packages']; ?></div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>Paket per KG</h3>
                        <div class="number"><?php echo $stats['total_kg']; ?></div>
                    </div>
                    <div class="stat-mini-card">
                        <h3>Paket per PCS</h3>
                        <div class="number"><?php echo $stats['total_pcs']; ?></div>
                    </div>
                </div>

                <!-- Tabel Paket -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Daftar Paket Laundry</h2>
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Cari paket..." onkeyup="searchTable()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <?php if (mysqli_num_rows($packages_result) > 0): ?>
                    <table id="packagesTable">
                        <thead>
                            <tr>
                                <th>Nama Paket</th>
                                <th>Deskripsi</th>
                                <th>Harga</th>
                                <th>Satuan</th>
                                <th>Estimasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($package = mysqli_fetch_assoc($packages_result)): ?>
                            <tr>
                                <td>
                                    <div class="package-name"><?php echo htmlspecialchars($package['nama_paket']); ?></div>
                                </td>
                                <td>
                                    <div class="package-desc" title="<?php echo htmlspecialchars($package['deskripsi']); ?>">
                                        <?php echo htmlspecialchars($package['deskripsi'] ?: '-'); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="price">Rp <?php echo number_format($package['harga_per_qty'], 0, ',', '.'); ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $package['satuan']; ?>">
                                        <?php echo strtoupper($package['satuan']); ?>
                                    </span>
                                </td>
                                <td><?php echo $package['estimasi_hari']; ?> hari</td>
                                <td>
                                    <button class="badge-status <?php echo $package['is_active'] ? 'badge-active' : 'badge-inactive'; ?>" 
                                            onclick="toggleStatus(<?php echo $package['id']; ?>, <?php echo $package['is_active'] ? '0' : '1'; ?>, this)">
                                        <?php echo $package['is_active'] ? 'Aktif' : 'Non-Aktif'; ?>
                                    </button>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon btn-edit" 
                                                onclick='editPackage(<?php echo json_encode($package); ?>)'
                                                title="Edit">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>
                                        <button class="btn-icon btn-delete" 
                                                onclick="deletePackage(<?php echo $package['id']; ?>, '<?php echo htmlspecialchars($package['nama_paket']); ?>')"
                                                title="Hapus">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        <p>Belum ada paket yang terdaftar</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah/Edit Paket -->
    <div id="packageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Paket</h2>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="packageForm">
                    <input type="hidden" id="packageId" name="package_id">
                    <input type="hidden" id="formAction" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="nama_paket">Nama Paket *</label>
                        <input type="text" id="nama_paket" name="nama_paket" required placeholder="Contoh: Cuci Komplit">
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" placeholder="Jelaskan detail layanan paket ini..."></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="harga_per_qty">Harga *</label>
                            <div class="input-prefix">
                                <span>Rp</span>
                                <input type="number" id="harga_per_qty" name="harga_per_qty" required min="0" step="100" placeholder="0">
                            </div>
                            <div class="helper-text">Harga per satuan</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="satuan">Satuan *</label>
                            <select id="satuan" name="satuan" required>
                                <option value="">Pilih Satuan</option>
                                <option value="kg">Kilogram (KG)</option>
                                <option value="pcs">Pieces (PCS)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="estimasi_hari">Estimasi Pengerjaan *</label>
                        <div class="input-prefix">
                            <input type="number" id="estimasi_hari" name="estimasi_hari" required min="1" max="30" value="3">
                            <span>hari</span>
                        </div>
                        <div class="helper-text">Waktu pengerjaan yang dibutuhkan</div>
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
        // Fungsi untuk membuka modal tambah paket
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Paket';
            document.getElementById('formAction').value = 'add_package';
            document.getElementById('packageForm').reset();
            document.getElementById('packageId').value = '';
            document.getElementById('packageModal').style.display = 'block';
        }

        // Fungsi untuk membuka modal edit paket
        function editPackage(package) {
            document.getElementById('modalTitle').textContent = 'Edit Paket';
            document.getElementById('formAction').value = 'edit_package';
            document.getElementById('packageId').value = package.id;
            document.getElementById('nama_paket').value = package.nama_paket;
            document.getElementById('deskripsi').value = package.deskripsi || '';
            document.getElementById('harga_per_qty').value = package.harga_per_qty;
            document.getElementById('satuan').value = package.satuan;
            document.getElementById('estimasi_hari').value = package.estimasi_hari;
            document.getElementById('packageModal').style.display = 'block';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('packageModal').style.display = 'none';
            document.getElementById('packageForm').reset();
        }

        // Tutup modal jika klik di luar modal
        window.onclick = function(event) {
            const modal = document.getElementById('packageModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Handle form submit
        document.getElementById('packageForm').addEventListener('submit', function(e) {
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

        // Fungsi toggle status paket
        function toggleStatus(packageId, isActive, buttonElement) {
            const formData = new FormData();
            formData.append('action', 'toggle_package_status');
            formData.append('package_id', packageId);
            formData.append('is_active', isActive);
            
            fetch('../../process/admin_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // Update tampilan badge tanpa reload
                    if (isActive == 1) {
                        buttonElement.className = 'badge-status badge-active';
                        buttonElement.textContent = 'Aktif';
                        buttonElement.onclick = function() { toggleStatus(packageId, 0, this); };
                    } else {
                        buttonElement.className = 'badge-status badge-inactive';
                        buttonElement.textContent = 'Non-Aktif';
                        buttonElement.onclick = function() { toggleStatus(packageId, 1, this); };
                    }
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                showAlert('error', 'Terjadi kesalahan: ' + error.message);
            });
        }

        // Fungsi delete paket
        function deletePackage(packageId, packageName) {
            if (confirm(`Apakah Anda yakin ingin menghapus paket "${packageName}"?`)) {
                const formData = new FormData();
                formData.append('action', 'delete_package');
                formData.append('package_id', packageId);
                
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
            const table = document.getElementById('packagesTable');
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

        // Format input harga dengan thousand separator
        document.getElementById('harga_per_qty').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            this.value = value;
        });
    </script>
</body>
</html>
