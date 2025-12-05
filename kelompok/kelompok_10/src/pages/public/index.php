<?php
// Contoh variabel dinamis (opsional)
$title = "Beranda | Web Kami";
$welcome = "Selamat Datang di Website Kelompok 10!";
$description = "Website ini dibuat untuk memenuhi tugas Pemrograman Web.";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        header {
            background: #4F46E5;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav {
            background: #333;
            padding: 10px;
            text-align: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 10px;
            font-size: 16px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            background: white;
            margin-top: 20px;
            border-radius: 8px;
        }
        footer {
            background: #4F46E5;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <header>
        <h1><?= $welcome ?></h1>
    </header>

    <nav>
        <a href="index.php">Beranda</a>
        <a href="about.php">Tentang</a>
        <a href="contact.php">Kontak</a>
    </nav>

    <div class="container">
        <h2>Halo, Pengunjung!</h2>
        <p><?= $description ?></p>
        <p>Ini adalah tampilan awal halaman beranda kami.</p>
    </div>

    <footer>
        &copy; 2025 Kelompok 10 - Pemrograman Web
    </footer>

</body>
</html>
