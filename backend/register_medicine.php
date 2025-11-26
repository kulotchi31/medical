<?php
session_start();

// Restrict access to super_admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: ../frontend/access_denied.php"); // Redirect to an access denied page
    exit();
}

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "neustmrdb"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Form submitted successfully"); // Log form submission
  

    $medicineName = trim($_POST['medicineName']);
    $medicineDescription = trim($_POST['medicineDescription']);


    $stmt = $conn->prepare("INSERT INTO medicine (medicine_name, medicine_description) VALUES (?, ?)");
    $stmt->bind_param("ss",  $medicineName, $medicineDescription);
    
    if ($stmt->execute()) {
            $_SESSION['message3'] = "$medicineName created successfully!";
            $_SESSION['message_type3'] = "success";
    } else {
        $_SESSION['message3'] = "Registration failed!";
        $_SESSION['message_type3'] = "error";
    }

    $stmt->close();
    $conn->close();
    header("Location: ../frontend/add_medicine");
    exit();
}
?>
