<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$idCl = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT idCmd FROM commande WHERE idCl = ? AND Statut = 'en attente'");
$stmt->execute([$idCl]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    $idCmd = $order['idCmd'];


    $stmt = $pdo->prepare("UPDATE commande SET Statut = 'en cours' WHERE idCmd = ?");
    $stmt->execute([$idCmd]);


    echo "<script>
            alert('Commande confirmée avec succès !');
            window.location.href = 'index.php';
          </script>";
} else {
    echo "alert('Erreur : aucune commande en attente.');";
}
?>
