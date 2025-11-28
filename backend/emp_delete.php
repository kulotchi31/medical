<?php
include 'db_connect.php';

header("Content-Type: application/json");

// Validate request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid method"]);
    exit;
}

if (!isset($_POST['emp_id']) || empty($_POST['emp_id'])) {
    echo json_encode(["success" => false, "message" => "Missing emp_id"]);
    exit;
}

$emp_id = intval($_POST['emp_id']);

$query = "UPDATE neustmrdb.employee SET deleted_at = NOW() WHERE emp_id = ?";

// >>> FIX: Use correct connection
$stmt = $conn_medical->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn_medical->error]);
    exit;
}

$stmt->bind_param("i", $emp_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Employee deleted"]);
} else {
    echo json_encode(["success" => false, "message" => "Execution failed: " . $stmt->error]);
}

$stmt->close();
$conn_medical->close();
