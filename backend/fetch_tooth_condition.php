<?php 
include 'db_connect.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

$id_number = mysqli_real_escape_string($conn_student, $_GET['id_number'] ?? '');
$tooth = mysqli_real_escape_string($conn_student, $_GET['tooth'] ?? '');

if (!empty($id_number) && !empty($tooth)) {
    $query = "SELECT tooth_number, record_details, 
                     DATE_FORMAT(date_created, '%Y-%m-%d %H:%i:%s') AS date_created 
              FROM dental_records 
              WHERE student_id = ? 
              AND tooth_number = ? 
              AND date_deleted IS NULL 
              ORDER BY date_created DESC 
              LIMIT 5";

    if ($stmt = $conn_medical->prepare($query)) {
        $stmt->bind_param("ss", $id_number, $tooth);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (!empty($result)) {
            echo json_encode($result);
        } else {
            error_log("No records found for id_number=$id_number and tooth=$tooth");
            echo json_encode([]);
        }
    } else {
        error_log("Query preparation failed: " . $conn_medical->error);
        http_response_code(500);
        echo json_encode(["error" => "Failed to prepare the query."]);
    }
} else {
    error_log("Invalid or missing parameters: id_number=$id_number, tooth=$tooth");
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing parameters."]);
}
?>
