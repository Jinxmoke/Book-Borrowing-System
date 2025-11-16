<?php
ob_start();
session_start();
include('../config/connect.php');

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized access");
}

$token = $_GET['token'] ?? '';
error_log("Received token: $token");

if (!validatePDFToken($token)) {
    error_log("Invalid token.");
    die("Invalid or expired access token");
}

// Extract book_id from token
$parts = explode('|', base64_decode($token));
$book_id = $parts[0];

$stmt = $conn->prepare("SELECT pdf FROM manage_books WHERE book_id = ? AND book_type = 'ebook'");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $pdfPath = realpath('../files/' . $row['pdf']);
    error_log("Resolved PDF path: $pdfPath");

    if (file_exists($pdfPath)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="book.pdf"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        readfile($pdfPath);
        exit;
    } else {
        error_log("PDF file not found: $pdfPath");
        die("PDF not found");
    }
}

error_log("No matching book found in database for book_id: $book_id");
die("PDF not found");
ob_end_flush();
?>
