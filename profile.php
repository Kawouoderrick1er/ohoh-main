<?php // c:\xampp\htdocs\ohoh-main\profile.php (REVISED)
session_start();

// --- Sécurité : Vérifier si l'utilisateur est connecté ---
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php?error=login_required");
    exit();
}

require_once 'base.php'; // Connexion $conn

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = 'info'; // Default
$user = null; // Pour stocker les données utilisateur

// --- Récupérer les informations actuelles de l'utilisateur ---
try {
    $sql_fetch = "SELECT id, nom, email, telephone, adresse, profile_image_path, type_utilisateur, date_inscription
                  FROM utilisateurs WHERE id = :id";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt_fetch->execute();
    $user = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Si l'utilisateur n'est pas trouvé (peu probable si ID en session est valide)
        session_unset();
        session_destroy();
        header("Location: connexion.php?error=user_not_found");
        exit();
    }
} catch (PDOException $e) {
    error_log("Erreur fetch profile: " . $e->getMessage());
    $message = "Erreur lors du chargement de votre profil.";
    $message_type = 'danger';
}

// --- Traitement de la mise à jour du profil (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    // Récupérer et nettoyer les données du formulaire
    $nom = trim(filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
    $telephone = trim(filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_SPECIAL_CHARS));
    $adresse = trim(filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_SPECIAL_CHARS));
    // Mot de passe (optionnel)
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation simple
    if (empty($nom)) {
        $message = "Le nom ne peut pas être vide.";
        $message_type = 'danger';
    } elseif (empty($email)) {
        $message = "L'adresse email n'est pas valide.";
        $message_type = 'danger';
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $message = "Les nouveaux mots de passe ne correspondent pas.";
        $message_type = 'danger';
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $message = "Le nouveau mot de passe doit faire au moins 6 caractères.";
        $message_type = 'danger';
    } else {
        // Préparer la mise à jour SQL
        $sql_update_parts = [];
        $bind_params = [];

        // Champs texte
        if ($nom !== $user['nom']) {
            $sql_update_parts[] = "nom = :nom";
            $bind_params[':nom'] = $nom;
        }
        if ($email !== $user['email']) {
            // Vérifier si le nouvel email n'est pas déjà pris par un AUTRE utilisateur
            $sql_check_email = "SELECT id FROM utilisateurs WHERE email = :email AND id != :id";
            $stmt_check_email = $conn->prepare($sql_check_email);
            $stmt_check_email->execute([':email' => $email, ':id' => $user_id]);
            if ($stmt_check_email->fetch()) {
                 $message = "Cette adresse email est déjà utilisée par un autre compte.";
                 $message_type = 'danger';
                 goto end_update_process; // Sortir si email déjà pris
            }
            $sql_update_parts[] = "email = :email";
            $bind_params[':email'] = $email;
        }
        if ($telephone !== $user['telephone']) {
            $sql_update_parts[] = "telephone = :telephone";
            $bind_params[':telephone'] = $telephone ?: null; // Stocker NULL si vide
        }
        if ($adresse !== $user['adresse']) {
            $sql_update_parts[] = "adresse = :adresse";
            $bind_params[':adresse'] = $adresse ?: null; // Stocker NULL si vide
        }

        // Nouveau mot de passe
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update_parts[] = "mot_de_passe = :password";
            $bind_params[':password'] = $hashed_password;
        }

        // --- Gestion de l'Upload de l'Image de Profil ---
        $new_image_path = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_image'];
            $upload_dir = 'uploads/profile_pics/'; // Assurez-vous que ce dossier existe et est accessible en écriture
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2 MB

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0775, true); // Créer le dossier si besoin
            }


            if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $unique_filename = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
                $destination = $upload_dir . $unique_filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $new_image_path = $destination;
                    // Supprimer l'ancienne image si elle existe et n'est pas l'image par défaut
                    $old_image_path = $user['profile_image_path'];
                    if (!empty($old_image_path) && $old_image_path !== 'Images/profile/default-avatar.png' && file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                    // Ajouter à la requête SQL
                    $sql_update_parts[] = "profile_image_path = :image_path";
                    $bind_params[':image_path'] = $new_image_path;
                } else {
                    $message = "Erreur lors du déplacement du fichier image.";
                    $message_type = 'danger';
                }
            } else {
                if (!in_array($file['type'], $allowed_types)) {
                    $message = "Type de fichier image non autorisé (JPEG, PNG, GIF uniquement).";
                } else {
                    $message = "Le fichier image est trop volumineux (max 2MB).";
                }
                $message_type = 'danger';
            }
        } // Fin gestion upload

        // --- Exécuter la mise à jour si des changements sont détectés ---
        if (!empty($sql_update_parts) && $message_type !== 'danger') {
            try {
                $sql_update = "UPDATE utilisateurs SET " . implode(', ', $sql_update_parts) . " WHERE id = :id";
                $bind_params[':id'] = $user_id;
                $stmt_update = $conn->prepare($sql_update);

                if ($stmt_update->execute($bind_params)) {
                    $message = "Profil mis à jour avec succès !";
                    $message_type = 'success';

                    // Mettre à jour les informations en session immédiatement
                    if (isset($bind_params[':nom'])) $_SESSION['user_name'] = $nom;
                    if (isset($bind_params[':email'])) $_SESSION['user_email'] = $email;
                    if (isset($bind_params[':image_path'])) $_SESSION['profile_image_path'] = $new_image_path;

                    // Re-fetch user data to display updated info
                    $stmt_fetch->execute(); // Re-exécute la requête initiale
                    $user = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

                } else {
                    $message = "Erreur lors de la mise à jour du profil.";
                    $message_type = 'danger';
                }
            } catch (PDOException $e) {
                error_log("Erreur update profile: " . $e->getMessage());
                $message = "Erreur base de données lors de la mise à jour.";
                $message_type = 'danger';
                 if ($e->getCode() == 23000) { // Probablement email dupliqué
                     $message = "Erreur : L'adresse email est peut-être déjà utilisée.";
                 }
            }
        } elseif ($message_type !== 'danger') {
            $message = "Aucune modification détectée.";
            $message_type = 'info';
        }
    }
    end_update_process: // Label pour goto en cas d'erreur email
}

// --- Récupération des formations suivies (si applicable) ---
$formations_suivies = [];
if ($user && $user['type_utilisateur'] == 'etudiant') {
    try {
        $sql_formations = "SELECT c.titre
                           FROM inscriptions i
                           JOIN cours c ON i.cours_id = c.id
                           WHERE i.utilisateur_id = :user_id
                           ORDER BY i.date_inscription DESC";
        $stmt_formations = $conn->prepare($sql_formations);
        $stmt_formations->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_formations->execute();
        $formations_suivies = $stmt_formations->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur fetch formations profile: " . $e->getMessage());
        // Ne pas bloquer la page pour ça, juste ne pas afficher les formations
    }
}


// --- Inclure la navigation ---
include 'navigation.php';

// --- Définir le chemin de l'image à afficher ---
$displayImagePath = $user['profile_image_path'] ?? 'Images/profile/default-avatar.png';
if (empty($displayImagePath) || !file_exists($displayImagePath)) {
    $displayImagePath = 'Images/profile/default-avatar.png'; // Assurez-vous que ce fichier existe
}

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?php echo htmlspecialchars($user['nom'] ?? 'Utilisateur'); ?></title>
    <style>
        .profile-page-container { max-width: 900px; margin: 2rem auto; }
        .profile-picture-page {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 1rem auto;
            border: 4px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .profile-card { border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .profile-card .card-header { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; }
        .upload-btn-wrapper { position: relative; overflow: hidden; display: inline-block; cursor: pointer; }
        .upload-btn-wrapper input[type=file] { font-size: 100px; position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; }
        .img-preview-container { max-width: 150px; margin: 0 auto; } /* Pour la prévisualisation JS */
        #imagePreview { max-width: 100%; height: auto; border-radius: 50%; margin-top: 10px; display: none; }
    </style>
</head>

<main class="container profile-page-container mt-4 mb-5">
    <h1 class="text-center mb-4">Mon Profil</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($user): ?>
    <form action="profile.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="update_profile" value="1"> <!-- Indicateur d'action -->
        <div class="row g-4">
            <!-- Colonne Image de Profil -->
            <div class="col-md-4">
                <div class="card profile-card text-center">
                    <div class="card-header">
                        <h5 class="mb-0">Image de Profil</h5>
                    </div>
                    <div class="card-body">
                        <div class="img-preview-container">
                            <img src="<?php echo htmlspecialchars($displayImagePath); ?>" alt="Image de profil" class="profile-picture-page" id="currentProfilePic">
                            <img id="imagePreview" src="#" alt="Aperçu" class="profile-picture-page"/>
                        </div>
                        <div class="upload-btn-wrapper btn btn-outline-primary btn-sm mt-2">
                            <i class="fas fa-camera"></i> Changer l'image
                            <input type="file" name="profile_image" id="profile_image_input" accept="image/png, image/jpeg, image/gif">
                        </div>
                        <div class="form-text">Max 2MB (JPG, PNG, GIF)</div>
                    </div>
                </div>
            </div>

            <!-- Colonne Informations Personnelles -->
            <div class="col-md-8">
                <div class="card profile-card">
                     <div class="card-header">
                        <h5 class="mb-0">Informations Personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo htmlspecialchars($user['nom']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="adresse" name="adresse" rows="2"><?php echo htmlspecialchars($user['adresse'] ?? ''); ?></textarea>
                        </div>
                        <hr>
                         <h6 class="text-muted">Changer le mot de passe (optionnel)</h6>
                         <div class="row g-2">
                             <div class="col-md-6">
                                 <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                 <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                             </div>
                             <div class="col-md-6">
                                 <label for="confirm_password" class="form-label">Confirmer mot de passe</label>
                                 <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                             </div>
                         </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer les modifications</button>
                    </div>
                </div>

                 <!-- Section Informations Compte (Readonly) -->
                <div class="card profile-card mt-4">
                     <div class="card-header">
                        <h5 class="mb-0">Informations du Compte</h5>
                    </div>
                    <div class="card-body">
                         <p><strong>Type de compte :</strong> <?php echo ucfirst(htmlspecialchars($user['type_utilisateur'])); ?></p>
                         <p><strong>Membre depuis :</strong> <?php echo date("d F Y", strtotime($user['date_inscription'])); ?></p>
                    </div>
                </div>

                 <!-- Section Formations Suivies (si étudiant) -->
                 <?php if ($user['type_utilisateur'] == 'etudiant' && !empty($formations_suivies)): ?>
                 <div class="card profile-card mt-4">
                     <div class="card-header">
                        <h5 class="mb-0">Mes Formations</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($formations_suivies as $formation): ?>
                            <li class="list-group-item"><?php echo htmlspecialchars($formation['titre']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </form>
    <?php else: ?>
        <!-- Afficher un message si $user est null après l'erreur initiale -->
         <div class="alert alert-danger" role="alert">
            Impossible de charger les informations du profil.
        </div>
    <?php endif; ?>

</main>

<script>
    // Script pour prévisualiser l'image sélectionnée
    const imageInput = document.getElementById('profile_image_input');
    const imagePreview = document.getElementById('imagePreview');
    const currentProfilePic = document.getElementById('currentProfilePic');

    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block'; // Afficher l'aperçu
                currentProfilePic.style.display = 'none'; // Cacher l'image actuelle
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none'; // Cacher l'aperçu si aucun fichier
             currentProfilePic.style.display = 'block'; // Remontrer l'image actuelle
        }
    });
</script>

<?php include 'foote.php'; ?>
