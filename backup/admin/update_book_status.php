<?php
include('../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the book_id sent via POST
    $bookId = intval($_POST['book_id']);

    // Start a transaction to ensure both operations are executed together
    $conn->begin_transaction();

    try {
        // 1. Update the status of the book in the manage_books table
        $sql_update = "UPDATE manage_books SET status = 'available' WHERE book_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $bookId);
        $stmt_update->execute();

        if ($stmt_update->affected_rows === 0) {
            throw new Exception("No rows updated in the manage_books table.");
        }

        // 2. Delete the book from the condemned_books table
        $sql_delete = "DELETE FROM condemned_books WHERE book_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $bookId);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows === 0) {
            throw new Exception("No rows deleted from the condemned_books table.");
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to the condemned books page after successful update
        header("Location: admin_condemnedBooks.php?message=success");

    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        // Redirect with error message
        header("Location: admin_condemnedBooks.php?message=error");
    } finally {
        // Close prepared statements and connection
        $stmt_update->close();
        $stmt_delete->close();
        $conn->close();
    }
}
?>
