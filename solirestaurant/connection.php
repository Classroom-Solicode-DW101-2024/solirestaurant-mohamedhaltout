<?php

$host = 'localhost';
$dbname = 'solirestaurant';
$username = 'root';
$password = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

} catch (PDOException $e) {
    echo("Echec dela connexion ". $e -> getMessage());
}


function getLastIdClient() {
    global $pdo;
        $sql = "SELECT MAX(idClient) AS maxId FROM client";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result= $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($result['maxId'])) {
            $MaxId = 0;
        } else {
            $MaxId = $result['maxId'];
        }
        return $MaxId;
    } 
    
    

    function tel_existe($tel){
        global $pdo;
        $sql = "SELECT * FROM client where telCl=:telCl";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':telCl', $telCl);
        $stmt->execute();
        $rusult = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rusult;
    }

?>