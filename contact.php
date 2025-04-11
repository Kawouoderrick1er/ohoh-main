<?php // c:\xampp\htdocs\ohoh\contact.php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'base.php'; // Inclure la connexion $conn

$message_display = '';
$message_type = 'danger';
$form_data = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['envoyer'])) {
    $form_data['nom'] = trim(filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS));
    $form_data['email'] = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
    $form_data['sujet'] = trim(filter_input(INPUT_POST, 'sujet', FILTER_SANITIZE_SPECIAL_CHARS));
    $form_data['message'] = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS));

    if (empty($form_data['nom']) || empty($form_data['email']) || empty($form_data['sujet']) || empty($form_data['message'])) {
        $message_display = "Veuillez remplir tous les champs obligatoires (*).";
    } elseif (!$form_data['email']) {
         $message_display = "L'adresse email fournie n'est pas valide.";
         $form_data['email'] = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    } else {
        $message_saved_to_db = false;
        // --- Sauvegarde en Base de Données ---
        try {
            $ip_adresse = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            $sql_insert_msg = "INSERT INTO messages_contact (nom, email, sujet, message, ip_adresse, user_agent)
                               VALUES (:nom, :email, :sujet, :message, :ip, :ua)";
            $stmt_insert_msg = $conn->prepare($sql_insert_msg);
            $stmt_insert_msg->bindParam(':nom', $form_data['nom']);
            $stmt_insert_msg->bindParam(':email', $form_data['email']);
            $stmt_insert_msg->bindParam(':sujet', $form_data['sujet']);
            $stmt_insert_msg->bindParam(':message', $form_data['message']);
            $stmt_insert_msg->bindParam(':ip', $ip_adresse);
            $stmt_insert_msg->bindParam(':ua', $user_agent);
            $stmt_insert_msg->execute();
            $message_saved_to_db = true; // Marquer comme sauvegardé

        } catch (PDOException $e) {
            error_log("Erreur sauvegarde message contact DB: " . $e->getMessage());
            $message_display = "Une erreur technique est survenue lors de la sauvegarde de votre message. ";
            // On peut choisir de continuer ou non l'envoi d'email ici
            // Pour l'instant, on continue mais on prévient l'utilisateur
        }

        // --- Envoi de l'Email de Notification ---
        $mail = new PHPMailer(true);
        try {
            // --- Paramètres Serveur (VOS INFOS) ---
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // !! VOTRE SERVEUR SMTP !!
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kawouoderrick@gmail.com'; // !! VOTRE EMAIL SMTP !!
            $mail->Password   = 'hkebszfedxvgynis'; // !! VOTRE MOT DE PASSE (ou d'application) !!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Destinataires
            $mail->setFrom('kawouoderrick@gmail.com', 'Contact D-X-T'); // !! VOTRE ADRESSE D'EXPEDITION !!
            $mail->addAddress('kawouoderrick@gmail.com', 'Support D-X-T'); // !! VOTRE ADRESSE DE RECEPTION !!
            $mail->addReplyTo($form_data['email'], $form_data['nom']);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = '[D-X-T Contact] Nouveau message: ' . $form_data['sujet']; // Sujet plus clair pour l'admin
            $mail->Body    = "Nouveau message reçu via le formulaire de contact D-X-T :<br><br>" .
                             "<b>Nom :</b> " . htmlspecialchars($form_data['nom']) . "<br>" .
                             "<b>Email :</b> " . htmlspecialchars($form_data['email']) . "<br>" .
                             "<b>Sujet :</b> " . htmlspecialchars($form_data['sujet']) . "<br><br>" .
                             "<b>Message :</b><br>" . nl2br(htmlspecialchars($form_data['message'])) . "<br><br>" .
                             "<hr><small>IP: " . htmlspecialchars($ip_adresse ?? 'N/A') . "</small>";
            $mail->AltBody = "Nouveau message:\nNom: " . $form_data['nom'] . "\nEmail: " . $form_data['email'] . "\nSujet: " . $form_data['sujet'] . "\n\nMessage:\n" . $form_data['message'];

            $mail->send();

            // Message final à l'utilisateur
            if ($message_saved_to_db) {
                 $message_display = 'Votre message a été envoyé et enregistré avec succès !';
                 $message_type = 'success';
                 $form_data = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => '']; // Vider si tout OK
            } else {
                 // Si la sauvegarde DB a échoué mais l'email est parti
                 $message_display .= " La notification par email a été envoyée.";
                 $message_type = 'warning'; // Indiquer un succès partiel
            }


        } catch (Exception $e) {
            // Si l'email échoue
            if ($message_saved_to_db) {
                // Le message est en DB, mais l'email admin a échoué
                 $message_display = "Votre message a bien été enregistré, mais la notification admin n'a pas pu être envoyée. Erreur: " . htmlspecialchars($mail->ErrorInfo);
                 $message_type = 'warning';
            } else {
                // Les deux ont échoué
                 $message_display .= " De plus, la notification par email n'a pas pu être envoyée. Erreur: " . htmlspecialchars($mail->ErrorInfo);
                 $message_type = 'danger';
            }
            error_log("Erreur Email Contact Form: {$mail->ErrorInfo}");
        }
    }
}

include 'navigation.php';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - D-X-T</title>
</head>

    <section class="contact-section">
        <div class="container">
             <h2 class="section-title animate-on-scroll">Contactez-nous</h2>
            <div class="row g-lg-5 justify-content-center">
                <div class="col-lg-5 d-flex">
                    <div class="contact-info animate-on-scroll">
                        <h3>Restons en contact</h3>
                        <p>Nous sommes là pour répondre à toutes vos questions. Utilisez le formulaire ou nos coordonnées ci-dessous.</p>
                        <hr>
                        <p><i class="fas fa-map-marker-alt"></i> Votre Adresse Complète, Ville, Pays</p>
                        <p><i class="fas fa-phone"></i> +XX XXX XXX XXX</p>
                        <p><i class="fas fa-envelope"></i> info@d-x-t.com</p>
                        <hr>
                        <p>Suivez-nous également sur les réseaux sociaux !</p>
                         <div class="mt-3 social-links">
                            <a href="#" aria-label="Facebook" class="fs-4"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Twitter" class="fs-4"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="LinkedIn" class="fs-4"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" aria-label="Instagram" class="fs-4"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 d-flex">
                    <div class="contact-form-container w-100 animate-on-scroll" style="transition-delay: 0.1s;">
                        <h3 class="text-center mb-4">Envoyez-nous un message</h3>
                        <?php if (!empty($message_display)): ?>
                            <div class="alert alert-<?php echo $message_type; ?> fade-in is-visible" role="alert">
                                <?php echo htmlspecialchars($message_display); ?>
                            </div>
                        <?php endif; ?>
                        <form action="contact.php" method="post" novalidate>
                            <div class="mb-3 form-group-animate">
                                <label for="nom" class="form-label">Votre Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo htmlspecialchars($form_data['nom']); ?>">
                            </div>
                            <div class="mb-3 form-group-animate" style="transition-delay: 0.1s;">
                                <label for="email" class="form-label">Votre Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($form_data['email']); ?>">
                            </div>
                            <div class="mb-3 form-group-animate" style="transition-delay: 0.2s;">
                                <label for="sujet" class="form-label">Sujet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sujet" name="sujet" required value="<?php echo htmlspecialchars($form_data['sujet']); ?>">
                            </div>
                            <div class="mb-3 form-group-animate" style="transition-delay: 0.3s;">
                                <label for="message" class="form-label">Votre Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($form_data['message']); ?></textarea>
                            </div>
                            <div class="d-grid form-group-animate" style="transition-delay: 0.4s;">
                                <button type="submit" name="envoyer" value="envoyer" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'foote.php'; ?>
