<?php
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

if (empty($_POST['emp_id'])) {
    echo json_encode(["success" => false, "message" => "Missing emp_id."]);
    exit;
}

$emp_id = mysqli_real_escape_string($conn_medical, $_POST['emp_id']);

$allowedFields = [
    "id_number",
    "fname",
    "mname",
    "lname",
    "position",
    "office",
    "campus",
    "province",
    "city",
    "barangay",
    "emc_person",
    "emc_number",
    "emc_address"
];

$updateFields = [];

foreach ($allowedFields as $field) {
    if (isset($_POST[$field])) {
        $value = mysqli_real_escape_string($conn_medical, $_POST[$field]);
        $updateFields[] = "$field = '$value'";
    }
}

if (!empty($_FILES['emp_image']['name'])) {
    $uploadResult = uploadEmployeeImage($_FILES['emp_image']);
    if ($uploadResult['success']) {
        $imagePath = mysqli_real_escape_string($conn_medical, $uploadResult['path']);
        $updateFields[] = "emp_image = '$imagePath'";
    } else {
        echo json_encode(["success" => false, "message" => $uploadResult['message']]);
        exit;
    }
}

if (!empty($updateFields)) {
    $updateQuery = "UPDATE employee SET " . implode(", ", $updateFields) . " WHERE emp_id = '$emp_id'";
    if (!mysqli_query($conn_medical, $updateQuery)) {
        $error = mysqli_error($conn_medical);
        echo json_encode(["success" => false, "message" => "Database update failed: $error"]);
        exit;
    }
}

echo json_encode(["success" => true, "message" => "Employee updated successfully!"]);
exit;

function uploadEmployeeImage($file)
{
    $targetDir = "../uploads/employees/";

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($file["name"]);
    $targetPath = $targetDir . $fileName;

    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["success" => false, "message" => "Invalid image file."];
    }

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        return ["success" => true, "path" => "uploads/employees/" . $fileName];
    } else {
        return ["success" => false, "message" => "Failed to upload image."];
    }
}
?>
