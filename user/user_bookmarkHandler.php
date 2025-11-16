<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['member_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to bookmark books'
    ]);
    exit();
}

include('../config/connect.php');
$member_id = $_SESSION['member_id'];

// Check if book_id is provided
if (!isset($_POST['book_id']) || empty($_POST['book_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Book ID is missing'
    ]);
    exit();
}

$book_id = $_POST['book_id'];

// Check if the book exists in the manage_books table
$sql_check_book = "SELECT book_id FROM manage_books WHERE book_id = ?";
$stmt_check_book = $conn->prepare($sql_check_book);
$stmt_check_book->bind_param('i', $book_id);
$stmt_check_book->execute();
$result_check_book = $stmt_check_book->get_result();

if ($result_check_book->num_rows == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'The selected book does not exist in the library'
    ]);
    exit();
}

// Insert or update the bookmark
$sql_insert_update = "INSERT INTO bookmarks (member_id, book_id)
                      VALUES (?, ?)
                      ON DUPLICATE KEY UPDATE book_id = NULL";
$stmt_insert_update = $conn->prepare($sql_insert_update);
$stmt_insert_update->bind_param('ii', $member_id, $book_id);

if ($stmt_insert_update->execute()) {
    if ($stmt_insert_update->affected_rows == 1) {
        echo json_encode([
            'success' => true,
            'message' => 'Book bookmarked successfully'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Bookmark removed successfully'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error adding/removing bookmark: ' . $stmt_insert_update->error
    ]);
}

$stmt_insert_update->close();
$conn->close();
?>
