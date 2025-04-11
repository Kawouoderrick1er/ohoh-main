<?php // c:\xampp\htdocs\ohoh\inscription.php
session_start(); // Démarrer la session pour potentiellement connecter l'utilisateur après inscription

// Inclure la configuration de la base de données ou la connexion directe
require_once 'base.php'; // Assurez-vous que ce fichier contient la connexion PDO $conn

$message = '';
$message_type = 'danger'; // Par défaut 'danger' pour les erreurs

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';
    $password_confirm = $_POST['mot_de_passe_confirm'] ?? '';

    // --- Validation Côté Serveur ---
    if (empty($nom) || empty($email) || empty($password) || empty($password_confirm)) {
        $message = "Veuillez remplir tous les champs obligatoires (*).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide.";
    } elseif (strlen($password) < 6) { // Exemple: longueur minimale du mot de passe
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $password_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            // Vérifier si l'email existe déjà
            $sql_check = "SELECT id FROM utilisateurs WHERE email = :email";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();

            if ($stmt_check->fetch()) {
                $message = "Cette adresse email est déjà utilisée.";
            } else {
                // Hasher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insérer l'utilisateur (type 'etudiant' par défaut)
                $sql_insert = "INSERT INTO utilisateurs (nom, email, mot_de_passe, telephone, adresse, type_utilisateur)
                               VALUES (:nom, :email, :mot_de_passe, :telephone, :adresse, 'etudiant')";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bindParam(':nom', $nom);
                $stmt_insert->bindParam(':email', $email);
                $stmt_insert->bindParam(':mot_de_passe', $hashed_password);
                $stmt_insert->bindParam(':telephone', $telephone);
                $stmt_insert->bindParam(':adresse', $adresse);

                if ($stmt_insert->execute()) {
                    $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                    $message_type = 'success';

                    // Optionnel: Connecter l'utilisateur automatiquement
                    // $_SESSION['user_id'] = $conn->lastInsertId();
                    // $_SESSION['user_name'] = $nom;
                    // $_SESSION['user_type'] = 'etudiant';
                    // header("Location: profile.php"); // Rediriger vers le profil
                    // exit();

                    // Vider les champs POST pour ne pas les réafficher (si pas de redirection)
                     $_POST = array(); // Efface les données POST après succès

                } else {
                    $message = "Une erreur s'est produite lors de l'inscription.";
                }
            }
        } catch (PDOException $e) {
            // En production, logguer l'erreur détaillée
            error_log("Erreur d'inscription: " . $e->getMessage());
            $message = "Erreur technique lors de l'inscription. Veuillez réessayer plus tard.";
        }
    }
}

include 'navigation.php'; // Inclure la barre de navigation
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - D-X-T</title>
    <!-- <style>
        .form-container {
            max-width: 600px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            color: #343a40;
            margin-bottom: 1.5rem;
        }
        .form-label .text-danger { font-size: 0.9em; margin-left: 2px; }

        /* Styles pour les animations JS */
        .fade-in { opacity: 0; }
        .animate-on-scroll { opacity: 0; transform: translateY(30px); transition: opacity 0.8s ease-out, transform 0.8s ease-out; }
        .animate-on-scroll.is-visible { opacity: 1; transform: translateY(0); }
        .form-group-animate { opacity: 0; transform: translateY(20px); }
    </style> -->
</head>

<main class="container mt-5 mb-5">
    <div class="form-container fade-in">
        <h2>Créer un compte</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="inscription.php" method="post" novalidate>
            <div class="mb-3 form-group-animate">
                <label for="nom" class="form-label">Nom complet <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
            </div>
            <div class="mb-3 form-group-animate">
                <label for="email" class="form-label">Adresse Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
             <div class="mb-3 form-group-animate">
                <label for="telephone" class="form-label">Téléphone (Optionnel)</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>">
            </div>
             <div class="mb-3 form-group-animate">
                <label for="adresse" class="form-label">Adresse (Optionnel)</label>
                <textarea class="form-control" id="adresse" name="adresse" rows="2"><?php echo htmlspecialchars($_POST['adresse'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3 form-group-animate">
                <label for="mot_de_passe" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required minlength="6">
                 <div class="form-text">Doit contenir au moins 6 caractères.</div>
            </div>
            <div class="mb-3 form-group-animate">
                <label for="mot_de_passe_confirm" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required>
            </div>
            <div class="d-grid gap-2 form-group-animate">
                <button type="submit" class="btn btn-primary btn-lg">S'inscrire</button>
            </div>
            <p class="text-center mt-3 form-group-animate">
                Déjà un compte ? <a href="connexion.php">Connectez-vous</a>
            </p>
        </form>
    </div>
</main>

<?php include 'foote.php'; // Inclure le pied de page ?>
