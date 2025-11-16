<?php
include('../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = intval($_POST['book_id']);
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];
    $bookType = $_POST['book_type'];
    $publicationDate = $_POST['publication_date'];
    $publisher = $_POST['publisher'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $expiryDays = intval($_POST['expiry_days']);

    // Prepare the query
    $query = "UPDATE manage_books SET
                title = ?,
                author = ?,
                genre = ?,
                isbn = ?,
                book_type = ?,
                publication_date = ?,
                publisher = ?,
                status = ?,
                description = ?,
                expiry_days = ?
              WHERE book_id = ?";

    // Prepare parameters
    $paramTypes = 'sssssssssii';
    $params = [
        $title, $author, $genre, $isbn, $bookType,
        $publicationDate, $publisher, $status,
        $description, $expiryDays, $bookId
    ];

    // Prepare and execute statement
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Dynamically bind parameters
        $stmt->bind_param($paramTypes, ...$params);

        if ($stmt->execute()) {
            header('Location: admin_catalogForm.php?success=Book updated successfully');
        } else {
            header('Location: admin_catalogForm.php?error=Failed to update book');
        }
    } else {
        header('Location: admin_catalogForm.php?error=Failed to prepare statement');
    }
    exit();
} else {
    header('Location: admin_catalogForm.php?error=Invalid request');
    exit();
}
?>
