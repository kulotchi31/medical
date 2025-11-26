<?php
require_once 'db_connect.php';
$conn  = $conn_medical; 

session_start();
    
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string ($conn_medical,trim($_POST['email']));
    $password = mysqli_real_escape_string($conn_medical, trim($_POST['password']));

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();        
       
        if (password_verify($password, $user['password'])) { 
            
            $_SESSION['user_id'] = $user['user_id']; 
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            
            $_SESSION['message'] = "Login successful!";
            $_SESSION['message_type'] = "success";
            header("Location: ../frontend/medical_record_v2"); 
            exit();
        } else {
            
            $_SESSION['message'] = "Invalid password!";
            $_SESSION['message_type'] = "error";
            header("Location: ../index");
            exit();
        }
    } else {
        
        $_SESSION['message'] = "Email not found!";
        $_SESSION['message_type'] = "error";
        header("Location: ../index");
        exit();
        
        $conn->close();
    }

    
    $conn->close();
}
?>
