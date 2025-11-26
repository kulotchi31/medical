<?php
include '../backend/db_connect.php';

if (!isset($_GET['id'])) {
    echo "<p class='text-danger'>Invalid request.</p>";
    exit;
}

$medical_id = mysqli_real_escape_string($conn_student, $_GET['id']);
$query = "
    SELECT s.student_id, s.id_number, s.first_name, s.middle_name, s.last_name, s.campus, 
           ml.course_name, m.medical_id, p.blood_pressure, p.blood_type, m.allergy, 
           m.asthma, m.diabetes, m.heart_disease, m.other_HC, m.medication, 
           p.exam_date, p.height_cm, p.weight_kg
    FROM neust_student_details.students s
    LEFT JOIN neustmrdb.medical_record m ON s.student_id = m.student_id
    LEFT JOIN neustmrdb.physical_examination p ON m.medical_id = p.medical_id
    LEFT JOIN neustmrdb.medical_location ml ON m.course_id = ml.campus_id
    WHERE m.medical_id = '$medical_id'
    LIMIT 1
";

$result = mysqli_query($conn_student, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<p class='text-danger'>Medical record not found.</p>";
    exit;
}

$row = mysqli_fetch_assoc($result);
?>

<form id="editMedicalForm">
    <input type="hidden" name="medical_id" value="<?php echo $row['medical_id']; ?>">

    <div class="mb-3">
        <label for="allergy" class="form-label">Allergy</label>
        <input type="text" class="form-control" id="allergy" name="allergy" value="<?php echo $row['allergy']; ?>">
    </div>

    <div class="mb-3">
        <label for="asthma" class="form-label">Asthma</label>
        <select class="form-control" id="asthma" name="asthma">
            <option value="0" <?php echo ($row['asthma'] == 0) ? 'selected' : ''; ?>>No</option>
            <option value="1" <?php echo ($row['asthma'] == 1) ? 'selected' : ''; ?>>Yes</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="blood_pressure" class="form-label">Blood Pressure</label>
        <input type="text" class="form-control" id="blood_pressure" name="blood_pressure" value="<?php echo $row['blood_pressure']; ?>">
    </div>

    <div class="mb-3">
        <label for="blood_type" class="form-label">Blood Type</label>
        <input type="text" class="form-control" id="blood_type" name="blood_type" value="<?php echo $row['blood_type']; ?>">
    </div>

    <div class="mb-3">
        <label for="height_cm" class="form-label">Height (cm)</label>
        <input type="number" class="form-control" id="height_cm" name="height_cm" value="<?php echo $row['height_cm']; ?>">
    </div>

    <div class="mb-3">
        <label for="weight_kg" class="form-label">Weight (kg)</label>
        <input type="number" class="form-control" id="weight_kg" name="weight_kg" value="<?php echo $row['weight_kg']; ?>">
    </div>

    <button type="button" class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
</form>
