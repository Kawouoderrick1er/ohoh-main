<?php // c:\xampp\htdocs\ohoh-main\navigation.php (MODIFIED)
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session to access user info if logged in
}

// Déterminer la page actuelle pour le lien actif
$currentPage = basename($_SERVER['PHP_SELF']);

// Récupérer le chemin de l'image de profil depuis la session (sera défini lors de la connexion)
$profileImagePath = $_SESSION['profile_image_path'] ?? 'Images/profile/default-avatar.png'; // Chemin vers une image par défaut
// Assurer que le chemin est valide ou utiliser le défaut
if (empty($profileImagePath) || !file_exists($profileImagePath)) {
     $profileImagePath = 'Images/profile/default-avatar.png'; // Assurez-vous que ce fichier existe
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Le titre sera défini dans chaque page spécifique -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Lien vers le CSS global -->
    <!-- Styles pour l'image de profil dans la navbar -->
    <style>
        .profile-picture-nav {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
            border: 1px solid #dee2e6; /* Légère bordure */
        }
        .navbar-custom .dropdown-toggle::after {
             display: none; /* Masquer la flèche par défaut du dropdown */
        }
        .navbar-custom .dropdown-menu {
            margin-top: 0.5rem !important; /* Ajuster la position du menu */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="navbar.php"> <!-- Lien vers l'accueil -->
                <img src="Images/digi.jpg" alt="D-X-T Logo"> D-X-T
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'navbar.php' || $currentPage == 'index.php') ? 'active' : ''; ?>" href="navbar.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'A_propos.php') ? 'active' : ''; ?>" href="A_propos.php">À Propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'formation.php') ? 'active' : ''; ?>" href="formation.php">Formations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'blog.php') ? 'active' : ''; ?>" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center flex-wrap">
                    <div class="auth-buttons">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Utilisateur connecté -->
                            <div class="dropdown">
                                <!-- Bouton avec image de profil -->
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo htmlspecialchars($profileImagePath); ?>" alt="Profil" class="profile-picture-nav">
                                    <span class="d-none d-sm-inline mx-1 text-white"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser1">
                                    <li><a class="dropdown-item <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>" href="profile.php"><i class="fas fa-user-circle me-2"></i>Mon Profil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li> <!-- Lien exemple -->
                                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'administrateur'): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="admin_dashboard.php" target="_blank"><i class="fas fa-shield-alt me-2"></i>Dashboard Admin</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <!-- Utilisateur non connecté -->
                            <a href="inscription.php" class="btn btn-outline-secondary <?php echo ($currentPage == 'inscription.php') ? 'active' : ''; ?>">Inscription</a>
                            <a href="connexion.php" class="btn btn-primary <?php echo ($currentPage == 'connexion.php') ? 'active' : ''; ?>">Connexion</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main> <!-- La balise main s'ouvre ici et se ferme dans foote.php -->
    <!-- Le contenu de la page spécifique viendra ici -->
