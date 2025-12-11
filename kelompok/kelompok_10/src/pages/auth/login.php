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
    <title>Login - Zira Laundry</title>
    <link rel="stylesheet" href="../../assets/css/auth.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-left">
                <div class="logo-section">
                    <div class="logo-icon">
                        <img src="../../assets/img/Zira_Laundry.jpg" alt="Zira Laundry" style="width: 80px; height: 80px; object-fit: contain; border-radius: 10px;">
                    </div>
                    <h2>ZIRA LAUNDRY</h2>
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
