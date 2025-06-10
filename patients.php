<?php 
include 'database.php';
session_start();

// Handle Add Patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patient'])) {
    $name = $_POST['name'];
    $birth_date = $_POST['birth_date'];
    $phone = $_POST['phone'];
    
    $stmt = $conn->prepare("INSERT INTO patients (name, birth_date, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $birth_date, $phone);
    $stmt->execute();
    
    header("Location: patients.php");
    exit();
}

// Handle Update Patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_patient'])) {
    $patient_id = $_POST['patient_id'];
    $name = $_POST['name'];
    $birth_date = $_POST['birth_date'];
    $phone = $_POST['phone'];
    
    $stmt = $conn->prepare("UPDATE patients SET name = ?, birth_date = ?, phone = ? WHERE patient_id = ?");
    $stmt->bind_param("sssi", $name, $birth_date, $phone, $patient_id);
    $stmt->execute();
    
    header("Location: patients.php");
    exit();
}

// Handle Delete Patient
if (isset($_GET['delete'])) {
    $patient_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    
    header("Location: patients.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Patients Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                <i class="bi bi-plus-circle"></i> Add New Patient
            </button>
        </div>
        
        <!-- Patients List -->
        <div class="card">
            <div class="card-header">
                <h4>Patients List</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Birth Date</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM patients");
                        while ($patient = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $patient['patient_id'] ?></td>
                                <td><?= htmlspecialchars($patient['name']) ?></td>
                                <td><?= $patient['birth_date'] ?></td>
                                <td><?= htmlspecialchars($patient['phone']) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal<?= $patient['patient_id'] ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <a href="?delete=<?= $patient['patient_id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this patient?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $patient['patient_id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Patient</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="patient_id" value="<?= $patient['patient_id'] ?>">
                                                <div class="mb-3">
                                                    <label for="edit_name<?= $patient['patient_id'] ?>" class="form-label">Name</label>
                                                    <input type="text" class="form-control" 
                                                           id="edit_name<?= $patient['patient_id'] ?>" 
                                                           name="name" 
                                                           value="<?= htmlspecialchars($patient['name']) ?>" 
                                                           required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_birth_date<?= $patient['patient_id'] ?>" class="form-label">Birth Date</label>
                                                    <input type="date" class="form-control" 
                                                           id="edit_birth_date<?= $patient['patient_id'] ?>" 
                                                           name="birth_date" 
                                                           value="<?= $patient['birth_date'] ?>" 
                                                           required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_phone<?= $patient['patient_id'] ?>" class="form-label">Phone</label>
                                                    <input type="text" class="form-control" 
                                                           id="edit_phone<?= $patient['patient_id'] ?>" 
                                                           name="phone" 
                                                           value="<?= htmlspecialchars($patient['phone']) ?>" 
                                                           required>
                                                </div>
                                                <div class="text-end">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_patient" class="btn btn-primary">Update</button>
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

    <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Birth Date</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_patient" class="btn btn-primary">Add Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>