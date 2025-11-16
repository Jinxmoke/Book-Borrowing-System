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
$pdf->Cell(0, 10, 'Terms and Conditions', 0, 1, 'C');

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
addSection($pdf, "1. Eligibility",
"Registration requires accurate and complete information. Users are responsible for keeping their login credentials secure.");

addSection($pdf, "2. Registration and Account",
"Users must provide valid user information during registration. Accounts are non-transferable, and users are responsible for all activities conducted under their account.");

addSection($pdf, "3. Borrowing Rules",
"Each user may borrow up to 3 books at a time. Overdue physical book returns may result in fines.");

addSection($pdf, "4. User Responsibilities",
"Users are responsible for ensuring the safe handling and return of borrowed physical books. Loss or damage to physical books may result in temporarily banned from borrowing books until the issue is resolved. Unauthorized reproduction or distribution of eBooks is strictly prohibited.");

addSection($pdf, "5. Privacy and Data Security",
"We collect and store personal data solely for managing your account and borrowing history. Your data will not be shared with third parties without your explicit consent, except as required by law.");

addSection($pdf, "6. Termination",
"We may suspend or terminate your access to our services if you violate these Terms and Conditions. ");


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
