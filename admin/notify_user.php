<?php
require 'C:\xampp\htdocs\e-book\vendor\autoload.php';

$pusher = new Pusher\Pusher(
    'd634f1649c151fec12e6',
    'ebe82ec59b8b84901f8c',
    '1897855',
    array(
        'cluster' => 'eu',
        'useTLS' => true
    )
);

if (isset($_POST['action']) && $_POST['action'] == 'notify') {
    $bookId = $_POST['book_id'];

    // Fetch book and user details from the database
    // This is a placeholder. Replace with your actual database query
    $bookDetails = fetchBookDetails($bookId);

    if ($bookDetails) {
        $dateTime = new DateTime();

        $data = [
            'book_id' => $bookId,
            'message' => 'An overdue book notification has been sent for ' . $bookDetails['title'],
            'timestamp' => $dateTime->format('Y-m-d H:i:s')
        ];

        // Trigger to the specific user's channel
        $event = 'new-notification';
        $channel = 'user-' . $bookDetails['member_id'];

        $pusher->trigger($channel, $event, $data);

        echo json_encode(['success' => true, 'message' => 'Notification sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
    }
    exit;
}

function fetchBookDetails($bookId) {
    // Implement your database query here
    // Return an array with book details and member_id
    // Example:
    // return ['title' => 'Book Title', 'member_id' => 123];
}
