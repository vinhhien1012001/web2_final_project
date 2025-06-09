<?php
include 'database.php';

$error = '';
$success = '';

// Catch mysqli exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Use prepared statements
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        $stmt->execute();

        $success = "Register successful! <a href='login.php'>Login here</a>";
        $stmt->close();
    }
} catch (mysqli_sql_exception $e) {
    // Handle duplicate email specifically
    if (str_contains($e->getMessage(), 'Duplicate entry')) {
        $error = "This email is already registered.";
    } else {
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>REGISTER</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form method="POST" class="register">
        <h2>Register</h2>

        <?php if ($error): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color: green;"><?= $success ?></p>
        <?php endif; ?>

        <input type="text" name="name" required placeholder="Name" />
        <input type="email" name="email" required placeholder="Email" />
        <input type="password" name="password" required placeholder="Password" />
        <button type="submit">Register</button>

        <p>Already have an account? <a href="./login.php">Login</a></p>
    </form>
</body>

</html>