<?php
include('../config/connect.php');
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM lender_books WHERE book_id = '$book_id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $book = $result->fetch_assoc();
        echo json_encode($book);
    } else {
        echo json_encode(['error' => 'Book not found']);
    }
} else {
    echo json_encode(['error' => 'No book ID provided']);
}
?>
