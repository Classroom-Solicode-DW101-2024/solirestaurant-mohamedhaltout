<?php

require 'connection.php'; 

session_start(); 

if (isset($_SESSION['isLogging']) === true) {
    echo '<div style="padding: 90px 50px 50px 20px; display: inline-block; width: 600px;
    height: 10px;  text-align: center; font-size: 20px;
    font-weight: 600;">Hello ' . ($_SESSION['nomCl']) . '</div>';

} else {
    header("Location: login.php");
    exit();
}


$sql = "SELECT * FROM plat WHERE 1"; 


$typeCuisine = isset($_POST['type_cuisine']) ? trim($_POST['type_cuisine']) : "";
$category = isset($_POST['category']) ? trim($_POST['category']) : "";


$params = [];
if (!empty($typeCuisine)) {
    $sql .= " AND TypeCuisine = ?";
    $params[] = $typeCuisine;
}
if (!empty($category)) {
    $sql .= " AND categoriePlat = ?";
    $params[] = $category;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Restaurant Data</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>


<header>
        <a href=""><img src="img/restaurant.png" alt="logo"></a>
        <a href="commandes.php"><img src="img/basket.png" alt="logo"></a>

    </header>

    <div class="main_section">
    <main>
        <h1>Searching for dishes<br><span> made easy.</span></h1>
        <p>Find the perfect effortlessly with tailored results at your fingertips!</p>
    </main>


    <form method="POST" action="index.php"> 
       <div class="select-menu">
        <h2>Filter by Type and Category</h2>

        <select id="type_cuisine" name="type_cuisine">
            <option value="">All Types</option> 
            <option value="Moroccan">Moroccan</option>
            <option value="Italian">Italian</option>
            <option value="Indian">Indian</option>
            <option value="Japanese">Japanese</option>
            <option value="American">American</option>
            <option value="Thai">Thai</option>
            <option value="Mexican">Mexican</option>
            <option value="Vietnamese">Vietnamese</option>
            <option value="French">French</option>
            <option value="British">British</option>
            <option value="Chinese">Chinese</option>
            <option value="Peruvian">Peruvian</option>
            <option value="Greek">Greek</option>
            <option value="Turkish">Turkish</option>
            <option value="Middle Eastern">Middle Eastern</option>
        </select>

        <select id="category" name="category">
            <option value="">All Categories</option>
            <option value="plat principal">Plat principal</option>
            <option value="entrée">Entrée</option>
            <option value="Dessert">Dessert</option>
        </select>  

        <button type="submit">Filter</button>

    </div>
</div>
</form>


<div class="card-container">
    <?php
    if (empty($dishes)) {
        echo "<p>No dishes found matching.</p>";
    } else {
        foreach ($dishes as $row) {
            echo "<div class='card'>
                <img src='".($row['image'])."' alt='Dish Image'>
                <h3>".($row['nomPlat'])."</h3>
                <p><strong>Type:</strong> ".($row['TypeCuisine'])."</p>
                <p class='price'>".($row['prix'])." MAD</p>
                <button class='order-btn' data-id='".($row['idPlat'])."'>Order Now</button>
            </div>";
        }
    }
    ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const orderButtons = document.querySelectorAll('.order-btn');

        orderButtons.forEach(button => {
            button.addEventListener('click', function() {
                let dishId = this.getAttribute('data-id');
                let dishCard = document.querySelector(`#dish-${dishId}`);
                let buttonClicked = this; 
        

                fetch('add_to_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'idPlat=' + dishId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {

                        

                        buttonClicked.style.backgroundColor = "#515151"; 
                        buttonClicked.style.color = "#fff"; 
                        buttonClicked.textContent = "Ordred";
                    } else {
                        console.error("Error:", data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
</script>

</body>
</html>

