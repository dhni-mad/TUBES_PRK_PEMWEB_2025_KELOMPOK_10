<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'laundry_system');

function getConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    
    return $conn;
}

function closeConnection($conn) {
    if ($conn) {
        mysqli_close($conn);
    }
}

function executeQuery($conn, $query) {
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Query Error: " . mysqli_error($conn));
        return false;
    }
    
    return $result;
}

function fetchData($conn, $query) {
    $result = executeQuery($conn, $query);
    
    if (!$result) {
        return [];
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    mysqli_free_result($result);
    return $data;
}

function fetchSingle($conn, $query) {
    $result = executeQuery($conn, $query);
    
    if (!$result) {
        return null;
    }
    
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    
    return $row;
}

function escapeString($conn, $string) {
    return mysqli_real_escape_string($conn, $string);
}

$conn = getConnection();
?>
