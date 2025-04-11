<?php // c:\xampp\htdocs\ohoh-main\gestion_generique.php (MODIFIÉ POUR AJAX)
session_start();

// --- Sécurité ---
// if (!isset($_SESSION['admin_id'])) {
//     http_response_code(403);
//     echo '<div class="alert alert-danger" role="alert"><strong>Accès refusé.</strong></div>';
//     exit();
// }

// --- Configuration des Tables ---
$config_tables = [
    'apprenants' => [
        'table_name' => 'utilisateurs',
        'display_name' => 'Apprenants',
        'primary_key' => 'id',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'telephone' => ['label' => 'Téléphone', 'type' => 'tel', 'required' => false],
            'adresse' => ['label' => 'Adresse', 'type' => 'textarea', 'required' => false],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'etudiant'",
        'insert_values' => ['type_utilisateur' => 'etudiant'],
        'default_sort' => 'date_inscription DESC'
    ],
    // ... (vos autres configurations: formateurs, cours, etc.) ...
     'formateurs' => [
        'table_name' => 'utilisateurs',
        'display_name' => 'Formateurs',
        'primary_key' => 'id',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'telephone' => ['label' => 'Téléphone', 'type' => 'tel', 'required' => false],
            'adresse' => ['label' => 'Adresse', 'type' => 'textarea', 'required' => false],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'formateur'",
        'insert_values' => ['type_utilisateur' => 'formateur'],
        'default_sort' => 'date_inscription DESC'
    ],
    'administrateurs' => [
        'table_name' => 'utilisateurs',
        'display_name' => 'Administrateurs',
        'primary_key' => 'id',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'mot_de_passe' => ['label' => 'Mot de passe', 'type' => 'password', 'required' => true, 'edit_optional' => true, 'no_list' => true],
            'date_inscription' => ['label' => 'Inscrit le', 'type' => 'datetime', 'readonly' => true],
        ],
        'list_condition' => "type_utilisateur = 'administrateur'",
        'insert_values' => ['type_utilisateur' => 'administrateur'],
        'default_sort' => 'date_inscription DESC'
    ],
    'cours' => [
        'table_name' => 'cours',
        'display_name' => 'Formations / Cours',
        'primary_key' => 'id',
        'columns' => [
            'titre' => ['label' => 'Titre', 'type' => 'text', 'required' => true],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => true],
            'formateur_id' => ['label' => 'ID Formateur', 'type' => 'number', 'required' => false], // Amélioration: Dropdown
            'statut' => [ // Ajout du statut
                'label' => 'Statut',
                'type' => 'select',
                'options' => ['publié' => 'Publié', 'brouillon' => 'Brouillon', 'archivé' => 'Archivé'],
                'required' => true,
                'default' => 'brouillon'
            ],
            'date_creation' => ['label' => 'Créé le', 'type' => 'datetime', 'readonly' => true],
        ],
        'default_sort' => 'date_creation DESC'
    ],
    'lecons' => [
        'table_name' => 'lecons',
        'display_name' => 'Leçons',
        'primary_key' => 'id',
        'columns' => [
            'titre' => ['label' => 'Titre', 'type' => 'text', 'required' => true],
            'contenu' => ['label' => 'Contenu', 'type' => 'textarea', 'required' => true],
            'cours_id' => ['label' => 'ID Cours Parent', 'type' => 'number', 'required' => true], // Amélioration: Dropdown
             'statut' => [ // Ajout du statut
                'label' => 'Statut',
                'type' => 'select',
                'options' => ['publié' => 'Publié', 'brouillon' => 'Brouillon', 'archivé' => 'Archivé'],
                'required' => true,
                'default' => 'brouillon'
            ],
            'date_creation' => ['label' => 'Créé le', 'type' => 'datetime', 'readonly' => true],
        ],
        'default_sort' => 'date_creation DESC'
    ],
    'messages_contact' => [
        'table_name' => 'messages_contact',
        'display_name' => 'Messages de Contact',
        'primary_key' => 'id',
        'columns' => [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'readonly' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'readonly' => true],
            'sujet' => ['label' => 'Sujet', 'type' => 'text', 'readonly' => true],
            'message' => ['label' => 'Message', 'type' => 'textarea', 'readonly' => true, 'no_list' => true], // Ne pas lister le message complet
            'date_reception' => ['label' => 'Reçu le', 'type' => 'datetime', 'readonly' => true],
            'statut' => [
                'label' => 'Statut',
                'type' => 'select',
                'options' => ['nouveau' => 'Nouveau', 'lu' => 'Lu', 'répondu' => 'Répondu', 'archivé' => 'Archivé'],
                'required' => true,
                'default' => 'nouveau'
            ],
        ],
        'default_sort' => 'date_reception DESC',
        'can_delete' => true, // Permettre la suppression
        'can_add' => false, // Ne pas permettre l'ajout manuel via cette interface
    ],
    'inscriptions' => [
        'table_name' => 'inscriptions',
        'display_name' => 'Inscriptions aux Cours',
        'primary_key' => 'id',
        'columns' => [
            'utilisateur_id' => ['label' => 'ID Apprenant', 'type' => 'number', 'required' => true], // Amélioration: Dropdown/Lookup
            'cours_id' => ['label' => 'ID Cours', 'type' => 'number', 'required' => true], // Amélioration: Dropdown/Lookup
            'date_inscription' => ['label' => 'Date', 'type' => 'datetime', 'readonly' => true],
        ],
        'default_sort' => 'date_inscription DESC',
        'can_delete' => true, // Permettre la suppression
    ],
];

// --- Connexion DB ---
require_once 'base.php'; // $conn

// --- Logique Générique (Traitement POST, Récupération Données) ---
$message = '';
$message_type = 'info';
$table_key = $_GET['table'] ?? null;
$config = null;
$data_list = [];
$columns_to_display = [];
$columns_for_form = [];
$can_add = true;
$can_delete = true;
$pk_name = 'id'; // Valeur par défaut

if ($table_key && isset($config_tables[$table_key])) {
    $config = $config_tables[$table_key];
    $table_name = $config['table_name'];
    $pk_name = $config['primary_key']; // Récupérer le nom réel de la PK
    $can_add = $config['can_add'] ?? true;
    $can_delete = $config['can_delete'] ?? true;

    $columns_to_display = array_filter($config['columns'], fn($col) => !($col['no_list'] ?? false));
    $columns_for_form = array_filter($config['columns'], fn($col) => !($col['readonly'] ?? false));

    // --- Traitement POST (Ajout, Modif, Suppr) ---
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        $action = $_POST['action'];
        $id = $_POST[$pk_name] ?? null;

        try {
            // Action: Ajouter
            if ($action === 'add' && isset($_POST['addData']) && $can_add) {
                 $sql_cols = []; $sql_placeholders = []; $bind_params = [];
                 // Ajouter valeurs fixes
                 if (isset($config['insert_values'])) {
                     foreach ($config['insert_values'] as $col => $val) {
                         $sql_cols[] = "`$col`"; $sql_placeholders[] = ":$col"; $bind_params[":$col"] = $val;
                     }
                 }
                 // Ajouter valeurs du formulaire
                 foreach ($columns_for_form as $col_name => $col_config) {
                     if (isset($_POST[$col_name]) && !isset($bind_params[":$col_name"])) {
                         $value = $_POST[$col_name];
                         if ($col_name === 'mot_de_passe' && !empty($value)) {
                             $value = password_hash($value, PASSWORD_DEFAULT);
                         } elseif ($col_name === 'mot_de_passe') continue; // Ne pas insérer mdp vide si requis

                         $sql_cols[] = "`$col_name`"; $sql_placeholders[] = ":$col_name";
                         $bind_params[":$col_name"] = ($value === '' && !($col_config['required'] ?? false)) ? null : $value;
                     } elseif (isset($col_config['default']) && !isset($bind_params[":$col_name"])) {
                         $sql_cols[] = "`$col_name`"; $sql_placeholders[] = ":$col_name"; $bind_params[":$col_name"] = $col_config['default'];
                     }
                 }
                 // Exécuter
                 if (!empty($sql_cols)) {
                     $sql = "INSERT INTO `$table_name` (" . implode(', ', $sql_cols) . ") VALUES (" . implode(', ', $sql_placeholders) . ")";
                     $stmt = $conn->prepare($sql); $stmt->execute($bind_params);
                     $message = htmlspecialchars($config['display_name']) . " ajouté(e)."; $message_type = 'success';
                 } else { $message = "Aucune donnée à ajouter."; $message_type = 'warning'; }
            }
            // Action: Modifier
            elseif ($action === 'edit' && isset($_POST['editData']) && $id) {
                 $sql_updates = []; $bind_params = [];
                 foreach ($columns_for_form as $col_name => $col_config) {
                     if ($col_name === $pk_name || ($col_config['no_edit'] ?? false)) continue;
                     if (isset($_POST[$col_name])) {
                         $value = $_POST[$col_name];
                         if ($col_name === 'mot_de_passe') {
                             if (!empty($value)) { // MAJ seulement si fourni
                                 $value = password_hash($value, PASSWORD_DEFAULT);
                                 $sql_updates[] = "`$col_name` = :$col_name"; $bind_params[":$col_name"] = $value;
                             }
                         } else {
                             $sql_updates[] = "`$col_name` = :$col_name";
                             $bind_params[":$col_name"] = ($value === '' && !($col_config['required'] ?? false)) ? null : $value;
                         }
                     }
                 }
                 // Exécuter
                 if (!empty($sql_updates)) {
                     $bind_params[":$pk_name"] = $id;
                     $sql = "UPDATE `$table_name` SET " . implode(', ', $sql_updates) . " WHERE `$pk_name` = :$pk_name";
                     $stmt = $conn->prepare($sql); $stmt->execute($bind_params);
                     $message = htmlspecialchars($config['display_name']) . " modifié(e)."; $message_type = 'success';
                 } else { $message = "Aucune modification."; $message_type = 'info'; }
            }
            // Action: Supprimer
            elseif ($action === 'delete' && isset($_POST['deleteData']) && $id && $can_delete) {
                 if ($table_key === 'administrateurs' && $id == $_SESSION['admin_id']) {
                     throw new Exception("Auto-suppression interdite.");
                 }
                 $sql = "DELETE FROM `$table_name` WHERE `$pk_name` = :$pk_name";
                 $stmt = $conn->prepare($sql); $stmt->bindParam(":$pk_name", $id, PDO::PARAM_INT); $stmt->execute();
                 if ($stmt->rowCount() > 0) {
                     $message = htmlspecialchars($config['display_name']) . " supprimé(e)."; $message_type = 'success';
                 } else { $message = "Élément non trouvé/supprimé."; $message_type = 'warning'; }
            }
        } catch (PDOException | Exception $e) {
            $message = "Erreur: " . $e->getMessage(); $message_type = 'danger';
            error_log("Erreur CRUD gestion_generique ($table_key): " . $e->getMessage());
        }
    }

    // --- Récupération des données pour affichage ---
    try {
        $sql = "SELECT * FROM `$table_name`";
        if (!empty($config['list_condition'])) $sql .= " WHERE " . $config['list_condition'];
        $sql .= " ORDER BY " . ($config['default_sort'] ?? "`$pk_name` DESC");
        $stmt = $conn->prepare($sql); $stmt->execute();
        $data_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Erreur récupération données: " . $e->getMessage(); $message_type = 'danger';
        error_log("Erreur Fetch gestion_generique ($table_key): " . $e->getMessage());
        $data_list = [];
    }

} else {
    if ($table_key) $message = "Config '$table_key' non trouvée.";
    else $message = "Section non spécifiée.";
    $message_type = 'warning';
}

// --- Début du FRAGMENT HTML ---
// Pas de <!DOCTYPE>, <html>, <head>, <body> ici !
?>

<?php if ($config): ?>
    <div class="gestion-section gestion-<?php echo htmlspecialchars($table_key); ?>"> {/* Ajout classe dynamique */}

        <div class="d-flex justify-content-between align-items-center mb-3">
             <h2 class="mb-0">Gestion des <?php echo htmlspecialchars($config['display_name']); ?></h2>
             <?php if ($can_add): ?>
             <button class="btn btn-primary" onclick="toggleAddForm()"> {/* Appel fonction globale */}
                 <i class="fas fa-plus me-1"></i> Ajouter
             </button>
             <?php endif; ?>
        </div>

        <?php if ($table_key === 'apprenants'): ?>
            <p class="intro-text">Gérez ici la liste des étudiants inscrits sur la plateforme.</p>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'Ajout -->
        <?php if ($can_add): ?>
        <div id="addFormContainer" class="card card-body mb-4" style="display: none;">
             <h4>Ajouter <?php echo htmlspecialchars($config['display_name']); ?></h4> <hr>
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
                <div class="mt-3">
                    <button type="submit" name="addData" class="btn btn-success"><i class="fas fa-check me-1"></i> Ajouter</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleAddForm()">Annuler</button> {/* Appel fonction globale */}
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Tableau des Données -->
        <h4>Liste existante</h4>
        <div class="table-responsive shadow-sm">
            <table class="table table-striped table-hover table-bordered mt-2">
                <thead class="table-light">
                    <tr>
                        <?php foreach ($columns_to_display as $col_name => $col_config): ?>
                            <th><?php echo htmlspecialchars($col_config['label']); ?></th>
                        <?php endforeach; ?>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_list)): ?>
                        <tr><td colspan="<?= count($columns_to_display) + 1 ?>" class="text-center text-muted py-4"><i class="fas fa-info-circle me-2"></i>Aucun enregistrement.</td></tr>
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
                                        // Préparer les données pour JS (exclure mdp)
                                        $edit_data = array_filter($row, fn($key) => $key !== 'mot_de_passe', ARRAY_FILTER_USE_KEY);
                                    ?>
                                    <i class='fas fa-edit' title="Modifier" onclick='showEditModal(<?php echo json_encode($edit_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>, "<?php echo $pk_name; ?>")'></i> {/* Appel fonction globale + PK Name */}
                                    <?php if ($can_delete): ?>
                                        <i class='fas fa-trash-alt' title="Supprimer" onclick='deleteItem(<?php echo json_encode($row[$pk_name]); ?>)'></i> {/* Appel fonction globale */}
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal pour la Modification (INCLUS DANS LE FRAGMENT) -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Modifier <?php echo htmlspecialchars($config['display_name']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="<?php echo $pk_name; ?>" id="edit_<?php echo $pk_name; ?>"> {/* ID dynamique basé sur PK */}

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
                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                 <button type="submit" name="editData" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire caché pour la suppression (INCLUS DANS LE FRAGMENT) -->
        <?php if ($can_delete): ?>
        <form id="deleteForm" action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post" style="display: none;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="<?php echo $pk_name; ?>" id="delete_id"> {/* ID dynamique basé sur PK */}
            <input type="hidden" name="deleteData" value="1">
        </form>
        <?php endif; ?>

    </div> {/* Fin .gestion-section */}

<?php else: // Afficher message si config non valide ?>
     <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
         <?php echo $message; ?>
     </div>
<?php endif; ?>

<?php // --- Fin du FRAGMENT HTML ---
// PAS de </body> ou </html> ici
// PAS de <script> pour toggleAddForm, showEditModal, deleteItem ici
?>
