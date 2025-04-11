<?php // c:\xampp\htdocs\ohoh-main\gestion_generique.php (Commentaires visibles retirés)
session_start();

// --- Sécurité ---
// !! IMPORTANT: Décommentez cette section en production !!
/*
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403); // Forbidden
    echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle me-2"></i><strong>Accès refusé.</strong> Veuillez vous reconnecter.</div>';
    exit();
}
*/

// --- Configuration des Tables ---
$config_tables = [
    'apprenants' => [
        'table_name' => 'utilisateurs', 'display_name' => 'Apprenants', 'primary_key' => 'id',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'telephone' => ['label' => 'Téléphone', 'type' => 'tel'],
            'adresse' => ['label' => 'Adresse', 'type' => 'textarea'],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'etudiant'", 'insert_values' => ['type_utilisateur' => 'etudiant'], 'default_sort' => 'date_inscription DESC'
    ],
    'formateurs' => [
        'table_name' => 'utilisateurs', 'display_name' => 'Formateurs', 'primary_key' => 'id',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'telephone' => ['label' => 'Téléphone', 'type' => 'tel'],
            'adresse' => ['label' => 'Adresse', 'type' => 'textarea'],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'formateur'", 'insert_values' => ['type_utilisateur' => 'formateur'], 'default_sort' => 'date_inscription DESC'
    ],
    'administrateurs' => [
        'table_name' => 'utilisateurs', 'display_name' => 'Administrateurs', 'primary_key' => 'id',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'mot_de_passe' => ['label' => 'Mot de passe', 'type' => 'password', 'required' => true, 'edit_optional' => true, 'no_list' => true],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'administrateur'", 'insert_values' => ['type_utilisateur' => 'administrateur'], 'default_sort' => 'date_inscription DESC'
    ],
    'cours' => [
        'table_name' => 'cours', 'display_name' => 'Formations / Cours', 'primary_key' => 'id',
        'columns' => [
            'titre' => ['label' => 'Titre', 'type' => 'text', 'required' => true],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => true],
            'formateur_id' => ['label' => 'ID Formateur', 'type' => 'number'],
            'statut' => ['label' => 'Statut', 'type' => 'select', 'options' => ['publié' => 'Publié', 'brouillon' => 'Brouillon', 'archivé' => 'Archivé'], 'required' => true, 'default' => 'brouillon'],
            'date_creation' => ['label' => 'Créé le', 'type' => 'datetime', 'readonly' => true],
        ], 'default_sort' => 'date_creation DESC'
    ],
    'lecons' => [
        'table_name' => 'lecons', 'display_name' => 'Leçons', 'primary_key' => 'id',
        'columns' => [
            'titre' => ['label' => 'Titre', 'type' => 'text', 'required' => true],
            'contenu' => ['label' => 'Contenu', 'type' => 'textarea', 'required' => true],
            'cours_id' => ['label' => 'ID Cours Parent', 'type' => 'number', 'required' => true],
            'statut' => ['label' => 'Statut', 'type' => 'select', 'options' => ['publié' => 'Publié', 'brouillon' => 'Brouillon', 'archivé' => 'Archivé'], 'required' => true, 'default' => 'brouillon'],
            'date_creation' => ['label' => 'Créé le', 'type' => 'datetime', 'readonly' => true],
        ], 'default_sort' => 'date_creation DESC'
    ],
    'messages_contact' => [
        'table_name' => 'messages_contact', 'display_name' => 'Messages de Contact', 'primary_key' => 'id',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'readonly' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'readonly' => true],
            'sujet' => ['label' => 'Sujet', 'type' => 'text', 'readonly' => true],
            'message' => ['label' => 'Message', 'type' => 'textarea', 'readonly' => true, 'no_list' => true],
            'date_reception' => ['label' => 'Reçu le', 'type' => 'datetime', 'readonly' => true],
            'statut' => ['label' => 'Statut', 'type' => 'select', 'options' => ['nouveau' => 'Nouveau', 'lu' => 'Lu', 'répondu' => 'Répondu', 'archivé' => 'Archivé'], 'required' => true, 'default' => 'nouveau'],
        ], 'default_sort' => 'date_reception DESC', 'can_delete' => true, 'can_add' => false,
    ],
    'inscriptions' => [
        'table_name' => 'inscriptions', 'display_name' => 'Inscriptions', 'primary_key' => 'id',
        'columns' => [
            'utilisateur_id' => ['label' => 'ID Apprenant', 'type' => 'number', 'required' => true],
            'cours_id' => ['label' => 'ID Cours', 'type' => 'number', 'required' => true],
            'date_inscription' => ['label' => 'Date', 'type' => 'datetime', 'readonly' => true],
        ], 'default_sort' => 'date_inscription DESC', 'can_delete' => true,
    ],
];

// --- Inclusion des dépendances ---
require_once 'base.php'; // $conn
require_once 'crud_operations.php'; // Inclure les fonctions CRUD

// --- Initialisation des variables ---
$message = '';
$message_type = 'info';
$table_key = $_GET['table'] ?? null;
$config = null;
$data_list = [];
$columns_to_display = [];
$columns_for_form = [];
$can_add = true;
$can_delete = true;
$pk_name = 'id';

// --- Validation de la configuration ---
if ($table_key && isset($config_tables[$table_key])) {
    $config = $config_tables[$table_key];
    $pk_name = $config['primary_key'];
    $can_add = $config['can_add'] ?? true;
    $can_delete = $config['can_delete'] ?? true;

    // Filtrer les colonnes pour l'affichage et les formulaires
    $columns_to_display = array_filter($config['columns'], fn($col) => !($col['no_list'] ?? false));
    $columns_for_form = array_filter($config['columns'], fn($col) => !($col['readonly'] ?? false));

    // --- Traitement des Actions POST via les fonctions CRUD ---
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        $action = $_POST['action'];
        $id = $_POST[$pk_name] ?? null;
        $result = ['success' => false, 'message' => 'Action non reconnue.']; // Résultat par défaut

        if ($action === 'add' && isset($_POST['addData']) && $can_add) {
            $result = addItem($conn, $config, $_POST);
        } elseif ($action === 'edit' && isset($_POST['editData']) && $id) {
            $result = editItem($conn, $config, $_POST, $id);
        } elseif ($action === 'delete' && isset($_POST['deleteData']) && $id && $can_delete) {
            $admin_id_session = $_SESSION['admin_id'] ?? null; // Passer l'ID admin pour la vérification
            $result = deleteItem($conn, $config, $id, $admin_id_session);
        }

        // Mettre à jour le message et le type basé sur le résultat des fonctions CRUD
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger'; // Simplifié: succès ou danger
        if (!$result['success'] && strpos($result['message'], 'Aucune modification') !== false) {
            $message_type = 'info'; // Cas spécifique pour "aucune modification"
        }
         if (!$result['success'] && strpos($result['message'], 'Aucune donnée valide') !== false) {
            $message_type = 'warning'; // Cas spécifique pour "aucune donnée"
        }
         if (!$result['success'] && strpos($result['message'], 'trouvé ou supprimé') !== false) {
            $message_type = 'warning'; // Cas spécifique pour "non trouvé"
        }


    } // Fin du traitement POST

    // --- Récupération des données pour affichage via la fonction CRUD ---
    $fetchResult = fetchData($conn, $config);
    if ($fetchResult['success']) {
        $data_list = $fetchResult['data'];
    } else {
        // Si la récupération échoue après une action POST réussie, afficher quand même le message de succès
        if (empty($message) || $message_type !== 'success') {
             $message = $fetchResult['message'];
             $message_type = 'danger';
        }
        $data_list = [];
    }

} else {
    // Gérer le cas où la table n'est pas valide ou non spécifiée
    if ($table_key) $message = "Config '$table_key' non trouvée.";
    else $message = "Section non spécifiée.";
    $message_type = 'warning';
}

// --- Début du FRAGMENT HTML ---
?>

<?php if ($config): ?>
    <?php
        // Définir une icône par défaut ou spécifique à la table (logique inchangée)
        $section_icon = 'fa-table';
        if ($table_key === 'apprenants') $section_icon = 'fa-users';
        elseif ($table_key === 'formateurs') $section_icon = 'fa-chalkboard-teacher';
        elseif ($table_key === 'cours') $section_icon = 'fa-book-open';
        elseif ($table_key === 'lecons') $section_icon = 'fa-tasks';
        elseif ($table_key === 'inscriptions') $section_icon = 'fa-user-check';
        elseif ($table_key === 'messages_contact') $section_icon = 'fa-envelope-open-text';
        elseif ($table_key === 'administrateurs') $section_icon = 'fa-user-cog';
    ?>
    <div class="gestion-section gestion-<?php echo htmlspecialchars($table_key); ?>">

        <div class="d-flex justify-content-between align-items-center mb-4">
             <h2 class="mb-0"><i class="fas <?php echo $section_icon; ?>"></i> Gestion des <?php echo htmlspecialchars($config['display_name']); ?></h2>
             <?php if ($can_add): ?>
             <button class="btn btn-primary" onclick="toggleAddForm()">
                 <i class="fas fa-plus"></i> Ajouter
             </button>
             <?php endif; ?>
        </div>

        <?php if ($table_key === 'apprenants'): ?>
            <p class="intro-text">Gérez ici la liste des étudiants inscrits sur la plateforme.</p>
        <?php endif; ?>

        <?php if ($message): ?>
            <?php
                $alert_icon = 'fa-info-circle';
                if ($message_type === 'success') $alert_icon = 'fa-check-circle';
                elseif ($message_type === 'danger' || $message_type === 'error') $alert_icon = 'fa-exclamation-triangle';
                elseif ($message_type === 'warning') $alert_icon = 'fa-exclamation-circle';
            ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
                <i class="fas <?php echo $alert_icon; ?>"></i>
                <div><?php echo $message; ?></div>
            </div>
        <?php endif; ?>

        <?php if ($can_add): ?>
        <div id="addFormContainer" class="card card-body mb-4 shadow-sm" style="display: none;">
             <h4><i class="fas fa-plus-circle text-primary"></i> Ajouter <?php echo htmlspecialchars($config['display_name']); ?></h4> <hr class="my-3">
            <form action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post" id="addForm">
                <input type="hidden" name="action" value="add">
                <div class="row g-3">
                    <?php foreach ($columns_for_form as $col_name => $col_config): ?>
                        <?php if ($col_name === $pk_name) continue; ?>
                        <div class="col-md-6">
                            <label for="add_<?php echo $col_name; ?>" class="form-label">
                                <?php echo htmlspecialchars($col_config['label']); ?>
                                <?php echo ($col_config['required'] ?? false) ? '<span class="text-danger">*</span>' : ''; ?>
                            </label>
                            <?php $input_type = $col_config['type'] ?? 'text'; ?>
                            <?php if ($input_type === 'textarea'): ?>
                                <textarea class="form-control" id="add_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" rows="3" <?= ($col_config['required'] ?? false) ? 'required' : '' ?>><?php echo htmlspecialchars($col_config['default'] ?? ''); ?></textarea>
                            <?php elseif ($input_type === 'select'): ?>
                                <select class="form-select" id="add_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" <?= ($col_config['required'] ?? false) ? 'required' : '' ?>>
                                    <?php foreach ($col_config['options'] as $value => $text): ?>
                                        <option value="<?= htmlspecialchars($value) ?>" <?= (isset($col_config['default']) && $col_config['default'] == $value) ? 'selected' : '' ?>><?= htmlspecialchars($text) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="<?= htmlspecialchars($input_type) ?>" class="form-control" id="add_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" value="<?= htmlspecialchars($col_config['default'] ?? '') ?>" <?= ($col_config['required'] ?? false) ? 'required' : '' ?>>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <button type="submit" name="addData" class="btn btn-success"><i class="fas fa-check me-1"></i> Ajouter</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleAddForm()"><i class="fas fa-times"></i> Annuler</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <h4 class="mb-3">Liste existante</h4>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-striped table-hover mt-0 mb-0">
                <thead class="thead-light">
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
                            <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>Aucun enregistrement trouvé.
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($data_list as $row): ?>
                            <tr>
                                <?php foreach ($columns_to_display as $col_name => $col_config): ?>
                                    <td>
                                        <?php
                                            $cell_value = $row[$col_name] ?? '';
                                            if (($col_config['type'] ?? '') === 'select' && isset($col_config['options'][$cell_value])) {
                                                echo htmlspecialchars($col_config['options'][$cell_value]);
                                            } elseif (strlen($cell_value) > 75 && ($col_config['type'] ?? 'text') === 'textarea') {
                                                echo htmlspecialchars(substr($cell_value, 0, 75)) . '...';
                                            } else { echo htmlspecialchars($cell_value); }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class='action-icons text-center'>
                                    <?php
                                        $edit_data = array_filter($row, fn($key) => $key !== 'mot_de_passe', ARRAY_FILTER_USE_KEY);
                                    ?>
                                    <i class='fas fa-edit' title="Modifier" onclick='showEditModal(<?php echo json_encode($edit_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>, "<?php echo $pk_name; ?>")'></i>
                                    <?php if ($can_delete): ?>
                                        <i class='fas fa-trash-alt' title="Supprimer" onclick='deleteItem(<?php echo json_encode($row[$pk_name]); ?>)'></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="editModalLabel"><i class="fas fa-edit text-primary"></i> Modifier <?php echo htmlspecialchars($config['display_name']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="<?php echo $pk_name; ?>" id="edit_<?php echo $pk_name; ?>">

                            <div class="row g-3">
                                <?php foreach ($columns_for_form as $col_name => $col_config): ?>
                                     <?php if ($col_name === $pk_name || ($col_config['no_edit'] ?? false)) continue; ?>
                                    <div class="col-md-6">
                                        <label for="edit_<?php echo $col_name; ?>" class="form-label">
                                            <?php echo htmlspecialchars($col_config['label']); ?>
                                            <?php $is_required = ($col_config['required'] ?? false) && !($col_config['edit_optional'] ?? false); ?>
                                            <?php echo $is_required ? '<span class="text-danger">*</span>' : ''; ?>
                                            <?php if ($col_config['edit_optional'] ?? false): ?> <small class="text-muted">(Laisser vide pour ne pas changer)</small> <?php endif; ?>
                                        </label>
                                        <?php $input_type = $col_config['type'] ?? 'text'; ?>
                                        <?php if ($input_type === 'textarea'): ?>
                                            <textarea class="form-control" id="edit_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" rows="3" <?= $is_required ? 'required' : '' ?>></textarea>
                                        <?php elseif ($input_type === 'select'): ?>
                                             <select class="form-select" id="edit_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" <?= $is_required ? 'required' : '' ?>>
                                                <?php foreach ($col_config['options'] as $value => $text): ?>
                                                    <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($text) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="<?= htmlspecialchars($input_type) ?>" class="form-control" id="edit_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" <?= $is_required ? 'required' : '' ?>>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="modal-footer mt-3">
                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Annuler</button>
                                 <button type="submit" name="editData" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($can_delete): ?>
        <form id="deleteForm" action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post" style="display: none;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="<?php echo $pk_name; ?>" id="delete_id">
            <input type="hidden" name="deleteData" value="1">
        </form>
        <?php endif; ?>

    </div>

<?php else: ?>
     <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
        <i class="fas <?php echo ($message_type === 'warning' || $message_type === 'danger' || $message_type === 'error') ? 'fa-exclamation-triangle' : 'fa-info-circle'; ?>"></i>
        <div><?php echo $message; ?></div>
     </div>
<?php endif; ?>

<?php // --- Fin du FRAGMENT HTML --- ?>
