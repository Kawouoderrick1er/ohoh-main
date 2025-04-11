<?php // c:\xampp\htdocs\ohoh-main\crud_operations.php

/**
 * Récupère les données d'une table selon la configuration.
 *
 * @param PDO $conn L'objet de connexion PDO.
 * @param array $config La configuration de la table (issue de $config_tables).
 * @return array ['success' => bool, 'data' => array|null, 'message' => string]
 */
function fetchData(PDO $conn, array $config): array
{
    $table_name = $config['table_name'];
    $pk_name = $config['primary_key'];
    $default_sort = $config['default_sort'] ?? "`$pk_name` DESC";
    $list_condition = $config['list_condition'] ?? '1'; // Condition par défaut si non fournie

    try {
        // TODO: Gérer les JOINs si nécessaire pour afficher des noms liés (ex: inscriptions)
        $sql = "SELECT * FROM `$table_name` WHERE $list_condition ORDER BY $default_sort";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $data, 'message' => 'Données récupérées.'];

    } catch (PDOException $e) {
        error_log("Erreur fetchData ($table_name): " . $e->getMessage());
        return ['success' => false, 'data' => null, 'message' => "Erreur lors de la récupération des données: " . $e->getMessage()];
    }
}

/**
 * Ajoute un nouvel enregistrement dans une table.
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
                // Ne pas insérer un mot de passe vide si le champ existe dans la config
                if (isset($config['columns']['mot_de_passe'])) continue;
            }

            $sql_cols[] = "`$col_name`";
            $sql_placeholders[] = ":$col_name";
            // Gérer les valeurs vides pour les champs non requis
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
        error_log("Erreur addItem ($table_name): " . $e->getMessage());
        return ['success' => false, 'message' => "Erreur base de données lors de l'ajout: " . $e->getMessage()];
    }
}

/**
 * Modifie un enregistrement existant.
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
                // Autres champs
                $sql_updates[] = "`$col_name` = :$col_name";
                $bind_params[":$col_name"] = ($value === '' && !($col_config['required'] ?? false) && !($col_config['edit_optional'] ?? false)) ? null : $value;
            }
        }
    }

    if (empty($sql_updates)) {
        return ['success' => true, 'message' => "Aucune modification détectée ou fournie."]; // Considéré comme succès car pas d'erreur
    }

    try {
        $bind_params[":$pk_name"] = $id; // Ajouter l'ID pour la clause WHERE
        $sql = "UPDATE `$table_name` SET " . implode(', ', $sql_updates) . " WHERE `$pk_name` = :$pk_name";
        $stmt = $conn->prepare($sql);
        $stmt->execute($bind_params);
        return ['success' => true, 'message' => htmlspecialchars($config['display_name']) . " modifié(e) avec succès."];

    } catch (PDOException $e) {
        error_log("Erreur editItem ($table_name, ID: $id): " . $e->getMessage());
        return ['success' => false, 'message' => "Erreur base de données lors de la modification: " . $e->getMessage()];
    }
}

/**
 * Supprime un enregistrement.
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
            throw new Exception("Auto-suppression interdite pour les administrateurs.");
        }

        $sql = "DELETE FROM `$table_name` WHERE `$pk_name` = :$pk_name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":$pk_name", $id); // Utiliser bindParam pour type hinting potentiel
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => htmlspecialchars($config['display_name']) . " supprimé(e) avec succès."];
        } else {
            return ['success' => false, 'message' => "L'élément n'a pas pu être trouvé ou supprimé (ID: $id)."];
        }

    } catch (PDOException $e) {
        error_log("Erreur deleteItem ($table_name, ID: $id): " . $e->getMessage());
        return ['success' => false, 'message' => "Erreur base de données lors de la suppression: " . $e->getMessage()];
    } catch (Exception $e) { // Capturer l'exception personnalisée
         error_log("Erreur logique deleteItem ($table_name, ID: $id): " . $e->getMessage());
         return ['success' => false, 'message' => "Erreur: " . $e->getMessage()];
    }
}

?>
