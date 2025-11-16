<?php
// return_book.php
session_start();
include('../config/connect.php');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receive book_id from the AJAX request
    $book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
    $conn->begin_transaction();

    try {
        // Fetch the book borrowing details from borrowed_books table
        $fetch_query = "SELECT book_id, title, member_id, borrow_date, name FROM borrowed_books WHERE book_id = ?";
        $fetch_stmt = $conn->prepare($fetch_query);
        $fetch_stmt->bind_param("i", $book_id);
        $fetch_stmt->execute();
        $result = $fetch_stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Book not found in borrowed books.");
        }

        // Get the borrowed book details
        $book_details = $result->fetch_assoc();

        //Insert the book details into returned_books table
        $insert_query = "INSERT INTO returned_books (
            book_id,
            title,
            member_id,
            borrow_date,
            return_date,
            name
        ) VALUES (?, ?, ?, ?, CURRENT_DATE, ?)";

        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param(
            "isiss",
            $book_details['book_id'],
            $book_details['title'],
            $book_details['member_id'],
            $book_details['borrow_date'],
            $book_details['name']
        );
        $insert_stmt->execute();

        //Delete the record from borrowed_books table
        $delete_query = "DELETE FROM borrowed_books WHERE book_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $book_id);
        $delete_stmt->execute();

        //Update book status in manage_books table
        $update_query = "UPDATE manage_books SET status = 'available' WHERE book_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $book_id);
        $update_stmt->execute();

        $conn->commit();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Book returned successfully!'
        ]);

    } catch (Exception $e) {

        $conn->rollback();

        // Return error response
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    // Close statements and connection
    $fetch_stmt->close();
    $insert_stmt->close();
    $delete_stmt->close();
    $update_stmt->close();
    $conn->close();

    exit();
}
?>
