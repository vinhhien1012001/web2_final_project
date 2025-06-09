<?php include 'database.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PATIENTS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Add patient</h2>
        <form method="POST">
            Name: <input type="text" name="name">
            Birth Date: <input type="date" name="birth_date">
            Phone: <input type="text" name="phone">
            <input type="submit" name="submit" value="add patient">
        </form>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $birth_date = $_POST['birth_date'];
        $phone = $_POST['phone'];
        $conn->query("INSERT INTO patients (name, birth_date, phone) VALUES ('$name','$birth_date','$phone') ");

        echo "PATIENT ADDED!";
    }
    ?>
</body>

</html>