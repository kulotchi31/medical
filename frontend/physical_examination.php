<?php
session_start();
include '../backend/db_connect.php';

$user_data = null;
$physical_exam = null;
$error_message = null;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['id_number']) || empty(trim($_POST['id_number']))) {
        $error_message = "ID number is required.";
    } else {
        $id_number = htmlspecialchars(trim($_POST['id_number']));

        // Fetch student data
        $query = "SELECT * FROM students WHERE id_number = ?";
        $stmt = $conn_student->prepare($query);
        $stmt->bind_param("s", $id_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();

            // Fetch medical_id from medical_records
            $query_medical = "SELECT medical_id FROM medical_record WHERE student_id = ?";
            $stmt_medical = $conn_medical->prepare($query_medical);
            $stmt_medical->bind_param("i", $user_data['student_id']);
            $stmt_medical->execute();
            $result_medical = $stmt_medical->get_result();

            if ($result_medical->num_rows > 0) {
                $medical_data = $result_medical->fetch_assoc();
                $medical_id = $medical_data['medical_id'];

                // Fetch the physical examination record using medical_id
                $query_exam = "SELECT * FROM physical_examination WHERE medical_id = ?";
                $stmt_exam = $conn_medical->prepare($query_exam);
                $stmt_exam->bind_param("i", $medical_id);
                $stmt_exam->execute();
                $result_exam = $stmt_exam->get_result();

                if ($result_exam->num_rows > 0) {
                    $physical_exam = $result_exam->fetch_assoc();
                } else {
                    $error_message = "No physical examination records found.";
                }
            } else {
                $error_message = "Medical record not found.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../img/NEUST.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Physical Examination</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <h3 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-md"></i> Physical Examination</h3>
                    </div>
                    <div class="card-body">
                        <form action="physical_examination.php" method="POST">
                            <div class="form-group">
                                <label for="id_number">ID Number</label>
                                <input type="text" list="studentList" id="id_number" name="id_number"  class="form-control" placeholder="Type Number or Name" required>
                                    <datalist id="studentList"></datalist>
                            </div>
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>

                        <?php if ($user_data): ?>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="m-0 font-weight-bold text-primary">Student Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="physicalExam"><strong>ID Number:</strong> <?php echo htmlspecialchars($user_data['id_number']); ?></p>
                                            <p class="physicalExam"><strong>Name:</strong> <?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['middle_name'] . ' ' . $user_data['last_name']); ?></p>
                                            <p class="physicalExam"><strong>Guardian:</strong> <?php echo htmlspecialchars($user_data['guardian_name']); ?></p>
                                            <p class="physicalExam"><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($user_data['emergency_contact']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <?php if ($physical_exam): ?>
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="m-0 font-weight-bold text-primary">Physical Examination Record</h5>
                                            </div>
                                            <div class="card-body">
                                                <p class="physicalExam"><strong>Exam Date:</strong> <?php echo htmlspecialchars($physical_exam['exam_date']); ?></p>
                                                <p class="physicalExam"><strong>Height (cm):</strong> <?php echo htmlspecialchars($physical_exam['height_cm']); ?></p>
                                                <p class="physicalExam"><strong>Weight (kg):</strong> <?php echo htmlspecialchars($physical_exam['weight_kg']); ?></p>
                                                <p class="physicalExam"><strong>Blood Pressure:</strong> <?php echo htmlspecialchars($physical_exam['blood_pressure']); ?></p>
                                                <p class="physicalExam"><strong>Smoking:</strong> <?php echo $physical_exam['smoking'] ? 'Yes' : 'No'; ?></p>
                                                <p class="physicalExam"><strong>Liquor Drinking:</strong> <?php echo $physical_exam['liquor_drinking'] ? 'Yes' : 'No'; ?></p>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="text-danger">No physical examination records found.</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#physicalExamModal">
                                Add Physical Examination Record
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Physical Examination Input -->
<div class="modal fade" id="physicalExamModal" tabindex="-1" role="dialog" aria-labelledby="physicalExamModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="physicalExamModalLabel">Enter Physical Examination Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="../backend/save_physical_exam.php" method="POST">
                    <input type="hidden" name="medical_id" value="<?php echo $medical_id ?? ''; ?>">
                    <div class="form-group">
                        <label for="exam_date">Exam Date</label>
                        <input type="date" name="exam_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="height_cm">Height (cm)</label>
                        <input type="number" step="0.1" name="height_cm" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="weight_kg">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight_kg" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="blood_pressure">Blood Pressure</label>
                        <input type="text" name="blood_pressure" class="form-control" required>
                    </div>
                    <div> 
                        <label>Smoking:</label>
                        <input type="radio" name="smoking" value="1"> Yes
                        <input type="radio" name="smoking" value="0" checked> No
                    <br>                  
                        <label>Liquor Drinking:</label>
                        <input type="radio" name="liquor_drinking" value="1"> Yes
                        <input type="radio" name="liquor_drinking" value="0" checked> No       
                    </div>

                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
   <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../vendor/chart.js/Chart.min.js"></script>
    
    <script>
        document.getElementById('id_number').addEventListener('input', function () {
            let val = this.value;
            if (val.length > 0) {
                fetch('searchid.php?q=' + encodeURIComponent(val))
                    .then(res => res.json())
                    .then(data => {
                        let datalist = document.getElementById('studentList');
                        datalist.innerHTML = '';
                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.id_number;
                            option.textContent = item.full_name;
                            datalist.appendChild(option);
                        });
                    });
            }
        });
    </script>
</body>
</html>