<?php


function getStudentData($conn_student, $id_number) {
    $query = "SELECT * FROM students WHERE id_number = ?";
    $stmt = $conn_student->prepare($query);
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getDentalRecords($conn_medical, $id_number) {
    $query = "SELECT * FROM dental_records WHERE student_id = ? AND date_deleted IS NULL ORDER BY date_created DESC";
    $stmt = $conn_medical->prepare($query);
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty(trim($_POST['id_number']))) {
        $error_message = "ID number is required.";
    } else {
        $id_number = htmlspecialchars(trim($_POST['id_number']));
        $user_data = getStudentData($conn_student,  $id_number);
        
        if ($user_data) {
            $dental_records = getDentalRecords($conn_medical, $id_number);
        } else {
            $error_message = "User not found.";
        }
    }
}
 ?>