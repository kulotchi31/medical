<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = mysqli_real_escape_string($conn_medical, $_POST['student_id']);
    $date = mysqli_real_escape_string($conn_medical, $_POST['date']);
    
    $chief_complaint = $_POST['chief_complaint'];
    if ($chief_complaint === 'Other' && !empty($_POST['other_complaint'])) {
        $chief_complaint = $_POST['other_complaint'];
    }
    $chief_complaint = mysqli_real_escape_string($conn_medical, $chief_complaint);
    
    $treatment = mysqli_real_escape_string($conn_medical, $_POST['treatment']);

    if (empty($student_id) || empty($date) || empty($chief_complaint)  || empty($treatment)) {
        $_SESSION['message'] = "All fields (Student ID, Date, Chief Complaint, and Treatment) are required.";
        $_SESSION['message_type'] = "error";
        header("Location: ../frontend/emp_treatment_record.php");
        exit;
    }

    $query = "INSERT INTO employee_treatment_records (id_number, date_treatment, emp_complaint,  treatment) VALUES (?, ?, ?, ?)";
    $stmt = $conn_medical->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn_medical->error);
    }

    $stmt->bind_param("ssss", $student_id, $date, $chief_complaint, $treatment);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Medical record added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        $_SESSION['message_type'] = "error";
    }

    $stmt->close();
    header("Location: ../frontend/emp_treatment_record.php");
    exit;
}
?>
