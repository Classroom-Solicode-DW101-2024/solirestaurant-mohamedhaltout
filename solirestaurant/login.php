<?php
require "connection.php"; 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $telCl = trim($_POST['telCl']);

    if (empty($telCl)) {
        echo "Please enter a phone number.";
    } else {
        $stmt = $pdo->prepare("SELECT idClient, telCl, nomCl, prenomCl FROM client WHERE telCl = ?");
        $stmt->execute([$telCl]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['idClient'];  
            $_SESSION['nomCl'] = $user['nomCl'];  
            $_SESSION['prenomCl'] = $user['prenomCl'];
            $_SESSION['telCl'] = $telCl;
            $_SESSION['isLogging'] = true;
        

            header("Location: index.php");
            exit();

        } else {
            echo "User not found. Please register.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>
<body>

<header>
        <a href=""><img src="img/restaurant.png" alt="logo"></a>
    </header>

    <div class="login_form">
    <h2>Welcome</h2>
    <h3>Lorem ipsum dolor sit amet, consectetur</h3>
    <form method="post" action="">
        <input type="number" name="telCl" placeholder="Enter your phone number" required><br>
        <input type="submit" value="Login">
    </form>
    <h3>New here create your account    <a href="register.php"> Register</a></h3>
    <p class= "agree">Lorem ipsum dolor sit amet<br> consectetur 
    Lorem ipsum dolor sit amet, </p>
    <h3>Admin Login!  <a href="admin_login.php"> Log In</a></h3>
    </div>
</body>
</html>
