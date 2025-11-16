<?php
include('../config/connect.php');

// Get the current date
$current_date = date('Y-m-d');

// Query to select overdue books with borrower's name, book title, and borrow date
$sql = "
    SELECT bb.book_id, bb.due_date, bb.book_type, bb.member_id, bb.borrow_date, m.name AS name, b.title AS title
    FROM borrowed_books bb
    JOIN user_info m ON bb.member_id = m.member_id
    JOIN manage_books b ON bb.book_id = b.book_id
    WHERE bb.status = 'borrowed' AND bb.due_date < ? AND bb.book_type = 'ebook'
";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_date);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are overdue books with book_type as 'ebook'
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $book_id = $row['book_id'];
        $book_type = $row['book_type'];
        $due_date = $row['due_date'];
        $member_id = $row['member_id'];
        $borrow_date = $row['borrow_date'];
        $name = $row['name'];
        $title = $row['title'];

        // Insert the overdue ebook into the returned_books table
        $insert_sql = "
            INSERT INTO returned_books (book_id, member_id, return_date, name, title, borrow_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iissss", $book_id, $member_id, $current_date, $name, $title, $borrow_date);
        $insert_stmt->execute();

        // Delete the overdue ebook from borrowed_books
        $delete_sql = "DELETE FROM borrowed_books WHERE book_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $book_id);
        $delete_stmt->execute();

        // Update the ebook status in manage_books to 'available'
        $update_book_sql = "UPDATE manage_books SET status = 'available' WHERE book_id = ?";
        $update_book_stmt = $conn->prepare($update_book_sql);
        $update_book_stmt->bind_param("i", $book_id);
        $update_book_stmt->execute();
    }

    echo "Overdue e-books status updated.";
} else {
    echo "No overdue e-books found.";
}

$conn->close();
?>