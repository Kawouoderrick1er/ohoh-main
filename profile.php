<?php
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: connexion.php");
//     exit();
// }
require_once 'base.php'; // Assurez-vous que le fichier de connexion à la base de données est inclus ici
$message = "";

try {
    $conn = new PDO("mysql:host=localhost;dbname=formation_professionnelle", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['save'])) {
            $username = htmlspecialchars(trim($_POST['username']));
            $email = htmlspecialchars(trim($_POST['email']));
            $phone = htmlspecialchars(trim($_POST['phone']));

            // Mise à jour des informations utilisateur
            $sql = "UPDATE utilisateurs SET nom = :nom, email = :email, telephone = :telephone WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nom', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $phone);
            $stmt->bindParam(':id', $_SESSION['user_id']);

            if ($stmt->execute()) {
                $message = "Informations sauvegardées.";
            } else {
                $message = "Erreur lors de la sauvegarde des informations.";
            }
        } elseif (isset($_POST['delete'])) {
            $field = htmlspecialchars(trim($_POST['field']));

            // Suppression de l'information utilisateur spécifique
            $sql = "UPDATE utilisateurs SET $field = NULL WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $_SESSION['user_id']);

            if ($stmt->execute()) {
                $message = ucfirst($field) . " supprimé.";
            } else {
                $message = "Erreur lors de la suppression de l'information.";
            }
        } elseif (isset($_POST['deleteAll'])) {
            // Suppression de toutes les informations utilisateur
            $sql = "UPDATE utilisateurs SET nom = NULL, email = NULL, telephone = NULL WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $_SESSION['user_id']);

            if ($stmt->execute()) {
                $message = "Toutes les informations supprimées.";
            } else {
                $message = "Erreur lors de la suppression des informations.";
            }
        }
    }

    // Récupération des informations utilisateur
    $sql = "SELECT * FROM utilisateurs WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupération des formations suivies par l'utilisateur
    $sql = "SELECT nom_formation FROM inscriptions WHERE utilisateur_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "Erreur de connexion à la base de données: " . $e->getMessage();
}

// Initialiser $formations comme un tableau vide s'il n'existe pas
if (!isset($formations)) {
    $formations = [];
}

// Initialiser $user comme un tableau vide s'il n'existe pas
if (!$user) {
    $user = ['nom' => '', 'email' => '', 'telephone' => ''];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .profile-container {
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        .profile-info, .profile-image {
            margin: 10px 0;
        }
        .profile-info h2, .profile-image h2 {
            margin-top: 0;
        }
        .profile-info input, .profile-info button {
            display: block;
            margin: 10px 0;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .profile-info .input-container {
            display: flex;
            align-items: center;
        }
        .profile-info .input-container input {
            flex: 1;
        }
        .profile-info .input-container i {
            margin-left: 10px;
            cursor: pointer;
        }
        .profile-image img {
            width: 100%;
            max-width: 200px;
            height: auto;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
        }
        .profile-image input[type="file"] {
            display: none;
        }
        .profile-image label {
            display: block;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .profile-info button {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-info button i {
            margin-right: 5px;
        }
        .message {
            margin-bottom: 20px;
            color: green;
        }
        .formation-list {
            margin-top: 20px;
        }
        .formation-list h3 {
            margin-bottom: 10px;
        }
        .formation-list ul {
            list-style-type: none;
            padding: 0;
        }
        .formation-list ul li {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-image">
            <h2>Image de Profil</h2>
            <img src="default-profile.png" id="profilePic" alt="Profile Picture">
            <input type="file" id="fileInput" accept="image/*" onchange="loadFile(event)">
            <label for="fileInput"><i class="fas fa-camera"></i> Changer Image</label>
        </div>

        <div class="profile-info">
            <h2>Informations Utilisateur</h2>
            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <form action="profile.php" method="post">
                <div class="input-container">
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['nom']); ?>" placeholder="Nom d'utilisateur">
                    <i class="fas fa-edit" onclick="editInfo('username')"></i>
                    <i class="fas fa-trash-alt" onclick="deleteInfo('username')"></i>
                </div>
                <div class="input-container">
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email">
                    <i class="fas fa-edit" onclick="editInfo('email')"></i>
                    <i class="fas fa-trash-alt" onclick="deleteInfo('email')"></i>
                </div>
                <div class="input-container">
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['telephone']); ?>" placeholder="Téléphone">
                    <i class="fas fa-edit" onclick="editInfo('phone')"></i>
                    <i class="fas fa-trash-alt" onclick="deleteInfo('phone')"></i>
                </div>
                <button type="submit" name="save"><i class="fas fa-save"></i> Enregistrer</button>
                <button type="submit" name="deleteAll"><i class="fas fa-trash-alt"></i> Supprimer</button>
            </form>
        </div>

        <div class="formation-list">
            <h3>Formations Suivies</h3>
            <ul>
                <?php foreach ($formations as $formation): ?>
                    <li><?php echo htmlspecialchars($formation['nom_formation']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        function deleteInfo(field) {
            document.querySelector(`input[name="${field}"]`).value = '';
        }

        function editInfo(field) {
            document.querySelector(`input[name="${field}"]`).focus();
        }

        var loadFile = function(event) {
            var image = document.getElementById('profilePic');
            image.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>
</body>
</html>