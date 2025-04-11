<?php
$servername = "localhost";
$username = "root"; // remplacez par votre nom d'utilisateur MySQL
$password = ""; // remplacez par votre mot de passe MySQL
$dbname = "formation_professionnelle";

try {
    // Créer une connexion
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configurer le mode d'erreur PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussie";
} catch(PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage();
}
?>