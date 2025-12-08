<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header('Location: ../admin/dashboard.php');
    } elseif ($role === 'kasir') {
        header('Location: ../cashier/new_transaction.php');
    } elseif ($role === 'worker') {
        header('Location: ../worker/task_list.php');
    }
    exit();
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Laundry</title>
    <link rel="stylesheet" href="../../assets/css/auth.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-left">
                <div class="logo-section">
                    <div class="logo-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="12" y="20" width="40" height="32" rx="4" fill="white" opacity="0.9"/>
                            <circle cx="32" cy="36" r="8" fill="white" opacity="0.5"/>
                            <path d="M20 16C20 14.8954 20.8954 14 22 14H42C43.1046 14 44 14.8954 44 16V20H20V16Z" fill="white"/>
                        </svg>
                    </div>
                    <h2>E-LAUNDRY</h2>
                </div>

                <div class="login-form-wrapper">
                    <h3>Login untuk akunmu</h3>

                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <form action="../../process/auth_handler.php" method="POST" class="login-form">
                        <input type="hidden" name="action" value="login">
                        
                        <div class="form-group">
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                placeholder="Username"
                                required 
                                autofocus
                            >
                        </div>

                        <div class="form-group">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="Password"
                                required
                            >
                        </div>

                        <button type="submit" class="btn-login">LOGIN</button>
                    </form>

                    <div class="login-footer">
                        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                        <p><a href="../public/index.php">Kembali ke Beranda</a></p>
                    </div>
                </div>

                <div class="copyright">
                    <p>&copy; Kelompok 10 2025 All Rights Reserved</p>
                </div>
            </div>

            <div class="login-right">
                <div class="image-overlay">
                    <img src="../../assets/img/laundry.png" alt="Laundry Illustration" class="laundry-image">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
