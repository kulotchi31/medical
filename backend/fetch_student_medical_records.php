<?php
include 'db_connect.php';

$query = "SELECT * FROM students"; 
$result = mysqli_query($conn, $query);

if (!$result) {
    die(json_encode(["error" => "Query failed: " . mysqli_error($conn)]));
}

$records = mysqli_fetch_all($result, MYSQLI_ASSOC);
header('Content-Type: application/json');
echo json_encode($records);
?>
