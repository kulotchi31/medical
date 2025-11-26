<?php
include 'db_connect.php';

header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Log the incoming POST data
    file_put_contents("debug_log.txt", "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

    // Validate `medical_id`
    if (empty($_POST['medical_id'])) {
        file_put_contents("debug_log.txt", "Error: Missing medical_id\n", FILE_APPEND);
        echo json_encode(["success" => false, "message" => "Missing medical_id."]);
        exit;
    }

    $medical_id = mysqli_real_escape_string($conn_medical, trim($_POST['medical_id']));
    file_put_contents("debug_log.txt", "Received medical_id: $medical_id\n", FILE_APPEND);

    // Check if the medical_id exists in the database
    $checkQuery = "SELECT student_id FROM neustmrdb.medical_record WHERE medical_id = '$medical_id'";
    $checkResult = mysqli_query($conn_medical, $checkQuery);
    if ($checkResult) {
        $row = mysqli_fetch_assoc($checkResult);
        if (!$row) {
            file_put_contents("debug_log.txt", "Error: medical_id $medical_id does not exist\n", FILE_APPEND);
            echo json_encode(["success" => false, "message" => "Medical record not found."]);
            exit;
        }
        $student_id = $row['student_id'];
    } else {
        $error = mysqli_error($conn_medical);
        file_put_contents("debug_log.txt", "Database error during medical_id check: $error\n", FILE_APPEND);
        echo json_encode(["success" => false, "message" => "Database error: $error"]);
        exit;
    }

    // Update fields in the `students` table
    $studentFields = [];
    $allowedStudentFields = [
        'first_name' => 'first_name',
        'middle_name' => 'middle_name',
        'last_name' => 'last_name',
        'guardian_name' => 'guardian_name',
        'emergency_contact' => 'emergency_contact'
    ];

    foreach ($allowedStudentFields as $key => $dbField) {
        if (isset($_POST[$key])) {
            $studentFields[] = "$dbField = '" . mysqli_real_escape_string($conn_student, $_POST[$key]) . "'";
        }
    }

    if (!empty($studentFields)) {
        $studentQuery = "UPDATE neust_student_details.students
                         SET " . implode(", ", $studentFields) . " WHERE student_id = '$student_id'";

        // Log the constructed query
        file_put_contents("debug_log.txt", "Constructed Student Query: $studentQuery\n", FILE_APPEND);

        // Execute the query
        $studentResult = mysqli_query($conn_student, $studentQuery);
        if (!$studentResult) {
            $error = mysqli_error($conn_student);
            file_put_contents("debug_log.txt", "Database error during student update: $error\n", FILE_APPEND);
            echo json_encode(["success" => false, "message" => "Database error: $error"]);
            exit;
        }
    }

    // Update fields in the `medical_record` table
    $medicalFields = [];
    $allowedMedicalFields = [
        'allergy' => 'allergy',
        'asthma' => 'asthma',
        'diabetes' => 'diabetes',
        'heart_disease' => 'heart_disease',
        'seizure_disorder' => 'seizure_disorder',
        'other_HC' => 'other_HC',
        'medication' => 'medication',
        'record_date' => 'record_date'
    ];

    foreach ($allowedMedicalFields as $key => $dbField) {
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
            if (in_array($key, ['asthma', 'seizure_disorder'])) {
                // Ensure boolean fields are either 0 or 1
                $value = ($value == '1') ? 1 : 0;
            }
            $medicalFields[] = "$dbField = '" . mysqli_real_escape_string($conn_medical, $value) . "'";
        }
    }

    if (!empty($medicalFields)) {
        $medicalQuery = "UPDATE neustmrdb.medical_record
                         SET " . implode(", ", $medicalFields) . " WHERE medical_id = '$medical_id'";

        // Log the constructed query
        file_put_contents("debug_log.txt", "Constructed Medical Query: $medicalQuery\n", FILE_APPEND);

        // Execute the query
        $medicalResult = mysqli_query($conn_medical, $medicalQuery);
        if (!$medicalResult) {
            $error = mysqli_error($conn_medical);
            file_put_contents("debug_log.txt", "Database error during medical record update: $error\n", FILE_APPEND);
            echo json_encode(["success" => false, "message" => "Database error: $error"]);
            exit;
        }
    }

    echo json_encode(["success" => true, "message" => "Record updated successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

/**
 * Handle image upload and return the result.
 *
 * @param array $file The uploaded file data from $_FILES.
 * @return array An associative array with 'success', 'path', and 'message'.
 */
function handleImageUpload($file)
{
    $targetDir = "../uploads/medical_records/";
    $fileName = time() . "_" . basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;

    // Check if the file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["success" => false, "message" => "Invalid image file."];
    }

    // Move the uploaded file
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return ["success" => true, "path" => "uploads/medical_records/" . $fileName];
    } else {
        return ["success" => false, "message" => "Failed to upload image."];
    }
}
?>
