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
    <form action="./login.php" method="POST" class="login">
        <h2>Login</h2>
        <input type="email" name="email" required placeholder="Email">

        <input type="password" name="password" required placeholder="Password">

        <button type="submit">Login</button>
        <p>Don't have an account?
            <a href="./register.php">Register here</a>
        </p>
    </form>

    <?php
    session_start();

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "No user found!";
    }
    ?>

</body>

</html>