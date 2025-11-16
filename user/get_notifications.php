<?php
session_start();
include('../config/connect.php');

if (!isset($_SESSION['member_id'])) {
    echo json_encode([]);
    exit;
}

$member_id = $_SESSION['member_id'];

// Select notifications where the status is not 'read'
$sql = "SELECT * FROM user_notification WHERE member_id = ? AND status != 'read' ORDER BY created_at DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        'member_id' => $row['member_id'],
        'message' => $row['message'],
        'type' => $row['type'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode($notifications);
?>
