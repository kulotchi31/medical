<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index");
    exit();
}

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['spreadsheet']) && $_FILES['spreadsheet']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['spreadsheet']['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);

        if ($fileType !== 'text/plain' && $fileType !== 'text/csv') {
            die("Error: Uploaded file is not a valid CSV.");
        }

        $file = fopen($fileTmpPath, 'r');
        if ($file === false) {
            die("Error: Unable to open the uploaded file.");
        }

        // Skip the header row
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            list(
                $id_number, $first_name, $last_name, $middle_name, $course_name, $student_campus,
                $province, $city, $barangay, $guardian_name, $emergency_contact, $school_year,
                $blood_pressure, $blood_type, $height_cm, $weight_kg, $allergy, $asthma,
                $seizure_disorder, $heart_disease, $diabetes, $other_HC, $medication, $exam_date,
                $dose_number, $vaccination_date, $smoking, $liquor_drinking
            ) = $row;

            // Insert student data
            $stmt1 = $conn_student->prepare("INSERT INTO students (id_number, first_name, last_name, middle_name, campus, guardian_name, province, city, barangay, emergency_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt1) {
                die("Error preparing student statement: " . $conn_student->error);
            }
            $stmt1->bind_param("ssssssssss", $id_number, $first_name, $last_name, $middle_name, $student_campus, $guardian_name, $province, $city, $barangay, $emergency_contact);
            if (!$stmt1->execute()) {
                die("Error inserting student details: " . $stmt1->error);
            }
            $student_id = $conn_student->insert_id;

            // Insert or retrieve course location
            $stmt_check_location = $conn_medical->prepare("SELECT campus_id FROM medical_location WHERE course_name = ?");
            $stmt_check_location->bind_param("s", $course_name);
            $stmt_check_location->execute();
            $result_location = $stmt_check_location->get_result();

            if ($result_location->num_rows == 0) {
                $stmt_insert_location = $conn_medical->prepare("INSERT INTO medical_location (campus_name, course_name) VALUES (?, ?)");
                $stmt_insert_location->bind_param("ss", $student_campus, $course_name);
                $stmt_insert_location->execute();
            }

            $stmt_get_course_id = $conn_medical->prepare("SELECT campus_id FROM medical_location WHERE course_name = ?");
            $stmt_get_course_id->bind_param("s", $course_name);
            $stmt_get_course_id->execute();
            $result_course_id = $stmt_get_course_id->get_result();

            if ($result_course_id->num_rows > 0) {
                $course_id = $result_course_id->fetch_assoc()['campus_id'];
            } else {
                die("Error: Course ID not found.");
            }

            // Insert medical record
            $stmt2 = $conn_medical->prepare("INSERT INTO medical_record (student_id, course_id, school_year, allergy, asthma, diabetes, heart_disease, seizure_disorder, other_HC, medication, record_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("iisssssssss", $student_id, $course_id, $school_year, $allergy, $asthma, $diabetes, $heart_disease, $seizure_disorder, $other_HC, $medication, $exam_date);
            if (!$stmt2->execute()) {
                die("Error inserting medical record: " . $stmt2->error);
            }
            $medical_id = $conn_medical->insert_id;

            // Insert physical examination
            $stmt3 = $conn_medical->prepare("INSERT INTO physical_examination (medical_id, exam_date, height_cm, weight_kg, smoking, liquor_drinking, blood_pressure, blood_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt3->bind_param("isddssss", $medical_id, $exam_date, $height_cm, $weight_kg, $smoking, $liquor_drinking, $blood_pressure, $blood_type);
            if (!$stmt3->execute()) {
                die("Error inserting physical examination: " . $stmt3->error);
            }

            // Insert vaccine record
            $stmt4 = $conn_medical->prepare("INSERT INTO vaccine (medical_id, dose_number, vaccination_date) VALUES (?, ?, ?)");
            $stmt4->bind_param("iis", $medical_id, $dose_number, $vaccination_date);
            if (!$stmt4->execute()) {
                die("Error inserting vaccine record: " . $stmt4->error);
            }
        }

        fclose($file);

        // Redirect to data.php with a success message
        header("Location: ../frontend/data.php?status=success");
        exit();
    }

    // Handle form submissions (if any)
    $id_number = $_POST['id_number'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $student_campus = $_POST['student_campus'] ?? '';
    $guardian_name = $_POST['guardian_name'] ?? '';
    $province = $_POST['province'] ?? '';
    $city = $_POST['city'] ?? '';
    $barangay = $_POST['barangay'] ?? '';
    $emergency_contact = $_POST['emergency_contact'] ?? '';
    $student_photo = $_FILES['student_photo']['name'] ?? '';
    $course_name = $_POST['course_name'] ?? '';
    $school_year = $_POST['school_year'] ?? '';
    $blood_pressure = $_POST['blood_pressure'] ?? '';
    $blood_type = $_POST['blood_type'] ?? '';
    $height_cm = $_POST['height_cm'] ?? '';
    $weight_kg = $_POST['weight_kg'] ?? '';
    $allergy = $_POST['allergy'] ?? '';
    $asthma = $_POST['asthma'] ?? '';
    $diabetes = $_POST['diabetes'] ?? '';
    $heart_disease = $_POST['heart_disease'] ?? '';
    $seizure_disorder = $_POST['seizure_disorder'] ?? '';
    $other_HC = $_POST['other_HC'] ?? '';
    $medication = $_POST['medication'] ?? '';
    $exam_date = $_POST['exam_date'] ?? '';
    $smoking = $_POST['smoking'] ?? '';
    $liquor_drinking = $_POST['liquor_drinking'] ?? '';
    $dose_number = $_POST['dose_number'] ?? '';
    $vaccination_date = $_POST['vaccination_date'] ?? '';

    if (isset($_FILES["student_photo"]) && $_FILES["student_photo"]["error"] == 0) {
        $target_dir = "uploads/"; 
        $target_file = $target_dir . basename($_FILES["student_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["student_photo"]["tmp_name"]);
        if ($check === false) {
            die("Error: File is not an image.");
        }

        if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
            die("Error: Only JPG, JPEG, and PNG files are allowed.");
        }

        if (!move_uploaded_file($_FILES["student_photo"]["tmp_name"], $target_file)) {
            die("Error uploading the image.");
        }

        $student_photo = basename($_FILES["student_photo"]["name"]); 
    } else {
        die("Error: No file uploaded.");
    }

    $stmt1 = $conn_student->prepare("INSERT INTO students (id_number, first_name, last_name, middle_name, campus, guardian_name, province, city, barangay, emergency_contact, student_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt1) {
        die("Error preparing student statement: " . $conn_student->error);
    }
    $stmt1->bind_param("sssssssssss", $id_number, $first_name, $last_name, $middle_name, $student_campus, $guardian_name, $province, $city, $barangay, $emergency_contact, $target_file);

    if (!$stmt1->execute()) {
        die("Error inserting student details: " . $stmt1->error);
    }
    $student_id = $conn_student->insert_id;

    $stmt_check_location = $conn_medical->prepare("SELECT campus_id FROM medical_location WHERE course_name = ?");
    if (!$stmt_check_location) {
        die("Error preparing course location query: " . $conn_medical->error);
    }
    $stmt_check_location->bind_param("s", $course_name);
    $stmt_check_location->execute();
    $result_location = $stmt_check_location->get_result();

    if ($result_location->num_rows == 0) {
        $stmt_insert_location = $conn_medical->prepare("INSERT INTO medical_location (campus_name, course_name) VALUES (?, ?)");
        if (!$stmt_insert_location) {
            die("Error preparing insert medical location: " . $conn_medical->error);
        }
        $stmt_insert_location->bind_param("ss", $student_campus, $course_name);
        $stmt_insert_location->execute();
    }

    $stmt_get_course_id = $conn_medical->prepare("SELECT campus_id FROM medical_location WHERE course_name = ?");
    $stmt_get_course_id->bind_param("s", $course_name);
    $stmt_get_course_id->execute();
    $result_course_id = $stmt_get_course_id->get_result();

    if ($result_course_id->num_rows > 0) {
        $course_id = $result_course_id->fetch_assoc()['campus_id'];
    } else {
        die("Error: Course ID not found.");
    }

    $stmt2 = $conn_medical->prepare("INSERT INTO medical_record (student_id, course_id, school_year, allergy, asthma, diabetes, heart_disease, seizure_disorder, other_HC, medication, record_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt2) {
        die("Error preparing medical record: " . $conn_medical->error);
    }
    $stmt2->bind_param("iisssssssss", $student_id, $course_id, $school_year, $allergy, $asthma, $diabetes, $heart_disease, $seizure_disorder, $other_HC, $medication, $exam_date);

    if (!$stmt2->execute()) {
        die("Error inserting medical record: " . $stmt2->error);
    }
    $medical_id = $conn_medical->insert_id;

    $stmt3 = $conn_medical->prepare("INSERT INTO physical_examination (medical_id, exam_date, height_cm, weight_kg, smoking, liquor_drinking, blood_pressure, blood_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt3) {
        die("Error preparing physical examination: " . $conn_medical->error);
    }
    $stmt3->bind_param("isddssss", $medical_id, $exam_date, $height_cm, $weight_kg, $smoking, $liquor_drinking, $blood_pressure, $blood_type);

    if (!$stmt3->execute()) {
        die("Error inserting physical examination: " . $stmt3->error);
    }

    $stmt4 = $conn_medical->prepare("INSERT INTO vaccine (medical_id, dose_number, vaccination_date) VALUES (?, ?, ?)");
    if (!$stmt4) {
        die("Error preparing vaccine record: " . $conn_medical->error);
    }
    $stmt4->bind_param("iis", $medical_id, $dose_number, $vaccination_date);

    if ($stmt4->execute()) {
        // Redirect to data.php with a success message
        header("Location: ../frontend/data.php?status=success");
        exit();
    } else {
        die("Error inserting vaccine record: " . $stmt4->error);
    }
}
?>