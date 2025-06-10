<?php 
include 'database.php';
session_start();

// Handle Add Feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_feedback'])) {
    $doctor_id = $_POST['doctor_id'];
    $patient_id = $_POST['patient_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    $stmt = $conn->prepare("INSERT INTO feedback (doctor_id, patient_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $doctor_id, $patient_id, $rating, $comment);
    $stmt->execute();
    
    header("Location: feedbacks.php");
    exit();
}

// Handle Update Feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_feedback'])) {
    $feedback_id = $_POST['feedback_id'];
    $doctor_id = $_POST['doctor_id'];
    $patient_id = $_POST['patient_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    $stmt = $conn->prepare("UPDATE feedback SET doctor_id = ?, patient_id = ?, rating = ?, comment = ? WHERE feedback_id = ?");
    $stmt->bind_param("iiisi", $doctor_id, $patient_id, $rating, $comment, $feedback_id);
    $stmt->execute();
    
    header("Location: feedbacks.php");
    exit();
}

// Handle Delete Feedback
if (isset($_GET['delete'])) {
    $feedback_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    
    header("Location: feedbacks.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container my-4">
        <h2>Feedback Management</h2>
        
        <!-- Add Feedback Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Add New Feedback</h4>
            </div>
            <div class="card-body">
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
                        <label for="rating" class="form-label">Rating</label>
                        <select class="form-control" id="rating" name="rating" required>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="add_feedback" class="btn btn-primary">Submit Feedback</button>
                </form>
            </div>
        </div>

        <!-- Feedback List -->
        <div class="card">
            <div class="card-header">
                <h4>Feedback List</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor</th>
                            <th>Patient</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $feedbacks = $conn->query("SELECT f.*, d.name as doctor_name, p.name as patient_name 
                                                 FROM feedback f 
                                                 JOIN doctors d ON f.doctor_id = d.doctor_id 
                                                 JOIN patients p ON f.patient_id = p.patient_id 
                                                 ORDER BY f.created_at DESC");
                        while ($feedback = $feedbacks->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $feedback['feedback_id'] ?></td>
                                <td><?= htmlspecialchars($feedback['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($feedback['patient_name']) ?></td>
                                <td><?= str_repeat('★', $feedback['rating']) . str_repeat('☆', 5 - $feedback['rating']) ?></td>
                                <td><?= htmlspecialchars($feedback['comment']) ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($feedback['created_at'])) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal<?= $feedback['feedback_id'] ?>">
                                        Edit
                                    </button>
                                    <a href="?delete=<?= $feedback['feedback_id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this feedback?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $feedback['feedback_id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Feedback</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="feedback_id" value="<?= $feedback['feedback_id'] ?>">
                                                <div class="mb-3">
                                                    <label for="edit_doctor_id<?= $feedback['feedback_id'] ?>" class="form-label">Doctor</label>
                                                    <select class="form-control" name="doctor_id" required>
                                                        <?php
                                                        $doctors = $conn->query("SELECT * FROM doctors");
                                                        while ($doctor = $doctors->fetch_assoc()):
                                                        ?>
                                                            <option value="<?= $doctor['doctor_id'] ?>" 
                                                                    <?= $doctor['doctor_id'] == $feedback['doctor_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($doctor['name']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_patient_id<?= $feedback['feedback_id'] ?>" class="form-label">Patient</label>
                                                    <select class="form-control" name="patient_id" required>
                                                        <?php
                                                        $patients = $conn->query("SELECT * FROM patients");
                                                        while ($patient = $patients->fetch_assoc()):
                                                        ?>
                                                            <option value="<?= $patient['patient_id'] ?>" 
                                                                    <?= $patient['patient_id'] == $feedback['patient_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($patient['name']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_rating<?= $feedback['feedback_id'] ?>" class="form-label">Rating</label>
                                                    <select class="form-control" name="rating" required>
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == $feedback['rating'] ? 'selected' : '' ?>>
                                                                <?= $i ?> Star<?= $i > 1 ? 's' : '' ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_comment<?= $feedback['feedback_id'] ?>" class="form-label">Comment</label>
                                                    <textarea class="form-control" name="comment" rows="3" required><?= htmlspecialchars($feedback['comment']) ?></textarea>
                                                </div>
                                                <button type="submit" name="update_feedback" class="btn btn-primary">Update</button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
