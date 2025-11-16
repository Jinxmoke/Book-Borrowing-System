<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "bookborrowing";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . " - Error Code: " . mysqli_connect_errno());
}


// $host = "localhost";
// $username = "oubomnof_book_borrowing_db";
// $password = "michaeljamesochea1234567890";
// $database = "oubomnof_library_db";

// $conn = mysqli_connect($host, $username, $password, $database);

// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error() . " - Error Code: " . mysqli_connect_errno());
// }

?>
