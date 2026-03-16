-- Migration pour ajouter les fonctionnalités documents et règlements
-- RAMP-BENIN

USE ramp_benin;

-- 1. Ajouter maitre_de_stage_id dans user_profiles (pour stagiaires)
ALTER TABLE user_profiles 
ADD COLUMN IF NOT EXISTS maitre_de_stage_id INT NULL AFTER tuteur_id;

ALTER TABLE user_profiles 
ADD CONSTRAINT fk_maitre_stage 
FOREIGN KEY (maitre_de_stage_id) REFERENCES users(id) ON DELETE SET NULL;

-- 2. Table user_payments (règlements pour volontaires)
CREATE TABLE IF NOT EXISTS user_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    profile_id INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    periode DATE NOT NULL, -- Mois concerné (YYYY-MM-01)
    type_paiement ENUM('mensuel', 'ponctuel', 'prime') DEFAULT 'mensuel',
    statut ENUM('en_attente', 'paye', 'retarde', 'annule') DEFAULT 'en_attente',
    date_paiement DATE NULL,
    mode_paiement VARCHAR(50) NULL, -- virement, especes, cheque, etc.
    reference_paiement VARCHAR(100) NULL,
    notes TEXT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (profile_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_periode (periode),
    INDEX idx_statut (statut),
    UNIQUE KEY unique_user_periode (user_id, periode, type_paiement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table document_templates (modèles de documents obligatoires par type)
CREATE TABLE IF NOT EXISTS document_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_type ENUM('volontaire', 'stagiaire', 'benevole') NOT NULL,
    type_document VARCHAR(100) NOT NULL,
    nom_document VARCHAR(200) NOT NULL,
    obligatoire BOOLEAN DEFAULT TRUE,
    ordre INT DEFAULT 0,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_template (profile_type, type_document),
    INDEX idx_profile_type (profile_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Insérer les modèles de documents obligatoires
INSERT INTO document_templates (profile_type, type_document, nom_document, obligatoire, ordre, description) VALUES
-- Volontaires
('volontaire', 'charte_engagement', 'Charte d\'engagement', TRUE, 1, 'Document à signer par le volontaire'),
('volontaire', 'cv', 'Curriculum Vitae', TRUE, 2, 'CV du volontaire'),
('volontaire', 'lettre_motivation', 'Lettre de motivation', TRUE, 3, 'Lettre de motivation'),
('volontaire', 'piece_identite', 'Pièce d\'identité', TRUE, 4, 'Copie de la pièce d\'identité'),
('volontaire', 'reglement_interne', 'Règlement interne', TRUE, 5, 'Règlement interne signé'),
-- Stagiaires
('stagiaire', 'convention_stage', 'Convention de stage', TRUE, 1, 'Convention tripartite signée'),
('stagiaire', 'cv', 'Curriculum Vitae', TRUE, 2, 'CV du stagiaire'),
('stagiaire', 'lettre_motivation', 'Lettre de motivation', TRUE, 3, 'Lettre de motivation'),
('stagiaire', 'piece_identite', 'Pièce d\'identité', TRUE, 4, 'Copie de la pièce d\'identité'),
('stagiaire', 'attestation_scolaire', 'Attestation de scolarité', TRUE, 5, 'Attestation de l\'établissement'),
-- Bénévoles
('benevole', 'engagement', 'Engagement bénévole', TRUE, 1, 'Document d\'engagement signé'),
('benevole', 'piece_identite', 'Pièce d\'identité', FALSE, 2, 'Copie de la pièce d\'identité (optionnel)')
ON DUPLICATE KEY UPDATE nom_document = VALUES(nom_document);

-- 5. Ajouter champ montant_mensuel dans user_profiles pour volontaires
ALTER TABLE user_profiles 
ADD COLUMN IF NOT EXISTS montant_mensuel DECIMAL(10, 2) NULL AFTER heures_effectuees;

-- 6. Ajouter champ duree_stage dans user_profiles pour stagiaires (en mois)
ALTER TABLE user_profiles 
ADD COLUMN IF NOT EXISTS duree_stage INT NULL AFTER rapport_rendu;

