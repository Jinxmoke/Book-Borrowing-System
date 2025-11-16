<?php
include('../config/connect.php');

if (isset($_GET['book_id'])) {
    $booking_id = $_GET['book_id'];
    echo "$booking_id";

    $sql = "SELECT book_id from manage_books
            WHERE book_id = $booking_id;";

    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $status_id = $row['status_id'];

        $sql = "DELETE FROM manage_books WHERE book_id = $booking_id";
        $result = mysqli_query($conn, $sql);
    }

  }

header("Location: admin_catalogForm.php");
exit();
?>
