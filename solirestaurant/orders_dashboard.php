<?php
require 'connection.php';
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true ) {
    header("Location: admin_login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(idCmd) AS order_count FROM commande WHERE DATE(dateCmd) = CURDATE()");
$stmt->execute();
$orderCount = $stmt->fetch(PDO::FETCH_ASSOC)['order_count'];


$stmt = $pdo->prepare("
    SELECT p.nomPlat, p.image, SUM(cp.qte) AS total_quantity
    FROM commande_plat cp
    JOIN plat p ON cp.idPlat = p.idPlat
    JOIN commande c ON cp.idCmd = c.idCmd
    WHERE DATE(c.dateCmd) = CURDATE()
    GROUP BY p.nomPlat, p.image
");
$stmt->execute();
$dishesOrdered = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("SELECT COUNT(DISTINCT idCl) AS total_clients FROM commande");
$stmt->execute();
$totalClients = $stmt->fetch(PDO::FETCH_ASSOC)['total_clients'];


$stmt = $pdo->prepare("SELECT COUNT(idCmd) AS canceled_orders FROM commande WHERE Statut = 'annulée'");
$stmt->execute();
$canceledOrders = $stmt->fetch(PDO::FETCH_ASSOC)['canceled_orders'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idCmd'], $_POST['statut'])) {
    $idCmd = intval($_POST['idCmd']);
    $statut = $_POST['statut'];


    $stmt = $pdo->prepare("UPDATE commande SET Statut = ? WHERE idCmd = ?");
    $stmt->execute([$statut, $idCmd]);

    echo "<script>
            alert('Statut de la commande mis à jour avec succès !');
            window.location.href = '#';
          </script>";
}


$stmt = $pdo->prepare("SELECT idCmd, idCl, dateCmd, Statut FROM commande WHERE DATE(dateCmd) = CURDATE()");
$stmt->execute();
$ordersToday = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard.css">
    <title>Tableau de bord - Restaurant</title>
</head>
<body>
    <header>
        <img src="img/restaurant.png" alt="Resta">
    </header>

    <main>
        <h1>Tableau de bord du Restaurant</h1>


<div class="dashboard">
        <p id="para">Total de Commandes <span><?php echo $orderCount; ?></span></p>
        <p id="total_client">Total de clients <span><?php echo $totalClients; ?></span></p>
        <p id="annule">Commandes annulées <span><?php echo $canceledOrders; ?></span></p>
        </div>



        <h2>Commandes passées aujourd'hui</h2>
        <table >
            <thead>
                <tr>
                    <th>ID Commande</th>
                    <th>ID Client</th>
                    <th>Date de Commande</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($ordersToday as $order) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($order['idCmd']) . "</td>";
                    echo "<td>" . htmlspecialchars($order['idCl']) . "</td>";
                    echo "<td>" . htmlspecialchars($order['dateCmd']) . "</td>";
                    echo "<td>" . htmlspecialchars($order['Statut']) . "</td>";
                    echo "<td>
                            <form method='POST' action='#'>
                                <input type='hidden' name='idCmd' value='" . $order['idCmd'] . "'>
                                <select name='statut'>
                                    <option value='en attente' " . ($order['Statut'] == 'en attente' ? 'selected' : '') . ">En attente</option>
                                    <option value='en cours' " . ($order['Statut'] == 'en cours' ? 'selected' : '') . ">En cours</option>
                                    <option value='expédiée' " . ($order['Statut'] == 'expédiée' ? 'selected' : '') . ">Expédiée</option>
                                    <option value='livrée' " . ($order['Statut'] == 'livrée' ? 'selected' : '') . ">Livrée</option>
                                    <option value='annulée' " . ($order['Statut'] == 'annulée' ? 'selected' : '') . ">Annulée</option>
                                </select>
                                <button type='submit'>Mettre à jour</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        
        <h3>Liste des plats commandés aujourd'hui :</h3>
        <div class="card-container">
            <?php
            foreach ($dishesOrdered as $dish) {
                echo "
                <div class='card'>
                    <img src='" . htmlspecialchars($dish['image']) . "' alt='" . htmlspecialchars($dish['nomPlat']) . "'>
                    <h3>" . htmlspecialchars($dish['nomPlat']) . "</h3>
                    <p>Quantité Commandée : " . htmlspecialchars($dish['total_quantity']) . "</p>
                </div>";
            }
            ?>
        </div>

    
    </main>
</body>
</html>
