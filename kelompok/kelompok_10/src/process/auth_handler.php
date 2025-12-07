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

    $_SESSION['error'] = 'Username atau password salah!';
    header('Location: ../pages/auth/login.php');
    exit();
}

function handleRegister() {
    global $conn;
    
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($full_name) || empty($username) || empty($email) || empty($no_hp) || empty($alamat) || empty($password)) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
        header('Location: ../pages/auth/register.php');
        exit();
    }

    if ($password !== $password_confirm) {
        $_SESSION['error'] = 'Password dan konfirmasi password tidak sama!';
        header('Location: ../pages/auth/register.php');
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password minimal 6 karakter!';
        header('Location: ../pages/auth/register.php');
        exit();
    }

    $username = mysqli_real_escape_string($conn, $username);
    
    $check_staff = "SELECT id FROM users WHERE username = '$username' LIMIT 1";
    $result_staff = mysqli_query($conn, $check_staff);
    
    $check_customer = "SELECT id FROM customers WHERE username = '$username' LIMIT 1";
    $result_customer = mysqli_query($conn, $check_customer);
    
    if (mysqli_num_rows($result_staff) > 0 || mysqli_num_rows($result_customer) > 0) {
        $_SESSION['error'] = 'Username sudah digunakan!';
        header('Location: ../pages/auth/register.php');
        exit();
    }

    $full_name = mysqli_real_escape_string($conn, $full_name);
    $email = mysqli_real_escape_string($conn, $email);
    $no_hp = mysqli_real_escape_string($conn, $no_hp);
    $alamat = mysqli_real_escape_string($conn, $alamat);
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $insert_query = "INSERT INTO customers (username, password, full_name, email, no_hp, alamat, is_active) 
                     VALUES ('$username', '$password_hash', '$full_name', '$email', '$no_hp', '$alamat', TRUE)";
    
    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['success'] = 'Registrasi berhasil! Silakan login dengan akun Anda.';
        header('Location: ../pages/auth/login.php');
    } else {
        $_SESSION['error'] = 'Terjadi kesalahan saat registrasi!';
        header('Location: ../pages/auth/register.php');
    }
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
