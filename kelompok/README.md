src/
│
├── config/
│   └── database.php                <-- (Ketua) Koneksi Database
│
├── database/
│   └── schema.sql                  <-- (Ketua) Tabel: users, packages, transactions
│
├── assets/                         <-- CSS & Gambar
│   ├── css/
│   │   ├── admin_auth.css          <-- (Ketua) Login & Dashboard Owner
│   │   ├── public.css              <-- (Anggota 1) Halaman Depan & Tracking
│   │   ├── worker.css              <-- (Anggota 2) Dashboard Petugas Cuci
│   │   └── cashier.css             <-- (Anggota 3) Form Kasir & Struk
│   │
│   └── img/
│       └── icons/                  <-- Ikon (Mesin cuci, setrika, lunas, pending)
│
├── includes/                       <-- Potongan Layout (Navbar/Sidebar)
│   ├── header_public.php           <-- (Anggota 1) Navbar Depan
│   ├── sidebar_admin.php           <-- (Ketua) Sidebar Admin
│   ├── sidebar_worker.php          <-- (Anggota 2) Sidebar Petugas
│   └── sidebar_cashier.php         <-- (Anggota 3) Sidebar Kasir
│
├── process/                        <-- Logika Backend PHP
│   ├── auth_handler.php            <-- (Ketua) Login/Logout
│   ├── admin_handler.php           <-- (Ketua) CRUD Paket & User
│   ├── tracking_handler.php        <-- (Anggota 1) Cek Resi
│   ├── worker_handler.php          <-- (Anggota 2) Update Status Cucian
│   └── transaction_handler.php     <-- (Anggota 3) Simpan Transaksi & Bayar
│
└── pages/                          <-- Halaman Tampilan (View)
    │
    ├── auth/
    │   └── login.php               <-- (Ketua) Form Login Pegawai
    │
    ├── admin/                      <-- AREA KETUA (Owner)
    │   ├── dashboard.php           <-- Laporan Pendapatan
    │   ├── manage_users.php        <-- Kelola Akun Pegawai
    │   └── manage_packages.php     <-- Atur Harga Paket (Kiloan/Satuan)
    │
    ├── public/                     <-- AREA ANGGOTA 1 (Pelanggan)
    │   ├── index.php               <-- Beranda & Daftar Harga
    │   └── check_status.php        <-- Hasil Tracking Resi
    │
    ├── worker/                     <-- AREA ANGGOTA 2 (Bagian Cuci)
    │   ├── task_list.php           <-- Daftar Cucian Aktif (Belum Selesai)
    │   └── task_history.php        <-- Riwayat Pengerjaan
    │
    └── cashier/                    <-- AREA ANGGOTA 3 (Kasir)
        ├── new_transaction.php     <-- Form Terima Cucian (Input Berat/Paket)
        ├── transaction_list.php    <-- List Transaksi (Ambil Cucian & Bayar)
        └── invoice_print.php       <-- Halaman Cetak Struk
        