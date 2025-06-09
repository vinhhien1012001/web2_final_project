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
        <h2>Add Doctor</h2>
        <form method="POST">
            Name: <input type="text" name="name">
            Specialty:
            <select name="specialty_id">
                <option value="">Select</option>
                <?php
                $res = $conn->query("SELECT * FROM specialties");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='{$row['specialty_id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            <input type="submit" name="submit" value="Add doctor">
        </form>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $specialty_id = $_POST['specialty_id'];
        $conn->query("INSERT INTO doctors (name, specialty_id) VALUES ('$name','$specialty_id') ");

        echo "DOCTOR ADDED!";
    }
    ?>
</body>

</html>