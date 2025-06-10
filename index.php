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
    <link rel="stylesheet" href="./style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="home">
        <!-- Quick Actions -->
        <div class="row quick-actions g-4 dashboard_wrapper">
            <div class="my-col-4">
                <a href="patients.php" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-people text-primary me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Manage Patients</h5>
                                <p class="card-text text-muted mb-0">Add, edit, or remove patient records.</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="my-col-4">
                <a href="doctors.php" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-person-badge text-success me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Manage Doctors</h5>
                                <p class="card-text text-muted mb-0">View and update doctor information.</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="my-col-4">
                <a href="appointments.php" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-calendar-check text-info me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Manage Appointments</h5>
                                <p class="card-text text-muted mb-0">Book, edit, or cancel appointments.</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="my-col-4">
                <a href="medications.php" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-capsule text-warning me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Manage Medications</h5>
                                <p class="card-text text-muted mb-0">Prescribe and track medications.</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="my-col-4">
                <a href="feedbacks.php" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-star text-secondary me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Feedbacks</h5>
                                <p class="card-text text-muted mb-0">View and manage patient feedback.</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="my-col-4">
                <a href="chart.php" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-bar-chart-line text-danger me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">View Charts</h5>
                                <p class="card-text text-muted mb-0">Visualize hospital statistics.</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>