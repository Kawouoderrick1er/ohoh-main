<?php // c:\xampp\htdocs\ohoh-main\crud_operations.php (Minor adjustments might be needed based on testing, but likely OK)

// ... (fetchData function remains mostly for admin panel) ...
function fetchData(PDO $conn, array $config): array
{
    $table_name = $config['table_name'];
    $pk_name = $config['primary_key'];
    $default_sort = $config['default_sort'] ?? "`$pk_name` DESC";
    $list_condition = $config['list_condition'] ?? '1';

    // --- START: Potential JOIN logic for Admin Panel (Example for Inscriptions) ---
    $select_cols = "*";
    $joins = "";
    $table_key = array_search($config, $GLOBALS['config_tables'] ?? [], true);

    if ($table_key === 'inscriptions') {
        // Example: Fetch names for inscriptions list in admin
        $select_cols = "i.*, u.nom AS apprenant_nom, u.email AS apprenant_email, c.titre AS formation_titre";
        $joins = " AS i LEFT JOIN utilisateurs u ON i.utilisateur_id = u.id ";
        $joins .= " LEFT JOIN cours c ON i.cours_id = c.id ";
        // Adjust list_condition if needed, e.g., prepend table alias 'i.'
        if ($list_condition !== '1') {
            // Simple prefixing, might need more complex logic for ambiguous columns
             $list_condition = preg_replace('/(?<!\.)(`?\w+`?)\s*(=|<|>|LIKE|IN)/i', 'i.$1 $2', $list_condition);
        }
         // Adjust default_sort
         if (strpos($default_sort, '.') === false && strpos($default_sort, '(') === false) {
             $sort_parts = explode(' ', trim($default_sort));
             $sort_col = $sort_parts[0];
             $sort_dir = $sort_parts[1] ?? 'DESC';
             // Check if sort col exists in the main table 'i'
             if (isset($config['columns'][trim($sort_col, '`')]) || $sort_col === $pk_name) {
                $default_sort = "i." . $sort_col . " " . $sort_dir;
             }
             // Otherwise, assume it's from a joined table (less safe) or leave as is
         }

    } elseif ($table_key === 'formations') {
         // Example: Fetch trainer name for formations list in admin
         $select_cols = "c.*, u.nom AS formateur_nom";
         $joins = " AS c LEFT JOIN utilisateurs u ON c.formateur_id = u.id AND u.type_utilisateur = 'formateur' ";
         if ($list_condition !== '1') {
             $list_condition = preg_replace('/(?<!\.)(`?\w+`?)\s*(=|<|>|LIKE|IN)/i', 'c.$1 $2', $list_condition);
         }
         if (strpos($default_sort, '.') === false && strpos($default_sort, '(') === false) {
             $sort_parts = explode(' ', trim($default_sort));
             $sort_col = $sort_parts[0];
             $sort_dir = $sort_parts[1] ?? 'DESC';
             if (isset($config['columns'][trim($sort_col, '`')]) || $sort_col === $pk_name) {
                 $default_sort = "c." . $sort_col . " " . $sort_dir;
             }
         }
    }
    // --- END: Potential JOIN logic ---


    try {
        // Use $select_cols and $joins
        $sql = "SELECT $select_cols FROM `$table_name` $joins WHERE $list_condition ORDER BY $default_sort";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Post-process for display if needed (e.g., format price in admin list)
        if ($table_key === 'formations' || $table_key === 'inscriptions') {
            foreach ($data as &$row) {
                if (isset($row['prix'])) {
                    // You might want a raw value for editing, so maybe format later in the HTML
                    // $row['prix_formate'] = number_format((float)$row['prix'], 2, ',', ' ') . ' €';
                }
                 if (isset($row['montant_paye'])) {
                    // $row['montant_paye_formate'] = number_format((float)$row['montant_paye'], 2, ',', ' ') . ' €';
                 }
            }
            unset($row); // Break the reference
        }


        return ['success' => true, 'data' => $data, 'message' => 'Données récupérées.'];

    } catch (PDOException $e) {
        error_log("Erreur fetchData ($table_name): " . $e->getMessage() . " SQL: " . $sql); // Log SQL on error
        return ['success' => false, 'data' => null, 'message' => "Erreur lors de la récupération des données."]; // Hide detailed error from user
    }
}


/**
 * Ajoute un nouvel enregistrement dans une table.
 * Handles 'prix', 'logo_path' like other fields.
 *
 * @param PDO $conn L'objet de connexion PDO.
 * @param array $config La configuration de la table.
 * @param array $postData Les données du formulaire ($_POST).
 * @return array ['success' => bool, 'message' => string]
 */
function addItem(PDO $conn, array $config, array $postData): array
{
    $table_name = $config['table_name'];
    $columns_for_form = array_filter($config['columns'], fn($col) => !($col['readonly'] ?? false));

    $sql_cols = [];
    $sql_placeholders = [];
    $bind_params = [];

    // Ajouter les valeurs fixes (ex: type_utilisateur)
    if (isset($config['insert_values']) && is_array($config['insert_values'])) {
        foreach ($config['insert_values'] as $col => $val) {
            $sql_cols[] = "`$col`";
            $sql_placeholders[] = ":$col";
            $bind_params[":$col"] = $val;
        }
    }

    // Ajouter les valeurs du formulaire
    foreach ($columns_for_form as $col_name => $col_config) {
        if (isset($postData[$col_name]) && !isset($bind_params[":$col_name"])) {
            $value = $postData[$col_name];

            // Hasher le mot de passe si nécessaire
            if ($col_name === 'mot_de_passe' && !empty($value) && isset($config['columns']['mot_de_passe'])) {
                $value = password_hash($value, PASSWORD_DEFAULT);
            } elseif ($col_name === 'mot_de_passe') {
                if (isset($config['columns']['mot_de_passe'])) continue; // Ne pas insérer vide si champ existe
            }

            // Format price correctly (remove potential formatting)
            if ($col_name === 'prix' && isset($config['columns']['prix'])) {
                 $value = str_replace(',', '.', $value); // Ensure decimal point is dot
                 $value = filter_var($value, FILTER_VALIDATE_FLOAT);
                 if ($value === false) $value = 0.00; // Default if invalid
            }

            $sql_cols[] = "`$col_name`";
            $sql_placeholders[] = ":$col_name";
            // Gérer les valeurs vides pour les champs non requis ou NULL allowed
            $bind_params[":$col_name"] = ($value === '' && !($col_config['required'] ?? false)) ? null : $value;

        } elseif (isset($col_config['default']) && !isset($bind_params[":$col_name"])) {
            // Utiliser la valeur par défaut si le champ n'est pas dans POST
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
        error_log("Erreur addItem ($table_name): " . $e->getMessage() . " PARAMS: " . json_encode($bind_params));
        // Check for duplicate entry specifically
        if ($e->getCode() == 23000) { // Integrity constraint violation
             return ['success' => false, 'message' => "Erreur lors de l'ajout: Une valeur existe déjà (ex: email ou identifiant unique)."];
        }
        return ['success' => false, 'message' => "Erreur base de données lors de l'ajout."]; // Generic error for user
    }
}

/**
 * Modifie un enregistrement existant.
 * Handles 'prix', 'logo_path' like other fields.
 *
 * @param PDO $conn L'objet de connexion PDO.
 * @param array $config La configuration de la table.
 * @param array $postData Les données du formulaire ($_POST).
 * @param mixed $id L'ID de l'enregistrement à modifier.
 * @return array ['success' => bool, 'message' => string]
 */
function editItem(PDO $conn, array $config, array $postData, $id): array
{
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

            // Gérer le mot de passe (MAJ seulement si non vide)
            if ($col_name === 'mot_de_passe' && isset($config['columns']['mot_de_passe'])) {
                if (!empty($value)) {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                    $sql_updates[] = "`$col_name` = :$col_name";
                    $bind_params[":$col_name"] = $value;
                }
                // Si vide et 'edit_optional', on ne fait rien
            } else {
                // Format price correctly
                if ($col_name === 'prix' && isset($config['columns']['prix'])) {
                    $value = str_replace(',', '.', $value);
                    $value = filter_var($value, FILTER_VALIDATE_FLOAT);
                     if ($value === false) $value = 0.00; // Default if invalid
                }

                // Autres champs
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
        return ['success' => false, 'message' => "Aucune donnée valide fournie pour la modification."]; // Changed from success to false/warning
    }

    try {
        $bind_params[":$pk_name"] = $id; // Ajouter l'ID pour la clause WHERE
        $sql = "UPDATE `$table_name` SET " . implode(', ', $sql_updates) . " WHERE `$pk_name` = :$pk_name";
        $stmt = $conn->prepare($sql);
        $stmt->execute($bind_params);

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => htmlspecialchars($config['display_name']) . " modifié(e) avec succès."];
        } else {
             // Possibility: data submitted was the same as existing data
             // Let's check if the record exists at all
             $checkSql = "SELECT COUNT(*) FROM `$table_name` WHERE `$pk_name` = :id";
             $checkStmt = $conn->prepare($checkSql);
             $checkStmt->execute([':id' => $id]);
             if ($checkStmt->fetchColumn() > 0) {
                 return ['success' => true, 'message' => "Aucune modification nécessaire (données identiques)."]; // Changed to success/info
             } else {
                 return ['success' => false, 'message' => "L'élément à modifier n'a pas été trouvé (ID: $id)."];
             }
        }

    } catch (PDOException $e) {
        error_log("Erreur editItem ($table_name, ID: $id): " . $e->getMessage() . " PARAMS: " . json_encode($bind_params));
         if ($e->getCode() == 23000) {
             return ['success' => false, 'message' => "Erreur lors de la modification: Une valeur existe déjà (ex: email ou identifiant unique)."];
        }
        return ['success' => false, 'message' => "Erreur base de données lors de la modification."]; // Generic error for user
    }
}


/**
 * Supprime un enregistrement.
 * Includes check for admin self-delete.
 *
 * @param PDO $conn L'objet de connexion PDO.
 * @param array $config La configuration de la table.
 * @param mixed $id L'ID de l'enregistrement à supprimer.
 * @param mixed $admin_id L'ID de l'admin connecté (pour éviter l'auto-suppression).
 * @return array ['success' => bool, 'message' => string]
 */
function deleteItem(PDO $conn, array $config, $id, $admin_id = null): array
{
    if ($id === null) {
        return ['success' => false, 'message' => "ID manquant pour la suppression."];
    }

    $table_name = $config['table_name'];
    $pk_name = $config['primary_key'];
    $table_key = array_search($config, $GLOBALS['config_tables'] ?? [], true); // Retrouver la clé de config

    try {
        // Vérification anti-auto-suppression pour les admins
        if ($table_key === 'administrateurs' && $admin_id !== null && $id == $admin_id) {
            // throw new Exception("Auto-suppression interdite pour les administrateurs."); // Throwing exception stops execution
             return ['success' => false, 'message' => "Erreur: Auto-suppression interdite pour les administrateurs."]; // Return error message instead
        }

        // TODO: Add cascading delete logic or checks for related records (e.g., cannot delete formation if inscriptions exist?)
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
        $stmt->bindParam(":$pk_name", $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => htmlspecialchars($config['display_name']) . " supprimé(e) avec succès."];
        } else {
            return ['success' => false, 'message' => "L'élément n'a pas pu être trouvé ou supprimé (ID: $id)."];
        }

    } catch (PDOException $e) {
        error_log("Erreur deleteItem ($table_name, ID: $id): " . $e->getMessage());
         // Check for foreign key constraint errors
         if (strpos($e->getMessage(), 'FOREIGN KEY constraint fails') !== false) {
             return ['success' => false, 'message' => "Impossible de supprimer : cet élément est lié à d'autres enregistrements (ex: inscriptions, etc.)."];
         }
        return ['success' => false, 'message' => "Erreur base de données lors de la suppression."]; // Generic error
    } catch (Exception $e) { // Capturer l'exception personnalisée (si utilisée)
         error_log("Erreur logique deleteItem ($table_name, ID: $id): " . $e->getMessage());
         return ['success' => false, 'message' => "Erreur: " . $e->getMessage()];
    }
}

?>
