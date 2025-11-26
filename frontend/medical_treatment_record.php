<?php
session_start();
include '../backend/db_connect.php';

$user_data = null;
$medical_records = null;
$error_message1 = null;

// Fetch options for the 'Chief Complaint' dropdown from the database.
$query_issues = "SELECT issue_name FROM common_health_issues";
$result_issues = $conn_medical->query($query_issues);
$issues = [];
if ($result_issues && $result_issues->num_rows > 0) {
    while ($row = $result_issues->fetch_assoc()) {
        $issues[] = $row['issue_name'];
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_number'])) {
    $_SESSION['id_number'] = $_POST['id_number'];
    if (!isset($_POST['id_number']) || empty(trim($_POST['id_number']))) {
        $error_message1 = "ID number is required.";
    } else {
        $id_number = htmlspecialchars(trim($_POST['id_number']));

        $query = "SELECT * FROM students WHERE id_number = ?";
        $stmt = $conn_student->prepare($query);
        $stmt->bind_param("s", $id_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();

            $query_records = "SELECT * FROM medical_treatment_records WHERE student_id = ?";
            $stmt_records = $conn_medical->prepare($query_records);
            $stmt_records->bind_param("s", $id_number);
            $stmt_records->execute();
            $result_records = $stmt_records->get_result();

            if ($result_records->num_rows > 0) {
                $medical_records = $result_records->fetch_all(MYSQLI_ASSOC);
            } else {
                $medical_records = [];
            }

            // Check for success message from session
            if (isset($_SESSION['message'])) {
                echo "<script>Swal.fire({ icon: 'success', title: '" . $_SESSION['message'] . "' });</script>";
                unset($_SESSION['message']);
            }
        } else {
            $error_message1 = "User not found.";
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
    <title>Medical Treatment Record Input</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                            <h3 class="m-0 font-weight-bold text-primary"><i
                                    class="fas fa-fw fa-notes-medical"></i>Medical Treatment Record</h3>
                        </div>
                        <div class="card-body">
                            <h4>User Information</h4>
                            <?php if (!empty($error_message1)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message1); ?></div>
                            <?php endif; ?>

                            <form action="../frontend/medical_treatment_record.php" method="POST">
                                <div class="form-group">
                                    <label for="id_number">Student/Employee (Number/Name)</label>
                                    <input type="text" list="studentList" id="id_number" name="id_number"  class="form-control" placeholder="Type Number or Name" required>
                                    <datalist id="studentList"></datalist>

                                </div>


                                <button type="submit" class="btn btn-primary btn-block"
                                    id="filter-button">Filter</button>
                            </form>

                            <?php if ($user_data): ?>
                                <h5 class="mt-4">User Details</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Id Number</th>
                                                <th>Name</th>
                                                <th>Campus</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user_data['id_number']); ?></td>
                                                <td><?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['middle_name'] . ' ' . $user_data['last_name']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($user_data['campus']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if ($medical_records): ?>
                                    <h5 class="mt-4">Medical Treatment Records</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Chief Complaint</th>
                                                    <th>Treatment</th>
                                                    <th>Date Created</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBody">
                                                <!-- Rows will be dynamically populated -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav>
                                        <ul class="pagination" id="pagination">
                                            <!-- Pagination buttons will be dynamically populated -->
                                        </ul>
                                    </nav>

                                    <script>
                                        const recordsPerPage = 5;
                                        const medicalRecords = <?php echo json_encode(array_reverse($medical_records)); ?>;
                                        let currentPage = 1;

                                        function renderTable() {
                                            const tableBody = document.getElementById('tableBody');
                                            tableBody.innerHTML = '';

                                            const startIndex = (currentPage - 1) * recordsPerPage;
                                            const endIndex = startIndex + recordsPerPage;
                                            const paginatedRecords = medicalRecords.slice(startIndex, endIndex);

                                            paginatedRecords.forEach(record => {
                                                const row = document.createElement('tr');
                                                row.innerHTML = `
                                                    <td>${record.date}</td>
                                                    <td>${record.chief_complaint}</td>
                                                    <td>${record.treatment}</td>
                                                    <td>${record.date_created}</td>
                                                `;
                                                tableBody.appendChild(row);
                                            });
                                        }

                                        function renderPagination() {
                                            const pagination = document.getElementById('pagination');
                                            pagination.innerHTML = '';

                                            const totalPages = Math.ceil(medicalRecords.length / recordsPerPage);

                                            for (let i = 1; i <= totalPages; i++) {
                                                const pageItem = document.createElement('li');
                                                pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
                                                pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                                                pageItem.addEventListener('click', (e) => {
                                                    e.preventDefault();
                                                    currentPage = i;
                                                    renderTable();
                                                    renderPagination();
                                                });
                                                pagination.appendChild(pageItem);
                                            }
                                        }

                                        // Initial render
                                        renderTable();
                                        renderPagination();
                                    </script>
                                <?php endif; ?>

                                <!-- Separate Button to Show Modal -->


                                <!-- Add Medical Treatment Record Modal -->
                                <div class="modal fade" id="addTreatmentModal" tabindex="-1"
                                    aria-labelledby="addTreatmentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addTreatmentModalLabel">Add Medical Treatment
                                                    Record</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="../backend/insert_medical_record.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="student_id"
                                                        value="<?php echo htmlspecialchars($user_data['id_number']); ?>">
                                                    <input type="hidden" name="first_name"
                                                        value="<?php echo htmlspecialchars($user_data['first_name']); ?>">
                                                    <input type="hidden" name="middle_name"
                                                        value="<?php echo htmlspecialchars($user_data['middle_name']); ?>">
                                                    <input type="hidden" name="last_name"
                                                        value="<?php echo htmlspecialchars($user_data['last_name']); ?>">

                                                    <div class="form-group">
                                                        <label for="date">Date</label>
                                                        <input type="date" name="date" class="form-control"
                                                            value="<?php echo date('Y-m-d'); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="chief_complaint">Chief Complaint</label>
                                                        <select name="chief_complaint" id="chief_complaint"
                                                            class="form-control" required onchange="toggleOtherField()">
                                                            <option value="">Select a complaint</option>
                                                            <?php foreach ($issues as $issue): ?>
                                                                <option value="<?php echo htmlspecialchars($issue); ?>">
                                                                    <?php echo htmlspecialchars($issue); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" id="other_complaint_field"
                                                        style="display: none;">
                                                        <label for="other_complaint">Specify Other Complaint</label>
                                                        <input type="text" name="other_complaint" id="other_complaint"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="treatment">Treatment</label>
                                                        <textarea name="treatment" class="form-control" rows="3"
                                                            required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Submit Record</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trigger Button for Modal -->
                                <button type="button" class="btn btn-primary btn-block" data-bs-toggle="modal"
                                    data-bs-target="#addTreatmentModal">
                                    Add Medical Treatment Record
                                </button>

                                <form action="medical_certificate.php" method="GET">
                                    <input type="hidden" name="id_number"
                                        value="<?php echo htmlspecialchars($user_data['id_number']); ?>">
                                    <button type="submit" class="btn btn-success btn-block">
                                        Generate Medical Certificate <i class="fa fa-file-pdf"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
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


    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
    <script>
        function toggleOtherField() {
            const complaintDropdown = document.getElementById('chief_complaint');
            const otherField = document.getElementById('other_complaint_field');
            if (complaintDropdown.value === 'Other') {
                otherField.style.display = 'block';
            } else {
                otherField.style.display = 'none';
                document.getElementById('other_complaint').value = '';
            }
        }
    </script>
    <script>
        <?php if (isset($_SESSION['message1'])): ?>
            Swal.fire({
                icon: "<?php echo $_SESSION['message_type1']; ?>",
                title: "<?php echo $_SESSION['message1']; ?>"
            });
            <?php unset($_SESSION['message1']); ?>
        <?php endif; ?>
    </script>
    <?php if (isset($_SESSION['message']) && $_SESSION['message_type'] === 'success'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: "success",
                    title: "<?php echo $_SESSION['message']; ?>",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('filter-button').click();
                    }
                });
            });
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_GET['id_number']) && isset($_SESSION['message']) && $_SESSION['message_type'] === 'success'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const idNumberField = document.querySelector('input[name="id_number"]');
                const form = idNumberField.closest('form');
                if (idNumberField.value.trim() !== '') {
                    form.submit();
                }
            });
        </script>


    <?php endif; ?>
</body>

</html>