<?php
session_start();
include '../backend/db_connect.php';



if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$campusQuery = "SELECT DISTINCT campus FROM neust_student_details.students";
$campusResult = mysqli_query($conn_student, $campusQuery);

$courseQuery = "SELECT DISTINCT course_name FROM neustmrdb.medical_location";
$courseResult = mysqli_query($conn_student, $courseQuery);

$campusFilter = isset($_GET['campus']) ? $_GET['campus'] : '';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : '';

$query = "
    SELECT s.student_id, s.id_number, s.first_name, s.middle_name, s.last_name, s.campus AS campus_name, 
           ml.course_name, m.medical_id, 
           p.blood_pressure, p.blood_type, m.allergy, m.asthma, m.diabetes, m.heart_disease, 
           m.other_HC, m.medication, p.exam_date, p.height_cm, p.weight_kg
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
if (!$result) {
    die("Query failed: " . mysqli_error($conn_student));
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Medical Records</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body id="page-top">
                    <div id="wrapper">
                        <?php include '../frontend/sidebar.php'; ?>
                         <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
           
            </nav>
                        <div id="content-wrapper" class="d-flex flex-column">
                            <div id="content">
                                <div class="container-fluid"> 
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            
                                            <h3 class="m-0 font-weight-bold text-primary">Student & Medical Records</h3>
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
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </div>
</form>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID Number</th>
                                        <th>Name</th>
                                        <th>Campus</th>
                                        <th>Course</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                           <td><?php echo $row['id_number']; ?></td>
                                            <td><?php echo $row['first_name'] . " " . $row['middle_name'] . ". " . $row['last_name']; ?></td>
                                           <td><?php echo $row['campus_name'] ?? 'N/A'; ?></td>
                                            <td><?php echo $row['course_name'] ?? 'N/A'; ?></td>
                                        <td>
                                         <button class="btn btn-primary" onclick="showDetails(<?php echo $row['medical_id']; ?>)" title="View Details">
                                          <i class="fas fa-eye"></i> 
                                       </button>
                                         <button class="btn btn-warning" onclick="editRecord(<?php echo $row['medical_id']; ?>)" title="Edit Record">
                                         <i class="fas fa-edit"></i> 
                                          </button>
                                         <button class="btn btn-danger" onclick="deleteRecord(<?php echo $row['medical_id']; ?>)" title="Delete Record">
                                         <i class="fas fa-trash"></i> 
                                        </button>
                                        </td>
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

<div class="modal fade" id="medicalModal" tabindex="-1" aria-labelledby="medicalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="medicalModalLabel">Medical Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent">       
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="modal fade" id="editMedicalModal" tabindex="-1" aria-labelledby="editMedicalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-x2"> <!-- Wider modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Medical Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMedicalForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit_medical_id" name="medical_id">

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <label class="form-label">Upload Student Photo:</label>
                            <input type="file" name="student_photo" id="student_photo" class="form-control" accept="image/*" >
                            <div class="mt-3">
                                <img id="preview_image" src="" alt="Student Photo" class="img-thumbnail" style="max-width: 200px; display: none;">
                            </div>
                        </div>
                    </div>
              
                    <div class="row">
                        <!-- First Column -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="edit_middle_name" name="middle_name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="edit_last_name" name="last_name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">School Year</label>
                                <input type="text" class="form-control" id="edit_school_year" name="school_year">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Guardian</label>
                                <input type="text" class="form-control" id="edit_guardian" name="guardian">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Emergency Contact #</label>
                                <input type="text" class="form-control" id="edit_emergency_number" name="emergency_number">
                            </div>
                        </div>

                        <!-- Second Column -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Blood Type</label>
                                <select class="form-control" id="edit_blood_type" name="blood_type">
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Allergy</label>
                                <input type="text" class="form-control" id="edit_allergy" name="allergy">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Asthma</label>
                                <div>
                                    <input type="radio" id="asthma_yes" name="asthma" value="Yes">
                                    <label for="asthma_yes">Yes</label>
                                    <input type="radio" id="asthma_no" name="asthma" value="No" checked>
                                    <label for="asthma_no">No</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Diabetes</label>
                                <select class="form-control" id="edit_diabetes" name="diabetes">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Heart Disease</label>
                                <select class="form-control" id="edit_heart_disease" name="heart_disease">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Other Conditions</label>
                                <input type="text" class="form-control" id="edit_other_conditions" name="other_conditions">
                            </div>
                        </div>

                        <!-- Third Column -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Smoking</label>
                                <input type="checkbox" id="edit_smoking" name="smoking" value="Yes">
                            
                           &emsp;
                                <label class="form-label">Drinking Liquor</label>
                                <input type="checkbox" id="edit_drinking" name="drinking" value="Yes">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Blood Pressure</label>
                                <input type="text" class="form-control" id="edit_blood_pressure" name="blood_pressure">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Height (cm)</label>
                                <input type="text" class="form-control" id="edit_height" name="height">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="text" class="form-control" id="edit_weight" name="weight">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vaccine Dose:</label><br>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="dose_number" value="1" id="first_dose">
                                    <label class="form-check-label" for="first_dose">First Dose</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="dose_number" value="2" id="second_dose">
                                    <label class="form-check-label" for="second_dose">Second Dose</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="dose_number" value="3" id="first_booster">
                                    <label class="form-check-label" for="first_booster">First Booster</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="dose_number" value="4" id="second_booster">
                                    <label class="form-check-label" for="second_booster">Second Booster</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="dose_number" value="0" id="no_vaccine">
                                    <label class="form-check-label" for="no_vaccine">Did not take vaccine</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Examination Date:</label>
                                <input type="date" name="exam_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>


  

    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../vendor/chart.js/Chart.min.js"></script>




<script>

$(document).ready(function() {
    $('#dataTable').DataTable();
});

function showDetails(medical_id) {
    let modalBody = document.getElementById("modalContent");

    modalBody.innerHTML = `<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p>Loading...</p></div>`;

    fetch(`../backend/fetch_medical_details.php?id=${medical_id}`)
        .then(response => response.json())
        .then(data => {
            let studentPhotoHTML = data.student_photo 
                ? `<img src="../${data.student_photo}" alt="Student Photo" class="img-fluid rounded-circle shadow" width="200" height="200">` 
                : '<p class="text-muted">No Student Photo</p>';

modalBody.innerHTML = `
    <div class="row mt-3">
        <!-- Left Column: Student Photo & Contact Details -->
        <div class="col-md-4 text-center">
            ${studentPhotoHTML}
           <p class="mt-2 fw-bold" style="color: black;"> ${data.first_name || ''} ${data.middle_name ? data.middle_name + '.' : ''} ${data.last_name || ''}</p>
            <p  class="mt-2 fw-bold" style="color: black;"> ${data.id_number || 'N/A'}</p>
            <table class="table table-bordered">
                <tr><th>S.Y.</th><td>${data.school_year || 'N/A'}</td></tr>
                <tr><th>Guardian</th><td>${data.guardian_name || 'N/A'}</td></tr>
                <tr><th>Emergency #</th><td>${data.emergency_contact || 'N/A'}</td></tr>
            </table>
        </div>

        <!-- Right Column: Two Tables -->
        <div class="col-md-8">
            <div class="row">
                <!-- First Table (Left Side) -->
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr><th>Allergy</th><td>${data.allergy ? data.allergy : '<span class="text-muted">None</span>'}</td></tr>
                        <tr><th>Asthma</th><td>${data.asthma ? 'Yes' : 'No'}</td></tr>
                        <tr><th>Diabetes</th><td>${data.diabetes ? data.diabetes : 'None'}</td></tr>
                        <tr><th>Heart Disease</th><td>${data.heart_disease ? data.heart_disease : '<span class="text-muted">None</span>'}</td></tr>
                        <tr><th>Other Conditions</th><td>${data.other_HC ? data.other_HC : '<span class="text-muted">None</span>'}</td></tr>
                        <tr><th>Smoking</th><td>${data.smoking || 'N/A'}</td></tr>
                        <tr><th>Drinking Liquor</th><td>${data.liquor_drinking || 'N/A'}</td></tr>


                    </table>
                </div>

                <!-- Second Table (Right Side) -->
                <div class="col-md-6">
                    <table class="table table-bordered">

                        <tr><th>Blood Pressure</th><td>${data.blood_pressure || 'N/A'}</td></tr>
                        <tr><th>Blood Type</th><td>${data.blood_type || 'Unknown'}</td></tr>
                        <tr><th>Height</th><td>${data.height_cm ? data.height_cm + ' cm' : 'N/A'}</td></tr>
                        <tr><th>Weight</th><td>${data.weight_kg ? data.weight_kg + ' kg' : 'N/A'}</td></tr>
                        <tr><th>Medication</th><td>${data. medication ? data.medication : '<span class="text-muted">None</span>'}</td></tr>
                        <tr><th>Examination Date</th><td>${data.exam_date || 'N/A'}</td></tr>
                        <tr><th>Vaccine Record</th><td>${data.vaccine_status || 'N/A'}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
            `;

            let modal = new bootstrap.Modal(document.getElementById("medicalModal"));
            modal.show();
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            modalBody.innerHTML = `<p class="text-danger">Failed to load data. Please try again.</p>`;
        });
}

// Function to edit a medical record
function editRecord(medical_id) {
    fetch(`../backend/fetch_medical_details.php?id=${medical_id}`)
        .then(response => response.json())
        .then(data => {
            $('#edit_medical_id').val(data.medical_id);
            $('#edit_allergy').val(data.allergy);
            $('#edit_asthma').val(data.asthma);
            $('#edit_diabetes').val(data.diabetes);
            $('#edit_heart_disease').val(data.heart_disease);
            $('#edit_other_HC').val(data.other_HC);
            $('#edit_medication').val(data.medication);
            
            let modal = new bootstrap.Modal(document.getElementById("editMedicalModal"));
            modal.show();
        })
        .catch(error => console.error("Error fetching data:", error));
}

function deleteRecord(medical_id) {
    if (confirm("Are you sure you want to delete this medical record?")) {
        $.ajax({
            url: '../backend/delete_medical_record.php',
            type: 'POST',
            data: { medical_id: medical_id },
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function() {
                alert("Failed to delete record.");
            }
        });
    }
}       





    document.getElementById('student_photo').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_image').src = e.target.result;
            document.getElementById('preview_image').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('preview_image').style.display = 'none';
    }
});


$("#editMedicalForm").submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: '../backend/update_medical_record.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            alert(response);
            location.reload();
        },
        error: function() {
            alert("Failed to update record.");
        }
    });
});


function deleteRecord(medical_id) {
    Swal.fire({
        title: "Are you sure?",
        html: '<i class="fas fa-trash fa-3x" style="color: #d33;"></i><br><br>This action cannot be undone!',
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
       
            window.location.href = `../backend/delete_medical_record.php?id=${medical_id}`;
        }
    });
}
function editRecord(medical_id) {
    fetch(`../backend/fetch_medical_details.php?id=${medical_id}`)
        .then(response => response.json())
        .then(data => {
            $('#edit_medical_id').val(data.medical_id);
            $('#edit_first_name').val(data.first_name);
            $('#edit_middle_name').val(data.middle_name);
            $('#edit_last_name').val(data.last_name);
            $('#edit_school_year').val(data.school_year);
            $('#edit_guardian').val(data.guardian_name);
            $('#edit_emergency_number').val(data.emergency_contact);
            
            $('#edit_blood_type').val(data.blood_type);
            $('#edit_allergy').val(data.allergy);
            $('#edit_diabetes').val(data.diabetes);
            $('#edit_heart_disease').val(data.heart_disease);
            $('#edit_other_conditions').val(data.other_HC);
            $('#edit_blood_pressure').val(data.blood_pressure);
            $('#edit_height').val(data.height_cm);
            $('#edit_weight').val(data.weight_kg);
            $('#edit_medication').val(data.medication);
            
            
            // Handling radio buttons for Asthma
            if (data.asthma === "Yes") {
                $('#asthma_yes').prop('checked', true);
            } else {
                $('#asthma_no').prop('checked', true);
            }

            // Handling checkboxes for Smoking and Drinking
            $('#edit_smoking').prop('checked', data.smoking === "Yes");
            $('#edit_drinking').prop('checked', data.liquor_drinking === "Yes");

            // Handling radio buttons for Vaccine Dose
            if (data.dose_number === "1") {
                $('#first_dose').prop('checked', true);
            } else if (data.dose_number === "2") {
                $('#second_dose').prop('checked', true);
            } else {
                $('input[name="dose_number"]').prop('checked', false); // Uncheck all if no value
            }

            let modal = new bootstrap.Modal(document.getElementById("editMedicalModal"));
            modal.show();
        })
        .catch(error => console.error("Error fetching data:", error));
}

</script>

</body>
</html>
