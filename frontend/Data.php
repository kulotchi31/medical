<?php 
include '../backend/db_connect.php'; // Ensure the database connection is included
include '../backend/add_data.php';

// Declare the database connection variables as global
global $conn_medical, $conn_student;

if (!isset($conn_medical) || !isset($conn_student)) {
    die("Error: Database connection not established. Please check your db_connect.php file.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['spreadsheet']) && $_FILES['spreadsheet']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['spreadsheet']['tmp_name'];
    $skipped_rows = []; // To track skipped rows
    $error_messages = []; // To track error messages for each skipped row

    try {
        // Debugging: Log file upload details
        if (!file_exists($file)) {
            throw new Exception('Uploaded file not found.');
        }
        echo "<script>console.log('File uploaded successfully: " . $_FILES['spreadsheet']['name'] . "');</script>";

        // Open the CSV file
        $handle = fopen($file, 'r');
        if ($handle === false) {
            throw new Exception('Failed to open the CSV file.');
        }

        $header = fgetcsv($handle); // Read the header row
        if ($header === false) {
            throw new Exception('Failed to read the CSV header.');
        }
        echo "<script>console.log('CSV header read successfully: " . json_encode($header) . "');</script>";

        // Validate header structure
        $expected_columns = ['ID Number', 'First Name', 'Last Name', 'Middle Name', 'Course Name', 'Student Campus', 'Province', 'City', 'Barangay', 'Guardian Name', 'Emergency Contact', 'School Year', 'Blood Pressure', 'Blood Type', 'Height (cm)', 'Weight (kg)', 'Allergy', 'Asthma', 'Seizure Disorder', 'Heart Disease', 'Diabetes', 'Other HC', 'Medication', 'Exam Date', 'Dose Number', 'Vaccination Date', 'Smoking', 'Liquor Drinking'];
        if ($header !== $expected_columns) {
            throw new Exception('CSV header does not match the expected format.');
        }

        while (($row = fgetcsv($handle)) !== false) {
            // Debugging: Log each row being processed
            echo "<script>console.log('Processing row: " . json_encode($row) . "');</script>";

            // Start transactions for both databases
            mysqli_begin_transaction($conn_student);
            mysqli_begin_transaction($conn_medical);

            try {
                // Map CSV columns to variables with default values for optional fields
                $id_number = $row[0] ?? '';
                $first_name = $row[1] ?? '';
                $last_name = $row[2] ?? '';
                $middle_name = $row[3] ?? 'N/A';
                $course_name = $row[4] ?? '';
                $student_campus = $row[5] ?? '';
                $province = $row[6] ?? 'N/A';
                $city = $row[7] ?? 'N/A';
                $barangay = $row[8] ?? 'N/A';
                $guardian_name = $row[9] ?? 'N/A';
                $emergency_contact = isset($row[10]) ? preg_replace('/[^0-9]/', '', $row[10]) : 'N/A';
                $school_year = $row[11] ?? 'N/A';
                $blood_pressure = $row[12] ?? 'N/A';
                $blood_type = $row[13] ?? 'N/A';
                $height_cm = is_numeric($row[14]) ? $row[14] : 0;
                $weight_kg = is_numeric($row[15]) ? $row[15] : 0;
                $allergy = $row[16] ?? 'None';
                $asthma = is_numeric($row[17]) ? $row[17] : 0;
                $seizure_disorder = is_numeric($row[18]) ? $row[18] : 0;
                $heart_disease = $row[19] ?? 'None';
                $diabetes = is_numeric($row[20]) ? $row[20] : 0;
                $other_HC = $row[21] ?? 'None';
                $medication = $row[22] ?? 'None';
                $exam_date = isset($row[23]) ? date('Y-m-d', strtotime(str_replace('/', '-', $row[23]))) : null;
                $dose_number = is_numeric($row[24]) ? $row[24] : 0;
                $vaccination_date = isset($row[25]) && !empty($row[25]) ? date('Y-m-d', strtotime(str_replace('/', '-', $row[25]))) : null;
                $smoking = is_numeric($row[26]) ? $row[26] : 0;
                $liquor_drinking = is_numeric($row[27]) ? $row[27] : 0;

                // Validate required fields
                if (empty($id_number) || empty($first_name) || empty($last_name) || empty($course_name) || empty($student_campus)) {
                    $skipped_rows[] = $row;
                    $error_messages[] = "Missing required fields for ID: $id_number.";
                    echo "<script>console.error('Missing required fields for row: " . json_encode($row) . "');</script>";
                    continue; // Skip invalid rows
                }

                // Check for duplicate ID numbers
                $check_query = "SELECT id_number FROM students WHERE id_number = ?";
                $stmt_check = $conn_student->prepare($check_query);
                if (!$check_result) {
                    throw new Exception("Database error during duplicate check: " . mysqli_error($conn_student));
                }
                if (mysqli_num_rows($check_result) > 0) {
                    $skipped_rows[] = $row;
                    $error_messages[] = "Duplicate ID number: $id_number.";
                    echo "<script>console.error('Duplicate ID number: $id_number');</script>";
                    continue; // Skip duplicate entries
                }

                // Insert data into the students table
                $query = "INSERT INTO students (id_number, first_name, last_name, middle_name, course_name, student_campus, province, city, barangay, guardian_name, emergency_contact) 
                          VALUES ('$id_number', '$first_name', '$last_name', '$middle_name', '$course_name', '$student_campus', '$province', '$city', '$barangay', '$guardian_name', '$emergency_contact')";
                if (!mysqli_query($conn_student, $query)) {
                    throw new Exception("Failed to insert student data for ID: $id_number. Error: " . mysqli_error($conn_student));
                }

                $student_id = mysqli_insert_id($conn_student);

                // Insert data into the medical_records table
                $query = "INSERT INTO medical_records (student_id, school_year, allergy, asthma, diabetes, heart_disease, seizure_disorder, other_HC, medication, record_date) 
                          VALUES ('$student_id', '$school_year', '$allergy', '$asthma', '$diabetes', '$heart_disease', '$seizure_disorder', '$other_HC', '$medication', '$exam_date')";
                if (!mysqli_query($conn_medical, $query)) {
                    throw new Exception("Failed to insert medical record for ID: $id_number. Error: " . mysqli_error($conn_medical));
                }

                $medical_id = mysqli_insert_id($conn_medical);

                // Insert data into the physical_examination table
                $query = "INSERT INTO physical_examination (medical_id, exam_date, height_cm, weight_kg, smoking, liquor_drinking, blood_pressure, blood_type) 
                          VALUES ('$medical_id', '$exam_date', '$height_cm', '$weight_kg', '$smoking', '$liquor_drinking', '$blood_pressure', '$blood_type')";
                if (!mysqli_query($conn_medical, $query)) {
                    throw new Exception("Failed to insert physical examination for ID: $id_number. Error: " . mysqli_error($conn_medical));
                }

                // Insert data into the vaccine table
                $query = "INSERT INTO vaccine (medical_id, dose_number, vaccination_date) 
                          VALUES ('$medical_id', '$dose_number', '$vaccination_date')";
                if (!mysqli_query($conn_medical, $query)) {
                    throw new Exception("Failed to insert vaccine record for ID: $id_number. Error: " . mysqli_error($conn_medical));
                }

                // Commit transactions if all operations succeed
                mysqli_commit($conn_student);
                mysqli_commit($conn_medical);

            } catch (Exception $e) {
                // Rollback transactions on failure
                mysqli_rollback($conn_student);
                mysqli_rollback($conn_medical);

                // Log the error and skip the row
                $skipped_rows[] = $row;
                $error_messages[] = $e->getMessage();
                echo "<script>console.error('Error: " . $e->getMessage() . "');</script>";
                continue;
            }
        }

        fclose($handle);

        // Debugging: Log skipped rows and errors
        echo "<script>console.log('Skipped rows: " . json_encode($skipped_rows) . "');</script>";
        echo "<script>console.log('Error messages: " . json_encode($error_messages) . "');</script>";

        // Add SweetAlert notification for success or errors
        $skipped_count = count($skipped_rows);
        if ($skipped_count > 0) {
            $error_details = implode('<br>', $error_messages);
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Partial Success!',
                        html: 'Data imported successfully, but $skipped_count rows were skipped.<br><br><strong>Errors:</strong><br>$error_details',
                        confirmButtonColor: '#327A33'
                    });
                }, 100);
            </script>";
        } else {
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Data imported successfully!',
                        confirmButtonColor: '#327A33'
                    }).then(() => {
                        window.location.reload();
                    });
                }, 100);
            </script>";
        }
    } catch (Exception $e) {
        echo "<script>console.error('Error: " . $e->getMessage() . "');</script>";
        echo "<script>Swal.fire('Error', 'Failed to process the CSV file: " . $e->getMessage() . "', 'error');</script>";
    }
} else {
    echo "<script>console.error('No file uploaded or an error occurred during upload.');</script>";
    echo "<script>Swal.fire('Error', 'No file uploaded or an error occurred during upload.', 'error');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../img/NEUST.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student & Medical Record</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    
</head>
<body id="page-top">

<div id="wrapper">
    <?php include '../frontend/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h4 class="ml-3">Add Student & Medical Record</h4>
            </nav>
            
            <div class="container-fluid">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h3 class="m-0 font-weight-bold text-primary">Import Students from Spreadsheet</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="spreadsheet">Upload Spreadsheet</label>
                                <input type="file" name="spreadsheet" id="spreadsheet" class="form-control" accept=".csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h3 class="m-0 font-weight-bold text-primary">Personal Information</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                       
                            <div class="row">

                                <div class="col-md-6">


                                    <label>ID Number:</label>
                                    <input type="text" name="id_number" class="form-control" required>

                                    <label>First Name:</label>
                                    <input type="text" name="first_name" class="form-control" required>

                                    <label>Last Name:</label>
                                    <input type="text" name="last_name" class="form-control" required>

                                    <label>Middle name:</label>
                                    <input type="text" name="middle_name" class="form-control">

                                    <label>Course:</label>
                                    <select name="course_name" class="form-control" required>
                                    <option value="Bachelor of Science in Architecture">Bachelor of Science in Architecture</option>
                                    <option value="Bachelor of Science in Criminology">Bachelor of Science in Criminology</option>
                                    <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
                                    <option value="Bachelor of Physical Education">Bachelor of Physical Education</option>
                                    <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>                       
                                    <option value="Bachelor of Technology and Livelihood Education">Bachelor of Technology and Livelihood Education</option>
                                    <option value="Bachelor of Science in Industrial Education">Bachelor of Science in Industrial Education</option>
                                    <option value="Bachelor of Special Needs Education">Bachelor of Special Needs Education</option>                       
                                    <option value="Bachelor of Science in Civil Engineering">Bachelor of Science in Civil Engineering</option>
                                    <option value="Bachelor of Science in Electrical Engineering">Bachelor of Science in Electrical Engineering</option>
                                    <option value="Bachelor of Science in Mechanical Engineering">Bachelor of Science in Mechanical Engineering</option>
                                    <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
                                    <option value="Bachelor of Science in Business Administration">Bachelor of Science in Business Administration</option>
                                    <option value="Bachelor of Science in Entrepreneurship">Bachelor of Science in Entrepreneurship</option>
                                    <option value="Bachelor of Science in Hospitality Management">Bachelor of Science in Hospitality Management</option>
                                    <option value="Bachelor of Science in Hotel and Restaurant Management">Bachelor of Science in Hotel and Restaurant Management</option>
                                    <option value="Bachelor of Science in Tourism Management">Bachelor of Science in Tourism Management</option>
                                    <option value="Bachelor of Public Administration">Bachelor of Public Administration</option>
                                    <option value="Bachelor of Science in Nursing">Bachelor of Science in Nursing</option>
                                    </select>

                                    <label>Campus:</label>
                                    <select name="student_campus" class="form-control" required>
                                        <option value="Main Campus">Sumacab Main Campus</option>
                                        <option value="Atate Campus">Atate Campus</option>
                                        <option value="Fort Magsaysay Campus">Fort Magsaysay Campus</option>
                                        <option value="Gabaldon Campus">Gabaldon Campus</option>
                                        <option value="General Tinio Street Campus">General Tinio Street Campus</option>
                                        <option value="Nampicuan Campus">Nampicuan Campus</option>
                                        <option value="San Isidro Campus">San Isidro Campus</option>
                                        <option value="Sto. Domingo Campus">Sto. Domingo Campus</option>                             
                                    </select>
                                    </div>

                                <div class="col-md-6">
                                    <label>Upload Student Photo:</label>
                                <input type="file" name="student_photo" class="form-control" accept="image/*" >
                                    <label>Province:</label>
                                    <input type="text" name="province" class="form-control" required>

                                    <label>City:</label>
                                    <input type="text" name="city" class="form-control" required>

                                    <label>Barangay:</label>
                                    <input type="text" name="barangay" class="form-control" required>

                                    <label>Parent/Guardian Name:</label>
                                    <input type="text" name="guardian_name" class="form-control" required>

                                    <label>Emergency Contact #:</label>
                                    <input type="text" name="emergency_contact" class="form-control" placeholder="+63" required>
                                </div>

                            </div>
                                  

                            <hr>
                            <h6 class="text-primary">Medical Record</h6>

                            <div class="row">
                                <div class="col-md-6">


                                    <label>School Year:</label>
                                    <input type="text" name="school_year" class="form-control" required>

                                    <label>Blood Pressure:</label>
                                    <input type="text" name="blood_pressure" class="form-control" required>

                                    <label>Blood Type:</label>
                                    <select name="blood_type" class="form-control" required>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>

                                    <label>Height (cm):</label>
                                    <input type="text" name="height_cm" class="form-control"required>

                                    <label>Weight (kg):</label>
                                    <input type="text" name="weight_kg" class="form-control"required>
                                    
                                    <label>Allergy:</label>
                                    <input type="text" name="allergy" class="form-control"required>
                                    <br>
                                    </div>
                                    <div class="col-md-6">
                                        <br>
                                    <label>Asthma:</label>
                                    <input type="radio" name="asthma" value="1"> Yes
                                    <input type="radio" name="asthma" value="0" checked> No
                                    <br>

                                    <label>Seizure Disorder:</label>
                                    <input type="radio" name="seizure_disorder" value="1"> Yes
                                    <input type="radio" name="seizure_disorder" value="0" checked> No
                                    <br><br>

                                    <label>Heart Disease:</label>
                                    <input type="radio" name="heart_disease" id="heartDiseaseYes" onclick="handleHeartDisease(true)"> Yes
                                    <input type="radio" name="heart_disease" id="heartDiseaseNo" value="None" onclick="handleHeartDisease(false)" checked> No
                                    <input type="text" id="heartDiseaseDetails" class="form-control mt-2" placeholder="Specify heart disease details" style="display:none;">
                                    <br>

                                    <label>Diabetes:</label>
                                    <input type="radio" name="diabetes" id="diabetesYes" onclick="handleDiabetes(true)"> Yes
                                    <input type="radio" name="diabetes" id="diabetesNo" value="None" onclick="handleDiabetes(false)" checked> No
                                    <input type="text" id="diabetesDetails" class="form-control" placeholder="Specify diabetes details" style="display:none;">
                                    <br>
                                    <br>
                                    <label>Other Health Conditions:</label>
                                    <input type="text" name="other_HC" class="form-control" placeholder="if not applicable put N/A" required>

                                    <label>Medication:</label>
                                    <input type="text" name="medication" class="form-control" placeholder="if not applicable put N/A" required>

                                    <label>Examination Date:</label>
                                    <input type="date" name="exam_date" class="form-control" >
                                </div>
                            </div>
                                    <hr>
                                    <h6 class="text-primary">Vaccine Record</h6>

                                    <div class="row">   
                                        <div class="col-md-6">
                                    
                                            <label>Dose:</label><br>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="dose_number" value="1" id="first_dose" >
                                                <label class="form-check-label" for="first_dose">First Dose</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="dose_number" value="2" id="second_dose">
                                                <label class="form-check-label" for="second_dose">Second Dose</label>
                                           </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="dose_number" value="3" id="first_booster">
                                                <label class="form-check-label" for="first_booster">First Booster</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="dose_number" value="4" id="second_booster">
                                                <label class="form-check-label" for="second_booster">Second Booster</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="dose_number" value="0" id="no_vaccine">
                                                <label class="form-check-label" for="no_vaccine">did not take vaccine</label>
                                            </div><br>
                                    
                                           <label>Vaccination Date:</label>
                                            <input type="date" name="vaccination_date" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Life Style Information</h6>
                                                    <label>Smoking:</label>
                                                    <input type="radio" name="smoking" value="1"> Yes
                                                    <input type="radio" name="smoking" value="0" checked> No
                                                     <br>                  

                                                    <label>Liquor Drinking:</label>
                                                    <input type="radio" name="liquor_drinking" value="1"> Yes
                                                    <input type="radio" name="liquor_drinking" value="0" checked> No       
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

</div>
   <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../vendor/chart.js/Chart.min.js"></script>
<script>
function handleHeartDisease(isYes) {
    let inputField = document.getElementById("heartDiseaseDetails");

    if (isYes) {
        inputField.style.display = "block";
        inputField.setAttribute("name", "heart_disease"); 
        inputField.setAttribute("required", "true");
        document.getElementById("heartDiseaseYes").removeAttribute("name"); 
    } else {
        inputField.style.display = "none";
        inputField.removeAttribute("name"); 
        inputField.removeAttribute("required");
        inputField.value = "";
        document.getElementById("heartDiseaseYes").setAttribute("name", "heart_disease");
    }
}

// Save input dynamically
document.getElementById("heartDiseaseDetails").addEventListener("input", function () {
    localStorage.setItem("heart_disease", this.value);
});

function handleDiabetes(isYes) {
    let inputField = document.getElementById("diabetesDetails");

    if (isYes) {
        inputField.style.display = "block";
        inputField.setAttribute("name", "diabetes"); 
        inputField.setAttribute("required", "true");
        document.getElementById("diabetesYes").removeAttribute("name"); 
    } else {
        inputField.style.display = "none";
        inputField.removeAttribute("name"); 
        inputField.removeAttribute("required");
        inputField.value = "";
        document.getElementById("diabetesYes").setAttribute("name", "diabetes"); 
    }
}


document.getElementById("diabetesDetails").addEventListener("input", function () {
    localStorage.setItem("diabetes", this.value);
});
</script>
</body>
</html>
