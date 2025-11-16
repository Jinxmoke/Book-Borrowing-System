<?php
session_start();
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    die("Error: Name is not provided or session name is empty.");
}
$name = $_SESSION['name'];
$member_id = $_SESSION['member_id'];

include('../config/connect.php');
require '../pusher/vendor/autoload.php';

// Pusher Configuration
$pusher = new Pusher\Pusher(
    'd634f1649c151fec12e6', // App Key
    'ebe82ec59b8b84901f8c', // App Secret
    '1897855',              // App ID
    array('cluster' => 'eu') // Cluster
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];

    // Validate Book Existence
    $source_sql = "SELECT book_type FROM manage_books WHERE book_id = ?";
    $source_stmt = $conn->prepare($source_sql);
    $source_stmt->bind_param("i", $book_id);
    $source_stmt->execute();
    $source_result = $source_stmt->get_result();
    $source_row = $source_result->fetch_assoc();
    $source_stmt->close();

    if (!$source_row) {
        die("Error: Book not found in the system.");
    }

    $book_type = $source_row['book_type'];

    // Limit User's Pending Requests
    $count_sql = "SELECT COUNT(*) AS request_count FROM pending_requests WHERE member_id = ? AND status = 'pending'";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $member_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $count_stmt->close();

    if ($count_row['request_count'] >= 3) {
        header("Location: user_catalogForm.php?message=You can only have up to 3 pending requests at a time.");
        exit();
    }

    // Insert Pending Request
    $conn->begin_transaction();
    try {
        $insert_sql = "INSERT INTO pending_requests (book_id, title, member_id, status, request_date)
                       VALUES (?, ?, ?, 'pending', NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("isi", $book_id, $title, $member_id);
        $insert_stmt->execute();

        // Generate Token for eBooks
        if ($book_type === 'ebook') {
            $token = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime("+7 days")); // Token valid for 7 days

            $token_sql = "INSERT INTO tokens (member_id, book_id, token, expiry) VALUES (?, ?, ?, ?)";
            $token_stmt = $conn->prepare($token_sql);
            $token_stmt->bind_param("iiss", $member_id, $book_id, $token, $expiry);
            $token_stmt->execute();
            $token_stmt->close();
        }

        $insert_stmt->close();
        $conn->commit();

        // Notify Admin via Pusher
        $adminData = [
            'message' => 'A user has requested a book.',
            'user_name' => $name,
            'book_title' => $title,
            'book_id' => $book_id,
            'member_id' => $member_id
        ];
        $pusher->trigger('admin-channel', 'book-borrowed', $adminData);

        // Notify User via Pusher
        $userData = [
            'message' => 'You have successfully requested the book.',
            'book_title' => $title,
            'book_id' => $book_id
        ];
        $pusher->trigger('user-channel', 'new-notification', $userData);

        // Redirect with Success Message
        header("Location: user_catalogForm.php?message=Book request successfully");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

$conn->close();
?>
