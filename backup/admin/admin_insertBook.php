<?php
session_start();
include('../config/connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $book_type = $_POST['book_type'];
    $publication_date = $_POST['publication_date'];
    $publisher = $_POST['publisher'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $expiry_days = $_POST['expiry_days'];
    $isbn = $_POST['isbn'];

    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_dir = '../uploads/';

    $image_extension = pathinfo($image, PATHINFO_EXTENSION);
    $unique_image_name = uniqid() . '.' . $image_extension;

    $pdf = null;
    $pdf_tmp = null;
    $unique_pdf_name = null;
    $pdf_upload_dir = '../files/';

    if ($book_type === 'ebook' && isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
        $pdf = $_FILES['pdf']['name'];
        $pdf_tmp = $_FILES['pdf']['tmp_name'];

        $pdf_extension = pathinfo($pdf, PATHINFO_EXTENSION);
        $unique_pdf_name = uniqid() . '.' . $pdf_extension;

        if (!move_uploaded_file($pdf_tmp, $pdf_upload_dir . $unique_pdf_name)) {
            echo "PDF upload failed.";
            exit();
        }
    }

    if (move_uploaded_file($image_tmp, $upload_dir . $unique_image_name)) {
        $stmt = $conn->prepare("INSERT INTO manage_books (title, author, genre, isbn, book_type, publication_date, publisher, status, description, image, pdf, expiry_days) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssi", $title, $author, $genre, $isbn, $book_type, $publication_date, $publisher, $status, $description, $unique_image_name, $unique_pdf_name, $expiry_days);

        if ($stmt->execute()) {
            header('Location: admin_catalogForm.php?success=1');
            exit();
        } else {
            echo "Could not insert record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Image upload failed.";
    }

    $conn->close();
} else {
    header('Location: admin_catalogForm.php?error=invalid_request');
    exit();
}
?>
