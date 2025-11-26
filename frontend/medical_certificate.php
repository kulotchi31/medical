<?php
session_start();
require '../backend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$id_number = isset($_GET['id_number']) ? trim($_GET['id_number']) : '';

$student_data = null;

if (!empty($id_number)) {
    $query = "SELECT * FROM students WHERE id_number = ?";
    $stmt = $conn_student->prepare($query);
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $student_data = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../img/NEUST.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Medical Certificate</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <style>
        .container-fluid {
            max-width: 700px;
            margin: auto;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body id="page-top">

<div id="wrapper">
    <?php include 'sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"></nav>

            <div class="container-fluid" style="max-width: 1500px; margin: auto;">
    <div class="row">
        <!-- Form Section -->
        <div class="col-auto">
            <div class="card shadow mb-4">
            <div class="card-header py-3">
            <h3>Generate Medical Certificate</h3>
            <hr>
            <div class="alert alert-info">
                <strong>Note:</strong> Fill out the form below to generate a medical certificate for a student.
            </div>

            <form id="medicalForm" action="../backend/medical_certificate_pdf.php" method="POST">
                <div class="form-group">
                    <label for="id_number">Student ID Number:</label>
                    <div class="input-group">
                        <input type="text" list="studentList" id="id_number" name="id_number"  class="form-control" placeholder="Type Number or Name" value="<?php echo htmlspecialchars($id_number); ?>" required>
                        <datalist id="studentList"></datalist>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" onclick="fetchStudentDetails()">Fetch</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($student_data['first_name']) ? htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']) : ''; ?>" placeholder="Student Name" readonly>
                </div>

                <div class="form-group">
                    <label for="campus">Campus Name:</label>
                    <input type="text" class="form-control" id="campus" name="campus" value="<?php echo isset($student_data['campus']) ? htmlspecialchars($student_data['campus']) : ''; ?>" placeholder="Campus Name" readonly>
                </div>

                <div class="form-group">
                    <label for="exam_date">Examination Date:</label>
                    <input type="date" class="form-control" id="exam_date" name="exam_date" required>
                </div>

                <div class="form-group">
                    <label>Certificate Type:</label>
                    <div>
                        <input type="radio" id="good_condition" name="certificate_type" value="good_condition" checked onclick="toggleRemarks()">
                        <label for="good_condition">physically fit</label>
                        <input type="radio" id="medical_issue" name="certificate_type" value="medical_issue" onclick="toggleRemarks()">
                        <label for="medical_issue">Medical Issue</label>
                    </div>
                </div>

                <div class="form-group hidden" id="remarksSection">
                    <label for="diagnosis">Chief Complaint:</label>
                    <textarea class="form-control" id="diagnosis" name="diagnosis" rows="2"></textarea>

                    <label for="treatment">Treatment:</label>
                    <textarea class="form-control" id="treatment" name="treatment" rows="2"></textarea>
                </div>
                <button type="button" class="btn btn-secondary" onclick="previewCertificate()">
                    Preview Certificate <i class="fa fa-eye"></i>
                </button>
                <button type="submit" class="btn btn-primary">
                    Generate Certificate <i class="fa fa-print"></i>
                </button>
            </form>
        </div>
    </div>
</div>

        <!-- Preview Section -->
        <div class="col-md-6">
        <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div id="pdfPreview" style="border: 1px solid #ddd; padding: 10px; height: 700px; overflow: auto;">
                <iframe id="pdfIframe" style="width: 100%; height: 100%;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
</div></div>
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


<script>
    function fetchStudentDetails() {
        let idNumber = document.getElementById('id_number').value.trim();
        if (idNumber === '') {
            alert('Please enter an ID number.');
            return;
        }
        fetch(`../backend/fetch_student_details.php?id_number=${idNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('name').value = data.name;
                    document.getElementById('campus').value = data.campus;
                } else {
                    alert('Student not found!');
                    document.getElementById('name').value = '';
                    document.getElementById('campus').value = '';
                }
            })
            .catch(error => console.error('Error fetching student details:', error));
    }

    function toggleRemarks() {
        let isMedicalIssue = document.getElementById('medical_issue').checked;
        document.getElementById('remarksSection').classList.toggle('hidden', !isMedicalIssue);

        // Make diagnosis & treatment required if "Medical Issue" is selected
        document.getElementById('diagnosis').required = isMedicalIssue;
        document.getElementById('treatment').required = isMedicalIssue;
    }

    function previewCertificate() {
        const form = document.getElementById('medicalForm');
        const formData = new FormData(form);
        const queryString = new URLSearchParams(formData).toString();

        // Show the preview section
        document.getElementById('pdfPreview').style.display = 'block';

        // Load the PDF preview in the iframe
        document.getElementById('pdfIframe').src = `../backend/medical_certificate_pdf.php?preview=true&${queryString}`;
    }
</script>

</body>
</html>