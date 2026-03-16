<?php
/**
 * Script pour exécuter la migration users enrichment
 * RAMP-BENIN
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

try {
    echo "=== Migration Enrichissement Module Utilisateurs ===\n\n";
    
    // 1. Mise à jour table users
    echo "1. Mise à jour de la table users...\n";
    
    // Vérifier et modifier le rôle ENUM
    $check = $db->query("SHOW COLUMNS FROM users WHERE Field = 'role'")->fetch();
    $currentEnum = $check['Type'];
    if (strpos($currentEnum, 'volontaire') === false) {
        $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'user', 'membre_administration', 'volontaire', 'stagiaire', 'benevole') DEFAULT 'user'");
        echo "   ✓ Rôles mis à jour\n";
    } else {
        echo "   ✓ Rôles déjà à jour\n";
    }
    
    // Ajouter photo
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'photo'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN photo VARCHAR(255) NULL AFTER full_name");
        echo "   ✓ Colonne photo ajoutée\n";
    } else {
        echo "   ✓ Colonne photo existe déjà\n";
    }
    
    // Ajouter competences
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'competences'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN competences TEXT NULL AFTER email");
        echo "   ✓ Colonne competences ajoutée\n";
    } else {
        echo "   ✓ Colonne competences existe déjà\n";
    }
    
    // Ajouter domaines_interet
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'domaines_interet'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN domaines_interet TEXT NULL AFTER competences");
        echo "   ✓ Colonne domaines_interet ajoutée\n";
    } else {
        echo "   ✓ Colonne domaines_interet existe déjà\n";
    }
    
    // Ajouter disponibilite
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'disponibilite'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN disponibilite VARCHAR(100) NULL AFTER domaines_interet");
        echo "   ✓ Colonne disponibilite ajoutée\n";
    } else {
        echo "   ✓ Colonne disponibilite existe déjà\n";
    }
    
    // 2. Créer table user_profiles
    echo "\n2. Création de la table user_profiles...\n";
    $check = $db->query("SHOW TABLES LIKE 'user_profiles'");
    if ($check->rowCount() == 0) {
        $db->exec("CREATE TABLE user_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            profile_type ENUM('volontaire', 'stagiaire', 'benevole') NOT NULL,
            date_debut DATE NULL,
            date_fin DATE NULL,
            statut_validation ENUM('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
            date_validation DATE NULL,
            valide_par INT NULL,
            notes TEXT NULL,
            duree_engagement INT NULL,
            charte_signee BOOLEAN DEFAULT FALSE,
            date_charte_signee DATE NULL,
            heures_effectuees INT DEFAULT 0,
            raison_fin ENUM('fin_contrat', 'demission', 'exclusion', 'autre') NULL,
            ecole_universite VARCHAR(200) NULL,
            niveau_etudes VARCHAR(100) NULL,
            convention_signee BOOLEAN DEFAULT FALSE,
            date_convention_signee DATE NULL,
            tuteur_id INT NULL,
            projet_affectation_id INT NULL,
            rapport_rendu BOOLEAN DEFAULT FALSE,
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "   ✓ Table user_profiles créée\n";
    } else {
        echo "   ✓ Table user_profiles existe déjà\n";
    }
    
    // 3. Créer table user_evaluations
    echo "\n3. Création de la table user_evaluations...\n";
    $check = $db->query("SHOW TABLES LIKE 'user_evaluations'");
    if ($check->rowCount() == 0) {
        $db->exec("CREATE TABLE user_evaluations (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "   ✓ Table user_evaluations créée\n";
    } else {
        echo "   ✓ Table user_evaluations existe déjà\n";
    }
    
    // 4. Créer table user_activities
    echo "\n4. Création de la table user_activities...\n";
    $check = $db->query("SHOW TABLES LIKE 'user_activities'");
    if ($check->rowCount() == 0) {
        $db->exec("CREATE TABLE user_activities (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "   ✓ Table user_activities créée\n";
    } else {
        echo "   ✓ Table user_activities existe déjà\n";
    }
    
    // 5. Créer table user_documents
    echo "\n5. Création de la table user_documents...\n";
    $check = $db->query("SHOW TABLES LIKE 'user_documents'");
    if ($check->rowCount() == 0) {
        $db->exec("CREATE TABLE user_documents (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "   ✓ Table user_documents créée\n";
    } else {
        echo "   ✓ Table user_documents existe déjà\n";
    }
    
    // 6. Créer table user_cycles
    echo "\n6. Création de la table user_cycles...\n";
    $check = $db->query("SHOW TABLES LIKE 'user_cycles'");
    if ($check->rowCount() == 0) {
        $db->exec("CREATE TABLE user_cycles (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "   ✓ Table user_cycles créée\n";
    } else {
        echo "   ✓ Table user_cycles existe déjà\n";
    }
    
    echo "\n=== Migration terminée avec succès! ===\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

