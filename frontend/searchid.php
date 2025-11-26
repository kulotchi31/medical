<?php 
    include '../backend/db_connect.php';

//Search By ID, First Name and Last Name
$q = $_GET['q'] ?? '';

if (!empty($q)) {
    $stmt = $conn_student->prepare("
        SELECT id_number, CONCAT(last_name, ', ', first_name) AS full_name
        FROM students
        WHERE id_number LIKE ? OR first_name LIKE ? OR last_name LIKE ?
        ORDER BY id_number ASC
        LIMIT 5
    ");
    $like = "%{$q}%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
}
?>