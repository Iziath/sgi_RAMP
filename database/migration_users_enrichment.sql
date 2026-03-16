-- Migration pour enrichir le module Utilisateurs
-- RAMP-BENIN
-- Phase 1: Base de données

USE ramp_benin;

-- 1. Mise à jour de la table users
-- Ajouter les nouveaux rôles à l'ENUM existant
ALTER TABLE users 
MODIFY COLUMN role ENUM('admin', 'manager', 'user', 'membre_administration', 'volontaire', 'stagiaire', 'benevole') 
DEFAULT 'user';

-- Ajouter champs communs pour tous les types
ALTER TABLE users ADD COLUMN IF NOT EXISTS photo VARCHAR(255) NULL AFTER full_name;
ALTER TABLE users ADD COLUMN IF NOT EXISTS competences TEXT NULL AFTER email;
ALTER TABLE users ADD COLUMN IF NOT EXISTS domaines_interet TEXT NULL AFTER competences;
ALTER TABLE users ADD COLUMN IF NOT EXISTS disponibilite VARCHAR(100) NULL AFTER domaines_interet;

-- 2. Table user_profiles (informations spécifiques par type)
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    profile_type ENUM('volontaire', 'stagiaire', 'benevole') NOT NULL,
    
    -- Champs communs
    date_debut DATE NULL,
    date_fin DATE NULL,
    statut_validation ENUM('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
    date_validation DATE NULL,
    valide_par INT NULL,
    notes TEXT NULL,
    
    -- Spécifique VOLONTAIRE
    duree_engagement INT NULL,
    charte_signee BOOLEAN DEFAULT FALSE,
    date_charte_signee DATE NULL,
    heures_effectuees INT DEFAULT 0,
    raison_fin ENUM('fin_contrat', 'demission', 'exclusion', 'autre') NULL,
    
    -- Spécifique STAGIAIRE
    ecole_universite VARCHAR(200) NULL,
    niveau_etudes VARCHAR(100) NULL,
    convention_signee BOOLEAN DEFAULT FALSE,
    date_convention_signee DATE NULL,
    tuteur_id INT NULL,
    projet_affectation_id INT NULL,
    rapport_rendu BOOLEAN DEFAULT FALSE,
    
    -- Spécifique BÉNÉVOLE
    type_engagement ENUM('ponctuel', 'recurrent') DEFAULT 'ponctuel',
    badge_reconnaissance BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (valide_par) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (tuteur_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (projet_affectation_id) REFERENCES projects(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_user_profile (user_id, profile_type),
    INDEX idx_profile_type (profile_type),
    INDEX idx_statut_validation (statut_validation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table user_evaluations
CREATE TABLE IF NOT EXISTS user_evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    profile_id INT NOT NULL,
    type_evaluation ENUM('mensuelle', 'trimestrielle', 'finale') NOT NULL,
    periode_debut DATE NOT NULL,
    periode_fin DATE NOT NULL,
    evaluateur_id INT NOT NULL,
    note_globale DECIMAL(3,2) NULL,
    points_forts TEXT NULL,
    points_amelioration TEXT NULL,
    recommandations TEXT NULL,
    statut ENUM('brouillon', 'finalisee') DEFAULT 'brouillon',
    date_evaluation DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (profile_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluateur_id) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_user_id (user_id),
    INDEX idx_type_evaluation (type_evaluation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table user_activities
CREATE TABLE IF NOT EXISTS user_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_id INT NULL,
    project_id INT NULL,
    date_participation DATE NOT NULL,
    heures REAL DEFAULT 0,
    description TEXT NULL,
    statut ENUM('planifiee', 'en_cours', 'terminee', 'annulee') DEFAULT 'planifiee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_date_participation (date_participation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table user_documents
CREATE TABLE IF NOT EXISTS user_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    profile_id INT NULL,
    type_document ENUM('charte_engagement', 'convention_stage', 'cv', 'lettre_motivation', 'certificat', 'evaluation', 'autre') NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille_fichier INT NULL,
    date_upload DATE NOT NULL,
    uploaded_by INT NULL,
    description TEXT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (profile_id) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_type_document (type_document)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table user_cycles
CREATE TABLE IF NOT EXISTS user_cycles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    profile_id INT NOT NULL,
    cycle_type ENUM('recrutement', 'integration', 'evaluation', 'fin') NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    statut ENUM('en_cours', 'termine', 'annule') DEFAULT 'en_cours',
    responsable_id INT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (profile_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (responsable_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_cycle_type (cycle_type),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

