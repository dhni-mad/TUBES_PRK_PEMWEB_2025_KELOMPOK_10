<?php
session_start();
require_once '../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'login') {
        handleLogin();
    } elseif ($action === 'register') {
        handleRegister();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'logout') {
        handleLogout();
    }
}
function handleLogin() {
    global $conn;
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
        header('Location: ../pages/auth/login.php');
        exit();
    }
    $username = mysqli_real_escape_string($conn, $username);
    $query_staff = "SELECT id, username, password, full_name, role, is_active 
                    FROM users 
                    WHERE username = '$username' 
                    LIMIT 1";
    $result_staff = mysqli_query($conn, $query_staff);
    if (!$result_staff) {
        $_SESSION['error'] = 'Terjadi kesalahan sistem!';
        header('Location: ../pages/auth/login.php');
        exit();
    }
    $user = mysqli_fetch_assoc($result_staff);
    if ($user) {
        if (!$user['is_active']) {
            $_SESSION['error'] = 'Akun Anda tidak aktif. Hubungi administrator!';
            header('Location: ../pages/auth/login.php');
            exit();
        }
        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Username atau password salah!';
            header('Location: ../pages/auth/login.php');
            exit();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_type'] = 'staff';
        $_SESSION['login_time'] = time();
        $role = $user['role'];
        if ($role === 'admin') {
            header('Location: ../pages/admin/dashboard.php');
        } elseif ($role === 'kasir') {
            header('Location: ../pages/cashier/new_transaction.php');
        } elseif ($role === 'worker') {
            header('Location: ../pages/worker/task_list.php');
        }
        exit();
    }
    $query_customer = "SELECT id, username, password, full_name, email, no_hp, alamat, is_active 
                       FROM customers 
                       WHERE username = '$username' 
                       LIMIT 1";
    $result_customer = mysqli_query($conn, $query_customer);
    if ($result_customer) {
        $customer = mysqli_fetch_assoc($result_customer);
        if ($customer) {
            if (!$customer['is_active']) {
                $_SESSION['error'] = 'Akun Anda tidak aktif!';
                header('Location: ../pages/auth/login.php');
                exit();
            }
            if (!password_verify($password, $customer['password'])) {
                $_SESSION['error'] = 'Username atau password salah!';
                header('Location: ../pages/auth/login.php');
                exit();
            }
            $_SESSION['user_id'] = $customer['id'];
            $_SESSION['username'] = $customer['username'];
            $_SESSION['full_name'] = $customer['full_name'];
            $_SESSION['email'] = $customer['email'];
            $_SESSION['no_hp'] = $customer['no_hp'];
            $_SESSION['alamat'] = $customer['alamat'];
            $_SESSION['user_type'] = 'customer';
            $_SESSION['login_time'] = time();
            header('Location: ../pages/public/index.php');
            exit();
        }
    }
    $_SESSION['error'] = 'Username atau password salah!';
    header('Location: ../pages/auth/login.php');
    exit();
}
function handleRegister() {
    $_SESSION['error'] = 'Fitur registrasi tidak tersedia. Silakan hubungi administrator untuk membuat akun.';
    header('Location: ../pages/auth/login.php');
    exit();
}
function handleLogout() {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['success'] = 'Anda berhasil logout!';
    header('Location: ../pages/auth/login.php');
    exit();
}
?>
