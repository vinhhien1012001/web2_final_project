<?php 
include 'database.php';
session_start();

// Handle Add Medication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_medication'])) {
    $appointment_id = $_POST['appointment_id'];
    $name = $_POST['name'];
    $dosage = $_POST['dosage'];
    
    $stmt = $conn->prepare("INSERT INTO medications (appointment_id, name, dosage) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $appointment_id, $name, $dosage);
    
    if ($stmt->execute()) {
        $success_message = "Medication added successfully!";
    } else {
        $error_message = "Error adding medication: " . $conn->error;
    }
}

// Handle Update Medication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_medication'])) {
    $medication_id = $_POST['medication_id'];
    $appointment_id = $_POST['appointment_id'];
    $name = $_POST['name'];
    $dosage = $_POST['dosage'];
    
    $stmt = $conn->prepare("UPDATE medications SET appointment_id = ?, name = ?, dosage = ? WHERE medication_id = ?");
    $stmt->bind_param("issi", $appointment_id, $name, $dosage, $medication_id);
    
    if ($stmt->execute()) {
        $success_message = "Medication updated successfully!";
    } else {
        $error_message = "Error updating medication: " . $conn->error;
    }
}

// Handle Delete Medication
if (isset($_GET['delete'])) {
    $medication_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM medications WHERE medication_id = ?");
    $stmt->bind_param("i", $medication_id);
    
    if ($stmt->execute()) {
        $success_message = "Medication deleted successfully!";
    } else {
        $error_message = "Error deleting medication: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medications Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Medications Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicationModal">
                <i class="bi bi-plus-circle"></i> Add New Medication
            </button>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Medications List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Appointment</th>
                                <th>Medication Name</th>
                                <th>Dosage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $medications = $conn->query("SELECT m.*, a.appointment_date, d.name as doctor_name, p.name as patient_name 
                                                       FROM medications m 
                                                       JOIN appointments a ON m.appointment_id = a.appointment_id 
                                                       JOIN doctors d ON a.doctor_id = d.doctor_id 
                                                       JOIN patients p ON a.patient_id = p.patient_id 
                                                       ORDER BY a.appointment_date");
                            while ($medication = $medications->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?= $medication['medication_id'] ?></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold"><?= htmlspecialchars($medication['doctor_name']) ?></span>
                                            <span class="text-muted"><?= htmlspecialchars($medication['patient_name']) ?></span>
                                            <small class="text-muted">
                                                <?= date('Y-m-d H:i', strtotime($medication['appointment_date'])) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($medication['name']) ?></td>
                                    <td><?= htmlspecialchars($medication['dosage']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal<?= $medication['medication_id'] ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <a href="?delete=<?= $medication['medication_id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this medication?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $medication['medication_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Medication</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST">
                                                    <input type="hidden" name="medication_id" value="<?= $medication['medication_id'] ?>">
                                                    <div class="mb-3">
                                                        <label for="edit_appointment_id<?= $medication['medication_id'] ?>" class="form-label">Appointment</label>
                                                        <select class="form-control" name="appointment_id" required>
                                                            <?php
                                                            $appointments = $conn->query("SELECT a.*, d.name as doctor_name, p.name as patient_name 
                                                                                        FROM appointments a 
                                                                                        JOIN doctors d ON a.doctor_id = d.doctor_id 
                                                                                        JOIN patients p ON a.patient_id = p.patient_id 
                                                                                        ORDER BY a.appointment_date DESC");
                                                            while ($appointment = $appointments->fetch_assoc()):
                                                            ?>
                                                                <option value="<?= $appointment['appointment_id'] ?>" 
                                                                        <?= $appointment['appointment_id'] == $medication['appointment_id'] ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($appointment['doctor_name']) ?> - 
                                                                    <?= htmlspecialchars($appointment['patient_name']) ?> 
                                                                    (<?= date('Y-m-d H:i', strtotime($appointment['appointment_date'])) ?>)
                                                                </option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_name<?= $medication['medication_id'] ?>" class="form-label">Medication Name</label>
                                                        <input type="text" class="form-control" 
                                                               name="name" 
                                                               value="<?= htmlspecialchars($medication['name']) ?>" 
                                                               required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_dosage<?= $medication['medication_id'] ?>" class="form-label">Dosage</label>
                                                        <input type="text" class="form-control" 
                                                               name="dosage" 
                                                               value="<?= htmlspecialchars($medication['dosage']) ?>" 
                                                               required>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="update_medication" class="btn btn-primary">Update</button>
                                                    </div>
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
    </div>

    <!-- Add Medication Modal -->
    <div class="modal fade" id="addMedicationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Medication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="appointment_id" class="form-label">Appointment</label>
                            <select class="form-control" id="appointment_id" name="appointment_id" required>
                                <option value="">Select Appointment</option>
                                <?php
                                $appointments = $conn->query("SELECT a.*, d.name as doctor_name, p.name as patient_name 
                                                            FROM appointments a 
                                                            JOIN doctors d ON a.doctor_id = d.doctor_id 
                                                            JOIN patients p ON a.patient_id = p.patient_id 
                                                            ORDER BY a.appointment_date DESC");
                                while ($appointment = $appointments->fetch_assoc()):
                                ?>
                                    <option value="<?= $appointment['appointment_id'] ?>">
                                        <?= htmlspecialchars($appointment['doctor_name']) ?> - 
                                        <?= htmlspecialchars($appointment['patient_name']) ?> 
                                        (<?= date('Y-m-d H:i', strtotime($appointment['appointment_date'])) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Medication Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="dosage" class="form-label">Dosage</label>
                            <input type="text" class="form-control" id="dosage" name="dosage" required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_medication" class="btn btn-primary">Add Medication</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>