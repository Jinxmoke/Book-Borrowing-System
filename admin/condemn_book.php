<?php
include('../config/connect.php');

if (isset($_POST['book_id']) && isset($_POST['reason'])) {
    $book_id = intval($_POST['book_id']);
    $condemn_reason = trim($_POST['reason']);

    // Fetch the book details from `manage_books`
    $sqlFetch = "SELECT * FROM manage_books WHERE book_id = ?";
    $stmtFetch = $conn->prepare($sqlFetch);
    $stmtFetch->bind_param("i", $book_id);
    $stmtFetch->execute();
    $result = $stmtFetch->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();

        // Insert book details into `condemned_books`
        $sqlInsert = "INSERT INTO condemned_books (book_id, condemn_reason, title, genre, isbn, description, condemned_date)
                      VALUES (?, ?, ?, ?, ?, ?, NOW())";  // Use NOW() for the current timestamp
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param(
            "isssss",
            $book['book_id'],
            $condemn_reason,
            $book['title'],
            $book['genre'],
            $book['isbn'],
            $book['description']
        );

        if ($stmtInsert->execute()) {
            // Update the `status` of the book in `manage_books` to 'condemned'
            $sqlUpdateStatus = "UPDATE manage_books SET status = 'condemned' WHERE book_id = ?";
            $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
            $stmtUpdateStatus->bind_param("i", $book_id);
            $stmtUpdateStatus->execute();

            echo "Book successfully condemned and added to the condemned books list.";
        } else {
            echo "Failed to add the book to the condemned books list.";
        }

        $stmtInsert->close();
        $stmtUpdateStatus->close();
    } else {
        echo "Book not found.";
    }

    $stmtFetch->close();
} else {
    echo "Invalid request.";
}

$conn->close();
