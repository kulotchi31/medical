<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['medical_id'])) {
    // Sanitize input
    $medical_id = intval($_POST['medical_id']); // Ensure it's an integer

    $query = "UPDATE neustmrdb.medical_record SET deleted_at = NOW() WHERE medical_id = ?";

    if ($stmt = $conn_medical->prepare($query)) { // Ensure $conn_medical is correct
        $stmt->bind_param("i", $medical_id);
        
        if ($stmt->execute()) {
            echo "success"; // Ensure front-end checks for "success"
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error in prepared statement: " . $conn_medical->error;
    }

    $conn_medical->close();
} else {
    echo "Invalid request.";
}
?>
