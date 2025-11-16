<?php
// get_notifications.php
include('../config/connect.php');
$sql = "SELECT id, title, time, is_unread, member_id FROM notifications WHERE is_unread = 1 ORDER BY time DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    echo json_encode($notifications);
} else {
    echo json_encode(['error' => 'Failed to retrieve notifications']);
}
mysqli_close($conn);
?>
