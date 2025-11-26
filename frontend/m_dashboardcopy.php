<?php
// Database connections
$host = 'localhost';
$username = 'root';
$password = '';
$medical_db = 'neustmrdb';
$student_db = 'neust_student_details';

// Connect to student DB
$conn_student = new mysqli($host, $username, $password, $student_db);
if ($conn_student->connect_error) {
    die("Connection to Student DB failed: " . $conn_student->connect_error);
}

// Connect to medical DB
$conn_medical = new mysqli($host, $username, $password, $medical_db);
if ($conn_medical->connect_error) {
    die("Connection to Medical DB failed: " . $conn_medical->connect_error);
}

// Fetch total students
$total_students = 0;
$total_students_query = "SELECT COUNT(*) AS total_students FROM students";
if ($result = $conn_student->query($total_students_query)) {
    $total_students = $result->fetch_assoc()['total_students'];
    $result->free();
}

// Fetch campus distribution
$campus_data = [];
$campus_query = "SELECT campus, COUNT(*) AS count FROM students GROUP BY campus";
if ($result = $conn_student->query($campus_query)) {
    $campus_data = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
}

// Fetch common health issues (chief complaints)
$health_issues = [];
$health_issues_query = "SELECT chief_complaint, COUNT(*) AS count FROM medical_treatment_records WHERE date_deleted IS NULL GROUP BY chief_complaint";
if ($result = $conn_medical->query($health_issues_query)) {
    $health_issues = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
}

// Fetch monthly chief complaint count (using valid date column, e.g., date_created)
$monthly_data = array_fill(1, 12, 0);
$monthly_query = "SELECT MONTH(date_created) AS month, COUNT(*) AS count FROM medical_treatment_records WHERE date_deleted IS NULL GROUP BY MONTH(date_created)";
if ($result = $conn_medical->query($monthly_query)) {
    while ($row = $result->fetch_assoc()) {
        $monthly_data[(int)$row['month']] = (int)$row['count'];
    }
    $result->free();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../img/NEUST.png" type="image/png">
    <meta charset="UTF-8">
    <title>Student Health Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="../css/dashboard.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="main-content">
                    <div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h1 class="dashboard-title">Student Health Dashboard</h1>
                            <div style="display: flex; gap: 20px;">
                                <div>
                                    <label for="campusFilter">Filter by Campus:</label>
                                    <select id="campusFilter" onchange="filterCampus()">
                                        <option value="all">All Campuses</option>
                                        <?php foreach ($campus_data as $campus): ?>
                                            <option value="<?= htmlspecialchars($campus['campus']); ?>"><?= htmlspecialchars($campus['campus']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="startDate">Start Date:</label>
                                    <input type="date" id="startDate" onchange="filterByDate()">
                                </div>
                                <div>
                                    <label for="endDate">End Date:</label>
                                    <input type="date" id="endDate" onchange="filterByDate()">
                                </div>
                            </div>
                        </div>

                        <div class="stats-cards" style="display: flex; gap: 20px;">
                            <div class="card" style="flex: 1;">
                                <h3>Total Students</h3>
                                <p style="font-size: 28px; text-align: center; margin: 0; padding: 10px; background-color: #f9f9f9; border-radius: 5px;">
                                    <?= $total_students; ?>
                                </p>
                            </div>
                            <div class="card" style="flex: 2;">
                                <h3>Students by Campus</h3>
                                <ul style="list-style: none; padding: 0; margin: 0;">
                                    <?php foreach ($campus_data as $campus): ?>
                                        <li style="padding: 5px 0; border-bottom: 1px solid #ddd;">
                                            <?= $campus['campus'] . ': ' . $campus['count']; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="card" style="flex: 1; width: 100%;">
                                <h3>Total Chief Complaints (<?php echo date('F'); ?>)</h3>
                                <p style="font-size: 28px; text-align: center; margin: 0; padding: 10px; background-color: #f9f9f9; border-radius: 5px;">
                                    <?php
                                    $current_month = date('m');
                                    $current_month_complaints = $monthly_data[(int)$current_month] ?? 0;
                                    echo $current_month_complaints;
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="charts" style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <h3>Chief Complaints Overview</h3>
                                <canvas id="chiefComplaintChart"></canvas>
                            </div>
                            <div style="flex: 1;">
                                <h3>Monthly Chief Complaints </h3>
                                <canvas id="monthlyComplaintChart"></canvas>
                            </div>
                        </div>

                        <div class="card" style="margin-top: 40px;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background-color: #f2f2f2;">
                                        <th style="padding: 10px; border: 1px solid #ddd;">Chief Complaint</th>
                                        <th style="padding: 10px; border: 1px solid #ddd;">Treatment</th>
                                        <th style="padding: 10px; border: 1px solid #ddd;">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($health_issues as $record): ?>
                                        <tr>
                                            <td style="padding: 10px; border: 1px solid #ddd;">
                                                <?= $record['chief_complaint']; ?>
                                            </td>
                                            <td style="padding: 10px; border: 1px solid #ddd;">
                                                <?php
                                                $cc = $conn_medical->real_escape_string($record['chief_complaint']);
                                                $treat_q = "SELECT treatment FROM medical_treatment_records WHERE chief_complaint = '$cc' AND treatment IS NOT NULL AND treatment != '' LIMIT 1";
                                                $treat_res = $conn_medical->query($treat_q);
                                                $treat = $treat_res ? $treat_res->fetch_assoc() : null;
                                                echo $treat['treatment'] ?? 'N/A';
                                                ?>
                                            </td>
                                            <td style="padding: 10px; border: 1px solid #ddd;">
                                                <?= $record['count']; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const barChartColors = [
            'rgba(75, 192, 192, 0.5)',
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)'
        ];

        new Chart(document.getElementById('chiefComplaintChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($health_issues, 'chief_complaint')); ?>,
                datasets: [
                    {
                        label: 'Chief Complaints Count',
                        data: <?= json_encode(array_column($health_issues, 'count')); ?>,
                        backgroundColor: barChartColors,
                        borderColor: barChartColors.map(color => color.replace('0.5', '1')),
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const xValues = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        const complaintData = {
            "Fever": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Common Cold": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Cough": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Flu (Influenza)": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Sore Throat": [0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0],
            "Headache": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Migraine": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Asthma": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Allergic Rhinitis": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Skin Allergies": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Chickenpox": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Measles": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Dengue Fever": [0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0],
            "Hypertension": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Anemia": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Scoliosis": [0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0],
            "Vision Problems": [0, 0, 0, 4, 1, 0, 0, 0, 0, 0, 0, 0],
            "Hearing Impairment": [0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0],
            "Dental Cavities": [0, 0, 0, 7, 1, 0, 0, 0, 0, 0, 0, 0],
            "Back Pain": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Sports Injuries": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Stomach Pain": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Diarrhea": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Constipation": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            "Dehydration": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        };

        const sortedComplaints = Object.entries(complaintData)
            .map(([complaint, data]) => ({ complaint, total: data.reduce((a, b) => a + b, 0), data }))
            .sort((a, b) => b.total - a.total)
            .slice(0, 5);

        const predefinedColors = [
            '#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#A133FF'
        ];

        const top5Datasets = sortedComplaints.map((item, index) => ({
            label: item.complaint,
            data: item.data,
            borderColor: barChartColors[index % barChartColors.length].replace('0.5', '1'),
            backgroundColor: barChartColors[index % barChartColors.length],
            fill: true,
            tension: 0.4
        }));

        new Chart("monthlyComplaintChart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: top5Datasets
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                size: 14,
                                family: 'Arial',
                                weight: 'bold'
                            },
                            color: '#333'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        cornerRadius: 5
                    }
                },
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#555'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(200, 200, 200, 0.5)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#555'
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function filterCampus() {
            const selectedCampus = document.getElementById('campusFilter').value;

            // Filter table rows
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const campusCell = row.querySelector('td:nth-child(1)');
                if (selectedCampus === 'all' || campusCell.textContent.trim() === selectedCampus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter chart data
            const filteredComplaints = Object.entries(complaintData)
                .map(([complaint, data]) => {
                    const filteredData = data.map((count, index) => {
                        return selectedCampus === 'all' ? count : (campusDataByMonth[selectedCampus]?.[complaint]?.[index] || 0);
                    });
                    return { complaint, total: filteredData.reduce((a, b) => a + b, 0), data: filteredData };
                })
                .sort((a, b) => b.total - a.total)
                .slice(0, 5);

            const updatedDatasets = filteredComplaints.map((item, index) => ({
                label: item.complaint,
                data: item.data,
                borderColor: predefinedColors[index % predefinedColors.length],
                backgroundColor: predefinedColors[index % predefinedColors.length] + '33',
                fill: true,
                tension: 0.4
            }));

            // Update the chart
            const chart = Chart.getChart("monthlyComplaintChart");
            chart.data.datasets = updatedDatasets;
            chart.update();
        }

        function filterByDate() {
            const startDate = new Date(document.getElementById('startDate').value);
            const endDate = new Date(document.getElementById('endDate').value);

            if (isNaN(startDate) || isNaN(endDate)) {
                return; // Exit if dates are invalid
            }

            // Filter table rows
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const dateCell = row.querySelector('td:nth-child(3)'); // Assuming the date is in the 3rd column
                const rowDate = new Date(dateCell.textContent.trim());

                if (rowDate >= startDate && rowDate <= endDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter chart data
            const filteredComplaints = Object.entries(complaintData)
                .map(([complaint, data]) => {
                    const filteredData = data.map((count, index) => {
                        const monthDate = new Date(2023, index); // Example year 2023
                        return (monthDate >= startDate && monthDate <= endDate) ? count : null;
                    }).filter(value => value !== null); // Remove null values

                    return { complaint, total: filteredData.reduce((a, b) => a + b, 0), data: filteredData };
                })
                .filter(item => item.total > 0) // Remove complaints with no data in the range
                .sort((a, b) => b.total - a.total)
                .slice(0, 5);

            const updatedDatasets = filteredComplaints.map((item, index) => ({
                label: item.complaint,
                data: item.data,
                borderColor: predefinedColors[index % predefinedColors.length],
                backgroundColor: predefinedColors[index % predefinedColors.length] + '33',
                fill: true,
                tension: 0.4
            }));

            // Update chart labels to match the selected date range
            const filteredLabels = xValues.filter((_, index) => {
                const monthDate = new Date(2023, index); // Example year 2023
                return monthDate >= startDate && monthDate <= endDate;
            });

            // Update the chart
            const chart = Chart.getChart("monthlyComplaintChart");
            chart.data.labels = filteredLabels;
            chart.data.datasets = updatedDatasets;
            chart.options.plugins.title = {
                display: true,
                text: `Data from ${startDate.toLocaleDateString()} to ${endDate.toLocaleDateString()}`
            };
            chart.update();
        }
    </script>

</body>
</html>
