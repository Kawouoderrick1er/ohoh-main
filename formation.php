<?php // c:\xampp\htdocs\ohoh-main\formation.php (REVISED FOR DYNAMIC DISPLAY)
session_start();
require_once 'base.php'; // Inclure la connexion $conn

$formations = [];
$error_message = '';

try {
    // Récupérer les formations publiées avec le nom du formateur
    $sql = "SELECT c.id, c.titre, c.description, c.prix, c.logo_path, c.formateur_id, u.nom AS nom_formateur
            FROM cours c
            LEFT JOIN utilisateurs u ON c.formateur_id = u.id AND u.type_utilisateur = 'formateur'
            WHERE c.statut = 'publié' -- Afficher uniquement les cours publiés
            ORDER BY c.date_creation DESC"; // Trier par date de création
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erreur fetch formations (formation.php): " . $e->getMessage());
    $error_message = "Erreur lors du chargement des formations.";
}

include 'navigation.php'; // Inclut l'en-tête, la nav, et ouvre <main>
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Formations - D-X-T</title>
    <!-- Styles spécifiques pour cette page -->
    <style>
        .formation-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%; /* Assure que les cartes dans une rangée ont la même hauteur */
            display: flex;
            flex-direction: column;
            border: none;
            border-radius: 8px;
            overflow: hidden; /* Pour que l'image respecte les coins arrondis */
        }
        .formation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            width: 100%;
            height: 200px; /* Hauteur fixe pour les logos/images */
            object-fit: cover; /* 'cover' remplit bien l'espace */
            background-color: #eee; /* Couleur de fond si l'image est transparente ou manquante */
        }
        .card-body {
            flex-grow: 1; /* Permet au corps de s'étendre */
            display: flex;
            flex-direction: column; /* Organise le contenu verticalement */
            padding: 1.25rem;
        }
        .card-title {
            font-weight: 600; /* Titre un peu plus gras */
            color: #343a40;
            margin-bottom: 0.5rem;
        }
        .trainer-name {
            font-size: 0.9rem;
            color: #6c757d; /* Gris pour le nom du formateur */
            margin-bottom: 1rem;
        }
        .trainer-name i {
            color: #007bff; /* Icône en bleu */
        }
        .price {
            font-weight: bold;
            color: #28a745; /* Vert pour le prix */
            font-size: 1.2rem;
            margin-top: auto; /* Pousse le prix vers le bas s'il y a de l'espace */
            margin-bottom: 1rem;
        }
        .access-request-form {
            margin-top: 1rem; /* Espace au-dessus du formulaire */
        }
        .access-request-form input[type="email"] {
            margin-bottom: 0.75rem; /* Espace sous l'email */
        }
        .request-access-btn {
            font-size: 0.95rem;
        }

        /* Styles pour le modal (identiques à la version précédente) */
        .modal-price { font-size: 1.2rem; font-weight: bold; color: #28a745; }
        #paymentModal .alert { display: none; } /* Cacher les alertes modales initialement */

    </style>
</head>

<div class="container mt-5 mb-5">
    <h1 class="text-center mb-5 display-5">Découvrez Nos Formations</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php elseif (empty($formations)): ?>
        <div class="alert alert-info text-center" role="alert">
            <i class="fas fa-info-circle fa-2x mb-3 d-block text-secondary"></i>
            Aucune formation n'est disponible pour le moment. Revenez bientôt !
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($formations as $formation): ?>
                <?php
                    // Définir le chemin du logo avec un fallback
                    $logoPath = (!empty($formation['logo_path']) && file_exists($formation['logo_path']))
                                ? $formation['logo_path']
                                : 'Images/logos/default.png'; // Assurez-vous que ce fichier existe
                ?>
                <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                    <div class="card formation-card shadow-sm animate-on-scroll">
                        <img src="<?php echo htmlspecialchars($logoPath); ?>" class="card-img-top" alt="Logo <?php echo htmlspecialchars($formation['titre']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($formation['titre']); ?></h5>

                            <?php if (!empty($formation['nom_formateur'])): ?>
                                <p class="trainer-name mb-2">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> Par <?php echo htmlspecialchars($formation['nom_formateur']); ?>
                                </p>
                            <?php else: ?>
                                <p class="trainer-name mb-2 text-muted"><i class="fas fa-chalkboard-teacher me-1"></i> Formateur non spécifié</p>
                            <?php endif; ?>

                            <!-- Prix -->
                            <p class="price mb-3">
                                <?php echo number_format((float)$formation['prix'], 2, ',', ' '); ?> €
                            </p>

                            <!-- Formulaire de demande d'accès -->
                            <div class="access-request-form mt-auto">
                                <form onsubmit="return false;"> <!-- Empêche la soumission standard -->
                                    <input type="email" class="form-control form-control-sm request-email-input mb-2" placeholder="Votre email pour l'accès" required
                                           value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; /* Pré-remplir si connecté */ ?>">

                                    <button type="button" class="btn btn-primary btn-sm w-100 request-access-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#paymentModal"
                                            data-formation-id="<?php echo $formation['id']; ?>"
                                            data-formation-titre="<?php echo htmlspecialchars($formation['titre']); ?>"
                                            data-formation-prix="<?php echo $formation['prix']; ?>">
                                        <i class="fas fa-key me-1"></i> Demander l'accès
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de Paiement (Identique à la version précédente) -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Accès à la formation : <span><!-- Titre ici --></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert" id="modalErrorMessage" style="display: none;"></div>
                <div class="alert alert-success" role="alert" id="modalSuccessMessage" style="display: none;"></div>

                <form id="paymentForm">
                    <input type="hidden" name="formation_id" id="modalFormationId">
                    <input type="hidden" name="email" id="modalEmail"> <!-- Email stocké ici -->

                    <p class="text-center">Pour finaliser votre inscription à <strong id="modalFormationTitre"></strong>, veuillez simuler le paiement.</p>
                    <p class="text-center modal-price mb-3">Montant : <span id="modalFormationPrix"></span> €</p>

                    <div class="mb-3">
                        <label for="paymentPhone" class="form-label">Votre Numéro de Téléphone (pour paiement) <span class="text-danger">*</span></label>
                        <div class="input-group">
                             <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                             <input type="tel" class="form-control" id="paymentPhone" name="phone" placeholder="Ex: 699XXXXXX" required pattern="[0-9]{9,15}">
                        </div>
                         <div class="form-text">Entrez votre numéro pour approuver le paiement (simulation).</div>
                    </div>

                     <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Moyen de Paiement <span class="text-danger">*</span></label>
                        <select class="form-select" id="paymentMethod" name="moyen_paiement" required>
                            <option value="" selected disabled>Choisir...</option>
                            <option value="MTN">MTN Mobile Money</option>
                            <option value="Orange">Orange Money</option>
                            <!-- Ajoutez d'autres méthodes si nécessaire -->
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success" id="submitPaymentBtn">
                            <i class="fas fa-check-circle me-1"></i> Valider et Payer (Simulation)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript (Identique à la version précédente pour gérer le modal et le fetch) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const paymentForm = document.getElementById('paymentForm');
    const modalTitleSpan = document.getElementById('paymentModalLabel').querySelector('span');
    const modalFormationTitre = document.getElementById('modalFormationTitre');
    const modalFormationPrix = document.getElementById('modalFormationPrix');
    const modalFormationIdInput = document.getElementById('modalFormationId');
    const modalEmailInput = document.getElementById('modalEmail'); // Input caché pour l'email
    const modalErrorMessage = document.getElementById('modalErrorMessage');
    const modalSuccessMessage = document.getElementById('modalSuccessMessage');
    const submitPaymentBtn = document.getElementById('submitPaymentBtn');
    const paymentPhoneInput = document.getElementById('paymentPhone');

    // Utiliser la délégation d'événements pour les boutons "Demander l'accès"
    document.querySelector('.container').addEventListener('click', function(event) {
        // Vérifier si le clic provient d'un bouton de demande d'accès
        if (event.target.classList.contains('request-access-btn')) {
            const button = event.target;
            const card = button.closest('.card'); // Trouver la carte parente
            const emailInput = card.querySelector('.request-email-input'); // Trouver l'input email dans cette carte
            const email = emailInput.value.trim();

            // Valider l'email avant d'ouvrir le modal
            if (!email || !validateEmail(email)) {
                alert("Veuillez saisir une adresse email valide.");
                emailInput.focus();
                event.stopPropagation(); // Empêcher l'ouverture du modal si l'email est invalide
                return;
            }

            // Récupérer les données de la formation depuis les attributs data-* du bouton
            const formationId = button.dataset.formationId;
            const formationTitre = button.dataset.formationTitre;
            const formationPrix = parseFloat(button.dataset.formationPrix).toFixed(2);

            // Remplir les informations dans le modal
            modalTitleSpan.textContent = formationTitre;
            modalFormationTitre.textContent = formationTitre;
            modalFormationPrix.textContent = formationPrix.replace('.', ','); // Afficher avec une virgule
            modalFormationIdInput.value = formationId;
            modalEmailInput.value = email; // Stocker l'email validé dans le champ caché du modal

            // Réinitialiser l'état du modal (messages d'erreur/succès, formulaire)
            modalErrorMessage.style.display = 'none';
            modalSuccessMessage.style.display = 'none';
            paymentForm.reset(); // Effacer les champs (numéro de tél, moyen de paiement)
            modalEmailInput.value = email; // Ré-appliquer l'email après reset
            modalFormationIdInput.value = formationId; // Ré-appliquer l'ID après reset
            submitPaymentBtn.disabled = false;
            submitPaymentBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Valider et Payer (Simulation)';

            // Le modal s'ouvrira automatiquement grâce à data-bs-toggle/target
        }
    });

    // Gestion de la soumission du formulaire de paiement (identique)
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!paymentPhoneInput.value.trim()) {
             showModalError("Veuillez entrer votre numéro de téléphone.");
             return;
        }
         if (!document.getElementById('paymentMethod').value) {
             showModalError("Veuillez choisir un moyen de paiement.");
             return;
         }

        submitPaymentBtn.disabled = true;
        submitPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Traitement...';
        modalErrorMessage.style.display = 'none';
        modalSuccessMessage.style.display = 'none';

        const formData = new FormData(paymentForm);

        fetch('process_payment_access.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModalSuccess(data.message);
                setTimeout(() => { paymentModal.hide(); }, 4000);
            } else {
                showModalError(data.message || "Une erreur inconnue est survenue.");
                submitPaymentBtn.disabled = false;
                submitPaymentBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Valider et Payer (Simulation)';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showModalError("Erreur de communication avec le serveur. Veuillez réessayer.");
            submitPaymentBtn.disabled = false;
            submitPaymentBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Valider et Payer (Simulation)';
        });
    });

    // Fonctions utilitaires pour le modal (identiques)
    function showModalError(message) {
        modalErrorMessage.textContent = message;
        modalErrorMessage.style.display = 'block';
        modalSuccessMessage.style.display = 'none';
    }
    function showModalSuccess(message) {
        modalSuccessMessage.textContent = message;
        modalSuccessMessage.style.display = 'block';
        modalErrorMessage.style.display = 'none';
    }
    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

});
</script>

<?php include 'foote.php'; // Inclut la fermeture de </main>, footer, scripts, etc. ?>
