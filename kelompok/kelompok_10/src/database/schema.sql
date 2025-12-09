CREATE DATABASE IF NOT EXISTS laundry_system;
use laundry_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(15),
    role ENUM('admin', 'kasir', 'worker') NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_paket VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga_per_qty DECIMAL(10, 2) NOT NULL,
    satuan ENUM('kg', 'pcs') NOT NULL DEFAULT 'kg',
    estimasi_hari INT DEFAULT 3,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transactions (
    id VARCHAR(20) PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    alamat TEXT,
    package_id INT NOT NULL,
    berat_qty DECIMAL(8, 2) NOT NULL,
    total_harga DECIMAL(12, 2) NOT NULL,
    status_laundry ENUM('Pending', 'Washing', 'Ironing', 'Done', 'Taken') DEFAULT 'Pending',
    status_bayar ENUM('Unpaid', 'Paid') DEFAULT 'Unpaid',
    tgl_masuk DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tgl_estimasi_selesai DATETIME,
    tgl_selesai DATETIME NULL,
    tgl_diambil DATETIME NULL,
    kasir_input_id INT,
    kasir_bayar_id INT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE RESTRICT,
    FOREIGN KEY (kasir_input_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (kasir_bayar_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status_laundry (status_laundry),
    INDEX idx_status_bayar (status_bayar),
    INDEX idx_tgl_masuk (tgl_masuk),
    INDEX idx_nama_pelanggan (nama_pelanggan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id VARCHAR(20) NOT NULL,
    status_before ENUM('Pending', 'Washing', 'Ironing', 'Done', 'Taken'),
    status_after ENUM('Pending', 'Washing', 'Ironing', 'Done', 'Taken') NOT NULL,
    changed_by INT NOT NULL,
    catatan VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payment_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id VARCHAR(20) NOT NULL,
    jumlah_bayar DECIMAL(12, 2) NOT NULL,
    metode_bayar ENUM('Cash', 'Transfer', 'E-Wallet') DEFAULT 'Cash',
    kasir_id INT NOT NULL,
    catatan VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (kasir_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(15),
    alamat TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, full_name, role, is_active) VALUES
('admin', '$2y$10$4SuWwALHx5yzZ/l74zbqiQ0H0CpVOHUcSDF2UTantLpQUAVE31MDG', 'Administrator', 'admin', TRUE),
('kasir01', '$2y$10$4SuWwALHx5yzZ/l74zbqiQ0H0CpVOHUcSDF2UTantLpQUAVE31MDG', 'Kasir 1', 'kasir', TRUE),
('worker01', '$2y$10$4SuWwALHx5yzZ/l74zbqiQ0H0CpVOHUcSDF2UTantLpQUAVE31MDG', 'Petugas Cuci 1', 'worker', TRUE);

INSERT INTO packages (nama_paket, deskripsi, harga_per_qty, satuan, estimasi_hari) VALUES
('Cuci Komplit', 'Cuci, Kering, Setrika, dan Parfum', 6000.00, 'kg', 3),
('Cuci Kering', 'Cuci dan Kering (Tanpa Setrika)', 4000.00, 'kg', 2),
('Setrika Saja', 'Khusus Setrika', 5000.00, 'kg', 1),
('Express 1 Hari', 'Cuci Komplit Selesai 1 Hari', 12000.00, 'kg', 1),
('Bedcover', 'Cuci Bedcover/Sprei', 25000.00, 'pcs', 3),
('Sepatu', 'Cuci Sepatu', 15000.00, 'pcs', 2);

DELIMITER $$

CREATE TRIGGER before_insert_transaction
BEFORE INSERT ON transactions
FOR EACH ROW
BEGIN
    DECLARE next_number INT;
    DECLARE today_date VARCHAR(8);
    DECLARE prefix VARCHAR(20);
    
    SET today_date = DATE_FORMAT(NOW(), '%Y%m%d');
    SET prefix = CONCAT('TRX-', today_date, '-');
    
    SELECT COALESCE(COUNT(*), 0) + 1 INTO next_number
    FROM transactions
    WHERE id LIKE CONCAT(prefix, '%');
    
    IF NEW.id IS NULL OR NEW.id = '' THEN
        SET NEW.id = CONCAT(prefix, LPAD(next_number, 4, '0'));
    END IF;
    
    IF NEW.tgl_estimasi_selesai IS NULL THEN
        SET NEW.tgl_estimasi_selesai = DATE_ADD(NEW.tgl_masuk, INTERVAL (
            SELECT estimasi_hari FROM packages WHERE id = NEW.package_id
        ) DAY);
    END IF;
END$$

DELIMITER ;

CREATE VIEW v_dashboard_summary AS
SELECT 
    COUNT(*) as total_transaksi,
    SUM(CASE WHEN status_bayar = 'Unpaid' THEN 1 ELSE 0 END) as belum_dibayar,
    SUM(CASE WHEN status_laundry IN ('Pending', 'Washing', 'Ironing') THEN 1 ELSE 0 END) as sedang_proses,
    SUM(CASE WHEN status_laundry = 'Done' AND status_bayar = 'Paid' THEN 1 ELSE 0 END) as siap_diambil,
    SUM(CASE WHEN DATE(tgl_masuk) = CURDATE() THEN total_harga ELSE 0 END) as pendapatan_hari_ini,
    SUM(CASE WHEN MONTH(tgl_masuk) = MONTH(CURDATE()) THEN total_harga ELSE 0 END) as pendapatan_bulan_ini
FROM transactions;
