<?php

include 'db_connect.php'; 


$campusQuery = "SELECT DISTINCT campus FROM neust_student_details.students";
$campusResult = mysqli_query($conn_student, $campusQuery);

$courseQuery = "SELECT DISTINCT course_name FROM neustmrdb.medical_location";
$courseResult = mysqli_query($conn_student, $courseQuery);

$campusFilter = isset($_GET['campus']) ? $_GET['campus'] : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';


$query = "
    SELECT s.student_id, s.id_number, s.first_name, s.middle_name, s.last_name, s.campus AS campus_name, 
           ml.course_name, m.school_year, s.guardian_name, s.emergency_contact, m.medical_id, 
           p.blood_pressure, p.blood_type, m.allergy, m.asthma, m.diabetes, m.heart_disease, 
           m.other_HC, m.medication, p.exam_date, p.height_cm, p.weight_kg
    FROM neust_student_details.students s
    LEFT JOIN neustmrdb.medical_record m ON s.student_id = m.student_id
    LEFT JOIN neustmrdb.physical_examination p ON m.medical_id = p.medical_id
    LEFT JOIN neustmrdb.medical_location ml ON m.course_id = ml.campus_id
    WHERE 1=1";

if ($campusFilter) {
    $query .= " AND s.campus = '" . mysqli_real_escape_string($conn_student, $campusFilter) . "'";
}
if ($courseFilter) {
    $query .= " AND ml.course_name = '" . mysqli_real_escape_string($conn_student, $courseFilter) . "'";
}

$result = mysqli_query($conn_student, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn_student));
}
?>
