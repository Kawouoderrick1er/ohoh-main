<?php // c:\xampp\htdocs\ohoh\navigation.php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    // session_start();
}

// Déterminer la page actuelle pour le lien actif
$currentPage = basename($_SERVER['PHP_SELF']);
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
    <!-- Pas de <style> ici, tout est dans style.css -->
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
                        <a class="nav-link <?php echo ($currentPage == 'formation.php') ? 'active' : ''; ?>" href="formation.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'blog.php') ? 'active' : ''; ?>" href="#">Blog</a> <!-- Lien Blog (à créer) -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center flex-wrap"> <!-- Flex wrap pour mobile -->
                    <!-- Barre de recherche optionnelle -->
                    <!--
                    <div class="search-bar me-lg-3">
                        <i class="fas fa-search"></i>
                        <input class="form-control" type="search" placeholder="Rechercher..." aria-label="Search">
                    </div>
                    -->
                    <div class="auth-buttons">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Utilisateur connecté -->
                            <a href="profile.php" class="btn btn-outline-secondary <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
                                <i class="fas fa-user me-1"></i> Profil (<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?>)
                            </a>
                            <a href="logout.php" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
                            </a>
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
