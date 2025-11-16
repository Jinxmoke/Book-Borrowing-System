<?php
session_start();
include('../config/connect.php');

// Fetch pending requests from the database
$sql = "SELECT * FROM pending_requests WHERE status = 'pending'";
$result = $conn->query($sql);

// Array to hold the requests
$pending_requests = [];

while ($row = $result->fetch_assoc()) {
    $book_id = $row['book_id'];
    $title = $row['title'];
    $member_id = $row['member_id'];

    // Fetch member's name from user_info
    $member_sql = "SELECT name FROM user_info WHERE member_id = ?";
    $stmt = $conn->prepare($member_sql);
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $member_result = $stmt->get_result();
    $member_row = $member_result->fetch_assoc();
    $member_name = $member_row['name'];
    $stmt->close();

    $pending_requests[] = [
        'id' => $row['id'],
        'title' => $title,
        'member_name' => $member_name,
        'request_date' => $row['request_date'],
        'book_id' => $book_id,
        'member_id' => $member_id
    ];
}

echo json_encode($pending_requests);

$conn->close();
?>
