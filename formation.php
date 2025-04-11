<?php // c:\xampp\htdocs\ohoh-main\formation.php (COMPLETELY REVISED)
session_start();
require_once 'base.php'; // Include DB connection

$formations = [];
$error_message = '';

try {
    // Fetch published formations with trainer names
    $sql = "SELECT c.id, c.titre, c.description, c.prix, c.logo_path, c.formateur_id, u.nom AS nom_formateur
            FROM cours c
            LEFT JOIN utilisateurs u ON c.formateur_id = u.id AND u.type_utilisateur = 'formateur'
            WHERE c.statut = 'publié'
            ORDER BY c.date_creation DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erreur fetch formations (formation.php): " . $e->getMessage());
    $error_message = "Erreur lors du chargement des formations.";
}

include 'navigation.php'; // Includes header, nav, opens <main>
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Formations - D-X-T</title>
    <!-- Specific styles for this page -->
    <style>
        .formation-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%; /* Ensure cards in a row have same height */
            display: flex;
            flex-direction: column;
        }
        .formation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            width: 100%;
            height: 180px; /* Fixed height for logos */
            object-fit: cover; /* Cover ensures image fills space nicely */
            border-bottom: 1px solid #eee;
        }
        .card-body {
            flex-grow: 1; /* Allows body to expand */
            display: flex;
            flex-direction: column;
        }
        .card-title {
            font-weight: bold;
            color: #333;
        }
        .card-text {
             font-size: 0.95rem;
             color: #555;
             flex-grow: 1; /* Allows text to expand */
             margin-bottom: 1rem;
        }
        .card-footer {
            background-color: #f8f9fa;
            border-top: none;
            font-size: 0.9rem;
        }
        .trainer-name { color: #6c757d; }
        .price { font-weight: bold; color: #007bff; font-size: 1.1rem; }
        .access-request-form input[type="email"] { margin-bottom: 0.5rem; }
        .modal-price { font-size: 1.2rem; font-weight: bold; color: #28a745; }
        #paymentModal .alert { display: none; } /* Hide modal alert initially */
    </style>
</head>

<div class="container mt-5 mb-5">
    <h1 class="text-center mb-4">Découvrez Nos Formations</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php elseif (empty($formations)): ?>
        <div class="alert alert-info text-center" role="alert">
            <i class="fas fa-info-circle me-2"></i> Aucune formation n'est disponible pour le moment. Revenez bientôt !
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($formations as $formation): ?>
                <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                    <div class="card formation-card shadow-sm">
                        <img src="<?php echo htmlspecialchars(!empty($formation['logo_path']) ? $formation['logo_path'] : 'Images/logos/default.png'); ?>" class="card-img-top" alt="Logo <?php echo htmlspecialchars($formation['titre']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($formation['titre']); ?></h5>
                            <?php if (!empty($formation['nom_formateur'])): ?>
                                <p class="trainer-name mb-2"><i class="fas fa-chalkboard-teacher me-1"></i> Par <?php echo htmlspecialchars($formation['nom_formateur']); ?></p>
                            <?php endif; ?>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($formation['description'], 0, 120))) . (strlen($formation['description']) > 120 ? '...' : ''); ?></p>
                            <div class="mt-auto"> <!-- Pushes content below to bottom -->
                                <p class="price mb-3">Prix : <?php echo number_format((float)$formation['prix'], 2, ',', ' '); ?> €</p>
                                <form class="access-request-form" onsubmit="return false;"> <!-- Prevent default submit -->
                                    <input type="email" class="form-control form-control-sm request-email-input" placeholder="Votre email pour l'accès" required
                                           value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; /* Pre-fill if logged in */ ?>">
                                    <button type="button" class="btn btn-primary btn-sm w-100 request-access-btn"
                                            data-bs-toggle="modal" data-bs-target="#paymentModal"
                                            data-formation-id="<?php echo $formation['id']; ?>"
                                            data-formation-titre="<?php echo htmlspecialchars($formation['titre']); ?>"
                                            data-formation-prix="<?php echo $formation['prix']; ?>">
                                        <i class="fas fa-key me-1"></i> Demander l'accès
                                    </button>
                                </form>
                            </div>
                        </div>
                        <!-- Optional Footer for more info -->
                        <!-- <div class="card-footer text-muted">
                            <small>Ref: F<?php //echo $formation['id']; ?></small>
                        </div> -->
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Accès à la formation : <span></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert" id="modalErrorMessage"></div>
                <div class="alert alert-success" role="alert" id="modalSuccessMessage"></div>

                <form id="paymentForm">
                    <input type="hidden" name="formation_id" id="modalFormationId">
                    <input type="hidden" name="email" id="modalEmail"> <!-- Store email here -->

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
                            <!-- Add other methods if needed -->
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const paymentForm = document.getElementById('paymentForm');
    const modalTitleSpan = document.getElementById('paymentModalLabel').querySelector('span');
    const modalFormationTitre = document.getElementById('modalFormationTitre');
    const modalFormationPrix = document.getElementById('modalFormationPrix');
    const modalFormationIdInput = document.getElementById('modalFormationId');
    const modalEmailInput = document.getElementById('modalEmail');
    const modalErrorMessage = document.getElementById('modalErrorMessage');
    const modalSuccessMessage = document.getElementById('modalSuccessMessage');
    const submitPaymentBtn = document.getElementById('submitPaymentBtn');
    const paymentPhoneInput = document.getElementById('paymentPhone');

    // Use event delegation for request buttons
    document.querySelector('.container').addEventListener('click', function(event) {
        if (event.target.classList.contains('request-access-btn')) {
            const button = event.target;
            const card = button.closest('.card');
            const emailInput = card.querySelector('.request-email-input');
            const email = emailInput.value.trim();

            if (!email || !validateEmail(email)) {
                alert("Veuillez saisir une adresse email valide.");
                emailInput.focus();
                event.stopPropagation(); // Prevent modal from showing
                return;
            }

            // Populate modal
            const formationId = button.dataset.formationId;
            const formationTitre = button.dataset.formationTitre;
            const formationPrix = parseFloat(button.dataset.formationPrix).toFixed(2);

            modalTitleSpan.textContent = formationTitre;
            modalFormationTitre.textContent = formationTitre;
            modalFormationPrix.textContent = formationPrix.replace('.', ',');
            modalFormationIdInput.value = formationId;
            modalEmailInput.value = email; // Store email in hidden input

            // Reset modal state
            modalErrorMessage.style.display = 'none';
            modalSuccessMessage.style.display = 'none';
            paymentForm.reset(); // Clear phone number etc.
            modalEmailInput.value = email; // Re-set email after reset
            modalFormationIdInput.value = formationId; // Re-set ID after reset
            submitPaymentBtn.disabled = false;
            submitPaymentBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Valider et Payer (Simulation)';

            // No need to manually call paymentModal.show() if using data-bs-toggle/target
        }
    });


    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent normal form submission

        // Basic validation (HTML5 required should handle most)
        if (!paymentPhoneInput.value.trim()) {
             showModalError("Veuillez entrer votre numéro de téléphone.");
             return;
        }
         if (!document.getElementById('paymentMethod').value) {
             showModalError("Veuillez choisir un moyen de paiement.");
             return;
         }


        // Disable button and show loading state
        submitPaymentBtn.disabled = true;
        submitPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Traitement...';
        modalErrorMessage.style.display = 'none';
        modalSuccessMessage.style.display = 'none';

        const formData = new FormData(paymentForm);

        fetch('process_payment_access.php', { // Send to the new processing script
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Expect JSON response
        .then(data => {
            if (data.success) {
                showModalSuccess(data.message);
                // Optionally close modal after a delay
                setTimeout(() => {
                    paymentModal.hide();
                }, 4000); // Close after 4 seconds
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

<?php include 'foote.php'; // Includes closing </main>, footer, scripts, closing </body></html> ?>
