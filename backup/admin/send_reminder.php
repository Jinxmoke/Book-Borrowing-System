<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

header('Content-Type: application/json');


$data = json_decode(file_get_contents('php://input'), true);


if (isset($data['bookId'], $data['borrowerEmail'], $data['borrowerName'], $data['bookTitle'], $data['dueDate'])) {
    $bookId = $data['bookId'];
    $borrowerEmail = $data['borrowerEmail'];
    $borrowerName = $data['borrowerName'];
    $bookTitle = $data['bookTitle'];
    $dueDate = $data['dueDate'];

    
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
        $mail->addAddress($borrowerEmail, $borrowerName); // Recipient's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Overdue Book Reminder';
        $mail->Body    = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
                    <h2 style='color: #FD8418; font-size: 24px; text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;'>Book Overdue Reminder</h2>
                    <p style='font-size: 16px; color: #555;'>Dear <strong>{$borrowerName}</strong>,</p>
                    <p style='font-size: 16px; color: #555;'>This is a friendly reminder that the book <strong style='color: #FD8418;'>{$bookTitle}</strong> is overdue.</p>
                    <p style='font-size: 16px; color: #555;'><strong>Due Date:</strong> {$dueDate}</p>
                    <p style='font-size: 16px; color: #555;'>Please return the book to the library as soon as possible.</p>
                    <div style='margin-top: 20px; background-color: #FD8418; color: white; padding: 10px; text-align: center; border-radius: 4px;'>
                        <p style='font-size: 16px; margin: 0;'>Thank you,<br><strong>Caloocan Public Library</strong></p>
                    </div>
                </div>
            </body>
            </html>
        ";

        // Send the email
        $mail->send();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing data']);
}
?>
