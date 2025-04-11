<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['valider'])) {
    $formation = $_POST['formation'];
    $moyen_paiement = $_POST['moyen_paiement'];
    $telephone = $_POST['telephone'];
    $montant = $_POST['montant'];

    // Connexion à la base de données
    try {
        $conn = new PDO("mysql:host=localhost;dbname=formation_professionnelle", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insertion des données dans la table inscriptions
        $sql = "INSERT INTO inscriptions (utilisateur_id, formation, moyen_paiement, telephone, montant) VALUES (:utilisateur_id, :formation, :moyen_paiement, :telephone, :montant)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':utilisateur_id', $_SESSION['user_id']);
        $stmt->bindParam(':formation', $formation);
        $stmt->bindParam(':moyen_paiement', $moyen_paiement);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':montant', $montant);

        if ($stmt->execute()) {
            $message = "Votre inscription à la formation $formation a été validée. Vous serez contacté au numéro $telephone.";
        } else {
            $message = "Erreur lors de l'inscription.";
        }
    } catch (PDOException $e) {
        $message = "Erreur de connexion à la base de données: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Formations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .formation-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        .formation-container h2 {
            margin-bottom: 20px;
        }
        .formation-container select, .formation-container input, .formation-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .formation-container button {
            background-color: #007bff;
            color: white;
            border: none;
            transition: background-color 0.3s ease-in-out;
        }
        .formation-container button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 20px;
            color: green;
        }
    </style>
    <script>
        function showPaymentFields() {
            var moyenPaiement = document.getElementById('moyen_paiement').value;
            var paymentFields = document.getElementById('payment_fields');
            paymentFields.style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="formation-container">
        <h2>Inscription à une Formation</h2>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="formations.php" method="post">
            <select name="formation" required>
                <option value="">Sélectionner une formation</option>
                <option value="Formation 1">Formation 1</option>
                <option value="Formation 2">Formation 2</option>
                <option value="Formation 3">Formation 3</option>
            </select>
            <select name="moyen_paiement" id="moyen_paiement" onchange="showPaymentFields()" required>
                <option value="">Sélectionner un moyen de paiement</option>
                <option value="MTN">MTN</option>
                <option value="Orange">Orange</option>
            </select>
            <div id="payment_fields" style="display: none;">
                <input type="text" name="telephone" placeholder="Numéro de téléphone" required>
                <input type="number" name="montant" placeholder="Montant" required>
            </div>
            <button type="submit" name="valider">Valider</button>
        </form>
    </div>
</body>
</html>