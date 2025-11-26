<?php
header("Content-Type: application/json"); 
include 'db_connect.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    if (!$conn_medical) {
        $response['message'] = "Database connection error.";
        echo json_encode($response);
        exit;
    }

    $id_number = mysqli_real_escape_string($conn_medical, trim($_POST['id_number'] ?? ''));
    $tooth_number = mysqli_real_escape_string($conn_medical, trim($_POST['tooth'] ?? ''));
    $record_details = mysqli_real_escape_string($conn_medical, trim($_POST['record_details'] ?? ''));


    if (!empty($id_number) && !empty($tooth_number) && !empty($record_details)) {
        // Prepare SQL statement
        $query = "INSERT INTO dental_records (student_id, tooth_number, record_details, date_created) 
                  VALUES (?, ?, ?, NOW())";

        if ($stmt = $conn_medical->prepare($query)) {
            $stmt->bind_param("sss", $id_number, $tooth_number, $record_details);

            if ($stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['message'] = "Database error: Failed to insert dental record.";
                error_log("Database Insert Error: " . $stmt->error);
            }

            $stmt->close(); 
        } else {
            $response['message'] = "Database error: Failed to prepare statement.";
            error_log("SQL Prepare Error: " . $conn_medical->error);
        }
    } else {
        $response['message'] = "All fields are required.";
    }
} else {
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
?>
