<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$idCl = $_SESSION['user_id'];
$idPlat = isset($_POST['idPlat']) ? ($_POST['idPlat']) : 0;
$dateCmd = date("Y-m-d H:i:s");
$statut = 'en attente'; 

if ($idPlat > 0) {
    try {
        $pdo->beginTransaction();


        $stmt = $pdo->prepare("SELECT idCmd FROM commande WHERE idCl = ? AND Statut = 'en attente' LIMIT 1");
        $stmt->execute([$idCl]);
        $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingOrder) {
            $idCmd = $existingOrder['idCmd'];
        } else {

            $stmt = $pdo->query("SELECT MAX(idCmd) AS last_id FROM commande");
            $lastIdRow = $stmt->fetch(PDO::FETCH_ASSOC);
            $idCmd = $lastIdRow['last_id'] ? $lastIdRow['last_id'] + 1 : 1;

            $stmt = $pdo->prepare("INSERT INTO commande (idCmd, dateCmd, Statut, idCl) VALUES (?, ?, ?, ?)");
            $stmt->execute([$idCmd, $dateCmd, $statut, $idCl]);
        }

        if (!$idCmd) {
            echo json_encode(["status" => "error", "message" => "Failed to create or retrieve order."]);
            exit();
        }


        $stmt = $pdo->prepare("SELECT qte FROM commande_plat WHERE idCmd = ? AND idPlat = ?");
        $stmt->execute([$idCmd, $idPlat]);
        $existingDish = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingDish) {

            $newQuantity = $existingDish['qte'] + 1;
            $stmt = $pdo->prepare("UPDATE commande_plat SET qte = ? WHERE idCmd = ? AND idPlat = ?");
            $stmt->execute([$newQuantity, $idCmd, $idPlat]);
        } else {

            $stmt = $pdo->prepare("INSERT INTO commande_plat (idCmd, idPlat, qte) VALUES (?, ?, 1)");
            $stmt->execute([$idCmd, $idPlat]);
        }

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Dish added to order."]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
