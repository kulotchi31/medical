<?php
session_start();
include '../backend/db_connect.php';

if (!isset($_GET['id'])) {
    echo "<p class='text-danger'>Invalid request!</p>";
    exit();
}

$medical_id = mysqli_real_escape_string($conn_student, $_GET['id']);
$query = "
    SELECT m.medical_id, s.id_number, s.first_name, s.middle_name, s.last_name, 
           m.allergy, m.asthma, m.diabetes, m.heart_disease, 
           m.other_HC, m.medication
    FROM neustmrdb.medical_record m
    JOIN neust_student_details.students s ON s.student_id = m.student_id
    WHERE m.medical_id = '$medical_id'";

$result = mysqli_query($conn_student, $query);

if ($row = mysqli_fetch_assoc($result)) {
?>

<form id="editMedicalForm">
    <input type="hidden" name="medical_id" value="<?php echo $row['medical_id']; ?>">
    
    <div class="mb-3">
        <label class="form-label">ID Number:</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['id_number']); ?>" disabled>
    </div>

    <div class="mb-3">
        <label class="form-label">Full Name:</label>
        <input type="text" class="form-control" 
               value="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . '. ' . $row['last_name']); ?>" 
               disabled>
    </div>

    <div class="mb-3">
        <label class="form-label">Allergy:</label>
        <input type="text" name="allergy" class="form-control" value="<?php echo htmlspecialchars($row['allergy']); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Asthma:</label>
        <select name="asthma" class="form-control">
            <option value="Yes" <?php echo ($row['asthma'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
            <option value="No" <?php echo ($row['asthma'] == 'No') ? 'selected' : ''; ?>>No</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Diabetes:</label>
        <input type="text" name="diabetes" class="form-control" value="<?php echo htmlspecialchars($row['diabetes']); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Heart Disease:</label>
        <input type="text" name="heart_disease" class="form-control" value="<?php echo htmlspecialchars($row['heart_disease']); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Other Health Conditions:</label>
        <input type="text" name="other_HC" class="form-control" value="<?php echo htmlspecialchars($row['other_HC']); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Medication:</label>
        <input type="text" name="medication" class="form-control" value="<?php echo htmlspecialchars($row['medication']); ?>">
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>

<?php
} else {
    echo "<p class='text-danger'>Record not found!</p>";
}
?>

<script>
function loadEditModal(medical_id) {
    let modalBody = document.getElementById("editModalContent");
    modalBody.innerHTML = `<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p>Loading...</p></div>`;

    fetch(`edit_modal.php?id=${medical_id}`)
        .then(response => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.text();
        })
        .then(data => {
            modalBody.innerHTML = data;
            let modal = new bootstrap.Modal(document.getElementById("editModal"));
            modal.show();

           
            document.getElementById("editMedicalForm").addEventListener("submit", function(event) {
                event.preventDefault();
                saveEditForm(new FormData(this));
            });
        })
        .catch(error => {
            console.error("Error loading modal:", error);
            modalBody.innerHTML = `<p class="text-danger">Failed to load form. Please try again.</p>`;
        });
}

function saveEditForm(formData) {
    fetch("../backend/update_medical_record.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire("Updated!", "Medical record has been updated.", "success")
                .then(() => location.reload());
        } else {
            Swal.fire("Error!", data.message, "error");
        }
    })
    .catch(error => {
        console.error("Error saving data:", error);
        Swal.fire("Error!", "Failed to update record. Please try again.", "error");
    });
}
</script>
