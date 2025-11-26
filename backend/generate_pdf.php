<?php
require('../vendor/fpdf/fpdf.php'); // Ensure you have FPDF installed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = $_POST['id_number'] ?? 'N/A';
    $name = $_POST['name'] ?? 'N/A';
    $campus = $_POST['campus'] ?? 'N/A';
    $exam_date = $_POST['exam_date'] ?? 'N/A';
    $remarks = $_POST['remarks'] ?? 'N/A';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, "Medical Certificate", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 10, "ID Number: $id_number");
    $pdf->Ln();
    $pdf->Cell(40, 10, "Name: $name");
    $pdf->Ln();
    $pdf->Cell(40, 10, "Campus: $campus");
    $pdf->Ln();
    $pdf->Cell(40, 10, "Exam Date: $exam_date");
    $pdf->Ln();
    $pdf->MultiCell(0, 10, "Remarks: $remarks");
    $pdf->Ln();

    $pdf_folder = "../generated_pdfs/";
    if (!file_exists($pdf_folder)) {
        mkdir($pdf_folder, 0777, true);
    }

    $pdf_path = $pdf_folder . "medical_certificate_preview.pdf";
    $pdf->Output($pdf_path, 'F');

    echo json_encode(["pdf_url" => $pdf_path]);
}
?>
