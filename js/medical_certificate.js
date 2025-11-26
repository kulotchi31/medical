    function fetchStudentDetails() {
        var idNumber = document.getElementById('id_number').value;
        if (idNumber.trim() !== '') {
            fetch('fetch_student_details.php?id_number=' + idNumber)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('name').value = data.name;
                    document.getElementById('campus').value = data.campus;
                } else {
                    alert('Student not found!');
                }
            })
            .catch(error => console.error('Error fetching student details:', error));
        } else {
            alert('Please enter an ID number.');
        }
    }
    document.getElementById('generatePDF').addEventListener('click', function (e) {
    e.preventDefault();
    
    let formData = new FormData(document.getElementById('medicalForm'));

    fetch('generate_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.pdf_url) {
            showPDF(data.pdf_url);
        } else {
            alert("Error generating PDF!");
        }
    })
    .catch(error => console.error('Error:', error));
});

function showPDF(url) {
    let loadingTask = pdfjsLib.getDocument(url);
    
    loadingTask.promise.then(function(pdf) {
        pdf.getPage(1).then(function(page) {
            let scale = 1.5;
            let viewport = page.getViewport({ scale: scale });

            let canvas = document.getElementById('pdfViewer');
            let context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            let renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            page.render(renderContext);
        });
    });
}
