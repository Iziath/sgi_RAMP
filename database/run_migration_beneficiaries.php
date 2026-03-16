<?php
/**
 * Script pour exécuter la migration beneficiaries
 * RAMP-BENIN
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

try {
    echo "Début de la migration...\n";
    
    // Vérifier si la colonne ville_de_provenance existe
    $check = $db->query("SHOW COLUMNS FROM beneficiaries LIKE 'ville_de_provenance'");
    if ($check->rowCount() == 0) {
        echo "Ajout de la colonne ville_de_provenance...\n";
        $db->exec("ALTER TABLE beneficiaries ADD COLUMN ville_de_provenance VARCHAR(100) NULL AFTER address");
        echo "✓ Colonne ville_de_provenance ajoutée\n";
    } else {
        echo "✓ Colonne ville_de_provenance existe déjà\n";
    }
    
    // Vérifier si la colonne activity_id existe
    $check = $db->query("SHOW COLUMNS FROM beneficiaries LIKE 'activity_id'");
    if ($check->rowCount() == 0) {
        echo "Ajout de la colonne activity_id...\n";
        $db->exec("ALTER TABLE beneficiaries ADD COLUMN activity_id INT NULL AFTER project_id");
        echo "✓ Colonne activity_id ajoutée\n";
    } else {
        echo "✓ Colonne activity_id existe déjà\n";
    }
    
    // Vérifier si la clé étrangère existe
    $check = $db->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = 'ramp_benin' 
        AND TABLE_NAME = 'beneficiaries' 
        AND CONSTRAINT_NAME = 'fk_beneficiary_activity'
    ");
    if ($check->rowCount() == 0) {
        echo "Ajout de la clé étrangère fk_beneficiary_activity...\n";
        $db->exec("
            ALTER TABLE beneficiaries 
            ADD CONSTRAINT fk_beneficiary_activity 
            FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL
        ");
        echo "✓ Clé étrangère ajoutée\n";
    } else {
        echo "✓ Clé étrangère existe déjà\n";
    }
    
    // Vérifier si l'index existe
    $check = $db->query("SHOW INDEX FROM beneficiaries WHERE Key_name = 'idx_activity'");
    if ($check->rowCount() == 0) {
        echo "Ajout de l'index idx_activity...\n";
        $db->exec("CREATE INDEX idx_activity ON beneficiaries(activity_id)");
        echo "✓ Index ajouté\n";
    } else {
        echo "✓ Index existe déjà\n";
    }
    
    echo "\n✓ Migration terminée avec succès!\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

