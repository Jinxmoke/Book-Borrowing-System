<?php
session_start();
header('Content-Type: application/json');

require '../pusher/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $book_id = $data['book_id'];
    $member_id = $_SESSION['member_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "library_db";
    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $connection->connect_error]);
        exit;
    }

    // Get the book details from borrowed_books
    $query = "SELECT * FROM borrowed_books WHERE book_id = ? AND member_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ii", $book_id, $member_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $bookData = $result->fetch_assoc();

        // Check the source to determine which return process to use
        if ($bookData['source'] === 'lender_books') {
            // if The book is from lender_books, insert into lender_returned
            $insertQuery = "INSERT INTO lender_returned (book_id, title, member_id, name, borrow_date, return_date, lender_id)
                           VALUES (?, ?, ?, ?, ?, NOW(), ?)";
            $insertStmt = $connection->prepare($insertQuery);
            $insertStmt->bind_param("issssi",
                $bookData['book_id'],
                $bookData['title'],
                $bookData['member_id'],
                $bookData['name'],
                $bookData['borrow_date'],
                $bookData['lender_id']
            );

            if ($insertStmt->execute()) {
                // Update the status in lender_books to available
                $updateQuery = "UPDATE lender_books SET status = 'available' WHERE book_id = ?";
                $updateStmt = $connection->prepare($updateQuery);
                $updateStmt->bind_param("i", $book_id);
                $updateStmt->execute();

                // Delete from borrowed_books
                $deleteQuery = "DELETE FROM borrowed_books WHERE book_id = ? AND member_id = ?";
                $deleteStmt = $connection->prepare($deleteQuery);
                $deleteStmt->bind_param("ii", $book_id, $member_id);

                if ($deleteStmt->execute()) {
                    // Notify the lender using Pusher
                    $pusher = new Pusher\Pusher(
                        'd634f1649c151fec12e6',
                        'ebe82ec59b8b84901f8c',
                        '1897855',
                        [
                            'cluster' => 'eu',
                            'useTLS' => true
                        ]
                    );

                    // Trigger a Pusher event to notify the lender
                    $pusher->trigger('lender-channel', 'book-returned', [
                        'message' => "A book has been returned by member with ID: " . $member_id,
                        'book_title' => $bookData['title'],
                        'member_id' => $member_id,
                        'return_time' => date('Y-m-d H:i:s')
                    ]);

                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete from borrowed_books: ' . $deleteStmt->error]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to insert into lender_returned: ' . $insertStmt->error]);
            }
        } else {
            // If the book is from manage_books, insert into returned_books
            $insertQuery = "INSERT INTO returned_books (book_id, title, member_id, name, borrow_date, return_date)
                           VALUES (?, ?, ?, ?, ?, NOW())";
            $insertStmt = $connection->prepare($insertQuery);
            $insertStmt->bind_param("issss",
                $bookData['book_id'],
                $bookData['title'],
                $bookData['member_id'],
                $bookData['name'],
                $bookData['borrow_date']
            );

            if ($insertStmt->execute()) {
                // Update the status in manage_books to available
                $updateQuery = "UPDATE manage_books SET status = 'available' WHERE book_id = ?";
                $updateStmt = $connection->prepare($updateQuery);
                $updateStmt->bind_param("i", $book_id);
                $updateStmt->execute();

                // Delete from borrowed_books
                $deleteQuery = "DELETE FROM borrowed_books WHERE book_id = ? AND member_id = ?";
                $deleteStmt = $connection->prepare($deleteQuery);
                $deleteStmt->bind_param("ii", $book_id, $member_id);

                if ($deleteStmt->execute()) {
                    // Notify the admin using Pusher
                    $pusher = new Pusher\Pusher(
                      'd634f1649c151fec12e6',
                      'ebe82ec59b8b84901f8c',
                      '1897855',
                      [
                          'cluster' => 'eu',
                          'useTLS' => true
                      ]
                    );

                    // Trigger a Pusher event to notify the admin
                    $pusher->trigger('admin-channel', 'book-returned', [
                        'message' => "A book has been returned by member with ID: " . $member_id,
                        'book_title' => $bookData['title'],
                        'member_id' => $member_id,
                        'return_time' => date('Y-m-d H:i:s')
                    ]);

                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete from borrowed_books: ' . $deleteStmt->error]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to insert into returned_books: ' . $insertStmt->error]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Book not found in borrowed_books']);
    }

    $stmt->close();
    if (isset($insertStmt)) $insertStmt->close();
    if (isset($updateStmt)) $updateStmt->close();
    if (isset($deleteStmt)) $deleteStmt->close();
    $connection->close();
}
?>
