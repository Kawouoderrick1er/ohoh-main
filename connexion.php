<?php // c:\xampp\htdocs\ohoh-main\connexion.php (MODIFIED)
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

require_once 'base.php';

$message = '';
$message_type = 'danger';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "Veuillez saisir votre email et votre mot de passe.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide.";
    } else {
        try {
            // Inclure profile_image_path dans la sélection
            $sql = "SELECT id, nom, email, mot_de_passe, type_utilisateur, profile_image_path
                    FROM utilisateurs WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Stocker les informations en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_type'] = $user['type_utilisateur'];
                $_SESSION['user_email'] = $user['email']; // Utile pour pré-remplir
                $_SESSION['profile_image_path'] = $user['profile_image_path']; // << AJOUTÉ

                if ($user['type_utilisateur'] == 'administrateur') {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_name'] = $user['nom'];
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: profile.php"); // Rediriger vers le profil
                }
                exit();

            } else {
                $message = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            $message = "Erreur technique lors de la connexion. Veuillez réessayer plus tard.";
        }
    }
}

include 'navigation.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - D-X-T</title>
</head>

<main class="container mt-5 mb-5">
    <div class="form-container fade-in" style="max-width: 500px; margin: 3rem auto; padding: 2rem; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
        <h2 class="text-center mb-4">Connexion</h2>

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

<?php include 'foote.php'; ?>
