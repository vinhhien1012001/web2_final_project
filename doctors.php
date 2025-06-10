<?php
require_once 'database.php';

// Create (Add new doctor)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $name = $_POST['name'];
    $specialty_id = $_POST['specialty_id'];
    
    $sql = "INSERT INTO doctors (name, specialty_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $specialty_id);
    $stmt->execute();
    
    header("Location: doctors.php");
    exit();
}

// Update doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_doctor'])) {
    $doctor_id = $_POST['doctor_id'];
    $name = $_POST['name'];
    $specialty_id = $_POST['specialty_id'];
    
    $sql = "UPDATE doctors SET name = ?, specialty_id = ? WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $name, $specialty_id, $doctor_id);
    $stmt->execute();
    
    header("Location: doctors.php");
    exit();
}

// Delete doctor
if (isset($_GET['delete'])) {
    $doctor_id = $_GET['delete'];
    
    $sql = "DELETE FROM doctors WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    
    header("Location: doctors.php");
    exit();
}

// Fetch all doctors with their specialties
$sql = "SELECT d.*, s.name as specialty_name 
        FROM doctors d 
        LEFT JOIN specialties s ON d.specialty_id = s.specialty_id";
$result = $conn->query($sql);
$doctors = $result->fetch_all(MYSQLI_ASSOC);

// Fetch all specialties for the dropdown
$specialties_result = $conn->query("SELECT * FROM specialties");
$specialties = $specialties_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Doctors Management</h2>
        
        <!-- Add Doctor Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Add New Doctor</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Doctor Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="specialty_id" class="form-label">Specialty</label>
                        <select class="form-control" id="specialty_id" name="specialty_id" required>
                            <?php foreach ($specialties as $specialty): ?>
                                <option value="<?= $specialty['specialty_id'] ?>"><?= htmlspecialchars($specialty['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_doctor" class="btn btn-primary">Add Doctor</button>
                </form>
            </div>
        </div>

        <!-- Doctors List -->
        <div class="card">
            <div class="card-header">
                <h4>Doctors List</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Specialty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td><?= $doctor['doctor_id'] ?></td>
                                <td><?= htmlspecialchars($doctor['name']) ?></td>
                                <td><?= htmlspecialchars($doctor['specialty_name']) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal<?= $doctor['doctor_id'] ?>">
                                        Edit
                                    </button>
                                    <a href="?delete=<?= $doctor['doctor_id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this doctor?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $doctor['doctor_id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Doctor</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="doctor_id" value="<?= $doctor['doctor_id'] ?>">
                                                <div class="mb-3">
                                                    <label for="edit_name<?= $doctor['doctor_id'] ?>" class="form-label">Name</label>
                                                    <input type="text" class="form-control" 
                                                           id="edit_name<?= $doctor['doctor_id'] ?>" 
                                                           name="name" 
                                                           value="<?= htmlspecialchars($doctor['name']) ?>" 
                                                           required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_specialty<?= $doctor['doctor_id'] ?>" class="form-label">Specialty</label>
                                                    <select class="form-control" 
                                                            id="edit_specialty<?= $doctor['doctor_id'] ?>" 
                                                            name="specialty_id" 
                                                            required>
                                                        <?php foreach ($specialties as $specialty): ?>
                                                            <option value="<?= $specialty['specialty_id'] ?>" 
                                                                    <?= $specialty['specialty_id'] == $doctor['specialty_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($specialty['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <button type="submit" name="update_doctor" class="btn btn-primary">Update</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>