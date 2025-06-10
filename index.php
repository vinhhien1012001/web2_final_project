<?php
include 'database.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
}

$patient_count = $doctor_count = $appointment_count = 0;

$patient_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM patients");
$doctor_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM doctors");
$appointment_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments");

if ($row = mysqli_fetch_assoc($patient_result)) {
    $patient_count = $row['total'];
}
if ($row = mysqli_fetch_assoc($doctor_result)) {
    $doctor_count = $row['total'];
}
if ($row = mysqli_fetch_assoc($appointment_result)) {
    $appointment_count = $row['total'];
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $id = mysqli_real_escape_string($conn, $id);

    $deleteQuery = "DELETE FROM products WHERE id = $id";
    if (mysqli_query($conn, $deleteQuery)) {
        $_SESSION['message'] = "Product deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting product: " . mysqli_error($conn);
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Credential</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="home">
        <div class="home_header">
            <h2>Welcome, <?php echo $_SESSION['email']; ?></h2>
            <a class="button" href="./logout.php">Logout</a>
        </div>

        <div class="stat-box">
            <h3>Quick Stats</h3>
            <ul>
                <li>Total Patients: <?= $patient_count ?></li>
                <li>Total Doctors: <?= $doctor_count ?></li>
                <li>Total Appointments: <?= $appointment_count ?></li>
            </ul>
        </div>

        <div class="stat-box">
            <h3>Navigation</h3>
            <div class="nav-links">
                <a class="button" href="patients.php">Manage Patients</a>
                <a class="button" href="doctors.php">Manage Doctors</a>
                <a class="button" href="appointments.php">Manage Appointments</a>
                <a class="button" href="medications.php">Manage Medications</a>
                <a class="button" href="feedbacks.php">Submit/View Feedback</a>
                <a class="button" href="chart.php">View Charts</a>
            </div>
        </div>
    </div>
</body>

</html>