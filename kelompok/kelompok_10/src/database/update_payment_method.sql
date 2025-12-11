-- Update metode_bayar di payment_history untuk mendukung Tunai dan QRIS
-- Jalankan query ini jika database sudah ada sebelumnya

USE laundry_system;

-- Backup data lama (opsional)
-- CREATE TABLE payment_history_backup AS SELECT * FROM payment_history;

-- Alter table payment_history
ALTER TABLE payment_history 
MODIFY COLUMN metode_bayar ENUM('Tunai', 'QRIS', 'Transfer', 'E-Wallet', 'Cash') DEFAULT 'Tunai';

-- Update nilai lama 'Cash' menjadi 'Tunai' untuk konsistensi
UPDATE payment_history 
SET metode_bayar = 'Tunai' 
WHERE metode_bayar = 'Cash';

-- Setelah semua data diupdate, hapus opsi 'Cash' dari ENUM
ALTER TABLE payment_history 
MODIFY COLUMN metode_bayar ENUM('Tunai', 'QRIS', 'Transfer', 'E-Wallet') DEFAULT 'Tunai';

SELECT 'Update payment_history berhasil!' AS status;
