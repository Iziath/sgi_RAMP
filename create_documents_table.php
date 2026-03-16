<?php
require 'config/constants.php';
require 'config/config.php';
require 'classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents('database/migration_documents.sql');
    $db->exec($sql);
    echo "✓ Table documents créée avec succès!";
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage();
}
?>
