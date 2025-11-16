<?php
include('../config/connect.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : null;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';
    $publication_date = isset($_POST['publication_date']) ? trim($_POST['publication_date']) : '';
    $publisher = isset($_POST['publisher']) ? trim($_POST['publisher']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Check if book_id is valid
    if (empty($book_id)) {
        $response['message'] = 'Invalid or missing book ID';
    } else {
        // Confirm if the book exists in lender_books
        $check_sql = "SELECT book_id FROM lender_books WHERE book_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Proceed with update if the book exists
            $sql = "UPDATE lender_books SET
                    title = ?,
                    author = ?,
                    genre = ?,
                    publication_date = ?,
                    publisher = ?,
                    status = ?,
                    quantity = ?,
                    description = ?
                    WHERE book_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssi", $title, $author, $genre, $publication_date, $publisher, $status, $quantity, $description, $book_id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Book updated successfully';
            } else {
                $response['message'] = 'Error updating book: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Book with specified ID not found in lender_books';
        }
        $check_stmt->close();
    }
} else {
    $response['message'] = 'Invalid request method';
}

$conn->close();
echo json_encode($response);
?>
