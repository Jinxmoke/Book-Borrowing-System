<?php
include('../config/connect.php');

// Receive notification data
$title = $_POST['title'] ?? '';
$member_id = $_POST['member_id'] ?? 0;
$notification_id = $_POST['notification_id'] ?? 0;

// Get current timestamp and format it for MySQL
$time = date('Y-m-d H:i:s');  // Format: YYYY-MM-DD HH:MM:SS

// Prepare SQL to insert notification
$sql = "INSERT INTO notifications (title, time, member_id, is_unread) VALUES (?, ?, ?, 1)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $title, $time, $member_id);

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
