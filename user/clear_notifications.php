<?php
session_start();
include('../config/connect.php');

if (!isset($_SESSION['member_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$member_id = $_SESSION['member_id'];

// Update the notification status to 'read'
$sql = "UPDATE user_notification SET status = 'read' WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$success = $stmt->execute();

echo json_encode(['success' => $success]);
?>
