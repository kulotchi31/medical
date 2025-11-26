<?php
include '../backend/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $medical_id = $_POST['medical_id'];
    $exam_date = $_POST['exam_date'];
    $height_cm = $_POST['height_cm'];
    $weight_kg = $_POST['weight_kg'];
    $blood_pressure = $_POST['blood_pressure'];
    $smoking = $_POST['smoking'];
    $liquor_drinking = $_POST['liquor_drinking'];

    $query = "INSERT INTO physical_examination (medical_id, exam_date, height_cm, weight_kg, blood_pressure, smoking, liquor_drinking) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn_medical->prepare($query);
    $stmt->bind_param("isddsis", $medical_id, $exam_date, $height_cm, $weight_kg, $blood_pressure, $smoking, $liquor_drinking);

    if ($stmt->execute()) {
        echo "success";
    } else {
        http_response_code(500);
        echo "error";
    }
}
?>
