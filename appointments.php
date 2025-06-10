<?php
include 'database.php';
session_start();

// Handle Add Appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_appointment'])) {
    $doctor_id = $_POST['doctor_id'];
    $patient_id = $_POST['patient_id'];
    $appointment_date = $_POST['appointment_date'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("INSERT INTO appointments (doctor_id, patient_id, appointment_date, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $doctor_id, $patient_id, $appointment_date, $notes);
    $stmt->execute();
    
    header("Location: appointments.php");
    exit();
}

// Handle Update Appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $doctor_id = $_POST['doctor_id'];
    $patient_id = $_POST['patient_id'];
    $appointment_date = $_POST['appointment_date'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("UPDATE appointments SET doctor_id = ?, patient_id = ?, appointment_date = ?, notes = ? WHERE appointment_id = ?");
    $stmt->bind_param("iissi", $doctor_id, $patient_id, $appointment_date, $notes, $appointment_id);
    $stmt->execute();
    
    header("Location: appointments.php");
    exit();
}

// Handle Delete Appointment
if (isset($_GET['delete'])) {
    $appointment_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    
    header("Location: appointments.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Appointments Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                <i class="bi bi-plus-circle"></i> Add New Appointment
            </button>
        </div>
        
        <!-- Appointments List -->
        <div class="card">
            <div class="card-header">
                <h4>Appointments List</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor</th>
                            <th>Patient</th>
                            <th>Date & Time</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $appointments = $conn->query("SELECT a.*, d.name as doctor_name, p.name as patient_name 
                                                    FROM appointments a 
                                                    JOIN doctors d ON a.doctor_id = d.doctor_id 
                                                    JOIN patients p ON a.patient_id = p.patient_id 
                                                    ORDER BY a.appointment_date");
                        while ($appointment = $appointments->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $appointment['appointment_id'] ?></td>
                                <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($appointment['appointment_date'])) ?></td>
                                <td><?= htmlspecialchars($appointment['notes']) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal<?= $appointment['appointment_id'] ?>">
                                        Edit
                                    </button>
                                    <a href="?delete=<?= $appointment['appointment_id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this appointment?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $appointment['appointment_id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Appointment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                                <div class="mb-3">
                                                    <label for="edit_doctor_id<?= $appointment['appointment_id'] ?>" class="form-label">Doctor</label>
                                                    <select class="form-control" name="doctor_id" required>
                                                        <?php
                                                        $doctors = $conn->query("SELECT * FROM doctors");
                                                        while ($doctor = $doctors->fetch_assoc()):
                                                        ?>
                                                            <option value="<?= $doctor['doctor_id'] ?>" 
                                                                    <?= $doctor['doctor_id'] == $appointment['doctor_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($doctor['name']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_patient_id<?= $appointment['appointment_id'] ?>" class="form-label">Patient</label>
                                                    <select class="form-control" name="patient_id" required>
                                                        <?php
                                                        $patients = $conn->query("SELECT * FROM patients");
                                                        while ($patient = $patients->fetch_assoc()):
                                                        ?>
                                                            <option value="<?= $patient['patient_id'] ?>" 
                                                                    <?= $patient['patient_id'] == $appointment['patient_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($patient['name']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_appointment_date<?= $appointment['appointment_id'] ?>" class="form-label">Date & Time</label>
                                                    <input type="datetime-local" class="form-control" 
                                                           name="appointment_date" 
                                                           value="<?= date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])) ?>" 
                                                           required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_notes<?= $appointment['appointment_id'] ?>" class="form-label">Notes</label>
                                                    <textarea class="form-control" name="notes" rows="3"><?= htmlspecialchars($appointment['notes']) ?></textarea>
                                                </div>
                                                <button type="submit" name="update_appointment" class="btn btn-primary">Update</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

  

    <div class="modal fade" id="addAppointmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="doctor_id" class="form-label">Doctor</label>
                        <select class="form-control" id="doctor_id" name="doctor_id" required>
                            <option value="">Select Doctor</option>
                            <?php
                            $doctors = $conn->query("SELECT d.*, s.name as specialty_name 
                                                   FROM doctors d 
                                                   LEFT JOIN specialties s ON d.specialty_id = s.specialty_id");
                            while ($doctor = $doctors->fetch_assoc()):
                            ?>
                                <option value="<?= $doctor['doctor_id'] ?>">
                                    <?= htmlspecialchars($doctor['name']) ?> (<?= htmlspecialchars($doctor['specialty_name']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Patient</label>
                        <select class="form-control" id="patient_id" name="patient_id" required>
                            <option value="">Select Patient</option>
                            <?php
                            $patients = $conn->query("SELECT * FROM patients");
                            while ($patient = $patients->fetch_assoc()):
                            ?>
                                <option value="<?= $patient['patient_id'] ?>">
                                    <?= htmlspecialchars($patient['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="appointment_date" class="form-label">Appointment Date & Time</label>
                        <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_appointment" class="btn btn-primary">Book Appointment</button>
                </form>
            </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>