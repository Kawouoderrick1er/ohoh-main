<?php // c:\xampp\htdocs\ohoh-main\gestion_generique.php (FONCTIONNALITÉS CRUD RÉACTIVÉES)
session_start();

// --- Sécurité ---
// !! IMPORTANT: Décommentez cette section en production !!
/*
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403); // Forbidden
    // Afficher un message adapté à l'intégration AJAX
    die('<div class="alert alert-danger m-3" role="alert"><i class="fas fa-exclamation-triangle me-2"></i><strong>Accès refusé.</strong> Session administrateur requise.</div>');
}
*/
$admin_id_session = $_SESSION['admin_id'] ?? null; // Pour vérification auto-suppression

// --- Configuration des Tables (Centralisée et Complète) ---
// Assurez-vous que cette configuration est correcte et complète
$config_tables = [
    'apprenants' => [
        'table_name' => 'utilisateurs', 'display_name' => 'Apprenants', 'primary_key' => 'id',
        'icon' => 'fa-users',
        'columns' => [
            'profile_image_path' => ['label' => 'Image', 'type' => 'image', 'no_edit' => true, 'no_add' => true],
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true, 'placeholder' => 'Nom complet'],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => 'exemple@domaine.com'],
            'telephone' => ['label' => 'Téléphone', 'type' => 'tel', 'placeholder' => 'Numéro de téléphone'],
            'adresse' => ['label' => 'Adresse', 'type' => 'textarea', 'placeholder' => 'Adresse postale', 'no_list' => true],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'etudiant'", 'insert_values' => ['type_utilisateur' => 'etudiant'], 'default_sort' => 'date_inscription DESC',
        'can_add' => true, 'can_edit' => true, 'can_delete' => true // Explicitement autorisé
    ],
    'formateurs' => [
        'table_name' => 'utilisateurs', 'display_name' => 'Formateurs', 'primary_key' => 'id',
        'icon' => 'fa-chalkboard-teacher',
        'columns' => [
            'profile_image_path' => ['label' => 'Image', 'type' => 'image', 'no_edit' => true, 'no_add' => true],
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true, 'placeholder' => 'Nom complet'],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => 'exemple@domaine.com'],
            'telephone' => ['label' => 'Téléphone', 'type' => 'tel', 'placeholder' => 'Numéro de téléphone'],
            'adresse' => ['label' => 'Adresse', 'type' => 'textarea', 'placeholder' => 'Adresse postale', 'no_list' => true],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'formateur'", 'insert_values' => ['type_utilisateur' => 'formateur'], 'default_sort' => 'date_inscription DESC',
        'can_add' => true, 'can_edit' => true, 'can_delete' => true
    ],
    'administrateurs' => [
        'table_name' => 'utilisateurs', 'display_name' => 'Administrateurs', 'primary_key' => 'id',
        'icon' => 'fa-user-cog',
        'columns' => [
            'profile_image_path' => ['label' => 'Image', 'type' => 'image', 'no_edit' => true, 'no_add' => true],
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true, 'placeholder' => 'Nom complet'],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => 'exemple@domaine.com'],
            'mot_de_passe' => ['label' => 'Mot de passe', 'type' => 'password', 'required' => true, 'edit_optional' => true, 'no_list' => true, 'placeholder' => 'Nouveau mot de passe (optionnel en modification)'],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'administrateur'", 'insert_values' => ['type_utilisateur' => 'administrateur'], 'default_sort' => 'date_inscription DESC',
        'can_add' => true, 'can_edit' => true, 'can_delete' => true // Attention à la suppression ici
    ],
    'formations' => [
        'table_name' => 'cours', 'display_name' => 'Formations', 'primary_key' => 'id',
        'icon' => 'fa-graduation-cap',
        'columns' => [
            'titre' => ['label' => 'Titre', 'type' => 'text', 'required' => true, 'placeholder' => 'Titre de la formation'],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => true, 'placeholder' => 'Description détaillée', 'no_list' => true],
            'formateur_id' => ['label' => 'ID Formateur', 'type' => 'number', 'placeholder' => 'ID numérique du formateur'], // TODO: Remplacer par select dynamique
            'formateur_nom' => ['label' => 'Nom Formateur', 'type' => 'text', 'readonly' => true, 'no_edit' => true, 'no_add' => true], // Colonne jointe
            'prix' => ['label' => 'Prix (€)', 'type' => 'number', 'step' => '0.01', 'required' => true, 'default' => 0.00, 'placeholder' => 'ex: 99.90'],
            'logo_path' => ['label' => 'Chemin Logo', 'type' => 'text', 'required' => false, 'placeholder' => 'Images/logos/default.png', 'no_list' => true], // TODO: Remplacer par upload
            'statut' => ['label' => 'Statut', 'type' => 'select', 'options' => ['publié' => 'Publié', 'brouillon' => 'Brouillon', 'archivé' => 'Archivé'], 'required' => true, 'default' => 'brouillon'],
            'date_creation' => ['label' => 'Créé le', 'type' => 'datetime', 'readonly' => true],
        ],
        'default_sort' => 'date_creation DESC',
        'can_add' => true, 'can_edit' => true, 'can_delete' => true
    ],
    'messages_contact' => [
        'table_name' => 'messages_contact', 'display_name' => 'Messages de Contact', 'primary_key' => 'id',
        'icon' => 'fa-envelope-open-text',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'readonly' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'readonly' => true],
            'sujet' => ['label' => 'Sujet', 'type' => 'text', 'readonly' => true],
            'message' => ['label' => 'Message', 'type' => 'textarea', 'readonly' => true, 'no_list' => true],
            'date_reception' => ['label' => 'Reçu le', 'type' => 'datetime', 'readonly' => true],
            'statut' => ['label' => 'Statut', 'type' => 'select', 'options' => ['nouveau' => 'Nouveau', 'lu' => 'Lu', 'répondu' => 'Répondu', 'archivé' => 'Archivé'], 'required' => true, 'default' => 'nouveau'],
        ],
        'default_sort' => 'date_reception DESC',
        'can_delete' => true,
        'can_add' => false, // On ne peut pas ajouter de message contact manuellement
        'can_edit' => true // On peut modifier le statut
    ],
    'inscriptions' => [
        'table_name' => 'inscriptions', 'display_name' => 'Inscriptions/Accès', 'primary_key' => 'id',
        'icon' => 'fa-user-check',
        'columns' => [
            'apprenant_nom' => ['label' => 'Nom Apprenant', 'type' => 'text', 'readonly' => true, 'no_edit' => true, 'no_add' => true], // Jointe
            'formation_titre' => ['label' => 'Titre Formation', 'type' => 'text', 'readonly' => true, 'no_edit' => true, 'no_add' => true], // Jointe
            'email_client' => ['label' => 'Email Client', 'type' => 'email', 'readonly' => true],
            'montant_paye' => ['label' => 'Montant Payé (€)', 'type' => 'number', 'readonly' => true, 'step' => '0.01'],
            'moyen_paiement' => ['label' => 'Moyen Paiement', 'type' => 'text', 'readonly' => true],
            'numero_telephone_paiement' => ['label' => 'Tél. Paiement', 'type' => 'tel', 'readonly' => true, 'no_list' => true],
            'access_code_envoye' => ['label' => 'Code Envoyé', 'type' => 'text', 'readonly' => true],
            'date_inscription' => ['label' => 'Date Accès', 'type' => 'datetime', 'readonly' => true],
        ],
        'default_sort' => 'date_inscription DESC',
        'can_delete' => true, // Permettre la suppression (révoquer accès?)
        'can_add' => false, // Accès via paiement, pas manuel ici
        'can_edit' => false // Pas d'édition prévue pour les inscriptions ici
    ],
];
// Rendre la config accessible globalement pour crud_operations.php
$GLOBALS['config_tables'] = $config_tables;

// --- Inclusion des Dépendances ---
require_once 'base.php'; // $conn
require_once 'crud_operations.php'; // Fonctions fetchData, addItem, editItem, deleteItem

// --- Initialisation des Variables ---
$message = '';
$message_type = 'info';
$table_key = $_GET['table'] ?? null;
$config = null;
$data_list = [];
$columns_to_display = [];
$columns_for_form = [];
$can_add = false; // Sera défini par la config
$can_edit = false; // Sera défini par la config
$can_delete = false; // Sera défini par la config
$pk_name = 'id';
$section_icon = 'fa-table'; // Icône par défaut

// --- Validation de la Configuration & Traitement des Actions ---
if ($table_key && isset($config_tables[$table_key])) {
    $config = $config_tables[$table_key];
    $pk_name = $config['primary_key'];
    // Récupérer les permissions depuis la config, ou true par défaut si non spécifié
    $can_add = $config['can_add'] ?? true;
    $can_edit = $config['can_edit'] ?? true;
    $can_delete = $config['can_delete'] ?? true;
    $section_icon = $config['icon'] ?? 'fa-table';

    // Filtrer les colonnes pour l'affichage et les formulaires
    $columns_to_display = array_filter($config['columns'], fn($col) => !($col['no_list'] ?? false));
    $columns_for_form = array_filter($config['columns'], fn($col) => !($col['readonly'] ?? false));

    // --- 1. TRAITEMENT DES ACTIONS POST (Ajout, Modification, Suppression) ---
    // Cette section est cruciale pour que les soumissions AJAX fonctionnent
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        $action = $_POST['action'];
        $id = $_POST[$pk_name] ?? null;
        $result = ['success' => false, 'message' => 'Action POST non reconnue ou non autorisée.'];

        try {
            // Vérifier si l'action est autorisée par la config avant d'appeler la fonction
            if ($action === 'add' && isset($_POST['addData']) && $can_add) {
                $result = addItem($conn, $config, $_POST);
            } elseif ($action === 'edit' && isset($_POST['editData']) && $id && $can_edit) {
                $result = editItem($conn, $config, $_POST, $id);
            } elseif ($action === 'delete' && isset($_POST['deleteData']) && $id && $can_delete) {
                $result = deleteItem($conn, $config, $id, $admin_id_session);
            }
        } catch (Exception $e) { // Capturer les exceptions générales des fonctions CRUD
            error_log("Erreur CRUD Exception: " . $e->getMessage());
            $result = ['success' => false, 'message' => "Une erreur inattendue est survenue lors de l'opération."];
        }

        // Mise à jour du message et du type pour affichage
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';

        // Affiner le type de message pour les cas non bloquants
        if (!$result['success']) {
            if (strpos($message, 'Aucune modification') !== false || strpos($message, 'données identiques') !== false) {
                $message_type = 'info';
            } elseif (strpos($message, 'Aucune donnée valide') !== false || strpos($message, 'non trouvé') !== false) {
                $message_type = 'warning';
            }
        }
        // Les données seront rechargées ci-dessous après le traitement POST
    } // Fin du traitement POST

    // --- 2. RÉCUPÉRATION DES DONNÉES POUR AFFICHAGE (TOUJOURS APRÈS LE POST) ---
    try {
        $fetchResult = fetchData($conn, $config);
        if ($fetchResult['success']) {
            $data_list = $fetchResult['data'];
            // Garder le message POST s'il existe et est important (pas juste 'info')
            if (empty($message) || $message_type === 'info') {
                // $message = $fetchResult['message']; // Message de fetch souvent peu utile
                // $message_type = 'info';
            }
        } else {
            // Afficher l'erreur de fetch seulement si pas d'erreur POST plus grave
            if (empty($message) || $message_type === 'info' || $message_type === 'success') {
                $message = $fetchResult['message'];
                $message_type = 'danger';
            }
            $data_list = [];
        }
    } catch (Exception $e) {
        error_log("Erreur FetchData Exception: " . $e->getMessage());
        if (empty($message) || $message_type === 'info' || $message_type === 'success') {
            $message = "Une erreur inattendue est survenue lors de la récupération des données.";
            $message_type = 'danger';
        }
        $data_list = [];
    }

} else { // Si $table_key est invalide ou non fourni
    if ($table_key) $message = "Configuration pour la table '<strong>" . htmlspecialchars($table_key) . "</strong>' non trouvée.";
    else $message = "Aucune section de gestion n'a été spécifiée.";
    $message_type = 'warning';
    $config = null;
    $data_list = [];
}

// --- 3. DÉBUT DU RENDU HTML (Fragment pour #content-area) ---
?>

<?php if ($config): // Afficher seulement si la configuration est valide ?>
    <div class="gestion-section gestion-<?php echo htmlspecialchars($table_key); ?>">

        <!-- En-tête de la section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
             <h2 class="mb-0"><i class="fas <?php echo $section_icon; ?> me-2"></i> Gestion des <?php echo htmlspecialchars($config['display_name']); ?></h2>
             <?php // Le bouton Ajouter s'affiche si $can_add est true (défini par la config) ?>
             <?php if ($can_add): ?>
             <button class="btn btn-primary" onclick="toggleAddForm()" aria-label="Ajouter un nouvel élément">
                 <i class="fas fa-plus"></i> Ajouter
             </button>
             <?php endif; ?>
        </div>

        <!-- Affichage des messages (Succès, Erreur, Info, Warning) -->
        <?php if ($message): ?>
            <?php
                $alert_icon = 'fa-info-circle'; // Icône par défaut
                if ($message_type === 'success') $alert_icon = 'fa-check-circle';
                elseif ($message_type === 'danger') $alert_icon = 'fa-exclamation-triangle';
                elseif ($message_type === 'warning') $alert_icon = 'fa-exclamation-circle';
            ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                 <i class="fas <?php echo $alert_icon; ?> me-2"></i>
                 <?php echo $message; // Le message est déjà protégé si nécessaire ?>
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'Ajout (caché par défaut, affiché si $can_add est true) -->
        <?php if ($can_add): ?>
        <div id="addFormContainer" class="card card-body mb-4 shadow-sm" style="display: none;">
             <h4><i class="fas fa-plus-circle text-primary me-1"></i> Ajouter : <?php echo htmlspecialchars($config['display_name']); ?></h4>
             <hr class="my-3">
            <form action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post" id="addForm">
                <input type="hidden" name="action" value="add">
                <div class="row g-3">
                    <?php foreach ($columns_for_form as $col_name => $col_config): ?>
                        <?php // Ne pas afficher le champ si c'est la clé primaire ou si 'no_add' est true
                              if ($col_name === $pk_name || ($col_config['no_add'] ?? false)) continue; ?>
                        <div class="col-md-6">
                            <label for="add_<?php echo $col_name; ?>" class="form-label">
                                <?php echo htmlspecialchars($col_config['label']); ?>
                                <?php echo ($col_config['required'] ?? false) ? '<span class="text-danger">*</span>' : ''; ?>
                            </label>
                            <?php
                                $input_type = $col_config['type'] ?? 'text';
                                $input_id = 'add_' . $col_name;
                                $input_name = $col_name;
                                $is_required = ($col_config['required'] ?? false);
                                $placeholder = htmlspecialchars($col_config['placeholder'] ?? '');
                                $default_value = htmlspecialchars($col_config['default'] ?? '');
                                $step = htmlspecialchars($col_config['step'] ?? 'any');
                            ?>
                            <?php if ($input_type === 'textarea'): ?>
                                <textarea class="form-control" id="<?= $input_id ?>" name="<?= $input_name ?>" rows="3" <?= $is_required ? 'required' : '' ?> placeholder="<?= $placeholder ?>"><?= $default_value ?></textarea>
                            <?php elseif ($input_type === 'select'): ?>
                                <select class="form-select" id="<?= $input_id ?>" name="<?= $input_name ?>" <?= $is_required ? 'required' : '' ?>>
                                    <?php
                                        $options = $col_config['options'] ?? [];
                                        $default_select_value = $col_config['default'] ?? null;
                                        $has_valid_default = isset($default_select_value) && isset($options[$default_select_value]);

                                        if (!$is_required && !$has_valid_default) {
                                            echo '<option value="">-- Choisir --</option>';
                                        } elseif ($is_required && !$has_valid_default && !empty($options)) {
                                             echo '<option value="" selected disabled>-- Choisir --</option>';
                                        } elseif (empty($options)) {
                                             echo '<option value="" selected disabled>-- Aucune option --</option>';
                                        }
                                    ?>
                                    <?php foreach ($options as $value => $text): ?>
                                        <option value="<?= htmlspecialchars($value) ?>" <?= ($has_valid_default && $default_select_value == $value) ? 'selected' : '' ?>><?= htmlspecialchars($text) ?></option>
                                    <?php endforeach; ?>
                                </select>
                             <?php elseif ($input_type === 'number'): ?>
                                <input type="number" class="form-control" id="<?= $input_id ?>" name="<?= $input_name ?>" value="<?= $default_value ?>" <?= $is_required ? 'required' : '' ?> step="<?= $step ?>" placeholder="<?= $placeholder ?>">
                            <?php else: // text, email, password, tel, date, datetime-local etc. ?>
                                <input type="<?= htmlspecialchars($input_type) ?>" class="form-control" id="<?= $input_id ?>" name="<?= $input_name ?>" value="<?= $default_value ?>" <?= $is_required ? 'required' : '' ?> placeholder="<?= $placeholder ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 pt-3 border-top d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2" onclick="toggleAddForm()"><i class="fas fa-times me-1"></i> Annuler</button>
                    <button type="submit" name="addData" value="1" class="btn btn-success"><i class="fas fa-check me-1"></i> Ajouter</button>
                </div>
            </form>
        </div>
        <?php endif; // Fin if ($can_add) ?>

        <!-- Tableau des données existantes -->
        <h4 class="mb-3">Liste existante</h4>
        <div class="table-responsive shadow-sm rounded border">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <?php foreach ($columns_to_display as $col_name => $col_config): ?>
                            <th><?php echo htmlspecialchars($col_config['label']); ?></th>
                        <?php endforeach; ?>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_list)): ?>
                        <tr><td colspan="<?= count($columns_to_display) + 1 ?>" class="text-center text-muted py-5">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>Aucun enregistrement trouvé.
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($data_list as $row): ?>
                            <tr>
                                <?php foreach ($columns_to_display as $col_name => $col_config): ?>
                                    <td>
                                        <?php
                                            // Logique de formatage des cellules (identique à la version précédente)
                                            $cell_value = $row[$col_name] ?? null;
                                            $col_type = $col_config['type'] ?? 'text';

                                            if ($cell_value === null && $col_type !== 'image') {
                                                echo '<em class="text-muted">N/A</em>';
                                            } elseif ($col_type === 'image') {
                                                $img_path = $cell_value ?: 'Images/profile/default-avatar.png';
                                                if (!file_exists($img_path)) $img_path = 'Images/profile/default-avatar.png';
                                                echo '<img src="' . htmlspecialchars($img_path) . '" alt="Image" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" loading="lazy">';
                                            } elseif ($col_type === 'select' && isset($col_config['options'][$cell_value])) {
                                                echo htmlspecialchars($col_config['options'][$cell_value]);
                                            } elseif ($col_type === 'number' && isset($col_config['step']) && $col_config['step'] == '0.01') {
                                                echo number_format((float)$cell_value, 2, ',', ' ') . ' €'; // Format prix
                                            } elseif ($col_type === 'datetime' || $col_type === 'date') {
                                                 try {
                                                     $format = ($col_type === 'datetime') ? 'd/m/Y H:i' : 'd/m/Y';
                                                     echo (new DateTime($cell_value))->format($format);
                                                 } catch (Exception $e) { echo htmlspecialchars($cell_value); } // Fallback si date invalide
                                            } elseif ($col_type === 'textarea' && is_string($cell_value) && strlen($cell_value) > 75) {
                                                echo '<span title="' . htmlspecialchars($cell_value) . '">' . htmlspecialchars(substr($cell_value, 0, 75)) . '...</span>'; // Troncature
                                            } else {
                                                echo htmlspecialchars((string)$cell_value); // Affichage standard
                                            }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class='action-icons text-center'>
                                    <?php // Afficher l'icône Modifier si $can_edit est true ?>
                                    <?php if ($can_edit): ?>
                                        <?php
                                            // Préparer les données pour le modal d'édition (sans mot de passe)
                                            $edit_data = array_filter($row, fn($key) => $key !== 'mot_de_passe', ARRAY_FILTER_USE_KEY);
                                        ?>
                                        <i class='fas fa-edit text-primary mx-1' style="cursor: pointer;" title="Modifier" aria-label="Modifier"
                                           onclick='showEditModal(<?php echo json_encode($edit_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>, "<?php echo $pk_name; ?>")'></i>
                                    <?php else: // Sinon, icône grisée ?>
                                         <i class='fas fa-edit text-muted mx-1' title="Modification désactivée"></i>
                                    <?php endif; ?>

                                    <?php // Afficher l'icône Supprimer si $can_delete est true ?>
                                    <?php if ($can_delete): ?>
                                        <?php
                                            $is_self_admin = ($table_key === 'administrateurs' && $admin_id_session !== null && $row[$pk_name] == $admin_id_session);
                                        ?>
                                        <?php if (!$is_self_admin): // Ne pas afficher si c'est l'admin connecté ?>
                                            <i class='fas fa-trash-alt text-danger mx-1' style="cursor: pointer;" title="Supprimer" aria-label="Supprimer"
                                               onclick='deleteItem(<?php echo json_encode($row[$pk_name]); ?>)'></i>
                                        <?php else: // Icône grisée pour auto-suppression ?>
                                            <i class='fas fa-trash-alt text-muted mx-1' title="Auto-suppression interdite"></i>
                                        <?php endif; ?>
                                     <?php else: // Sinon, icône grisée ?>
                                         <i class='fas fa-trash-alt text-muted mx-1' title="Suppression désactivée"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal pour l'Édition (généré si $can_edit est true) -->
        <?php if ($can_edit): ?>
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="editModalLabel"><i class="fas fa-edit text-primary me-1"></i> Modifier : <?php echo htmlspecialchars($config['display_name']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="<?php echo $pk_name; ?>" id="edit_<?php echo $pk_name; ?>"> <!-- ID sera rempli par JS -->

                            <div class="row g-3">
                                <?php foreach ($columns_for_form as $col_name => $col_config): ?>
                                     <?php // Ne pas afficher le champ si c'est la clé primaire ou si 'no_edit' est true
                                           if ($col_name === $pk_name || ($col_config['no_edit'] ?? false)) continue; ?>
                                    <div class="col-md-6">
                                        <label for="edit_<?php echo $col_name; ?>" class="form-label">
                                            <?php echo htmlspecialchars($col_config['label']); ?>
                                            <?php $is_required_edit = ($col_config['required'] ?? false) && !($col_config['edit_optional'] ?? false); ?>
                                            <?php echo $is_required_edit ? '<span class="text-danger">*</span>' : ''; ?>
                                            <?php if ($col_config['edit_optional'] ?? false): ?> <small class="text-muted">(Laisser vide pour ne pas changer)</small> <?php endif; ?>
                                        </label>
                                         <?php
                                            // Génération des champs du formulaire d'édition
                                            $input_type = $col_config['type'] ?? 'text';
                                            $input_id = 'edit_' . $col_name; // IMPORTANT: Préfixe 'edit_' pour le JS
                                            $input_name = $col_name; // Nom de colonne direct pour le POST
                                            $placeholder = htmlspecialchars($col_config['placeholder'] ?? '');
                                            $step = htmlspecialchars($col_config['step'] ?? 'any');
                                        ?>
                                        <?php if ($input_type === 'textarea'): ?>
                                            <textarea class="form-control" id="<?= $input_id ?>" name="<?= $input_name ?>" rows="3" <?= $is_required_edit ? 'required' : '' ?> placeholder="<?= $placeholder ?>"></textarea>
                                        <?php elseif ($input_type === 'select'): ?>
                                             <select class="form-select" id="<?= $input_id ?>" name="<?= $input_name ?>" <?= $is_required_edit ? 'required' : '' ?>>
                                                 <?php
                                                    $options = $col_config['options'] ?? [];
                                                    if (!$is_required_edit) {
                                                        echo '<option value="">-- (Aucun) --</option>';
                                                    } elseif (empty($options)) {
                                                        echo '<option value="" selected disabled>-- Options non définies --</option>';
                                                    }
                                                 ?>
                                                <?php foreach ($options as $value => $text): ?>
                                                    <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($text) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($input_type === 'number'): ?>
                                            <input type="number" class="form-control" id="<?= $input_id ?>" name="<?= $input_name ?>" <?= $is_required_edit ? 'required' : '' ?> step="<?= $step ?>" placeholder="<?= $placeholder ?>">
                                        <?php else: // text, email, password, tel, date, etc. ?>
                                            <input type="<?= htmlspecialchars($input_type) ?>" class="form-control" id="<?= $input_id ?>" name="<?= $input_name ?>" <?= $is_required_edit ? 'required' : '' ?> placeholder="<?= $placeholder ?>">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="modal-footer mt-3 pt-3 border-top">
                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Annuler</button>
                                 <button type="submit" name="editData" value="1" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; // Fin if($can_edit) ?>

        <!-- Formulaire caché pour la Suppression (généré si $can_delete est true) -->
        <?php if ($can_delete): ?>
        <form id="deleteForm" action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post" style="display: none;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="<?php echo $pk_name; ?>" id="delete_id"> <!-- L'ID sera mis ici par JS -->
            <input type="hidden" name="deleteData" value="1"> <!-- Indicateur pour le traitement POST -->
        </form>
        <?php endif; // Fin if ($can_delete) ?>

    </div> <!-- Fin .gestion-section -->

<?php else: // Si $config est null (erreur de config ou table non spécifiée) ?>
     <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> m-3" role="alert">
        <i class="fas <?php echo ($message_type === 'warning' || $message_type === 'danger') ? 'fa-exclamation-triangle' : 'fa-info-circle'; ?> me-2"></i>
        <?php echo $message; ?>
     </div>
<?php endif; // Fin de la condition if ($config) ?>

<?php // --- Fin du FRAGMENT HTML --- ?>
