<?php
session_start();
require_once 'db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn_medical, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn_medical, trim($_POST['password']));

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: index.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ? AND deleted_at IS NULL");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {
                case 'admin':
                    header("Location: admin_dashboard");
                    break;
                case 'staff':
                    header("Location: staff_dashboard");
                    break;
                default:
                    header("Location: super_admin_dashboard");
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password.";
        }
    } else {
        $_SESSION['error'] = "User not found.";
    }

    $stmt->close();
    $conn->close();

    header("Location: index.php");
    exit();
}
?>
