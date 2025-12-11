<?php
session_start();
require_once '../../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
$packages_query = "SELECT id, nama_paket, deskripsi, harga_per_qty, satuan, estimasi_hari, is_active, created_at 
                   FROM packages 
                   ORDER BY created_at DESC";
$packages_result = mysqli_query($conn, $packages_query);
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
    <title>Kelola Paket - Zira Laundry</title>
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
                        Tambah Paket
                    </button>
                </div>
                <div id="alertContainer"></div>
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
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Paket';
            document.getElementById('formAction').value = 'add_package';
            document.getElementById('packageForm').reset();
            document.getElementById('packageId').value = '';
            document.getElementById('packageModal').style.display = 'block';
        }
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
        function closeModal() {
            document.getElementById('packageModal').style.display = 'none';
            document.getElementById('packageForm').reset();
        }
        window.onclick = function(event) {
            const modal = document.getElementById('packageModal');
            if (event.target === modal) {
                closeModal();
            }
        }
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
        document.getElementById('harga_per_qty').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            this.value = value;
        });
    </script>
</body>
</html>
