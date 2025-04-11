<?php
// c:\xampp\htdocs\ohoh-main\admin_dashboard_overview.php
session_start();
// Optionnel: Vérification de sécurité (si accès direct doit être bloqué)
/*
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    die("Accès refusé.");
}
*/
require_once 'base.php'; // Pour accéder à la base de données si nécessaire

// --- Récupérer des statistiques (Exemples) ---
$stats = [
    'apprenants' => 0,
    'formateurs' => 0,
    'cours' => 0,
    'messages_nouveaux' => 0
];

try {
    // Compter les apprenants
    $stmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE type_utilisateur = 'etudiant'");
    $stats['apprenants'] = $stmt->fetchColumn() ?: 0; // Ajout de ?: 0 pour éviter null

    // Compter les formateurs
    $stmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE type_utilisateur = 'formateur'");
    $stats['formateurs'] = $stmt->fetchColumn() ?: 0;

    // Compter les cours (publiés ?)
    $stmt = $conn->query("SELECT COUNT(*) FROM cours WHERE statut = 'publié'"); // Ou tous les cours: COUNT(*) FROM cours
    $stats['cours'] = $stmt->fetchColumn() ?: 0;

    // Compter les messages de contact non lus
    $stmt = $conn->query("SELECT COUNT(*) FROM messages_contact WHERE statut = 'nouveau'");
    $stats['messages_nouveaux'] = $stmt->fetchColumn() ?: 0;

} catch (PDOException $e) {
    // Gérer l'erreur (log, message discret)
    error_log("Erreur DB Stats Overview: " . $e->getMessage());
    // Afficher un message d'erreur dans le tableau de bord si nécessaire
    echo '<div class="alert alert-warning">Erreur lors de la récupération des statistiques.</div>';
}

?>

<!-- Début du contenu pour #content-area -->
<h2 class="mb-4">Aperçu du Tableau de Bord</h2>

<div class="row g-4 mb-4">
    <!-- Carte Apprenants -->
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

    <!-- Carte Formateurs -->
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

    <!-- Carte Cours -->
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

    <!-- Carte Messages -->
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

<!-- Autres sections possibles -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-1"></i>
                Activité Récente (Exemple)
            </div>
            <div class="card-body">
                <p>Graphique ou liste des dernières inscriptions, derniers cours ajoutés, etc.</p>
                <!-- Placeholder pour un graphique -->
                 <canvas id="myAreaChart" width="100%" height="40"></canvas>
                 <p class="text-muted text-center mt-2">Intégration de graphiques avec Chart.js possible ici.</p>
            </div>
        </div>
    </div>
     <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="fas fa-tasks me-1"></i>
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

<!-- Fin du contenu pour #content-area -->

<!-- Optionnel: Inclure JS spécifique à l'overview si nécessaire (ex: pour Chart.js) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script>
    // Code pour initialiser Chart.js si utilisé
    // Exemple:
    document.addEventListener('DOMContentLoaded', () => { // S'assurer que le DOM est prêt
        const ctx = document.getElementById('myAreaChart');
        if(ctx) { // Vérifier si l'élément existe
            const myLineChart = new Chart(ctx, {
                type: 'line', // ou 'bar', etc.
                data: {
                    labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin"], // Exemple
                    datasets: [{
                        label: "Activité",
                        data: [10, 20, 15, 25, 22, 30], // Exemple
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {}
            });
        }
     });
</script> 
