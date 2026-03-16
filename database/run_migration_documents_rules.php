<?php
/**
 * Script pour exécuter la migration documents et règles
 * RAMP-BENIN
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

try {
    echo "=== Migration Documents et Règles ===\n\n";
    
    // 1. Ajouter maitre_de_stage_id
    echo "1. Ajout de maitre_de_stage_id...\n";
    $check = $db->query("SHOW COLUMNS FROM user_profiles LIKE 'maitre_de_stage_id'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE user_profiles ADD COLUMN maitre_de_stage_id INT NULL AFTER tuteur_id");
        echo "   ✓ Colonne ajoutée\n";
        
        // Ajouter la clé étrangère
        try {
            $db->exec("ALTER TABLE user_profiles ADD CONSTRAINT fk_maitre_stage FOREIGN KEY (maitre_de_stage_id) REFERENCES users(id) ON DELETE SET NULL");
            echo "   ✓ Clé étrangère ajoutée\n";
        } catch (Exception $e) {
            echo "   ⚠ Clé étrangère existe déjà\n";
        }
    } else {
        echo "   ✓ Colonne existe déjà\n";
    }
    
    // 2. Créer table user_payments
    echo "\n2. Création de la table user_payments...\n";
    $check = $db->query("SHOW TABLES LIKE 'user_payments'");
    if ($check->rowCount() == 0) {
        $db->exec("CREATE TABLE user_payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            profile_id INT NOT NULL,
            montant DECIMAL(10, 2) NOT NULL,
            periode DATE NOT NULL,
            type_paiement ENUM('mensuel', 'ponctuel', 'prime') DEFAULT 'mensuel',
            statut ENUM('en_attente', 'paye', 'retarde', 'annule') DEFAULT 'en_attente',
            date_paiement DATE NULL,
            mode_paiement VARCHAR(50) NULL,
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "   ✓ Table créée\n";
    } else {
        echo "   ✓ Table existe déjà\n";
    }
    
    // 3. Créer table document_templates
    echo "\n3. Création de la table document_templates...\n";
    $check = $db->query("SHOW TABLES LIKE 'document_templates'");
    if ($check->rowCount() == 0) {
        $db->exec("CREATE TABLE document_templates (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "   ✓ Table créée\n";
        
        // Insérer les modèles
        $templates = [
            ['volontaire', 'charte_engagement', 'Charte d\'engagement', 1, 1, 'Document à signer par le volontaire'],
            ['volontaire', 'cv', 'Curriculum Vitae', 1, 2, 'CV du volontaire'],
            ['volontaire', 'lettre_motivation', 'Lettre de motivation', 1, 3, 'Lettre de motivation'],
            ['volontaire', 'piece_identite', 'Pièce d\'identité', 1, 4, 'Copie de la pièce d\'identité'],
            ['volontaire', 'reglement_interne', 'Règlement interne', 1, 5, 'Règlement interne signé'],
            ['stagiaire', 'convention_stage', 'Convention de stage', 1, 1, 'Convention tripartite signée'],
            ['stagiaire', 'cv', 'Curriculum Vitae', 1, 2, 'CV du stagiaire'],
            ['stagiaire', 'lettre_motivation', 'Lettre de motivation', 1, 3, 'Lettre de motivation'],
            ['stagiaire', 'piece_identite', 'Pièce d\'identité', 1, 4, 'Copie de la pièce d\'identité'],
            ['stagiaire', 'attestation_scolaire', 'Attestation de scolarité', 1, 5, 'Attestation de l\'établissement'],
            ['benevole', 'engagement', 'Engagement bénévole', 1, 1, 'Document d\'engagement signé'],
            ['benevole', 'piece_identite', 'Pièce d\'identité', 0, 2, 'Copie de la pièce d\'identité (optionnel)']
        ];
        
        $stmt = $db->prepare("INSERT INTO document_templates (profile_type, type_document, nom_document, obligatoire, ordre, description) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($templates as $template) {
            try {
                $stmt->execute($template);
            } catch (Exception $e) {
                // Ignorer les doublons
            }
        }
        echo "   ✓ Modèles de documents insérés\n";
    } else {
        echo "   ✓ Table existe déjà\n";
    }
    
    // 4. Ajouter montant_mensuel
    echo "\n4. Ajout de montant_mensuel...\n";
    $check = $db->query("SHOW COLUMNS FROM user_profiles LIKE 'montant_mensuel'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE user_profiles ADD COLUMN montant_mensuel DECIMAL(10, 2) NULL AFTER heures_effectuees");
        echo "   ✓ Colonne ajoutée\n";
    } else {
        echo "   ✓ Colonne existe déjà\n";
    }
    
    // 5. Ajouter duree_stage
    echo "\n5. Ajout de duree_stage...\n";
    $check = $db->query("SHOW COLUMNS FROM user_profiles LIKE 'duree_stage'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE user_profiles ADD COLUMN duree_stage INT NULL AFTER rapport_rendu");
        echo "   ✓ Colonne ajoutée\n";
    } else {
        echo "   ✓ Colonne existe déjà\n";
    }
    
    echo "\n=== Migration terminée avec succès! ===\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

