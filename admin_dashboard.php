<?php // c:\xampp\htdocs\ohoh-main\admin_dashboard.php
session_start(); // Très important pour la sécurité !

// --- Vérification de Sécurité Essentielle ---
// !! DÉCOMMENTEZ CECI EN PRODUCTION !!
/*
if (!isset($_SESSION['admin_id'])) {
    // Rediriger vers la page de connexion si non connecté
    header("Location: admin_login.php?error=auth_required");
    exit();
}
*/

$admin_name = $_SESSION['admin_name'] ?? 'Admin'; // Utiliser le nom de l'admin stocké en session
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur - D-X-T</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- VOTRE CSS PERSONNALISÉ -->
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
</head>

<body>
    <div class="sidebar">
        <div class="admin-info">
             <i class="fas fa-user-shield"></i>
            <h2><?php echo htmlspecialchars($admin_name); ?></h2>
        </div>
        <div class="menu">
            <!-- Lien Tableau de Bord (Aperçu) -->
            <a href="#" data-url="admin_dashboard_overview.php" onclick="loadContent(this, event)" class="active">
                <i class="fas fa-tachometer-alt fa-fw"></i>Tableau de Bord
            </a>
            <hr class="text-secondary opacity-50 my-2">

            <!-- Liens de Gestion Principaux -->
            <a href="#" data-url="gestion_generique.php?table=apprenants" onclick="loadContent(this, event)">
                <i class="fas fa-users fa-fw"></i>Apprenants
            </a>
             <a href="#" data-url="gestion_generique.php?table=formateurs" onclick="loadContent(this, event)">
                <i class="fas fa-chalkboard-teacher fa-fw"></i>Formateurs
            </a>
            <a href="#" data-url="gestion_generique.php?table=cours" onclick="loadContent(this, event)">
                <i class="fas fa-book-open fa-fw"></i>Cours
            </a>
            <a href="#" data-url="gestion_generique.php?table=lecons" onclick="loadContent(this, event)">
                <i class="fas fa-tasks fa-fw"></i>Leçons
            </a>
             <a href="#" data-url="gestion_generique.php?table=inscriptions" onclick="loadContent(this, event)">
                <i class="fas fa-user-check fa-fw"></i>Inscriptions
            </a>

             <hr class="text-secondary opacity-50 my-2">

             <!-- Autres Outils -->
             <a href="#" data-url="gestion_generique.php?table=messages_contact" onclick="loadContent(this, event)">
                <i class="fas fa-envelope-open-text fa-fw"></i>Messages Contact
            </a>
            <a href="#" data-url="gestion_emails.php" onclick="loadContent(this, event)">
                <i class="fas fa-paper-plane fa-fw"></i>Envoyer Email
            </a>

             <hr class="text-secondary opacity-50 my-2">

             <!-- Administration Système -->
            <a href="#" data-url="gestion_generique.php?table=administrateurs" onclick="loadContent(this, event)">
                <i class="fas fa-user-cog fa-fw"></i>Administrateurs
            </a>
        </div>
         <div class="logout-link">
            <a href="admin_logout.php">
                <i class="fas fa-sign-out-alt"></i>Déconnexion
            </a>
        </div>
    </div>

    <div class="content">
        <div id="content-area" class="content-area">
            <!-- Contenu chargé dynamiquement ici -->
            <div class="loading-indicator"><i class="fas fa-spinner"></i> Chargement initial...</div>
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/admin_dashboard.js" defer></script>

</body>
</html>
