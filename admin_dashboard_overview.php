<?php
// c:\xampp\htdocs\ohoh-main\admin_dashboard_overview.php (Styles cohérents)
session_start();
// Optionnel: Vérification de sécurité
/*
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    die("Accès refusé.");
}
*/
require_once 'base.php';

// --- Récupérer des statistiques ---
$stats = [
    'apprenants' => 0, 'formateurs' => 0, 'cours' => 0, 'messages_nouveaux' => 0
];
try {
    $stmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE type_utilisateur = 'etudiant'");
    $stats['apprenants'] = $stmt->fetchColumn() ?: 0;
    $stmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE type_utilisateur = 'formateur'");
    $stats['formateurs'] = $stmt->fetchColumn() ?: 0;
    $stmt = $conn->query("SELECT COUNT(*) FROM cours WHERE statut = 'publié'");
    $stats['cours'] = $stmt->fetchColumn() ?: 0;
    $stmt = $conn->query("SELECT COUNT(*) FROM messages_contact WHERE statut = 'nouveau'");
    $stats['messages_nouveaux'] = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    error_log("Erreur DB Stats Overview: " . $e->getMessage());
    echo '<div class="alert alert-warning" role="alert"><i class="fas fa-exclamation-triangle me-2"></i>Erreur lors de la récupération des statistiques.</div>';
}
?>

<!-- Début du contenu pour #content-area -->
<div class="gestion-section gestion-overview is-visible">

    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Aperçu du Tableau de Bord</h2> 

    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="card text-white bg-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Apprenants</h5>
                            <span class="display-6"><?php echo $stats['apprenants']; ?></span>
                        </div>
                        <i class="fas fa-users fa-3x opacity-75"></i>
                    </div>
                </div>
                <div class="card-footer bg-light bg-opacity-25 border-0">
                    <a href="#" data-url="gestion_generique.php?table=apprenants" onclick="loadContent(this, event)" class="text-white stretched-link text-decoration-none">Voir détails <i class="fas fa-arrow-circle-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card text-white bg-info shadow-sm h-100">
                 <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Formateurs</h5>
                            <span class="display-6"><?php echo $stats['formateurs']; ?></span>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-3x opacity-75"></i>
                    </div>
                </div>
                <div class="card-footer bg-light bg-opacity-25 border-0">
                     <a href="#" data-url="gestion_generique.php?table=formateurs" onclick="loadContent(this, event)" class="text-white stretched-link text-decoration-none">Voir détails <i class="fas fa-arrow-circle-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card text-white bg-success shadow-sm h-100">
                 <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Cours Publiés</h5>
                            <span class="display-6"><?php echo $stats['cours']; ?></span>
                        </div>
                        <i class="fas fa-book-open fa-3x opacity-75"></i>
                    </div>
                </div>
                <div class="card-footer bg-light bg-opacity-25 border-0">
                     <a href="#" data-url="gestion_generique.php?table=cours" onclick="loadContent(this, event)" class="text-white stretched-link text-decoration-none">Voir détails <i class="fas fa-arrow-circle-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card text-white bg-warning shadow-sm h-100">
                 <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Nouveaux Messages</h5>
                            <span class="display-6"><?php echo $stats['messages_nouveaux']; ?></span>
                        </div>
                        <i class="fas fa-envelope-open-text fa-3x opacity-75"></i>
                    </div>
                </div>
                 <div class="card-footer bg-light bg-opacity-25 border-0">
                     <a href="#" data-url="gestion_generique.php?table=messages_contact" onclick="loadContent(this, event)" class="text-white stretched-link text-decoration-none">Voir détails <i class="fas fa-arrow-circle-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <i class="fas fa-chart-line me-1"></i>
                    Activité Récente (Exemple)
                </div>
                <div class="card-body">
                    <p class="text-muted">Graphique représentant l'activité récente (ex: inscriptions).</p>
                    <div style="height: 250px;"> 
                         <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <i class="fas fa-bolt me-1"></i>
                    Raccourcis Rapides
                </div>
                <div class="list-group list-group-flush">
                     <a href="#" data-url="gestion_generique.php?table=cours&action=add" onclick="loadContent(this, event); setTimeout(toggleAddForm, 500);" class="list-group-item list-group-item-action"><i class="fas fa-plus-circle me-2 text-success"></i> Ajouter un nouveau cours</a>
                     <a href="#" data-url="gestion_generique.php?table=apprenants&action=add" onclick="loadContent(this, event); setTimeout(toggleAddForm, 500);" class="list-group-item list-group-item-action"><i class="fas fa-user-plus me-2 text-primary"></i> Ajouter un apprenant</a>
                     <a href="#" data-url="gestion_emails.php" onclick="loadContent(this, event)" class="list-group-item list-group-item-action"><i class="fas fa-paper-plane me-2 text-info"></i> Envoyer un email groupé</a>
                     <a href="#" data-url="gestion_generique.php?table=messages_contact" onclick="loadContent(this, event)" class="list-group-item list-group-item-action"><i class="fas fa-envelope me-2 text-warning"></i> Consulter les messages</a>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- Fin du contenu pour #content-area -->

<?php // Les balises <script> pour Chart.js ont été supprimées ici car gérées globalement ?>
