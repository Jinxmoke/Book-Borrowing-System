<?php
// mark_as_read.php
include('../config/connect.php');

$notification_id = $_POST['notification_id'] ?? 0;

$sql = "UPDATE notifications SET is_unread = 0 WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $notification_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => mysqli_error($conn)
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
