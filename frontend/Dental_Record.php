<?php
session_start();
$user_data = null;
$dental_records = [];
$error_message = null;
$success_message = null;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index");
    exit();
}
include '../backend/db_connect.php';
include "../backend/func_dental_record.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="../img/NEUST.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Record Input</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <link href="../css/dental_style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div id="wrapper">
        <?php include 'sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                </nav>
                <div class="container mt-5">
                    <div class="card shadow">
                        <div class="card-header py-3">

                            <h3 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-tooth"></i>Dental
                                Record</h3>

                        </div>
                        <div class="card-body">
                            <h4>User Information</h4>

                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                            <?php endif; ?>

                            <form action="dental_record.php" method="POST">
                                <div class="form-group">
                                    <label for="id_number">ID Number</label>
                                    <input type="text" list="studentList" id="id_number" name="id_number" class="form-control" placeholder="Type Number or Name" required>
                                    <datalist id="studentList"></datalist>

                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </form>

                            <?php if ($user_data): ?>
                                <h5 class="mt-4">Personal Information</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID Number</th>
                                                <th>Name</th>
                                                <th>Campus</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user_data['id_number']); ?></td>
                                                <td><?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['middle_name'] . ' ' . $user_data['last_name']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($user_data['campus']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="tooth-chart">

                                    <div class="tooth-section">
                                        <h4 class="section-title">UPPER LEFT</h4>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div class="tooth-option" data-tooth="18" onclick="openToothModal(18)">18</div>
                                        <div class="tooth-option" data-tooth="17" onclick="openToothModal(17)">17</div>
                                        <div class="tooth-option" data-tooth="16" onclick="openToothModal(16)">16</div>
                                        <div class="tooth-option" data-tooth="15" onclick="openToothModal(15)">15</div>
                                        <div class="tooth-option" data-tooth="14" onclick="openToothModal(14)">14</div>
                                        <div class="tooth-option" data-tooth="13" onclick="openToothModal(13)">13</div>
                                        <div class="tooth-option" data-tooth="12" onclick="openToothModal(12)">12</div>
                                        <div class="tooth-option" data-tooth="11" onclick="openToothModal(11)">11</div>
                                    </div>

                                    <div class="tooth-section">
                                        <h4 class="section-title">UPPER RIGHT</h4>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div class="tooth-option" data-tooth="21" onclick="openToothModal(21)">21</div>
                                        <div class="tooth-option" data-tooth="22" onclick="openToothModal(22)">22</div>
                                        <div class="tooth-option" data-tooth="23" onclick="openToothModal(23)">23</div>
                                        <div class="tooth-option" data-tooth="24" onclick="openToothModal(24)">24</div>
                                        <div class="tooth-option" data-tooth="25" onclick="openToothModal(25)">25</div>
                                        <div class="tooth-option" data-tooth="26" onclick="openToothModal(26)">26</div>
                                        <div class="tooth-option" data-tooth="27" onclick="openToothModal(27)">27</div>
                                        <div class="tooth-option" data-tooth="28" onclick="openToothModal(28)">28</div>
                                    </div>

                                    <div class="tooth-section">
                                        <h4 class="section-title">LOWER LEFT</h4>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div class="tooth-option" data-tooth="48" onclick="openToothModal(48)">48</div>
                                        <div class="tooth-option" data-tooth="47" onclick="openToothModal(47)">47</div>
                                        <div class="tooth-option" data-tooth="46" onclick="openToothModal(46)">46</div>
                                        <div class="tooth-option" data-tooth="45" onclick="openToothModal(45)">45</div>
                                        <div class="tooth-option" data-tooth="44" onclick="openToothModal(44)">44</div>
                                        <div class="tooth-option" data-tooth="43" onclick="openToothModal(43)">43</div>
                                        <div class="tooth-option" data-tooth="42" onclick="openToothModal(42)">42</div>
                                        <div class="tooth-option" data-tooth="41" onclick="openToothModal(41)">41</div>
                                    </div>

                                    <div class="tooth-section">
                                        <h4 class="section-title">LOWER RIGHT</h4>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div class="tooth-option" data-tooth="31" onclick="openToothModal(31)">31</div>
                                        <div class="tooth-option" data-tooth="32" onclick="openToothModal(32)">32</div>
                                        <div class="tooth-option" data-tooth="33" onclick="openToothModal(33)">33</div>
                                        <div class="tooth-option" data-tooth="34" onclick="openToothModal(34)">34</div>
                                        <div class="tooth-option" data-tooth="35" onclick="openToothModal(35)">35</div>
                                        <div class="tooth-option" data-tooth="36" onclick="openToothModal(36)">36</div>
                                        <div class="tooth-option" data-tooth="37" onclick="openToothModal(37)">37</div>
                                        <div class="tooth-option" data-tooth="38" onclick="openToothModal(38)">38</div>
                                    </div>

                                </div>
                                <div class="tooth-chart-label1">
                                    <div class="tooth-section-chart">
                                        <div></div>
                                        <h5 class="">HEALTHY</h5>
                                        <div></div>
                                        <h5 class="">CAVITY</h4>
                                            <div></div>
                                            <h5 class="">FILLING</h5>
                                            <div></div>
                                            <h5 class="">EXTRACTED</h5>
                                            <div></div>
                                            <div></div>
                                            <i class="fas fa-circle circle-icon-1"></i>
                                            <div></div>
                                            <i class="fas fa-circle circle-icon-2"></i>
                                            <div></div>
                                            <i class="fas fa-circle circle-icon-3"></i>
                                            <div></div>
                                            <i class="fas fa-circle circle-icon-4"></i>
                                            <div></div>
                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="toothModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeToothModal()">&times;</span>
                <h4>Tooth Condition (T<span id="toothNumberDisplay"></span>)</h4>
                <form id="toothForm">
                    <input type="hidden" id="toothNumber" name="tooth">
                    <label for="record_details">Condition:</label>
                    <select id="record_details" name="record_details" required onchange="updateToothColor()">
                        <option value="Healthy">Healthy</option>
                        <option value="Cavity">Cavity</option>
                        <option value="Filling">Filling</option>
                        <option value="Extracted">Extracted</option>
                    </select>
                    <button type="submit">Save</button>
                </form>
                <hr>
                <h5>Previous Conditions</h5>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Condition</th>
                        </tr>
                    </thead>
                    <tbody id="previousConditions">
                        <!-- Dynamic rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../vendor/chart.js/Chart.min.js"></script>


    <script>
        document.getElementById('id_number').addEventListener('input', function () {
            let val = this.value;
            if (val.length > 0) {
                fetch('searchid.php?q=' + encodeURIComponent(val))
                    .then(res => res.json())
                    .then(data => {
                        let datalist = document.getElementById('studentList');
                        datalist.innerHTML = '';
                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.id_number;
                            option.textContent = item.full_name;
                            datalist.appendChild(option);
                        });
                    });
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let idNumber = "<?php echo isset($id_number) ? htmlspecialchars($id_number, ENT_QUOTES, 'UTF-8') : ''; ?>";

            if (idNumber) {
                fetchToothConditions(idNumber);
            }

            loadSavedToothColors();
        });

        /**
         * Fetches tooth conditions for a specific student
         */
        function fetchToothConditions(idNumber) {
            fetch(`../backend/fetch_tooth_condition.php?id_number=${idNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        let toothConditions = {};

                        data.forEach(record => {
                            const toothNumber = record.tooth_number;
                            const latestCondition = record.record_details;
                            const conditionColor = record.condition_color;

                            updateToothColor(toothNumber, conditionColor);
                            toothConditions[toothNumber] = latestCondition;
                        });

                        localStorage.setItem(`toothConditions_${idNumber}`, JSON.stringify(toothConditions));
                    } else {
                        console.log("No records found.");
                    }
                })
                .catch(error => console.error("Error fetching tooth conditions:", error));
        }

        /**
         * Updates the tooth color based on the condition
         */
        function updateToothColor(toothNumber, color) {
            let toothElement = document.querySelector(`.tooth-option[data-tooth="${toothNumber}"]`);
            if (!toothElement) return;

            toothElement.style.backgroundColor = color;
        }

        /**
         * Loads saved tooth colors from local storage
         */
        function loadSavedToothColors() {
            let idNumber = "<?php echo isset($id_number) ? htmlspecialchars($id_number, ENT_QUOTES, 'UTF-8') : ''; ?>";
            let savedConditions = JSON.parse(localStorage.getItem(`toothConditions_${idNumber}`));

            if (savedConditions) {
                Object.keys(savedConditions).forEach(toothNumber => {
                    let color = getConditionColor(savedConditions[toothNumber]);
                    updateToothColor(toothNumber, color);
                });
            }
        }

        /**
         * Returns color for a specific condition
         */
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

        /**
         * Opens the modal for a specific tooth and loads condition history
         */
        function openToothModal(toothNumber) {
            document.getElementById("toothNumber").value = toothNumber;
            document.getElementById("toothNumberDisplay").innerText = toothNumber;
            document.getElementById("toothModal").style.display = "flex";

            let idNumber = "<?php echo isset($id_number) ? htmlspecialchars($id_number, ENT_QUOTES, 'UTF-8') : ''; ?>";

            console.log(`Fetching conditions for id_number: ${idNumber}, tooth: ${toothNumber}`);

            fetch(`../backend/fetch_tooth_condition.php?id_number=${idNumber}&tooth=${toothNumber}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Backend response:", data); // Debugging: Log the response

                    if (data.error) {
                        console.error("Error:", data.error);
                        document.getElementById("previousConditions").innerHTML = "<tr><td colspan='2'>Error loading records</td></tr>";
                        return;
                    }

                    let conditionsList = "";

                    if (data.length > 0) {
                        data.forEach(record => {
                            conditionsList += `<tr>
                        <td>${record.date_created}</td>
                        <td>${record.record_details}</td>
                    </tr>`;
                        });
                    } else {
                        conditionsList = "<tr><td colspan='2'>No previous records</td></tr>";
                    }

                    document.getElementById("previousConditions").innerHTML = conditionsList;
                })
                .catch(error => {
                    console.error("Error fetching tooth conditions:", error); // Debugging: Log the error
                    document.getElementById("previousConditions").innerHTML = "<tr><td colspan='2'>Error loading records</td></tr>";
                });
        }

        /**
         * Submits the new condition and updates the UI
         */
        document.getElementById("toothForm").addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const toothNumber = document.getElementById("toothNumber").value;
            const condition = document.getElementById("record_details").value;
            const idNumber = "<?php echo isset($id_number) ? htmlspecialchars($id_number, ENT_QUOTES, 'UTF-8') : ''; ?>";

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
                        updateToothColor(toothNumber, getConditionColor(condition));

                        let savedConditions = JSON.parse(localStorage.getItem(`toothConditions_${idNumber}`)) || {};
                        savedConditions[toothNumber] = condition;
                        localStorage.setItem(`toothConditions_${idNumber}`, JSON.stringify(savedConditions));
                    } else {
                        Swal.fire('Error', data.message || 'Failed to add dental record.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An error occurred while submitting the form.', 'error');
                });
        });

        /**
         * Closes the tooth modal
         */
        function closeToothModal() {
            document.getElementById("toothModal").style.display = "none";
        }
    </script>

</body>

</html>