<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = mysqli_real_escape_string($conn_student, $_POST['student_id'] ?? '');
    $tooth_number = mysqli_real_escape_string($conn_medical, $_POST['tooth_number'] ?? '');
    $record_details = mysqli_real_escape_string($conn_medical, $_POST['record_details'] ?? '');

    if ($student_id && $tooth_number && $record_details) {
        $query = "INSERT INTO dental_records (student_id, tooth_number, record_details, date_created) VALUES (?, ?, ?, NOW())";
        $stmt = $conn_medical->prepare($query);
        $stmt->bind_param("sss", $student_id, $tooth_number, $record_details);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Missing required fields."]);
    }
}
?>
