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
