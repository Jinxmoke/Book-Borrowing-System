<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

header('Content-Type: application/json');

include('../config/connect.php');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['bookId'], $data['borrowerEmail'], $data['borrowerName'], $data['bookTitle'], $data['dueDate'])) {
    $bookId = $data['bookId'];
    $borrowerEmail = $data['borrowerEmail'];
    $borrowerName = $data['borrowerName'];
    $bookTitle = $data['bookTitle'];
    $dueDate = $data['dueDate'];

    // Check if a fine has already been added for this book
    $checkFine = $conn->prepare("SELECT id FROM overdue_fines WHERE book_id = ? AND borrower_email = ?");
    $checkFine->bind_param("is", $bookId, $borrowerEmail);
    $checkFine->execute();
    $result = $checkFine->get_result();

    if ($result->num_rows == 0) {
        // No fine exists, add a new fine
        $addFine = $conn->prepare("INSERT INTO overdue_fines (book_id, borrower_email) VALUES (?, ?)");
        $addFine->bind_param("is", $bookId, $borrowerEmail);
        $addFine->execute();

        // Update user's total fines
        $updateFines = $conn->prepare("UPDATE user_info SET fines = fines + 1 WHERE email = ?");
        $updateFines->bind_param("s", $borrowerEmail);
        $updateFines->execute();

        $fineAdded = true;
    } else {
        $fineAdded = false;
    }

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'mail.bsit-ucc.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bookborrowing@bsit-ucc.com'; 
        $mail->Password   = '9mR_aKw{CB#M';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('bookborrowing@bsit-ucc.com', 'Caloocan Public Library');
        $mail->addAddress($borrowerEmail, $borrowerName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Overdue Book Reminder';
        $body = <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Overdue Reminder</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f7f9;
            color: #333;
            padding: 20px;
            margin: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 80px;
            height: 80px;
            background-color: #FD8418;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        h2 {
            color: #FD8418;
            font-size: 28px;
            font-weight: 600;
            margin: 0;
        }
        .content {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
        }
        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 15px;
        }
        .highlight {
            color: #FD8418;
            font-weight: 600;
        }
        .warning {
            background-color: #fff3e0;
            border-left: 4px solid #FD8418;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .fine-note {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .footer {
            background-color: #FD8418;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }
        .footer p {
            font-size: 16px;
            margin: 0;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Book Overdue Reminder</h2>
        </div>
        <div class="content">
            <p>Dear <span class="highlight">{$borrowerName}</span>,</p>
            <p>This is a friendly reminder that the book <span class="highlight">{$bookTitle}</span> is overdue.</p>
            <p><strong>Due Date:</strong> {$dueDate}</p>
            <p>Please return the book to the library as soon as possible.</p>
            <div class="warning">
                <p><strong>Important:</strong> Please be aware that each overdue book will result in a fine added to your account. You are allowed up to 3 fines, but once you reach this limit, your account will be disabled.</p>
            </div>
EOD;

        if ($fineAdded) {
            $body .= <<<EOD
            <div class='fine-note'>
                <p><strong>Note:</strong> A fine of 1 has been added to your account for this overdue book.</p>
            </div>
EOD;
        }

        $body .= <<<EOD
        </div>
        <div class="footer">
            <p>Thank you,<br><strong>Caloocan Public Library</strong></p>
        </div>
    </div>
</body>
</html>
EOD;

        $mail->Body = $body;
        // Send the email
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Reminder sent' . ($fineAdded ? ' and fine added' : '')]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing data']);
}

$conn->close();
?>
