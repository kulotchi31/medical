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
<link rel="icon" href="../img/NEUST.png" type="image/png">
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
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Medical Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editModalContent">
                <form id="editMedicalForm" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            <div class="form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="${data.first_name || ''}">
                            </div>
                            <div class="form-group">
                                <label for="middle_name">Middle Name:</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name" value="${data.middle_name || ''}">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="${data.last_name || ''}">
                            </div>
                            <div class="form-group">
                                <label for="guardian_name">Guardian Name:</label>
                                <input type="text" class="form-control" id="guardian_name" name="guardian_name" value="${data.guardian_name || ''}">
                            </div>
                            <div class="form-group">
                                <label for="emergency_contact">Emergency Contact:</label>
                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="${data.emergency_contact || ''}">
                            </div>
                        </div>

                        <!-- Health Conditions -->
                        <div class="col-md-6">
                            <h5>Health Conditions</h5>
                            <div class="form-group">
                                <label for="allergy">Allergy:</label>
                                <input type="text" class="form-control" id="allergy" name="allergy" value="${data.allergy || ''}">
                            </div>
                            <div class="form-group">
                                <label for="asthma">Asthma:</label>
                                <select class="form-control" id="asthma" name="asthma">
                                    <option value="1" ${data.asthma == "1" ? 'selected' : ''}>Yes</option>
                                    <option value="0" ${data.asthma == "0" ? 'selected' : ''}>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="diabetes">Diabetes:</label>
                                <input type="text" class="form-control" id="diabetes" name="diabetes" value="${data.diabetes || ''}">
                            </div>
                            <div class="form-group">
                                <label for="heart_disease">Heart Disease:</label>
                                <input type="text" class="form-control" id="heart_disease" name="heart_disease" value="${data.heart_disease || ''}">
                            </div>
                            <div class="form-group">
                                <label for="other_HC">Other Conditions:</label>
                                <textarea class="form-control" id="other_HC" name="other_HC" rows="2">${data.other_HC || ''}</textarea>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="col-md-12">
                            <h5>Update Image</h5>
                            <div class="form-group">
                                <label for="medical_image">Medical Image:</label>
                                <input type="file" class="form-control" id="medical_image" name="medical_image" accept="image/*">
                            </div>
                            <div class="form-group text-center">
                                <img id="preview_image" src="../<?php echo isset($data['medical_image']) && $data['medical_image'] ? $data['medical_image'] : ''; ?>" alt="Medical Image" class="img-fluid rounded" style="max-width: 200px; display: <?php echo isset($data['medical_image']) && $data['medical_image'] ? 'block' : 'none'; ?>;">
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
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
    <div class="row">
        <!-- Personal Information -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Personal Information</div>
                <div class="card-body text-center">
                    ${studentPhotoHTML}
                    <h5 class="mt-2">${data.first_name || ''} ${data.middle_name ? data.middle_name + '.' : ''} ${data.last_name || ''}</h5>
                    <p class="fw-bold text-muted">${data.id_number || 'N/A'}</p>

                    <table class="table table-bordered">
                        <tr><th>S.Y.</th><td>${data.school_year || 'N/A'}</td></tr>
                        <tr><th>Guardian</th><td>${data.guardian_name || 'N/A'}</td></tr>
                        <tr><th>Emergency #</th><td>${data.emergency_contact || 'N/A'}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Health Conditions -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Health Conditions</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th>Allergy</th><td>${data.allergy || '<span class="text-muted">None</span>'}</td></tr>
                        <tr><th>Asthma</th><td>${data.asthma ? 'Yes' : 'No'}</td></tr>
                        <tr><th>Diabetes</th><td>${data.diabetes || 'None'}</td></tr>
                        <tr><th>Heart Disease</th><td>${data.heart_disease || '<span class="text-muted">None</span>'}</td></tr>
                        <tr><th>Other Conditions</th><td>${data.other_HC || '<span class="text-muted">None</span>'}</td></tr>
                        <tr><th>Smoking</th><td>${data.smoking || 'N/A'}</td></tr>
                        <tr><th>Drinking Liquor</th><td>${data.liquor_drinking || 'N/A'}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Physical Examination -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Physical Examination</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th>Blood Pressure</th><td>${data.blood_pressure || 'N/A'}</td></tr>
                        <tr><th>Blood Type</th><td>${data.blood_type || 'Unknown'}</td></tr>
                        <tr><th>Height</th><td>${data.height_cm ? data.height_cm + ' cm' : 'N/A'}</td></tr>
                        <tr><th>Weight</th><td>${data.weight_kg ? data.weight_kg + ' kg' : 'N/A'}</td></tr>
                        <tr><th>Exam Date</th><td>${data.exam_date || 'N/A'}</td></tr>
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


function deleteRecord(medical_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this action!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../backend/delete_medical_record.php',
                type: 'POST',
                data: { medical_id: medical_id },
                success: function(response) {
                    Swal.fire({
                        title: "Deleted!",
                        text: "The medical record has been deleted.",
                        icon: "success",
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to delete the record. Please try again.",
                        icon: "error",
                        confirmButtonColor: "#d33"
                    });
                }
            });
        }
    });
}
     

// Handle image preview when selecting a new file
document.getElementById('medical_image').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const previewImg = document.getElementById('preview_image');

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.style.display = 'none';
        previewImg.src = ''; // Clear the preview
    }
});

function editRecord(medical_id) {
    if (!medical_id) {
        console.error("Invalid medical_id provided.");
        Swal.fire({
            title: "Error!",
            text: "Invalid medical record ID.",
            icon: "error",
            confirmButtonColor: "#d33"
        });
        return;
    }

    const modalContent = document.getElementById('editModalContent');
    modalContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p>Loading...</p>
        </div>
    `;

    fetch(`../backend/fetch_medical_details.php?id=${encodeURIComponent(medical_id)}`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (!data || Object.keys(data).length === 0) {
                throw new Error("No data found for the provided medical_id.");
            }

            modalContent.innerHTML = `
                <form id="editMedicalForm" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            <div class="form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="${data.first_name || ''}">
                            </div>
                            <div class="form-group">
                                <label for="middle_name">Middle Name:</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name" value="${data.middle_name || ''}">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="${data.last_name || ''}">
                            </div>
                            <div class="form-group">
                                <label for="guardian_name">Guardian Name:</label>
                                <input type="text" class="form-control" id="guardian_name" name="guardian_name" value="${data.guardian_name || ''}">
                            </div>
                            <div class="form-group">
                                <label for="emergency_contact">Emergency Contact:</label>
                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="${data.emergency_contact || ''}">
                            </div>
                        </div>

                        <!-- Health Conditions -->
                        <div class="col-md-6">
                            <h5>Health Conditions</h5>
                            <div class="form-group">
                                <label for="allergy">Allergy:</label>
                                <input type="text" class="form-control" id="allergy" name="allergy" value="${data.allergy || ''}">
                            </div>
                            <div class="form-group">
                                <label for="asthma">Asthma:</label>
                                <select class="form-control" id="asthma" name="asthma">
                                    <option value="1" ${data.asthma == "1" ? 'selected' : ''}>Yes</option>
                                    <option value="0" ${data.asthma == "0" ? 'selected' : ''}>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="diabetes">Diabetes:</label>
                                <input type="text" class="form-control" id="diabetes" name="diabetes" value="${data.diabetes || ''}">
                            </div>
                            <div class="form-group">
                                <label for="heart_disease">Heart Disease:</label>
                                <input type="text" class="form-control" id="heart_disease" name="heart_disease" value="${data.heart_disease || ''}">
                            </div>
                            <div class="form-group">
                                <label for="other_HC">Other Conditions:</label>
                                <textarea class="form-control" id="other_HC" name="other_HC" rows="2">${data.other_HC || ''}</textarea>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="col-md-6">
                            <h5>Update Image</h5>
                            <div class="form-group">
                                <label for="medical_image">Medical Image:</label>
                                                            <div class="form-group text-center">
                                <img id="preview_image" src="../${data.medical_image || ''}" alt="Medical Image" class="img-fluid rounded" style="max-width: 200px; display: ${data.medical_image ? 'block' : 'none'};">
                            </div>
                                <input type="file" class="form-control" id="medical_image" name="medical_image" accept="image/*">
                            </div>

                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveChangesButton">Save Changes</button>
                    </div>
                </form>
            `;

            document.getElementById('editMedicalForm').addEventListener('submit', function (event) {
                event.preventDefault();
                saveMedicalRecord(medical_id);
            });

            // Handle image preview when selecting a new file
            document.getElementById('medical_image').addEventListener('change', function (event) {
                const file = event.target.files[0];
                const previewImg = document.getElementById('preview_image');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewImg.style.display = 'none';
                    previewImg.src = ''; // Clear the preview
                }
            });
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            modalContent.innerHTML = `<p class="text-danger">Failed to load data. Please try again.</p>`;
        });

    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

function saveMedicalRecord(medical_id) {
    const formData = new FormData(document.getElementById('editMedicalForm'));
    formData.append('medical_id', medical_id);

    fetch(`../backend/update_medical_record.php`, {
        method: 'POST',
        body: formData,
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "Success!",
                    text: data.message,
                    icon: "success",
                    confirmButtonColor: "#3085d6"
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message || "An unknown error occurred.",
                    icon: "error",
                    confirmButtonColor: "#d33"
                });
            }
        })
}

</script>

</body>
</html>
