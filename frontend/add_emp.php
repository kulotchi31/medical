<?php
include '../backend/db_connect.php';

$alert = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_number']) && !isset($_FILES['spreadsheet'])) {

    $id_number  = $_POST['id_number'];
    $fname      = $_POST['first_name'];
    $lname      = $_POST['last_name'];
    $mname      = $_POST['middle_name'];
    $position   = $_POST['position'];
    $office     = $_POST['course_name'];
    $campus     = $_POST['student_campus'];
    $province   = $_POST['province'];
    $city       = $_POST['city'];
    $barangay   = $_POST['barangay'];
    $emc_person = $_POST['guardian_name'];
    $emc_number = preg_replace('/[^0-9]/', '', $_POST['emergency_contact']);
    $emc_address = "$barangay, $city, $province";

    $emp_image = "";
    if (!empty($_FILES["student_photo"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);

        $safe_file   = time() . "_" . basename($_FILES["student_photo"]["name"]);
        $target_file = $target_dir . $safe_file;

        if (move_uploaded_file($_FILES["student_photo"]["tmp_name"], $target_file)) {
            $emp_image = $safe_file;
        }
    }

    $stmt = $conn_medical->prepare("
        INSERT INTO employee 
        (id_number, fname, mname, lname, position, office, campus, province, city, barangay, 
         emc_person, emc_number, emc_address, emp_image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssssssssss",
        $id_number, $fname, $mname, $lname, $position, $office, $campus,
        $province, $city, $barangay, $emc_person, $emc_number, $emc_address, $emp_image
    );

    if ($stmt->execute()) {
        $alert = "success";
    } else {
        $alert = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../img/NEUST.png" type="image/png">
<meta charset="UTF-8">
<title>Add Employee</title>

<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="../css/sb-admin-2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body id="page-top">

<div id="wrapper">
    <?php include '../frontend/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                <h4 class="ml-3">Add Employee</h4>
            </nav>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h3 class="m-0 font-weight-bold text-primary">Personal Information</h3>
                </div>

                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">

                        <div class="row">
                            <div class="col-md-6">

                                <label>Employee Number:</label>
                                <input type="text" name="id_number" class="form-control" required>

                                <label>First Name:</label>
                                <input type="text" name="first_name" class="form-control" required>

                                <label>Last Name:</label>
                                <input type="text" name="last_name" class="form-control" required>

                                <label>Middle Name:</label>
                                <input type="text" name="middle_name" class="form-control">

                                <label>Office Position:</label>
                                <input type="text" name="position" class="form-control">

                                
                                <label>Office:</label>
                                <?php
                                $offices = [];
                                $q1 = $conn_medical->query("SELECT office_name FROM office ORDER BY office_name ASC");
                                while ($row = $q1->fetch_assoc()) $offices[] = $row['office_name'];
                                ?>

                                <select name="course_name" class="form-control" required>
                                    <option value="">-- Select Office --</option>
                                    <?php foreach ($offices as $office): ?>
                                        <option value="<?= htmlspecialchars($office) ?>"><?= htmlspecialchars($office) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <label>Campus:</label>
                                <?php
                                $campuses = [];
                                $q2 = $conn_medical->query("SELECT campus_name FROM campus ORDER BY campus_name ASC");
                                while ($row = $q2->fetch_assoc()) $campuses[] = $row['campus_name'];
                                ?>

                                <select name="student_campus" class="form-control" required>
                                    <option value="">-- Select Campus --</option>
                                    <?php foreach ($campuses as $campus): ?>
                                        <option value="<?= htmlspecialchars($campus) ?>"><?= htmlspecialchars($campus) ?></option>
                                    <?php endforeach; ?>
                                </select>

                            </div>

                            <div class="col-md-6">

                                <label>Upload Employee Image:</label>
                                <input type="file" name="student_photo" class="form-control" accept="image/*">

                                <label>Province:</label>
                                <input type="text" name="province" class="form-control" required>

                                <label>City:</label>
                                <input type="text" name="city" class="form-control" required>

                                <label>Barangay:</label>
                                <input type="text" name="barangay" class="form-control" required>

                                <label>Emergency Contact Person:</label>
                                <input type="text" name="guardian_name" class="form-control" required>

                                <label>Emergency Contact #:</label>
                                <input type="text" name="emergency_contact" class="form-control" placeholder="+63" required>

                            </div>
                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

<script>
<?php 
if ($alert === "success") {
    echo "
    Swal.fire({
        icon: 'success',
        title: 'Employee Added Successfully!',
        text: 'The employee record has been saved.',
        confirmButtonColor: '#327A33'
    }).then(() => {
        window.location.href = window.location.href;
    });
    ";
} elseif (!empty($alert)) {
    echo "
    Swal.fire({
        icon: 'error',
        title: 'Error Saving',
        text: '". addslashes($alert) ."'
    });
    ";
}
?>
</script>

</body>
</html>
