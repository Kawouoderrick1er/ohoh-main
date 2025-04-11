<?php // c:\xampp\htdocs\ohoh\details_formation.php
session_start();
require_once 'base.php';

$cours = null;
$lecons = [];
$error_message = '';
$cours_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$cours_id) {
    $error_message = "Identifiant de formation invalide.";
} else {
    try {
        // Récupérer les détails du cours (seulement s'il est publié)
        $sql_cours = "SELECT c.*, u.nom AS nom_formateur
                      FROM cours c
                      LEFT JOIN utilisateurs u ON c.formateur_id = u.id AND u.type_utilisateur = 'formateur'
                      WHERE c.id = :id AND c.statut = 'publié'";
        $stmt_cours = $conn->prepare($sql_cours);
        $stmt_cours->bindParam(':id', $cours_id, PDO::PARAM_INT);
        $stmt_cours->execute();
        $cours = $stmt_cours->fetch(PDO::FETCH_ASSOC);

        if (!$cours) {
            $error_message = "Formation non trouvée ou non publiée.";
        } else {
            // Récupérer les leçons publiées pour ce cours
            $sql_lecons = "SELECT id, titre, contenu -- Sélectionnez les colonnes nécessaires
                           FROM lecons
                           WHERE cours_id = :cours_id AND statut = 'publié'
                           ORDER BY date_creation ASC"; // Ou un autre ordre pertinent (ex: numero_ordre si vous ajoutez cette colonne)
            $stmt_lecons = $conn->prepare($sql_lecons);
            $stmt_lecons->bindParam(':cours_id', $cours_id, PDO::PARAM_INT);
            $stmt_lecons->execute();
            $lecons = $stmt_lecons->fetchAll(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        error_log("Erreur détails formation: " . $e->getMessage());
        $error_message = "Erreur lors du chargement des détails de la formation.";
    }
}

include 'navigation.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Titre dynamique -->
    <title><?php echo $cours ? htmlspecialchars($cours['titre']) : 'Détails Formation'; ?> - D-X-T</title>
    <style>
        .details-header {
            background-color: #e9ecef;
            padding: 2rem 1rem;
            margin-bottom: 2rem;
            border-radius: 8px;
        }
        .details-header h1 { margin-bottom: 0.5rem; }
        .details-header .lead { color: #6c757d; }
        .lecon-item {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 1rem;
            padding: 1rem 1.5rem;
            transition: box-shadow 0.3s ease;
        }
        .lecon-item:hover { box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .lecon-item h5 { color: #007bff; }
        .lecon-contenu { /* Style pour le contenu de la leçon si affiché directement */
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px dashed #eee;
            display: none; /* Caché par défaut, peut être affiché avec JS */
        }
        .btn-inscription { font-size: 1.1rem; padding: 0.6rem 1.5rem; }
    </style>
</head>

<main class="container mt-4 mb-5">

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <br><a href="formation.php" class="btn btn-link mt-2">Retour aux formations</a>
        </div>
    <?php elseif ($cours): ?>
        <!-- En-tête du cours -->
        <div class="details-header text-center fade-in">
            <h1><?php echo htmlspecialchars($cours['titre']); ?></h1>
            <?php if (!empty($cours['nom_formateur'])): ?>
                <p class="lead">Par <?php echo htmlspecialchars($cours['nom_formateur']); ?></p>
            <?php endif; ?>
            <p class="text-muted">Ajouté le <?php echo date("d F Y", strtotime($cours['date_creation'])); ?></p>
        </div>

        <div class="row g-4">
            <!-- Colonne Description & Inscription -->
            <div class="col-lg-8">
                <div class="card shadow-sm animate-on-scroll">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Description du Cours</h4>
                        <p><?php echo nl2br(htmlspecialchars($cours['description'])); ?></p>
                        <hr>
                        <!-- Bouton d'inscription (logique à implémenter) -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Vérifier si déjà inscrit -->
                            <?php
                                $is_inscrit = false;
                                try {
                                    $sql_check_insc = "SELECT 1 FROM inscriptions WHERE utilisateur_id = :user_id AND cours_id = :cours_id";
                                    $stmt_check_insc = $conn->prepare($sql_check_insc);
                                    $stmt_check_insc->bindParam(':user_id', $_SESSION['user_id']);
                                    $stmt_check_insc->bindParam(':cours_id', $cours_id);
                                    $stmt_check_insc->execute();
                                    if ($stmt_check_insc->fetch()) {
                                        $is_inscrit = true;
                                    }
                                } catch (PDOException $e) { /* Ignorer l'erreur ici */ }
                            ?>
                            <?php if ($is_inscrit): ?>
                                <button class="btn btn-success btn-lg disabled"><i class="fas fa-check-circle me-2"></i> Déjà Inscrit</button>
                            <?php else: ?>
                                <form action="process_inscription.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="cours_id" value="<?php echo $cours_id; ?>">
                                    <button type="submit" class="btn btn-primary btn-lg btn-inscription">
                                        <i class="fas fa-user-plus me-2"></i> S'inscrire à ce cours
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="connexion.php?redirect=details_formation.php?id=<?php echo $cours_id; ?>" class="btn btn-primary btn-lg btn-inscription">
                                <i class="fas fa-sign-in-alt me-2"></i> Connectez-vous pour vous inscrire
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Colonne Leçons -->
            <div class="col-lg-4">
                 <div class="card shadow-sm animate-on-scroll" style="transition-delay: 0.1s;">
                     <div class="card-header bg-light">
                         <h4 class="mb-0"><i class="fas fa-list-ul me-2"></i>Leçons du Cours</h4>
                     </div>
                    <div class="card-body">
                        <?php if (empty($lecons)): ?>
                            <p class="text-muted">Aucune leçon publiée pour ce cours pour le moment.</p>
                        <?php else: ?>
                            <?php foreach ($lecons as $index => $lecon): ?>
                                <div class="lecon-item animate-on-scroll" style="transition-delay: <?php echo $index * 0.1 + 0.2; ?>s;">
                                    <h5>
                                        <i class="fas fa-file-alt me-2 text-secondary"></i>
                                        <?php echo htmlspecialchars($lecon['titre']); ?>
                                        <!-- Optionnel: Bouton pour afficher/masquer contenu -->
                                        <!-- <button class="btn btn-sm btn-outline-secondary float-end" onclick="toggleLecon(<?php echo $lecon['id']; ?>)">Voir</button> -->
                                    </h5>
                                    <!-- Optionnel: Contenu caché -->
                                    <!-- <div class="lecon-contenu" id="lecon-<?php echo $lecon['id']; ?>">
                                        <?php // echo nl2br(htmlspecialchars($lecon['contenu'])); ?>
                                    </div> -->
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                 </div>
            </div>
        </div>

    <?php endif; ?>

</main>

<?php include 'foote.php'; ?>
