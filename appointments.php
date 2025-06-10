<?php include 'database.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Book Appointment</h2>
        <form method="POST">
            Doctor:
            <select name="doctor_id">
                <option value="">Select</option>
                <?php
                $res = $conn->query("SELECT * FROM doctors");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='{$row['doctor_id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            Patient:
            <select name="patient_id">
                <option value="">Select</option>
                <?php
                $res = $conn->query("SELECT * FROM patients");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='{$row['patient_id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            Date: <input type="datetime-local" name="appointment_date">
            Note: <textarea name="notes"></textarea>
            <input type="submit" name="submit" value="Add appointment">
        </form>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        $doctor_id = $_POST['doctor_id'];
        $patient_id = $_POST['patient_id'];
        $appointment_date = $_POST['appointment_date'];
        $notes = $_POST['notes'];
        $conn->query("INSERT INTO appointments (doctor_id, patient_id, appointment_date, notes) VALUES ('$doctor_id', '$patient_id', '$appointment_date', '$notes') ");

        echo "APPOINTMENT BOOKED!";
    }
    ?>
</body>

</html>