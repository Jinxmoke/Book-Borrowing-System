<?php
include('../config/connect.php');

if (isset($_GET['book_id'])) {
    $bookId = intval($_GET['book_id']);
    $query = "SELECT * FROM manage_books WHERE book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $bookId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        echo json_encode($book);
    } else {
        echo json_encode(['error' => 'Book not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
