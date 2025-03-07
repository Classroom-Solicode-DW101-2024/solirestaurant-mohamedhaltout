<?php

require "connection.php";

$erreurs=[];

$telCl = $nomCl = $last_name = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $telCl = trim($_POST['telCl']);
    $nomCl = trim($_POST['nomCl']);
    $prenomCl = trim($_POST['prenomCl']);
    
    $erreurs = [];


    $tel_is_exist = tel_existe($telCl);


    if (empty($telCl)) {
        $erreurs['telCl'] = "Remplir le téléphone";
    } elseif (!empty($tel_is_exist)) {
        $erreurs['telCl'] = "Le téléphone est déjà utilisé";
    }

    if (empty($nomCl)) {
        $erreurs['nomCl'] = "Remplir le nom";
    }

    if (empty($prenomCl)) {
        $erreurs['prenomCl'] = "Remplir le prénom";
    }


    if (!empty($erreurs)) {
        var_dump($erreurs);
    } else {
        try {
            $idClient = getLastIdClient() + 1;

            $stmt = $pdo->prepare("INSERT INTO client (idClient, telCl, nomCl, prenomCl) VALUES (?, ?, ?, ?)");

            if ($stmt->execute([$idClient, $telCl, $nomCl, $prenomCl])) {
                echo "Inscription réussie!";
                header("Location: index.php");
                exit(); 
                echo "Erreur: L'inscription a échoué.";
            }
        } catch (PDOException $e) {
            echo "Erreur SQL: " . $e->getMessage(); 
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Register</title>
</head>
<body>

<header>
        <a href=""><img src="img/restaurant.png" alt="logo"></a>
    </header>

<div class="register_form">
<h2>Register</h2>
    <h3>Lorem ipsum dolor sit amet, consectetur</h3>
    <form method="post" action="">
        <input type="number" name="telCl" placeholder="entre a phone number" ><br>
        <input type="text" name="nomCl" placeholder="Entre your first name" ><br>
        <input type="text" name="prenomCl" placeholder="Entre your last name" ><br>
        <input type="submit">
        <h3>Already have in account <a href="login.php">Log In</a></h3>
        <p class= "agree">Lorem ipsum dolor sit amet<br> consectetur 
    Lorem ipsum dolor sit amet, </p>
    </form>
</body>
</html>