<?php
session_start();
require_once '../backend/db_connect.php'; // Ensure database connection is included

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index");
    exit();
}

// Use the global $conn_medical variable for database operations
if (!isset($conn_medical) || $conn_medical->connect_error) {
    die("Database connection failed: " . $conn_medical->connect_error);
}

$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email, position, campus FROM users WHERE user_id = ?";
$stmt = $conn_medical->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../img/NEUST.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
<div id="wrapper">
    <?php include 'sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"></nav>
            <div class="container mt-5">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h3 class="m-0 font-weight-bold text-primary">Profile</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
                        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($user['position']); ?></p>
                        <p><strong>Campus:</strong> <?php echo htmlspecialchars($user['campus']); ?></p>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header py-3">
                        <h3 class="m-0 font-weight-bold text-primary">Change Password</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="change_password.php">
                            <div class="form-group">
                                <label for="currentPassword">Current Password</label>
                                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="newPassword">New Password</label>
                                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>