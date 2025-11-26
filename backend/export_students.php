<?php
// filepath: c:\xampp\htdocs\Med_invent_sys\Medical_Inventory_System\backend\export_students.php
include 'db_connect.php';

$record_type = isset($_GET['record_type']) ? $_GET['record_type'] : 'medical';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$blood_type = isset($_GET['blood_type']) ? $_GET['blood_type'] : '';
$allergy = isset($_GET['allergy']) ? $_GET['allergy'] : '';
$asthma = isset($_GET['asthma']) ? $_GET['asthma'] : '';
$diabetes = isset($_GET['diabetes']) ? $_GET['diabetes'] : '';
$heart_disease = isset($_GET['heart_disease']) ? $_GET['heart_disease'] : '';
$other_HC = isset($_GET['other_HC']) ? $_GET['other_HC'] : '';
$dental_condition = isset($_GET['dental_condition']) ? $_GET['dental_condition'] : '';
$campus = isset($_GET['campus']) ? $_GET['campus'] : '';
$course = isset($_GET['course']) ? $_GET['course'] : '';

$where_clause = "";
if (!empty($start_date) && !empty($end_date)) {
    $where_clause = "AND p.created_at BETWEEN '$start_date' AND '$end_date'";
}

if (!empty($campus)) $where_clause .= " AND l.campus_name = '$campus'";
if (!empty($course)) $where_clause .= " AND m.course_id = '$course'";

if ($record_type == 'medical') {
    if (!empty($blood_type)) $where_clause .= " AND UPPER(TRIM(p.blood_type)) = UPPER(TRIM('$blood_type'))";
    if (!empty($allergy)) $where_clause .= " AND m.allergy LIKE '%$allergy%'";
    if (!empty($asthma)) $where_clause .= " AND m.asthma = '$asthma'";
    if (!empty($diabetes)) $where_clause .= " AND m.diabetes = '$diabetes'";
    if (!empty($heart_disease)) $where_clause .= " AND m.heart_disease = '$heart_disease'";
    if (!empty($other_HC)) $where_clause .= " AND m.other_HC LIKE '%$other_HC%'";

    $query = "
        SELECT s.id_number, s.last_name, s.first_name, s.middle_name, l.campus_name AS campus, l.course_name AS course_name,
               p.blood_pressure, p.blood_type, 
               m.allergy, m.asthma, m.diabetes, m.heart_disease, m.other_HC, 
               p.height_cm, p.weight_kg
        FROM neust_student_details.students s
        LEFT JOIN neustmrdb.medical_record m ON s.student_id = m.student_id
        LEFT JOIN neustmrdb.physical_examination p ON m.medical_id = p.medical_id
        LEFT JOIN neustmrdb.medical_location l ON m.course_id = l.campus_id
        WHERE 1=1 $where_clause
    ";
} else {
    if (!empty($dental_condition)) $where_clause .= " AND d.record_details LIKE '%$dental_condition%'";

    $query = "
        SELECT s.id_number, s.last_name, s.first_name, s.middle_name, l.campus_name AS campus, l.course_name AS course_name,
               d.record_details AS dental_condition, d.date_created AS last_checkup_date
        FROM neust_student_details.students s
        LEFT JOIN neustmrdb.dental_records d ON s.student_id = d.student_id
        LEFT JOIN neustmrdb.medical_location l ON d.campus_id = l.campus_id
        WHERE 1=1 $where_clause
    ";
}

$result = mysqli_query($conn_student, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn_student));
}

// Export logic (e.g., CSV generation)
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="student_records.csv"');

$output = fopen('php://output', 'w');
if ($record_type == 'medical') {
    fputcsv($output, ['Student Number', 'Last Name', 'First Name', 'Middle Name', 'Campus', 'Course', 'Blood Pressure', 'Blood Type', 'Allergy', 'Asthma', 'Diabetes', 'Heart Disease', 'Other Health Conditions', 'Height (cm)', 'Weight (kg)']);
} else {
    fputcsv($output, ['Student Number', 'Last Name', 'First Name', 'Middle Name', 'Campus', 'Course', 'Dental Condition', 'Last Checkup Date']);
}

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
?>