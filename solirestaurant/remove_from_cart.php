<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

if (isset($_POST['idPlat'])) {
    $idCl = $_SESSION['user_id'];
    $idPlat = intval($_POST['idPlat']);


    $stmt = $pdo->prepare("SELECT idCmd FROM commande WHERE idCl = ? AND Statut = 'en attente'");
    $stmt->execute([$idCl]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $idCmd = $order['idCmd'];


        $stmt = $pdo->prepare("DELETE FROM commande_plat WHERE idCmd = ? AND idPlat = ?");
        $stmt->execute([$idCmd, $idPlat]);
    }
}

header("Location: commandes.php");
exit();
?>
