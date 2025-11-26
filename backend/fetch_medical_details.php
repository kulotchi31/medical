<?php
include 'db_connect.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

// Ensure medical_id is an integer
$medical_id = intval($_GET['id']); 

$query = "
    SELECT 
        s.student_photo,
        s.first_name,
        s.middle_name,
        s.last_name,
        s.id_number,
        s.guardian_name, 
        s.emergency_contact,
        p.blood_pressure, 
        p.blood_type, 
        m.allergy, 
        m.asthma, 
        m.diabetes, 
        m.heart_disease, 
        m.other_HC, 
        m.medication, 
        m.school_year,
        p.exam_date, 
        p.height_cm, 
        p.weight_kg,
        p.smoking,
        p.liquor_drinking,
        v.dose_number
    FROM neustmrdb.medical_record m
    LEFT JOIN neustmrdb.physical_examination p ON m.medical_id = p.medical_id
    LEFT JOIN neustmrdb.vaccine v ON m.medical_id = v.medical_id
    LEFT JOIN neust_student_details.students s ON s.student_id = m.student_id
    WHERE m.medical_id = ?
";

// Use prepared statements
$stmt = mysqli_prepare($conn_student, $query);
mysqli_stmt_bind_param($stmt, "i", $medical_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . mysqli_error($conn_student)]);
    exit;
}

if (mysqli_num_rows($result) == 0) {
    echo json_encode(["error" => "No medical record found"]);
    exit;
}

$data = mysqli_fetch_assoc($result);

// Convert boolean values
$data['smoking'] = isset($data['smoking']) && $data['smoking'] == 1 ? 'Yes' : 'No';
$data['liquor_drinking'] = isset($data['liquor_drinking']) && $data['liquor_drinking'] == 1 ? 'Yes' : 'No';

// Handle vaccine dose status
$dose_status = "No vaccination";
if (isset($data['dose_number'])) {
    switch ($data['dose_number']) {
        case 1:
            $dose_status = "1st Dose";
            break;
        case 2:
            $dose_status = "1st and 2nd Dose";
            break;
        case 3:
            $dose_status = "1st, 2nd, and 1st Booster";
            break;
        case 4:
            $dose_status = "1st, 2nd, 1st Booster, and 2nd Booster";
            break;
        default:
            $dose_status = "No vaccination";
    }
}

$data['vaccine_status'] = $dose_status;

// Return JSON response
echo json_encode($data);
?>
