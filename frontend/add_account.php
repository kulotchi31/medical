<?php
session_start();
require_once '../backend/db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../img/NEUST.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/login_signup.css"></style>

</head>
<body>
 <div id="wrapper">
<?php include 'sidebar.php'; ?>
<div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            </nav>

    <div class="container mt-5">

        <div class="card shadow">

            <div class="card-header py-3">
                <h3 class="m-0 font-weight-bold text-primary">Create New Account</h3>
            </div>
            <div class="card-body">
                <form action="../backend/register_process.php" method="POST" onsubmit="return confirmSubmission();">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="position">Position</label>
                        <input type="text" name="position" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="campus">Campus</label>
                        <select name="campus" class="form-control" required>
                            <option value="">Select Campus</option>
                            <?php 
                                $select_campus = mysqli_query($conn_medical, "SELECT * FROM `campus` WHERE 1");
                                while($row = mysqli_fetch_array($select_campus)){
                                    echo "<option value='$row[campus_id]'>$row[campus_name]</option>";
                                }
                                
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Register</button>
                </form>
                <script>
                    function confirmSubmission() {
                        console.log('Form submitted');
                        return true;
                    }
                </script>
            </div>
        </div>
    </div>
</div>
   <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../vendor/chart.js/Chart.min.js"></script>
    <script>
        <?php if (isset($_SESSION['message3'])): ?>
            Swal.fire({
                icon: "<?php echo $_SESSION['message_type3']; ?>",
                title: "<?php echo $_SESSION['message3']; ?>"
            });
            <?php unset($_SESSION['message3']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
