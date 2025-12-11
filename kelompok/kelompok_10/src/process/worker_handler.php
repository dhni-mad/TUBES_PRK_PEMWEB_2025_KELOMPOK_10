<?php
require_once __DIR__ . '/../config/database.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'update_status' 
        && isset($_POST['transaction_id']) 
        && isset($_POST['next_status'])
        && isset($_POST['worker_id'])
        && isset($_POST['status_before'])
    ) {
        $transaction_id = escapeString($conn, $_POST['transaction_id']);
        $next_status = escapeString($conn, $_POST['next_status']);
        $worker_id = (int)$_POST['worker_id'];
        $status_before = escapeString($conn, $_POST['status_before']);
        $catatan = "Status diperbarui oleh Petugas Cuci.";
        $update_query = "
            UPDATE 
                transactions 
            SET 
                status_laundry = '$next_status',
                tgl_selesai = IF('$next_status' = 'Done', NOW(), tgl_selesai)
            WHERE 
                id = '$transaction_id'
        ";
        $update_success = executeQuery($conn, $update_query);
        if ($update_success) {
            $log_query = "
                INSERT INTO 
                    status_logs (transaction_id, status_before, status_after, changed_by, catatan)
                VALUES 
                    ('$transaction_id', '$status_before', '$next_status', $worker_id, '$catatan')
            ";
            $log_success = executeQuery($conn, $log_query);
            if ($log_success) {
                closeConnection($conn);
                header("Location: ../pages/worker/task_list.php?success=status_updated");
                exit();
            } else {
                closeConnection($conn);
                header("Location: ../pages/worker/task_list.php?error=log_failed");
                exit();
            }
        } else {
            closeConnection($conn);
            header("Location: ../pages/worker/task_list.php?error=update_failed");
            exit();
        }
    } else {
        closeConnection($conn);
        header("Location: ../pages/worker/task_list.php?error=invalid_action");
        exit();
    }
} else {
    closeConnection($conn);
    header("Location: ../pages/worker/task_list.php");
    exit();
}
?>