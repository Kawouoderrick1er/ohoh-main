<?php // c:\xampp\htdocs\ohoh\connexion.php
session_start(); // Démarrer la session pour stocker les infos utilisateur

// Si l'utilisateur est déjà connecté, rediriger vers le profil ou l'accueil
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php"); // Ou navbar.php
    exit();
}

// Inclure la configuration de la base de données ou la connexion directe
require_once 'base.php'; // Assurez-vous que ce fichier contient la connexion PDO $conn

$message = '';
$message_type = 'danger'; // Par défaut 'danger' pour les erreurs

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "Veuillez saisir votre email et votre mot de passe.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide.";
    } else {
        try {
            // Rechercher l'utilisateur par email
            $sql = "SELECT id, nom, email, mot_de_passe, type_utilisateur FROM utilisateurs WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérifier si l'utilisateur existe et si le mot de passe est correct
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Mot de passe correct : Démarrer la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_type'] = $user['type_utilisateur']; // 'etudiant', 'formateur', 'administrateur'

                // Rediriger en fonction du type d'utilisateur
                if ($user['type_utilisateur'] == 'administrateur') {
                    // Stocker l'ID admin spécifiquement si nécessaire pour le dashboard
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_name'] = $user['nom'];
                    header("Location: admin_dashboard.php");
                } else {
                    // Pour étudiants et formateurs, rediriger vers le profil
                    header("Location: profile.php");
                }
                exit(); // Important après une redirection

            } else {
                // Utilisateur non trouvé ou mot de passe incorrect
                $message = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            // En production, logguer l'erreur détaillée
            error_log("Erreur de connexion: " . $e->getMessage());
            $message = "Erreur technique lors de la connexion. Veuillez réessayer plus tard.";
        }
    }
}

include 'navigation.php'; // Inclure la barre de navigation
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - D-X-T</title>
     <!-- <style>
        .form-container {
            max-width: 500px;
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
        <h2>Connexion</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="connexion.php" method="post" novalidate>
            <div class="mb-3 form-group-animate">
                <label for="email" class="form-label">Adresse Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="mb-3 form-group-animate">
                <label for="mot_de_passe" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
            </div>
             <div class="mb-3 form-check form-group-animate">
                <input type="checkbox" class="form-check-input" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
            </div>
            <div class="d-grid gap-2 form-group-animate">
                <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
            </div>
             <p class="text-center mt-3 form-group-animate">
                <a href="#">Mot de passe oublié ?</a>
            </p>
            <p class="text-center mt-2 form-group-animate">
                Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a>
            </p>
        </form>
    </div>
</main>

<?php include 'foote.php'; // Inclure le pied de page ?>
