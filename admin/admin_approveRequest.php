<?php
session_start();
include('../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'approve') {
    $id = $_POST['id'];
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $member_id = $_POST['member_id'];

    // Check if the book is available in the manage_books table
    $availability_sql = "SELECT status FROM manage_books WHERE book_id = ?";
    $stmt = $conn->prepare($availability_sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $availability_result = $stmt->get_result();
    $availability_row = $availability_result->fetch_assoc();
    $stmt->close();

    if (!$availability_row || $availability_row['status'] != 'available') {
        // Book is not available, redirect with a message
        header("Location: admin_pendingRequest?message=The book is not available for borrowing.");
        exit();
    }

    // Fetch the member's name from the user_info table
    $member_sql = "SELECT name FROM user_info WHERE member_id = ?";
    $stmt = $conn->prepare($member_sql);
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $member_result = $stmt->get_result();
    $member_row = $member_result->fetch_assoc();
    $name = $member_row['name'];
    $stmt->close();

    // Check if the user has already borrowed the specific book
    $check_sql = "SELECT * FROM borrowed_books WHERE book_id = ? AND member_id = ? AND status = 'borrowed'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $book_id, $member_id);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Book is already borrowed by the user, handle accordingly
        header("Location: admin_returnedBooksForm?message=This book is already borrowed by the user.");
        exit();
    }

    // Fetch expiry_days and book_type from manage_books table
    $book_sql = "SELECT expiry_days, book_type FROM manage_books WHERE book_id = ?";
    $stmt = $conn->prepare($book_sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book_result = $stmt->get_result();
    $book_row = $book_result->fetch_assoc();
    $expiry_days = $book_row['expiry_days'];
    $book_type = $book_row['book_type'];
    $stmt->close();

    // Calculate the due date based on expiry_days
    $due_date = date('Y-m-d', strtotime("+$expiry_days days"));

    // Insert the approved request into borrowed_books
    $sql = "INSERT INTO borrowed_books (book_id, title, member_id, name, status, borrow_date, due_date, book_type, expiry_days)
            VALUES (?, ?, ?, ?, 'borrowed', NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isissss", $book_id, $title, $member_id, $name, $due_date, $book_type, $expiry_days);
    $stmt->execute();

    // Update the book status to borrowed in the manage_books table
    $update_book_sql = "UPDATE manage_books SET status = 'borrowed' WHERE book_id = ?";
    $update_book_stmt = $conn->prepare($update_book_sql);
    $update_book_stmt->bind_param("i", $book_id);
    $update_book_stmt->execute();

    // If the book is an ebook, check if it's overdue and update the status to available
    if ($book_type == 'ebook') {
        $update_status_sql = "UPDATE manage_books mb
                              JOIN borrowed_books bb ON mb.book_id = bb.book_id
                              SET mb.status = 'available'
                              WHERE bb.due_date < NOW() AND bb.status = 'borrowed' AND mb.book_id = ?";
        $update_status_stmt = $conn->prepare($update_status_sql);
        $update_status_stmt->bind_param("i", $book_id);
        $update_status_stmt->execute();
    }

    // Delete the request from pending_requests after approval
    $delete_sql = "DELETE FROM pending_requests WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);
    $delete_stmt->execute();

    header("Location: admin_pendingRequest?message=Request approved, book borrowed, and request removed.");
    exit();
}

$conn->close();
?>
