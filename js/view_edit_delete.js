

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

