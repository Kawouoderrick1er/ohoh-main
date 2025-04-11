<?php // c:\xampp\htdocs\ohoh-main\crud_operations.php (REFACTORED & FIXED)

/**
 * Récupère les données d'une table avec la possibilité de faire des jointures (JOIN)
 * pour la vue admin.
 *
 * @param PDO $conn L'objet de connexion à la base de données.
 * @param array $config La configuration de la table.
 * @return array ['success' => bool, 'data' => array, 'message' => string]
 */
function fetchData(PDO $conn, array $config): array {
    $table_name = $config['table_name'];
    $pk_name = $config['primary_key'];
    $default_sort = $config['default_sort'] ?? "`$pk_name` DESC";
    $list_condition = $config['list_condition'] ?? '1';

    $select_cols = "*";
    $joins = "";
    $table_key = array_search($config, $GLOBALS['config_tables'] ?? [], true); // Get config key from global

    // --- Dynamic JOIN logic based on table_key ---
    if ($table_key === 'inscriptions') {
        $select_cols = "i.*, u.nom AS apprenant_nom, u.email AS apprenant_email, c.titre AS formation_titre";
        $joins = " AS i LEFT JOIN utilisateurs u ON i.utilisateur_id = u.id LEFT JOIN cours c ON i.cours_id = c.id";
        if ($list_condition !== '1') {
            // Simple prefixing, might need more complex logic for ambiguous columns
             $list_condition = preg_replace('/(?<!\.)(`?\w+`?)\s*(=|<|>|LIKE|IN)/i', 'i.$1 $2', $list_condition);
        }
    } elseif ($table_key === 'formations') {
        $select_cols = "c.*, u.nom AS formateur_nom";
        $joins = " AS c LEFT JOIN utilisateurs u ON c.formateur_id = u.id AND u.type_utilisateur = 'formateur'";
        if ($list_condition !== '1') {
             $list_condition = preg_replace('/(?<!\.)(`?\w+`?)\s*(=|<|>|LIKE|IN)/i', 'c.$1 $2', $list_condition);
        }
    }
     // Adjust default_sort
    if (strpos($default_sort, '.') === false && strpos($default_sort, '(') === false) {
        $sort_parts = explode(' ', trim($default_sort));
        $sort_col = $sort_parts[0];
        $sort_dir = $sort_parts[1] ?? 'DESC';
         if ($table_key === 'inscriptions') {
             if (isset($config['columns'][trim($sort_col, '`')]) || $sort_col === $pk_name) {
                $default_sort = "i." . $sort_col . " " . $sort_dir;
             }
         }elseif($table_key === 'formations'){
            if (isset($config['columns'][trim($sort_col, '`')]) || $sort_col === $pk_name) {
                 $default_sort = "c." . $sort_col . " " . $sort_dir;
             }
         }
    }

    try {
        $sql = "SELECT $select_cols FROM `$table_name` $joins WHERE $list_condition ORDER BY $default_sort";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $data, 'message' => 'Données récupérées.'];

    } catch (PDOException $e) {
        error_log("Erreur fetchData ($table_name): " . $e->getMessage() . " SQL: " . $sql); // Include SQL on error
        return ['success' => false, 'data' => null, 'message' => "Erreur lors de la récupération des données."];
    }
}

/**
 * Ajoute un nouvel enregistrement à une table.
 *
 * @param PDO $conn La connexion à la base de données.
 * @param array $config La configuration de la table.
 * @param array $postData Les données du formulaire.
 * @return array ['success' => bool, 'message' => string]
 */
function addItem(PDO $conn, array $config, array $postData): array {
    $table_name = $config['table_name'];
    $columns_for_form = array_filter($config['columns'], fn($col) => !($col['readonly'] ?? false));

    $sql_cols = [];
    $sql_placeholders = [];
    $bind_params = [];

    // Adding fixed values (e.g., type_utilisateur)
    if (isset($config['insert_values']) && is_array($config['insert_values'])) {
        foreach ($config['insert_values'] as $col => $val) {
            $sql_cols[] = "`$col`";
            $sql_placeholders[] = ":$col";
            $bind_params[":$col"] = $val;
        }
    }

    // Adding form values
    foreach ($columns_for_form as $col_name => $col_config) {
        if (isset($postData[$col_name]) && !isset($bind_params[":$col_name"])) {
            $value = $postData[$col_name];

            // Handle password hashing
            if ($col_name === 'mot_de_passe' && !empty($value) && isset($config['columns']['mot_de_passe'])) {
                $value = password_hash($value, PASSWORD_DEFAULT);
            } elseif ($col_name === 'mot_de_passe') {
                if (isset($config['columns']['mot_de_passe'])) continue; // Don't insert empty if field exists
            }

            // Format price correctly
            if ($col_name === 'prix' && isset($config['columns']['prix'])) {
                $value = str_replace(',', '.', $value);
                $value = filter_var($value, FILTER_VALIDATE_FLOAT);
                if ($value === false) $value = 0.00;
            }
            $sql_cols[] = "`$col_name`";
            $sql_placeholders[] = ":$col_name";
            $bind_params[":$col_name"] = ($value === '' && !($col_config['required'] ?? false)) ? null : $value; // Handle empty
        } elseif (isset($col_config['default']) && !isset($bind_params[":$col_name"])) {
            $sql_cols[] = "`$col_name`";
            $sql_placeholders[] = ":$col_name";
            $bind_params[":$col_name"] = $col_config['default'];
        }
    }

    if (empty($sql_cols)) {
        return ['success' => false, 'message' => "Aucune donnée valide à ajouter."];
    }

    try {
        $sql = "INSERT INTO `$table_name` (" . implode(', ', $sql_cols) . ") VALUES (" . implode(', ', $sql_placeholders) . ")";
        $stmt = $conn->prepare($sql);
        $stmt->execute($bind_params);
        return ['success' => true, 'message' => htmlspecialchars($config['display_name']) . " ajouté(e) avec succès."];

    } catch (PDOException $e) {
        error_log("Erreur addItem ($table_name): " . $e->getMessage() . " SQL: ". ($sql ?? 'N/A') . " PARAMS: " . json_encode($bind_params));
        if ($e->getCode() == 23000) { // Integrity constraint violation (like duplicate entry)
            return ['success' => false, 'message' => "Erreur lors de l'ajout: Une valeur existe déjà (ex: email ou identifiant unique)."];
        }
        return ['success' => false, 'message' => "Erreur base de données lors de l'ajout."];
    }
}

/**
 * Modifie un enregistrement existant.
 *
 * @param PDO $conn La connexion à la base de données.
 * @param array $config La configuration de la table.
 * @param array $postData Les données du formulaire.
 * @param mixed $id L'ID de l'enregistrement à modifier.
 * @return array ['success' => bool, 'message' => string]
 */
function editItem(PDO $conn, array $config, array $postData, $id): array {
    if ($id === null) {
        return ['success' => false, 'message' => "ID manquant pour la modification."];
    }

    $table_name = $config['table_name'];
    $pk_name = $config['primary_key'];
    $columns_for_form = array_filter($config['columns'], fn($col) => !($col['readonly'] ?? false));

    $sql_updates = [];
    $bind_params = [];

    foreach ($columns_for_form as $col_name => $col_config) {
        if ($col_name === $pk_name || ($col_config['no_edit'] ?? false)) continue;

        if (isset($postData[$col_name])) {
            $value = $postData[$col_name];

            // Handle password update (only if not empty)
            if ($col_name === 'mot_de_passe' && isset($config['columns']['mot_de_passe'])) {
                if (!empty($value)) {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                    $sql_updates[] = "`$col_name` = :$col_name";
                    $bind_params[":$col_name"] = $value;
                }
                // If empty and 'edit_optional', do nothing
            } else {
                // Format price correctly
                if ($col_name === 'prix' && isset($config['columns']['prix'])) {
                    $value = str_replace(',', '.', $value);
                    $value = filter_var($value, FILTER_VALIDATE_FLOAT);
                    if ($value === false) $value = 0.00; // Default if invalid
                }

                // Other fields
                $sql_updates[] = "`$col_name` = :$col_name";
                // Handle empty strings for non-required fields -> NULL
                $bind_params[":$col_name"] = ($value === '' && !($col_config['required'] ?? false) && !($col_config['edit_optional'] ?? false)) ? null : $value;
            }
        }
    }

    if (empty($sql_updates)) {
        // Check if only password was submitted but was empty (and optional)
        if (isset($postData['mot_de_passe']) && $postData['mot_de_passe'] === '' && ($config['columns']['mot_de_passe']['edit_optional'] ?? false)) {
             return ['success' => true, 'message' => "Aucune modification détectée ou fournie."];
        }
        return ['success' => false, 'message' => "Aucune donnée valide fournie pour la modification."];
    }

    try {
        $bind_params[":$pk_name"] = $id; // Add ID for WHERE clause
        $sql = "UPDATE `$table_name` SET " . implode(', ', $sql_updates) . " WHERE `$pk_name` = :$pk_name";
        $stmt = $conn->prepare($sql);
        $stmt->execute($bind_params);

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => htmlspecialchars($config['display_name']) . " modifié(e) avec succès."];
        } else {
             // Possibility: data submitted was the same as existing data, or record not found
             $checkSql = "SELECT COUNT(*) FROM `$table_name` WHERE `$pk_name` = :id";
             $checkStmt = $conn->prepare($checkSql);
             $checkStmt->execute([':id' => $id]);
             if ($checkStmt->fetchColumn() > 0) {
                 return ['success' => true, 'message' => "Aucune modification nécessaire (données identiques)."];
             } else {
                 return ['success' => false, 'message' => "L'élément à modifier n'a pas été trouvé (ID: $id)."];
             }
        }

    } catch (PDOException $e) {
        error_log("Erreur editItem ($table_name, ID: $id): " . $e->getMessage() . " SQL: ". ($sql ?? 'N/A') ." PARAMS: " . json_encode($bind_params));
         if ($e->getCode() == 23000) { // Integrity constraint violation
             return ['success' => false, 'message' => "Erreur lors de la modification: Une valeur existe déjà (ex: email ou identifiant unique)."];
        }
        return ['success' => false, 'message' => "Erreur base de données lors de la modification."];
    }
}

/**
 * Supprime un enregistrement de la base de données.
 *
 * @param PDO $conn La connexion à la base de données.
 * @param array $config La configuration de la table.
 * @param mixed $id L'ID de l'enregistrement à supprimer.
 * @param mixed|null $admin_id L'ID de l'admin connecté (pour éviter l'auto-suppression).
 * @return array ['success' => bool, 'message' => string]
 */ // <-- **** THIS IS THE MISSING CLOSING COMMENT ****
function deleteItem(PDO $conn, array $config, $id, $admin_id = null): array {
    if ($id === null) {
        return ['success' => false, 'message' => "ID manquant pour la suppression."];
    }

    $table_name = $config['table_name'];
    $pk_name = $config['primary_key'];
    $table_key = array_search($config, $GLOBALS['config_tables'] ?? [], true); // Get config key

    try {
        // Prevent admin self-deletion
        if ($table_key === 'administrateurs' && $admin_id !== null && $id == $admin_id) {
             return ['success' => false, 'message' => "Erreur: Auto-suppression interdite pour les administrateurs."];
        }

        // Optional: Add checks for related records before deleting (foreign key constraints)
        // Example check (conceptual):
        // if ($table_key === 'formations') {
        //     $checkSql = "SELECT COUNT(*) FROM inscriptions WHERE cours_id = :id";
        //     $checkStmt = $conn->prepare($checkSql);
        //     $checkStmt->execute([':id' => $id]);
        //     if ($checkStmt->fetchColumn() > 0) {
        //         return ['success' => false, 'message' => "Impossible de supprimer : des inscriptions existent pour cette formation."];
        //     }
        // }

        $sql = "DELETE FROM `$table_name` WHERE `$pk_name` = :$pk_name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":$pk_name", $id); // Use bindParam or pass in execute array
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => htmlspecialchars($config['display_name']) . " supprimé(e) avec succès."];
        } else {
            // Check if the item existed before attempting delete
            $checkSql = "SELECT COUNT(*) FROM `$table_name` WHERE `$pk_name` = :id";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([':id' => $id]);
            if ($checkStmt->fetchColumn() == 0) {
                 return ['success' => false, 'message' => "L'élément à supprimer n'a pas été trouvé (ID: $id)."];
            } else {
                 // Should not happen if rowCount was 0 but item exists, maybe DB issue?
                 return ['success' => false, 'message' => "L'élément n'a pas pu être supprimé (ID: $id). Raison inconnue."];
            }
        }

    } catch (PDOException $e) {
        error_log("Erreur deleteItem ($table_name, ID: $id): " . $e->getMessage() . " SQL: " . ($sql ?? 'N/A'));
         // Check for foreign key constraint errors (common codes vary by DB)
         if (strpos($e->getMessage(), 'FOREIGN KEY constraint fails') !== false || $e->getCode() == '23000') { // Adjust code if needed
             return ['success' => false, 'message' => "Impossible de supprimer : cet élément est lié à d'autres enregistrements (ex: inscriptions, etc.)."];
         }
        return ['success' => false, 'message' => "Erreur base de données lors de la suppression."];
    } catch (Exception $e) { // Catch other potential exceptions
         error_log("Erreur logique deleteItem ($table_name, ID: $id): " . $e->getMessage());
         return ['success' => false, 'message' => "Erreur: " . $e->getMessage()];
    }
}

?>
