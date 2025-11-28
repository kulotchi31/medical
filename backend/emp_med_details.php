<?php
include 'db_connect.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

$emp_id = intval($_GET['id']); 

$query = "
    SELECT 
        emp_id,
        id_number,
        fname,
        mname,
        lname,
        position,
        office,
        campus,
        province,
        city,
        barangay,
        emc_person,
        emc_number,
        emc_address,
        emp_image
    FROM employee
    WHERE emp_id = ?
";

$stmt = mysqli_prepare($conn_medical, $query);
mysqli_stmt_bind_param($stmt, "i", $emp_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . mysqli_error($conn_medical)]);
    exit;
}

if (mysqli_num_rows($result) == 0) {
    echo json_encode(["error" => "No employee record found"]);
    exit;
}

$data = mysqli_fetch_assoc($result);

echo json_encode($data);
?>
