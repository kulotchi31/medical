<?php
require_once('../tcpdf/tcpdf.php');
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['preview'])) {
    // Fetch data from POST or GET
    $id_number = isset($_POST['id_number']) ? $_POST['id_number'] : (isset($_GET['id_number']) ? $_GET['id_number'] : null);
    $exam_date = isset($_POST['exam_date']) ? $_POST['exam_date'] : (isset($_GET['exam_date']) ? $_GET['exam_date'] : null);
    $certificate_type = isset($_POST['certificate_type']) ? $_POST['certificate_type'] : (isset($_GET['certificate_type']) ? $_GET['certificate_type'] : null);
    $diagnosis = isset($_POST['diagnosis']) ? $_POST['diagnosis'] : (isset($_GET['diagnosis']) ? $_GET['diagnosis'] : null);
    $treatment = isset($_POST['treatment']) ? $_POST['treatment'] : (isset($_GET['treatment']) ? $_GET['treatment'] : null);

    // Validate required fields
    if (!$id_number || !$exam_date || !$certificate_type) {
        die("Error: Missing required fields.");
    }

    // Fetch student details
    $stmt = $conn_student->prepare("SELECT * FROM students WHERE id_number = ?");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Error: Student not found.");
    }
    $student = $result->fetch_assoc();

    // Initialize TCPDF
    $pdf = new TCPDF();
    $pdf->SetTitle('Medical Certificate');
    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 6, 'Republic of the Philippines', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'NUEVA ECIJA UNIVERSITY OF SCIENCE AND TECHNOLOGY', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 3, 'General Tinio Street, Cabanatuan City', 0, 1, 'C');
    $pdf->Ln(5);

    if ($certificate_type === 'medical_issue') {
        if (!$diagnosis || !$treatment) {
            die("Error: Diagnosis and treatment are required for medical reports.");
        }
    
        $pdf->SetFont('helvetica', '', 20);
        $pdf->Cell(0, 10, 'Medical Report', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, "To Whom It May Concern:", 0, 1);
        $pdf->Ln(10);
    
        $middleName = isset($student['middle_name']) ? htmlspecialchars($student['middle_name']) . " " : "";
        $barangay = isset($student['barangay']) ? htmlspecialchars($student['barangay']) : "";
        $city = isset($student['city']) ? htmlspecialchars($student['city']) : "";
        $province = isset($student['province']) ? htmlspecialchars($student['province']) : "";
    
        $pdf->MultiCell(0, 10,
            "      This is to certify that " . htmlspecialchars($student['first_name']) . " " .
            $middleName . htmlspecialchars($student['last_name']) . " of " . $barangay . ", " . $city . ", " . $province .
            " was medically examined on " . htmlspecialchars($exam_date) . ". Based on the examination, the following findings were noted: " .
            "The patient has been diagnosed with: " . htmlspecialchars($diagnosis) . ". The recommended treatment plan includes: " .
            htmlspecialchars($treatment) . ".", 
            0, 'L'
        );
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10,
            "      This medical report is issued upon the request of the patient for purposes of medical documentation, employment, or any other legal or official requirement. " .
            "It serves as a formal record of the patient's medical condition and the prescribed treatment plan.", 
            0, 'L'
        );
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10,
            "      Should you have any further inquiries regarding this report, please do not hesitate to contact the issuing medical office. " .
            "This document is valid only when signed by the authorized medical officer and bears the official seal of the institution.", 
            0, 'L'
        );
    } else {
        $pdf->SetFont('helvetica', '', 20);
        $pdf->Cell(0, 10, 'Medical Certificate', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, "To Whom It May Concern:", 0, 1);
        $pdf->Ln(10);
    
        $pdf->MultiCell(0, 10, 
            "      This is to certify that " . htmlspecialchars($student['first_name']) . " " . 
            (isset($student['middle_name']) ? htmlspecialchars($student['middle_name']) . " " : "") . 
            htmlspecialchars($student['last_name']) . " has undergone a comprehensive medical examination on " . 
            htmlspecialchars($exam_date) . ". Based on the results of the examination, the student is found to be in good physical and mental condition. " . 
            "The student is deemed fit to attend academic classes and participate in extracurricular activities without any restrictions.", 
            0, 'L'
        );
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10, 
            "      This certification is issued upon the request of the student for purposes of enrollment, employment, or any other legal or official requirement. " . 
            "It attests to the student's ability to perform tasks and responsibilities associated with their academic or professional endeavors.", 
            0, 'L'
        );
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10, 
            "      Should you have any further inquiries regarding this certification, please do not hesitate to contact the issuing medical office. " . 
            "This document is valid only when signed by the authorized medical officer and bears the official seal of the institution.", 
            0, 'L'
        );
    }

    $pdf->Ln(20);
    $pdf->Cell(0, 10, "Medical Officer _____________________", 0, 1, 'R');
    $pdf->Cell(0, 10, "", 0, 1, 'R');
    $pdf->Cell(0, 10, "Registration No. _____________________", 0, 1, 'R');

    // Output PDF
    $pdf->Output("medical_certificate.pdf", "I");
    exit;
} else {
    echo "Invalid Request";
}
?>