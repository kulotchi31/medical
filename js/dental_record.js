

document.addEventListener("DOMContentLoaded", function () {
    let idNumber = "<?php echo isset($id_number) ? htmlspecialchars($id_number, ENT_QUOTES, 'UTF-8') : ''; ?>";

    if (idNumber) {
        fetch('fetch_tooth_condition.php?id_number=' + idNumber)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    let toothConditions = {};

                    data.forEach(record => {
                        const toothNumber = record.tooth_number;
                        const latestCondition = record.record_details;
                        const toothElement = document.querySelector(`.tooth-option[data-tooth="${toothNumber}"]`);

                        if (toothElement) {
                            let color = getConditionColor(latestCondition);
                            toothElement.style.backgroundColor = color;
                            toothElement.setAttribute("title", `Last Condition: ${latestCondition}`);

                            toothConditions[toothNumber] = latestCondition;
                        }
                    });

                    localStorage.setItem("toothConditions", JSON.stringify(toothConditions));
                }
            })
            .catch(error => console.error("Error fetching tooth conditions:", error));
    }

 
    let savedConditions = JSON.parse(localStorage.getItem("toothConditions"));
    if (savedConditions) {
        Object.keys(savedConditions).forEach(toothNumber => {
            const toothElement = document.querySelector(`.tooth-option[data-tooth="${toothNumber}"]`);
            if (toothElement) {
                let color = getConditionColor(savedConditions[toothNumber]);
                toothElement.style.backgroundColor = color;
                toothElement.setAttribute("title", `Last Condition: ${savedConditions[toothNumber]}`);
            }
        });
    }
});


function getConditionColor(condition) {
    switch (condition) {
        case "Healthy":
            return "white";
        case "Cavity":
            return "#feeda8";
        case "Filling":
            return "#01bffd";
        case "Extracted":
            return "#0a0600";
        default:
            return "";
    }
}


function openToothModal(toothNumber) {
    document.getElementById("toothNumber").value = toothNumber;
    document.getElementById("toothNumberDisplay").innerText = toothNumber;
    document.getElementById("toothModal").style.display = "flex";

    fetch('../backend/fetch_tooth_condition.php?id_number=<?php echo $id_number; ?>&tooth=' + toothNumber)
        .then(response => response.json())
        .then(data => {
            let conditionsList = "";
            let latestCondition = "Healthy"; 

            if (data.length > 0) {
                latestCondition = data[0].record_details; 
                
                data.forEach(record => {
                    conditionsList += `<tr><td>${record.date_created}</td><td>${record.record_details}</td></tr>`;
                });
            } else {
                conditionsList = "<tr><td colspan='2'>No previous records</td></tr>";
            }

            document.getElementById("previousConditions").innerHTML = conditionsList;
            document.getElementById("record_details").value = latestCondition;
        })
        .catch(error => console.error("Error fetching tooth conditions:", error));
}


function updateToothColor(toothNumber, condition) {
    let toothElement = document.querySelector(`.tooth-option[data-tooth="${toothNumber}"]`);
    if (!toothElement) return;

    let color = getConditionColor(condition);
    toothElement.style.backgroundColor = color;

    // Update local storage
    let savedConditions = JSON.parse(localStorage.getItem("toothConditions")) || {};
    savedConditions[toothNumber] = condition;
    localStorage.setItem("toothConditions", JSON.stringify(savedConditions));
}


document.getElementById("toothForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const toothNumber = document.getElementById("toothNumber").value;
    const condition = document.getElementById("record_details").value;
    const idNumber = "<?php echo $id_number; ?>"; 

    formData.append('id_number', idNumber);

    fetch('../backend/submit_dental_record.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeToothModal();
            Swal.fire('Success', 'Dental record added successfully!', 'success');
            updateToothColor(toothNumber, condition);
        } else {
            Swal.fire('Error', data.message || 'Failed to add dental record.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while submitting the form.', 'error');
    });
});

function closeToothModal() {
    document.getElementById("toothModal").style.display = "none";
}

