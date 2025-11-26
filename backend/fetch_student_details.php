<?php
include 'db_connect.php'; 

if (isset($_GET['id_number'])) {
    $id_number = mysqli_real_escape_string($conn_student, $_GET['id_number']);

   
    $stmt = $conn_student->prepare("SELECT * FROM students WHERE id_number = ?");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        $student = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'name' => $student['first_name'] . ' ' . $student['last_name'],
            'campus' => $student['campus'] 
        ]);
    } else {
    
        echo json_encode(['success' => false]);
    }
} else {
    
    echo json_encode(['success' => false]);
}
?>
