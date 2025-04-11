<?php // c:\xampp\htdocs\ohoh\gestion_generique.php
// Démarrer la session pour la sécurité et la gestion de l'état
session_start();

// --- Vérification de Sécurité Essentielle ---
// S'assurer qu'un administrateur est connecté (DÉCOMMENTER EN PRODUCTION)
/*
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403); // Forbidden
    die("Accès refusé. Veuillez vous connecter en tant qu'administrateur.");
}
*/

// --- Configuration des Tables Gérables ---
$config_tables = [
    // Gestion des Apprenants (basée sur la table 'utilisateurs')
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
    // Gestion des Formateurs
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
    // Gestion des Administrateurs
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
    // Gestion des Cours/Formations
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
    // Gestion des Leçons
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
    // Gestion des Messages de Contact
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
    // Gestion des Inscriptions
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
        // Pour afficher les noms: nécessite des JOIN dans la requête de lecture
        // ou une logique de lookup plus avancée non implémentée ici.
        // Exemple avec lookup (simplifié, nécessite adaptation de la requête SELECT):
        // 'utilisateur_nom' => ['label' => 'Apprenant', 'type' => 'text', 'readonly' => true, 'virtual' => true],
        // 'cours_titre' => ['label' => 'Cours', 'type' => 'text', 'readonly' => true, 'virtual' => true],
    ],
];

// --- Connexion à la Base de Données ---
require_once 'base.php'; // Utiliser le fichier base.php pour la connexion $conn

// --- Logique Générique ---
$message = '';
$message_type = 'info';
$table_key = $_GET['table'] ?? null;
$config = null;
$data_list = [];
$columns_to_display = [];
$columns_for_form = [];
$can_add = true; // Par défaut, on peut ajouter
$can_delete = true; // Par défaut, on peut supprimer

if ($table_key && isset($config_tables[$table_key])) {
    $config = $config_tables[$table_key];
    $table_name = $config['table_name'];
    $pk_name = $config['primary_key'];
    $can_add = $config['can_add'] ?? true; // Vérifier si l'ajout est autorisé
    $can_delete = $config['can_delete'] ?? true; // Vérifier si la suppression est autorisée

    // Filtrer les colonnes
    $columns_to_display = array_filter($config['columns'], function($col) {
        return !isset($col['no_list']) || !$col['no_list'];
    });
     $columns_for_form = array_filter($config['columns'], function($col) {
        return !isset($col['readonly']) || !$col['readonly'];
    });

    // --- Traitement des Actions POST ---
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        $action = $_POST['action'];
        $id = $_POST[$pk_name] ?? null;

        try {
            // Action: Ajouter
            if ($action === 'add' && isset($_POST['addData']) && $can_add) {
                // ... (Logique d'ajout existante - pas de changement majeur ici) ...
                 $sql_cols = [];
                $sql_placeholders = [];
                $bind_params = [];

                if (isset($config['insert_values']) && is_array($config['insert_values'])) {
                    foreach ($config['insert_values'] as $col => $val) {
                        $sql_cols[] = "`" . $col . "`";
                        $sql_placeholders[] = ":" . $col;
                        $bind_params[":" . $col] = $val;
                    }
                }

                foreach ($columns_for_form as $col_name => $col_config) {
                    if (isset($_POST[$col_name])) {
                        $value = $_POST[$col_name];
                        if ($col_name === 'mot_de_passe' && !empty($value)) {
                            if (isset($config['columns']['mot_de_passe'])) {
                                $value = password_hash($value, PASSWORD_DEFAULT);
                            } else { continue; }
                        } elseif ($col_name === 'mot_de_passe' && empty($value) && ($col_config['required'] ?? false)) {
                             continue;
                        }
                        if (!isset($bind_params[":" . $col_name])) {
                            $sql_cols[] = "`" . $col_name . "`";
                            $sql_placeholders[] = ":" . $col_name;
                            $bind_params[":" . $col_name] = ($value === '' && !($col_config['required'] ?? false)) ? null : $value;
                        }
                    } elseif (isset($col_config['default']) && !isset($bind_params[":" . $col_name])) {
                        // Utiliser la valeur par défaut si le champ n'est pas envoyé (ex: select non modifié)
                        $sql_cols[] = "`" . $col_name . "`";
                        $sql_placeholders[] = ":" . $col_name;
                        $bind_params[":" . $col_name] = $col_config['default'];
                    }
                }

                if (!empty($sql_cols)) {
                    $sql = "INSERT INTO `$table_name` (" . implode(', ', $sql_cols) . ") VALUES (" . implode(', ', $sql_placeholders) . ")";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($bind_params);
                    $message = htmlspecialchars($config['display_name']) . " ajouté(e) avec succès.";
                    $message_type = 'success';
                } else {
                     $message = "Aucune donnée valide à ajouter.";
                     $message_type = 'warning';
                }
            }
            // Action: Modifier
            elseif ($action === 'edit' && isset($_POST['editData']) && $id) {
                // ... (Logique de modification existante - pas de changement majeur ici) ...
                 $sql_updates = [];
                $bind_params = [];

                foreach ($columns_for_form as $col_name => $col_config) {
                    if ($col_name === $pk_name) continue;
                    if (isset($col_config['no_edit']) && $col_config['no_edit']) continue;

                    if (isset($_POST[$col_name])) {
                        $value = $_POST[$col_name];
                        if ($col_name === 'mot_de_passe') {
                            if (!empty($value)) {
                                if (isset($config['columns']['mot_de_passe'])) {
                                    $value = password_hash($value, PASSWORD_DEFAULT);
                                    $sql_updates[] = "`" . $col_name . "` = :" . $col_name;
                                    $bind_params[":" . $col_name] = $value;
                                }
                            }
                        } else {
                            $sql_updates[] = "`" . $col_name . "` = :" . $col_name;
                            $bind_params[":" . $col_name] = ($value === '' && !($col_config['required'] ?? false)) ? null : $value;
                        }
                    }
                }

                if (!empty($sql_updates)) {
                    $bind_params[":" . $pk_name] = $id;
                    $sql = "UPDATE `$table_name` SET " . implode(', ', $sql_updates) . " WHERE `$pk_name` = :$pk_name";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($bind_params);
                    $message = htmlspecialchars($config['display_name']) . " modifié(e) avec succès.";
                    $message_type = 'success';
                } else {
                     $message = "Aucune modification détectée ou fournie.";
                     $message_type = 'info';
                }
            }
            // Action: Supprimer
            elseif ($action === 'delete' && isset($_POST['deleteData']) && $id && $can_delete) {
                // ... (Logique de suppression existante - ajout vérification $can_delete) ...
                 if ($table_key === 'administrateurs' && isset($_SESSION['admin_id']) && $id == $_SESSION['admin_id']) {
                     throw new Exception("Vous ne pouvez pas supprimer votre propre compte administrateur.");
                }

                $sql = "DELETE FROM `$table_name` WHERE `$pk_name` = :$pk_name";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":$pk_name", $id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $message = htmlspecialchars($config['display_name']) . " supprimé(e) avec succès.";
                    $message_type = 'success';
                } else {
                    $message = "L'élément n'a pas pu être trouvé ou supprimé.";
                    $message_type = 'warning';
                }
            }
        } catch (PDOException $e) {
            $message = "Erreur base de données lors de l'opération : " . $e->getMessage();
            $message_type = 'error';
        } catch (Exception $e) {
             $message = "Erreur : " . $e->getMessage();
             $message_type = 'error';
        }
    }

    // --- Récupération des données pour affichage ---
    try {
        // TODO: Améliorer pour gérer les JOIN si nécessaire (ex: afficher noms pour inscriptions)
        $sql = "SELECT * FROM `$table_name`";
        if (!empty($config['list_condition'])) {
            $sql .= " WHERE " . $config['list_condition'];
        }
        // Ajouter le tri par défaut
        $default_sort = $config['default_sort'] ?? "`$pk_name` DESC";
        $sql .= " ORDER BY " . $default_sort;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Erreur lors de la récupération des données: " . $e->getMessage();
        $message_type = 'error';
        $data_list = [];
    }

} else {
    // Message si table non valide ou non spécifiée
    if ($table_key) {
        $message = "Erreur : La section de gestion '$table_key' n'est pas configurée.";
        $message_type = 'error';
    } else {
         // Normalement, on arrive ici seulement si l'URL est appelée sans ?table=
         // Le dashboard charge toujours une table ou l'overview.
         $message = "Section de gestion non spécifiée.";
         $message_type = 'warning';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des <?php echo $config ? htmlspecialchars($config['display_name']) : 'Entités'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styles copiés depuis la version précédente - OK */
        .message { margin-bottom: 1.5rem; padding: 1rem; border-radius: 0.3rem; border: 1px solid transparent; }
        .message.success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .message.error { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
        .message.warning { color: #664d03; background-color: #fff3cd; border-color: #ffecb5; }
        .message.info { color: #055160; background-color: #cff4fc; border-color: #b6effb; }
        .action-icons i { cursor: pointer; margin-right: 0.75rem; font-size: 1.1rem; transition: opacity 0.2s ease-in-out; }
        .action-icons i.fa-edit { color: #0d6efd; }
        .action-icons i.fa-trash-alt { color: #dc3545; }
        .action-icons i:hover { opacity: 0.7; }
        th { background-color: #e9ecef; }
        .form-label .text-danger { font-size: 0.9em; margin-left: 2px; }
        #addFormContainer { display: none; margin-top: 1rem; }
        .table-responsive { margin-top: 1.5rem; }
        input[readonly], textarea[readonly], select[readonly] { background-color: #e9ecef; opacity: 1; pointer-events: none; }
        select:disabled { background-color: #e9ecef; opacity: 1; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">

        <?php if ($config): // Afficher seulement si config valide ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                 <h2 class="mb-0">Gestion des <?php echo htmlspecialchars($config['display_name']); ?></h2>
                 <?php if ($can_add): // Afficher bouton seulement si autorisé ?>
                 <button class="btn btn-primary" onclick="toggleAddForm()">
                     <i class="fas fa-plus me-1"></i> Ajouter
                 </button>
                 <?php endif; ?>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo htmlspecialchars($message_type); ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'Ajout -->
            <?php if ($can_add): ?>
            <div id="addFormContainer" class="card card-body mb-4">
                 <h4>Ajouter un(e) nouveau/nouvelle <?php echo htmlspecialchars($config['display_name']); ?></h4>
                 <hr>
                <form action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post" id="addForm">
                    <input type="hidden" name="action" value="add">
                    <div class="row g-3">
                        <?php foreach ($columns_for_form as $col_name => $col_config): ?>
                            <?php if ($col_name === $pk_name) continue; ?>
                            <div class="col-md-6">
                                <label for="add_<?php echo $col_name; ?>" class="form-label">
                                    <?php echo htmlspecialchars($col_config['label']); ?>
                                    <?php echo (isset($col_config['required']) && $col_config['required']) ? '<span class="text-danger">*</span>' : ''; ?>
                                </label>
                                <?php $input_type = $col_config['type'] ?? 'text'; ?>

                                <?php if ($input_type === 'textarea'): ?>
                                    <textarea class="form-control" id="add_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" rows="3" <?php echo (isset($col_config['required']) && $col_config['required']) ? 'required' : ''; ?>><?php echo htmlspecialchars($col_config['default'] ?? ''); ?></textarea>
                                <?php elseif ($input_type === 'select'): ?>
                                    <select class="form-select" id="add_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" <?php echo (isset($col_config['required']) && $col_config['required']) ? 'required' : ''; ?>>
                                        <?php foreach ($col_config['options'] as $value => $text): ?>
                                            <option value="<?php echo htmlspecialchars($value); ?>" <?php echo (isset($col_config['default']) && $col_config['default'] == $value) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($text); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: // Inclut text, email, number, password, tel, date, datetime, etc. ?>
                                    <input type="<?php echo htmlspecialchars($input_type); ?>" class="form-control" id="add_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" value="<?php echo htmlspecialchars($col_config['default'] ?? ''); ?>" <?php echo (isset($col_config['required']) && $col_config['required']) ? 'required' : ''; ?>>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3">
                        <button type="submit" name="addData" class="btn btn-success">
                             <i class="fas fa-check me-1"></i> Ajouter
                        </button>
                         <button type="button" class="btn btn-secondary" onclick="toggleAddForm()">Annuler</button>
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
                            <tr>
                                <td colspan="<?php echo count($columns_to_display) + 1; ?>" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>Aucun enregistrement trouvé.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data_list as $row): ?>
                                <tr>
                                    <?php foreach ($columns_to_display as $col_name => $col_config): ?>
                                        <td>
                                            <?php
                                                $cell_value = $row[$col_name] ?? '';
                                                // Afficher le texte correspondant pour les selects
                                                if (($col_config['type'] ?? '') === 'select' && isset($col_config['options'][$cell_value])) {
                                                    echo htmlspecialchars($col_config['options'][$cell_value]);
                                                } elseif (strlen($cell_value) > 75 && ($col_config['type'] ?? 'text') === 'textarea') {
                                                    echo htmlspecialchars(substr($cell_value, 0, 75)) . '...';
                                                } else {
                                                    echo htmlspecialchars($cell_value);
                                                }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class='action-icons text-center'>
                                        <?php
                                            $edit_data = [];
                                            foreach ($config['columns'] as $cn => $cc) {
                                                 if ($cn !== 'mot_de_passe') {
                                                     $edit_data[$cn] = $row[$cn] ?? '';
                                                 } else {
                                                      $edit_data[$cn] = '';
                                                 }
                                            }
                                        ?>
                                        <i class='fas fa-edit' title="Modifier" onclick='showEditModal(<?php echo json_encode($edit_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>)'></i>
                                        <?php if ($can_delete): // Afficher bouton seulement si autorisé ?>
                                            <i class='fas fa-trash-alt' title="Supprimer" onclick='deleteItem(<?php echo json_encode($row[$pk_name]); ?>)'></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal pour la Modification -->
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
                                <input type="hidden" name="<?php echo $pk_name; ?>" id="edit_<?php echo $pk_name; ?>">

                                <div class="row g-3">
                                    <?php foreach ($columns_for_form as $col_name => $col_config): ?>
                                         <?php if ($col_name === $pk_name) continue; ?>
                                         <?php if (isset($col_config['no_edit']) && $col_config['no_edit']) continue; ?>
                                        <div class="col-md-6">
                                            <label for="edit_<?php echo $col_name; ?>" class="form-label">
                                                <?php echo htmlspecialchars($col_config['label']); ?>
                                                <?php $is_required = (isset($col_config['required']) && $col_config['required'] && !(isset($col_config['edit_optional']) && $col_config['edit_optional'])); ?>
                                                <?php echo $is_required ? '<span class="text-danger">*</span>' : ''; ?>
                                                <?php if (isset($col_config['edit_optional']) && $col_config['edit_optional']): ?>
                                                    <small class="text-muted">(Laisser vide pour ne pas changer)</small>
                                                <?php endif; ?>
                                            </label>
                                            <?php $input_type = $col_config['type'] ?? 'text'; ?>

                                            <?php if ($input_type === 'textarea'): ?>
                                                <textarea class="form-control" id="edit_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" rows="3" <?php echo $is_required ? 'required' : ''; ?>></textarea>
                                            <?php elseif ($input_type === 'select'): ?>
                                                 <select class="form-select" id="edit_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" <?php echo $is_required ? 'required' : ''; ?>>
                                                    <?php foreach ($col_config['options'] as $value => $text): ?>
                                                        <option value="<?php echo htmlspecialchars($value); ?>">
                                                            <?php echo htmlspecialchars($text); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <input type="<?php echo htmlspecialchars($input_type); ?>" class="form-control" id="edit_<?php echo $col_name; ?>" name="<?php echo $col_name; ?>" <?php echo $is_required ? 'required' : ''; ?>>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="modal-footer mt-3">
                                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                     <button type="submit" name="editData" class="btn btn-primary">
                                         <i class="fas fa-save me-1"></i> Enregistrer
                                     </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire caché pour la suppression -->
            <?php if ($can_delete): ?>
            <form id="deleteForm" action="gestion_generique.php?table=<?php echo htmlspecialchars($table_key); ?>" method="post" style="display: none;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="<?php echo $pk_name; ?>" id="delete_id">
                <input type="hidden" name="deleteData" value="1">
            </form>
            <?php endif; ?>

        <?php else: // Afficher message si config non valide ?>
             <?php if ($message): ?>
                <div class="message <?php echo htmlspecialchars($message_type); ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
             <p class="text-center text-muted mt-5">Sélectionnez une section dans le menu latéral pour commencer.</p>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var editModalInstance = null;
        var addFormContainer = null;

        document.addEventListener('DOMContentLoaded', function() {
             var modalElement = document.getElementById('editModal');
             if (modalElement) {
                editModalInstance = new bootstrap.Modal(modalElement);
             }
             addFormContainer = document.getElementById('addFormContainer');
        });

        function toggleAddForm() {
            if (addFormContainer) {
                addFormContainer.style.display = (addFormContainer.style.display === 'none' || addFormContainer.style.display === '') ? 'block' : 'none';
                if (addFormContainer.style.display === 'block') {
                    const firstInput = addFormContainer.querySelector('input:not([type=hidden]), textarea, select');
                    if(firstInput) firstInput.focus();
                }
            } else {
                 console.error("addFormContainer non trouvé.");
            }
        }

        function showEditModal(data) {
            const pk_name = <?php echo json_encode($pk_name ?? 'id'); ?>;
            const configColumns = <?php echo json_encode($config['columns'] ?? []); ?>; // Obtenir la config des colonnes

            for (const key in data) {
                const inputElement = document.getElementById('edit_' + key);
                if (inputElement) {
                    if (inputElement.type === 'password') {
                        inputElement.value = ''; // Toujours vider le mot de passe
                        if(configColumns[key] && configColumns[key].edit_optional) {
                             inputElement.required = false; // Rendre non requis si optionnel
                        }
                    } else if (inputElement.tagName === 'SELECT') {
                        // Gérer la sélection pour les listes déroulantes
                        inputElement.value = data[key];
                        // Si la valeur n'existe pas dans les options, elle ne sera pas sélectionnée
                        // (peut arriver si la donnée est invalide ou l'option a été supprimée)
                        if (inputElement.value !== data[key]) {
                            console.warn(`Valeur "${data[key]}" non trouvée pour le select "${key}"`);
                            // Optionnel: sélectionner une valeur par défaut ou la première option
                            // inputElement.selectedIndex = 0;
                        }
                    } else {
                        inputElement.value = data[key];
                    }
                } else if (key === pk_name) {
                     const pkInput = document.getElementById('edit_' + pk_name);
                     if(pkInput) pkInput.value = data[key];
                }
            }

            if (editModalInstance) {
                editModalInstance.show();
            } else {
                 console.error("editModalInstance non initialisée.");
                 alert("Erreur: Impossible d'ouvrir le formulaire.");
            }
        }

        function deleteItem(id) {
            // Vérifier si la suppression est autorisée (redondant car bouton caché, mais sécurité supplémentaire)
            <?php if (!$can_delete): ?>
                alert("La suppression n'est pas autorisée pour cette section.");
                return;
            <?php endif; ?>

            if (confirm("Êtes-vous sûr de vouloir supprimer cet enregistrement ?\nCette action est irréversible.")) {
                const deleteIdInput = document.getElementById('delete_id');
                const deleteForm = document.getElementById('deleteForm');
                if (deleteIdInput && deleteForm) {
                    deleteIdInput.value = id;
                    deleteForm.submit();
                } else {
                     console.error("Formulaire/champ de suppression introuvable.");
                     alert("Erreur: Impossible d'initier la suppression.");
                }
            }
        }
    </script>
</body>
</html>
