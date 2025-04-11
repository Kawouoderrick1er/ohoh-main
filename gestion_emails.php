<?php
// gestion_emails.php (MODIFIÉ POUR AJAX)
session_start();

// --- Vérification de Sécurité Essentielle ---
// if (!isset($_SESSION['admin_id'])) {
//     http_response_code(403);
//     echo '<div class="alert alert-danger" role="alert"><strong>Accès refusé.</strong> Veuillez vous reconnecter.</div>';
//     exit();
// }

// --- Inclusion de PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Initialiser les variables
$message_display = '';
$message_type = 'info';

// --- Traitement de la soumission du formulaire ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_email'])) {
    // ... (Gardez votre logique d'envoi d'email existante) ...
    $recipient_email = filter_input(INPUT_POST, 'recipient_email', FILTER_VALIDATE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
    $body = $_POST['body'] ?? '';

    if (!$recipient_email) {
        $message_display = "Erreur : L'adresse email du destinataire n'est pas valide.";
        $message_type = 'danger';
    } elseif (empty($subject)) {
        $message_display = "Erreur : Le sujet ne peut pas être vide.";
        $message_type = 'danger';
    } elseif (empty($body)) {
        $message_display = "Erreur : Le corps de l'email ne peut pas être vide.";
        $message_type = 'danger';
    } else {
        $mail = new PHPMailer(true);
        try {
            // --- Paramètres Serveur ---
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kawouoderrick@gmail.com';
            $mail->Password   = 'hkebszfedxvgynis'; // !! MOT DE PASSE D'APPLICATION !!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // --- Destinataires ---
            $mail->setFrom('kawouoderrick@gmail.com', 'Admin D-X-T');
            $mail->addAddress($recipient_email);
            // $mail->addReplyTo('info@example.com', 'Information');

            // --- Contenu ---
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br(htmlspecialchars($body));
            $mail->AltBody = $body;

            $mail->send();
            $message_display = 'Email envoyé avec succès à ' . htmlspecialchars($recipient_email);
            $message_type = 'success';

        } catch (Exception $e) {
            $message_display = "L'email n'a pas pu être envoyé. Erreur PHPMailer: " . htmlspecialchars($mail->ErrorInfo);
            $message_type = 'danger';
            error_log("Mailer Error [gestion_emails.php]: {$mail->ErrorInfo}");
        }
    }
}

// --- Début de la sortie HTML pour #content-area ---
?>

<div class="d-flex justify-content-between align-items-center mb-3">
     <h2 class="mb-0"><i class="fas fa-envelope-open-text me-2"></i>Gestion des Emails</h2>
</div>
<hr>

<!-- Zone d'affichage des messages -->
<?php if (!empty($message_display)): ?>
    <div class="message alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
        <?php echo $message_display; ?>
    </div>
<?php endif; ?>

<!-- Avertissement important sur la configuration SMTP -->
<!-- <div class="alert alert-warning" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i> <strong>Attention :</strong> La configuration SMTP (Host, Username, Password, etc.) doit être correcte dans le code PHP. Utilisez un mot de passe d'application pour Gmail si la 2FA est activée.
</div> -->

<!-- Formulaire d'envoi d'email -->
<form action="gestion_emails.php" method="post" id="emailForm"> <!-- Donner un ID au formulaire si besoin de le cibler en JS -->
    <div class="mb-3">
        <label for="recipient_email" class="form-label">Destinataire <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="recipient_email" name="recipient_email" placeholder="adresse.destinataire@exemple.com" required value="<?php echo isset($_POST['recipient_email']) ? htmlspecialchars($_POST['recipient_email']) : ''; ?>">
    </div>
    <div class="mb-3">
        <label for="subject" class="form-label">Sujet <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="subject" name="subject" placeholder="Sujet de votre email" required value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
    </div>
    <div class="mb-3">
        <label for="body" class="form-label">Corps de l'email <span class="text-danger">*</span></label>
        <textarea class="form-control" id="body" name="body" rows="10" placeholder="Écrivez votre message ici..." required><?php echo isset($_POST['body']) ? htmlspecialchars($_POST['body']) : ''; ?></textarea>
        <!-- <div class="form-text">Les sauts de ligne seront convertis en &lt;<br> ;.</div> -->

    </div>

    <button type="submit" name="send_email" class="btn btn-primary">
        <i class="fas fa-paper-plane me-1"></i> Envoyer l'Email
    </button>
</form>

<!-- Optionnel: Script JS spécifique à cette section -->
<!-- <script>
    // Si le formulaire doit être soumis via AJAX pour éviter le rechargement même partiel:
    
    document.getElementById('emailForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Empêche la soumission standard
        const formData = new FormData(this);
        const url = this.action; // Récupère l'URL du formulaire

        // Afficher un indicateur de chargement si nécessaire
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Envoi...';

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Recharger uniquement cette section avec la réponse (qui contient le message de succès/erreur)
            // Il faut que la réponse PHP soit aussi juste le fragment HTML mis à jour
            contentArea.innerHTML = data; // 'contentArea' est défini dans admin_dashboard.php
            reinitializeBootstrapComponents(); // Réinitialiser si besoin
        })
        .catch(error => {
            console.error('Erreur soumission email:', error);
            alert('Erreur lors de l\'envoi du formulaire.');
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Envoyer l\'Email';
        });
    });
   
</script> -->

<?php // --- Fin de la sortie HTML pour #content-area --- ?>
