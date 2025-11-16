<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: login_form.php");
    exit();
}

include('../config/connect.php');

$member_id = $_SESSION['member_id'];
$book_id = $_POST['book_id'];

if (!isset($book_id) || empty($book_id)) {
    $message = "Book ID is missing.";
    header("Location: user_descriptionForm.php?book_id=$book_id&message=" . urlencode($message));
    exit();
}

// Check if the book exists in the manage_books table
$sql_check_book = "SELECT book_id FROM manage_books WHERE book_id = ?";
$stmt_check_book = $conn->prepare($sql_check_book);
$stmt_check_book->bind_param('i', $book_id);
$stmt_check_book->execute();
$result_check_book = $stmt_check_book->get_result();

if ($result_check_book->num_rows == 0) {
    $message = "The selected book does not exist in the library.";
    header("Location: user_descriptionForm.php?book_id=$book_id&message=" . urlencode($message));
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
        $message = "Book bookmarked successfully.";
    } else {
        $message = "Bookmark removed successfully.";
    }
} else {
    $message = "Error adding/removing bookmark: " . $stmt_insert_update->error;
}

$stmt_insert_update->close();

header("Location: user_descriptionForm.php?book_id=$book_id&message=" . urlencode($message));
exit();
?>
