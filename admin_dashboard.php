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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styles généraux (existants) */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: #343a40; padding: 20px; color: white; display: flex; flex-direction: column; position: fixed; height: 100%; top: 0; left: 0; box-shadow: 2px 0 5px rgba(0,0,0,0.1); overflow-y: auto; z-index: 1030; }
        .sidebar .admin-info { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #495057; }
        .sidebar .admin-info i { font-size: 2.5rem; margin-bottom: 10px; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 0; }
        .sidebar .menu { flex-grow: 1; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 12px 15px; margin-bottom: 8px; border-radius: 5px; transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out; }
        .sidebar a i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: white; }
        .sidebar .logout-link { margin-top: auto; padding-top: 15px; border-top: 1px solid #495057; }
        .sidebar .logout-link a { background-color: #dc3545; color: white; text-align: center; }
        .sidebar .logout-link a:hover { background-color: #c82333; }
        .content { margin-left: 250px; padding: 30px; flex-grow: 1; background-color: #f8f9fa; }
        .content-area { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); min-height: calc(100vh - 60px); }
        .loading-indicator { text-align: center; padding: 50px; font-size: 1.2rem; color: #6c757d; }
        .loading-indicator i { margin-right: 10px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }

        /* Styles pour les icônes d'action dans les tables chargées */
        .action-icons i { cursor: pointer; margin: 0 0.5rem; font-size: 1.1rem; transition: opacity 0.2s ease-in-out; }
        .action-icons i.fa-edit { color: #0d6efd; } /* Bleu */
        .action-icons i.fa-trash-alt { color: #dc3545; } /* Rouge */
        .action-icons i:hover { opacity: 0.7; }

        /* --- Styles de Personnalisation pour la Section Apprenants --- */
        .gestion-apprenants .table thead {
            background-color: #cfe2ff; /* Bleu clair pour l'en-tête */
            color: #052c65;
        }
        .gestion-apprenants .btn-primary {
             background-color: #0d6efd; /* Bleu standard pour le bouton Ajouter */
             border-color: #0d6efd;
        }
        .gestion-apprenants .card-body h4 {
            color: #0a58ca; /* Titre du formulaire d'ajout en bleu foncé */
        }
        .gestion-apprenants .intro-text {
            font-style: italic;
            color: #6c757d;
            margin-bottom: 1rem;
            border-left: 3px solid #0d6efd;
            padding-left: 10px;
        }
        /* Ajoutez d'autres styles spécifiques si nécessaire */
        .gestion-section .alert { margin-top: 1rem; } /* Marge pour les messages d'alerte */

    </style>
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
                <i class="fas fa-tachometer-alt"></i>Tableau de Bord
            </a>
            <hr class="text-secondary opacity-50 my-2">

            <!-- Liens de Gestion Principaux -->
            <a href="#" data-url="gestion_generique.php?table=apprenants" onclick="loadContent(this, event)">
                <i class="fas fa-users"></i>Apprenants
            </a>
             <a href="#" data-url="gestion_generique.php?table=formateurs" onclick="loadContent(this, event)">
                <i class="fas fa-chalkboard-teacher"></i>Formateurs
            </a>
            <a href="#" data-url="gestion_generique.php?table=cours" onclick="loadContent(this, event)">
                <i class="fas fa-book-open"></i>Cours
            </a>
            <a href="#" data-url="gestion_generique.php?table=lecons" onclick="loadContent(this, event)">
                <i class="fas fa-file-alt"></i>Leçons
            </a>
             <a href="#" data-url="gestion_generique.php?table=inscriptions" onclick="loadContent(this, event)">
                <i class="fas fa-user-check"></i>Inscriptions
            </a>

             <hr class="text-secondary opacity-50 my-2">

             <!-- Autres Outils -->
             <a href="#" data-url="gestion_generique.php?table=messages_contact" onclick="loadContent(this, event)">
                <i class="fas fa-envelope-open-text"></i>Messages Contact
            </a>
            <a href="#" data-url="gestion_emails.php" onclick="loadContent(this, event)">
                <i class="fas fa-paper-plane"></i>Envoyer Email
            </a>

             <hr class="text-secondary opacity-50 my-2">

             <!-- Administration Système -->
            <a href="#" data-url="gestion_generique.php?table=administrateurs" onclick="loadContent(this, event)">
                <i class="fas fa-user-cog"></i>Administrateurs
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
            <div class="loading-indicator"><i class="fas fa-spinner"></i> Chargement initial...</div>
        </div>
    </div>

    <!-- Scripts JS -->
    <!-- Bootstrap JS Bundle (dépendance) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js (dépendance, si utilisé) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- VOTRE SCRIPT PERSONNALISÉ (doit venir APRÈS les dépendances) -->
    <script src="assets/js/admin_dashboard.js" defer></script> {/* MODIFIÉ ICI */}

</body>
</html>
