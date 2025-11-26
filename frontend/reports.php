<?php
session_start();
include '../backend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}


// Fetch distinct campuses and courses for filters
$campusQuery = "SELECT DISTINCT campus FROM neust_student_details.students";
$campusResult = mysqli_query($conn_student, $campusQuery);

$courseQuery = "SELECT DISTINCT course_name FROM neustmrdb.medical_location";
$courseResult = mysqli_query($conn_student, $courseQuery);

$campusFilter = isset($_GET['campus']) ? $_GET['campus'] : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';

// Updated query to include only important fields for export
$query = "
    SELECT s.id_number, s.first_name, s.last_name, s.campus AS campus_name, 
           ml.course_name, p.blood_pressure, p.blood_type, p.height_cm, p.weight_kg
    FROM neust_student_details.students s
    LEFT JOIN neustmrdb.medical_record m ON s.student_id = m.student_id
    LEFT JOIN neustmrdb.physical_examination p ON m.medical_id = p.medical_id
    LEFT JOIN neustmrdb.medical_location ml ON m.course_id = ml.campus_id
    WHERE (m.deleted_at IS NULL OR m.deleted_at = '')";

if ($campusFilter) {
    $query .= " AND s.campus = '" . mysqli_real_escape_string($conn_student, $campusFilter) . "'";
}
if ($courseFilter) {
    $query .= " AND ml.course_name = '" . mysqli_real_escape_string($conn_student, $courseFilter) . "'";
}
$result = mysqli_query($conn_student, $query);
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Export logic
if (isset($_POST['export'])) {
    $exportType = $_POST['exportType'];

    if ($exportType === 'students') {
        $query = "SELECT id_number, first_name, last_name, campus FROM students";
    } elseif ($exportType === 'medical') {
        $checkTableQuery = "SHOW TABLES FROM neustmrdb LIKE 'medical_treatment_records'";
        $tableExistsResult = mysqli_query($conn_student, $checkTableQuery);

        if (mysqli_num_rows($tableExistsResult) > 0) {
            $query = "SELECT student_id, chief_complaint, treatment, date_created FROM neustmrdb.medical_treatment_records";
        } else {
            die("Error: The 'medical_treatment_records' table does not exist in the 'neustmrdb' database.");
        }
    } elseif ($exportType === 'dental') {
        $checkTableQuery = "SHOW TABLES FROM neustmrdb LIKE 'dental_records'";
        $tableExistsResult = mysqli_query($conn_student, $checkTableQuery);

        if (mysqli_num_rows($tableExistsResult) > 0) {
            $query = "SELECT student_id, tooth_number, record_details, date_created FROM neustmrdb.dental_records WHERE date_deleted IS NULL";
        } else {
            die("Error: The 'dental_records' table does not exist in the 'neustmrdb' database.");
        }
    }

    $result = mysqli_query($conn_student, $query);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $exportType . '_report.csv');
    $output = fopen('php://output', 'w');
    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit;
}

if (isset($_GET['campus']) || isset($_GET['course']) || isset($_GET['exportType'])) {
    $campusFilter = isset($_GET['campus']) ? $_GET['campus'] : '';
    $courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
    $exportType = isset($_GET['exportType']) ? $_GET['exportType'] : 'students';

    if ($exportType === 'students') {
        $query = "SELECT id_number, first_name, last_name, campus FROM students WHERE 1=1";
        if ($campusFilter) {
            $query .= " AND campus = '" . mysqli_real_escape_string($conn_student, $campusFilter) . "'";
        }
    } elseif ($exportType === 'medical') {
        $query = "SELECT student_id, chief_complaint, treatment, date_created FROM neustmrdb.medical_treatment_records WHERE 1=1";
        if ($campusFilter) {
            $query .= " AND campus = '" . mysqli_real_escape_string($conn_student, $campusFilter) . "'";
        }
        if ($courseFilter) {
            $query .= " AND course_name = '" . mysqli_real_escape_string($conn_student, $courseFilter) . "'";
        }
    } elseif ($exportType === 'dental') {
        $query = "SELECT student_id, tooth_number, record_details, date_created FROM neustmrdb.dental_records WHERE date_deleted IS NULL";
        if ($campusFilter) {
            $query .= " AND campus = '" . mysqli_real_escape_string($conn_student, $campusFilter) . "'";
        }
        if ($courseFilter) {
            $query .= " AND course_name = '" . mysqli_real_escape_string($conn_student, $courseFilter) . "'";
        }
    }

    $result = mysqli_query($conn_student, $query);
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../img/NEUST.png" type="image/png">


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include '../frontend/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg" alt="User Profile">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-primary">Reports</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="mb-4">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label for="campus">Campus:</label>
                                        <select name="campus" id="campus" class="form-control">
                                            <option value="">All Campuses</option>
                                            <?php while ($row = mysqli_fetch_assoc($campusResult)) { ?>
                                                <option value="<?php echo $row['campus']; ?>" <?php echo ($campusFilter == $row['campus']) ? 'selected' : ''; ?>>
                                                    <?php echo $row['campus']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="course">Course:</label>
                                        <select name="course" id="course" class="form-control">
                                            <option value="">All Courses</option>
                                            <?php while ($row = mysqli_fetch_assoc($courseResult)) { ?>
                                                <option value="<?php echo $row['course_name']; ?>" <?php echo ($courseFilter == $row['course_name']) ? 'selected' : ''; ?>>
                                                    <?php echo $row['course_name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="exportType">Export Type:</label>
                                        <select name="exportType" id="exportType" class="form-control">
                                            <option value="students" <?php echo (isset($exportType) && $exportType === 'students') ? 'selected' : ''; ?>>Student Data</option>
                                            <option value="medical" <?php echo (isset($exportType) && $exportType === 'medical') ? 'selected' : ''; ?>>Medical Treatment Records</option>
                                            <option value="dental" <?php echo (isset($exportType) && $exportType === 'dental') ? 'selected' : ''; ?>>Dental Records</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <form method="POST" class="mb-4">
                                <div class="row align-items-end">
                                    <div class="col-md-6">
                                        <button type="submit" name="export" class="btn btn-success w-100">Export</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable">
                                    <thead>
                                        <tr>
                                            <?php if (!empty($students)) {
                                                foreach (array_keys($students[0]) as $header) {
                                                    echo "<th>" . htmlspecialchars($header) . "</th>";
                                                }
                                            } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $row) { ?>
                                            <tr>
                                                <?php foreach ($row as $cell) { ?>
                                                    <td><?php echo htmlspecialchars($cell); ?></td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        document.getElementById('exportType').addEventListener('change', function() {
            const exportType = this.value;
            fetch(`fetch_data.php?exportType=${exportType}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.querySelector('#dataTable tbody');
                    tableBody.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(row => {
                            const tr = document.createElement('tr');
                            Object.values(row).forEach(cell => {
                                const td = document.createElement('td');
                                td.textContent = cell;
                                tr.appendChild(td);
                            });
                            tableBody.appendChild(tr);
                        });
                    } else {
                        const tr = document.createElement('tr');
                        const td = document.createElement('td');
                        td.textContent = 'No data available';
                        td.colSpan = Object.keys(data[0] || {}).length || 1;
                        tr.appendChild(td);
                        tableBody.appendChild(tr);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    </script>
</body>
</html>