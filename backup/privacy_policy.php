<?php
require('./fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10,'Caloocan Public Library',0,0,'C');
        // Line break
        $this->Ln(20);
    }

    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Create instance of PDF class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Set colors
$pdf->SetDrawColor(0,80,180);
$pdf->SetFillColor(230,230,0);
$pdf->SetTextColor(0,0,0);

// Set font for the document
$pdf->SetFont('Arial', 'B', 16);

// Title of the document
$pdf->Cell(0, 10, 'Privacy Policy', 0, 1, 'C');

// Line break
$pdf->Ln(10);

// Set font for the body text
$pdf->SetFont('Arial', '', 11);

// Define fine value
$fine_per_day = 1; // Fine per day for overdue physical books

// Function to add a section
function addSection($pdf, $title, $content) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $title, 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 6, $content, 0, 'J');
    $pdf->Ln(5);
}

// Add Terms and Conditions sections
addSection($pdf, "1. Your User Information",
"When you join our borrowing system, we collect information needed to manage your accounts. This includes your name, email, home address, and phone number. We also create a unique member ID for your account that helps us track your borrowed books and return dates.");

addSection($pdf, "2. Book Borrowing Activity",
"We maintain a detailed record of all the books you borrow, which includes important information such as the due dates and return dates for each item. This allows us to keep track of your borrowing history. In addition, we monitor any fines associated with late returns or damages, ensuring that all the charges are properly recorded and managed.");

addSection($pdf, "3. How We Use Your Information",
"We use the information we collect from you to process borrowing and return requests, communicate with you about your account, and send reminders about due dates or overdue books.");

addSection($pdf, "4. Protecting Your Reading Privacy",
"We understand that your reading choices are personal. Your borrowing history and reading preferences remain confidential and are never shared with other members.");

addSection($pdf, "5. Digital Security",
"We protect your online account with robust password security and data encryption.");

addSection($pdf, "6. Data Retention",
"We maintain your borrowing history for [specific time period] to effectively manage returns and address any disputes.");


// Contact Information
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Contact Us', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, "If you have questions about these terms, please contact us at:
bookborrowing@bsit-ucc.com
", 0, 'J');

// Border
$pdf->Rect(5, 5, 200, 287, 'D');

// Output the PDF
$pdf->Output('I', 'Terms_and_Conditions.pdf');
?>
