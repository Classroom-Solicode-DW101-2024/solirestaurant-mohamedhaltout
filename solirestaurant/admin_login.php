<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $adminUser = "admin";
    $adminPass = "admin";


    $inputUser = trim($_POST['username']);
    $inputPass = trim($_POST['password']);


    if ($inputUser === $adminUser && $inputPass === $adminPass) {

        $_SESSION['admin_logged_in'] = true;


        header("Location: orders_dashboard.php");
        exit();
    } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Admin Login</title>
</head>
<body>

<header>
    <a href=""><img src="img/restaurant.png" alt="logo"></a>
</header>

<div class="login_form">
    <h2>Admin Login</h2>
    <h3>Please enter your credentials to access the admin dashboard</h3>
    
    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Login">
    </form>
    <h3>Client Login!  <a href="login.php"> Log In</a></h3>
</div>

</body>
</html>
