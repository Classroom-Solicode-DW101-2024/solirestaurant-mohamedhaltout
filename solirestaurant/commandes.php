<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$idCl = $_SESSION['user_id'];


$stmt = $pdo->prepare("
    SELECT cp.idPlat, p.nomPlat, p.prix, cp.qte, (p.prix * cp.qte) AS total_price, p.image 
    FROM commande_plat cp
    JOIN plat p ON cp.idPlat = p.idPlat
    JOIN commande c ON cp.idCmd = c.idCmd
    WHERE c.idCl = ? AND c.Statut = 'en attente'
");
$stmt->execute([$idCl]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);


$totalPrice = array_sum(array_column($cartItems, 'total_price'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="panier.css">
</head>
<body>

<h1>Votre Panier</h1>

<table>
    <thead>
        <tr>
            <th>Plat</th>
            <th>Image</th>
            <th>Prix (MAD)</th>
            <th>Quantité</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cartItems as $item): ?>
        <tr id="item_<?= $item['idPlat'] ?>">
            <td><?= htmlspecialchars($item['nomPlat']) ?></td>
            <td><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['nomPlat']) ?>" width="100"></td>
            <td class="price"><?= htmlspecialchars($item['prix']) ?> MAD</td>
            <td class="quantity">
                <button class="decrease" data-id="<?= $item['idPlat'] ?>">-</button>
                <span id="qty_<?= $item['idPlat'] ?>"><?= $item['qte'] ?></span>
                <button class="increase" data-id="<?= $item['idPlat'] ?>">+</button>
            </td>
            <td class="total_price" id="total_<?= $item['idPlat'] ?>"><?= htmlspecialchars($item['total_price']) ?> MAD</td>
            <td>
                <form method="POST" action="remove_from_cart.php">
                    <input type="hidden" name="idPlat" value="<?= $item['idPlat'] ?>">
                    <button type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3>Total: <span id="totalPrice"><?= $totalPrice ?></span> MAD</h3>


<form method="POST" action="confirm_order.php">
    <button type="submit">Confirmer la commande</button>
</form>

<script>
document.querySelectorAll('.increase').forEach(button => {
    button.addEventListener('click', function() {
        let itemId = this.getAttribute('data-id');
        let qtyElement = document.getElementById('qty_' + itemId);
        let totalPriceElement = document.getElementById('total_' + itemId);
        let currentQty = parseInt(qtyElement.innerText);
        let price = parseFloat(document.querySelector(`#item_${itemId} .price`).innerText);


        currentQty++;
        qtyElement.innerText = currentQty;
        totalPriceElement.innerText = (currentQty * price).toFixed(2) + " MAD";

        updateTotalPrice();
    });
});

document.querySelectorAll('.decrease').forEach(button => {
    button.addEventListener('click', function() {
        let itemId = this.getAttribute('data-id');
        let qtyElement = document.getElementById('qty_' + itemId);
        let totalPriceElement = document.getElementById('total_' + itemId);
        let currentQty = parseInt(qtyElement.innerText);
        let price = parseFloat(document.querySelector(`#item_${itemId} .price`).innerText);

        if (currentQty > 1) {

            currentQty--;
            qtyElement.innerText = currentQty;
            totalPriceElement.innerText = (currentQty * price).toFixed(2) + " MAD";
        }

        updateTotalPrice();
    });
});

function updateTotalPrice() {
    let total = 0;
    document.querySelectorAll('.total_price').forEach(totalPriceElement => {
        total += parseFloat(totalPriceElement.innerText || 0);
    });
    document.getElementById("totalPrice").innerText = total.toFixed(2);
}
</script>

</body>
</html>
