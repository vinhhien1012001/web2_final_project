<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Get doctors by specialty
$specialtyQuery = "SELECT s.name, COUNT(d.doctor_id) as count 
                  FROM specialties s 
                  LEFT JOIN doctors d ON s.specialty_id = d.specialty_id 
                  GROUP BY s.specialty_id";
$specialtyResult = $conn->query($specialtyQuery);
$specialtyLabels = [];
$specialtyData = [];
while($row = $specialtyResult->fetch_assoc()) {
    $specialtyLabels[] = $row['name'];
    $specialtyData[] = $row['count'];
}

// 2. Get patient age distribution
$ageQuery = "SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 20 THEN 'Under 20'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 30 THEN '20-29'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 40 THEN '30-39'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 50 THEN '40-49'
                    ELSE '50+'
                END as age_group,
                COUNT(*) as count
            FROM patients
            GROUP BY age_group
            ORDER BY FIELD(age_group, 'Under 20', '20-29', '30-39', '40-49', '50+')";
$ageResult = $conn->query($ageQuery);
$ageLabels = [];
$ageData = [];
while($row = $ageResult->fetch_assoc()) {
    $ageLabels[] = $row['age_group'];
    $ageData[] = $row['count'];
}

// 3. Get appointments over time (last 6 months)
$appointmentsQuery = "SELECT DATE(appointment_date) as date, COUNT(*) as count 
                     FROM appointments 
                     WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                     GROUP BY DATE(appointment_date)
                     ORDER BY date";
$appointmentsResult = $conn->query($appointmentsQuery);
$appointmentDates = [];
$appointmentCounts = [];
while($row = $appointmentsResult->fetch_assoc()) {
    $appointmentDates[] = $row['date'];
    $appointmentCounts[] = $row['count'];
}

// 4. Get doctor ratings distribution
$ratingsQuery = "SELECT rating, COUNT(*) as count 
                FROM feedback 
                GROUP BY rating 
                ORDER BY rating";
$ratingsResult = $conn->query($ratingsQuery);
$ratingLabels = [];
$ratingData = [];
while($row = $ratingsResult->fetch_assoc()) {
    $ratingLabels[] = $row['rating'] . ' Stars';
    $ratingData[] = $row['count'];
}

// 5. Get medication usage statistics
$medicationsQuery = "SELECT name, COUNT(*) as count 
                    FROM medications 
                    GROUP BY name 
                    ORDER BY count DESC 
                    LIMIT 5";
$medicationsResult = $conn->query($medicationsQuery);
$medicationLabels = [];
$medicationData = [];
while($row = $medicationsResult->fetch_assoc()) {
    $medicationLabels[] = $row['name'];
    $medicationData[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Statistics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 45%;
            margin: 20px;
            display: inline-block;
        }
        .chart-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        h1, h2 {
            text-align: center;
            color: #333;
            margin: 20px 0;
        }
        .system-diagram {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <h1>Hospital Statistics Dashboard</h1>
    
    <!-- System Structure Diagram -->
    <div class="system-diagram">
        <h2>System Structure</h2>
        <canvas id="systemDiagram"></canvas>
    </div>

    <h2>Dynamic Data Analysis</h2>
    <div class="chart-wrapper">
        <div class="chart-container">
            <canvas id="specialtyChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="ageDistributionChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="appointmentsChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="ratingsChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="medicationsChart"></canvas>
        </div>
    </div>

    <script>
        // System Structure Diagram
        new Chart(document.getElementById('systemDiagram'), {
            type: 'radar',
            data: {
                labels: [
                    'User Management',
                    'Doctor Management',
                    'Patient Management',
                    'Appointment System',
                    'Feedback System',
                    'Medication Management'
                ],
                datasets: [{
                    label: 'System Components',
                    data: [5, 5, 5, 5, 5, 5],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Hospital Management System Structure'
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });

        // 1. Doctors by Specialty (Pie Chart)
        new Chart(document.getElementById('specialtyChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($specialtyLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($specialtyData); ?>,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Doctors by Specialty'
                    }
                }
            }
        });

        // 2. Patient Age Distribution (Bar Chart)
        new Chart(document.getElementById('ageDistributionChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($ageLabels); ?>,
                datasets: [{
                    label: 'Number of Patients',
                    data: <?php echo json_encode($ageData); ?>,
                    backgroundColor: '#36A2EB'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Patient Age Distribution'
                    }
                }
            }
        });

        // 3. Appointments Over Time (Line Chart)
        new Chart(document.getElementById('appointmentsChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($appointmentDates); ?>,
                datasets: [{
                    label: 'Number of Appointments',
                    data: <?php echo json_encode($appointmentCounts); ?>,
                    borderColor: '#4BC0C0',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Appointments Over Time'
                    }
                }
            }
        });

        // 4. Doctor Ratings Distribution (Bar Chart)
        new Chart(document.getElementById('ratingsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($ratingLabels); ?>,
                datasets: [{
                    label: 'Number of Ratings',
                    data: <?php echo json_encode($ratingData); ?>,
                    backgroundColor: '#FFCE56'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Doctor Ratings Distribution'
                    }
                }
            }
        });

        // 5. Medication Usage Statistics (Bar Chart)
        new Chart(document.getElementById('medicationsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($medicationLabels); ?>,
                datasets: [{
                    label: 'Number of Prescriptions',
                    data: <?php echo json_encode($medicationData); ?>,
                    backgroundColor: '#9966FF'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top 5 Most Prescribed Medications'
                    }
                }
            }
        });
    </script>
</body>
</html>
