<?php
// Database configuration
include('../config/connect.php');

// Get the current date and time
$currentDateTime = date('Y-m-d H:i:s');

// Query to fetch overdue books from borrowed_books table
$overdueBooksQuery = "
    SELECT book_id, title, member_id, due_date
    FROM borrowed_books
    WHERE due_date < ? AND status != 'returned';
";

// Prepare the query
$stmt = $conn->prepare($overdueBooksQuery);
$stmt->bind_param('s', $currentDateTime);
$stmt->execute();
$result = $stmt->get_result();

$insertedNotifications = 0;

if ($result->num_rows > 0) {
    // Insert notifications for each overdue book
    $insertNotificationQuery = "
        INSERT INTO notifications (title, time, member_id, is_unread)
        VALUES (?, NOW(), ?, 1);
    ";

    $insertStmt = $conn->prepare($insertNotificationQuery);

    while ($row = $result->fetch_assoc()) {
        $title = "Overdue Book: " . $row['title'];
        $memberId = $row['member_id'];

        // Insert notification
        $insertStmt->bind_param('si', $title, $memberId);
        if ($insertStmt->execute()) {
            $insertedNotifications++;
        }
    }

    $insertStmt->close();
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Respond with the result
echo json_encode([
    'status' => 'success',
    'inserted' => $insertedNotifications,
    'message' => "$insertedNotifications notifications added."
]);
