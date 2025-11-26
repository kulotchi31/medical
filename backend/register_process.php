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

require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Form submitted successfully"); // Log form submission
    error_log("Email: " . $_POST['email']); // Log email input
    error_log("Role: " . $_POST['role']); // Log role input

    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $position = trim($_POST['position']);
    $campus = trim($_POST['campus']);

    // Check if the email is already registered
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['message3'] = "Email is already registered!";
        $_SESSION['message_type3'] = "warning";
        header("Location: ../frontend/add_account.php");
        exit();
    }

    $password = bin2hex(random_bytes(4)); 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (email, password, role, first_name, last_name, position, campus) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $email, $hashed_password, $role, $first_name, $last_name, $position, $campus);
    
    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 2;  // Enable verbose debug output
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mrjixel@gmail.com';
            $mail->Password = 'nktrxardrueklezj'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true
                ]
            ];

            $mail->setFrom('mrjixel@gmail.com', 'NEUST');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your Account Details';
            $mail->Body = "
            *** PLEASE DO NOT REPLY OR SEND MESSAGE TO THIS EMAIL *** <br><br>
            Welcome! Your account has been created.<br><br>
            <strong>Email:</strong> $email <br>
            <strong>Password:</strong> $password <br><br>
            Please change your password after logging in.";

            $mail->send();
            
            $_SESSION['message3'] = "Account created successfully! Password has been sent to email.";
            $_SESSION['message_type3'] = "success";
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo); // Log the error
            $_SESSION['message3'] = "Account created, but email failed to send. Error: " . $mail->ErrorInfo;
            $_SESSION['message_type3'] = "warning";
        }
    } else {
        $_SESSION['message3'] = "Registration failed!";
        $_SESSION['message_type3'] = "error";
    }

    $stmt->close();
    $conn->close();
    header("Location: ../frontend/add_account.php");
    exit();
}
?>
