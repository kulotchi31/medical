$(document).ready(function() {
    $('#dataTable').DataTable();
});

function showDetails(medical_id) {
    let modalBody = document.getElementById("modalContent");

    modalBody.innerHTML = `<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p>Loading...</p></div>`;

    fetch(`../backend/fetch_medical_details.php?id=${medical_id}`)
        .then(response => response.json())
        .then(data => {
            let studentPhotoHTML = '';
            if (data.student_photo) {
                
                studentPhotoHTML = `<img src="../${data.student_photo}" alt="Student Photo" class="img-fluid rounded-circle shadow" width="200" height="200">`;
            } else {
                studentPhotoHTML = '<p class="text-muted">No Student Photo</p>';
            }


            modalBody.innerHTML = `
                <div class="text-center mt-3">
                    ${studentPhotoHTML}
                </div>
                <table class="table table-bordered mt-3">
                    <tr><th>Blood Pressure</th><td>${data.blood_pressure || 'N/A'}</td></tr>
                    <tr><th>Blood Type</th><td>${data.blood_type || 'Unknown'}</td></tr>
                    <tr><th>Height</th><td>${data.height_cm ? data.height_cm + ' cm' : 'N/A'}</td></tr>
                    <tr><th>Weight</th><td>${data.weight_kg ? data.weight_kg + ' kg' : 'N/A'}</td></tr>
                    <tr><th>Allergy</th><td>${data.allergy ? data.allergy : '<span class="text-muted">None</span>'}</td></tr>
                    <tr><th>Asthma</th><td>${data.asthma ? 'Yes' : 'No'}</td></tr>
                    <tr><th>Diabetes</th><td>${data.diabetes ? data.diabetes : 'None'}</td></tr>
                    <tr><th>Heart Disease</th><td>${data.heart_disease ? data.heart_disease : '<span class="text-muted">None</span>'}</td></tr>
                    <tr><th>Other Conditions</th><td>${data.other_HC ? data.other_HC : '<span class="text-muted">None</span>'}</td></tr>
                    <tr><th>Medication</th><td>${data.medication ? data.medication : '<span class="text-muted">None</span>'}</td></tr>
                    <tr><th>Examination Date</th><td>${data.exam_date || 'N/A'}</td></tr>
                </table>
            `;

            let modal = new bootstrap.Modal(document.getElementById("medicalModal"));
            modal.show();
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            modalBody.innerHTML = `<p class="text-danger">Failed to load data. Please try again.</p>`;
        });
}