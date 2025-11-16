<?php
session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: /e-book/login_form.php");
    exit;
}

include('../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $member_id = $_SESSION['member_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Check if the user has borrowed and returned the book, and hasn't already reviewed it
    $check_query = "
        SELECT 1
        FROM returned_books
        WHERE book_id = ? AND member_id = ?
        AND NOT EXISTS (
            SELECT 1 FROM book_comments
            WHERE book_id = returned_books.book_id AND member_id = returned_books.member_id
        )
        LIMIT 1
    ";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $book_id, $member_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        $_SESSION['error'] = "You can only review books you have borrowed and returned, and you haven't already reviewed.";
        header("Location: user_descriptionForm.php?book_id=" . $book_id);
        exit;
    }

    // Insert the review
    $insert_query = "
        INSERT INTO book_comments (book_id, member_id, rating, comment)
        VALUES (?, ?, ?, ?)
    ";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("iiis", $book_id, $member_id, $rating, $comment);

    if ($insert_stmt->execute()) {
        $_SESSION['success'] = "Your review has been submitted successfully.";
    } else {
        $_SESSION['error'] = "There was an error submitting your review. Please try again.";
    }

    header("Location: user_descriptionForm?book_id=" . $book_id);
    exit;
}
