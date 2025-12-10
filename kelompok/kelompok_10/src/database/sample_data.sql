-- Insert sample transactions for testing
INSERT INTO transactions (id, nama_pelanggan, no_hp, alamat, package_id, berat_qty, total_harga, status_laundry, status_bayar, tgl_masuk, tgl_estimasi_selesai, kasir_input_id) VALUES
('TRX001', 'Budi Santoso', '081234567890', 'Jl. Merdeka No. 123', 1, 5.5, 33000, 'Pending', 'Unpaid', '2025-12-08 10:30:00', '2025-12-11 10:30:00', 2),
('TRX002', 'Siti Nurhaliza', '082345678901', 'Jl. Ahmad Yani No. 45', 2, 3.0, 12000, 'Washing', 'Paid', '2025-12-08 11:15:00', '2025-12-10 11:15:00', 2),
('TRX003', 'Ahmad Wijaya', '083456789012', 'Jl. Sudirman No. 67', 4, 2.5, 30000, 'Ironing', 'Paid', '2025-12-07 14:20:00', '2025-12-08 14:20:00', 2),
('TRX004', 'Rini Kusuma', '084567890123', 'Jl. Gatot Subroto No. 89', 1, 4.0, 24000, 'Done', 'Paid', '2025-12-06 09:00:00', '2025-12-09 09:00:00', 2),
('TRX005', 'Hendra Gunawan', '085678901234', 'Jl. Imam Bonjol No. 11', 3, 2.0, 10000, 'Taken', 'Paid', '2025-12-05 16:45:00', '2025-12-06 16:45:00', 2),
('TRX006', 'Dewi Lestari', '086789012345', 'Jl. Diponegoro No. 22', 1, 6.0, 36000, 'Pending', 'Unpaid', '2025-12-10 08:30:00', '2025-12-13 08:30:00', 2),
('TRX007', 'Fajar Hermawan', '087890123456', 'Jl. Basuki Rahmat No. 33', 2, 2.5, 10000, 'Washing', 'Paid', '2025-12-09 13:00:00', '2025-12-11 13:00:00', 2),
('TRX008', 'Maya Anggraeni', '088901234567', 'Jl. Jendral Sudirman No. 44', 4, 3.5, 42000, 'Done', 'Paid', '2025-12-04 10:15:00', '2025-12-05 10:15:00', 2);
