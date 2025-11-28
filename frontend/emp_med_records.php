<?php
session_start();
include '../backend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$campusFilter = isset($_GET['campus']) ? $_GET['campus'] : '';
$officeFilter = isset($_GET['office']) ? $_GET['office'] : '';

$campusQuery = "SELECT campus_name FROM campus ORDER BY campus_name ASC";
$campusResult = mysqli_query($conn_medical, $campusQuery);

$officeQuery = "SELECT office_name FROM office ORDER BY office_name ASC";
$officeResult = mysqli_query($conn_medical, $officeQuery);

$query = "
    SELECT emp_id, id_number, fname, mname, lname, position, office, campus, province, city, barangay, 
           emc_person, emc_number, emc_address, emp_image
    FROM employee
    WHERE 1 = 1 and deleted_at is NULL
";

if (!empty($campusFilter)) {
    $query .= " AND campus = '" . mysqli_real_escape_string($conn_medical, $campusFilter) . "'";
}

if (!empty($officeFilter)) {
    $query .= " AND office = '" . mysqli_real_escape_string($conn_medical, $officeFilter) . "'";
}

$result = mysqli_query($conn_medical, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn_medical));
}
?>

 


 
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../img/NEUST.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Medical Records</title>
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
                                            
                                            <h3 class="m-0 font-weight-bold text-primary">Employee Medical Records</h3>
                                        </div>
                                        <div class="card-body">
                        <form method="GET" class="mb-4">
                         <div class="row align-items-end">
                             <div class="col-md-4">
                                 <label for="campus">Campus:</label>
                                     <select name="campus" id="campus" class="form-control">
                                           <option value="">All Campuses</option>
                                             <?php while ($row = mysqli_fetch_assoc($campusResult)) { ?>
                                                <option value="<?= $row['campus_name']; ?>" <?= ($campusFilter == $row['campus_name']) ? 'selected' : ''; ?>>
                                                    <?= $row['campus_name']; ?>
                                                </option>
                                            <?php } ?>
                                     </select>
                            </div>
        <div class="col-md-4">
            <label for="course">Office:</label>
            <select name="course" id="course" class="form-control">
                <option value="">All Offices</option>
                <?php while ($row = mysqli_fetch_assoc($officeResult)) { ?>
                    <option value="<?= $row['office_name']; ?>" <?= ($officeFilter == $row['office_name']) ? 'selected' : ''; ?>>
                        <?= $row['office_name']; ?>
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
                                        <th>Employee Number</th>
                                        <th>Name</th>
                                        <th>Campus</th>
                                        <th>Office</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?= $row['id_number']; ?></td>
                                            <td><?= $row['fname'] . " " . $row['mname'] . ". " . $row['lname']; ?></td>
                                            <td><?= $row['campus']; ?></td>
                                            <td><?= $row['office']; ?></td>
                                          
                                                <td>
                                         <button class="btn btn-primary" onclick="showDetails(<?php echo $row['emp_id']; ?>)" title="View Details">
                                          <i class="fas fa-eye"></i> 
                                       </button>
                                         <button class="btn btn-warning" onclick="editRecord(<?php echo $row['emp_id']; ?>)" title="Edit Record">
                                         <i class="fas fa-edit"></i> 
                                          </button>
                                         <button class="btn btn-danger" onclick="deleteRecord(<?php echo $row['emp_id']; ?>)" title="Delete Record">
                                         <i class="fas fa-trash"></i> 
                                        </button>
                                        </td>
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
<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editModalContent">
                <form id="editEmployeeForm" enctype="multipart/form-data">

                    <input type="hidden" id="emp_id" name="emp_id">

                    <div class="row">

                        <div class="col-md-6">
                            <h5>Personal Information</h5>

                            <label>ID Number:</label>
                            <input type="text" id="id_number" name="id_number" class="form-control" required >

                            <label>First Name:</label>
                            <input type="text" id="fname" name="fname" class="form-control" required>

                            <label>Middle Name:</label>
                            <input type="text" id="mname" name="mname" class="form-control">

                            <label>Last Name:</label>
                            <input type="text" id="lname" name="lname" class="form-control" required>

                            <label>Position:</label>
                            <input type="text" id="position" name="position" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <h5>Location & Contact</h5>

                            <label>Office:</label>
                            <input type="text" id="office" name="office" class="form-control" required>

                            <label>Campus:</label>
                            <input type="text" id="campus" name="campus" class="form-control" required>

                            <label>Province:</label>
                            <input type="text" id="province" name="province" class="form-control" required>

                            <label>City:</label>
                            <input type="text" id="city" name="city" class="form-control" required>

                            <label>Barangay:</label>
                            <input type="text" id="barangay" name="barangay" class="form-control" required>
                        </div>

                        <!-- EMERGENCY DETAILS -->
                        <div class="col-md-12 mt-3">
                            <h5>Emergency Contact</h5>

                            <label>Contact Person:</label>
                            <input type="text" id="emc_person" name="emc_person" class="form-control" required>

                            <label>Contact Number:</label>
                            <input type="text" id="emc_number" name="emc_number" class="form-control" required>

                            <label>Address:</label>
                            <input type="text" id="emc_address" name="emc_address" class="form-control" required>

                            <h5>Employee Photo</h5>
                            <label>Employee Image:</label>
                            
                            <input type="file" class="form-control" id="emp_image" name="emp_image" accept="image/*">
                            
                            <div class="mt-2 text-center">
                                <img id="preview_image" src="" class="img-fluid rounded" style="max-width: 200px; display: none;">
                            </div>
                        </div>

                        
                        
                        </form>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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

function showDetails(emp_id) {
    const modalBody = document.getElementById("modalContent");
    modalBody.innerHTML = `<div class="text-center">
        <div class="spinner-border text-primary" role="status"></div>
        <p>Loading...</p>
    </div>`;

    fetch(`../backend/emp_med_details.php?id=${emp_id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = `<p class="text-danger">${data.error}</p>`;
                return;
            }

            const photoHTML = data.emp_image 
                ? `<img src="../${data.emp_image}" class="img-fluid rounded-circle shadow" width="200" height="200">`
                : `<p class="text-muted">No Employee Photo</p>`;

            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">Personal Info</div>
                            <div class="card-body text-center">
                                ${photoHTML}
                                <h5 class="mt-2">${data.fname} ${data.mname ? data.mname+'.' : ''} ${data.lname}</h5>
                                <p class="fw-bold text-muted">${data.id_number}</p>
                                <table class="table table-bordered mt-2">
                                    <tr><th>Position</th><td>${data.position}</td></tr>
                                    <tr><th>Office</th><td>${data.office}</td></tr>
                                    <tr><th>Campus</th><td>${data.campus}</td></tr>
                                    <tr><th>Address</th><td>${data.province} ${data.city}<br> brgy. ${data.barangay}</td></tr>
                                    
                                    <tr><th>Emergency Contact</th><td>${data.emc_person} (${data.emc_number})<br>${data.emc_address}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            new bootstrap.Modal(document.getElementById("medicalModal")).show();
        })
        .catch(err => {
            console.error(err);
            modalBody.innerHTML = `<p class="text-danger">Failed to load data.</p>`;
        });
}



function deleteRecord(emp_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this action!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: '../backend/emp_delete.php',
            type: 'POST',
            data: { emp_id: emp_id },
            success: function(response) {
                console.log("DELETE RESPONSE:", response);

                if (typeof response === "string") {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error("Invalid JSON:", response);
                        Swal.fire("Error!", "Unexpected server response.", "error");
                        return;
                    }
                }

                if (response.success) {
                    Swal.fire({
                        title: "Deleted!",
                        text: "The employee record has been deleted.",
                        icon: "success"
                    }).then(() => location.reload());
                } else {
                    Swal.fire("Error!", response.message, "error");
                }
            },
            error: function() {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to delete the record. Please try again.",
                    icon: "error"
                });
            }
        });
    });
}

     

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
        previewImg.src = ''; 
    }
});

function editRecord(emp_id) {
    if (!emp_id) return Swal.fire({
        title: "Error!",
        text: "Invalid employee ID.",
        icon: "error",
        confirmButtonColor: "#d33"
    });

    const modalContent = document.getElementById('editModalContent');
    modalContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p>Loading...</p>
        </div>
    `;

    fetch(`../backend/emp_med_details.php?id=${encodeURIComponent(emp_id)}`)
        .then(res => res.json())
        .then(data => {
            if (!data || Object.keys(data).length === 0) {
                modalContent.innerHTML = `<p class="text-danger">No data found.</p>`;
                return;
            }

            modalContent.innerHTML = `
                <form id="editEmployeeForm" enctype="multipart/form-data">
                    <input type="hidden" name="emp_id" value="${emp_id}">
                    <div class="row">
                        <!-- LEFT -->
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            <div class="mb-2">
                                <label>ID Number</label>
                                <input type="text" class="form-control" name="id_number" value="${data.id_number}" required>
                            </div>
                            <div class="mb-2">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="fname" value="${data.fname}" required>
                            </div>
                            <div class="mb-2">
                                <label>Middle Name</label>
                                <input type="text" class="form-control" name="mname" value="${data.mname}">
                            </div>
                            <div class="mb-2">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="lname" value="${data.lname}" required>
                            </div>
                            <div class="mb-2">
                                <label>Position</label>
                                <input type="text" class="form-control" name="position" value="${data.position}">
                            </div>
                        </div>

                        <!-- RIGHT -->
                        <div class="col-md-6">
                            <h5>Office & Location</h5>
                            <div class="mb-2">
                                <label>Office</label>
                                <input type="text" class="form-control" name="office" value="${data.office}" required>
                            </div>
                            <div class="mb-2">
                                <label>Campus</label>
                                <input type="text" class="form-control" name="campus" value="${data.campus}" required>
                            </div>
                            <div class="mb-2">
                                <label>Province</label>
                                <input type="text" class="form-control" name="province" value="${data.province}" required>
                            </div>
                            <div class="mb-2">
                                <label>City</label>
                                <input type="text" class="form-control" name="city" value="${data.city}" required>
                            </div>
                            <div class="mb-2">
                                <label>Barangay</label>
                                <input type="text" class="form-control" name="barangay" value="${data.barangay}" required>
                            </div>
                        </div>

                        <!-- EMERGENCY -->
                        <div class="col-12 mt-3">
                            <h5>Emergency Contact</h5>
                            <div class="mb-2">
                                <label>Contact Person</label>
                                <input type="text" class="form-control" name="emc_person" value="${data.emc_person}" required>
                            </div>
                            <div class="mb-2">
                                <label>Contact Number</label>
                                <input type="text" class="form-control" name="emc_number" value="${data.emc_number}" required>
                            </div>
                            <div class="mb-2">
                                <label>Address</label>
                                <input type="text" class="form-control" name="emc_address" value="${data.emc_address}" required>
                            </div>
                        </div>

                        <!-- PHOTO -->
                        <div class="col-12 mt-3 text-center">
                            <h5>Employee Photo</h5>
                            <img id="preview_image" src="../${data.emp_image || ''}" class="img-fluid rounded mb-2" style="max-width:200px; display:${data.emp_image?'block':'none'};">
                            <input type="file" class="form-control" name="emp_image" id="emp_image" accept="image/*">
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            `;

            document.getElementById('emp_image').addEventListener('change', function(e){
                const file = e.target.files[0];
                const preview = document.getElementById('preview_image');
                if(file){
                    const reader = new FileReader();
                    reader.onload = function(ev){
                        preview.src = ev.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '';
                    preview.style.display = 'none';
                }
            });

            document.getElementById('editEmployeeForm').addEventListener('submit', function(e){
                e.preventDefault();
                saveEmployeeRecord(emp_id);
            });

        }).catch(err => {
            console.error(err);
            modalContent.innerHTML = `<p class="text-danger">Failed to load data.</p>`;
        });

    new bootstrap.Modal(document.getElementById('editEmployeeModal')).show();
}



function saveEmployeeRecord(emp_id) {
    const form = document.getElementById("editEmployeeForm");
    const formData = new FormData(form);

    formData.append("emp_id", emp_id);

    fetch("../backend/emp_med_update.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log("UPDATE RESPONSE:", data);

        if (data.success) {
            Swal.fire({
                title: "Success!",
                text: data.message,
                icon: "success"
            }).then(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById("editEmployeeModal"));
                modal.hide();

                location.reload();
            });

        } else {
            Swal.fire({
                title: "Error",
                text: data.message,
                icon: "error"
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            title: "Error",
            text: "Something went wrong. Check console.",
            icon: "error"
        });
    });
}


</script>

</body>
</html>
