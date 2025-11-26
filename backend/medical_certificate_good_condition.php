<?php
require_once('../vendor/tcpdf/tcpdf.php');
include '../backend/db_connect.php';

if (!isset($_GET['id_number'])) {
    die('ID Number is required.');
}

$id_number = htmlspecialchars($_GET['id_number']);

// Fetch student details
$query = "SELECT * FROM students WHERE id_number = ?";
$stmt = $conn_student->prepare($query);
$stmt->bind_param("s", $id_number);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Fetch medical records
$query_records = "SELECT * FROM medical_treatment_records WHERE student_id = ?";
$stmt_records = $conn_medical->prepare($query_records);
$stmt_records->bind_param("s", $id_number);
$stmt_records->execute();
$result_records = $stmt_records->get_result();
$medical_records = $result_records->fetch_all(MYSQLI_ASSOC);

// Create new PDF document
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Medical Department');
$pdf->SetTitle('Medical Report');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

// Document Header
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Medical Report', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Student Information', 0, 1);
$pdf->Cell(0, 8, 'ID Number: ' . $user_data['id_number'], 0, 1);
$pdf->Cell(0, 8, 'Name: ' . $user_data['first_name'] . ' ' . $user_data['middle_name'] . ' ' . $user_data['last_name'], 0, 1);
$pdf->Cell(0, 8, 'Campus: ' . $user_data['campus'], 0, 1);
$pdf->Ln(5);

if (empty($medical_records)) {
    // No medical records - Good Health Certificate
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Certificate of Good Health', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, 
        "This is to certify that the student named above has undergone a review of their medical records, "
        . "and no health issues or medical conditions have been recorded in our system. "
        . "The student is hereby declared to be in good health and fit to participate in academic and extracurricular activities.", 
        0, 'C'
    );
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 11);
    $pdf->MultiCell(0, 10, 
        "Issued by the Medical Department of the institution on " . date('F j, Y') . ".", 
        0, 'C'
    );
} else {
    // Medical Records Exist - Show Treatment Details
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Medical Treatment Records', 0, 1);
    
    foreach ($medical_records as $record) {
        $pdf->SetFont('helvetica', '', 11);
        $pdf->MultiCell(0, 8, "Date: " . $record['date'] . "\nChief Complaint: " . $record['chief_complaint'] . "\nTreatment: " . $record['treatment'] . "\nDate Created: " . $record['date_created'], 1, 'L');
        $pdf->Ln(4);
    }
}

$pdf->Output('medical_report.pdf', 'D');
?>
