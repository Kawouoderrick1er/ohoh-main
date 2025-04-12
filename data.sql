-- Créer la base de données
CREATE DATABASE formation_professionnelle;

-- Utiliser la base de données
USE formation_professionnelle;

-- Créer la table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(100) NOT NULL,
    telephone VARCHAR(15),
    adresse TEXT,
    type_utilisateur ENUM('etudiant', 'formateur', 'administrateur') NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Créer la table cours
CREATE TABLE cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    formateur_id INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formateur_id) REFERENCES utilisateurs(id)
);

-- Créer la table lecons
CREATE TABLE lecons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    contenu TEXT NOT NULL,
    cours_id INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cours_id) REFERENCES cours(id)
);

-- Créer la table inscriptions
CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    cours_id INT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (cours_id) REFERENCES cours(id)
);

-- Créer la table évaluations
CREATE TABLE evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    cours_id INT,
    note INT,
    commentaires TEXT,
    date_evaluation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (cours_id) REFERENCES cours(id)
);

-- Créer la table commentaires
CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    contenu TEXT,
    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    type_commentaire ENUM('cours', 'leçon'),
    reference_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (reference_id) REFERENCES lecons(id) ON DELETE CASCADE,
    FOREIGN KEY (reference_id) REFERENCES cours(id) ON DELETE CASCADE
);

-- Créer la table administrateurs
CREATE TABLE administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

-- Créer la table apprenants
CREATE TABLE apprenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(15),
    adresse TEXT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Créer la table formations
CREATE TABLE formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    video_url VARCHAR(255),
    pdf_url VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Modifier la table utilisateurs pour inclure le téléphone et l'adresse
ALTER TABLE utilisateurs 
ADD telephone VARCHAR(15) NULL DEFAULT NULL AFTER mot_de_passe,
ADD adresse TEXT AFTER telephone;

ALTER TABLE cours
ADD COLUMN statut ENUM ('publié', 'brouillon' , 'archive') DEFAULT 'brouillon';

ALTER TABLE lecons
ADD COLUMN statut ENUM ('publié', 'brouillon' , 'archive') DEFAULT 'brouillon';
-- Créer la table pour stocker les messages du formulaire de contact
CREATE TABLE messages_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date_reception TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('nouveau', 'lu', 'répondu', 'archivé') DEFAULT 'nouveau',
    ip_adresse VARCHAR(45) NULL, -- Optionnel: stocker l'IP pour référence
    user_agent TEXT NULL       -- Optionnel: stocker le navigateur
);


-- Rename the 'cours' table to 'formations' (Optional but recommended for clarity)
-- RENAME TABLE cours TO formations;
-- If you renamed the table, update ALL references in the PHP code from 'cours' to 'formations'.
-- For this example, I will keep the table name 'cours' but change display names.

-- Add 'prix' and 'logo_path' columns to the 'cours' table
ALTER TABLE `cours`
ADD COLUMN `prix` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 AFTER `formateur_id`,
ADD COLUMN `logo_path` VARCHAR(255) NULL DEFAULT NULL AFTER `prix`;

-- Modify the 'inscriptions' table to store payment info and allow NULL user_id
-- Ensure utilisateur_id can be NULL if the user isn't registered/logged in
ALTER TABLE `inscriptions`
MODIFY COLUMN `utilisateur_id` INT(11) NULL DEFAULT NULL,
ADD COLUMN `email_client` VARCHAR(255) NULL DEFAULT NULL AFTER `cours_id`, -- Store email used for payment
ADD COLUMN `montant_paye` DECIMAL(10, 2) NULL DEFAULT NULL AFTER `email_client`,
ADD COLUMN `moyen_paiement` VARCHAR(50) NULL DEFAULT NULL AFTER `montant_paye`, -- e.g., 'MTN', 'Orange'
ADD COLUMN `numero_telephone_paiement` VARCHAR(20) NULL DEFAULT NULL AFTER `moyen_paiement`,
ADD COLUMN `access_code_envoye` VARCHAR(50) NULL DEFAULT NULL AFTER `numero_telephone_paiement`; -- Store the code sent

-- Remove old/unused columns if they exist from the previous 'formations.php' logic
-- ALTER TABLE `inscriptions` DROP COLUMN `formation`; -- If you had this text column
-- ALTER TABLE `inscriptions` DROP COLUMN `telephone`; -- If you had this (replaced by numero_telephone_paiement)
-- ALTER TABLE `inscriptions` DROP COLUMN `montant`; -- If you had this (replaced by montant_paye)

-- Make date_inscription default to current timestamp automatically
ALTER TABLE `inscriptions`
MODIFY COLUMN `date_inscription` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Pour utilisateurs (utilisé dans plusieurs gestions)
ALTER TABLE utilisateurs ADD INDEX idx_type_utilisateur (type_utilisateur);
ALTER TABLE utilisateurs ADD INDEX idx_email (email); -- Probablement déjà unique, mais un index aide
ALTER TABLE utilisateurs ADD INDEX idx_date_inscription (date_inscription);

-- Pour cours (formations)
ALTER TABLE cours ADD INDEX idx_statut (statut);
ALTER TABLE cours ADD INDEX idx_formateur_id (formateur_id);
ALTER TABLE cours ADD INDEX idx_date_creation (date_creation);

-- Pour messages_contact
ALTER TABLE messages_contact ADD INDEX idx_statut (statut);
ALTER TABLE messages_contact ADD INDEX idx_date_reception (date_reception);

-- Pour inscriptions
ALTER TABLE inscriptions ADD INDEX idx_utilisateur_id (utilisateur_id);
ALTER TABLE inscriptions ADD INDEX idx_cours_id (cours_id);
ALTER TABLE inscriptions ADD INDEX idx_date_inscription (date_inscription);

ALTER TABLE utilisateurs
ADD COLUMN profile_image_path VARCHAR(255) NULL DEFAULT NULL COMMENT 'Chemin vers l image de profil' AFTER adresse;
