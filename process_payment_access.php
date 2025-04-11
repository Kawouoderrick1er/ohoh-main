<?php // c:\xampp\htdocs\ohoh-main\process_payment_access.php
session_start();
require_once 'base.php'; // DB Connection

// --- Include PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// --- Response Setup ---
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Erreur initiale.'];

// --- Input Validation ---
$formation_id = filter_input(INPUT_POST, 'formation_id', FILTER_VALIDATE_INT);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS); // Basic sanitize, more specific validation needed for phone formats
$moyen_paiement = filter_input(INPUT_POST, 'moyen_paiement', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$formation_id) {
    $response['message'] = 'ID de formation invalide.';
    echo json_encode($response);
    exit;
}
if (!$email) {
    $response['message'] = 'Adresse email invalide.';
    echo json_encode($response);
    exit;
}
if (empty($phone)) { // Add more specific validation if needed
    $response['message'] = 'Numéro de téléphone requis.';
    echo json_encode($response);
    exit;
}
if (empty($moyen_paiement)) {
    $response['message'] = 'Moyen de paiement requis.';
    echo json_encode($response);
    exit;
}

// --- Fetch Formation Details (Price) ---
try {
    $sql_cours = "SELECT titre, prix FROM cours WHERE id = :id AND statut = 'publié'";
    $stmt_cours = $conn->prepare($sql_cours);
    $stmt_cours->bindParam(':id', $formation_id, PDO::PARAM_INT);
    $stmt_cours->execute();
    $cours = $stmt_cours->fetch(PDO::FETCH_ASSOC);

    if (!$cours) {
        $response['message'] = 'Formation non trouvée ou non disponible.';
        echo json_encode($response);
        exit;
    }
    $formation_titre = $cours['titre'];
    $montant_a_payer = (float)$cours['prix'];

} catch (PDOException $e) {
    error_log("Erreur DB fetch cours (process_payment): " . $e->getMessage());
    $response['message'] = 'Erreur serveur lors de la récupération des détails de la formation.';
    echo json_encode($response);
    exit;
}

// --- SIMULATE PAYMENT ---
// In a real application, you would integrate with a payment gateway API here.
// For now, we assume payment is successful if inputs are valid.
$payment_successful = true; // <<<< SIMULATION

if ($payment_successful) {

    // --- Generate Access Code ---
    $access_code = strtoupper(bin2hex(random_bytes(4))); // Example: 8-char hex code

    // --- Record Inscription/Payment ---
    try {
        // Check if user exists to get ID
        $user_id = null;
        $sql_find_user = "SELECT id FROM utilisateurs WHERE email = :email";
        $stmt_find_user = $conn->prepare($sql_find_user);
        $stmt_find_user->bindParam(':email', $email);
        $stmt_find_user->execute();
        $user_result = $stmt_find_user->fetch(PDO::FETCH_ASSOC);
        if ($user_result) {
            $user_id = $user_result['id'];
        }

        // Insert into inscriptions table
        $sql_insert = "INSERT INTO inscriptions (utilisateur_id, cours_id, email_client, montant_paye, moyen_paiement, numero_telephone_paiement, access_code_envoye, date_inscription)
                       VALUES (:user_id, :cours_id, :email, :montant, :moyen, :phone, :code, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Can be NULL
        $stmt_insert->bindParam(':cours_id', $formation_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':email', $email);
        $stmt_insert->bindParam(':montant', $montant_a_payer);
        $stmt_insert->bindParam(':moyen', $moyen_paiement);
        $stmt_insert->bindParam(':phone', $phone);
        $stmt_insert->bindParam(':code', $access_code);

        $stmt_insert->execute();

    } catch (PDOException $e) {
        error_log("Erreur DB insert inscription (process_payment): " . $e->getMessage());
        // Decide if we should still send the email if DB fails? Probably not.
        $response['message'] = 'Erreur serveur lors de l\'enregistrement de l\'inscription.';
        echo json_encode($response);
        exit;
    }

    // --- Send Access Code Email ---
    $mail = new PHPMailer(true);
    try {
        // --- Server Settings (Copy from gestion_emails.php or contact.php) ---
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Your SMTP Host
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kawouoderrick@gmail.com'; // Your SMTP Email
        $mail->Password   = 'hkebszfedxvgynis'; // Your SMTP Password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        // --- Recipients ---
        $mail->setFrom('kawouoderrick@gmail.com', 'Support D-X-T'); // Your From Address
        $mail->addAddress($email); // Send to the client's email
        // $mail->addReplyTo('support@d-x-t.com', 'Support D-X-T');

        // --- Content ---
        $mail->isHTML(true);
        $mail->Subject = 'Votre accès à la formation : ' . $formation_titre;
        $mail->Body    = "Bonjour,<br><br>" .
                         "Merci pour votre inscription à la formation <strong>" . htmlspecialchars($formation_titre) . "</strong>.<br><br>" .
                         "Votre paiement (simulation) a été validé.<br><br>" .
                         "Voici votre code d'accès personnel : <strong style='font-size: 1.2em; color: #007bff;'>" . $access_code . "</strong><br><br>" .
                         "Conservez ce code précieusement. Vous en aurez besoin pour accéder au contenu de la formation.<br>" .
                         "<em>(Note: Le mécanisme d'utilisation de ce code doit être implémenté séparément).</em><br><br>" .
                         "Cordialement,<br>" .
                         "L'équipe D-X-T";
        $mail->AltBody = "Bonjour,\n\nMerci pour votre inscription à la formation " . $formation_titre . ".\n\n" .
                         "Votre paiement (simulation) a été validé.\n\n" .
                         "Voici votre code d'accès personnel : " . $access_code . "\n\n" .
                         "Conservez ce code précieusement. Vous en aurez besoin pour accéder au contenu de la formation.\n" .
                         "(Note: Le mécanisme d'utilisation de ce code doit être implémenté séparément).\n\n" .
                         "Cordialement,\nL'équipe D-X-T";

        $mail->send();

        $response['success'] = true;
        $response['message'] = 'Paiement simulé réussi ! Un email contenant votre code d\'accès a été envoyé à ' . htmlspecialchars($email) . '.';

    } catch (Exception $e) {
        error_log("Mailer Error (process_payment): {$mail->ErrorInfo}");
        // Payment was successful, DB entry made, but email failed.
        $response['success'] = false; // Indicate partial failure
        $response['message'] = "Paiement enregistré, mais l'envoi de l'email d'accès a échoué. Veuillez contacter le support avec votre email ($email) et le nom de la formation.";
    }

} else {
    // --- Payment Simulation Failed ---
    $response['message'] = 'La simulation de paiement a échoué. Veuillez réessayer.';
    // No DB entry, no email sent
}

// --- Send Final JSON Response ---
echo json_encode($response);
exit;
?>
